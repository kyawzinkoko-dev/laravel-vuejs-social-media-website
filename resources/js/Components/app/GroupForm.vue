<template>

    <div class="mb-3">
        <label>Group Name</label>
        <TextInput type="text" v-model="form.name" class="mt-1 block w-full" required autofocus
            :autocomplete="form.name" placeholder="Group Name" />
    </div>

<div class="mb-3">
        <Checkbox :checked="form.auto_approve" />
        Auto approve
    </div>
    <div class="mb-3">
        <label>About Group</label>
        <TextInput type="text" v-model="form.about" class="mt-2 block w-full" required autofocus
            :autocomplete="form.about" placeholder="About group" />
    </div>
</template>

<script setup>

import TextInput from "../TextInput.vue";
import Checkbox from "../Checkbox.vue";

defineProps({
    form: Object
})
</script>

<style lang="scss" scoped></style>