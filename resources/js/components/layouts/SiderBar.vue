<script>
import simplebar from "simplebar-vue";
import { checkRolesAccess, checkPermissionAccess } from '@/js/middleware/access.js';
import {
    projectMethods,
    projectGetters,
    taskMethods,
    authMethods,
    taskGetters,
    authGetters,
} from "@/js/store/helpers";
export default {
    data() {
        return {
            publicPath: process.env.PUBLIC_URL+'projects/',
            lastPage: 0,
            currentPage: 1,
        };
    },
    components: {
        simplebar,
    },
    computed: {
        ...projectGetters,
        ...taskGetters
    },
    methods: {
        ...projectMethods,
        ...taskMethods,
        checkRolesAccess(roles) {
            return checkRolesAccess(roles);
        },
    },
    async created() {       
    },
    mounted() {
        
    },
};
</script>
<template>
    <!-- ========== Left Sidebar Start ========== -->
    <div class="vertical-menu">
        <simplebar class="h-100" ref="currentMenu" id="my-element">
            <!--- Sidemenu -->
            <div id="sidebar-menu">
                <!-- Left Menu Start -->
                <ul class="metismenu list-unstyled" id="side-menu">
                    <li>
                        <router-link :to="{
                            name: 'dashboard',
                        }" class="side-nav-link-ref">
                            <i class="bx ri-dashboard-line"></i><span>Dashboard</span>
                        </router-link>
                    </li>
                    <li v-if="checkRolesAccess(['administrator', 'leader'])">
                        <router-link :to="{
                            name: 'alluser',
                        }" class="side-nav-link-ref">
                                <i class="ri-user-fill"></i><span>User Manager</span>
                            </router-link>
                    </li>
                    <li v-if="checkRolesAccess(['administrator', 'manager'])">
                        <router-link :to="{
                            name: 'create-project',
                        }" class="side-nav-link-ref">
                                <i class="ri-add-box-fill"></i><span>Create Project</span>
                            </router-link>
                    </li>
                    <!-- v-if="checkRolesAccess(['administrator', 'manager'])" -->
                    <li>
                        <router-link :to="{
                            name: 'list-projects',
                        }" class="side-nav-link-ref">
                                <i class="ri-add-box-fill"></i><span>Dự Án</span>
                            </router-link>
                    </li>
                    <li>
                        <router-link :to="{
                            name: 'my-task',
                        }" class="side-nav-link-ref">
                                <i class="ri-table-line p-0"></i> <span>Tasks của bạn</span>
                            </router-link>
                    </li>
                    <!-- <li>
                        <a><i class="ri-calendar-2-line p-0"></i> <span>Lịch</span></a>
                    </li>     -->
                </ul>
            </div>
            <!-- Sidebar -->
        </simplebar>
    </div>
    <!-- Left Sidebar End -->
</template>
<style lang="scss">
    .more {
        padding: 5px 25px;
        background: #c7820f;
        margin-top: 10px;
        display: inline-block;
        border-radius: 5px;
        color: #fff;
    }
    .workspace_view{
        color: #8590a5;
        padding-left: 25px;
        p{
            margin-bottom: 5px;
        }
       
        .calendar,.table{
            color: #8590a5;
            float: none;
            cursor: pointer;
            transition: all 0.4s;
            .btn_more{
                 display: none;
            }
           
            
            p{
            margin-bottom: 0 !important;
         }
            i{
                font-size: 17.6px;
            }
            p{
                margin-left: 7px !important;
            }
        }
        .calendar:hover, .table:hover{
            color: #d7e4ec;
            .btn_more{
                 display: block;
            }

        }
        
    }
    .list_table{
        color: #8590a5;
        padding: 0 1.5rem;
        font-family: "Inter", sans-serif;
        font-weight: 500;
        font-size: 13px;
        h6 {
            color: #8590a5;
            border-bottom: 0.5px dotted #8590a5;
            padding-bottom: 8px;
            margin-bottom: 8px !important;
            font-size: 15px;
            width: 100%;
        }
        .project {
            .align-items-center {
                width: 100%;
            }
        }
        .list_table_header{
                cursor: pointer;
                i{
                    color: #d7e4ec;
                }
            }
             .list_table_header:hover{
                .btn_more{
                 display: block;
                 color: #d7e4ec;
            }
             }
        .btn_more{
                 display: none;
            }
        p{
            margin-bottom: 5px;
        }
        .project{
            cursor: pointer;
            height: 25px;
        }
       
        .list_table_content:hover{
            .list_icon{
                i{
                display: inline-block;
             }
            }
             
        }
        .list_table_content{
            color: #8590a5;
            .list_icon{
                 color:#d7e4ec;
                 i{
                    display: none;
                    font-size: 12px;
                 }
            }
            i{
                font-size: 17.6px;
            }
            p{
                margin-bottom: 0 !important;
                margin-left: 7px !important;
                font-size: 13px;
                text-transform: capitalize;
                /* word-wrap: break-word; */
                white-space: nowrap;
                overflow: hidden;
                /* border: 1px solid; */
                text-overflow: ellipsis;
                width: calc(100% - 37px);
            }
            .image{
                width: 30px;
                height: 25px;
                background: antiquewhite;
                border-radius: 3px;
                background-repeat: no-repeat;
                background-size: cover;
                background-position: center;
            }
        }
    }
</style>