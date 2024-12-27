<template>
    <Teleport to="body">
        <TransitionRoot appear :show="show" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-50">
                <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0"
                    enter-to="opacity-100" leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-black/25 " />
                </TransitionChild>
                <div class="fixed inset-0 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center text-center">
                        <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100" leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100" leave-to="opacity-0 scale-95">
                            <DialogPanel
                                class="w-full max-w-md transform overflow-hidden rounded bg-white dark:bg-gray-800 text-gray-300 text-left align-middle shadow-xl transition-all">
                                <DialogTitle as="h3"
                                    class="bg-gray-200 dark:bg-gray-700 font-medium leading-6 text-gray-900 dark:text-gray-300 py-3 px-4 flex items-center justify-between">
                                    Create new group
                                    <button @click="closeModal" class="rounded-full p-2 hover:bg-gray-400">
                                        <XMarkIcon class="w-4 h-4" />
                                    </button>
                                </DialogTitle>
                                <div class="m-4">
                                    <GroupForm :form="form" />
                                    <div class="flex justify-end my-3">
                                        <div class="flex gap-2 items-center">
                                            <button type="button"
                                                class="bg-gray-200 text-sm hover:bg-gray-300 dark:bg-gray-900 dark:hover:bg-gray-700 transition-colors py-2 px-2 rounded"
                                                @click="closeModal">
                                                Cancel
                                            </button>
                                            <button type="button"
                                                class="bg-indigo-600 w-full flex items-center gap-1 justify-center hover:bg-indigo-500 text-sm font-semibold py-2 px-3 text-white rounded transition-colors shadow-sm focus-visible:outline"
                                                @click="submit">
                                                <BookmarkIcon class="h-4 w-4" />
                                                Submit
                                            </button>
                                        </div>
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
import { XMarkIcon, BookmarkIcon } from "@heroicons/vue/24/outline";
import { useForm } from "@inertiajs/vue3";
import { computed } from "vue";
import TextInput from "../TextInput.vue";
import Checkbox from "../Checkbox.vue";
import axiosClient from "@/axiosClient";
import GroupForm from "./GroupForm.vue";
const props = defineProps({
    modelValue: Boolean,
});
const form = useForm({
    name: "",
    auto_approve: true,
    about: "",
});

const emits = defineEmits(["update:modelValue", "hide", 'createGroup']);

const show = computed({
    get: () => props.modelValue,
    set: (value) => emits("update:modelValue", value),
});

const closeModal = () => {
    show.value = false;
};

function submit() {
    axiosClient.post(route("group.create"), form)
        .then(({ data }) => {
            show.value = false;

            console.log(data)
            emits('createGroup', data)

        })
        .catch(e => console.log(e))

}
</script>

<style lang="scss" scoped></style>
