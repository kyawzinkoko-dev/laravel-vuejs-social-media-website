<template>
    <Head title="Profile" />
    <AuthenticatedLayout>
        <div
            class="w-[768px] container mx-auto bg-gray-100 dark:bg-gray-700 dark:text-gray-400 text-black h-full overflow-auto"
        >
            <div
                v-show="showNotification && success"
                class="py-2 transition-opacity opacity-100 px-3 my-2 text-white rounded-sm bg-emerald-500 text-sm"
            >
                {{ success }}
            </div>
            <div
                v-if="showNotification && errors.cover"
                class="py-2 transition-opacity opacity-100 px-3 my-2 text-white rounded-sm bg-red-500 text-sm"
            >
                {{ errors.cover }}
            </div>
            <div class="relative bg-white dark:bg-gray-800 dark:text-gray-300 group">
                <img
                    :src="
                        coverImageSrc ||
                        user.cover_url ||
                        '/img/default_cover.webp'
                    "
                    class="h-[200px] object-cover w-full"
                />
                <div class="absolute top-2 right-2">
                    <button
                        v-if="!coverImageSrc"
                        class="group-hover:opacity-100 focus:outline-none cursor-pointer opacity-0 gap-1 transition-opacity flex items-center text-xs text-gray-800 bg-gray-50 py-1 px-2"
                    >
                        <CameraIcon class="w-4 h-4 cursor-pointer" />

                        Update Cover Image
                        <input
                            @change="onCoverChange"
                            type="file"
                            class="absolute top-0 left-0 right-0 bottom-0 opacity-0 cursor-pointer"
                        />
                    </button>
                    <template v-else>
                        <div class="flex gap-1 whitespace-nowrap">
                            <button
                                @click="cancelCoverImge"
                                class="flex gap-0.5 items-center text-xs hover:bg-gray-200 rounded-sm text-gray-800 bg-gray-50 py-1 px-2"
                            >
                                <XMarkIcon class="w-3 h-3" />
                                Cancel
                            </button>
                            <button
                                @click="submitCoverImage"
                                class="flex items-center gap-0.5 bg-gray-700 hover:bg-gray-900 rounded-sm text-gray-100 py-0 px-2 text-xs"
                            >
                                <CheckIcon class="w-3 h-3" />Submit
                            </button>
                        </div>
                    </template>
                </div>
                <div class="flex">
                    <div
                        class="relative group/avatar ml-[48px] w-[128px] h-[128px] -mt-[64px] flex items justify-center"
                    >
                        <img
                            :src="
                                avatarImageSc ||
                                user.avatar_url ||
                                '/img/avatar.png'
                            "
                            class="rounded-full object-cover w-full h-full"
                        />

                        <button
                            v-if="!avatarImageSc"
                            class="absolute left-0 top-0 bottom-0 right-0 opacity-0 bg-black/50 group-hover/avatar:opacity-100 p-1 rounded-full text-gray-200 flex items-center justify-center"
                        >
                            <CameraIcon class="w-8 h-8" />
                            <input
                                @change="onAvatarChange"
                                type="file"
                                class="absolute top-0 left-0 right-0 bottom-0 opacity-0 cursor-pointer"
                            />
                        </button>
                        <template v-else>
                            <div
                                class="absolute flex gap-1 flex-col top-1 right-0"
                            >
                                <button
                                    @click="cancelAvataImage"
                                    class="w-7 h-7 flex items-center justify-center bg-red-500/80 text-white rounded-full"
                                >
                                    <XMarkIcon class="w-3 h-3" />
                                </button>
                                <button
                                    @click="submitAvatarImage"
                                    class="fw-7 h-7 flex items-center justify-center bg-emerald-500/80 text-white rounded-full"
                                >
                                    <CheckIcon class="w-5 h-5" />
                                </button>
                            </div>
                        </template>
                    </div>

                    <div class="flex-1 flex justify-between items-center p-4">
                        <div class="">
                            <h2 class="font-bold text-lg">{{ user.name }}</h2>
                            <h3 class="text-xs text-gray-500">
                                {{ followerCount }} followers
                            </h3>
                        </div>

                        <PrimaryButton v-if="isMyProfile">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="w-4 h-4 mr-2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"
                                />
                            </svg>

                            Edit Profile</PrimaryButton
                        >
                        <template v-else>
                            <PrimaryButton
                                v-if="!isCurrentUserFollower"
                                @click="followUser"
                            >
                                Follow user
                            </PrimaryButton>
                            <DangerButton v-else @click="followUser">
                                Unfollow user
                            </DangerButton>
                        </template>
                    </div>
                </div>
            </div>

            <div class="">
                <TabGroup>
                    <TabList class="flex bg-white dark:bg-gray-800 dark:text-gray-300">
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Posts" :selected="selected" />
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Followers" :selected="selected" />
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Following" :selected="selected" />
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Photos" :selected="selected" />
                        </Tab>
                        <Tab
                            v-if="isMyProfile"
                            v-slot="{ selected }"
                            as="template"
                        >
                            <TabItem text="My Profile" :selected="selected" />
                        </Tab>
                    </TabList>

                    <TabPanels class="mt-2">
                        <TabPanel class="">
                            <CreatePost />
                            <PostList :posts="posts.data" />
                        </TabPanel>
                        <TabPanel class="">
                            <TextInput
                                class="w-full mb-3"
                                placeholder="Search user"
                                v-model="searchFollowersKeyword"
                            />
                            <div
                                v-if="followers.length"
                                class="grid grid-cols-2 gap-3"
                            >
                                <UserListItem
                                    class="rounded-lg shadow"
                                    v-for="follower of followers"
                                    :user="follower"
                                    v-model="searchFollowersKeyword"
                                />
                            </div>
                            <div v-else class="py-4 text-center text-gray-900 dark:text-gray-300">
                                User doesn't have any follower
                            </div>
                        </TabPanel>
                        <TabPanel class="">
                            <TextInput
                                class="w-full mb-3"
                                placeholder="Search user"
                                v-model="searchFollowingsKeyword"
                            />
                            <div
                                v-if="followings.length"
                                class="grid grid-cols-2 gap-3"
                            >
                                <UserListItem
                                    class="rounded-lg shadow-sm"
                                    v-for="following of followings"
                                    :user="following"
                                />
                            </div>
                            <div v-else class="py-4 text-center text-gray-900 dark:text-gray-300">
                                User doesn't have any following
                            </div>
                        </TabPanel>
                        <TabPanel class="bg-white p-3 shadow">
                            <TabPhotos :photos="photos"/>
                        </TabPanel>
                        <TabPanel class="" v-if="isMyProfile">
                            <Edit
                                :status="status"
                                :must-verify-email="mustVerifyEmail"
                            />
                        </TabPanel>
                    </TabPanels>
                </TabGroup>
            </div>
        </div>
    </AuthenticatedLayout>
   
</template>

<script setup>
import { usePage, Head, useForm } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { Tab, TabGroup, TabList, TabPanel, TabPanels } from "@headlessui/vue";
import TabItem from "./Partials/TabItem.vue";
import Edit from "./Edit.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { CameraIcon, XMarkIcon, CheckIcon } from "@heroicons/vue/24/outline";
import DangerButton from "@/Components/DangerButton.vue";
import CreatePost from "@/Components/app/CreatePost.vue";
import PostList from "@/Components/app/PostList.vue";
import UserListItem from "@/Components/app/UserListItem.vue";
import TextInput from "@/Components/TextInput.vue";
import TabPhotos from "./TabPhotos.vue";
import AttachmentPreviewModal from "@/Components/app/AttachmentPreviewModal.vue";

const props = defineProps({
    errors: Object,
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    isCurrentUserFollower: {
        type: Boolean,
    },
    user: {
        type: Object,
    },
    followerCount: Number,
    followings: Array,
    followers: Array,
    posts: Object,
    success: {
        type: String,
    },
    photos:Array
});


const authUser = usePage().props.auth.user;
const coverImageSrc = ref("");
const avatarImageSc = ref("");
const searchFollowingsKeyword = ref("");
const searchFollowersKeyword = ref("");
const isMyProfile = computed(() => authUser && authUser.id === props.user.id);

const imageFile = useForm({
    cover: null,
    avatar: null,
});
let showNotification = ref(true);
const onCoverChange = (e) => {
    imageFile.cover = e.target.files[0];
    if (imageFile.cover) {
        const reader = new FileReader();
        reader.onload = () => {
            coverImageSrc.value = reader.result;
        };
        reader.readAsDataURL(imageFile.cover);
    }
};

const onAvatarChange = (e) => {
    imageFile.avatar = e.target.files[0];
    if (imageFile.avatar) {
        const reader = new FileReader();
        reader.onload = () => {
            avatarImageSc.value = reader.result;
        };
        reader.readAsDataURL(imageFile.avatar);
    }
};
const cancelCoverImge = () => {
    imageFile.cover = null;
    coverImageSrc.value = null;
    console.log("cover image canceled");
};
const cancelAvataImage = () => {
    imageFile.avatar = null;
    avatarImageSc.value = null;
    console.log("avatar iamge canceled ");
};
const submitAvatarImage = () => {
    console.log("avatar submited");
    imageFile.post(route("profile.updateImage"), {
        preserveScroll: true,
        onSuccess: () => {
            cancelAvataImage();
            setTimeout(() => {
                showNotification.value = false;
            }, 3000);
        },
        onError: () => {
            setTimeout(() => {
                showNotification.value = false;
            }, 3000);
        },
    });
    console.log(showNotification);
};
const submitCoverImage = () => {
    console.log("cover submitted");
    imageFile.post(route("profile.updateImage"), {
        preserveScroll: true,
        onSuccess: () => {
            cancelCoverImge();

            setTimeout(() => {
                showNotification.value = false;
            }, 3000);
        },
        onError: () => {
            setTimeout(() => {
                showNotification.value = false;
            }, 3000);
        },
    });
};

const followUser = () => {
    const form = useForm({
        follow: !props.isCurrentUserFollower,
    });
    form.post(route("user.follow", props.user), { preserveScroll: true });
};


</script>
<style lang="scss" scoped></style>
