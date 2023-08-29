<script>
import { taskHelper } from "@/js/helpers/helptask";
import { taskMethods, taskGetters} from "@/js/store/helpers";
import Labels from './Labels.vue';
export default {
  components: { Labels },
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
            file:null,
            linkUrl: "",
            checkUrl: false         
        };
    },
    computed: {
        ...taskGetters
    },
    methods: {
        ...taskMethods,
        async upload(e){
            let files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return;
            this.file = e.target.files[0];
            var data = {
                'task_id' : this.currentTask.id,
                'file' : this.file 
            }
            this.$emit('hideModalPopup', 'files');
            var data = await taskHelper.uploadFilesTask( data, this.$store );
            if (data) {
                this.listTasks[this.currentTask.id]['list_files'] = data.list_files;
                this.currentTask.list_files = data.list_files;
                this.file = null;
            }
        },
        async onUploadUrl(){
            if (this.linkUrl) {
                this.checkUrl = taskHelper.validateUrl(this.linkUrl);                
                if (this.checkUrl) {
                    var data = {
                        'url': this.linkUrl,
                        'name': this.checkUrl.hostname,
                        'task_id': this.currentTask.id
                    }
                    this.$emit('hideModalPopup', 'files');
                    var data = await this.uploadFile(data);
                    if (data) {
                        this.listTasks[this.currentTask.id]['list_files'] = data.list_files;
                        this.currentTask.list_files = data.list_files;
                        this.linkUrl = null;
                    }
                }
            }
            
        },
        // hidden modal 
        hideModalPopup(data){
            this.$emit('hideModalPopup', 'files')
        },
        // hidden modal 
        showModalPopup(data){
            this.$emit('showModalPopup', 'files')
        },
    },
    created() {
    },
    mounted() {
    },
}
</script>
<template>
    <b-list-group-item class="btn_add_file">
        <div class="item" @click="showModalPopup">
            <i class="ri-attachment-2"></i> File đính
            kèm
        </div>
        <div class="modalFile" v-if="popupFiles">
            <div
                :class="[
                    'modalFile-header d-flex flex-row align-items-center justify-content-center',
                ]"
            >
                <span>Đính kèm từ</span>
                <a
                    @click.stop="hideModalPopup"
                    ><i class="ri-close-line"></i
                ></a>
            </div>
            <div class="list_upload">
                <div class="upload">
                    <label for="choose_file">Máy tính</label>
                    <input type="file" v-modal="file" @change="upload" class="form-control" id="choose_file">
                </div>
            </div>
            <hr />
            <div class="attach_link">
                <strong>Đính kèm liên kết</strong>
                <input v-model="linkUrl" placeholder="Dán liên kết vào đây" />
                <div class="btn" @click="onUploadUrl">Đính kèm</div>
            </div>
            <hr />
            <!-- <p class="note">
                Mẹo: Bạn có thể kéo thả các tập tin và
                liên kết vào thẻ để tải chúng lên.
            </p> -->
        </div>
    </b-list-group-item>
</template>