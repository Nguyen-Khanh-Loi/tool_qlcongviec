<script>
import { VueEditor } from "vue3-editor";
import {
    authGetters,
    projectMethods,
    projectGetters,
} from "@/js/store/helpers";
import { checkRolesAccess, checkPermissionAccess } from '@/js/middleware/access.js';
import { optionChart } from "@/js/helpers/chart";
import moment from "moment";
import Swal from "sweetalert2";
import { uploadMedia } from "@/js/helpers/media";
import { languageText } from "@/js/helpers/language";
export default {
    components: {
        VueEditor
    },
    props: {
        results: {
            type: Object,
            default: () => {
                return {}
            },
        },
        title: {
            type: String,
            default: () => {
                return ""
            },
        },
        project: {
            type: Object,
            default: () => {
                return {}
            },
        },
    },
    data() {
        return {
            series: [],
            chartOption: {},
            data: {},
            show:false,
            images: null,
            publicPath : process.env.PUBLIC_URL,
            file:null,
            checkUpdateProject:false,
            languageTextForm: languageText,
        };
    },
    computed: {
        ...authGetters,
        ...projectGetters
    },
    methods: {
        ...projectMethods,
        options:() => optionChart.optionProject(),
        checkRolesAccess:(roles) => checkRolesAccess(roles),
        onChangeImages(e){
            let files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return;
            this.file = e.target.files[0];
            const reader = new FileReader()
            reader.onload = (event) => {
                this.images = event.target.result
            }
            reader.readAsDataURL(this.file)
        },
        async onUpdateProject(){
            var data = {
                'id':this.currentProject.id,
                'file': this.file,
                'url_image':this.currentProject.url_image,
                'title': this.currentProject.title,
                'end_time':moment(this.currentProject.end_time).format('YYYY-MM-DD HH:mm:ss'),
                'start_time':moment(this.currentProject.start_time).format('YYYY-MM-DD HH:mm:ss'),
                'description':this.currentProject.description ? this.currentProject.description : '',
            }            
            var results = await this.updateProject(data);
            if (results) {
                Swal.fire({
                    position: 'bottom-start',
                    icon: 'success',
                    title: this.languageTextForm.edit_success,
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                });
                this.$emit('updateProject', results );
                this.$store.dispatch("addCurrentProject", results);
                setTimeout(() => {
                    this.show = false
                }, 2000);
                this.checkUpdateProject = false
            }
        },
        // upload images ussing editor
        handleImageAdded: async function(file, Editor, cursorLocation, resetUploader) {
            var formData = new FormData();
            formData.append("file", file);
            formData.append("type", 'projects');
            var data = await uploadMedia.upload(formData);
            try {
                if (data) {
                    const url = this.publicPath+'projects/'+data; // Get url from response
                    Editor.insertEmbed(cursorLocation, "image", url);
                    resetUploader();
                }
            } catch (error) {
                console.log('Upload Images', error)
            }
        },
        // remove images
        onRemoveImages(e){
            this.file = false;
            this.images = false;
            this.currentProject.url_image = false;
        },
        onFocus:function(quill){
            this.editorDesc = quill
        },
        async handlePaste(event, callback) {  
            var items = event.clipboardData.items;
            if (items == undefined) {
                if (typeof callback == "function") {
                    callback(undefined);
                }
            }
            for (var i = 0; i < items.length; i++) {
                if (items[i].type.indexOf("image") == -1) continue;
                var blob = items[i].getAsFile();
            }
            if (typeof blob != 'undefined') {
                event.preventDefault();
                const fileUpload = new Blob([blob], blob);
                const formData = new FormData();
                formData.append("file", fileUpload, blob.name);
                formData.append("type", 'projects');
                var data = await uploadMedia.upload(formData);
                try {
                    if (data) {
                        const url = this.publicPath+'projects/'+data;
                        const editorInstance = this.editorDesc.editor;
                        editorInstance.insertEmbed(this.$refs.editor.quill.getSelection().index, 'image', url); // insert url image in content editor
                        this.projectData.description = this.$refs.editor.quill.root.innerHTML 
                    }
                } catch (error) {
                    console.log('Upload Images', error)
                }
            } 
        },
        onShowModal(){
            sessionStorage.setItem('oldDataProject', JSON.stringify(this.currentProject));
            this.checkUpdateProject = true;
            this.show = true;
        },
        // hidden modal popup
        async hiddenModal(data){
            // updated dữ liệu của dự án 
            if (this.checkUpdateProject) {                
                var oldDataProject = sessionStorage.getItem('oldDataProject');
                oldDataProject = JSON.parse(oldDataProject);
                this.$store.dispatch("addCurrentProject", oldDataProject);
                sessionStorage.removeItem('oldDataProject');
                this.checkUpdateProject = false;
            }            
            this.show = false;
        }, 
    },
    watch: {
        results: function() {
            this.data = this.$props.results;
            this.chartOption = this.options();
            this.series = [this.results.total];       
            this.chartOption['labels'] = ['Progress'];       
            if (this.data.total == 100) {
                this.chartOption['colors'] = '#7bd405';
                this.chartOption['labels'] = ['Success'];   
            }
        },        
    }, 
    created() {}, 
    mounted() {},  
};
</script>
<template>
    <b-modal
        v-model="show"
        title="Sửa dự án"
        size="xl"
        hide-footer
        @hide="hiddenModal"
    >
        <form @submit.prevent="onUpdateProject">
            <b-form-group
                :label="languageTextForm.name_title"
                label-for="title-input"
                :class="['wrap-title']"
            >
                <div class = "d-flex">
                    <code v-if="currentProject.code">{{ `${'['+currentProject.code+']'}` }}</code>
                    <code>{{ `${'['+currentProject.code_company_center+']'}` }}</code>
                    <b-form-input
                        id="title-project"
                        v-model="currentProject.title"
                        required
                    >
                    </b-form-input>
                </div>
            </b-form-group>
            <b-form-group
                :label="languageTextForm.desc_project"
                label-for="title-input"
            >
                <vue-editor
                    use-custom-image-handler 
                    @image-added="handleImageAdded"
                    @paste="handlePaste"
                    @focus="onFocus"
                    refs = "editor"
                    v-model="currentProject.description"
                ></vue-editor>
            </b-form-group>
            <b-form-group
                :label="languageTextForm.start_date"
                label-for="datetime-picker"
            >
                <VueDatePicker
                    required
                    v-model="currentProject.start_time"
                    auto-apply
                    :month-change-on-scroll="false"
                />
            </b-form-group>
            <b-form-group
                :label="languageTextForm.end_date"
                label-for="datetime-picker"
            >
                <VueDatePicker
                    required
                    v-model="currentProject.end_time"
                    auto-apply
                    :month-change-on-scroll="false"
                />
            </b-form-group>
            <div class="wrap-img">
                <div class="images-projects" v-if="images">
                    <img :src="`${images}`" alt="">
                </div>
                <div class="images-projects" v-else-if="currentProject.url_image">
                    <img :src="`${publicPath+'projects/'+currentProject.url_image}`" alt="">
                </div>
            </div>
            <div class="d-flex mb-3">
                <div class="btn_edit_image me-3">
                    <label for="edit_image">{{ languageTextForm.add_image }}</label>
                    <input type="file" id="edit_image" accept="image/*" @change="onChangeImages" role="button">
                </div>
                <button @click="onRemoveImages" type="button" class="btn btn-danger" v-if="currentProject.url_image">{{ languageTextForm.remove_img }}</button>
            </div>
            <div :class="['modal-footer']">
                <b-button type="submit" variant="primary"
                    >{{ languageTextForm.edit }}</b-button
                >
            </div>
        </form>
    </b-modal>
    <div class="card" :role="`${authUserData.roles[0].name === 'administrator' ? 'button' : ''}`">
        <div class="card-body">
            <button role="button" class="btn btn-primary" v-if="checkRolesAccess(['administrator', 'manager'])" @click="onShowModal">{{ languageTextForm.edit }}</button>
            <h4 class="card-title mb-4 text-center code"><code v-if="currentProject.code">{{ `${'['+currentProject.code+']'}` }}</code>{{ currentProject.title }}</h4>
            <apexchart                            
                type="radialBar"
                dir="ltr"
                :series="series"
                :options="chartOption"
            ></apexchart>
            <div class="infomation">
                <div class="wrap-img">
                    <div class="images-projects" v-if="currentProject.url_image">
                        <img :src="`${publicPath+'projects/'+currentProject.url_image}`" alt="">
                    </div>
                </div>
                <div
                    v-if="currentProject.description"
                    v-bind:innerHTML="`${
                        currentProject.description
                            ? currentProject.description
                            : ''
                    }`"
                    :class="['content-desc mt-5']"
                ></div>
                <div class="date">
                    <span><strong class="me-2">Start date</strong>{{ currentProject.start_time }}</span>
                    <br>
                    <span><strong class="me-2">End date</strong>{{ currentProject.end_time }}</span>
                </div>                
            </div>
        </div>                    
    </div>
</template>
<style lang="scss" scoped>
    .btn_edit_image{
             input{
                display: none;
             }
             label{
                padding: 8px;
                background: #dcdee2;
                border-radius: 3px;
             }
        }
    .modal-footer{
        border: 0;
    }
    form {
        .wrap-img {
            .images-projects {
                margin: 0 0 10px;
            }
        }
    }
    .wrap-img {
        .images-projects {
            width: 150px;
            height: 200px;
            position: relative;
            margin: 0 auto;
            img{
                width: 100%;
                height: 100%;
                position: absolute;
                object-fit: cover;
            }
        }
    }
    .infomation{
        p{
            img{
                width: 100%;
            }
        }
    }
    .wrap-title {
        position: relative;
        code {
            color: rgb(33, 37, 41);
            font-weight: bold;
            display: inline-flex;
            background: rgba(0, 0, 0, 0.1);
            padding: 0 5px;
            border-radius: 3px;
            margin-right: 5px;
            align-items: center;
            text-transform: uppercase;
        }
    }
</style>