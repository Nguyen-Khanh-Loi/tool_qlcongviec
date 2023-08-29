<script>
import { taskHelper } from "@/js/helpers/helptask";
import { userHelper } from "@/js/helpers/users";
import { taskMethods, taskGetters} from "@/js/store/helpers";
import { dateHelper } from "@/js/helpers/datehelper";
import { VueEditor } from "vue3-editor";
import { uploadMedia } from "@/js/helpers/media";
import {
    socketMethods,
    socketGetters
} from "@/js/store/helpers";
import moment from "moment";
import { socket } from "@/js/store/modules/socket";
export default {
    props: {
        infoAuth: {
            type: Object,
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
    components: {
        VueEditor
    },
    data() {
        return {
            showActive:false,
            messenger: false,
            content: {},
            currentEditor: null,
            currentIdSMS: null, // set current ref editor chang messenger
            publicPath: process.env.URL_SERVER,
            showBoxEditor:{}
        };
    },
    computed: {
        ...taskGetters,
        ...socketGetters,
    },
    methods: {
        ...taskMethods,        
        ...socketMethods,
        // show avata
        showAvatar(url){
            return userHelper.avatar(url);
        },
        fullName(user){
            return userHelper.fullName(user);
        },
        onSendMessenger(){
            if (this.messenger) {
                if (this.currentTask.project_id) {
                    var data = {
                        'messenger': this.messenger,
                        'user_id'  : this.infoAuth.id,
                        'room'         : this.currentTask.projects.slug,
                        'current_task'  : this.currentTask.id,
                        'project_id'  : this.currentTask.project_id,
                        'created_at': moment(new Date()).format('YYYY-MM-DD HH:mm:ss'), 
                        'updated_at': moment(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                        'event': 'send messenger'
                    }
                    this.onEventChat(data);
                }
                this.showActive = false;
                this.messenger = false;
            }            
        }, 
        
        onDateDuration(date){
            return dateHelper.showDateDuration(date);
        },  
        handleImageAddedMessenger: async function(file, Editor, cursorLocation, resetUploader) {
            var formData = new FormData();
            formData.append("file", file);
            var data = await uploadMedia.uploadServer(formData);
            try {
                if (data) {
                    const url = this.publicPath+data; // Get url from response
                    Editor.insertEmbed(cursorLocation, "image", url);
                    resetUploader();
                }
            } catch (error) {
                console.log('Upload Images', error)
            }
        },
        onFocus:function(quill){
            this.currentEditor = quill
        },
        // paste image in editor
        async handlePasteImages(event, callback) { 
            const current = 'editormessenger'+this.currentIdSMS;
            var idSMS = this.currentIdSMS
            var items = event.clipboardData.items;
            if (items == undefined) {
                if (typeof callback == "function") {
                    callback(undefined);
                }
            }
            for (var i = 0; i < items.length; i++) {
                if (items[i].type.indexOf("image") == -1) continue;
                var blob = items[i].getAsFile();
            }
            if (typeof blob != 'undefined') {
                event.preventDefault();
                const fileUpload = new Blob([blob], blob);
                const formData = new FormData();
                formData.append("file", fileUpload, blob.name);
                var data = await uploadMedia.uploadServer(formData);
                try {
                    if (data) {
                        const url = this.publicPath+data;
                        const editorInstance = this.currentEditor.editor;
                        if (idSMS) {
                            editorInstance.insertEmbed(this.$refs[current][0].quill.getSelection().index, 'image', url); // insert url
                            // set content when paste image
                            var results = this.$refs[current][0].quill.root.innerHTML;
                            this.$store.getters.listTasks[this.currentTask.id].messengers = this.$store.getters.listTasks[this.currentTask.id].messengers.filter(function(item){
                                if (item.id == idSMS) {
                                    item['content'] = results;
                                }
                                return item
                            });
                        }else{
                            editorInstance.insertEmbed(this.$refs.editormessenger.quill.getSelection().index, 'image', url); // insert url image in content editor
                            this.messenger = this.$refs.editormessenger.quill.root.innerHTML 
                        }
                    }
                } catch (error) {
                    console.log('Upload Images', error)
                }
            } 
        },
        // remove messenger
        removeMessenger(e){
            const data = {
                'id'  : parseInt(e.target.parentNode.parentNode.getAttribute('data-id')),
                'room': this.currentTask.projects.slug,
                'task_id' : this.currentTask.id,
                'event': 'remove messenger'
            }
            this.onEventChat(data);
        },
        // edit messenger
        editMessenger(e){
            var id = parseInt(e.target.parentNode.parentNode.parentNode.getAttribute('data-id'));
            var results = false;
            for (let index = 0; index < this.$store.getters.listTasks[this.currentTask.id].messengers.length; index++) {
                const sms = this.$store.getters.listTasks[this.currentTask.id].messengers[index]
                if (sms.id == id) {
                    results = sms.content;
                    break;
                }
            }
            var data = {
                'task_id' : this.currentTask.id,
                'id': parseInt(id),
                'content': results,
                'updated_at': moment(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                'event': 'edit messenger',
                'room': this.currentTask.projects.slug,
            };
            this.onEventChat(data);
            this.currentIdSMS = false;
            this.showBoxEditor = {};
        },
        // edit messenger
        onCancelEdit(e){
            var id = e.target.parentNode.parentNode.parentNode.getAttribute('data-id');
            var oldMessenger = this.content[id];
            this.$store.getters.listTasks[this.currentTask.id].messengers = this.$store.getters.listTasks[this.currentTask.id].messengers.filter(function(item){
                if (item.id == id) {
                    item['content'] = oldMessenger;
                }
                return item
            });
            this.showBoxEditor = {};
            this.currentIdSMS = false;
        },
        //show editor
        showEditor(data){
            this.showActive = false;
            this.showBoxEditor = {};
            this.showBoxEditor[data.id] = true;
            this.content[data.id] = data.content;
            this.currentIdSMS = data.id
        }
    },
    created() {          
        // join client in room
        socket.emit('join room', this.$route.params.slug);
    },
    mounted() {
        let content_messenger = document.querySelector('.content-messenger')
        document.addEventListener('click', (e) => {
            const check = e.composedPath().includes(content_messenger);
            if(!check) {
                this.showActive = false; 
            }else{
                this.showBoxEditor = {};
            }         
        });
    },
}
</script>
<template>  
    <div :class="['content-main-detail']">
        <h6 class="d-flex flex-row align-items-center">
            <i class="ri-list-check"></i><span>Hoạt động</span>
        </h6>                       
        <div :class="['list-work']" id="list-content-messeger">
            <div class="history_active">
                <div class="avatar">
                    <div class="image">
                        <img
                            :src="`${showAvatar(infoAuth.avatar)}`"
                            :alt="`${fullName(infoAuth)}`"
                        />
                    </div>
                </div>
                <div class="history_active-content content_chat content-messenger">
                    <div>
                        <input
                            class="form-control"
                            placeholder="Viết bình luận..."
                            v-if="!showActive"
                            @click="showActive = !showActive"
                        />
                    </div>
                    <div :class="['messenger']" v-if="showActive">                          
                        <div :class="['content-editor']">
                            <vue-editor
                                id="content-mesenger"
                                placeholder="Viết bình luận.."
                                v-model="messenger"
                                use-custom-image-handler 
                                @image-added="handleImageAddedMessenger"
                                ref="editormessenger"   
                                @focus="onFocus"
                                @paste="handlePasteImages"
                                class="editor"
                            ></vue-editor>
                        </div>
                        <div class="list_button">
                            <div class="btn_save bg-primary" @click="onSendMessenger" >Lưu</div>
                            <div
                                class="btn_cancel bg-danger"
                                @click="showActive = !showActive"
                            >
                                Hủy
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>
            <div :class="['list-messenger']">
                <ul v-if="currentTask.messengers">
                    <li v-for="messenger in currentTask.messengers">
                        <div class="history_active">
                            <div class="avatar">
                                <div class="image">
                                    <img
                                        :src="`${showAvatar(messenger.info_user.avatar)}`"
                                        :alt="`${fullName(messenger.info_user)}`"
                                    />
                                </div>
                            </div>
                            <div class="history_active-content content_chat edit_messenger" :data-id="messenger.id">
                                <div class="d-flex">
                                    <strong class="me-2">{{ fullName(messenger.info_user) }}</strong>
                                    <span>{{ onDateDuration(messenger.updated_at) }}</span>
                                    <span v-if="messenger.status_edit">{{ '( đã sửa )' }}</span>
                                </div>
                                <div v-if="!showBoxEditor[messenger.id]" class="form-control" v-bind:innerHTML="`${
                                    messenger.content
                                        ? messenger.content
                                        : ''
                                }`"></div>
                                <div v-else>
                                    <vue-editor
                                        :id="`content-mesenger-${messenger.id}`"
                                        placeholder="Viết bình luận.."
                                        v-model="messenger.content"
                                        use-custom-image-handler 
                                        @image-added="handleImageAddedMessenger"
                                        :ref="`editormessenger${messenger.id}`"   
                                        @focus="onFocus"
                                        @paste="handlePasteImages"
                                        :data-id="messenger.id"
                                    ></vue-editor>
                                    <div class="list_button">
                                        <div class="btn_save bg-primary" @click="editMessenger" >Lưu</div>
                                        <div
                                            class="btn_cancel bg-danger"
                                            @click="onCancelEdit"
                                        >
                                            Hủy bỏ thay đổi
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-1 d-flex align-items-center list_btn" v-if="messenger.info_user.id == infoAuth.id && !showBoxEditor[messenger.id]">
                                    <span class="decord"></span>
                                    <span role="button" @click="showEditor(messenger)" class="ms-1 text-decoration-underline">Chỉnh sửa</span>
                                    <span class="decord ms-2"></span>
                                    <span role="button" @click="removeMessenger" class="ms-1 text-decoration-underline btn_delete">Xóa</span>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
<style lang="scss">
.content_chat {
    width: 100%;
    .content-editor {
        background-color: #fff;
    }
    .bg-danger {
        color: #fff;
    }
    .list_button {
        margin:10px 0 0
    }    
}
#list-content-messeger {
    .history_active {
        align-items: self-start !important;
    }
    .list-messenger {
        ul {
            list-style: none;
            padding: 0;
            .form-control {
                *:last-child {
                    margin: 0;
                }
                img {
                    max-width: 100%;
                }
            }
        }
        .edit_messenger{
            font-size: 13px;
            .decord{
                width: 4px;
                height: 4px;
                background: #979191;
                border-radius: 100%;
                margin-right: 2px;
            }
        }
    }
}
// #content-mesenger {
//     height: 100px;
//     .ql-editor{
//         min-height: 100px;
//     }
// }
</style>