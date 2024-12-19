<template>
    <template v-for="(attachment, idx) of attachments.slice(0, 4)">
        <div
            @click="$emit('attachmentClick', idx)"
            class="group bg-blue-100 h-full w-full cursor-pointer flex items-center justify-center flex-col text-gray-500 relative"
        >
            <div
                v-if="idx === 3 && attachments.length > 4"
                class="text-center flex justify-center items-center absolute top-0 left-0 right-0 bottom-0 text-white bg-black/60 z-10 text-2xl"
            >
                +{{ attachments.length - 4 }} more
            </div>
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
            <div v-else-if="isVideo(attachment)" class=" relative flex justify-center items-center">
                <PlayCircleIcon class="w-20 h-20 absolute z-20 text-white opacity-45"/>
                <div class="absolute top-0 left-0 z-10 w-full h-full bg-black/40"></div>
                <video :src="attachment.url" class=" "> </video>
            </div>
            <div class="py-32 items-center flex flex-col justify-center" v-else>
                <PaperClipIcon class="w-12 h-12" />
                <small> {{ attachment.name }}</small>
            </div>
        </div>
    </template>
    </template>
    <script setup>
    import { isImage, isVideo } from "@/helper";
    import { ArrowDownTrayIcon,PlayCircleIcon, PaperClipIcon } from "@heroicons/vue/24/outline";
     const props = defineProps({
        attachments: Array,
    });
    defineEmits(["attachmentClick"]);
    </script>
    
    <style lang="scss" scoped></style>
    