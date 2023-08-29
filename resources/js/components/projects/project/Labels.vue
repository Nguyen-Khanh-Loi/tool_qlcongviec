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
        labels: {
            type: Object,
            default: () => {
                return {}
            },
        },
    },
    data() {},
    computed: {
        ...taskGetters
    },
    methods: {
        ...taskMethods,
        onShowModal(){ 
            this.$emit('showModalPopup', 'label');
        },
        onHideShowModal(){ 
            this.$emit('hideModalPopup', 'label');
        },
        //updated data task
        async updateDataCurrentTask( obj ) {
            await taskHelper.updateDataTask( obj, this.$store )
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
        <div class="item" @click="onShowModal">
            <i class="ri-price-tag-3-line"></i> Nhãn
        </div>
        <div class="modalFilter" v-if="popupFiles">
            <div
                :class="[
                    'modalFilter-header d-flex flex-row align-items-center justify-content-between',
                ]"
            >
                <span>Nhãn</span>
                <a
                    @click="onHideShowModal"
                    ><i class="ri-close-line"></i
                ></a>
            </div>
            <div class="filter_of_table" v-for="(label,index) in labels">
                <div
                    class="list_color d-flex flex-row align-items-center"
                    
                    @click="updateDataCurrentTask(
                        {
                            'action' : currentTask.task_labels ? !currentTask.task_labels[label.id] ? 'active' : 'deactive' : 'active',
                            'id' : label.id,
                            'data': label,
                            'key' : 'task_labels',
                            'field': 'labels',
                        }
                    )"
                >
                    <i :class="`${ currentTask.task_labels ? !currentTask.task_labels[label.id] ? 'ri-checkbox-blank-line' : 'ri-checkbox-line' : 'ri-checkbox-blank-line' }`"></i>
                    <div class="color color1" :style="{ background: label.color}">
                        <!-- <div class="color_child"></div> -->
                        <p class="ms-2">{{ label.name }}</p>
                    </div>
                </div>                                       
            </div>
        </div>
    </b-list-group-item>
</template>
