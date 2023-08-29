<script>
// import { taskMethods, taskGetters} from "../../store/helpers";
import { taskHelper } from "@/js/helpers/helptask";
import { taskMethods, taskGetters} from "@/js/store/helpers";
import { store } from '@/js/store/store';
export default {
    props: {
        cards: {
            type: Object,
            default: () => {
                return {}
            },
        },
        popupFiles: {
            type: Boolean,
            default: () => {
                return false
            },
        },
    },
    data() {
        return {
            card_id: store.getters.currentTask.card_id,
        };
    },
    computed: {
        ...taskGetters,
        // card_id(){
        //     return this.currentTask.card_id
        // }
    },
    methods: {
        ...taskMethods,
        async moveTask(e, card_id) {
            e.stopPropagation();
            var oldCardId = this.currentTask.card_id;
            var taskId    = this.currentTask.id;
            var newCardId = card_id;
            if (oldCardId != newCardId) {
                this.listTasks[taskId]['card_id'] = newCardId;
                var listOldCards = this.listTaskDraggable[oldCardId];
                var listOldCardsStore = this.taskDraggableStore[oldCardId];
                var listNewCards = listOldCards.filter(v => v !== taskId);
                var listNewCardsStore = listOldCardsStore.filter(v => v !== taskId);
                var data = {
                    'task_id' : taskId,
                    'info_task' : {
                        'card_id': newCardId
                    }
                }
                await this.updateTask(data);
                this.listTaskDraggable[oldCardId] = listNewCards;
                this.taskDraggableStore[oldCardId] = listNewCardsStore;
                if (typeof this.listTaskDraggable[newCardId] == 'undefined') {
                    this.listTaskDraggable[newCardId] = [];
                }
                if (typeof this.taskDraggableStore[newCardId] == 'undefined') {
                    this.taskDraggableStore[newCardId] = [];
                }
                this.listTaskDraggable[newCardId].push(taskId);
                // console.log('taskDraggableStore', taskDraggableStore);
                // this.taskDraggableStore[newCardId].push(taskId);
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
    <h6>Thao tác</h6>
    <b-list-group>
        <b-list-group-item class="btn_move">
            <div class="btn-group">
                <button type="button" class="item btn dropdown-toggle border-none" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ri-arrow-right-line" role="button"></i>
                    Di chuyển
                </button>
                <div class="dropdown-menu modal_move">
                    <div class="modal_move-header d-flex flex-row align-items-center justify-content-center"><span>Di chuyển thẻ</span><a><i class="ri-close-line"></i></a></div>
                    <h6 class="title">Chọn đích đến</h6>
                    <div class="modal_move-content">
                        <div class="btn select_list">
                            <div  class="button" role="button" v-for="(card, index) in cards" :class="[`${currentTask.card_id == card.id ? 'active' : ''}`,'dropdown-item']" @click="moveTask($event, card.id)">
                                {{ card.title }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </b-list-group-item>
    </b-list-group>
</template>