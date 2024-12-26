<script setup>
import PostItem from "@/Components/app/PostItem.vue";
import PostModal from "./PostModal.vue";
import { ref, onMounted, watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import AttachmentPreviewModal from "./AttachmentPreviewModal.vue";
import axiosClient from "@/axiosClient";

const authUser = usePage().props.auth.user;
const page = usePage();
const showEditModal = ref(false);
const showAttachmentModal = ref(false);
const previewAttachmentPost = ref({})
const editPost = ref({});
;
const loadMoreInterset = ref(null);
const allPost = ref({
    data: [],
    next: null,
});
defineProps({
    posts: Array,
});
watch(
    () => page.props.posts,
    () => {
        console.log('watch trigger')
        if (page.props.posts) {
            allPost.value = {
                data: page.props.posts.data,
                next: page.props.posts.links?.next,
            };
        }
    },
    { deep: true, immediate: true }
);

const openEditModal = (post) => {
    editPost.value = post;
    console.log(editPost)
    showEditModal.value = true;
};

const openAttachmentPreviewModal = (post, index) => {
    console.log('here');
    
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
    console.log(allPost)
    if (!allPost.value.next) {
        return;
    }
    axiosClient.get(allPost.value.next).then(({data}) => {
        console.log(data)
        allPost.value.data= [...allPost.value.data,...data.data]
        allPost.value.next = data.links?.next;
    });
}
onMounted(() => {
    console.log('load more triggered')
    const rootMargin = window.innerWidth < 1024 ? "-255px 0px 0px 0px" : "-100px 0px 0px 0px";
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => entry.isIntersecting && loadMore());
        },
        { rootMargin }
    );
    observer.observe(loadMoreInterset.value);
});
</script>
<template>
    <div class="overflow-auto ">
        <PostItem
            v-for="post of allPost.data"
            :key="post.id"
            :post="post"
            @editClick="openEditModal"
            @attachmentClick="openAttachmentPreviewModal"
        />
        <div ref="loadMoreInterset"></div>
        <PostModal
            :group="editPost.group"
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
