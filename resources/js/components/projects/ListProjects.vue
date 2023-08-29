<script>
import { VueEditor } from "vue3-editor";
import $ from "jquery";
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
import { dateHelper } from "@/js/helpers/datehelper";
import { languageText } from "@/js/helpers/language";
import { options } from "@/js/helpers/options";
import { checkRolesAccess } from '@/js/middleware/access.js';
import { staticProject } from "@/js/helpers/statisproject";
import Select2 from 'vue3-select2-component';
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
import DataTableBs5 from 'datatables.net-bs5';
import moment from "moment";
DataTable.use(DataTablesCore);
DataTable.use(DataTableBs5);
export default {
    components: {
        VueEditor,
        PageHeader,
        Select2,
        DataTable
    },
    data() {
        return {
            title: "",
            items: [],
            projectData: {}, // dữ liệu của dự án 
            images: null,
            languageTextForm: languageText,
            load: true,
            publicPath: process.env.PUBLIC_URL+'projects/',
            listCode:[],
            msg: '',
            code_id: '',
            listCodeProject: false,
            countCodes: 0,
            showCode: true,
            listCompanyCenters: [], // dữ liệu của trung tâm
            companyCenter: false,
            listDataProject: [], // tất cả dự án
            columns:[],
            showModal: false,
            defaultDataProject: {}, // value default code game, code của trung tâm 
            optionsTable:options.optionsTable(),// set option datatable   
            listRole: ['administrator', 'manager', 'project_manager', 'supper_administrator']      
        }
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
        checkRolesAccess(roles) {
            return checkRolesAccess(roles);
        },
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
        // remove images
        onRemoveImages(e){
            this.projectData['file'] = false;
            this.images = false;
            this.projectData.url_image = false;
        },
        // create new project
        async hanldeProject() {
            if (this.load) {
                this.load= false;
                this.projectData['start_time'] = moment(this.projectData['start_time']).format('YYYY-MM-DD HH:mm:ss');
                this.projectData['end_time'] = moment(this.projectData['end_time']).format('YYYY-MM-DD HH:mm:ss');
                if (!this.projectData.code) {
                    this.msg = 'Vui lòng tạo mã dự án!';
                    this.load = true;
                    return;
                }
                if (this.projectData.id) {
                    // update project
                    var data = await this.updateProject(this.projectData);
                }else{
                    // create project
                    var data = await this.createProject(this.projectData);
                }
                
                if (data) {
                    var _data = data;
                    _data.progress = 0;
                    _data.content = this.trimString(_data.description)
                    // updated dữ liệu của dự án
                    if (this.projectData.id) {
                        let index = false;
                        this.listDataProject.filter((item, key) => {
                            if (this.projectData.id == item.id) {
                                index = key
                            }
                        })
                        this.listDataProject[index] = _data;                        
                    } else {
                        this.listDataProject.unshift(_data); // add new projects
                    }
                    $(this.$refs.projectTable).DataTable().destroy();
                    this.optionsTable['data'] = this.listDataProject;
                    this.optionsTable['columns'] = this.columns;
                    $(this.$refs.projectTable).DataTable(this.optionsTable);
                    var titleSwal = this.projectData.id ? this.languageTextForm.edit_success : this.languageTextForm.create_success
                    Swal.fire({
                        position: 'bottom-start',
                        icon: 'success',
                        title: titleSwal,
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                    });
                    setTimeout(() => {
                        this.show = false
                    }, 2000);                    
                };
                this.projectData = {};
                this.projectData['company_center_id'] = this.defaultDataProject.company_center_id;
                this.projectData['code_company_center'] = this.defaultDataProject.code_company_center;
                this.projectData['code_id'] = this.defaultDataProject.code_id;
                this.images = null;
                this.load = true;
                this.showCode = true;
                this.showModal = false;
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
            var generalCode = parseInt(this.projectData.code_id + Math.floor(Math.random() * 100).toString().padStart(2, "0"));
            if (this.listCodeProject.includes(generalCode)) {
                var codeInterval = setInterval(() => {
                    generalCode = parseInt(this.projectData.code_id + Math.floor(Math.random() * 100).toString().padStart(2, "0"));
                    if (!listCodesTest.includes(generalCode)) {
                        this.projectData.code = generalCode
                        clearInterval(codeInterval) 
                    }
                }, 100);

            }else{
                this.projectData.code = generalCode
            }
            this.msg = '',
            this.showCode = false;
        },
        // change Code
        async onChangeSelect(val, action){ 
            if (action) {
                var idCode = parseInt(val.id);
                if (this.code_id != idCode) {
                    let res = await axios.post(`/api/codes/show/${idCode}`);  
                    if(res.status == 200){
                        this.listCodeProject = res.data;
                        this.countCodes = this.listCodeProject ? 100 - this.listCodeProject.length : 0
                    }
                    this.code_id = idCode;
                }
                return;                
            }
            // thêm mã của trung tâm vào dự án
            // console.log('this.projectData.company_center_id', this.projectData.company_center_id);
            this.companyCenter.filter((item) => {
                if (parseInt(val.id) == item.id) {
                    this.projectData.code_company_center = item.code;
                }
            })
        },
        trimString(str){
            var content = '';
            if (str !== null && typeof str !== "undefined") {
                content = str.replace(/(<([^>]+)>)/ig, '')
                content = content.replace(/&nbsp;/g, ' ');   
                content = content.replace(/^(.{20}[^\s]*).*/, "$1...");
            } 
            return content;
        },
        // show modal popup
        handleShowModal(data){
            if(data['result']){
                this.projectData = data['result'];
                // check images project
                if (this.projectData.url_image) {
                    this.images = this.publicPath+this.projectData.url_image
                }
                
            }else{
                $('.form-create-project').find('.wrap-choose').show();
                this.projectData = {};
                this.projectData['company_center_id'] = this.defaultDataProject.company_center_id;
                this.projectData['code_company_center'] = this.defaultDataProject.code_company_center;
                this.projectData['code_id'] = this.defaultDataProject.code_id;
            }
            this.showModal = true;
        },
        // hidden modal popup
        async hiddenModal(data){
            // updated dữ liệu của dự án 
            var oldDataProject = sessionStorage.getItem('oldDataProject');
            oldDataProject = JSON.parse(oldDataProject)
            if (this.projectData.id) {
                let index = false;
                this.listDataProject.filter((item, key) => {
                    if (this.projectData.id == item.id) {
                        index = key
                    }
                })
                this.listDataProject[index] = oldDataProject;
                sessionStorage.removeItem('oldDataProject');
            }
            this.showModal = false;
            this.projectData = {};
            this.images = false;
            this.projectData['company_center_id'] = this.defaultDataProject.company_center_id;
            this.projectData['code_company_center'] = this.defaultDataProject.code_company_center;
            this.projectData['code_id'] = this.defaultDataProject.code_id;           
        },
        // tính % tiến độ hoàn thành của task
        statisticalProject(data){
            return staticProject.statisticalTasks(data);
        },  
        // redirect
        onRedirectProject(result){
            if (result && result[0]) {
                this.$router.push({
                    name: this.checkRolesAccess(this.listRole) ? 'analytics' : 'project',
                    params: { 
                        'slug': result[0]['slug'],
                        'id': result[0]['id'],
                    }
                });
            }
        } 
    },
    created() { 
    },
    async mounted() {
        var $ref = this;   
        this.columns = [
            { data: 'image', 
                className: 'image',
                title: languageText.image_text,
                render: function (data, type, row) {
                    var url_img = $ref.publicPath+'default-project.jpg';
                    if (row.url_image) {
                        url_img = $ref.publicPath+row.url_image
                    }
                    return `
                        <span class="images_table"><img src="${url_img}" alt="${row.title}"/></span>
                    `;
                }
            },
            {   data: 'title',
                title: languageText.name_title,
                className: 'title_project',
                render: function (data, type, row) {
                   var code_company_center = row.code_company_center;
                   var code_project = row.code;
                    return `
                        <div class="d-flex align-items-center">
                            <code class="me-2">[${code_project}]</code>
                            <code class="me-2">[${code_company_center}]</code>
                            <h6 class="mb-0 title">${row.title}</h6>
                        </div>
                    `;
                }
            },
            { data: 'content', title: languageText.content_text, className: 'content' },
            { 
                data: 'progress', 
                title: languageText.progress_text,
                render: function(data, type, row){
                    return `<strong>${$ref.statisticalProject(row.tasks)+'%'}</strong>`;
                }
            },
            { data: 'status', title: languageText.status_text, 
                render: function (data, type, row) { 
                    return languageText[row.status];// hiển thị trạng thái của dự án
                }
            },
            { data: 'start_date',
                title: languageText.start_date, 
                className: 'start_date',
                render: function (data, type, row) {
                    var format = {
                        'format':'DD-MM-YYYY',
                        'date':row.start_time,
                    };                    
                    return dateHelper.formatDate(format);// show date of project
                }
            },
            { data: 'end_date',
                title: languageText.end_date, 
                className: 'end_date',
                render: function (data, type, row) {
                    var format = {
                        'format':'DD-MM-YYYY',
                        'date':row.end_time,
                    };                    
                    return dateHelper.formatDate(format);// show date of project
                }
            },
            { 
                data: 'action', 
                title: languageText.action_text,
                render: function (data, type, row) {
                    return `
                        <button project-id="${row.id}" type="button" class="btn btn-outline-primary edit_project btn-sm">${$ref.languageTextForm.edit}</button>
                        <button project-id="${row.id}" type="button" class="btn btn-outline-danger delete_project btn-sm">${$ref.languageTextForm.delete}</button>
                    `;
                }
            },
        ]    
        this.auth();
        this.title = this.languageTextForm.title_all_project;
        this.items = [
            {
                text: "Gosu",
                href: "/",
            },
            {
                text: this.title,
                active: true,
            },
        ];
        try {
            // get list data project
            const resultProject = await this.getProjects();
            if (resultProject['data']) {
                var _resultProject = resultProject['data'];
                this.listDataProject = _resultProject.filter((item) => {
                    item.progress = 0;
                    item.content = this.trimString(item.description)
                    return item;
                })
                // add data table
                this.optionsTable['data'] = this.listDataProject;
                this.optionsTable['columns'] = this.columns;
                $(this.$refs.projectTable).DataTable(this.optionsTable);
            }
            // get all code project
            const resultData = await this.getAllCodes();
            // listCode
            if (resultData['data']) {
                var resultCodes = resultData['data']
                for (const key in resultCodes) {
                    const element = resultCodes[key];
                    var data = {};
                    data['id'] = element.id;
                    data['text'] = element.types +' '+ element.country 
                    this.listCode.push(data); // tất cả mã của game ví dụ PC Vietnam/Mobi Viet nam ......
                }              
                this.listCodeProject = resultData['codes']; 
                this.countCodes = this.listCodeProject ? 100 - this.listCodeProject.length : 0;
                this.companyCenter = resultData['company_center'];
                for (const key in this.companyCenter) {
                    const element = this.companyCenter[key];
                    var data = {};
                    data['id'] = element.id;
                    data['text'] = element.title
                    this.listCompanyCenters.push(data);
                }
                this.defaultDataProject.company_center_id = this.companyCenter[0]['id']; // id của trung tâm
                this.defaultDataProject.code_company_center = this.companyCenter[0]['code']; // mã code của trung tâm
                this.defaultDataProject.code_id = resultCodes[0]['id']; // code của mã game 
                this.projectData['company_center_id'] = this.defaultDataProject.company_center_id;
                this.projectData['code_company_center'] = this.defaultDataProject.code_company_center;
                this.projectData['code_id'] = this.defaultDataProject.code_id;
                this.code_id = resultCodes[0]['id']; // code của mã game
            }
        } catch (error) {
            console.log('error get code of project ', error);
        }
        //edit
        $(document).on('click', '.edit_project', function (e) {
            var projectId = $(this).attr('project-id');
            $('.form-create-project').find('.wrap-choose').hide();
            const resultProjectData = $ref.listDataProject;
            const result = resultProjectData.filter( (item, key) =>{
                if(item.id == parseInt(projectId)){
                    return item;
                }
            });
            if (result) {
                // lưu dư liệu của  dự án trước khi chỉnh sửa
                sessionStorage.setItem('oldDataProject', JSON.stringify(result[0]));
                $ref.handleShowModal({'type': 'edit', 'result': result[0]}); 
            }       
        });
        $(document).on('click', '.delete_project', function (e) {
            var projectId = $(this).attr('project-id');
            var title = $(this).parents('tr').find('.title_project').html();
            Swal.fire({
                    title: languageText.title_remove_project,
                    html: title,  
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: languageText.remove_text,
                    cancelButtonText: languageText.cancel_text,
                    dangerMode: true,
                }).then(async function(isConfirm) {                    
                if (isConfirm.isConfirmed) {
                    // delete project
                    const dataUpdate = {
                        'id': projectId,
                        'action':'remove'
                    }
                    var data = await $ref.updateProject(dataUpdate);
                    if (data) {
                        // updated dữ liệu của dự án
                        $ref.listDataProject = $ref.listDataProject.filter((item, key) => {
                            return item.id != projectId
                        })
                        console.log('$ref.listDataProject', $ref.listDataProject);
                        $($ref.$refs.projectTable).DataTable().destroy();
                        $ref.optionsTable['data'] = $ref.listDataProject;
                        $ref.optionsTable['columns'] = $ref.columns;
                        $($ref.$refs.projectTable).DataTable($ref.optionsTable);
                        Swal.fire({
                            position: 'bottom-start',
                            icon: 'success',
                            title: languageText.success_remove_project,
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                        });
                    }
                }
            })
        });
        // redirect
        $(document).on('click','#projectTable tbody tr', function (e) {            
            var projectId = $(this).find('.edit_project').attr('project-id');
            if( !$(e.target).hasClass('edit_project') && !$(e.target).hasClass('delete_project')){
                const resultProjectData = $ref.listDataProject;
                const result = resultProjectData.filter( (item, key) =>{
                    if(item.id == parseInt(projectId)){
                        return item;
                    }
                });
                $ref.onRedirectProject(result)
            };
            
        });
    },
};
</script>
<template>
    <b-modal
        v-model="showModal"
        @hide="hiddenModal"
        size="lg"
        hide-footer
        hide-header
    >
        <div
            :class="[
                'd-flex flex-row align-items-center justify-content-between',
            ]"
        >
            <h2 class="d-flex flex-row fs-3">
                <span class="text-center">{{ languageTextForm.create_title }}</span>
            </h2>
            <div :class="['btn_close']" @click="hiddenModal">
                <i class="ri-close-line"></i>
            </div>
        </div>
        <div class="form-create-project">
            <form @submit.prevent="hanldeProject">
                <div class="row mb-3 wrap-choose">
                    <div class="col-lg-6">
                        <label class="form-label d-block">{{ languageTextForm.choose_center }}</label>
                        <Select2 v-model="projectData.company_center_id" :options="listCompanyCenters"  @select="onChangeSelect($event)"/>
                    </div>
                    <div class="col-lg-6" v-if="showCode">
                        <label class="form-label d-block">{{ languageTextForm.choose_code }}</label>
                        <div class="d-flex">
                            <Select2 v-model="projectData.code_id" :options="listCode" @select="onChangeSelect($event, 'code_project')"/>
                            <input type="hidden" v-model="projectData.code">
                            <span role="button" class="btn btn-primary btn-md" @click="onGeneralCode">{{ languageTextForm.create_code }}</span>
                        </div>
                        <p class="mb-0 mt-2 error" v-if="countCodes > 0 && countCodes < 10"><strong>Chú ý:</strong> Còn <strong>{{ countCodes }}</strong> mã dự án</p>
                    </div> 
                </div>
                <b-form-group
                    :label="languageTextForm.name_title"
                    label-for="title-input"
                    :class="[`${projectData.code ? 'show-code' : ''}`,'wrap-title']"
                >
                    <div class = "d-flex">
                        <code v-if="projectData.code">{{ `${'['+projectData.code+']'}` }}</code>
                        <code>{{ `${'['+projectData.code_company_center+']'}` }}</code>
                        <b-form-input
                        class="form-control"
                            :placeholder="languageTextForm.place_title"
                            id="title-project"
                            v-model="projectData.title"
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
                        v-model="projectData.description"
                    ></vue-editor>
                </b-form-group>
                <b-form-group
                    :label="languageTextForm.start_date"
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
                    :label="languageTextForm.end_date"
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
                <div class="d-flex mb-3">
                    <div class="btn_add_image me-3">
                        <label for="add_image">{{ languageTextForm.add_image }}</label>
                        <input type="file" id="add_image" accept="image/*" @change="onChangeImages" role="button">
                    </div>
                    <button @click="onRemoveImages" type="button" class="btn btn-danger" v-if="images">{{ languageTextForm.remove_img }}</button>
                </div>
                
                <div>
                    <h5 class="error" v-if="this.msg && !projectData.id">{{ this.msg }}</h5>
                    <b-button type="submit" variant="primary" v-if="!projectData.id"
                        >{{ languageTextForm.name_button_create }}</b-button
                    >
                    <b-button type="submit" variant="primary" v-else
                        >{{ languageTextForm.edit_project }}</b-button
                    >
                </div>
            </form>
        </div>
    </b-modal>
    <div class="container-fluid">        
        <div class="row no-gutters">
            <div class="col-lg-12">
                <PageHeader :title="title" :items="items" />
                <div v-if="checkRolesAccess(['administrator', 'manager'])">
                    <button @click="handleShowModal({'type':'create'})" class="btn btn-primary">{{ languageTextForm.create_title }}</button>
                </div>
                <div class="table-responsive-lg">
                    <table
                        ref="projectTable"
                        class="table table-bordered table-striped table-hover display nowrap"
                        id="projectTable"
                    ></table>
                </div>
            </div>
        </div>
    </div>
</template>
<style lang="scss">
code{
    color: #000 !important;
    font-size: 16px !important;
    background: rgba(0, 0, 0, 0.1);
    border-radius: 3px;
    padding: 5px;
    margin-right: 2px;
    font-weight: bold;
}
#projectTable{
    td{
        padding: 0.5rem;
    }
    tr{
        cursor: pointer;
    }
    .title_project{
        code{
            color: #000;
            font-size: 16px;
            text-transform: uppercase;
        }
        code:first-child{
            margin-right: 2px !important;
        }
    }
}
    .images_table {
        width: 30px;
        height: 30px;
        position: relative;
        display: block;
        img{
            width: 100%;
            height: 100%;
            object-fit: fill;
            object-position: center;
        }
    }
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
        z-index: 1055;
    }
    .images_projects{
        img{
            max-width: 100%;
        }
    }
    .swal2-html-container {
        text-align: center;
    }
</style>