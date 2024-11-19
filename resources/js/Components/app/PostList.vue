<script setup>
import PostItem from "@/Components/app/PostItem.vue";
import PostModal from "./PostModal.vue";
import { ref, onMounted, watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import AttachmentPreviewModal from "./AttachmentPreviewModal.vue";
import axiosClient from "@/axiosClient";
defineProps({
    posts: Array,
});
const authUser = usePage().props.auth.user;
const page = usePage();
const showEditModal = ref(false);
const showAttachmentModal = ref(false);
const editPost = ref({});
const previewAttachmentPost = ref({});
const loadMoreInterset = ref(null);
const allPost = ref({
    data: [],
    next: null,
});

watch(
    () => page.props.posts,
    () => {
        if (page.props.posts) {
            allPost.value = {
                data: page.props.posts,
                next: page.props.posts.links?.next,
            };
        }
    },
    { deep: true, immediate: true }
);
console.log(page.props.posts)
// watch(() => page.props.posts, () => {
//     if (page.props.posts) {
//         allPosts.value = {
//             data: page.props.posts.data,
//             next: page.props.posts.links?.next
//         }
//     }
// }, {deep: true, immediate: true})
const openEditModal = (post) => {
    editPost.value = post;
    showEditModal.value = true;
};
const openAttachmentPreviewModal = (post, index) => {
    previewAttachmentPost.value = {
        post,
        index,
    };
    showAttachmentModal.value = true;
};
const onModalHide = () => {
    editPost.value = {
        id: null,
        body: "",
        user: authUser,
    };
};

function loadMore() {
   
    if (!allPost.value.next) {
        return;
    }
    axiosClient.get(allPost.value.next).then(({data}) => {
      
        allPost.value.data= [...allPost.value.data,...data.data]
        allPost.value.next = data.links?.next;
    });
}
onMounted(() => {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => entry.isIntersecting && loadMore());
        },
        { rootMargin: "-255px 0px 0px 0px" }
    );
    observer.observe(loadMoreInterset.value);
});
</script>
<template>
    <div class="overflow-auto h-full flex-1 flex flex-col">
        <PostItem
            v-for="post of allPost.data"
            :key="post.id"
            :post="post"
            @editClick="openEditModal"
            @attachmentClick="openAttachmentPreviewModal"
        />
        <div ref="loadMoreInterset"></div>
        <PostModal
            :post="editPost"
            v-model="showEditModal"
            @hide="onModalHide"
        />
        <AttachmentPreviewModal
            :attachments="previewAttachmentPost.post?.attachments || []"
            v-model:index="previewAttachmentPost.index"
            v-model="showAttachmentModal"
        />
    </div>
</template>

<style scoped></style>
