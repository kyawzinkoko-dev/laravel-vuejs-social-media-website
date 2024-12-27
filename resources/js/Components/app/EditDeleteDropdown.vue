<template>
    <div class="relative">
        <Menu>
            <MenuButton
                class="w-8 h-8 flex items-center rounded-full hover:bg-black/10 transition justify-center"
                ><EllipsisVerticalIcon aria-hidden="true" class="w-5 h-5"
            /></MenuButton>
            <transition
                enter-active-class="transition duration-100 ease-out"
                enter-from-class="transform scale-95 opacity-0"
                enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-75 ease-in"
                leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0"
            >
                <MenuItems
                    class="absolute right-0 mt-2 z-30 w-56 origin-top-right divide-y divide-gray-100 rounded-md dark:bg-gray-900 text-gray-900 dark:text-gray-300 bg-white shadow-lg ring-1 ring-black/5 focus:outline-none"
                >
                    <div class="py-2 px-1  text-gray-900">
                        <MenuItem v-slot="{ active }">
                            <a
                                :href="route('post.view', post)"
                                :class="[
                                    active
                                        ? 'bg-indigo-500 text-white'
                                        : 'text-gray-800 dark:text-gray-300',
                                    'flex items-center rounded w-full p-2 text-sm',
                                ]"
                            >
                                <EyeIcon
                                    aria-hidden="true"
                                    class="w-5 h-5 text-black/50 dark:text-gray-300 mr-2"
                                />
                                View Post
                            </a>
                        </MenuItem>
                        <MenuItem v-slot="{ active }">
                            <button
                                @click="copyToClipboard"
                                :class="[
                                    active
                                        ? 'bg-indigo-500 text-white'
                                        : 'text-gray-800 dark:text-gray-300',
                                    'flex items-center rounded w-full p-2 text-sm',
                                ]"
                            >
                                <ClipboardIcon
                                    aria-hidden="true"
                                    class="w-5 h-5 text-black/50 dark:text-gray-300 mr-2"
                                />
                                Copy Post Url
                            </button>
                        </MenuItem>
                        <MenuItem v-if="isEditAllow" v-slot="{ active }">
                            <button
                                @click="$emit('edit')"
                                :class="[
                                    active
                                        ? 'bg-indigo-500 text-white'
                                        : 'text-gray-800 dark:text-gray-300',
                                    'flex items-center rounded w-full p-2 text-sm',
                                ]"
                            >
                                <PencilIcon
                                    aria-hidden="true"
                                    class="w-5 h-5 text-black/50 mr-2 dark:text-gray-300"
                                />
                                Edit
                            </button>
                        </MenuItem>
                        <MenuItem v-if="isPinAllow" v-slot="{ active }">
                            <button
                                @click="$emit('pin')"
                                :class="[
                                    active
                                        ? 'bg-indigo-500 text-white'
                                        : 'text-gray-800 dark:text-gray-300',
                                    'flex items-center rounded w-full p-2 text-sm',
                                ]"
                            >
                             <PinIcon class="dark:text-gray-300"/>   
                                {{ isPinned? 'Unpin' : 'Pin' }}
                            </button>
                        </MenuItem>
                        <MenuItem v-if="isDeleteAllow" v-slot="{ active }">
                            <button
                                @click="$emit('delete')"
                                :class="[
                                    active
                                        ? 'bg-indigo-500 text-white'
                                        : 'text-gray-800 dark:text-gray-300',
                                    'flex items-center rounded w-full p-2 text-sm',
                                ]"
                            >
                                <TrashIcon
                                    aria-hidden="true"
                                    class="w-5 h-5 text-black/50 dark:text-gray-300 mr-2"
                                />
                                Delete
                            </button>
                        </MenuItem>
                    </div>
                </MenuItems>
            </transition>
        </Menu>
    </div>
</template>

<script setup>
import {
    PencilIcon,
    EllipsisVerticalIcon,
    TrashIcon,
    ClipboardIcon,
    EyeIcon,
} from "@heroicons/vue/24/solid";
import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import PinIcon from "./PinIcon.vue";

const props = defineProps({
    post: {
        type: Object,
        default: null,
    },
    comment: {
        type: Object,
        default: null,
    },
});
const authUser = usePage().props.auth.user;
const user = computed(() => props.comment?.user || props.post?.user);
const isEditAllow = computed(() => {
    return user.value.id === authUser.id;
});
const group = usePage().props.group ? usePage().props.group : null;

const isDeleteAllow = computed(() => {
    if (user.value.id === authUser.id) return true;

    if (props.post.user.id === authUser.id) return true;
    return props.post?.group?.role === "admin";
});

const isPinAllow = computed(() => {
    return user.value.id === authUser.id || props.post.group && props.group?.role ==="admin";
});

const isPinned = computed(()=>{
    if(group?.id){
        return group?.pinned_post_id ===props.post.id
    }
    return authUser?.pinned_post_id === props.post.id
})
const copyToClipboard = computed(() => {
    navigator.clipboard
        .writeText(route(`post.view`, props.post))
        .then((d) => {
            console.log(d);
        })
        .catch((e) => console.log(e));
});
defineEmits(["edit", "delete","pin"]);
</script>

<style lang="scss" scoped></style>
