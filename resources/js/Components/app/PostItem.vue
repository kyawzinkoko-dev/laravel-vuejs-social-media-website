<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import {
    HandThumbUpIcon,
    ChatBubbleBottomCenterIcon,
} from "@heroicons/vue/24/outline";
import PostHeaderUser from "./PostHeaderUser.vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import axiosClient from "@/axiosClient";
import ReadMoreReadLess from "../ReadMoreReadLess.vue";
import EditDeleteDropdown from "./EditDeleteDropdown.vue";
import CommentList from "./CommentList.vue";
import Attachments from "./Attachments.vue";
import { computed } from "vue";
import UrlPreview from "./UrlPreview.vue";
import PinIcon from "./PinIcon.vue";
const props = defineProps({
    post: Object,
});
const group = usePage().props.group? usePage().props.group : null;
console.log(group);
const authUser = usePage().props.auth.user;
const isPinned = computed(() => {
    if (group?.id) {
        return group.pinned_post_id === props.post.id;
    }
    return authUser.pinned_post_id === props.post.id;
});

const postBody = computed(() => {
    let content = props.post.body.replace(
        /(?:(\s+)|<p>)((#\w+)(?![^<]*<\/a>))/g,
        (match, group1, group2) => {
            const encodedGroup = encodeURIComponent(group2);
            return `${
                group1 || ""
            }<a href="/search/${encodedGroup}" class="hashtag">${group2}</a>`;
        }
    );

    return content;
});
const emit = defineEmits(["editClick", "attachmentClick"]);
const openEditModal = () => {
    emit("editClick", props.post);
};

function deletePost() {
    if (window.confirm("Are you sure you want to delete this post?")) {
        router.delete(route("post.destroy", props.post), {
            preserveScroll: true,
        });
    }
}
function openAtachment(index) {
    emit("attachmentClick", props.post, index);
}

//reaaction of post
function sendReaction(type) {
    axiosClient
        .post(route("post.reaction", props.post), {
            reaction: type,
        })
        .then(({ data }) => {
            props.post.current_user_has_reaction =
                data.current_user_has_reaction;
            props.post.num_of_reactions = data.num_of_reactions;
        });
}
function pinUnPinPost() {
    const form = useForm({
        forGroup: group?.id,
    });
    let isPinned = false;
    if (group?.id) {
        isPinned = group?.pinned_post_id === props.post.pinned_post_id;
    } else {
        isPinned = authUser.pinned_post_id === props.post.pinned_post_id;
    }
    form.post(route("post.pinUnpin", props.post), {
        preserveScroll: true,
        onSuccess: () => {
            if (group?.id) {
                group.pinned_post_id = isPinned ? null : props.post.id;
            } else {
                authUser.pinned_post_id = isPinned ? null : props.post.id;
            }
        },
    });
}
</script>

<template>
    <div class="bg-white dark:bg-gray-800 p-4 rounded border dark:border-gray-800 text-gray-900 dark:text-gray-300 shadow-sm mb-3">
        <div class="flex justify-between items-center mb-3">
            <PostHeaderUser :post="post" />
            <div class="flex">
                <div
                v-if="isPinned"
                class="flex items-center"
            >
        <PinIcon/> Pinned
       
        </div>
        <EditDeleteDropdown
                :post="post"
                @edit="openEditModal"
                @delete="deletePost"
                @pin="pinUnPinPost"
            />
            </div>
           
        </div>
        
        <div>
            <ReadMoreReadLess :content="postBody" />
            <UrlPreview :preview="post.preview" :url="post.preview_url" />
        </div>
        <!-- Attachment -->
        <div
            class="grid gap-2 my-2"
            :class="[
                post.attachments?.length === 1 ? 'grid-cols-1' : 'grid-cols-2',
            ]"
        >
            <Attachments
                :attachments="post.attachments"
                @attachmentClick="openAtachment"
            />
        </div>
        <!--like dislike comment-->

        <Disclosure v-slot="{ open }" class=" transition-all" as="div">
            <div class="transition-all flex items-center gap-4 justify-between">
                <button
                    @click.prevent="sendReaction('like')"
                    class="flex justify-center text-gray-800 dark:bg-gray-700 dark:text-gray-300 shadow flex-1 items-center gap-1 py-2 px-4 bg-gray-100 rounded-lg hover:bg-gray-200"
                    :class="[
                        post.current_user_has_reaction
                            ? 'bg-sky-100 dark:bg-sky-800 hover:bg-sky-200'
                            : 'bg-gray-100 hover:bg-gray-200',
                    ]"
                >
                    <span><HandThumbUpIcon class="w-6 h-6" /></span>
                    <span>{{ post.num_of_reactions }}</span>
                    <span class="">{{
                        post.current_user_has_reaction ? "Unlike" : "Like"
                    }}</span>
                </button>

                <DisclosureButton
                    class="flex justify-center items-center flex-1 px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200"
                >
                    <ChatBubbleBottomCenterIcon class="w-6 h-6" />
                    <span class="mr-2">{{ post.num_of_comments }}</span>
                    Comment
                </DisclosureButton>
            </div>

            <DisclosurePanel
                class="px-4 transition-all max-h-80 pb-2 pt-4 text-sm text-gray-500 overflow-auto"
            >
                <CommentList
                    :post="post"
                    :data="{ comments: post.comments }"
                    :placeholder="'Enter your comment'"
                />
            </DisclosurePanel>
        </Disclosure>
    </div>
</template>

<style scoped></style>
