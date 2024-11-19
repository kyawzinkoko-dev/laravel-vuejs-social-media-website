<template>
    <Disclosure v-slot="{ open }">
        <div
            class="ck-content-output"
            :class="contentClass"
            v-if="!open"
            v-html="content?.substring(0, 200)"
        ></div>
        <template v-if="content?.length >= 200">
            <DisclosurePanel>
                <div
                    class="ck-content-output"
                    :class="contentClass"
                    v-html="content"
                />
            </DisclosurePanel>
            <div class="flex justify-end item">
                <DisclosureButton class="">
                    <span class="text-blue-600 hover:underline">{{
                        open ? "Read less" : "Read more"
                    }}</span>
                </DisclosureButton>
            </div>
        </template>
    </Disclosure>
</template>

<script setup>
import { Disclosure, DisclosurePanel, DisclosureButton } from "@headlessui/vue";

const props =   defineProps({
    content: {
        String,
    },
    contentClass: String,
});
</script>

<style lang="scss" scoped></style>
