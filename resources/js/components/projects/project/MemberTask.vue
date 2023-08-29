<script>
import { userHelper } from "@/js/helpers/users";
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
        };
    },
    computed: {
        ...taskGetters,
    },
    methods: {
        ...taskMethods, 
        showAvatar(url){
            return userHelper.avatar(url);
        },
        fullName(user){
            return userHelper.fullName(user);
        }     
    },
    created() {
    },
    mounted() {
    },
}
</script>
<template>  
    <div class="member" v-if="currentTask.members">        
        <p>Thành viên</p>
        <div :class="['d-flex align-items-center']">
            <div class="list_user">
                <div
                    class="user"
                    v-for="(
                        userTask, index
                    ) in currentTask.members"
                    :key="index"
                >
                    <img
                        :src="`${showAvatar(userTask.avatar)}`"
                        :alt="`${fullName(userTask)}`"
                        :title="`${fullName(userTask)}`"
                    />
                </div>
            </div>
            <!-- <div
                class="btn_add_user d-flex align-items-center justify-content-center ms-2"
            >
                <i class="ri-add-line"></i>
            </div> -->
        </div>
    </div>
</template>
