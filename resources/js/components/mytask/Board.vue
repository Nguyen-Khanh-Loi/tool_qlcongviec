<template>
    <div class="row">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">Created on Apr 28</div>
            <div class="d-flex align-items-center">
                <b-dropdown variant="white" right toggle-class="dropdown-item">
                    <template v-slot:button-content>
                        <div
                            class="btn_incomplete_tasks d-flex align-items-center"
                        >
                            <i class="ri-checkbox-circle-line me-1"></i>
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
                            <i class="ri-arrow-right-s-line ms-2"></i>
                            <div class="connect position-absolute"></div>
                            <div
                                class="completed_tasks_dropdown position-absolute"
                            >
                                <div class="p-1">All completed tasks</div>
                                <div class="p-1">Marked complete since</div>
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
                <div class="btn_sort d-flex align-items-center ms-3">
                    <i class="ri-arrow-up-down-fill me-1"></i>
                    <span>Sort</span>
                </div>
            </div>
        </div>
        <hr class="mt-3 mb-4" />
        <div class="col-3" v-for="item of ListMyProject" :key="item.id">
            <div class="card card_main">
                <div class="card-body card_header pt-3 pb-2">
                    <h4 class="card-title">{{ item.name }}</h4>
                </div>
                <div class="card-body border-bottom card-content">
                    <div class="task-list">
                        <div class="card task-box cursor-pointer">
                            <b-progress> </b-progress>
                            <div class="card-body cursor-pointer">
                                <div class="list_filter">
                                    <div
                                        class="filter"
                                        :style="{ backgroundColor: `red` }"
                                    ></div>
                                </div>
                                <div class="float-end ml-2">
                                    <div>Mar 15, 2023</div>
                                </div>
                                <div class="mb-3">
                                    <a href="">#2</a>
                                </div>
                                <div>
                                    <h5 class="font-size-16">
                                        <a> Task test demo</a>
                                    </h5>
                                </div>

                                <div class="d-flex team mb-0">
                                    <div :class="['d-flex align-items-center']">
                                        <i class="ri-time-line me-1"></i>
                                        04/04/2023
                                    </div>
                                    <div :class="['d-flex align-items-center']">
                                        <i class="ri-attachment-2"></i>2
                                    </div>
                                    <div :class="['d-flex align-items-center']">
                                        <i class="ri-checkbox-line"></i>5/8
                                    </div>
                                    <div class="align-self-center d-flex">
                                        <div class="list-member">
                                            <div class="team-member">
                                                <a
                                                    class="team-member d-inline-block"
                                                >
                                                    <img
                                                        src="http://localhost/quanlycongviec/public/images/avatar.png"
                                                        class="rounded-circle avatar-xs"
                                                    />
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn_add_card">
                            <i class="ri-add-line"></i>
                            <span> Thêm thẻ</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
// import frame1 from './component/frame1.vue';
export default {
    name: "board",
    data() {
        return {
            ListMyProject: [
                { name: "To do" },
                { name: "Pending" },
                { name: "Doing" },
                { name: "Done" },
            ],
        };
    },
};
</script>
<style lang="scss" scoped>
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
</style>
