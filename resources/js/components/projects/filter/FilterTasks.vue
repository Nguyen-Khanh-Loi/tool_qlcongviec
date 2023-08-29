<script>
import { userHelper } from "@/js/helpers/users";
import { taskGetters } from "@/js/store/helpers";
import $ from "jquery";
export default {
    props: {
        labels: {
            type: Object,
            default: () => {
                return false;
            },
        },
        users: {
            type: Object,
            default: () => {
                return false;
            },
        },
        number: {
            type: Object,
            default: () => {
                return false;
            },
        },
    },
    data() {
        return {
            filterDates: [
                {
                    value: "no",
                    title: "Không có ngày hết hạn",
                    class: "",
                    icon: "ri-calendar-2-line",
                    color: "#091e420f",
                },
                {
                    value: "out_date",
                    title: "Quá hạn",
                    nclass: "danger",
                    icon: "ri-time-line",
                    color: "#ca3521",
                },
                {
                    value: "day_date",
                    title: "Hết hạn vào ngày mai",
                    nclass: "warning",
                    icon: "ri-time-line",
                    color: "#F5A623",
                },
                {
                    value: "week_date",
                    title: "Hết hạn vào tuần sau",
                    nclass: "",
                    icon: "ri-time-line",
                    color: "#e2b203",
                },
                {
                    value: "month_date",
                    title: "Hết hạn vào tháng sau",
                    nclass: "",
                    icon: "ri-time-line",
                    color: "#7ED321",
                },
            ],
            filterData: {},
            nameSearch: "",
            show: false,
            // number: null
        };
    },
    computed: {
        ...taskGetters,
    },
    methods: {
        showFilter() {
            this.show = !this.show;
        },
        showAvatar(url) {
            return userHelper.avatar(url);
        },
        fullName(user) {
            return userHelper.fullName(user);
        },
        addLabelFilter(event) {
            // add user id in filter task
            try {
                if (!this.filterData["label"]) {
                    this.filterData["label"] = [];
                }
                if (!this.filterData["label"]["id"]) {
                    this.filterData["label"]["id"] = [];
                }
                if (event.target.checked) {
                    this.filterData["label"]["id"].push(event.target.value);
                } else {
                    this.filterData["label"]["id"] = this.filterData["label"][
                        "id"
                    ].filter(function (e) {
                        return e !== event.target.value;
                    });
                    if (this.filterData["label"]["id"].length == 0) {
                        delete this.filterData["label"]["id"];
                    }
                    if (
                        typeof this.filterData["label"]["no"] == "undefined" &&
                        typeof this.filterData["label"]["id"] == "undefined"
                    ) {
                        delete this.filterData["label"];
                    }
                }
                this.onFilter();
            } catch (error) {
                console.log(error);
            }
        },
        addUserIdFilter(event) {
            // add user id in filter task
            try {
                if (!this.filterData["users"]) {
                    this.filterData["users"] = [];
                }
                if (!this.filterData["users"]["list_user"]) {
                    this.filterData["users"]["list_user"] = [];
                }
                if (event.target.checked) {
                    this.filterData["users"]["list_user"].push(
                        event.target.value
                    );
                    $("#checkAllUsers").prop("checked", true);
                } else {
                    this.filterData["users"]["list_user"] = this.filterData[
                        "users"
                    ]["list_user"].filter(function (e) {
                        return e !== event.target.value;
                    });
                    if (this.filterData["users"]["list_user"].length == 0) {
                        delete this.filterData["users"]["list_user"];
                        $("#checkAllUsers").prop("checked", false);
                    }
                    if (
                        typeof this.filterData["users"]["no"] == "undefined" &&
                        typeof this.filterData["users"]["list_user"] ==
                            "undefined"
                    ) {
                        delete this.filterData["users"];
                    }
                }
                this.onFilter();
            } catch (error) {
                console.log(error);
            }
        },
        onFilter() {
            this.$emit("filterTask", this.filterData);
        },
        onSearch(e) {
            if (e.target.value) {
                this.filterData["search"] = e.target.value;
            } else {
                delete this.filterData["search"];
            }
            this.onFilter();
        },
        onClearSearch(e){
            this.filterData = {};
            this.onFilter();
            $(this.$refs.myFilter.querySelectorAll(".checkbox input[type='checkbox']")).prop('checked', false);
            $(this.$refs.myFilter.querySelectorAll("input[type='text']")).val('');
        }
    },
    watch: {},
    created() {},
    mounted() {
        $(this.$refs.myFilter).on("click", function (evt) {
            evt.stopPropagation();
        });
        const ref = this;
        const elementCheckBox = $(
            this.$refs.myFilter.querySelectorAll(".checkbox")
        );
        elementCheckBox.click(function (evt) {
            var $this = $(this).find('input[type="checkbox"]');
            const nameValue = $this.val();
            const nameAttr = $this.attr("name");
            if (nameAttr == "user") {
                if (typeof ref.filterData["users"] == "undefined") {
                    ref.filterData["users"] = [];
                }
                if ($this.is(":checked")) {
                    $this.prop("checked", true);
                    if (nameValue == "no") {
                        ref.filterData["users"]["no"] = nameValue;
                    } else {
                        // add list user in filter
                        if (
                            typeof ref.filterData["users"]["list_user"] ==
                            "undefined"
                        ) {
                            ref.filterData["users"]["list_user"] = [];
                        }
                        var childrenElement = $this
                            .parents(".item")
                            .find('.list-user input[type="checkbox"]');
                        childrenElement.each(function (index, element) {
                            ref.filterData["users"]["list_user"].push(
                                this.value
                            );
                            $(this).prop("checked", true);
                        });
                    }
                } else {
                    if (ref.filterData["users"].length > 0) {
                        ref.filterData["users"] = ref.filterData[
                            "users"
                        ].filter(function (e) {
                            return e != nameValue;
                        });
                    }
                    $this.prop("checked", false);
                    if (nameValue == "no") {
                        delete ref.filterData["users"]["no"];
                    } else {
                        delete ref.filterData["users"]["list_user"];
                        var childrenElement = $this
                            .parents(".item")
                            .find('.list-user input[type="checkbox"]');
                        childrenElement.each(function (index, element) {
                            $(this).prop("checked", false);
                        });
                    }
                    if (
                        typeof ref.filterData["users"]["no"] == "undefined" &&
                        typeof ref.filterData["users"]["list_user"] ==
                            "undefined"
                    ) {
                        delete ref.filterData["users"];
                    }
                }
            }

            // add date in filter
            if (nameAttr == "date") {
                if (typeof ref.filterData["date"] == "undefined") {
                    ref.filterData["date"] = [];
                }
                if ($this.is(":checked")) {
                    $this
                        .parents(".list")
                        .find('input[type="checkbox"]')
                        .prop("checked", false);
                    $this.prop("checked", true);
                    ref.filterData["date"] = nameValue;
                } else {
                    $(this).prop("checked", false);
                    delete ref.filterData["date"];
                }
            }

            // label in filter
            if (nameAttr == "label") {
                if (typeof ref.filterData["label"] == "undefined") {
                    ref.filterData["label"] = [];
                }
                if ($this.is(":checked")) {
                    $this.prop("checked", true);
                    if (nameValue == "no") {
                        ref.filterData["label"]["no"] = nameValue;
                    }
                } else {
                    $(this).prop("checked", false);
                    if (nameValue == "no") {
                        delete ref.filterData["label"]["no"];
                    }
                    if (
                        typeof ref.filterData["label"]["no"] == "undefined" &&
                        typeof ref.filterData["label"]["id"] == "undefined"
                    ) {
                        delete ref.filterData["label"];
                    }
                }
            }
            ref.onFilter();
        });
         // await this.auth();
        let wrap_filter_task = document.querySelector('.wrap_filter_task')
        document.addEventListener('click', (e) => {
            const check = e.composedPath().includes(wrap_filter_task);
            if(!check) {
                this.show = false; 
            }         
        });
    },
};
</script>
<template>
    <div
        class="text-end d-flex align-items-center justify-content-end"        
    >
        <div class="btn-group wrap_filter_task">
            <div class="d-flex align-items-center btn_filter rounded-1">
                <button
                    type="button"
                    :class="[`${number['check'] ? 'checkcount' : ''}`, '']"
                    @click="showFilter"
                >
                    <span class="icon icon-close"
                        ><i class="ri-filter-3-line"></i
                    ></span>
                    <span>Lọc</span>
                    <span class="rounded-circle" v-if="number['check']">{{
                        number['count']
                    }}</span>
                </button>
                <span @click="onClearSearch" class="close" role="button" v-if="number['check']"
                    ><i class="ri-close-line"></i
                ></span>
            </div>
            <div
                :class="[`${show ? 'showmodal' : ''}`, 'dropdown_add_user']"
                ref="myFilter"
                id="myFilter"
            >
                <div>
                    <div
                        class="modal_move-header d-flex flex-row align-items-center justify-content-center"
                    >
                        <span>Lọc</span
                        ><a @click.stop="showFilter"
                            ><i class="ri-close-line"></i
                        ></a>
                    </div>
                </div>
                <div class="dropdown_content">
                    <div class="dropdown_container">
                        <div class="item-dropdown mt-3">
                            <span>Từ khóa</span>
                            <input
                                type="text"
                                @input="onSearch"
                                placeholder="Nhập từ khóa"
                                class="mt-1"
                            />
                        </div>
                        <div class="item-dropdown mt-1">
                            <span>Thành viên</span>
                            <ul class="list">
                                <li class="item">
                                    <span class="checkbox">
                                        <input
                                            class="input"
                                            type="checkbox"
                                            name="user"
                                            :value="'no'"
                                        />
                                    </span>
                                    <span class="d-flex align-items-center">
                                        <i class="ri-price-tag-3-line"></i>
                                        <span>Không có thành viên</span>
                                    </span>
                                </li>
                                <li class="item item_select_member">
                                    <span class="checkbox">
                                        <input
                                            class="input"
                                            type="checkbox"
                                            id="checkAllUsers"
                                            name="user"
                                            :value="'check'"
                                        />
                                    </span>
                                    <span>
                                        <button
                                            type="button"
                                            class="btn_filter dropdown-toggle d-flex align-items-center"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false"
                                        >
                                            <span>Chọn thành viên</span>
                                            <span class="icon icon-down"
                                                ><i
                                                    class="ri-arrow-down-s-line"
                                                ></i
                                            ></span>
                                        </button>
                                        <div class="dropdown-menu">
                                            <ul class="list-user">
                                                <li
                                                    class="item"
                                                    v-for="user in users"
                                                >
                                                    <span>
                                                        <input
                                                            class="input choose_user"
                                                            type="checkbox"
                                                            name="choose_user"
                                                            :value="user.id"
                                                            @click="
                                                                addUserIdFilter
                                                            "
                                                        />
                                                        <!-- {{ `${fullName(user)}` }} -->
                                                    </span>
                                                    <span class="name">
                                                        <img
                                                            :src="`${showAvatar(
                                                                user.avatar
                                                            )}`"
                                                            :alt="`${fullName(
                                                                user
                                                            )}`"
                                                            :title="`${fullName(
                                                                user
                                                            )}`"
                                                        />
                                                        {{
                                                            `${fullName(user)}`
                                                        }}
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div class="item-dropdown">
                            <span>Ngày hết hạn</span>
                            <ul class="list">
                                <li
                                    v-for="data in filterDates"
                                    :class="[data.nclass, 'item']"
                                >
                                    <span class="checkbox">
                                        <input
                                            type="checkbox"
                                            name="date"
                                            :value="data.value"
                                        />
                                    </span>
                                    <span
                                        :class="data.class"
                                        class="d-flex align-items-center"
                                    >
                                        <span class="icon"
                                            ><i
                                                :class="data.icon"
                                                :style="{
                                                    background: data.color,
                                                    color: '#fff',
                                                }"
                                            ></i
                                        ></span>
                                        {{ data.title }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div class="item-dropdown">
                            <span>Nhãn</span>
                            <ul class="list">
                                <li class="item">
                                    <span class="checkbox">
                                        <input
                                            type="checkbox"
                                            name="label"
                                            :value="'no'"
                                        />
                                    </span>
                                    <span class="d-flex align-items-center">
                                        <i class="ri-price-tag-3-line"></i>
                                        <span>Không có Nhãn</span>
                                    </span>
                                </li>
                                <li class="item" v-for="label in labels">
                                    <span class="checkbox">
                                        <input
                                            type="checkbox"
                                            :name="'label'"
                                            :value="label.id"
                                            @click="addLabelFilter"
                                        />
                                    </span>
                                    <span
                                        class="name"
                                        :style="{ background: label.color }"
                                        >{{ label.name }}</span
                                    >
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
.showmodal {
    display: block !important;
}
.dropdown_add_user {
    display: none;
    position: absolute;
    right: 0;
    z-index: 1000;
    top: 36px;
    background: #fff;
    .backdrop {
        position: fixed;
        top: 0;
        right: 0;
        left: 0;
        bottom: 0;
    }
    .dropdown_content {
        max-height: 571px;
        overflow-y: scroll;
        width: 100%;
        display: flex;
        flex-direction: column;
        text-align: left;
        position: relative;
        z-index: 100;
        .dropdown_container {
            width: 93%;
        }
    }
    .modal_move-header {
        position: relative;
        z-index: 100;
        a {
            cursor: pointer;
        }
    }
    .dropdown_content::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
        border-radius: 5px;
        background-color: #f5f5f5;
    }

    .dropdown_content::-webkit-scrollbar {
        width: 5px;
        background-color: #f5f5f5;
    }

    .dropdown_content::-webkit-scrollbar-thumb {
        border-radius: 5px;
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
        background-color: rgba(0, 0, 0, 0.1);
    }
    .modal_move-header {
        a {
            background: none !important;
        }
    }
    .item-dropdown {
        span {
            margin-left: 0;
        }
    }
    ul {
        list-style: none;
        padding: 0;
        li:first-child {
            i {
                color: #000 !important;
            }
        }
        .item_select_member {
            align-items: baseline !important;
            .choose_user {
                width: 16px;
                height: 16px;
                margin-right: 10px;
            }
            span {
                i {
                    background: none !important;
                }
            }

            span:nth-child(2) {
                width: 100%;
                button {
                    border: 0;
                    width: 100%;
                    padding: 5px;
                    justify-content: space-between;
                    .icon-down {
                        width: 24px;
                    }
                }
                ul {
                    padding: 7px;
                    width: 223px;
                }
            }
        }
        .item {
            display: flex;
            align-items: center;
            .name {
                padding: 5px 10px;
                width: 100%;
            }
            span {
                i {
                    margin-right: 5px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    border-radius: 50%;
                    width: 24px;
                    height: 24px;
                    background: #091e420f;
                }
            }
            .checkbox {
                margin: 0 10px;
                input {
                    width: 16px;
                    height: 16px;
                }
            }
            .name {
                img {
                    width: 25px;
                    height: 25px;
                }
            }
        }
    }
}
.layoutview-header {
    .layoutview-header-right{
        .btn_filter {
            overflow: hidden;
            &.rounded-1 {
                padding: 0;
            }
            button {
                display: flex;
                flex-direction: row;
                flex-wrap: nowrap;
                justify-content: center;
                align-items: center;
                align-content: stretch;
                border: 0;
                outline: none;
                padding:0 5px;
                background-color: transparent;
                transition: all .3s ease-in-out;
                span.rounded-circle {
                    padding: 0;
                    display: inline-block;
                    line-height: 27px;
                    background-color: #ffffff;
                    margin: 0 5px;
                    width: 25px;
                    height: 25px;
                    text-align: center;
                    font-size: 12px;
                }
                &.checkcount {
                    &:hover{
                        transition: all .3s ease-in-out;
                        background-color: #ffffff;
                    }
                }
            }
            .close{
                padding: 0 3px;
                text-align: center;
                transition: all .3s ease-in-out;
                &:hover {
                    background-color: #fff;
                }
                i {
                    margin: 0;
                }
            }
        }
    }
}
</style>
