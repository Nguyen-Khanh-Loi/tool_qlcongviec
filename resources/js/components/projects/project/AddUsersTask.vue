<script>
import { taskHelper } from "@/js/helpers/helptask";
import { userHelper } from "@/js/helpers/users";
import { taskMethods, taskGetters} from "@/js/store/helpers";
import { socket } from "@/js/store/modules/socket"
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
            searchUsers : "",
            users : false,
        };
    },
    computed: {
        ...taskGetters,
        userInTasks(){
            var userInProject = this.$store.getters.projectUsers;
             const asArray = Object.values(userInProject);
            if(this.searchUsers){
                return asArray.filter((item)=>{
                    return this.searchUsers.toLowerCase().split(' ').every(v => item.name.toLowerCase().includes(v))
                })
            }else{
                return userInProject;
            }
        },
    },
    methods: {
        ...taskMethods,
        onShowModal(){ 
            this.$emit('showModalPopup', 'user');
        },
        onHideShowModal(){ 
            this.searchUsers = "",
            this.$emit('hideModalPopup', 'user');
        },
        //updated data task
        async updateDataCurrentTask( obj ) {
            obj['task_id'] = this.currentTask.id;
            obj['data_author'] = this.$store.getters.authUserData;
            // console.log('data', obj);
            // return;
            // socket.emit('user in tasks', obj);
            await taskHelper.updateDataTask( obj, this.$store );
            
        }, 
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
    <b-list-group-item>
        <div class="item" @click="onShowModal">
            <i class="ri-user-fill"></i> Thêm thành viên
        </div>
        <div class="modalMember" v-if="popupFiles">
            <div :class="['modalMember-header']">
                <span>Thành viên</span>
                <a @click="onHideShowModal"
                    ><i class="ri-close-line"></i
                ></a>
            </div>
            <input type="text" v-model="searchUsers" placeholder="Tìm kiếm các thành viên" />
            <p>Thành viên của task</p>
            <div class="member_of_table">
                <div
                    v-for="(user, index) in userInTasks"
                    :key="user.id"
                    :class="['list_member d-flex flex-row align-items-center w-100']"
                    @click="
                        updateDataCurrentTask({
                            action: currentTask.members
                                ? !currentTask.members[user.id]
                                    ? 'active'
                                    : 'deactive'
                                : 'active',
                            id: user.id,
                            data: user,
                            key: 'members',
                            field: 'list_user_ids',
                            types: 'add_remove_user'
                        })
                    "
                    :data-check="`${
                        currentTask.members
                            ? !currentTask.members[user.id]
                                ? 'active'
                                : 'deactive'
                            : 'active'
                    }`"
                >
                    <div class="avatar">
                        <div class="image">
                            <img
                                :src="`${showAvatar(user.avatar)}`"
                                :alt="`${fullName(user)}`"
                            />
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="name">
                            <p>{{ fullName(user) }}</p>
                        </div>
                        <span
                            v-if="
                                currentTask.members &&
                                currentTask.members[user.id]
                            "
                        >
                            <i class="ri-check-line"></i>
                        </span>
                    </div> 
                </div>
            </div>
        </div>
    </b-list-group-item>
</template>
