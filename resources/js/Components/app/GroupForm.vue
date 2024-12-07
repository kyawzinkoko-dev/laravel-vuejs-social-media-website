<template>
    <div class="mb-3">
        <label>Group Name</label>
        <TextInput
            type="text"
            v-model="form.name"
            class="mt-1 block w-full"
            required
            autofocus
            :autocomplete="form.name"
            placeholder="Group Name"
        />
    </div>

    <div class="mb-3">
        <Checkbox :checked="form.auto_approve" />
        Auto approve
    </div>
    <div class="mb-3">
        <label>About Group</label>
        <ckeditor
            :editor="editor"
            v-model="form.about"
            :config="editorConfig"
        ></ckeditor>
    </div>
</template>

<script setup>
import TextInput from "../TextInput.vue";
import Checkbox from "../Checkbox.vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";

import { Ckeditor } from "@ckeditor/ckeditor5-vue";
const editor = ClassicEditor;

const editorConfig = {
    toolbar: [
        "bold",
        "italic",
        "|",
        "bulletedList",
        "numberedList",
        "|",
        "heading",
        "|",
        "outdent",
        "indent",
        "|",
        "link",
        "|",
        "blockQuote",
    ],
};
defineProps({
    form: Object,
});
</script>

<style lang="scss" scoped></style>
