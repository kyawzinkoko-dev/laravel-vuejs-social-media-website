<template>
    <TextInput
        placeholder="Type to search"
        class="w-full mt-3"
        v-model="searchKeywork"
    />
    <div class=" overflow-auto flex-1 lg:h-full h-[200px] ">
        <div v-if="false" class="text-gray-400 mt-3 flex justify-center">
            You do not follow anyone now
        </div>
        <div v-else class="mt-3">
        <FollowingList title="test" image="https://placehold.co/400"/>
            </div>
    </div>
</template>

<script setup>
import TextInput from "../TextInput.vue";
import { ref } from "vue";
import FollowingList from "./FollowingList.vue";

const searchKeywork = ref("");
</script>

<style lang="scss" scoped></style>
