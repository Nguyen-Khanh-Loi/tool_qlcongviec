<script>
import { uploadMedia } from "@/js/helpers/media";
import { taskMethods, taskGetters} from "@/js/store/helpers";
import { VueEditor, Quill } from "vue3-editor";
import { ImageDrop } from "quill-image-drop-module";
Quill.register("modules/imageDrop", ImageDrop);

export default {
    components: {
        Editor: VueEditor,
    },
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
            taskUpdate:{},
            publicPath: process.env.PUBLIC_URL+'uploads/',
            editorSettings: {
                modules: {
                    imageDrop: true,
                }
            },
            editorDesc: false,
        }
    },
    computed: {
        ...taskGetters,
    },
    methods: {
        ...taskMethods,
        onShowModal(){ 
            this.$emit('showModalPopup', 'editor');
        },
        onHideShowModal(){ 
            this.$emit('hideModalPopup', 'editor');
        },
        async updateDataTask() {            
            this.taskUpdate["task_id"] = this.currentTask.id;
            var description = {
                description: this.currentTask.description,
            };
            this.taskUpdate["info_task"] = description;
            await this.updateTask(this.taskUpdate);
            this.$emit('hideModalPopup', 'editor');
        },
        // upload images ussing editor
        handleImageAdded: async function(file, Editor, cursorLocation, resetUploader) {
            var formData = new FormData();
            formData.append("file", file);
            var data = await uploadMedia.upload(formData);
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
            this.editorDesc = quill
        },
        async handlePaste(event, callback) {  
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
                var data = await uploadMedia.upload(formData);
                try {
                    if (data) {
                        const url = this.publicPath+data;
                        const editorInstance = this.editorDesc.editor;
                        editorInstance.insertEmbed(this.$refs.editor.quill.getSelection().index, 'image', url); // insert url image in content editor
                        this.currentTask.description = this.$refs.editor.quill.root.innerHTML 
                    }
                } catch (error) {
                    console.log('Upload Images', error)
                }
            } 
        },
    },
    created() {
    },
    mounted() {
        var ref = this
        let description = document.querySelector('#editor-description-task')
        document.addEventListener('click', (e) => {
            const check = e.composedPath().includes(description);
            if(!check) ref.onHideShowModal();           
        });
    },
}
</script>
<template>
    <!-- {{ currentTask }} -->
    <h6 d-flex flex-row align-items-center>
        <i class="ri-menu-2-line"></i>
        <span>Mô tả</span>
    </h6>
    <div :class="['description']" id="editor-description-task">
        <div
            v-if="!popupFiles"
            v-bind:innerHTML="`${
                currentTask.description
                    ? currentTask.description
                    : 'Thêm mô tả chi tiết hơn...'
            }`"
            :class="['content-desc']"
            @click="onShowModal"
        ></div>
        <div
            v-else="popupFiles"
            :class="['content-editor']"
        >
        <!-- useCustomImageHandler
        @imageAdded="handleImageAdded" -->
            <editor                
                use-custom-image-handler 
                @image-added="handleImageAdded"
                id="edit-current-task"
                ref="editor"
                :editorOptions="editorSettings"
                v-model="currentTask.description"               
                @paste="handlePaste"
                @focus="onFocus"
            ></editor>
            <div class="mt-3 mb-3">
                <b-button
                    variant="btn_save bg-primary me-2 text-light"
                    @click="updateDataTask()"
                    >Lưu</b-button
                >
                <b-button
                    :class="['btn_cancel bg-danger text-light']"
                    variant="light btn_cancel"
                    @click="onHideShowModal"
                    >Hủy</b-button
                >
            </div>
        </div>
    </div> 
</template>