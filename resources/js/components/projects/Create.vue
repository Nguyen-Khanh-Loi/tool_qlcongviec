<script>
import { VueEditor } from "vue3-editor";
import Swal from "sweetalert2";
import {
    projectMethods,
    projectGetters,
    authMethods,
    authGetters,
    codesMethods,
    codesGetters
} from "@/js/store/helpers";
import PageHeader from "@/js/components/layouts/page-header.vue";
import { uploadMedia } from "@/js/helpers/media";
import Select2 from 'vue3-select2-component';
export default {
    components: {
        VueEditor,
        PageHeader,
        Select2
    },
    data() {
        return {
            title: "",
            items: [],
            projectData: {},
            images: null,
            textForm: {
                'create_title': "Tạo dự án mới",
                'title': "Tên dự án",
                'place_title': "Nhập tên dự án",
                'desc': "Mô tả dự án",
                'start': "Thời gian bắt đầu",
                'place_start': "Chọn thời gian bắt đầu",
                'end': "Thời gian kết thúc",
                'place_end': "Chọn thời gian kết thúc",
                'add_image': "Thêm hình ảnh",
                'name_button_create': "Tạo mới",
            },
            load: true,
            publicPath: process.env.PUBLIC_URL+'projects/',
            listCode:[],
            msg: '',
            code: '',
            listCodeProject: false,
            countCodes: 0,
            showCode: true,
            listCompanyCenters: [],
            defaultCompanyCenter:1
        };
    },
    computed: {
        ...projectGetters,
        ...authGetters,
        ...codesGetters
    },
    methods: {
        ...projectMethods,
        ...authMethods,        
        ...codesMethods,
        onChangeImages(e){
            let files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return;
            this.projectData['file'] = e.target.files[0];

            const reader = new FileReader()
            reader.onload = (event) => {
                this.images = event.target.result
            }
            reader.readAsDataURL(this.projectData['file'])
        },
        async addProject() {
            // console.log('projectSiderBar', this.projectSiderBar.data)
            if (this.load) {
                this.load= false;
                if (!this.projectData.code_project) {
                    this.msg = 'Vui lòng tạo mã dự án!';
                    this.load = true;
                    return;
                }
                var data = await this.createProject(this.projectData);
                if (data) {
                    Swal.fire({
                        position: 'bottom-start',
                        icon: 'success',
                        title: 'Created project successfully',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                    });
                    setTimeout(() => {
                        this.show = false
                    }, 2000);
                    // add new project in siderbar
                    if (this.projectSiderBar.data) {
                        this.projectSiderBar.data.unshift(data);
                    }
                }
                this.projectData = {};
                this.projectData.code = this.code;
                this.images = null;
                this.load = true;
                this.showCode = true;
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
                    const url = this.publicPath+data; // Get url from response
                    Editor.insertEmbed(cursorLocation, "image", url);
                    resetUploader();
                }
            } catch (error) {
                console.log('Upload Images', error)
            }
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
                        const url = this.publicPath+data;
                        const editorInstance = this.editorDesc.editor;
                        editorInstance.insertEmbed(this.$refs.editor.quill.getSelection().index, 'image', url); // insert url image in content editor
                        this.projectData.description = this.$refs.editor.quill.root.innerHTML 
                    }
                } catch (error) {
                    console.log('Upload Images', error)
                }
            } 
        },
        onGeneralCode(){
            var generalCode = parseInt(this.projectData.code + Math.floor(Math.random() * 100).toString().padStart(2, "0"));
            if (this.listCodeProject.includes(generalCode)) {
                var codeInterval = setInterval(() => {
                    generalCode = parseInt(this.projectData.code + Math.floor(Math.random() * 100).toString().padStart(2, "0"));
                    if (!listCodesTest.includes(generalCode)) {
                        this.projectData.code_project = generalCode
                        clearInterval(codeInterval) 
                    }
                }, 100);

            }else{
                this.projectData.code_project = generalCode
            }
            this.msg = '',
            this.showCode = false;
        },
        // change Code
        async onChangeCode(val){
            var idCode = parseInt(val.id);
            if (this.code != idCode) {
                let res = await axios.post(`/api/codes/show/${idCode}`);  
                if(res.status == 200){
                    this.listCodeProject = res.data;
                    this.countCodes = this.listCodeProject ? 100 - this.listCodeProject.length : 0
                }
                this.code = idCode;
            }
        }
    },
    async created() {       
        this.auth();
        this.title = 'Tạo dự án mới';
        this.items = [
            {
                text: "Gosu",
                href: "/",
            },
            {
                text: this.textForm.create_title,
                active: true,
            },
        ];
        try {
            const resultData = await this.getAllCodes();
            // listCode
            if (resultData['data']) {
                var resultCodes = resultData['data']
                for (const key in resultCodes) {
                    const element = resultCodes[key];
                    var data = {};
                    data['id'] = element.id;
                    data['text'] = element.types +' '+ element.country 
                    this.listCode.push(data);
                }
                this.projectData.code = resultCodes[0]['id'];
                this.code = resultCodes[0]['id'];
                this.listCodeProject = resultData['codes']; 
                this.countCodes = this.listCodeProject ? 100 - this.listCodeProject.length : 0;
                var companyCenter = resultData['company_center'];
                for (const key in companyCenter) {
                    const element = companyCenter[key];
                    var data = {};
                    data['id'] = element.id;
                    data['text'] = element.title
                    this.listCompanyCenters.push(data);
                }
                this.defaultCompanyCenter = companyCenter[0]['id'];
            }
        } catch (error) {
            console.log('error get code of project ', error);
        }
    },
};
</script>
<template>
    <div class="container-fluid">        
        <div class="row no-gutters">
            <div class="col-lg-12">
                <PageHeader :title="title" :items="items" />
                <h2 class="text-center">{{ textForm.create_title }}</h2>
                <div class="form-create-project">
                    <form @submit.prevent="addProject">
                        <div class="row mb-3 wrap-choose">
                            <div class="col-lg-4" v-if="showCode">
                                <label class="form-label d-block">Chọn mã dự án</label>
                                <div class="d-flex">
                                    <Select2 v-model="projectData.code" :options="listCode" @select="onChangeCode($event)"/>
                                    <input type="hidden" v-model="projectData.code_project">
                                    <span role="button" class="btn btn-primary btn-md" @click="onGeneralCode">Tạo mã dự án</span>
                                </div>
                                <p class="mb-0 mt-2 error" v-if="countCodes > 0 && countCodes < 10"><strong>Chú ý:</strong> Còn <strong>{{ countCodes }}</strong> mã dự án</p>
                            </div> 
                            <div class="col-lg-4">
                                <label class="form-label d-block">Chọn Trung tâm</label>
                                <Select2 v-model="defaultCompanyCenter" :options="listCompanyCenters" :multiple="true"/>
                            </div>
                            <!-- <div class="col-lg-4">
                            </div> -->
                        </div>
                        <b-form-group
                            :label="textForm.title"
                            label-for="title-input"
                            :class="[`${projectData.code_project ? 'show-code' : ''}`,'wrap-title']"
                        >
                            <code v-if="projectData.code_project">{{ `${'['+projectData.code_project+']'}` }}</code>
                            <b-form-input
                                class="form-control"
                                :placeholder="textForm.place_title"
                                id="title-project"
                                v-model="projectData.title"
                                required
                            >
                            </b-form-input>
                        </b-form-group>
                        <b-form-group
                            :label="textForm.desc"
                            label-for="title-input"
                        >
                            <vue-editor
                                use-custom-image-handler 
                                @image-added="handleImageAdded"
                                @paste="handlePaste"
                                @focus="onFocus"
                                refs = "editor"
                                v-model="projectData.description"
                            ></vue-editor>
                        </b-form-group>
                        <b-form-group
                            :label="textForm.start"
                            label-for="datetime-picker"
                        >
                            <VueDatePicker
                                required
                                v-model="projectData.start_time"
                                auto-apply
                                :month-change-on-scroll="false"
                            />
                        </b-form-group>
                        <b-form-group
                            :label="textForm.end"
                            label-for="datetime-picker"
                        >
                            <VueDatePicker
                                required
                                v-model="projectData.end_time"
                                auto-apply
                                :month-change-on-scroll="false"
                            />
                        </b-form-group>
                        <div class="mb-3 images_projects" v-if="images">
                            <img :src="images" alt="">
                        </div>
                        <div class="btn_add_image mb-3">
                            <label for="add_image">{{ textForm.add_image }}</label>
                            <input type="file" id="add_image" accept="image/*" @change="onChangeImages" role="button">
                        </div>
                        <div>
                            <h5 class="error" v-if="this.msg">{{ this.msg }}</h5>
                            <b-button type="submit" variant="primary"
                                >{{ textForm.name_button_create }}</b-button
                            >
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<style lang="scss">
    .wrap-title {
        position: relative;
        code {
            color: rgb(33, 37, 41);
            font-weight: bold;
            display: inline-flex;
            background: rgba(0,0,0,.1);
            padding: 0 5px;
            border-radius: 3px;
            margin-right: 5px;            
            align-items: center;
            text-transform: uppercase;
        }        
    }
    .wrap-choose {
        .d-flex{ 
            > div {
                width: 100%;
            }
            .btn {
                padding: 0 5px;
                margin-left: 10px;
                min-width: 140px;
            }
        }
    }
    .btn_add_image input {
        display: none;
    }
    .btn_add_image label{
        padding: 8px;
        background: #dcdee2;
        border-radius: 3px;
    }
    .select2-container {
        width: 100% !important;
    }
    .images_projects{
        img{
            max-width: 100%;
        }
    }
</style>