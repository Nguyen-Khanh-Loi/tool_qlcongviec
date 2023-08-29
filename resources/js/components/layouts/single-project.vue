<script>
import { VueDraggableNext } from "vue-draggable-next";
import { mapGetters, mapActions, mutations } from "vuex";
import PageHeader from "../layouts/page-header.vue";
import moment from "moment";
import { VueEditor } from "vue3-editor";
import { taskHelper } from "../../helpers/helptask";
import { taskMethods, authMethods } from "../../store/helpers";
export default {
    page: {
        title: "Gosu Board",
        meta: [{ name: "description" }],
    },
    components: {
        draggable: VueDraggableNext,
        PageHeader,
        VueEditor,
    },
    data() {
        return {
            buttonAdd: {},
            newTasks: {},
            showModal: false,
            showActive: false,
            showModalMember: false,
            showModalFilter: false,
            showModalWorkToDo: false,
            showModalFile: false,
            showModalMove: false,
            showEditor: false,
            project_id: parseInt(this.$route.params.id),
            placeholder: "Nhập tiêu đề cho thẻ này...",
            taskUpdate: {},
            title: "Gosu Board",
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
            listMemberActive: {},
        };
    },
    computed: {
        ...mapGetters([
            "listTaskDraggable",
            "listCard",
            "listTasks",
            "currentTask",
            "listUsers",
            "authUserData",
        ]),
    },
    methods: {
        ...taskMethods,
        ...authMethods,

        handlerClick($id) {
            for (const key in this.listCard) {
                const cardProject = this.listCard[key];
                this.buttonAdd[cardProject.id] = false;
            }
            this.buttonAdd[$id] = true;
        },

        async createTask($id) {
            this.newTasks["card_id"] = $id;
            this.newTasks["project_id"] = this.project_id;
            if (this.newTasks && this.newTasks["title_" + $id]) {
                var newTask = await this.createNewTask(this.newTasks);
                if (typeof newTask != "undefined") {
                    this.listTaskDraggable[newTask.card_id].push(newTask.id);
                    this.listTasks[newTask.id] = newTask;
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
            this.getCurrentTask(data);
            this.showModal = true;
        },

        show_ModalMember(array) {
            this.showModalMember = !this.showModalMember;
            if (this.showModalMember) {
                this.listMemberActive = taskHelper.convertToObject(array);
            } else {
                this.listMemberActive = {};
            }
        },

        show_Filter() {
            this.showModalFilter = !this.showModalFilter;
        },

        /**
         *
         * @param {*} value returm date
         */
        dateTime(value) {
            return moment(value).format("ll");
        },

        // click show editor description
        handlerShowEditor() {
            this.showEditor = !this.showEditor;
        },

        // updated data current task
        async updateDataTask() {
            this.taskUpdate["task_id"] = this.currentTask.id;
            var description = {
                description: this.currentTask.description,
            };
            this.taskUpdate["info_task"] = description;
            await this.updateTask(this.taskUpdate);
            this.showEditor = false;
        },

        // check hiden modal
        onHideModal(evt) {
            if (evt.trigger === "backdrop") {
                if (this.showEditor == true || this.showModalMember == true) {
                    evt.preventDefault();
                    this.showEditor = false;
                    this.showModalMember = false;
                } else {
                    this.showModal = false;
                }
            }
        },

        /**
         * add and remove use in current task
         * @param {*} action
         * @param {*} user_id
         */
        async updatedUserTask(action, user_id) {
            if (action == "deactive") {
                delete this.listMemberActive[user_id];
            } else {
                this.listMemberActive[user_id] = parseInt(user_id);
            }
            this.taskUpdate["task_id"] = this.currentTask.id;
            var list_user_task = Object.keys(this.listMemberActive);
            var list_user_ids = {
                list_user_ids: list_user_task ? list_user_task.join(", ") : "",
            };
            this.taskUpdate["info_task"] = list_user_ids;
            var currentTaskCardId = this.listTasks[this.currentTask.id];
            await this.updateTask(this.taskUpdate);

            if (this.currentTask.list_user_ids) {
                currentTaskCardId["members"] = this.currentTask.members;
            } else {
                currentTaskCardId["members"] = false;
            }
        },
    },
    created() {
        this.auth();
        this.getListCards();
        this.getListTasks(1);
    },

    mounted() {
        document.body.classList.remove("auth-body-bg");
        document.body.classList.add("page-task");
    },
};
</script>
<template>
    <!-- <pre>{{ JSON.stringify(authUserData, undefined, 4) }}</pre> -->
    <b-modal
        v-model="showModal"
        @hide="onHideModal"
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
                    <div :class="['btn_close']" @click="showModal = !showModal">
                        <i class="ri-close-line"></i>
                    </div>
                </div>
                <div :class="['col-9']">
                    <div :class="['content-main-info']">
                        <div class="member" v-if="currentTask.members">
                            <p>Thành viên</p>
                            <div class="list_user">
                                <div
                                    class="user"
                                    v-for="(
                                        userTask, index
                                    ) in currentTask.members"
                                    :key="index"
                                >
                                    <img
                                        src="/images/avatar-2.jpg?feb0f89de58f0ef9b424b1beec766bd2"
                                        :title="userTask.name"
                                    />
                                </div>
                            </div>
                            <div class="btn_add_user">
                                <i class="ri-add-line"></i>
                            </div>
                        </div>
                        <div class="label">
                            <p>Nhãn</p>
                            <div class="list_label">
                                <div class="name_label">
                                    <div class="label_active"></div>
                                    <p>SDK</p>
                                </div>
                                <div class="btn_add_label">
                                    <i class="ri-add-line"></i>
                                </div>
                            </div>
                        </div>
                        <div class="notification">
                            <p>Thông báo</p>
                            <div class="btn_follow">
                                <i class="ri-eye-line"></i>
                                <p>Theo dõi</p>
                            </div>
                        </div>
                    </div>
                    <div :class="['content-main-detail']">
                        <h6 d-flex flex-row align-items-center>
                            <i class="ri-menu-2-line"></i>
                            <span>Mô tả</span>
                        </h6>
                        <div :class="['description']">
                            <div
                                v-if="!showEditor"
                                v-bind:innerHTML="`${
                                    currentTask.description
                                        ? currentTask.description
                                        : 'Thêm mô tả chi tiết hơn...'
                                }`"
                                :class="['content-desc']"
                                @click="handlerShowEditor()"
                            ></div>
                            <div
                                v-else="showEditor"
                                :class="['content-editor']"
                            >
                                <vue-editor
                                    id="edit-current-task"
                                    v-model="currentTask.description"
                                ></vue-editor>
                                <div class="mt-3 mb-3">
                                    <b-button
                                        variant="btn_save primary me-2"
                                        @click="updateDataTask()"
                                        >Lưu</b-button
                                    >
                                    <b-button
                                        :class="['btn_cancel']"
                                        variant="light btn_cancel"
                                        @click="handlerShowEditor()"
                                        >Hủy</b-button
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="list_work_todo">
                            <div class="work-todo">
                                <div
                                    class="work-todo-header d-flex flex-row align-items-center"
                                >
                                    <p
                                        class="d-flex flex-row align-items-center name"
                                    >
                                        <i class="ri-checkbox-line"></i>
                                        <span>Việc abc</span>
                                    </p>
                                    <div class="btn_delete">Xóa</div>
                                </div>
                                <div
                                    class="work-todo-content d-flex flex-row align-items-center"
                                >
                                    <p class="percent">0%</p>
                                    <div class="progress">
                                        <div class="progress-line"></div>
                                    </div>
                                </div>
                                <div class="btn_add">Thêm một mục</div>
                            </div>
                        </div>
                    </div>
                    <div :class="['content-main-detail']">
                        <h6 d-flex flex-row align-items-center>
                            <i class="ri-list-check"></i><span>Hoạt động</span>
                        </h6>
                        <textarea
                            class="textarea_active"
                            placeholder="Viết bình luận..."
                            v-if="!showActive"
                            @click-outside="showActive = !showActive"
                        ></textarea>
                        <div :class="['description']" v-if="showActive">
                            <div :class="['content-desc']"></div>
                            <div :class="['content-editor']">
                                <vue-editor
                                    id="edit-current-task"
                                    v-model="currentTask.description"
                                ></vue-editor>
                            </div>
                            <div class="list_button">
                                <div class="btn_save">Lưu</div>
                                <div
                                    class="btn_cancel"
                                    @click="showActive = !showActive"
                                >
                                    Hủy
                                </div>
                            </div>
                        </div>
                        <div :class="['list-work']">
                            <div class="history_active">
                                <div class="avatar">
                                    <div class="image">
                                        <img
                                            src="/images/avatar-2.jpg?feb0f89de58f0ef9b424b1beec766bd2"
                                            alt=""
                                        />
                                    </div>
                                </div>
                                <div class="history_active-content">
                                    <p>
                                        <b class="name">Nguyễn Khánh Lợi</b> đã
                                        tham gia thẻ này
                                    </p>
                                    <p class="time">2 Th12 2022 lúc 09:11</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div :class="['col-3']">
                    <div :class="['list-item']">
                        <h6>Thêm vào thẻ</h6>
                        <b-list-group>
                            <b-list-group-item>
                                <div
                                    class="item"
                                    @click="
                                        show_ModalMember(currentTask.members)
                                    "
                                >
                                    <i class="ri-user-fill"></i> Thêm thành viên
                                </div>
                                <div class="modalMember" v-if="showModalMember">
                                    <div :class="['modalMember-header']">
                                        <span>Thành viên</span>
                                        <a
                                            @click="
                                                show_ModalMember(
                                                    currentTask.members
                                                )
                                            "
                                            ><i class="ri-close-line"></i
                                        ></a>
                                    </div>
                                    <input
                                        type="text"
                                        placeholder="Tìm kiếm các thành viên"
                                    />
                                    <p>Thành viên của task</p>
                                    <div class="member_of_table">
                                        <div
                                            v-for="(user, index) in listUsers"
                                            :key="user.id"
                                            :class="[
                                                'list_member d-flex flex-row align-items-center',
                                            ]"
                                            @click="
                                                updatedUserTask(
                                                    !listMemberActive[
                                                        user.id
                                                    ] && listMemberActive
                                                        ? 'active'
                                                        : 'deactive',
                                                    user.id
                                                )
                                            "
                                            :data-check="`${
                                                !listMemberActive[user.id] &&
                                                listMemberActive
                                                    ? 'active'
                                                    : 'deactive'
                                            }`"
                                        >
                                            <div
                                                :class="[
                                                    'list_member d-flex flex-row align-items-center',
                                                ]"
                                            >
                                                <div class="avatar">
                                                    <div class="image">
                                                        <img
                                                            src="/images/avatar-2.jpg?feb0f89de58f0ef9b424b1beec766bd2"
                                                            alt=""
                                                        />
                                                    </div>
                                                </div>
                                                <div class="name">
                                                    <p>{{ user.name }}</p>
                                                </div>
                                                <span
                                                    v-if="
                                                        listMemberActive[
                                                            user.id
                                                        ]
                                                    "
                                                >
                                                    <i
                                                        class="ri-check-line"
                                                    ></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </b-list-group-item>
                            <b-list-group-item @click="showModalFilter = true">
                                <div class="item">
                                    <i class="ri-price-tag-3-line"></i> Nhãn
                                </div>
                                <div class="modalFilter" v-if="showModalFilter">
                                    <div
                                        :class="[
                                            'modalFilter-header d-flex flex-row align-items-center justify-content-between',
                                        ]"
                                    >
                                        <span>Nhãn</span>
                                        <a
                                            @click.stop="
                                                showModalFilter =
                                                    !showModalFilter
                                            "
                                            ><i class="ri-close-line"></i
                                        ></a>
                                    </div>
                                    <input
                                        class="search"
                                        type="text"
                                        placeholder="Tìm nhãn"
                                    />
                                    <p>Nhãn</p>
                                    <div class="filter_of_table">
                                        <div
                                            class="list_color d-flex flex-row align-items-center"
                                        >
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                value=""
                                                id="flexCheckDefault"
                                            />
                                            <div class="color color1">
                                                <div class="color_child"></div>
                                            </div>
                                            <div class="btn_edit">
                                                <i class="ri-pencil-line"></i>
                                            </div>
                                        </div>
                                        <div
                                            class="list_color d-flex flex-row align-items-center"
                                        >
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                value=""
                                                id="flexCheckDefault"
                                            />
                                            <div class="color color2">
                                                <div class="color_child"></div>
                                            </div>
                                            <div class="btn_edit">
                                                <i class="ri-pencil-line"></i>
                                            </div>
                                        </div>
                                        <div
                                            class="list_color d-flex flex-row align-items-center"
                                        >
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                value=""
                                                id="flexCheckDefault"
                                            />
                                            <div class="color color3">
                                                <div class="color_child"></div>
                                            </div>
                                            <div class="btn_edit">
                                                <i class="ri-pencil-line"></i>
                                            </div>
                                        </div>
                                        <div
                                            class="list_color d-flex flex-row align-items-center"
                                        >
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                value=""
                                                id="flexCheckDefault"
                                            />
                                            <div class="color color4">
                                                <div class="color_child"></div>
                                            </div>
                                            <div class="btn_edit">
                                                <i class="ri-pencil-line"></i>
                                            </div>
                                        </div>
                                        <div
                                            class="list_color d-flex flex-row align-items-center"
                                        >
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                value=""
                                                id="flexCheckDefault"
                                            />
                                            <div class="color color5">
                                                <div class="color_child"></div>
                                            </div>
                                            <div class="btn_edit">
                                                <i class="ri-pencil-line"></i>
                                            </div>
                                        </div>
                                        <div
                                            class="list_color d-flex flex-row align-items-center"
                                        >
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                value=""
                                                id="flexCheckDefault"
                                            />
                                            <div class="color color6">
                                                <div class="color_child"></div>
                                            </div>
                                            <div class="btn_edit">
                                                <i class="ri-pencil-line"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="btn btn_display_more">
                                        Tạo nhãn mới
                                    </div>
                                    <div class="btn_display_more">
                                        Hiển thị các thành viên khác trong không
                                        gian làm việc
                                    </div>
                                </div>
                            </b-list-group-item>
                            <b-list-group-item
                                @click="showModalWorkToDo = true"
                            >
                                <div class="item">
                                    <i class="ri-checkbox-line"></i> Việc cần
                                    làm
                                </div>
                                <div
                                    class="modal_work_todo"
                                    v-if="showModalWorkToDo"
                                >
                                    <div
                                        :class="[
                                            ' modal_work_todo-header d-flex flex-row align-items-center justify-content-center',
                                        ]"
                                    >
                                        <span>Thêm danh sách công việc</span>
                                        <a
                                            @click.stop="
                                                showModalWorkToDo = false
                                            "
                                            ><i class="ri-close-line"></i
                                        ></a>
                                    </div>
                                    <p class="title">Tiêu đề</p>
                                    <input
                                        type="text"
                                        placeholder="Việc cần làm"
                                    />
                                    <div class="btn_add">Thêm</div>
                                </div>
                            </b-list-group-item>
                            <b-list-group-item
                                ><i class="ri-time-line"></i> Ngày hết
                                hạn</b-list-group-item
                            >
                            <b-list-group-item @click="showModalFile = true">
                                <div class="item">
                                    <i class="ri-attachment-2"></i> File đính
                                    kèm
                                </div>
                                <div class="modalFile" v-if="showModalFile">
                                    <div
                                        :class="[
                                            'modalFile-header d-flex flex-row align-items-center justify-content-center',
                                        ]"
                                    >
                                        <span>Đính kèm từ</span>
                                        <a
                                            @click.stop="
                                                showModalFile = !showModalFile
                                            "
                                            ><i class="ri-close-line"></i
                                        ></a>
                                    </div>
                                    <div class="list_upload">
                                        <div class="upload">Máy tính</div>
                                        <div class="upload">Trello</div>
                                        <div class="upload">Google Driver</div>
                                        <div class="upload">Dropbox</div>
                                        <div class="upload">Box</div>
                                        <div class="upload">OneDriver</div>
                                    </div>
                                    <hr />
                                    <div class="attach_link">
                                        <b>Đính kèm liên kết</b>
                                        <input
                                            type="text"
                                            placeholder="Dán liên kết vào đây"
                                        />
                                        <div class="btn">Đính kèm</div>
                                    </div>
                                    <hr />
                                    <p class="note">
                                        Mẹo: Bạn có thể kéo thả các tập tin và
                                        liên kết vào thẻ để tải chúng lên.
                                    </p>
                                </div>
                            </b-list-group-item>
                            <!-- <b-list-group-item
                                ><i class="ri-image-line"></i> Ảnh
                                bìa</b-list-group-item
                            > -->
                        </b-list-group>
                    </div>
                    <div :class="['list-item']">
                        <h6>Thao tác</h6>
                        <b-list-group>
                            <b-list-group-item @click="showModalMove = true">
                                <div class="item">
                                    <i class="ri-arrow-right-line"></i>
                                    Di chuyển
                                </div>
                                <div class="modal_move" v-if="showModalMove">
                                    <div
                                        :class="[
                                            ' modal_move-header d-flex flex-row align-items-center justify-content-center',
                                        ]"
                                    >
                                        <span>Di chuyển thẻ</span>
                                        <a @click.stop="showModalMove = false"
                                            ><i class="ri-close-line"></i
                                        ></a>
                                    </div>
                                    <p class="title">Chọn đích đến</p>
                                    <div class="modal_move-content">
                                        <div class="btn select_table">
                                            <p>Bảng</p>
                                            <div class="name_table">Demo</div>
                                        </div>
                                        <div class="btn select_list">
                                            <p>Danh sách</p>
                                            <div class="name_list">Cần làm</div>
                                        </div>
                                        <div class="btn select_location">
                                            <p>Vị trí</p>
                                            <div class="number">1</div>
                                        </div>
                                    </div>
                                    <div class="btn_move">Di chuyển</div>
                                </div>
                            </b-list-group-item>
                            <!-- <b-list-group-item
                                ><i class="ri-price-tag-3-line"></i> Sao
                                chép</b-list-group-item
                            >
                            <b-list-group-item
                                ><i class="ri-checkbox-line"></i>
                                share</b-list-group-item
                            > -->
                        </b-list-group>
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
                <a class="btn_add-ons">
                    <div class="icon">
                        <i class="ri-rocket-2-line"></i>
                    </div>
                    <p>Tiện ích bổ sung</p>
                </a>
                <a class="btn_automation">
                    <i class="ri-flashlight-line"></i>
                    <p>Tự động hóa</p>
                </a>
                <a class="btn_filter">
                    <div class="icon"><i class="ri-filter-3-line"></i></div>
                    <p>Lọc</p>
                </a>
                <div class="list_user">
                    <div class="avatar">
                        <img
                            src="/images/avatar-1.jpg?feb0f89de58f0ef9b424b1beec766bd2"
                            alt=""
                        />
                    </div>
                    <div class="avatar">
                        <img
                            src="/images/avatar-2.jpg?feb0f89de58f0ef9b424b1beec766bd2"
                            alt=""
                        />
                    </div>
                    <div class="avatar">
                        <img
                            src="/images/avatar-3.jpg?feb0f89de58f0ef9b424b1beec766bd2"
                            alt=""
                        />
                    </div>
                    <div class="avatar">
                        <img
                            src="/images/avatar-4.jpg?feb0f89de58f0ef9b424b1beec766bd2"
                            alt=""
                        />
                    </div>
                </div>
                <a class="btn_share">
                    <div class="icon"><i class="ri-user-add-line"></i></div>
                    <p>Chia sẻ</p>
                </a>
                <a class="btn_menu">
                    <i class="ri-more-line"></i>
                </a>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-6">
                <div class="media">
                    <div class="me-3">
                        <img
                            src="@/assets/images/logo-sm-light.png"
                            alt
                            class="avatar-xs"
                        />
                    </div>
                    <div class="media-body">
                        <h5>Gosu Dashboard</h5>
                        <span class="badge badge-soft-success">Open</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="text-end">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item">
                            <a
                                href="javascript: void(0);"
                                class="d-inline-block"
                                v-b-tooltip.hover
                                title="Aaron Williams"
                            >
                                <img
                                    src="@/assets/images/users/avatar-2.jpg"
                                    class="rounded-circle avatar-xs"
                                    alt
                                />
                            </a>
                        </li>
                        <li class="list-inline-item">
                            <a
                                href="javascript: void(0);"
                                class="d-inline-block"
                                v-b-tooltip.hover
                                title="Jonathan McKinney"
                            >
                                <div class="avatar-xs">
                                    <span
                                        class="avatar-title rounded-circle bg-soft-primary text-primary"
                                        >J</span
                                    >
                                </div>
                            </a>
                        </li>
                        <li class="list-inline-item">
                            <a
                                href="javascript: void(0);"
                                class="d-inline-block"
                                v-b-tooltip.hover
                                title="Carole Connolly"
                            >
                                <img
                                    src="@/assets/images/users/avatar-4.jpg"
                                    class="rounded-circle avatar-xs"
                                    alt
                                />
                            </a>
                        </li>

                        <li class="list-inline-item">
                            <a href="javascript: void(0);" class="py-1">
                                <i class="mdi mdi-plus me-1"></i> New member
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div
                v-for="(card, index) in listCard"
                :key="index++"
                :class="['col-3']"
            >
                <div class="card-body">
                    <b-dropdown right class="float-end" variant="white">
                        <template v-slot:button-content>
                            <i
                                class="mdi mdi-dots-vertical m-0 text-muted font-size-20"
                            ></i>
                        </template>
                        <b-dropdown-item>Edit</b-dropdown-item>
                        <b-dropdown-item>Delete</b-dropdown-item>
                    </b-dropdown>

                    <!-- end dropdown -->
                    <h4 class="card-title">{{ card.title }}</h4>
                    <p class="mb-0">3 Tasks</p>
                </div>
                <div class="card">
                    <div class="card-body border-bottom">
                        <div id="todo-task" class="task-list">
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
                                    :data-cardid="card.id"
                                    @click="showTask(listTasks[task])"
                                >
                                    <!-- <pre>{{ JSON.stringify(task, undefined, 4) }}</pre> -->
                                    <div
                                        class="progress progress-sm animated-progess"
                                        style="height: 3px"
                                    >
                                        <div
                                            class="progress-bar"
                                            role="progressbar"
                                            style="width: 72%"
                                            aria-valuenow="72"
                                            aria-valuemin="0"
                                            aria-valuemax="100"
                                        ></div>
                                    </div>
                                    <div class="card-body">
                                        <div class="list_filter">
                                           <div class="filter"></div>
                                           <div class="filter"></div>
                                           <div class="filter"></div>
                                           <div class="filter"></div>
                                           <div class="filter"></div>
                                           <div class="filter"></div>
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
                                        <div class="d-inline-flex team mb-0">
                                            <div
                                                class="me-3 align-self-center"
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
                                                            <img
                                                                src="@/assets/images/users/avatar-2.jpg"
                                                                class="rounded-circle avatar-xs"
                                                                alt
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
</template>