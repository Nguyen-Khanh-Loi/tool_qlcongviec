<script>
import { staticProject } from "@/js/helpers/statisproject";
import { optionChart } from "@/js/helpers/chart";
import { userHelper } from "@/js/helpers/users";
import {
    projectMethods,
    projectGetters,
    authMethods,
    authGetters
} from "@/js/store/helpers";
import PageHeader from "@/js/components/layouts/page-header.vue";
import VueApexCharts from "vue3-apexcharts";
import AnalyticsDep from "./analytics/Depart.vue"
import AnalyticsProject from "./analytics/Project.vue"
export default {
    components: {
        PageHeader,
        apexchart: VueApexCharts,
        AnalyticsDep,
        AnalyticsProject
    },
    data() {
        return {
            slug:this.$route.params.slug,
            resultsDemo:[],
            series: [],
            chartOption: [],
            dataProject: "" ,
            userProjectManager: false,  
            listUserInProjectManager: [],  
            listDepartments: false,  
            listDataDepartments: [], 
            analyticProject: false   
        };
    },
    computed: {
        ...projectGetters,
        ...authGetters
    },
    methods: {
        ...projectMethods,
        ...authMethods,
        // show avater user
        showAvatarUser(url){
            return userHelper.avatar(url);
        },
        // show full name user
        fullName(user){
            return userHelper.fullName(user);
        },
        // /**
        //  * 
        //  * @param {*} data => all task in department
        //  */
        // analyticProject(){
        //     return staticProject.statisticalProject(this.currentProject.departments)
        // },
        updateProjectData(data){
            this.$store.dispatch( 'addTitleProject', data.title );
            this.$store.dispatch( 'addBreadcrumbProject', [
                {
                    text: "Gosu",
                    href: "/",
                },
                {
                    text: data.title,
                    active: true,
                },
            ] );
        },
        onViewProject(id){
            if (this.authUserData.roles[0].name === 'administrator') {
                this.$router.push({ name: 'project', params: { id: this.id } });
                this.$store.dispatch( 'department', id );
            }
        },
        onSeries(data){
            if (data) {
                return [data.percent];
            }  
        },
        onOption(data){
            if (data) {
                var option = optionChart.option();
                var color = '#042ecb';
                var title = 'Progress';
                if (data.percent == 100) {
                    color = '#7bd405';
                    title = 'Success'
                }
                option['labels'] = [title];
                option['colors'] = [color];
                return  option;
            }  
        },
        // kiểm tra user đã thêm quyền quản lý dự án
        onCheckUser(data){
            return userHelper.checkUserInProjects(data);
        },
        // add user project manager in project
        async addUserInProjectManager(data){
            if (data) {
                const checkUser = this.onCheckUser({'user': data, 'listUsers':this.listUserInProjectManager});
                var _data = {
                    'project_id': this.currentProject.id,
                    'user_id' : data.id, // user được thêm vào quản lý dự án
                    'type' : 'access_project',
                    'action': checkUser ? 'remove' :'project-management'
                }
                const results = await this.addRemoveUserInProject(_data);
                if (_data['action'] == 'remove') {                    
                    this.listUserInProjectManager = this.listUserInProjectManager.filter((item)=>{
                        return item.user_id != _data.user_id;
                    })
                }else{
                    this.listUserInProjectManager.push(results);
                }
            }
        },
        /**
         * add user project manager in project
         * data => infomation department
         * @param {*} data 
         */
        async addDepartmentInProjectManager(data){
            if (data) {
                const checkDepart = this.checkDepartment({'department': data, 'list':this.listDataDepartments});
                let _data = {
                    'project_id': this.currentProject.id,
                    'department_id' : data.id, // thêm bộ phận vào dự án
                    'action': checkDepart ? 'disapprove' : 'approve'
                }
                const results = await this.departmentInProject(_data);
                // updated list depart in project
                if (_data['action'] == 'disapprove') {                    
                    this.listDataDepartments = this.listDataDepartments.filter((item)=>{
                        return item.id != _data.department_id;
                    })
                }else{
                    this.listDataDepartments.push(results);
                }
                this.analyticProject = staticProject.statisticalProject(this.listDataDepartments);
            }
        },
        // check department in project
        checkDepartment(data){
            let result = false;
            if (data['list']) {
                for (const key in data['list']) {
                    const item = data['list'][key];
                    if (item.id == data.department.id) {
                        result = true;
                        break;
                    }
                }
            }
            return result;
        }
    },
   created() {        
       
    },
    async mounted() {
        var results = await this.getProject({'slug':this.$route.params.slug});
        if (results) {
            if (results.projectuser.length > 0) {
                this.listUserInProjectManager = results.projectuser;
            }
            // if (results.departments.length > 0) {
                this.listDataDepartments = results.departments;
                this.analyticProject = staticProject.statisticalProject(this.listDataDepartments);
            // }
        }
        const resultData = await this.getListUserProjectManager();
        if (resultData && resultData['project_manager']) {
            this.userProjectManager = resultData['project_manager'];
        }
        if (resultData && resultData['department']) {
            this.listDepartments = resultData['department'];
        }
    },
};
</script>
<template>
    <div class="container-fluid min-vh-100">
        <PageHeader :title="titleProject" :items="breadcrumbProject" />
        <div class="row justify-content-end pe-4">
            <div class="col-xs-4 width-220">
                <div class="dropdown" v-if="userProjectManager">
                    <button class="btn btn-primary dropdown-toggle analytic_btn" type="button" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                        Thêm người quản lý dự án
                    </button>
                    <ul class="dropdown-menu dropdown-menu-lg dropdown-add-member" aria-labelledby="dropdownUser">
                        <li>
                            <span class="d-block">Thêm bộ phận</span>
                            <hr>
                        </li>
                        <li>
                            <div class="input-group">
                                <input type="search" class="form-control" placeholder="Tìm kiếm...">
                                <!-- <button class="btn btn-primary" type="button">Search</button> -->
                            </div>
                        </li>
                        <li v-for="user in userProjectManager">
                            <div class="d-flex mt-3 justify-content-between align-items-center" @click="addUserInProjectManager(user)">
                                <div class="d-flex align-items-center">
                                    <img :src="showAvatarUser(user.avatar)" :alt="fullName(user)" class="me-2"/>
                                    <span>{{ fullName(user) }}</span> 
                                </div>
                                <!-- check user in project manager -->
                                <i v-if="onCheckUser({'user': user, 'listUsers': listUserInProjectManager})" class="ri-check-line"></i>
                            </div>
                        </li>
                        <!-- Add more dropdown items as needed -->
                    </ul>
                </div>
            </div>
            <div class="col-xs-4 ms-2 mb-4 width-220">
                <div class="dropdown" v-if="listDepartments">
                    <button class="btn btn-primary dropdown-toggle analytic_btn" type="button" id="dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Thêm bộ phận vào dự án
                    </button>
                    <ul class="dropdown-menu dropdown-menu-lg dropdown-add-part" aria-labelledby="dropdownUser">
                        <li>
                            <span class="d-block">Thêm bộ phận</span>
                            <hr>
                        </li>
                        <li>
                            <div class="input-group">
                                <input type="search" class="form-control" placeholder="Tìm kiếm...">
                                <!-- <button class="btn btn-primary" type="button">Search</button> -->
                            </div>
                        </li>
                        <li v-for="department in listDepartments" @click="addDepartmentInProjectManager(department)">
                            <div class="d-flex mt-3 justify-content-between">
                                <span>{{ department.title }}</span>
                                <i v-if="checkDepartment({'department':department, 'list':listDataDepartments})" class="ri-check-line"></i>
                            </div>
                        </li>
                        <!-- Add more dropdown items as needed -->
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-4">
                <AnalyticsProject @updateProject="updateProjectData" :results="analyticProject"></AnalyticsProject>
            </div>
            <div class="col-xl-8">
                <!-- <AnalyticsDep :results="dataChartProject.results" :id="currentProject.id" :slug="currentProject.slug"></AnalyticsDep> -->
                <div class="row">  
                    <div class="col-md-4" v-for="(result, index) in analyticProject.results" :key="index" @click="onViewProject(result.id)">
                        <div class="card" :role="`${authUserData.roles[0].name === 'administrator' ? 'button' : ''}`">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body overflow-hidden">
                                        <h4 class="card-title text-truncate font-size-14 mb-2">{{result.title}}</h4>
                                        <apexchart
                                        type="radialBar"                                        
                                        dir="ltr"
                                        :series="onSeries(result)"
                                        :options="onOption(result)"
                                        ></apexchart>
                                    </div>
                                    <div class="text-primary">
                                        <i :class="`ri-stack-line font-size-24`"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body border-top py-3">
                                <div class="text-truncate">
                                    <span class="badge badge-soft-success font-size-11">
                                        {{ 100 - result.percent+'%' }}
                                    </span>
                                    <span class="text-muted ml-2">Chưa hoàn thành</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style lang="scss">
.width-220{
    width: 220px !important;
}
.dropdown-add-member{
        padding: 20px !important;
        width: 360px;
    img{
        width: 32px;
        height: 32px;
    }
    .input-group{
        .form-control{
            padding: 3px 10px;
        }
    }
   
}
   .analytic_btn{
      width: 220px;
   }
   .dropdown-add-part{
    padding: 20px !important;
    width: 360px;
    .input-group{
        .form-control{
            padding: 3px 10px;
        }
    }
   }
</style>