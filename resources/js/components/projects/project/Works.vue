<script>
import { taskHelper } from "@/js/helpers/helptask";
import { taskMethods, taskGetters} from "@/js/store/helpers";
export default {
    props: {
        popupFiles: {
            type: Boolean,
            default: () => {
                return false
            },
        },
    },
    data() {
        return {
            nameWorkTodo: "Việc cần làm",
        }
    },
    computed: {
        ...taskGetters
    },
    methods: {
        ...taskMethods,
        onShowModal(){ 
            this.$emit('showModalPopup', 'works');
        },
        onHideShowModal(){ 
            this.$emit('hideModalPopup', 'works');
        },
        
        // add new  work todo
        async addNewWordToto(){
            if (this.nameWorkTodo) {
                var data = {
                    'title': this.nameWorkTodo,
                    'task_id': this.currentTask.id,
                };
                await taskHelper.addWorkTodo( data, this.$store );
                this.$emit('hideModalPopup', 'works');
            }
        },
    },    
    created() {
    },
    mounted() {
    },
}
</script>
<template>  
    <b-list-group-item>
        <div class="item"  @click="onShowModal">
            <i class="ri-checkbox-line"></i> Việc cần
            làm
        </div>
        <div
            class="modal_work_todo"
            v-if="popupFiles"
        >
            <div
                :class="[
                    ' modal_work_todo-header d-flex flex-row align-items-center justify-content-center',
                ]"
            >
                <span>Thêm danh sách công việc</span>
                <a
                    @click.stop="onHideShowModal"
                    ><i class="ri-close-line"></i
                ></a>
            </div>
            <b-form-group
                label="Tiêu đề"
                label-for="title-input"
            >
                <b-form-input
                    id=""
                    v-model="nameWorkTodo"
                    :value ="nameWorkTodo"
                >
                </b-form-input>
            </b-form-group>
            <div class="btn_add" @click="addNewWordToto()">Thêm</div>
        </div>
    </b-list-group-item>
</template>
