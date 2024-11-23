<script setup>
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import PostModal from "./PostModal.vue";

const props = defineProps({
    group: { type: Object, default: null },
});
const showModal = ref(false);
const authUser = usePage().props.auth.user;
const newPost = ref({
    id: null,
    body: "",
    user: authUser,
    group_id:null
});
function showCreatePostModal() {
    showModal.value = true;
}
</script>

<template>
    <div class="py-6 bg-white px-3 mb-5 rounded cursor-pointer">
        <div
            @click="showCreatePostModal"
            placeholder="Create New Post"
            class="py-2 px-3 border-2 border-gray-300 text-gray-500 rounded text-lg focus:ring-violet-500 w-full mb-4"
        >
            Click to create post
        </div>
        <PostModal :post="newPost" :group="group" v-model="showModal" />
    </div>
</template>

<style scoped></style>
