<script>
// import { taskMethods, taskGetters} from "../../store/helpers";
import { taskHelper } from "@/js/helpers/helptask";
// import { taskMethods, taskGetters} from "@/js/store/helpers";
// import moment from "moment";
export default {
    props: {
        deadline: {
            type: String,
            default: () => {
                return null
            },
        },
    },
    data() {
        return {
            // status: true
        };
    },
    computed: {
        // ...taskGetters
    },
    methods: {
        // ...taskMethods,
        showDateDeadLine(dateTasks){
            return taskHelper.calculateDate(dateTasks)
        }
    },
    created() {
    },
    mounted() {
       
    },
}
</script>
<template>
    
    <div v-if="deadline">
        <!-- {{ showDateDeadLine(deadline) }} -->
        <p>Ngày hết hạn</p>
        <div v-bind:innerHTML="showDateDeadLine(deadline)" class="deadline"></div>
    </div>
</template>