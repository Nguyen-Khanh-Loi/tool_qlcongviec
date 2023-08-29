<script>
import { mapActions } from "vuex";
import { userHelper } from "@/js/helpers/users";
import { socket } from "@/js/store/modules/socket";
import { dateHelper } from "@/js/helpers/datehelper";
import {
    socketMethods,
    taskMethods,
    taskGetters,
    authMethods,
    authGetters,
} from "@/js/store/helpers";
import moment from "moment";
export default {
    props: {
        infoAuth: {
            type: Object,
            default: () => {
                return false;
            },
        },
    },
    data() {
        return {
            languages: [
                {
                    flag: "images/us.jpg",
                    language: "en",
                    title: "English",
                },
                {
                    flag: "images/french.jpg",
                    language: "fr",
                    title: "French",
                },
                {
                    flag: "images/spain.jpg",
                    language: "es",
                    title: "spanish",
                },
            ],
            current_language: "en",
            avatar: "images/avatar.png",
            publicPath: process.env.PUBLIC_URL,
            load: true,
            dataSearch: false,
            nameSearch: null,
            messenger: false,
            check: false,
            showModalNotification: false,
            count: 0,
            slug: this.$route.params.slug,
        };
    },
    computed: {
        ...authGetters, 
        fullName() {
            return userHelper.fullName(this.infoAuth);
        },       
    },
    methods: {
        ...authMethods,
        ...taskMethods,
        ...socketMethods,
        // ...mapActions(["logout", "auth"]),
        async onSearchProject(e) {
            var keySearch = e.target.value;
            var data = { keySearch: keySearch };
            if (this.load && keySearch.length > 0) {
                // this.load = false;
                var results = await axios.post("/api/project/search", data);
                if (results.status == 200) {
                    if (results.data.length > 0) {
                        this.dataSearch = results.data;
                        this.messenger = false;
                    } else {
                        this.messenger =
                            "Không tìm thấy nội dung phù hợp tìm kiếm của bạn";
                    }
                    this.check = true;
                }
                // this.load = true
            } else {
                this.dataSearch = false;
            }
        },
        hiddenSearch() {
            this.nameSearch = null;
            this.dataSearch = false;
            this.check = false;
        },
        countNotifications(data){
            return Object.keys(data).length;
        },
        showAvatar(url){
            return userHelper.avatar(url);
        },
        fullNameAuthor(user) {
            return userHelper.fullName(user);
        },        
        onDateDuration(date){
            return dateHelper.showDateDuration(date);
        },
        /*showContentNotifications(data){                       
            var html = '';
            if (!data.date_remove_user) {
                html = `<p class="mb-0 mt-3 text_content">Thêm bạn ${this.onDateDuration(data.date_add_user)}</p>`;
                return html;
            } else {
                var date_remove = moment(data.date_remove_user).unix();
                var date_add = moment(data.date_add_user).unix();
                if (date_add > date_remove) {
                    html = `
                    <p class="mb-0 mt-3 text_content">Thêm bạn ${this.onDateDuration(data.date_add_user)}</p>
                    <p class="mb-0 mt-3 text_content">Xóa bạn ${this.onDateDuration(data.date_remove_user)}</p>
                    `;
                }else{
                    html = `
                    <p class="mb-0 mt-3 text_content">Xóa bạn ${this.onDateDuration(data.date_remove_user)}</p>
                    <p class="mb-0 mt-3 text_content">Thêm bạn ${this.onDateDuration(data.date_add_user)}</p>
                    `;
                }
            }
            return html;
        },*/
        getNotification(data){
            var html = '';
            switch (data['action']) {
                case 'create':
                    html = `${ this.fullNameAuthor(data.users) + ' đã tạo task mới '+this.onDateDuration(data.created_at)}`;
                    break;
                case 'approve':
                    html = `${ this.fullNameAuthor(data.users) + ' đã chấp nhận task của bạn '+this.onDateDuration(data.created_at)}`;
                    break;
                case 'disapprove':
                    html = `${ this.fullNameAuthor(data.users) + ' đã từ chối task của bạn '+this.onDateDuration(data.created_at)}`;
                    break;
                default:
                    break;
            }
            return html;
        },
        getTasks(id){
            var currentNotice = this.dataNotifications.filter((item)=>{
                if (item.id === id) {
                    return item;
                }
            });
            return currentNotice;
        },
        async onRedirect(data){
            this.showModalNotification = false;
            var id = data['task_id']
            var currentNotice = this.getTasks(id);            
            var slugProject = currentNotice[0].projects.slug
            var slugTask = currentNotice[0].slug;
            if (currentNotice) {     
                var results = await this.getListTasks({'slug': slugProject});
                if (data['show'] == 'project') {
                    this.$router.push({
                        name: 'project',
                        params: { 
                            'slug': slugProject,
                        }
                    });
                    return
                }
                
                if (data['show'] == 'task') {
                    this.$router.push({
                        name: 'viewtask',
                        params: { 
                            'slug': slugProject,
                            'name': slugTask
                        }
                    });
                    var elementTask = document.getElementById('task_'+id);
                    if (elementTask) {
                        elementTask.click();
                    }
                        
                }
            }
        },
        async handleTask(obj){
            var current = this.getTasks(obj['task_id']);
            var updateTaskData = {};
            updateTaskData['action'] = obj['action'];
            updateTaskData['task_id'] = obj['task_id'];
            updateTaskData['user_id'] = current[0].user_id; // user create task
            updateTaskData['auth_id'] = this.authUserData.id; // user check request task
            updateTaskData['project_id'] = current[0].project_id; // user check request task
            updateTaskData['info_task'] = {};
            updateTaskData['info_task']['active'] = 1;
            updateTaskData['info_task']['reject'] = obj['action'] == 'disapprove' ? 1 : 0;
            updateTaskData['info_task']['card_id'] = obj['action'] == 'disapprove' ? 5 : 2; // sẽ updated card id
            var results = await this.updateTask(updateTaskData);            
            if (results.status == 200) {
                if (results.data) {
                    var _result = results.data;
                    // update value store
                    if (typeof this.$store.getters.listTasks[obj['task_id']] != 'undefined') {
                        this.$store.getters.listTasks[obj['task_id']]['card_id'] = _result.card_id;
                        this.$store.getters.listTaskDraggable[current[0]['card_id']] = this.$store.getters.listTaskDraggable[current[0]['card_id']].filter( (item) => {
                            return item != obj['task_id'];
                        });
                        this.$store.getters.listTaskDraggable[_result['card_id']].push(obj['task_id']);
                    }
                    
                    // remove notification
                    if (typeof this.$store.getters.dataNotifications != 'undefined') { 
                        // notification forr user create task
                        current[0]['request_logs']['action'] = obj['action'];
                        current[0]['users'] = this.authUserData;
                        var data = {      
                            'data':current[0],         
                            'event': 'request tasks',
                        };
                        this.onEventChat(data);
                        let removeIndex;
                        this.$store.getters.dataNotifications.filter((item, key) => {
                            if (item.id === obj['task_id']) {
                                removeIndex = key
                            }
                        });
                        this.$store.getters.dataNotifications.splice(removeIndex, 1);
                        
                    }
                }
            }
        },
        // check task viewed
        async focusTasks(event, data){            
            var target = event.target; // Event target element
            var parent = target.parentNode; // Parent element
            if (parent) {
                var listClass = parent.className.split(' ');
                if (listClass.includes('btn') || listClass.includes('action')) {
                    return;
                }
            }else{

            }
            var roles = this.infoAuth.roles[0]['name'];
            //check roles leader and admin
            var check_role = userHelper.checkRoles(roles) && data.request_logs.action == 'create' ? true : false;
            const dataStatus = {
                'task_id': data.id,
                'author_id': data.users.id,
                'project_id': data.project_id,
                'check_role': check_role,          
            }
            let res = await axios.post(`/api/notifications/update`, dataStatus);
            if (res) {
                let removeIndex;
                this.$store.getters.dataNotifications.filter((item, key) => {
                    if (item.id === data.id) {
                        removeIndex = key
                    }
                });
                this.$store.getters.dataNotifications.splice(removeIndex, 1);
            }
        }
    },
    async created() {
        this.notifications();
        await this.auth();  
    },
    async mounted() {
        // await this.auth();
        let elementNotification = document.querySelector('.notification')
        document.addEventListener('click', (e) => {
            const check = e.composedPath().includes(elementNotification);
            if(!check) {
                this.showModalNotification = false; 
            }         
        });
    },
};
</script>
<template>
    <header id="page-topbar">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box">
                    <a href="/" class="logo logo-light">
                        <span class="logo-sm">
                            <img
                                :src="`${publicPath + 'images/logo.png'}`"
                                alt
                                height="22"
                            />
                        </span>
                        <span class="logo-lg">
                            <img
                                :src="`${publicPath + 'images/logo.png'}`"
                                alt
                                height="20"
                            />
                        </span>
                    </a>
                </div>

                <button
                    type="button"
                    class="btn btn-sm px-3 font-size-24 header-item waves-effect"
                    id="vertical-menu-btn"
                >
                    <i class="ri-menu-2-line align-middle"></i>
                </button>

                <!-- App Search-->
                <form class="app-search d-none d-lg-block">
                    <div class="position-relative">
                        <input
                            type="text"
                            class="form-control search-project"
                            placeholder="Tìm kiếm dự án"
                            v-model="nameSearch"
                            @input="onSearchProject"
                        />
                        <span class="ri-search-line"></span>
                    </div>
                    <div id="results_projects" v-if="check">
                        <div class="hidden-search" @click="hiddenSearch"></div>
                        <ul :class="['wrap-data']" v-if="!messenger">
                            <li v-for="project in dataSearch">
                                <router-link
                                    :to="{
                                        name: 'analytics',
                                        params: {
                                            slug: project.slug,
                                            id: project.id,
                                        },
                                    }"
                                    v-if="
                                        infoAuth.roles[0].name === 'manager' ||
                                        infoAuth.roles[0].name ===
                                            'administrator'
                                    "
                                >
                                    {{ project.title }}
                                </router-link>
                                <router-link
                                    :to="{
                                        name: 'project',
                                        params: {
                                            slug: project.slug,
                                            id: project.id,
                                        },
                                    }"
                                    v-else
                                >
                                    {{ project.title }}
                                </router-link>
                            </li>
                        </ul>
                        <div v-else :class="['wrap-data']">
                            <div
                                class="d-flex flex-column justify-content-center text-center"
                            >
                                <i
                                    class="ri-search-eye-line mt-2 mb-2 fs-4"
                                ></i>
                                <h6 class="mb-2">{{ messenger }}</h6>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="d-flex align-items-center">
                <div
                    class="notification me-3"
                    @click="showModalNotification = true"
                >
                    <i class="ri-notification-2-line"></i>
                        <p class="active" v-if="countNotifications(dataNotifications) > 0">{{ countNotifications(dataNotifications) > 9 ? '9+' : countNotifications(dataNotifications)  }}</p>
                    <div
                        class="dropdown_container modal-notification"
                        v-if="showModalNotification == true"
                    >
                        <div class="list_content">
                            <div
                                class="dropdown_header d-flex align-items-center justify-content-between"
                            >
                                <span>THÔNG BÁO</span>
                                <!-- <div
                                    class="btn_toggle d-flex align-items-center"
                                >
                                    <span class="me-2"
                                        >Chỉ hiển thị chưa đọc</span
                                    >
                                    <label class="switch me-2">
                                        <input
                                            type="checkbox"
                                            @click="toggleCheckbox"
                                        />
                                        <div class="slider round"></div>
                                    </label>
                                    <i class="ri-more-2-fill m-0"></i>
                                </div> -->
                            </div>
                            <hr />
                            <div v-if="countNotifications(dataNotifications) > 0">
                                <!-- <p class="cursor-pointer">
                                    Đánh dấu tất cả là đã đọc
                                </p> -->
                                <div class="dropdown_content" v-for="noti in dataNotifications" role="button" @click="focusTasks($event,noti)">
                                    <p @click="onRedirect({'task_id': noti.id, 'show':'task'})" class="name_task">{{ `${noti.title}` }}</p>
                                    <p @click="onRedirect({'task_id': noti.id, 'show':'project'})" class="name_project"><strong>Dự án</strong><code>{{ '['+noti.projects.code+']' }}</code>{{ `${noti.projects.title}` }}</p>
                                    <div class="action d-flex" v-if="noti.request_logs && noti.request_logs.action == 'create'">
                                        <button @click="handleTask({'action':'approve', 'task_id': noti.id})" type="button" class="btn btn-success"><i class="ri-check-line"></i></button>
                                        <button @click="handleTask({'action':'disapprove', 'task_id': noti.id})" type="button" class="btn btn-danger"><i class="ri-close-fill"></i></button>
                                    </div>
                                    <div class="user">
                                        <div class="d-flex">
                                            <div class="avatar">
                                                <img
                                                    :src="`${showAvatar(noti.users.avatar)}`"
                                                    :alt="`${fullNameAuthor(noti.users)}`"
                                                />
                                            </div>
                                            <p v-if="noti.request_logs" class="name" v-bind:innerHTML="getNotification({'action':noti.request_logs.action, 'users':noti.users, 'created_at':noti.created_at})"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <b-dropdown variant="white" right toggle-class="header-item">
                    <template v-slot:button-content>
                        <img
                            class
                            :src="`${publicPath + 'images/us.jpg'}`"
                            alt="Header Language"
                            height="16"
                        />
                    </template>
                    <b-dropdown-item
                        class="notify-item"
                        v-for="(entry, i) in languages"
                        :key="`Lang${i}`"
                        :value="entry"
                        :link-class="{
                            active: entry.language === current_language,
                        }"
                    >
                        <img
                            :src="`${publicPath + entry.flag}`"
                            alt="user-image"
                            class="me-1"
                            height="12"
                        />
                        <span class="align-middle">{{ entry.title }}</span>
                    </b-dropdown-item>
                </b-dropdown>
                <b-dropdown
                    right
                    variant="black"
                    toggle-class="header-item"
                    class="d-inline-block user-dropdown"
                >
                    <template v-slot:button-content>
                        <img
                            class="rounded-circle header-profile-user"
                            alt="Header Avatar"
                            :src="`${
                                infoAuth.avatar
                                    ? publicPath + 'users/' + infoAuth.avatar
                                    : publicPath + avatar
                            }`"
                        />
                        <span class="d-none d-xl-inline-block ms-1">
                            {{ fullName }}
                        </span>
                    </template>
                    <!-- item-->
                    <router-link
                        class="dropdown-item"
                        :to="{ name: 'Profile User' }"
                    >
                        <i class="ri-user-line align-middle mr-1"></i> Profile
                    </router-link>
                    <a class="dropdown-item" href="#">
                        <i class="ri-wallet-2-line align-middle me-1"></i>
                        My Wallet
                    </a>
                    <a class="dropdown-item d-block" href="#">
                        <i class="ri-settings-2-line align-middle me-1"></i>
                        Settings
                    </a>
                    <a class="dropdown-item" href="#">
                        <i class="ri-lock-unlock-line align-middle me-1"></i>
                        Lockscreen
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" @click="logout">
                        <i
                            class="ri-shut-down-line align-middle me-1 text-danger"
                        ></i>
                        Logout
                    </a>
                </b-dropdown>
            </div>
        </div>
    </header>
</template>

<style lang="scss" scoped>
.notify-item {
    .active {
        color: #16181b;
        background-color: #f8f9fa;
    }
}
.notification {
    position: relative;
    i {
        cursor: pointer;
    }
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 20px;
    }

    .switch input {
        display: none;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: 0.4s;
        transition: 0.4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 17px;
        width: 17px;
        left: 4px;
        bottom: 2px;
        background-color: white;
        -webkit-transition: 0.4s;
        transition: 0.4s;
    }

    input:checked + .slider {
        background-color: #1f845a;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #1f845a;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(21px);
        -ms-transform: translateX(21px);
        transform: translateX(21px);
    }

    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
    button {
        position: relative;
    }
    .active {
        position: absolute;
        background: red;
        border-radius: 100%;
        top: 2px;
        right: -7px;
        font-size: 12px;
        width: 20px;
        height: 20px;
        line-height: 20px;
        color: #fff;
        font-weight: 900;
        padding: 0;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    i {
        font-size: 20px;
        margin-top: 6px;
        display: block;
    }
    .dropdown_content:first-child {
        // background: red;
    }
    .dropdown_container {
        width: 424px;
        padding: 10px;
        position: fixed;
        background: #fff;
        right: 0;
        top: 74px;
        z-index: 1000;
        .list_content {
            position: relative;
            z-index: 1000;
        }

        .dropdown_content {
            margin-top: 15px;
            width: 360px;
            border: 2px solid #f1f2f4;
            border-radius: 5px;
            position: relative;
            .active {
                background: #0c66e4;
                width: 16px;
                height: 16px;
                border-radius: 100%;
                top: 0px;
                right: -30px;
            }
            p {
                margin: 0 !important;
                padding: 5px 10px!important;
            }
            .text_content {
                margin-left: 30px !important;
            }
            .name_project {
                padding: 5px 10px !important;
            }
            .user {
                background: #f1f2f4;
                padding: 10px;
                .avatar {
                    width: 20px;
                    height: 20px;
                    img {
                        width: 100%;
                    }
                }
                .name {
                    margin-left: 10px !important;
                    padding: 0 !important;
                }
            }
        }
    }
    .action {
        position: absolute;
        top:5px;
        right:5px;
        button {
            width: 20px;
            height: 20px;
            padding: 0;
            &:first-child{
                margin-right: 5px;
            }
            &:last-child{
                margin-right: 0px;
            }
            i {
                line-height: 20px;
                font-size: 14px;
                margin: 0;
            }
        }
    }
    .name_task {
        padding-right: 50px;
    }
}
</style>
