<script>
// import { taskMethods, taskGetters} from "../../store/helpers";
import { taskHelper } from "@/js/helpers/helptask";
import { taskMethods, taskGetters } from "@/js/store/helpers";
import moment from "moment";
export default {   
    data() {
        return {
            showActiveChecklist: false,
            activeEditChecklist: {},
            calculateChecklist: {},
            nameChecklist: "",
            checked: true,
        };
    },
    computed: {
        ...taskGetters,
    },
    methods: {
        ...taskMethods,
        // add new  work todo
        async deleteWordToto(id) {
            var status = await this.removeWorkTodo(id);
            if (status == 200) {
                if (this.currentTask.works[id]) {
                    delete this.currentTask.works[id];
                    delete this.listTasks[this.currentTask.id].works[id];
                }
            }
        },

        // add new list check in work todo
        showAddChecklist(id) {
            this.showActiveChecklist = {};
            this.showActiveChecklist[id] = !this.showActiveChecklist[id];
        },

        // add new list check in work todo
        showEditChecklist(id) {
            this.activeEditChecklist = {};
            this.activeEditChecklist[id] = !this.activeEditChecklist[id];
        },

        // create check list
        async newCheckList(id) {
            if (this.nameChecklist) {
                var data = {
                    id: id,
                    title: this.nameChecklist,
                };
                await taskHelper.addcheckLists(data, this.$store);
                this.showAddChecklist(id);
                this.nameChecklist = "";
            }
        },
        // remove check list
        async deleteCheckList(data) {
            await taskHelper.removeCheckListTask( data, this.$store );
        },

        // update check list
        async updateData(data) {
            if (this.currentTask.works[data["work_id"]].check_list[data["id"]].title) {
                var _data = {
                    title: this.currentTask.works[data["work_id"]].check_list[data["id"]]
                        .title,
                };
                if (typeof data["data"] != "undefined") {
                    _data["deadline"] = moment(data["data"]).format(
                        "YYYY-MM-DD HH:mm:ss"
                    );
                }
                if (typeof data["status"] != "undefined") {
                    _data["status"] = !this.currentTask.works[data["work_id"]].check_list[
                        data["id"]
                    ].status
                        ? 1
                        : 0;
                }

                data["data"] = _data;
                var result = await taskHelper.updatedDataChecklist(data, this.$store);
                if (result) {
                    this.activeEditChecklist = {};
                    if (typeof _data["deadline"] != "undefined") {
                        this.currentTask.works[data["work_id"]].check_list[
                            data["id"]
                        ]["deadline"] = _data["deadline"];
                    }
                    if (typeof data["status"] != "undefined") {
                        if (typeof this.listTasks[this.currentTask.id] != 'undefined') {
                            this.listTasks[this.currentTask.id]['works'][data["work_id"]]['check_list'][result.id] = result;
                        }
                    }
                }
            }
        },
        // calculate number check list
        percent(data) {
            return taskHelper.calculateListWorkTodo(data);
        },
        // show date deline check list
        convertDate(date) {
            return taskHelper.convertDate(date);
        },
    },
    created() {},
    mounted() {},
};
</script>
<template>
    <div class="list_work_todo">
        <div class="work-todo" v-for="work in currentTask.works">
            <div class="work-todo-header d-flex flex-row align-items-center">
                <div :class="['d-flex flex-row align-items-center name']">
                    <i class="ri-checkbox-line"></i>
                    <span>{{ work.title }}</span>
                </div>
                <b-button variant="light" @click="deleteWordToto(work.id)"
                    >Xóa</b-button
                >
            </div>
            <div class="d-flex align-items-center">
                <span>{{ percent(currentTask.works)["percent"][work.id] + "%" }}</span>
                <b-progress
                    :value="percent(currentTask.works)['percent'][work.id]"
                    :max="100"
                    :variant="`${
                        percent(currentTask.works)['percent'][work.id] == 100
                            ? 'success'
                            : ''
                    }`"
                ></b-progress>
            </div>
            <div v-if="work.check_list && work.check_list.length != 0">
                <div
                    :class="['mt-2']"
                    v-for="(checklist, index) in work.check_list"
                    :key="index"
                >
                    <div v-if="checklist.deadline" class="time">
                        <span>{{ convertDate(checklist.deadline) }}</span>
                    </div>
                    <div
                        class="d-flex justify-content-between align-items-center"
                    >
                        <div
                            class="d-flex justify-content-between align-items-center check"
                        >
                            <input
                                :class="{
                                    shake:
                                        percent(works)['percent'][work.id] ==
                                        100,
                                }"
                                type="checkbox"
                                v-model="checklist.status"
                                :checked="checklist.status"
                                @click="
                                    updateData({
                                        id: checklist.id,
                                        work_id: work.id,
                                        status: checklist.status,
                                    })
                                "
                            />
                            <div style="width: 100%">
                                <div @click="showEditChecklist(index)">
                                    <b-form-textarea
                                        :class="[
                                            `${
                                                activeEditChecklist[index]
                                                    ? 'show-textarea'
                                                    : ''
                                            }`,
                                            'form-control',
                                        ]"
                                        row="3"
                                        v-model="checklist.title"
                                    >
                                    </b-form-textarea>
                                </div>
                                <div
                                    :class="[
                                        'd-flex justify-content-between align-items-center',
                                    ]"
                                    v-if="activeEditChecklist[index]"
                                >
                                    <div
                                        :class="[
                                            'd-flex justify-content-between align-items-center mt-2 ms-4',
                                        ]"
                                    >
                                        <b-button
                                            @click="
                                                updateData({
                                                    id: checklist.id,
                                                    work_id: work.id,
                                                })
                                            "
                                            variant="primary"
                                            >Lưu</b-button
                                        >
                                        <b-button
                                            class="ms-2 bg-secondary text-light"
                                            @click="
                                                activeEditChecklist[index] =
                                                    !activeEditChecklist[index]
                                            "
                                            variant="light"
                                            ><i class="ri-close-fill"></i
                                        ></b-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            :class="[
                                'd-flex justify-content-between align-items-center',
                            ]"
                            v-if="activeEditChecklist[index]"
                        >
                            <b-button variant="light">
                                <VueDatePicker
                                    v-model="checklist.deadline"
                                    :month-change-on-scroll="false"
                                >
                                    <template #trigger
                                        ><i class="ri-time-line"></i
                                    ></template>
                                    <template #action-select="{ value }">
                                        <b-button
                                            variant="primary"
                                            @click="
                                                updateData({
                                                    id: checklist.id,
                                                    work_id: work.id,
                                                    data: value,
                                                })
                                            "
                                        >
                                            Save
                                        </b-button>
                                    </template>
                                </VueDatePicker>
                            </b-button>
                            <b-button
                                variant="light"
                                @click="
                                    deleteCheckList({
                                        id: checklist.id,
                                        work_id: work.id,
                                    })
                                "
                                ><i class="ri-delete-bin-7-line"></i
                            ></b-button>
                        </div>
                        <div
                            v-if="!activeEditChecklist[index]"
                            :class="[
                                'd-flex justify-content-between align-items-center',
                            ]"
                        >
                            <b-button variant="light">
                                <VueDatePicker
                                    v-model="checklist.deadline"
                                    :month-change-on-scroll="false"
                                >
                                    <template #trigger
                                        ><i class="ri-time-line"></i
                                    ></template>
                                    <template #action-select="{ value }">
                                        <b-button
                                            variant="primary"
                                            @click="
                                                updateData({
                                                    id: checklist.id,
                                                    work_id: work.id,
                                                    data: value,
                                                })
                                            "
                                        >
                                            Save
                                        </b-button>
                                    </template>
                                </VueDatePicker>
                            </b-button>
                            <b-button
                                variant="light"
                                @click="
                                    deleteCheckList({
                                        id: checklist.id,
                                        work_id: work.id,
                                    })
                                "
                                ><i class="ri-delete-bin-7-line"></i
                            ></b-button>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="showActiveChecklist[work.id]" class="ms-5 mt-1">
                <div>
                    <b-form-textarea
                        v-model="nameChecklist"
                        placeholder="Nhập tiêu đề...."
                        rows="3"
                        max-rows="6"
                    ></b-form-textarea>
                </div>
                <div>
                    <b-button
                        @click="newCheckList(work.id)"
                        variant="primary"
                        class="mt-2"
                        >Thêm</b-button
                    >
                    <b-button
                        @click="
                            showActiveChecklist[work.id] =
                                !showActiveChecklist[work.id]
                        "
                        variant="light"
                        class="mt-2 ms-2"
                        >Hủy</b-button
                    >
                </div>
            </div>
            <b-button
                class="btn_add_work"
                v-if="!showActiveChecklist[work.id]"
                variant="light"
                @click="showAddChecklist(work.id)"
                >Thêm một mục</b-button
            >
        </div>
    </div>
</template>
