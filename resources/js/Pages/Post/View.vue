<template>
    <AuthenticatedLayout>
        <div class="p-7 w-[600px] mx-auto h-full overflow-auto">
            <PostItem :post="post" @editClick="openEditModal"
            @attachmentClick="openAttachmentPreviewModal"/>
        </div>
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
    </AuthenticatedLayout>
</template>

<script setup>
import AttachmentPreviewModal from '@/Components/app/AttachmentPreviewModal.vue';
import PostItem from '@/Components/app/PostItem.vue';
import PostModal from '@/Components/app/PostModal.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';



defineProps({
    post:Object
})

const showEditModal = ref(false);
const showAttachmentModal = ref(false);
const editPost = ref({});
const previewAttachmentPost = ref({});
const authUser = usePage().props.auth.user;

const openEditModal = (post) => {
    editPost.value = post;
    console.log(editPost)
    showEditModal.value = true;
};

const openAttachmentPreviewModal = (post, index) => {
    console.log('log');
    
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

</script>

<style lang="scss" scoped>

</style>