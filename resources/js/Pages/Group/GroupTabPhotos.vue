<template>
    <div class="grid grid-cols-3 gap-2">
        <template v-for="(attachment, idx) of photos">
            <div
                @click="openPhoto(idx)"
                class="group  h-full w-full p-1 bg-sky-100 cursor-pointer flex items-center justify-center flex-col text-gray-500 relative"
            >
                <!-- download -->
                <a
                    @click.stop
                    :href="route('post.download', attachment)"
                    class="p-1 opacity-0 z-20 group-hover:opacity-100 transition-all bg-gray-700 rounded text-gray-100 absolute top-2 right-2"
                >
                    <ArrowDownTrayIcon class="h-4 w-4 cursor-pointer" />
                </a>
                <!-- download -->
                <img
                    v-if="isImage(attachment)"
                    :src="attachment.url"
                    class="h-full w-full object-cover aspect-square"
                />
                <div
                    class="py-32 items-center flex flex-col justify-center"
                    v-else
                >
                    <PaperClipIcon class="w-12 h-12" />
                    <small> {{ attachment.name }}</small>
                </div>
            </div>
        </template>
    </div>
    <AttachmentPreviewModal
            :attachments="photos || []"
            v-model:index="currentPhoto.index"
            v-model="showModal"
        />
</template>

<script setup>
import AttachmentPreviewModal from "@/Components/app/AttachmentPreviewModal.vue";
import { isImage } from "@/helper";
import { ArrowDownTrayIcon, PaperClipIcon } from "@heroicons/vue/24/outline";
import { ref } from "vue";

const showModal = ref(false);
const currentPhoto = ref(0)

defineProps({
    photos: Array,
});


//photo previews 
const openPhoto = ( index) => {
    currentPhoto.value = {
        index,
    };
    showModal.value = true;
};
</script>

<style lang="scss" scoped></style>
