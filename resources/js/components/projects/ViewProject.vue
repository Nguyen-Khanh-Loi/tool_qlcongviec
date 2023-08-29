<script>
import { VueDraggableNext } from "vue-draggable-next";
import PageHeader from "@/js/components/layouts/page-header.vue";
import { userHelper } from "@/js/helpers/users";
import moment from "moment";
import { taskHelper } from "@/js/helpers/helptask";
import { dateHelper } from "@/js/helpers/datehelper";
import { filterDataProject } from "@/js/helpers/filter";
import {
    taskMethods,
    authMethods,
    taskGetters,
    authGetters,
    socketMethods
} from "@/js/store/helpers";
import MoveCard from "./project/MoveCard.vue";
import CheckList from "./project/CheckLists.vue";
import TaskDeadline from "./project/TaskDeadline.vue";
import FilesTask from "./project/FilesTask.vue";
import FileUploads from "./project/UploadFiles.vue";
import UserTask from "./project/AddUsersTask.vue";
import Labels from "./project/Labels.vue";
import Works from "./project/Works.vue";
import DateTasks from "./project/DateTask.vue";
import Description from "./project/Description.vue";
import AddUser from "./project/AddUsersProject.vue";
import MemberTask from "./project/MemberTask.vue";
import LabelTask from "./project/LabelTask.vue";
import FilterTasks from "./filter/FilterTasks.vue";
import ChatTasks from "./project/ChatTasks.vue";
import { VueEditor } from "vue3-editor";
export default {
    page: {
        title: "Gosu Board",
        meta: [{ name: "description" }],
    },
    components: {
        draggable: VueDraggableNext,
        PageHeader,
        CheckList,
        TaskDeadline,
        MoveCard,
        FilesTask,
        FileUploads,
        UserTask,
        Labels,
        Works,
        DateTasks,
        Description,
        AddUser,
        MemberTask,
        LabelTask,
        FilterTasks,
        VueEditor,
        ChatTasks
    },
    data() {
        return {            
            data: {},
            buttonAdd: {},
            newTasks: {},
            allPopUp: {},
            showModal: false,
            showActive: false,
            project_id: false,
            slug: this.$route.params.slug,
            nameTask: this.$route.params.name,
            placeholder: "Nhập tiêu đề cho thẻ này...",
            taskUpdate: {},
            title: "",
            items: [
                {
                    text: "Gosu",
                    href: "/",
                },
                {
                    text: "Gosu Board",
                    active: true,
                },
            ],
            publicPath : process.env.PUBLIC_URL,
            countFilter: {},
            infoAuth: {},
            dataCurrentTask: false // data of current task
        };
    },
    computed: {
        ...taskGetters,
        ...authGetters,
    },
    methods: {
        ...taskMethods,
        ...authMethods,
        ...socketMethods,
        handlerClick($id) {
            for (const key in this.listCard) {
                const cardProject = this.listCard[key];
                this.buttonAdd[cardProject.id] = false;
            }
            this.buttonAdd[$id] = true;
        },

        async createTask($id) {
            const currentRoles = this.authUserData.roles[0]['name'];
            var cardId = this.listCard[0]['id'];
            if (userHelper.checkRoles(currentRoles)) {
                cardId = $id;
            }
            this.newTasks["card_id"] = cardId;
            this.newTasks["project_id"] = this.$store.getters.projectInfo.id;
            this.newTasks["postion"] = $id;

            if (this.authUserData.department_id) {
                this.newTasks["department_id"] = this.authUserData.department_id;
            }
            
            if (currentRoles == 'administrator') {
                this.newTasks["department_id"] = parseInt(sessionStorage.getItem('departmentId'));
            }
            
            // console.log( this.newTasks);
            // return;
            if (this.newTasks && this.newTasks["title_" + $id]) {  // title task by position card id         
                var results = await this.createNewTask(this.newTasks);
                if (typeof results != "undefined") {
                    var newTask = results.data;
                    if (
                        typeof this.listTaskDraggable[newTask.card_id] ==
                        "undefined"
                    ) {
                        this.listTaskDraggable[newTask.card_id] = [];
                    }
                    if (
                        typeof this.taskDraggableStore[newTask.card_id] ==
                        "undefined"
                    ) {
                        this.taskDraggableStore[newTask.card_id] = [];
                    }
                    this.listTasks[newTask.id] = newTask;
                    this.listTaskDraggable[newTask.card_id].push(newTask.id);
                    // send request create tasks
                    if (!userHelper.checkRoles(currentRoles)) {
                        var data = {
                            'leader': results["leaders"],
                            'tasks': newTask,
                            'event': 'request create tasks'
                        }
                        this.onEventChat(data); 
                    }     
                    // this.taskDraggableStore[newTask.card_id].push(newTask.id);
                    // console.log('taskDraggableStore1', this.taskDraggableStore);
                }
                this.newTasks = {};
            }
        },

        changeTask(event, cardId) {
            if (typeof event.added != "undefined") {
                this.taskUpdate["task_id"] = event.added.element;
                this.taskUpdate["info_task"] = {
                    card_id: cardId,
                };
                this.updateTask(this.taskUpdate);
            }
        },

        showTask(data) {
            this.$router.push({
                name: 'viewtask',
                params: { 
                    slug: data.projects.slug,
                    name: data.slug
                }
            });
            this.showModal = true;
            this.getCurrentTask(data);
        },

        hiddenModal(data) {
            this.getCurrentTask(false);
            this.$router.push({
                name: 'project',
                params: { 
                    slug: this.$route.params.slug,
                }
            });
            this.allPopUp = {};
            this.showModal = false; 
        },

        /**
         *
         * @param {*} value returm date
         */
        dateTime(value) {
            return moment(value).format("ll");
        },

        // calculate number check list
        calulateCheckList(data) {
            return taskHelper.calculateListWorkTodo(data);
        },

        async onPaste(pasteEvent, callback) {
            if (!this.allPopUp["editor"]) {
                if (pasteEvent.clipboardData == false) {
                    if (typeof callback == "function") {
                        callback(undefined);
                    }
                }
                var url = pasteEvent.clipboardData.getData("text");
                if (taskHelper.validateUrl(url)) {
                    var dataUrl = taskHelper.validateUrl(url);
                    var data = {
                        url: url,
                        name: dataUrl.hostname,
                        task_id: this.currentTask.id,
                    };
                    var results = await this.uploadFile(data);
                    if (results) {
                        this.listTasks[this.currentTask.id]["list_files"] =
                            results.list_files;
                        this.currentTask["list_files"] = results.list_files;
                    }
                } else {
                    var items = pasteEvent.clipboardData.items;
                    if (items == undefined) {
                        if (typeof callback == "function") {
                            callback(undefined);
                        }
                    }
                    for (var i = 0; i < items.length; i++) {
                        if (items[i].type.indexOf("image") == -1) continue;
                        var blob = items[i].getAsFile();
                        const fileUpload = new Blob([blob], blob);
                        const formData = new FormData();
                        formData.append("file", fileUpload, blob.name);
                        formData.append("task_id", this.currentTask.id);
                    }
                    if (typeof formData != "undefined") {
                        var results = await this.uploadFile(formData);
                        if (results) {
                            this.listTasks[this.currentTask.id] = results;
                            this.currentTask["list_files"] = results.list_files;
                        }
                    }
                }
            }
        },

        // hidden modal
        hideModalPopup(data) {
            this.allPopUp[data] = false;
        },
        // hidden modal
        showModalPopup(data) {
            this.allPopUp = {}
            this.allPopUp[data] = true;
        },

        // calculate files cards
        fileTasks(data) {
            return taskHelper.countFileTasks(data);
        },
        showAvatar(url){
            return userHelper.avatar(url);
        },
        fullName(user){
            return userHelper.fullName(user);
        },
        // show date deadline
        dateDeadLine(date){
            var dataDate = {
                'date': date,
                'format': 'DD/MM/YYYY'
            }
            return dateHelper.formatDate(dataDate);
        },
        //filter tasks
        onFilterTask(data){
            try {       
                var allTasks = this.$store.getters.listTasks; 
                var resultsDraggable = this.$store.getters.taskDraggableStore; 
                const dataTask = Object.values(allTasks);
                if (Object.keys(data).length > 0) {
                     // update data task
                    var _data = {
                        'tasks' : dataTask,
                        'types' : data
                    }
                    var resultsFilter = filterDataProject.filterTaskProject(_data);
                    // update data task in card
                    var _resultsDraggable = {}
                    for (const key in resultsDraggable) {
                        _resultsDraggable[key] = [];
                        resultsDraggable[key].filter(function(e) {
                            if (typeof resultsFilter[e] != 'undefined') {
                                _resultsDraggable[key].push(e)
                            }
                            return e; 
                        });         
                    }
                    this.countFilter['check'] = true;
                    this.countFilter['count'] = Object.keys(resultsFilter).length;
                    this.$store.commit('setTaskDraggable',_resultsDraggable);
                }else{
                    this.countFilter['check'] = false;
                    this.countFilter['count'] = 0;
                    this.$store.commit('setTaskDraggable', resultsDraggable); 
                }
                
            } catch (error) {
                console.log('error filter task', error)
            }
            
        },

        getInfoAuth(){
            this.infoAuth = this.$store.getters.authUserData;
        },
    },
    async created() {   
        this.data = {
            'department_id' : parseInt(sessionStorage.getItem('departmentId')),
            'slug': this.$route.params.slug,
        }        
        await this.getListTasks(this.data);         
        this.project_id = this.$store.getters.projectInfo.id;
        this.title = this.$store.getters.projectInfo.title,
        this.items = [
            {
                text: "Gosu",
                href: "/",
            },
            {
                text: this.title ,
                active: true,
            },
        ]       
        await this.auth();
        this.getInfoAuth(); 
        // show popup current task
        if (this.nameTask) {
            const dataAllTask = Object.values(this.listTasks);
            this.dataCurrentTask = dataAllTask.filter(item => item.slug === this.nameTask);
            this.getCurrentTask(this.dataCurrentTask[0]);
            this.showModal = true;
        }     
    },

    mounted() { 
        document.body.classList.remove("auth-body-bg");
        document.body.classList.add("page-task");
         // await this.auth();
        // let wrap_open_card = document.querySelector('.wrap-open-card')
        // document.addEventListener('click', (e) => {
        //     const check = e.composedPath().includes(wrap_open_card);
        //     if(!check) {
        //         this.buttonAdd = {} 
        //     }         
        // });
    },
};
</script>
<template>
    <!-- <pre>{{ JSON.stringify(listItemLabels, undefined, 4) }}</pre> @paste="onPaste"-->
    <b-modal
        v-model="showModal"
        @hide="hiddenModal"
        size="lg"
        hide-footer
        hide-header
    >
        <div :class="['container-fluid']">
            <div :class="['row']">
                <div
                    :class="[
                        'col-12 d-flex flex-row align-items-center justify-content-between',
                    ]"
                >
                    <div class="name_card">
                        <p class="d-flex flex-row fs-3">
                            <i class="ri-archive-fill"></i>
                            <span>{{ currentTask.title }}</span>
                        </p>
                        <span>Trong danh sách <a href="">Task</a></span>
                    </div>
                    <div :class="['btn_close']" @click="hiddenModal">
                        <i class="ri-close-line"></i>
                    </div>
                </div>
                <div :class="['col-9']">
                    <div :class="['content-main-info']">
                        <MemberTask />
                        <LabelTask />
                        <TaskDeadline
                            :deadline="currentTask.deadline"
                        ></TaskDeadline>
                    </div>
                    <div :class="['content-main-detail']">
                        <Description
                            @showModalPopup="showModalPopup"
                            @hideModalPopup="hideModalPopup"
                            :popupFiles="allPopUp['editor']"
                        ></Description>
                        <CheckList />
                        <FilesTask
                            @showModalPopup="showModalPopup"
                            @hideModalPopup="hideModalPopup"
                            :popupFiles="allPopUp['files1']"
                        ></FilesTask>
                    </div>
                    <ChatTasks :infoAuth="infoAuth" :projectId="project_id"></ChatTasks>                    
                </div>
                <div :class="['col-3 content_right']">
                    <div :class="['list-item']">
                        <h6>Thêm vào thẻ</h6>
                        <b-list-group>
                            <UserTask
                                @showModalPopup="showModalPopup"
                                @hideModalPopup="hideModalPopup"
                                :popupFiles="allPopUp['user']"
                            ></UserTask>
                            <Labels
                                :labels="listItemLabels"
                                @showModalPopup="showModalPopup"
                                @hideModalPopup="hideModalPopup"
                                :popupFiles="allPopUp['label']"
                            ></Labels>
                            <Works
                                @showModalPopup="showModalPopup"
                                @hideModalPopup="hideModalPopup"
                                :popupFiles="allPopUp['works']"
                            ></Works>
                            <DateTasks></DateTasks>
                            <FileUploads
                                @showModalPopup="showModalPopup"
                                @hideModalPopup="hideModalPopup"
                                :popupFiles="allPopUp['files']"
                            ></FileUploads>
                        </b-list-group>
                    </div>
                    <div :class="['list-item']">
                        <MoveCard
                            :cards="listCard"
                            @showModalPopup="showModalPopup"
                            @hideModalPopup="hideModalPopup"
                            :popupFiles="allPopUp['move']"
                        ></MoveCard>
                    </div>
                </div>
            </div>
        </div>
    </b-modal>

    <div class="container-fluid min-vh-100">
        <PageHeader :title="title" :items="items" />
        <!-- nav -->
        <div class="layoutview-header">
            <div class="layoutview-header-left">
                <h4 class="name_project">PROJECT</h4>
                <div
                    class="icon_star"
                    title="Đánh hoặc bỏ đánh dấu sao bảng này. Bảng được đánh dấu sao sẽ hiện ở đầu danh sách Bảng."
                >
                    <i class="ri-star-line"></i>
                </div>
                <a class="btn_display_working_space">
                    <div class="icon">
                        <i class="ri-group-line"></i>
                    </div>
                    <p>Hiển thị không gian làm việc</p>
                </a>
                <a class="btn_table">
                    <div class="icon">
                        <i class="ri-table-alt-line"></i>
                    </div>
                    <p>Bảng</p>
                </a>
                <div class="btn_dropdown">
                    <i class="ri-arrow-drop-down-line"></i>
                </div>
            </div>
            <div class="layoutview-header-right">
                <!-- <a class="btn_add-ons">
                    <div class="icon">
                        <i class="ri-rocket-2-line"></i>
                    </div>
                    <p>Tiện ích bổ sung</p>
                </a>
                <a class="btn_automation">
                    <i class="ri-flashlight-line"></i>
                    <p>Tự động hóa</p>
                </a> -->
                <FilterTasks :labels="listItemLabels" :number="countFilter" :users="projectUsers" @filterTask="onFilterTask"/>
                <!-- <a class="btn_share">
                    <div class="icon"><i class="ri-user-add-line"></i></div>
                    <p>Chia sẻ</p>
                </a>
                <a class="btn_menu">
                    <i class="ri-more-line"></i>
                </a> -->
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="media d-flex align-items-center">
                    <div class="me-3">
                        <img
                            :src="`${publicPath+'images/icon.png'}`"
                            alt
                            class="avatar-xs"
                        />
                    </div>
                    <div class="media-body">
                        <h5 class="mb-0">Gosu Dashboard</h5>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 add_user">                
                <AddUser :projectId="project_id"/>
            </div>
        </div>
        <div class="row wrap-card-task">
            <div
                v-for="(card, index) in listCard"
                :key="index++"
                :class="['col-3']"
            >
                <div class="card card_main">
                    <div class="card-body card_header pt-3 pb-2">
                        <h4 class="card-title">{{ card.title }}</h4>
                        <!-- end dropdown -->
                    </div>
                    <div class="card-body border-bottom card-content">
                        <div
                            :id="`${'wrap_card_' + card.id}`"
                            class="task-list"
                        >
                            <draggable
                                class="list-group"
                                group="tasks"
                                :list="listTaskDraggable[card.id]"
                                @change="changeTask($event, card.id)"
                            >
                                <div
                                    class="card task-box cursor-pointer"
                                    v-for="(task, index) in listTaskDraggable[
                                        card.id
                                    ]"
                                    :key="index"
                                    :id="`${'task_' + task}`"
                                    @click="showTask(listTasks[task])"
                                >
                                    <b-progress
                                        :value="
                                            calulateCheckList(
                                                listTasks[task].works
                                            ).percentTask
                                        "
                                        :max="100"
                                        :variant="`${
                                            calulateCheckList(
                                                listTasks[task].works
                                            ).percentTask == 100
                                                ? 'success'
                                                : ''
                                        }`"
                                    ></b-progress>
                                    <div class="card-body cursor-pointer">
                                        <div
                                            class="list_filter"
                                            v-if="listTasks[task].task_labels"
                                        >
                                            <div
                                                class="filter"
                                                v-for="label in listTasks[task]
                                                    .task_labels"
                                                :style="{ backgroundColor: `${label.color}`}"
                                            ></div>
                                        </div>
                                        <div class="float-end ml-2">
                                            <div>
                                                {{
                                                    dateTime(
                                                        listTasks[task]
                                                            .created_at
                                                    )
                                                }}
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <a href="#" class
                                                >#{{ listTasks[task].id }}</a
                                            >
                                        </div>
                                        <div>
                                            <h5 class="font-size-16">
                                                <a
                                                    href="javascript: void(0);"
                                                    class="text-dark"
                                                    >{{
                                                        listTasks[task].title
                                                    }}</a
                                                >
                                            </h5>
                                        </div>
                                        <div class="d-flex team mb-0">
                                            <div
                                                :class="[
                                                    'd-flex align-items-center',
                                                ]"
                                                v-if="
                                                    listTasks[task].deadline
                                                "
                                            >
                                                <i class="ri-time-line"></i>
                                                {{
                                                    dateDeadLine(listTasks[task].deadline)
                                                }}
                                            </div>
                                            <div
                                                :class="[
                                                    'd-flex align-items-center',
                                                ]"
                                                v-if="
                                                    listTasks[task].list_files
                                                "
                                            >
                                                <i class="ri-attachment-2"></i>
                                                {{
                                                    fileTasks(
                                                        listTasks[task]
                                                            .list_files
                                                    )
                                                }}
                                            </div>
                                            <div
                                                :class="[
                                                    'd-flex align-items-center',
                                                ]"
                                                v-if="
                                                    calulateCheckList(
                                                        listTasks[task].works
                                                    ).total != 0
                                                "
                                            >
                                                <i class="ri-checkbox-line"></i>
                                                {{
                                                    calulateCheckList(
                                                        listTasks[task].works
                                                    ).done +
                                                    "/" +
                                                    calulateCheckList(
                                                        listTasks[task].works
                                                    ).total
                                                }}
                                            </div>
                                            <div
                                                class="align-self-center d-flex"
                                                v-if="listTasks[task].members"
                                            >
                                                <div
                                                    class="list-member"
                                                    v-for="member in listTasks[
                                                        task
                                                    ].members"
                                                >
                                                    <div class="team-member">
                                                        <a
                                                            href="javascript: void(0);"
                                                            class="team-member d-inline-block"
                                                            v-b-tooltip.hover
                                                            data-placement="top"
                                                            :title="member.name"
                                                        >
                                                        
                                                        <!-- {{ member }} -->
                                                            <img
                                                                :src="`${showAvatar(member.avatar)}`"
                                                                :alt="`${fullName(member)}`"
                                                                :title="`${fullName(member)}`"
                                                                class="rounded-circle avatar-xs"
                                                            />
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </draggable>
                            <!-- end task card -->
                            <div class="wrap-open-card">
                                <div
                                    :class="['open-card']"
                                    v-if="buttonAdd[card.id]"
                                >
                                    <b-form-textarea
                                        :name="'card_id_' + card.id"
                                        v-model="newTasks['title_' + card.id]"
                                        :placeholder="placeholder"
                                        :id="card.id"
                                        rows="3"
                                        max-rows="6"
                                        :class="['mb-3']"
                                    ></b-form-textarea>
                                </div>
                                <button
                                    class="btn"
                                    v-if="!buttonAdd[card.id]"
                                    @click="handlerClick(card.id)"
                                >
                                    <i class="ri-add-line"></i>
                                    <span> Thêm thẻ</span>
                                </button>
                                <button
                                    class="btn btn-primary mt-1 waves-effect waves-light"
                                    v-else
                                    @click="createTask(card.id)"
                                >
                                    <i class="ri-add-line"></i>
                                    <span> Thêm thẻ</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style lang="scss">
    .wrap-card-task {
        .col-3 {
            width: 20%;
        }
    }
</style>