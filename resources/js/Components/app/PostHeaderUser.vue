<template>
    <a href="javascript:void(0)" class="mb-3">
        <div class="flex gap-3 items-center">
            <img
                :src="post.user.avatar_url"
                class="rounded-full w-12 border-2 hover:border-blue-500"
            />
            <div>
                <div class="flex items-center">
                    <a
                        href="javascript:void(0)"
                        class="font-bold mb-1 hover:underline"
                        >{{ post.user.name }}</a
                    >

                    <template v-if="post.group">
                        >
                        <a
                            href="javascript:void(0)"
                            class="font-bold hover:underline"
                            >{{ post.group.name }}</a
                        >
                    </template>
                </div>
                <small v-if="showTime" class="text-gray-400 text-sm">
                    {{ post.created_at }}
                </small>
            </div>
        </div>
    </a>
</template>

<script setup>
defineProps({
    post: {
        type: Object,
        required: true,
    },
    showTime: {
        type: Boolean,
        default: true,
    },
});
</script>

<style lang="scss" scoped></style>
