<script>
import login from './components/account/Login.vue';
import loginLayout from './components/account/LoginLayout.vue';
import register from './components/account/Register.vue';
import dashboard from './components/layouts/Dashboard.vue';
import layouthtml from './components/layouts/Layout.vue';
import Header from "./components/layouts/Header.vue";
import Footer from "./components/layouts/Footer.vue";
import SideBar from "./components/layouts/SiderBar.vue";
import { socket } from "@/js/store/modules/socket";
import {
    socketMethods,
    socketGetters
} from "@/js/store/helpers";

export default {
    components: {
        login,
        loginLayout,
        register,
        dashboard,
        layouthtml,
        Header,
        Footer,
        SideBar
    },
    data() {
        return {
            infoAuth : {},
        }
    },
    computed: {
        ...socketGetters,
        loginResponse() {
            let output = undefined;

            if (
                this.$store.getters.getLoginResponse.authenticated !==
                undefined &&
                output == undefined
            ) {
                output = this.$store.getters.getLoginResponse;
            }

            if (
                JSON.parse(sessionStorage.getItem('loginResponse')) !==
                undefined &&
                output == undefined
            ) {
                output = JSON.parse(sessionStorage.getItem('loginResponse'));
            }

            if (output == undefined) {
                output = {
                    authenticated: false,
                };
            }
            return output;
        },

        authUser() {
            if (this.$store.getters.getAuthUser.id !== undefined) {
                return this.$store.getters.getAuthUser;
            }
            return JSON.parse(sessionStorage.getItem('authUser'));
        },

        roleUser() {
            if (this.$store.getters.getAuthUser.id !== undefined) {
                return this.$store.getters.getAuthUser;
            }

            return this.authUser.roles[0]['name'];
        },
    },
    methods: {
        ...socketMethods,
        getInfoAuth(){
            this.infoAuth = this.$store.getters.authUserData;
        }, 
        updateInfoAuth(data){
            this.infoAuth = data;
        },
        showNotification(data = null) {
            var messenger = !data['messenger'] ? 'Bạn có tin nhắn mới' : data['messenger'];
            if ('Notification' in window) {
                Notification.requestPermission((permission) => {
                    if (permission === 'granted') {
                        const notification = new Notification(`${messenger}`, {
                            // body: 'Bạn có một tin nhắn mới',
                        // icon: '/path/to/icon.png',
                        });
                        // notification.onclick = function () {
                        //     window.open($url);
                        // };
                    }
                });
            }
        },
    },
    created: () => {        
        document.body.removeAttribute("data-layout", "horizontal");
        document.body.removeAttribute("data-topbar", "dark");
        document.body.setAttribute("data-sidebar", "dark");
    },
    mounted() {
        this.getInfoAuth();
        this.connect();   
        if (this.loginResponse.authenticated) {
            this.joinUser(this.infoAuth.id)
        }
        // console.log('loginResponse.authenticated', this.loginResponse.authenticated);
        // console.log('authUser', this.infoAuth);
        const ref = this
        // new chat
        socket.on('listen messenger', function(data){
            try {
                ref.$store.getters.listTasks[data.task_id].messengers.unshift(data)
                if (typeof ref.$store.getters.currentTask.messengers != 'undefined') {
                    if (ref.$store.getters.currentTask.id != data.task_id) {
                        ref.showNotification({'messenger': "Bạn có tin nhắn mới"});
                    }
                }else{                
                    if (ref.$store.getters.currentTask.id != data.task_id) {
                        ref.showNotification({'messenger': "Bạn có tin nhắn mới"});
                    }
                } 
                ref.messenger = null;
            } catch (error) {
                console.log('error ',error)
            }
            
            // socket.removeListener('listen messenger');        
        });
        // remove messenger
        socket.on('listen remove messenger', function(data){
            if (data) {
                ref.$store.getters.listTasks[data.task_id].messengers = ref.$store.getters.listTasks[data.task_id].messengers.filter(item => item.id !== data.id)
            }
        });
        // edit messenger
        socket.on('listen edit messenger', function(data){
            if (data) {
                // update data messenger
                ref.$store.getters.listTasks[data.task_id].messengers = ref.$store.getters.listTasks[data.task_id].messengers.filter(function(item){
                    if (item.id == data.id) {
                        item.content = data.content,
                        item.status_edit = 1,
                        item.updated_at = data.updated_at
                    }
                    return item;
                })
            }
        });

        socket.on('notification task', (data) => {
            // console.log('data', data);
            // console.log('listTasks', ref.$store.getters.listTasks);
            // console.log('TaskDraggable', ref.$store.getters.listTaskDraggable);
            ref.showNotification({'messenger': "Bạn có một thông báo mới"});

            if (typeof ref.$store.getters.dataNotifications != 'undefined') {
                ref.$store.getters.dataNotifications.unshift(data);
            }

            try {

                // update data state of list task
                if (typeof ref.$store.getters.listTasks != 'undefined') {
                    if (typeof ref.$store.getters.listTasks[data.id] == 'undefined') {
                        ref.$store.getters.listTasks[data.id] = {};
                    }
                    ref.$store.getters.listTasks[data.id] = data;
                }
                // update data state list task move
                console.log(' ref.$store.getters.listTaskDraggable ',  ref.$store.getters.listTaskDraggable);
                if (typeof ref.$store.getters.listTaskDraggable != 'undefined') {

                    if (data.request_logs.action == 'create') {
                        if (!ref.$store.getters.listTaskDraggable[1].includes(data.id)) {
                            ref.$store.getters.listTaskDraggable[1].push(data.id);                        
                        } 
                    }

                    if (data.request_logs.action == 'approve') {
                        if (!ref.$store.getters.listTaskDraggable[2].includes(data.id)) {
                            ref.$store.getters.listTaskDraggable[2].push(data.id);                        
                        }                        
                    }                
                }
            } catch (error) {
                console.log('error update data state', error);
            }
            
        })
        // document.body.classList.remove("auth-body-bg");
        // if (this.loader === true) {
        //     document.getElementById("preloader").style.display = "block";
        //     document.getElementById("status").style.display = "block";
        setTimeout(function () {
            document.getElementById("preloader").style.display = "none";
            document.getElementById("status").style.display = "none";
        }, 2500);
        // } else {
        //     document.getElementById("preloader").style.display = "none";
        //     document.getElementById("status").style.display = "none";
        // }
        
        
    }
}
</script>
<template>
    <!-- <router-view></router-view> -->
    <div v-if="
        !loginResponse.authenticated
    ">
        <loginLayout />
    </div>
    <div v-else>
        <div id="preloader">
            <div id="status">
                <div class="spinner">
                    <i class="ri-loader-line spin-icon"></i>
                </div>
            </div>
        </div>
        <!-- Begin page -->
        <div id="layout-wrapper">
            <Header :infoAuth="infoAuth"/>
            <SideBar />
            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">
                <div class="page-content">
                    <!-- <dashboard /> -->
                    <router-view @updateInfoAuth="updateInfoAuth"/>
                </div>
                <!-- End Page-content -->
            </div>
            <!-- end main content-->
            <!-- <Footer /> -->
        </div>
        <!-- END layout-wrapper -->
    </div>
</template>