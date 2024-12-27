<template>
    <div
        class="flex transition-all border-2 shadow hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 border-transparent hover:border-indigo-500 items-center gap-3 bg-white py-2 px-3 rounded-md"
    >
        <Link :href="route('profile', user.username)">
            <img
                :src="user.avatar_url"
                class="rounded-full w-[40px] cursor-pointer hover:border border-gray-500"
            />
        </Link>

        <div class="flex justify-between flex-1">
            <Link
                :href="route('profile', user.username)"
                class="flex items-center"
            >
                <h3 class="font-semibold text-lg hover:underline">
                    {{ user.name }}
                </h3>
            </Link>

            <div class="flex items-center gap-1">
                <button
                    class="py-1 text-xs px-2 rounded bg-emerald-500 hover:bg-emerald-600 text-white"
                    v-if="forApprove"
                    @click.prevent.stop="$emit('approve', user)"
                >
                    Approve
                </button>
                <button
                    class="py-1 px-2 text-xs rounded bg-red-500 hover:bg-red-600 text-white"
                    v-if="forApprove"
                    @click.prevent="$emit('reject', user)"
                >
                    Reject
                </button>
                <div v-if="showRoleDropDown">
                    <select
                        @change="$emit('roleChange', user, $event.target.value)"
                        class="py-1 border-0 ring-1 ring-inset ring-gray-500 rounded-md focus:ring-2 text-gray-900 shadow-sm focus:outline-none focus:ring-indigo-500 focus:ring-inset-2"
                        :disabled="disableRoleDropdown"
                    >
                        <option :selected="user.role === 'admin'" value="admin">
                            Admin
                        </option>
                        <option :selected="user.role === 'user'" value="user">
                            User
                        </option>
                    </select>
                </div>
                <div>
                    <button
                    v-if="showRoleDropDown"
                    @click="$emit('deleteUser',user)"
                        class="py-1 rounded text-white px-3 bg-gray-600 hover:bg-gray-700 disabled:bg-gray-400"
                        :disabled="disableRoleDropdown"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";

 const props = defineProps({
    user: Object,
    forApprove: {
        type: Boolean,
        default: false,
    },
    showRoleDropDown: {
        type: Boolean,
        default: false,
    },
    disableRoleDropdown: {
        type: Boolean,
        default: false,
    },
});
console.log(props.user);

defineEmits(["approve", "reject", "roleChange","deleteUser"]);
</script>

<style lang="scss" scoped></style>
