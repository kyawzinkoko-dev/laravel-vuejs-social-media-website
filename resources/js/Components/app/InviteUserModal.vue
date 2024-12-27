<template>
    <Teleport to="body">
        <TransitionRoot appear :show="show" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-50">
                <TransitionChild
                    as="template"
                    enter="duration-300 ease-out"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="duration-200 ease-in"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-black/25" />
                </TransitionChild>
                <div class="fixed inset-0 overflow-y-auto">
                    <div
                        class="flex min-h-full items-center justify-center text-center"
                    >
                        <TransitionChild
                            as="template"
                            enter="duration-300 ease-out"
                            enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100"
                            leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100"
                            leave-to="opacity-0 scale-95"
                        >
                            <DialogPanel
                                class="w-full max-w-md transform overflow-hidden rounded bg-white dark:text-gray-300 dark:bg-gray-800 text-left align-middle shadow-xl transition-all"
                            >
                                <DialogTitle
                                    as="h3"
                                    class="bg-gray-200 font-medium leading-6 text-gray-900 dark:bg-gray-700 dark:text-gray-300 py-3 px-4 flex items-center justify-between"
                                >
                                    Invite User
                                    <button
                                        @click="closeModal"
                                        class="rounded-full p-2 hover:bg-gray-400 dark:hover:bg-gray-800"
                                    >
                                        <XMarkIcon class="w-4 h-4" />
                                    </button>
                                </DialogTitle>
                                <div class="p-4">
                                    <div class="flex flex-col">
                                        <label>Username or Email</label>
                                        <TextInput
                                            class="w-full block mt-1"
                                            :class="[page.props.errors.email ? 'border-red-500 focus:border-red-500 focus:ring-red-500   hover:border-red-500' :'']"
                                            v-model="form.email"
                                            type="text"
                                            required
                                            autofocus
                                        />
                                        <span class="text-red-500 text-sm">{{ page.props.errors.email }}</span>
                                    </div>
                                    <div
                                        class="flex justify-end py-2 px-3 gap-2"
                                    >
                                        <button
                                        @click="closeModal"
                                            class="flex items-center bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-900 transition-colors py-2 px-3 rounded gap-1 text-sm"
                                        >
                                            <XMarkIcon class="h4 w-4" /> Cancel
                                        </button>
                                        <button
                                        @click="invite"
                                            class="flex items-center py-2 px-3 bg-indigo-500 hover:bg-indigo-400 rounded gap-1 text-white text-sm"
                                        >
                                            <BookmarkIcon class="w-4 h-4" />
                                            Submit
                                        </button>
                                    </div>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </Teleport>
</template>

<script setup>
import {
    TransitionRoot,
    TransitionChild,
    DialogTitle,
    Dialog,
    DialogPanel,
} from "@headlessui/vue";
import { BookmarkIcon, XMarkIcon } from "@heroicons/vue/24/outline";
import { useForm, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import TextInput from "../TextInput.vue";


const props = defineProps({
    modelValue: {
        type: Boolean,
    },
});
const page=usePage();
const emits = defineEmits(["update:modelValue"]);

const show = computed({
    get: () => props.modelValue,
    set: (value) => emits("update:modelValue", value),
});

const closeModal = () => {
    show.value = false;
};
const form = useForm({
    email: "",
});

function invite(){
    form.post(route("group.inviteUser",page.props.group.slug), {
        onSuccess: (res) => {
            console.log(res);
        },
        onError: (res) => {
            console.log(res);
        },
    });
};
</script>

<style lang="scss" scoped></style>
