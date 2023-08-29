<script>
import { taskHelper } from "@/js/helpers/helptask";
import { taskMethods, taskGetters } from "@/js/store/helpers";
export default {
    props: {
        popupFiles: {
            type: Boolean,
            default: () => {
                return false;
            },
        },
    },
    data() {
        return {};
    },
    computed: {
        ...taskGetters,
    },
    methods: {
        ...taskMethods,
    },
    created() {},
    mounted() {},
};
</script>
<template>
    <div class="label" v-if="currentTask.task_labels">
        <p>Nhãn</p>
        <div class="list_label">
            <div
                class="name_label"
                v-for="(label, index) in currentTask.task_labels"
                :key="index"
                :style="{ backgroundColor: `${label.color}` }"
            ></div>
        </div>
    </div>
</template>
