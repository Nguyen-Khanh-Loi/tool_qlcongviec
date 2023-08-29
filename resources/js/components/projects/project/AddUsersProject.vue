<script>
import { taskHelper } from "@/js/helpers/helptask";
import { userHelper } from "@/js/helpers/users";
import { taskMethods, taskGetters, projectMethods} from "@/js/store/helpers";
export default {
    props: {
        popupFiles: {
            type: Boolean,
            default: () => {
                return false
            },
        },
        projectId: {
            type: Number,
            default: () => {
                return false
            },
        },
    },
    data() {
        return {
            searchUsers : "",
            loading : false,
            path : '',

        };
    },
    computed: {
        ...taskGetters,
        resultUsers(){
            var dataUsers = this.$store.getters.listUsers ;
            const asArray = Object.values(dataUsers);
            if(this.searchUsers){
                return asArray.filter((item)=>{
                    return this.searchUsers.toLowerCase().split(' ').every(v => item.name.toLowerCase().includes(v))
                })
            }else{
                return asArray.filter((item)=>{
                    return item.roles[0]['name'] != 'leader';
                })
            }
        },        
    },
    methods: {
        ...taskMethods,   
        ... projectMethods,
        async addUserProject(event, arr){
            event.stopPropagation();
            if (arr) {
                if (this.projectId) {                   
                    var data = {
                        'project_id' : this.projectId,
                        'user_id' : arr['user_id'],
                        'action' : arr['action']
                    }             
                    var results = await taskHelper.addRemoveUserInProject( data, this.$store ); 
                    if ( arr['action'] == 'deactive') {
                        delete this.projectUsers[arr['user_id']];
                        for (const key in this.$store.getters.listTasks) {
                            const task = this.$store.getters.listTasks[key];
                            if (task.list_user_ids) {
                                const listUserIds = task.list_user_ids.split(',');
                                var filterUsers = listUserIds.filter(function(e) { return parseInt(e) !== arr['user_id'] });
                                this.$store.getters.listTasks[key]['list_user_ids'] = filterUsers.join(",");
                                delete this.$store.getters.listTasks[key]['members'][arr['user_id']]; 
                            }
                        } 
                    }else{
                        if (results) {
                            this.projectUsers[arr['user_id']] = results;
                        }
                    }
                }
            }
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
    mounted() {},
}
</script>
<template> 
    <div class="text-end d-flex align-items-center justify-content-end">
        <ul class="list-inline mb-0" v-if="projectUsers">
            <li class="list-inline-item" v-for="user in projectUsers">
                <a
                    href="javascript: void(0);"
                    class="d-inline-block"
                    v-b-tooltip.hover
                    :title="user.name"
                >
                    <img
                        :src="`${showAvatar(user.avatar)}`"
                        class="rounded-circle avatar-xs"
                        :alt = "`${fullName(user)}`"
                    />
                </a>
            </li>
        </ul>
        <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="mdi mdi-plus me-1"></i>
            </button>
            <div class="dropdown-menu dropdown_add_user">
                <div>
                    <div class="modal_move-header d-flex flex-row align-items-center justify-content-center"><span>Thêm thành viên</span><a><i class="ri-close-line"></i></a></div>
                    <input type="text" v-model="searchUsers" placeholder="Tìm kiếm các thành viên" />
                </div>
                <p>Thành viên của project</p>
                <div class="member_of_table">
                    <div
                        role="button"
                        v-for="(user, index) in resultUsers"
                        :key="user.id"
                        :class="['list_member d-flex flex-row align-items-center dropdown-item']"
                        @click="addUserProject($event, {'user_id': user.id, 'action': projectUsers[user.id] ? 'deactive' : 'active'})"
                    >
                        <div class="avatar">
                            <div class="image">
                                <img
                                    :src="`${showAvatar(user.avatar)}`"
                                    :alt = "`${fullName(user)}`"
                                />
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <div class="name">
                                <p>{{ fullName(user) }}</p>
                            </div>
                            <span
                                v-if="
                                    projectUsers[user.id]
                                "
                            >
                                <i class="ri-check-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
