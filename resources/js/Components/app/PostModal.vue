<template>
    <Teleport to="body">
        <TransitionRoot appear :show="show" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-50">
                <TransitionChild
                    as="template"
                    enter="duration-300 ease-out"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="duration-200 ease-in"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-black/25" />
                </TransitionChild>

                <div class="fixed inset-0 overflow-y-auto">
                    <div
                        class="flex min-h-full items-center justify-center text-center"
                    >
                        <TransitionChild
                            as="template"
                            enter="duration-300 ease-out"
                            enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100"
                            leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100"
                            leave-to="opacity-0 scale-95"
                        >
                            <DialogPanel
                                class="w-full max-w-md transform overflow-hidden rounded bg-white text-left align-middle shadow-xl transition-all"
                            >
                                <DialogTitle
                                    as="h3"
                                    class="bg-gray-200 font-medium leading-6 text-gray-900 py-3 px-4 flex items-center justify-between"
                                >
                                    {{
                                        post.id ? "Update Post" : "Create Post"
                                    }}
                                    <button
                                        @click="closeModal"
                                        class="rounded-full p-2 hover:bg-gray-400"
                                    >
                                        <XMarkIcon class="w-4 h-4" />
                                    </button>
                                </DialogTitle>
                                <div class="p-3">
                                    <PostHeaderUser
                                        :post="post"
                                        :show-time="false"
                                        class="mb-3"
                                    />
                                   
                                    <div
                                        v-if="error.group_id"
                                        class="bg-red-400 text-white py-1 px-3 mb-3 rounded"
                                    >
                                        {{ error.group_id }}
                                    </div>
                                    <ckeditor
                                        :editor="editor"
                                        v-model="form.body"
                                        :config="editorConfig"
                                    ></ckeditor>
                                 
                                    <div
                                        v-if="showExtansionWarning"
                                        class="py-2 px-2 border-l-4 border-amber-600 bg-amber-100 my-2"
                                    >
                                        File must be one of these extansion
                                        <br />
                                        <small>
                                            {{ attachmentExtansion.join(", ") }}
                                        </small>
                                    </div>
                                    <div
                                        v-if="error.attachments"
                                        class="py-2 px-3 border-l-4 border-red-600 bg-red-100 my-2"
                                    >
                                        {{ error.attachments }}
                                    </div>
                                    <div
                                        class="grid my-3 gap-3"
                                        :class="[
                                            computedAttachments.length === 1
                                                ? 'grid-cols-1'
                                                : 'grid-cols-2',
                                        ]"
                                    >
                                        <div
                                            class="flex flex-col"
                                            v-for="(
                                                myFile, index
                                            ) of computedAttachments"
                                        >
                                            <div
                                                class="group border-2 bg-blue-100 aspect-square h-full w-full flex p-2 rounded-sm items-center justify-center flex-col text-gray-500 relative"
                                                :class="[
                                                    attachmentError[index]
                                                        ? 'border-red-500'
                                                        : '',
                                                ]"
                                            >
                                                <button
                                                    @click="
                                                        resetMyFile(
                                                            myFile || myFile
                                                        )
                                                    "
                                                    class="p-1 transition-all bg-gray-400 hover:bg-black/20 rounded-full text-gray-100 absolute top-2 right-2"
                                                >
                                                    <XMarkIcon
                                                        class="w-5 h-5"
                                                    />
                                                </button>
                                                <div
                                                    v-if="myFile.deleted"
                                                    class="absolute opacity-100 flex items-center justify-between py-2 px-3 bg-black text-white right-0 bottom-0 left-0"
                                                >
                                                    To Be Delete
                                                    <ArrowUturnLeftIcon
                                                        @click="
                                                            undoDelete(myFile)
                                                        "
                                                        class="w-5 h-5 cursor-pointer z-30"
                                                    />
                                                </div>
                                                <img
                                                    v-if="
                                                        isImage(
                                                            myFile.file ||
                                                                myFile
                                                        )
                                                    "
                                                    :src="myFile.url"
                                                    class="h-full w-full object-contain aspect-square"
                                                    :class="[
                                                        myFile.deleted
                                                            ? 'opacity-50'
                                                            : '',
                                                    ]"
                                                />
                                                <div
                                                    v-else
                                                    class="flex justify-center items-center flex-col"
                                                    :class="[
                                                        myFile.deleted
                                                            ? 'opacity-50'
                                                            : '',
                                                    ]"
                                                >
                                                    <PaperClipIcon
                                                        class="w-10 h-10 mb-2"
                                                    />
                                                    <small class="text-center">
                                                        {{
                                                            (
                                                                myFile.file ||
                                                                myFile
                                                            ).name
                                                        }}</small
                                                    >
                                                </div>
                                            </div>
                                            <small
                                                class="text-red-500 text-center"
                                                >{{
                                                    attachmentError[index]
                                                }}</small
                                            >
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 py-3 px-4 flex gap-5">
                                    <button
                                        type="button"
                                        class="bg-indigo-600 flex justify-center items-center gap-1 w-full relative hover:bg-indigo-500 text-sm font-semibold py-2 px-3 text-white rounded transition-colors shadow-sm focus-visible:outline"
                                    >
                                        <input
                                            multiple
                                            @change="onAttachmentChange"
                                            type="file"
                                            class="absolute top-0 lef-0 right-0 bottom-0 opacity-0"
                                            @click.stop
                                        />
                                        <PaperClipIcon class="h-4 w-4" />

                                        AttachFile
                                    </button>
                                    <button
                                        type="button"
                                        class="bg-indigo-600 w-full flex items-center gap-1 justify-center hover:bg-indigo-500 text-sm font-semibold py-2 px-3 text-white rounded transition-colors shadow-sm focus-visible:outline"
                                        @click="submit"
                                    >
                                        <BookmarkIcon class="h-4 w-4" />
                                        Submit
                                    </button>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </Teleport>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
} from "@headlessui/vue";
import {
    XMarkIcon,
    PaperClipIcon,
    BookmarkIcon,
    ArrowUturnLeftIcon,
} from "@heroicons/vue/24/outline";
import PostHeaderUser from "./PostHeaderUser.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import { isImage } from "@/helper.js";
import { Ckeditor } from "@ckeditor/ckeditor5-vue";
import axiosClient from "@/axiosClient";
const editor = ClassicEditor;

const editorConfig = {
    toolbar: [
        "bold",
        "italic",
        "|",
        "bulletedList",
        "numberedList",
        "|",
        "heading",
        "|",
        "outdent",
        "indent",
        "|",
        "link",
        "|",
        "blockQuote",
    ],
};
const attachmentExtansion = usePage().props.attachmentExtansions;

const props = defineProps({
    post: {
        type: Object,
        required: false,
    },
    group: { type: Object, default: null },
    modelValue: Boolean,
});

/**
 * 'file' :File
 *  mime :'',
 *  src:''
 *  size :''
 * @type {attachmentFiles}
 */

const attachmentFiles = ref([]);
//show type of file extansions that are allowed as a warning if a user chose wrong file type
const attachmentError = ref([]);

//the error for total size of selected attachment file
const error = ref({});

//get the newly selected atachment and existing attachments of the post to update
const computedAttachments = computed(() => {
    if (props.post) {
        return [...attachmentFiles.value, ...(props.post.attachments || [])];
    }
    return attachmentFiles.value;
});

//show extansion warning
const showExtansionWarning = computed(() => {
    for (const myFile of attachmentFiles.value) {
        let file = myFile.file;
        let parts = file.name.split(".");
        let ext = parts.pop().toLowerCase();
        if (!attachmentExtansion.includes(ext)) {
            return true;
        }
    }
    return false;
});

//watch post changes
watch(
    () => props.post,
    () => {
        if (props.post) {
            form.body = props.post.body;
        } else {
            form.body = "";
        }
    }
);
//get the modelValue prop and update the value on emit of update:modelValue
const show = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});
const emit = defineEmits(["update:modelValue", "hide"]);
//close the modal and reset all the selected value
function closeModal() {
    show.value = false;

    resetModal();
    emit("hide");
}
//instance form value
const form = useForm({
    id: "",
    body: "",
    group_id: null,
    attachments: [],
    delete_file_ids: [],
    _method: "POST",
});

//update  or create post
const submit = () => {
    form.attachments = attachmentFiles.value.map((myFile) => myFile.file);
    if (props.group) {
        form.group_id = props.group.id;
    }

    if (props.post.id) {
        form._method = "PUT";
        form.post(route("post.update", props.post), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
            onError: (errors) => {
                processErrors(errors);
            },
        });
    } else {
        form.post(route("post.create"), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
            onError: (e) => {
                processErrors(e);
            },
        });
    }
};

//function to validate exansion of atachments
function processErrors(errors) {
    console.log(errors);
    for (const key in errors) {
        if (key.includes(".")) {
            const [, index] = key.split(".");
            attachmentError.value[index] = errors[key];
        } else {
            console.log(errors);
            error.value = errors;
        }
    }
}
//close the modal and reset the selected attachments
function resetModal() {
    form.reset();
    error.value = {};
    attachmentFiles.value = [];
    if (props.post.atachments) {
        props.post.attachments.forEach((file) => (file.deleted = false));
    }
}

//attachment preview
async function onAttachmentChange(e) {
    for (let file of e.target.files) {
        const myFile = {
            file,
            url: await readFile(file),
        };
        attachmentFiles.value.push(myFile);
    }

    e.target.value = null;
}
//get the base64 url of the selectd attchment
async function readFile(file) {
    return new Promise((resolve, reject) => {
        if (isImage(file)) {
            const reader = new FileReader();
            reader.onload = () => {
                resolve(reader.result);
            };
            reader.onerror = reject;
            reader.readAsDataURL(file);
        } else {
            resolve(null);
        }
    });
}

/**
 * function to  remove selected attachments
 *
 */
function resetMyFile(myFile) {
    if (myFile.file) {
        attachmentFiles.value = attachmentFiles.value.filter(
            (f) => f != myFile
        );
    } else {
        form.delete_file_ids.push(myFile.id);
        myFile.deleted = true;
    }
}

//undo seleted file that are to be delete
function undoDelete(myFile) {
    myFile.deleted = false;
    form.delete_file_ids = form.delete_file_ids.filter((id) => id != myFile.id);
}

</script>
