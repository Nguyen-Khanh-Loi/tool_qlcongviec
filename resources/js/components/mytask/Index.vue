<script>
import Board from "./Board.vue";
import { taskMethods, taskGetters, projectGetters, socketMethods } from "@/js/store/helpers";
import { userHelper } from "@/js/helpers/users";
import { dateHelper } from "@/js/helpers/datehelper";
import { listCardsMyTasks } from "./data";
import { VueDraggableNext } from "vue-draggable-next";
import MemberTask from "@Project/project/MemberTask.vue";
import LabelTask from "@Project/project/LabelTask.vue";
import TaskDeadline from "@Project/project/TaskDeadline.vue";
import Description from "@Project/project/Description.vue";
import CheckList from "@Project/project/CheckLists.vue";
import FilesTask from "@Project/project/FilesTask.vue";
import UserTask from "@Project/project/AddUsersTask.vue";
import Labels from "@Project/project/Labels.vue";
import Works from "@Project/project/Works.vue";
import DateTasks from "@Project/project/DateTask.vue";
import FileUploads from "@Project/project/UploadFiles.vue";
import MoveCard from "@Project/project/MoveCard.vue";
import ChatTasks from "@Project/project/ChatTasks.vue";
import Select2 from 'vue3-select2-component';
import { socket } from "@/js/store/modules/socket"
export default {
    components: {
        Board,
        draggable: VueDraggableNext,
        TaskDeadline,
        MemberTask,
        LabelTask,
        Description,
        CheckList,
        FilesTask,
        UserTask,
        Labels,
        Works,
        DateTasks,
        FileUploads,
        MoveCard,
        Select2,
        ChatTasks,       
    },
    data() {
        return {
            TabBoard:false,
            rotate: [],
            showCards: [],
            user: false,
            showAddtask: false,
            titleTask: [],
            listCardsMyTasks: false,
            listMyTasks: {},
            listTasksInCard: {},
            taskUpdate: {},
            showModal: false,
            allPopUp: {},
            taskData: {},
            myUsers: false,
        };
    },
    computed: {
        ...taskGetters,
        ...projectGetters
    },
    methods: {
        ...taskMethods,
        ...socketMethods,
        showAvatar() {
            return userHelper.avatar(this.user.avatar);
        },
        fullName() {
            return userHelper.fullName(this.user);
        },
        infoUser() {
            this.user = JSON.parse(sessionStorage.getItem("authUser"));
        },
        showDate(dateTask) {
            const dataDate = {
                date: dateTask,
                format: "MMMM DD YYYY",
            };
            return dateHelper.formatDate(dataDate);
        },
        onShowAddTasks(position) {
            this.showAddtask = [];
            this.showAddtask[position] = true;
        },
        //
        async createTask(position) {
            if (this.taskData) {                
                this.taskData['action'] = 'my-task'; 
                var newTask = await this.createNewTask(this.taskData);
                if (newTask) {
                    let project_id =this.taskData['project_id'];                   
                    if(this.projectSiderBar.data && this.projectSiderBar.data.length > 0){
                        if(project_id){
                            var project = this.projectSiderBar.data.filter(item => {
                                if (item.id == project_id) {
                                    return item;
                                }
                            });
                            if (project) {
                                newTask['projects'] = project[0];
                            }
                            
                        }
                    }     
                                 
                    this.listCardsInMyTasks[this.taskData.position].push(newTask['data']);
                    this.$store.dispatch("getCurrentTask", newTask['data']);
                    console.log('listTasks', this.$store.getters.listTasks);
                    // add new task in list task store
                    this.$store.getters.listTasks[newTask['data'].id] = newTask['data'];
                    if (project_id) {
                        // send request create tasks
                        const currentRoles = this.user.roles[0]['name'];
                        if (!userHelper.checkRoles(currentRoles)) {
                            var data = {
                                'leader': newTask["leaders"],
                                'tasks': newTask['data'],
                                'event': 'request create tasks'
                            }
                            this.onEventChat(data); 
                            socket.emit('join room', newTask['data'].projects.slug);
                        }  
                    }
                    this.taskData = {};
                    this.showModal = true;
                    this.allPopUp = {}
                }
            }
        },

        handleShowCard(position) {
            this.rotate[position] = true;
            this.showCards[position] = true;
        },

        // move task
        async changeTask(event, position) {
            if (typeof event.added != "undefined") {
                this.taskUpdate["task_id"] = event.added.element.id;
                this.taskUpdate["data_update"] = {
                    position: position,
                };
                let res = await axios.post(
                    "/api/my-tasks/update",
                    this.taskUpdate
                );
                if (res.status == 200) {
                    this.listCardsInMyTasks[position].filter((item) => {
                        if (item.id == event.added.element.id) {
                            item["position"] = position;
                        }
                    });
                }
                this.taskUpdate = {};
            }
        },

        // handleShowTasks
        async handleShowTasks(id) {
            const dataMyTasks = Object.values(this.listCardsInMyTasks);
            dataMyTasks.filter((item) => {
                item.filter((task) => {
                    if (task.id === id) {
                        // this.getCurrentTask(task);
                        this.$store.dispatch("getCurrentTask", task);
                    }
                });
            });
            if (this.currentTask) {
                let project_id = this.currentTask.project_id;
                if(project_id != 0){
                    var data = {
                        'project_id':project_id
                    }
                    let res = await axios.post(`/api/my-tasks/projects`, data); 
                    if (res.status == 200) {
                        this.$store.dispatch("getListUsersInProject", res.data); 
                    }
                    console.log(this.currentTask.projects.slug);
                    socket.emit('join room', this.currentTask.projects.slug);
                }
            }
            this.showModal = true;
        },

        handleHideModal() {
            this.showModal = false;
            this.allPopUp = {};
            this.$store.dispatch("getCurrentTask", false);
        },

        // hidden Popup
        hideModalPopup(data) {
            this.allPopUp[data] = false;
        },
        // show popup
        showModalPopup(data) {
            this.allPopUp = {};
            this.allPopUp[data] = true;
        },
        // show list projects
        showListProject(){
            if(this.projectSiderBar.data && this.projectSiderBar.data.length > 0){
                var myProjects = [];
                for (const key in this.projectSiderBar.data) {
                    const projects = this.projectSiderBar.data[key];
                    var data = {};
                    data['id'] = projects.id;
                    data['text'] = '['+projects.code +'] '+ projects.title 
                    myProjects.push(data);
                }
                return myProjects;
            }
        },
        showCreateTask(position){
            console.log('aaaaa')
            this.taskData['position'] = position;
            this.allPopUp = {};
            this.allPopUp['createTask'] = true;
        },
        async onChangeProject(val){
            var idProject = parseInt(val.id);
            if (idProject) {
                var data = {
                    'project_id':idProject
                }
                let res = await axios.post(`/api/my-tasks/projects`, data); 
                if(res.status == 200){
                    this.myUsers = [];
                    for (const key in res.data) {
                        const user = res.data[key];
                        var dataUser = {}
                        dataUser['id'] = user.id;
                        dataUser['text'] = userHelper.fullName(user);
                        this.myUsers.push(dataUser);
                    }
                }
            }
            
        },
        onDeadlineTask(data){
            var task_id = this.currentTask.id;
            for (const key in this.listCardsInMyTasks) {
                for (const k in this.listCardsInMyTasks[key]) {
                    if(this.listCardsInMyTasks[key][k]['id'] == task_id){
                        this.listCardsInMyTasks[key][k]['deadline'] = data;
                        break;
                    }
                }
            }
        }
    },
    async created() {
        try {
            this.infoUser();
            var results = await this.getListMyTasks();
            this.listCardsMyTasks = listCardsMyTasks;
            for (const key in listCardsMyTasks) {
                const card = listCardsMyTasks[key];
                if (typeof this.listTasksInCard[card.id] == "undefined") {
                    this.listTasksInCard[card.id] = [];
                    this.titleTask[card.id] = "";
                    this.showCards[card.id] = true;
                    this.rotate[card.id] = true;
                }
            }
            if (this.myTasks) {
                for (const key in this.myTasks) {
                    const tasks = this.myTasks[key];
                    const position = parseInt(tasks.position);
                    this.listMyTasks[tasks.id] = tasks;
                    this.listTasksInCard[position].push(tasks);
                }
                this.$store.dispatch("getListCardsInMyTasks", this.listTasksInCard); 
            }
        } catch (error) {
            console.log("error", error);
        }
    },
    mounted() {},
};
</script>
<template>
    <b-modal
        v-model="allPopUp['createTask']"
        @hide="handleHidePopupTask"
        size="lg"
        hide-footer
        hide-header
        :class="'modal-create'"
    >
        <div class="container-fluid">        
            <div class="row no-gutters">
                <div class="col-lg-12">
                    <h2 class="text-center">{{ 'Tạo task mới' }}</h2>
                    <div class="form-create-project">
                        <form @submit.prevent="createTask">
                            <b-form-group
                                :label="'Tiêu đề task'"
                                label-for="title-input"
                            >
                                <b-form-input
                                    class="form-control"
                                    :placeholder="'Vui lòng nhập tiêu đề'"
                                    id="title-project"
                                    v-model="taskData.title"
                                    required
                                >
                                </b-form-input>
                            </b-form-group>
                            <b-form-group
                                :label="'Ngày hết hạn'"
                                label-for="datetime-picker"
                            >
                                <VueDatePicker
                                    required
                                    v-model="taskData.date"
                                    auto-apply
                                    :month-change-on-scroll="false"
                                />
                            </b-form-group>
                            <b-form-group
                                :label="'Chọn dự án'"
                            >
                                <Select2 v-model="taskData.project_id" :options="showListProject()" @select="onChangeProject($event)"/>
                            </b-form-group>
                            <b-form-group
                                :label="'Chọn người quản lý'"
                                v-if="myUsers"
                            >
                                <Select2 v-model="taskData.user_collaborators" :options="myUsers"/>
                            </b-form-group>
                            <div>
                                <b-button type="submit" variant="primary"
                                    >{{ 'Tạo mới' }}</b-button
                                >
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </b-modal>
    <b-modal
        v-model="showModal"
        @hide="handleHideModal"
        size="lg"
        hide-footer
        hide-header
    >
        <!--  @hide="handleHideModal" -->
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
                            <!-- <input type="text" placeholder="Vui lòng nhập tiêu đề"> -->
                            <span>{{ currentTask.title }}</span>
                        </p>
                        <span>Trong danh sách <a href="">Task</a></span>
                    </div>
                    <div :class="['btn_close']" @click="handleHideModal">
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
                    <ChatTasks :infoAuth="user" :projectId="project_id"></ChatTasks>
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
                            <DateTasks @updateDeadline = "onDeadlineTask"></DateTasks>
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
    <div class="container-fluid wrapper-my-task tab-content" :class="{tab_list:TabBoard }">
        <div class="row">
            <div class="col-lg-12">
                <div
                    class="d-flex justify-content-between align-items-center header mb-3"
                >
                    <div
                        class="d-flex justify-content-between align-items-center"
                    >
                        <div
                            class="avatar"
                            :style="`background-image:url(${showAvatar()})`"
                        ></div>
                        <div class="ms-3">
                            <div class="d-flex align-items-center">
                                <h5 class="mb-0">My Tasks</h5>
                                <div class="dropdown cursor-pointer">
                                    <b-dropdown
                                        variant="white"
                                        right
                                        toggle-class="dropdown-item"
                                    >
                                        <template v-slot:button-content>
                                            <i
                                                class="ri-arrow-drop-down-line"
                                            ></i>
                                        </template>
                                        <b-dropdown-item>
                                            Sync to calendar…
                                        </b-dropdown-item>
                                        <b-dropdown-item>
                                            Add Tasks via Email…
                                        </b-dropdown-item>
                                        <b-dropdown-item>
                                            Export CSV
                                        </b-dropdown-item>
                                        <b-dropdown-item>
                                            Print
                                        </b-dropdown-item>
                                    </b-dropdown>

                                    <!-- <i class="ri-arrow-drop-down-line"></i> -->
                                </div>
                            </div>
                            <ul class="nav nav-pills mb-0" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation" @click="TabBoard = false">
                                    <button class="nav-link active" id="pills-list-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-list" type="button" role="tab" aria-controls="pills-list"
                                        aria-selected="true">List</button>
                                </li>
                                <li class="nav-item ms-4" role="presentation" @click="TabBoard = true;">
                                    <button class="nav-link" id="pills-board-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-list" type="button" role="tab" aria-controls="pills-board"
                                        aria-selected="false">Board</button>
                                </li>
                                <li class="nav-item ms-4" role="presentation" @click="mytask = false" >
                                    <button class="nav-link" id="pills-calendar-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-calendar" type="button" role="tab"
                                        aria-controls="pills-calendar" aria-selected="false">Calendar</button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="tab-content" id="pills-tabContent">
                    <!-- tab List -->
                    <div
                        class="tab-pane fade show active"
                        id="pills-list"
                        role="tabpanel"
                        aria-labelledby="pills-list-tab"
                    >
                        <div
                            class="d-flex align-items-center justify-content-between"
                        >
                            <div class="d-flex align-items-center">
                                <div
                                    class="btn_add_task d-flex justify-content-between align-items-center p-1 ps-1"
                                >
                                    <i class="ri-add-fill ms-1"></i>
                                    <span @click="showCreateTask(1)">Add Task</span>
                                </div>
                                <b-dropdown
                                    variant="white"
                                    right
                                    toggle-class="dropdown-item"
                                >
                                    <template v-slot:button-content>
                                        <div
                                            class="dropdown_add d-flex align-items-center p-1"
                                        >
                                            <i
                                                class="ri-arrow-drop-down-line"
                                            ></i>
                                        </div>
                                    </template>
                                    <b-dropdown-item>
                                        Add Section
                                    </b-dropdown-item>
                                    <b-dropdown-item>
                                        Add Milestone...
                                    </b-dropdown-item>
                                </b-dropdown>
                            </div>
                            <div class="d-flex align-items-center">
                                <b-dropdown
                                    variant="white"
                                    right
                                    toggle-class="dropdown-item"
                                >
                                    <template v-slot:button-content>
                                        <div
                                            class="btn_incomplete_tasks d-flex align-items-center"
                                        >
                                            <i
                                                class="ri-checkbox-circle-line me-1"
                                            ></i>
                                            <span>Incomplete tasks</span>
                                        </div>
                                    </template>
                                    <b-dropdown-item>
                                        <div class="position-relative ps-4">
                                            <i
                                                class="ri-check-line icon_check position-absolute"
                                            ></i>
                                            <span>Incomplete tasks</span>
                                        </div>
                                    </b-dropdown-item>
                                    <b-dropdown-item>
                                        <div
                                            class="d-flex align-items-center position-relative ps-4 completed_tasks"
                                        >
                                            <span> Completed tasks</span>
                                            <i
                                                class="ri-arrow-right-s-line ms-2"
                                            ></i>
                                            <div
                                                class="connect position-absolute"
                                            ></div>
                                            <div
                                                class="completed_tasks_dropdown position-absolute"
                                            >
                                                <div class="p-1">
                                                    All completed tasks
                                                </div>
                                                <div class="p-1">
                                                    Marked complete since
                                                </div>
                                                <div class="p-1">Today</div>
                                                <div class="p-1">Yesterday</div>
                                                <div class="p-1">1 week</div>
                                                <div class="p-1">2 week</div>
                                                <div class="p-1">3 week</div>
                                            </div>
                                        </div>
                                    </b-dropdown-item>
                                    <b-dropdown-item>
                                        <div class="position-relative ps-4">
                                            <span>All tasks</span>
                                        </div>
                                    </b-dropdown-item>
                                </b-dropdown>
                                <div
                                    class="btn_sort d-flex align-items-center ms-3"
                                >
                                    <b-dropdown
                                        variant="white"
                                        right
                                        toggle-class="dropdown-item"
                                        id="dropdown-divider"
                                    >
                                        <template v-slot:button-content>
                                            <div
                                                class="btn_incomplete_tasks d-flex align-items-center"
                                            >
                                                <i
                                                    class="ri-arrow-up-down-fill me-1"
                                                ></i>
                                                <span>Sort</span>
                                            </div>
                                        </template>
                                        <b-dropdown-item>
                                            <div class="position-relative ps-4">
                                                <i
                                                    class="ri-check-line icon_check position-absolute"
                                                ></i>
                                                <span>None</span>
                                            </div>
                                        </b-dropdown-item>
                                        <b-dropdown-item>
                                            <div class="position-relative ps-4">
                                                <span>Due Date</span>
                                            </div>
                                        </b-dropdown-item>

                                        <b-dropdown-item>
                                            <div class="position-relative ps-4">
                                                <span>Created by</span>
                                            </div>
                                        </b-dropdown-item>
                                        <b-dropdown-item>
                                            <div class="position-relative ps-4">
                                                <span>Created On</span>
                                            </div>
                                        </b-dropdown-item>
                                        <b-dropdown-item>
                                            <div class="position-relative ps-4">
                                                <span>Last modified on</span>
                                            </div>
                                        </b-dropdown-item>
                                        <b-dropdown-item>
                                            <div class="position-relative ps-4">
                                                <span>Likes</span>
                                            </div>
                                        </b-dropdown-item>

                                        <b-dropdown-item>
                                            <div class="position-relative ps-4">
                                                <span>Alphabetical</span>
                                            </div>
                                        </b-dropdown-item>
                                        <b-dropdown-item>
                                            <div class="position-relative ps-4">
                                                <span>Project</span>
                                            </div>
                                        </b-dropdown-item>
                                        <!-- <b-dropdown-divider></b-dropdown-divider>
                        <b-dropdown-item>
                            <div class="position-relative ps-4">
                                <span>Sort within sections</span>
                            </div> 
                        </b-dropdown-item> -->
                                    </b-dropdown>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div class="list_content">
                            <div class="list_task px-3">
                                <div class="d-flex">
                                    <div
                                        class="col-6 column d-flex align-items-center"
                                    >
                                        <p class="title ms-2 mb-0 ms-0">
                                            Tiêu đề
                                        </p>
                                    </div>
                                    <div class="col-2 column cursor-pointer">
                                        Ngày hết hạn
                                    </div>
                                    <div class="col-2 cursor-pointer column ">
                                        Dự án
                                    </div>
                                    <div class="col-2 cursor-pointer column">
                                        Người quản lý
                                    </div>
                                </div>
                            </div>

                            <div
                                class="content mt-0"
                                v-for="card in listCardsMyTasks"
                            >
                                <div class="d-flex align-items-center title">
                                    <div
                                        class="btn_arrow"
                                        :class="{ rotate: rotate[card.id] }"
                                        @click="
                                            rotate[card.id] = !rotate[card.id];
                                            showCards[card.id] =
                                                !showCards[card.id];
                                        "
                                    >
                                        <i class="ri-arrow-right-s-fill"></i>
                                    </div>
                                    <h6 class="mb-0 ms-1">
                                        {{ card.title }}
                                    </h6>
                                    <div class="btn_add ms-2">
                                        <i class="ri-add-line"></i>
                                    </div>
                                    <div class="btn_more ms-2">
                                        <i class="ri-more-fill"></i>
                                    </div>
                                </div>
                                <div
                                    class="list_task"
                                    v-if="showCards[card.id]"
                                >
                                    <draggable
                                        class="list-group"
                                        group="tasks"
                                        :list="listCardsInMyTasks[card.id]"
                                        @change="changeTask($event, card.id)"
                                    >
                                        <div
                                            class="d-flex flex_direction position-relative px-1 pb2"
                                            v-for="task in listCardsInMyTasks[
                                                card.id
                                            ]"
                                            @click="handleShowTasks(task.id)"
                                        >
                                        <div class="list_filter mt-3 d-none">
                                               <div class="filter" :style="{ backgroundColor: `red`}"></div>
                                               <div class="filter" :style="{ backgroundColor: `red`}"></div>
                                               <div class="filter" :style="{ backgroundColor: `red`}"></div>
                                               <div class="filter" :style="{ backgroundColor: `red`}"></div>
                                               <div class="filter" :style="{ backgroundColor: `red`}"></div>
                                         </div>
                                         <b-progress class="d-none">
                                          </b-progress>
                                            <div
                                                class="col-6 column d-flex align-items-center flex_direction"
                                            >
                                            
                                              <div class="mb-3 id_task mt-1 ">
                                                  <a href="">#2</a>
                                                  <div class="float-end ml-2">
                                                      <div>
                                                         Mar 15, 2023
                                                      </div>
                                                   </div>
                                              </div>
                                              <div class="d-flex name_task align-items-center">
                                                <i
                                                    class="ri-checkbox-circle-line cursor-pointer display_none"
                                                ></i>
                                                <p class="ms-2 title mb-0">
                                                    {{ task.title }}
                                                </p>
                                              </div>
                                               
                                                <div
                                                    class="d-flex align-items-center comment"
                                                    v-if="
                                                        typeof task.messengers != 'undefined' && task.messengers.length > 0
                                                    "
                                                >
                                                    <span>{{
                                                        task.messengers.length
                                                    }}</span>
                                                    <i
                                                        class="ri-chat-3-line ms-1 cursor-pointer display_none"
                                                    ></i>
                                                </div>
                                                <div class="btn_move_task display_none">
                                                    <i
                                                        class="ri-arrow-up-down-line cursor-pointer ms-2"
                                                    ></i>
                                                </div>
                                                <div
                                                    class="ms-4 cursor-pointer btn_detail display_none"
                                                >
                                                    <i
                                                        class="ri-arrow-drop-right-line"
                                                    ></i>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center p-7 col-2 w100 column">
                                             <div class="d-flex align-items-center">
                                                    <i class="ri-time-line me-1 none_tab1"></i> 
                                                <div
                                                class="cursor-pointer text-pink"
                                                 >
                                                  {{
                                                    `${
                                                        task.deadline
                                                            ? showDate(
                                                                  task.deadline
                                                              )
                                                            : ""
                                                       }`
                                                   }}
                                                   </div>
                                              </div>

                                              <div
                                                :class="[
                                                    'd-flex align-items-center file flex d-none',
                                                ]"
                                            
                                            >
                                                <i class="ri-attachment-2"></i>2
                                
                                            </div>
                                            <div :class="['d-flex align-items-center number_task flex d-none',]">
                                                <i class="ri-checkbox-line"></i>5/8
                                            </div>
                                            <div class="align-self-center d-flex">
                                                <div
                                                    class="list-member flex d-none">
                                                    <div class="team-member">
                                                        <a class="team-member d-inline-block" >
                                                            <img
                                                                src="http://localhost/quanlycongviec/public/images/avatar.png"   
                                                                class="rounded-circle avatar-xs"
                                                            />
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                               
                                            </div>
                                           
                                            <div
                                                class="col-2 cursor-pointer column display_none"
                                            >
                                                <div
                                                    class="project d-flex align-items-center"
                                                    v-if="task.projects"
                                                >
                                                    <div class="label">
                                                        <code>{{
                                                            "[" +
                                                            task.projects
                                                                .code +
                                                            "]"
                                                        }}</code>
                                                    </div>
                                                    <span>{{
                                                        task.projects.title
                                                    }}</span>
                                                </div>
                                            </div>
                                            <div
                                                class="col-2 cursor-pointer column display_none"
                                            ></div>
                                        </div>
                                    </draggable>
                                    <div class="add_task mt-2">
                                        <div
                                            class="d-flex"
                                            v-if="showAddtask[card.id]"
                                        >
                                            <div
                                                class="col-7 column d-flex align-items-center"
                                            >
                                                <i
                                                    class="ri-checkbox-circle-line cursor-pointer"
                                                ></i>
                                                <textarea
                                                    v-model="titleTask[card.id]"
                                                    class="ms-2 title mb-0"
                                                ></textarea>
                                                <div class="btn_move_task">
                                                    <i
                                                        class="ri-arrow-up-down-line cursor-pointer ms-2"
                                                    ></i>
                                                </div>
                                                <div
                                                    class="ms-4 cursor-pointer btn_detail"
                                                >
                                                    <i
                                                        class="ri-arrow-drop-right-line"
                                                    ></i>
                                                </div>
                                            </div>
                                            <div
                                                class="col-1 column cursor-pointer text-pink"
                                            ></div>
                                            <div
                                                class="col-2 cursor-pointer column"
                                            ></div>
                                            <div
                                                class="col-2 cursor-pointer column"
                                            ></div>
                                        </div>
                                        <span
                                            class="ps-3"
                                            role="button"
                                            @click="showCreateTask(card.id)"                                            
                                            v-if="!showAddtask[card.id]"
                                            >Add task...</span
                                        >
                                        <!-- @click="onShowAddTasks(card.id)" -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- tab Board -->
                    <!-- <div
                        class="tab-pane fade"
                        id="pills-board"
                        role="tabpanel"
                        aria-labelledby="pills-board-tab"
                    >
                        <Board class="mt-4"></Board>
                    </div> -->

                    <!-- tab calendar -->
                    <div
                        class="tab-pane fade"
                        id="pills-calendar"
                        role="tabpanel"
                        aria-labelledby="pills-calendar-tab"
                    >
                        tab3
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
.tab_list{
    .pb2{
        padding-bottom: 10px;
        border: 1px solid #e4e5e5;
    border-radius: 5px;
    background: #fff;
    min-height: 60px;
    margin-bottom: 10px;
    }
     .btn_add_card{
        padding: 0;
       display: flex !important;
       align-items: center;
     }
    .progress{
        display: block !important;
        height: 5px;
        position: absolute;
        width: 100%;
        right: 0;
        .progress-bar{
            height: 5px;
        }
    }
    .list_filter{
        column-gap: 3px;
        row-gap: 3px;
        width: 93%;
       margin-inline: auto;
       display: flex !important;
      flex-wrap: wrap;
      .filter{
        width: 40px;
       height: 8px;
       border-radius: 10px;
      }
    }
    .w100{
        width: 100% !important;
        flex-wrap: wrap;
        column-gap: 7px;
        i{
            display: block !important;
        }
    }
    .text-pink{
        color: #000;
        padding: 0 !important;
       
       
    }
    .p-7{
        padding: 0 7px;
    }
    .flex_direction{
        flex-direction: column;
    }
    .flex{
        display: flex !important;
    }
    .block{
        display: block !important;;
    }
    .list_content{
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        .list_task{
            display: none;
        }
    }
    .content{
        border: 1px solid #e4e5e5;
        border-radius:10px ;
        background: rgb(247 248 249);
        padding: 9px 20px;
        width: 23% !important;
        .title{
            padding: 10px 0;
            .btn_add,.btn_more,.btn_arrow{
                 display: none !important;
            }
            
        }
         .display_none{
            display: none !important;
         }
         .list_task{
            // border: 1px solid #e4e5e5;
            // border-radius: 5px;
            display: block;
            background: #f7f8f9;
            min-height: 60px;
            margin-bottom: 15px;
            .id_task{
                    display: flex !important;
                    justify-content: space-between;
                    width: 100%;
                }
            .column {
                width: 100% !important;
                border: none !important;
            }
            .name_task{
                justify-content: space-between;
                align-items: center;
                width: 100%;
                .title{
                    font-weight: 500;
                }
                p{
                    margin:0 !important;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                }
               
            }
         }
    }
}
.wrapper-my-task {
    .w100{
        i{
            display: none;
        }
    }
    .none_tab1{
        display: block;
    }
    .list-member,.number_task,.file{
        display: none;
    }
    .name_task{
                width: 80%;
                p{
                    text-overflow: ellipsis;
                    white-space: nowrap;
                }
               
            }
    .id_task{
        display: none;
    }
    .completed_tasks_dropdown {
        left: -208px;
        top: 10px;
        border: rgb(0 0 0 / 18%) 1px solid;
        padding: 8px;
        border-radius: 8px;
        display: none;
        background: #fff;
    }
    .completed_tasks:hover {
        .completed_tasks_dropdown {
            display: block;
        }
    }
    .main-content {
        .list_content {
            .content {
                padding: 0;
                > .title {
                    margin: 10px 0;
                }
            }
        }
    }
    .connect {
        width: 54px;
        height: 32px;
        left: -39px;
        top: 16px;
    }
    .icon_check {
        top: 50%;
        transform: translateY(-50%);
        left: -10px;
    }
    .dropdown-menu {
        padding: 8px !important;
    }
    .dropdown-item {
        padding: 0 !important;
    }
    .rotate {
        transform: rotate(90deg);
    }
    .header {
        border-bottom: 1px solid #e7e5e5;
        padding-bottom: 14px;
    }
    .avatar {
        width: 48px;
        height: 48px;
        border-radius: 100%;
        cursor: pointer;
        background-size: 100%;
        background-position: center;
        background-repeat: no-repeat;
    }
    i {
        font-size: 24px;
    }
    ul {
        margin-bottom: 0 !important;
        list-style: none !important;
        padding: 0 !important;
        font-size: 14px;
        li {
            cursor: pointer;
        }
        .nav-link {
            padding: 0;
            background: none;
            color: #6d6e6f;
            padding-bottom: 2px;
            border-radius: inherit !important;
        }

        .active {
            color: #000 !important;
            background: none !important;
            border-bottom: 2px solid #929293 !important;
        }
    }
    .btn_incomplete_tasks,
    .btn_sort {
        font-size: 12px;
        cursor: pointer;
        padding: 5px;
        border-radius: 5px;
        i {
            font-size: 12px;
        }
    }
    .btn_incomplete_tasks:hover,
    .btn_sort:hover {
        background: #e7e5e5;
    }
    .dropdown_add,
    .btn_add_task {
        background: #4573d2;
        color: #fff;
        cursor: pointer;
        font-size: 12px;
        i {
            font-size: 12px;
        }
    }
    .btn_add_task {
        border-radius: 6px 0px 0px 6px;
    }
    .dropdown_add {
        background: #4573d2;
        color: #fff;
        margin-left: 1px;
        border-radius: 0px 6px 6px 0px;
    }
    .title {
        h6 {
            font-size: 16px;
            font-weight: 500;
        }
        .btn_arrow {
            padding: 0 6px;
            cursor: pointer;
            color: #929293;
        }
    }
    .btn_more,
    .btn_add {
        padding: 0 6px;
        border-radius: 8px;
        cursor: pointer;
        display: none;
        i {
            color: #929293;
        }
    }
    .title:hover {
        cursor: pointer;
        .btn_more,
        .btn_add {
            display: block;
        }
    }
    .btn_arrow:hover,
    .btn_more:hover,
    .btn_add:hover {
        padding: 0 6px;
        border-radius: 8px;
        background: #f6f6f6;
        i {
            color: #000;
        }
    }
    .list_task {
        i {
            color: #9c9da2;
            font-size: 20px;
        }
        .column:hover {
            border: 1px solid #000;
        }
        .column:active {
            border: 1px solid #1512c7;
        }
        .column {
            border: 1px solid #edeae9;
            padding: 0 7px;
            align-items: center;
            display: flex;
            overflow: hidden;
            .title {
                border: 0;
                background: transparent;
                border-radius: 0;
                display: block;
                outline: 0;
                overflow: hidden;
                width: 100%;
                padding: 0;
                resize: none;
            }
            .comment {
                color: #9c9da2;
            }
            textarea {
                height: 50%;
            }
        }
    }

    .list-group .project {
        margin-left: 8px;
        background-color: #e8e5e4;
        border-radius: 10px;
        padding: 0px 10px;
        .label {
            border-radius: 3px;
            margin-right: 4px;
        }
    }
    .list-group .project:first-child {
        margin-left: 0px;
    }
    .text-pink {
        color: #c92f54;
    }
    .add_task {
        color: #9c9da2;
        cursor: pointer;
    }
    .add_section {
        color: #9c9da2;
        cursor: pointer;
        padding: 0 3px;
        width: 126px;
        border-radius: 6px;
        i {
            color: #7a7b7e;
        }
    }

    .add_section:hover {
        background: #e7e5e5;
        color: #000;
        i {
            color: #000;
        }
    }
}
.modal-create.modal{
    z-index: 1050;
}
</style>
