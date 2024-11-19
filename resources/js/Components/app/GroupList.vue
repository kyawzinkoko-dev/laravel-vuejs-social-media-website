<template>
    <div
        class="bg-white rounded border py-3 px-3 lg:h-full flex flex-col overflow-auto"
    >
        <div class="flex-1 block lg:hidden h-[200px]">
            <Disclosure v-slot="{ open }">
                <DisclosureButton class="w-full">
                    <div class="flex justify-between items-center">
                        <h2
                            class="text-2xl font-bold flex items-center justify-center"
                        >
                            My Groups
                        </h2>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-6 h-6 transition-all"
                            :class="
                                open ? 'rotate-90 transform transition-all' : ''
                            "
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m8.25 4.5 7.5 7.5-7.5 7.5"
                            />
                        </svg>
                    </div>
                </DisclosureButton>
                <DisclosurePanel>
                    <div class="flex justify-end mt-3 items-center">
                        <button
                            @click="openGroupModal"
                            class="text-xs py-1 px-2 bg-indigo-500 text-white rounded"
                        >
                            New Group
                        </button>
                    </div>
                    <GroupListItem :groups="groups" />
                </DisclosurePanel>
            </Disclosure>
        </div>
        <div class="lg:flex flex-col hidden overflow-hidden">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold flex">My Groups</h2>
                <button
                    @click="openGroupModal"
                    class="text-xs py-1 px-2 bg-indigo-500 text-white rounded"
                >
                    New Group
                </button>
            </div>

            <GroupListItem :groups="groups" />
        </div>
        <GroupModal v-model="showCreateGroupModal" @create-group="onCreateGroup" />
    </div>
</template>

<script setup>
import { ref } from "vue";
import GroupListItem from "./GroupListItem.vue";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import GroupModal from "./GroupModal.vue";
import { usePage } from "@inertiajs/vue3";

const props = defineProps({
    groups:{
        type:Array
    }
})
console.log(usePage().props)
const showCreateGroupModal = ref(false);
//shift newly created group to  props group array
function onCreateGroup(group){
    props.groups.unshift(group)
}
const openGroupModal = () => {
    showCreateGroupModal.value = true;
};
</script>

<style lang="scss" scoped></style>
