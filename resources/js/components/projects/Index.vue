<script>
// import { mapGetters, mapActions, mutations } from "vuex";
import { VueEditor } from "vue3-editor";
import { staticProject } from "@/js/helpers/statisproject";
import PageHeader from "@/js/components/layouts/page-header.vue";
import moment from "moment";
import {
    projectMethods,
    projectGetters,
    authMethods,
    authGetters
} from "../../store/helpers";
export default {
    components: {
        VueEditor,
        PageHeader
    },
    data() {
        return {
            title: "",
            items: [],
            projectData: this.$store.getters.projectData,
            authData: this.$store.getters.getAuthUserData,
            currentPage:"",
            totalPage:"",
            images: null,
            publicPath : process.env.PUBLIC_URL,
            load: true,
            total: 0
        };
    },
    computed: {
        ...projectGetters,
        ...authGetters,
    },
    methods: {
        ...projectMethods,
        ...authMethods,

        statisticalProject(data){
            return staticProject.statisticalTasks(data);
        },
        analyticProject(){
            return staticProject.statisticalProject(this.currentProject.departments)
        },
        formatDate(date){
            return moment(date).format('DD-MM-YYYY');
        },   
        
        trimString(str){
            var content = '';
            if (str != 'undefined') {
                content = str.replace(/(<([^>]+)>)/ig, '')
                content = content.replace(/&nbsp;/g, ' ');   
                content = content.replace(/^(.{20}[^\s]*).*/, "$1...");
            } 
            return content;
        }
    },
    async created() {       
        this.auth();
        await this.getProjects();
        this.title = 'Projects';
        this.items = [
            {
                text: "Gosu",
                href: "/",
            },
            {
                text: 'Projects',
                active: true,
            },
        ];
        this.currentPage = this.$store.getters.paginateProject;
        this.totalPage = this.$store.getters.totalPageProject;
        this.total = this.$store.getters.totalProject;
        const $this = this;
        window.addEventListener('scroll', function() {
            if (document.documentElement.scrollTop + window.innerHeight >= document.documentElement.scrollHeight) {
                if ($this.load) {
                    if ($this.currentPage < $this.totalPage) {                       
                        $this.load = false;
                        $this.currentPage++;
                        $this.axios
                            .get(`/api/project/index?page=${$this.currentPage}`)
                            .then((response) => {
                                if (response.status == 200) {
                                    if (response.data.data) {
                                        for (const key in response.data.data) {
                                            const project = response.data.data[key];
                                            $this.listProjects.push(project);
                                        }
                                    }
                                }                                
                            })
                            .catch((error) => {
                                console.log('error', error)
                            })
                            .finally(() => ($this.load = true));
                    }
                }
            }
        });
    },
};
</script>

<template>
    <div class="container-fluid">        
        <div class="row no-gutters">
            <div class="col-lg-12">
                <PageHeader :title="title" :items="items" />
                <div class="row" v-if="total">
                    <div
                        v-for="(project, index) in listProjects"
                        :class="['mb-4 col-lg-3']"
                    >
                        <div class="wrap-item">
                            <div :style="`${project.url_image ? 'background-image:url('+publicPath+'projects/'+project.url_image+')' : '' }`" class="item-project" v-if="authUserData.roles[0].name === 'manager' || authUserData.roles[0].name === 'administrator'">
                                <router-link                                                
                                    :to="{
                                        name: 'analytics',
                                        params: { slug: project.slug, id: project.id },
                                    }"                                                                                      
                                >
                                <div> 
                                    <h6>{{ project.title }}</h6>    
                                    <span class="badge badge-primary">{{ statisticalProject(project.tasks)+'%' }}</span>
                                    <div class="date_time">
                                        <p v-if="project.description && project.description != 'undefined'">{{ project.description ? trimString(project.description) : '' }}</p>
                                        <span class="date">
                                            Start: {{ formatDate(project.start_time) }}
                                        </span>
                                        <span class="date">
                                            End: {{ formatDate(project.end_time) }}
                                        </span>
                                        <b-button class="btn-primary btn_view">View</b-button>
                                    </div>
                                </div>       
                                </router-link>
                            </div>
                            <div :style="`${project.url_image ? 'background-image:url('+publicPath+'projects/'+project.url_image+')' : '' }`" class="item-project" v-else>
                                <router-link
                                    :to="{
                                        name: 'project',
                                        params: { slug: project.slug, id: project.id },
                                    }"                                     
                                >
                                    <div>
                                        <h6>{{ project.title }}</h6>
                                        <span class="badge badge-primary">{{ statisticalProject(project.tasks)+'%' }}</span>
                                        <div class="date_time">
                                            <p v-if="project.description && project.description != 'undefined'">{{ project.description ? trimString(project.description) : '' }}</p>
                                            <span class="date">
                                                Start: {{ formatDate(project.start_time) }}
                                            </span>
                                            <span class="date">
                                                End: {{ formatDate(project.end_time) }}
                                            </span>
                                            <b-button class="btn-primary btn_view">View</b-button>
                                        </div>
                                    </div>
                                </router-link>
                            </div>
                            <div class="item-icon">
                                <div
                                    class="icon-active"
                                    title="Có hoạt động mới trong bảng này."
                                ></div>
                                <div
                                    class="icon-star"
                                    title="Bấm để gắn dấu sao bảng này. Bảng này sẽ được thêm vào danh sách được đánh dấu sao của bạn."
                                >
                                    <i class="ri-star-line"></i>
                                </div>
                            </div>                            
                            <!-- <div class="spinner-border m-5" role="status" v-if="!load">
                                <span class="visually-hidden">Loading...</span>
                            </div> -->
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-3" v-if="!load">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <h2 v-else>
                    Không có dự án nào 
                </h2>
            </div>
        </div>
    </div>
    <!-- container-fluid -->
</template>
<style lang="scss" scoped>
    .wrap-item {
        border-radius: 5px;
        background-color: rgb(0, 121, 191);
        transition: all .3s ease-in-out;
        position: relative;
        top:0;
        overflow: hidden;
        &:hover {
            transition: all .3s ease-in-out;
            top: -5px
        }
        .item-icon {
            display: none;
        }
        .item-project {            
            min-height: 130px;
            position: relative;
            a { 
                color: #fff;
                position: absolute;
                width: 100%;
                height: 100%;
                padding:20px;
                display: flex;
                flex-direction: column;
                &:hover {
                    background-color: rgba($color: #000000, $alpha: .2);
                }
                p {
                    text-transform: uppercase;
                    font-size: 16px;
                    font-weight: bold;
                    padding-right: 50px;
                }
                .badge {
                    position: absolute;
                    top: 20px;
                    right: 0;
                    padding: 6px 8px;
                    border-radius: 30px 0px 0px 30px;
                    background: RED;
                }
                .date {
                    font-size: 14px;
                    text-transform: capitalize;
                    font-style: italic;
                }
            }
        }
    }
    .btn_add_image{
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
    .images_projects {
        width: 150px;
        height: 200px;
        position: relative;
        img{
            width: 100%;
            height: 100%;
            position: absolute;
            object-fit: cover;
        }
    }
</style>