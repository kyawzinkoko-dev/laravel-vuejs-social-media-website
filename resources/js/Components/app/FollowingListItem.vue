<template>
    <TextInput
        placeholder="Type to search"
        class="w-full mt-3"
        v-model="searchKeywork"
    />
    <div class=" overflow-auto flex-1 lg:h-full h-[200px] ">
        <div v-if="!following" class="text-gray-400 mt-3 flex justify-center">
            You do not follow anyone now
        </div>
        <div v-else class="mt-3">
        <FollowingList :title="following.name" :image="following.avatar_url"/>
        
            </div>
    </div>
</template>

<script setup>
import TextInput from "../TextInput.vue";
import { ref } from "vue";
import FollowingList from "./FollowingList.vue";

const props = defineProps({
    following:Object
})
console.log(props.following)
const searchKeywork = ref("");
</script>

<style lang="scss" scoped></style>
