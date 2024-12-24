<template>
    <teleport to="body">
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
                    <div class="h-screen w-screen">
                        <TransitionChild
                            as="template"
                            class="w-full h-full"
                            enter="duration-300 ease-out"
                            enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100"
                            leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100"
                            leave-to="opacity-0 scale-95"
                        >
                            <DialogPanel
                                class="flex flex-col w-full transform overflow-hidden bg-slate-800 text-left align-middle shadow-xl transition-all"
                            >
                                <button
                                    @click="closeModal"
                                    class="absolute right-3 top-3 w-10 h-10 rounded-full hover:bg-black/10 transition flex items-center justify-center text-gray-100 z-40"
                                >
                                    <XMarkIcon class="w-6 h-6" />
                                </button>
                                <div class="relative group h-full">
                                    <div
                                        @click="previous"
                                        class="absolute opacity-0 group-hover:opacity-100 text-gray-100 cursor-pointer flex items-center w-12 h-full left-0 bg-black/5 z-30"
                                    >
                                        <ChevronLeftIcon class="w-12" />
                                    </div>
                                    <div
                                        @click="next"
                                        class="absolute opacity-0 group-hover:opacity-100 text-gray-100 cursor-pointer flex items-center w-12 h-full right-0 bg-black/5 z-30"
                                    >
                                        <ChevronRightIcon class="w-12" />
                                    </div>

                                    <div
                                        class="flex items-center justify-center w-full h-full p-3"
                                    >
                                        <img
                                            v-if="isImage(attachment)"
                                            :src="attachment.url"
                                            class="max-w-full max-h-full"
                                        />
                                        
                                        <div v-else-if="isVideo(attachment)" class="flex items-center">
                                            <video :src="attachment.url" controls autoplay></video>
                                        </div>
                                      
                                    </div>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </teleport>
</template>

<script setup>
import { computed } from "vue";
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
} from "@headlessui/vue";
import {
    XMarkIcon,
    ChevronRightIcon,
    ChevronLeftIcon,
    PaperClipIcon,
} from "@heroicons/vue/24/outline";
import { isImage ,isVideo} from "@/helper";

const props = defineProps({
    modelValue: {
        type: Boolean,
        required: true,
    },
    attachments: {
        type: Array,
        required: true,
    },
    index: Number,
});
const emit = defineEmits(["update:modelValue", "update:index"]);
const show = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});
const currentIndex = computed({
    get: () => props.index,
    set: (value) => emit("update:index", value),
});
const attachment = computed(() => {
    return props.attachments[currentIndex.value];
});
const closeModal = () => {
    show.value = false;
};

function previous() {
    if (currentIndex.value === 0) return;
    currentIndex.value--;
}
function next() {
    if (currentIndex.value === props.attachments.length - 1) return;
    currentIndex.value++;
}
</script>
