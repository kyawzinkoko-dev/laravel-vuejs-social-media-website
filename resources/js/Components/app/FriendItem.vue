<template>
    <div
        class="bg-white dark:bg-gray-800 text-gray-900 dark:tex-gray-300 dark:border-gray-500 rounded border py-3 px-3 lg:h-full flex flex-col overflow-auto"
    >
        <div class="flex-1 block lg:hidden">
            <Disclosure v-slot="{ open }">
                <DisclosureButton class="w-full">
                    <div class="flex justify-between items-center">
                        <h2
                            class="text-2xl font-bold flex items-center justify-center text-gray-900 dark:text-gray-300"
                        >
                            My Following
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
                    
                    <FollowingListItem  :followings="followings"/>
                    
                </DisclosurePanel>
            </Disclosure>
        </div>
        <div class="flex-col hidden lg:flex overflow-hidden">
            <h2 class="text-2xl font-bold flex items-center justify-center text-gray-900 dark:text-gray-300">
                My Following
            </h2>
            <FollowingListItem :followings="followings" />
        </div>
    </div>
</template>

<script setup>
import FollowingListItem from "./FollowingListItem.vue";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
defineProps({
    followings:Array
})

</script>

<style lang="scss" scoped></style>
