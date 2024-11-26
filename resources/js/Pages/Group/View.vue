<template>

    <Head title="Group" />
    <AuthenticatedLayout>
        <div class="w-[768px] container mx-auto bg-gray-100 h-full overflow-auto">
            <div v-show="showNotification && success"
                class="py-2 transition-opacity opacity-100 px-3 my-2 text-white rounded-sm bg-emerald-500 text-sm">
                {{ success }}
            </div>
            <div class="relative bg-white group">
                <img :src="coverImageSrc ||
                    group.cover_url ||
                    'https://placehold.co/600x400'
                    " class="h-[200px] object-cover w-full" />
                <div v-if="isCurrentUserAdmin" class="absolute top-2 right-2">
                    <button v-if="!coverImageSrc"
                        class="group-hover:opacity-100 focus:outline-none cursor-pointer opacity-0 gap-1 transition-opacity flex items-center text-xs text-gray-800 bg-gray-50 py-1 px-2">
                        <CameraIcon class="w-4 h-4 cursor-pointer" />

                        Update Cover Image
                        <input @change="onCoverChange" type="file"
                            class="absolute top-0 left-0 right-0 bottom-0 opacity-0 cursor-pointer" />
                    </button>

                    <template v-else>
                        <div class="flex gap-1 whitespace-nowrap">
                            <button @click="cancelCoverImge"
                                class="flex gap-0.5 items-center text-xs hover:bg-gray-200 rounded-sm text-gray-800 bg-gray-50 py-1 px-2">
                                <XMarkIcon class="w-3 h-3" />
                                Cancel
                            </button>
                            <button @click="submitCoverImage(group)"
                                class="flex items-center gap-0.5 bg-gray-700 hover:bg-gray-900 rounded-sm text-gray-100 py-0 px-2 text-xs">
                                <CheckIcon class="w-3 h-3" />Submit
                            </button>
                        </div>
                    </template>
                </div>
                <div class="flex">
                    <div
                        class="relative group/thumbnail ml-[48px] w-[128px] h-[128px] -mt-[64px] flex items justify-center">
                        <img :src="thumbnailImageSc ||
                            group.thumbnail_url ||
                            'https://placehold.co/80x40'" class="rounded-full object-cover h-full w-full" />

                        <button v-if="!thumbnailImageSc"
                            class="absolute left-0 top-0 bottom-0 right-0 opacity-0 bg-black/50 group-hover/thumbnail:opacity-100 p-1 rounded-full text-gray-200 flex items-center justify-center">
                            <CameraIcon class="w-8 h-8" />
                            <input v-if="isCurrentUserAdmin" @change="onthumbnailChange" type="file"
                                class="absolute top-0 left-0 right-0 bottom-0 opacity-0 cursor-pointer" />
                        </button>
                        <template v-else>
                            <div v-if="isCurrentUserAdmin" class="absolute flex gap-1 flex-col top-1 right-0">
                                <button @click="cancelThumbnailImage"
                                    class="w-7 h-7 flex items-center justify-center bg-red-500/80 text-white rounded-full">
                                    <XMarkIcon class="w-3 h-3" />
                                </button>
                                <button @click="submitThumbnailImage(group)"
                                    class="fw-7 h-7 flex items-center justify-center bg-emerald-500/80 text-white rounded-full">
                                    <CheckIcon class="w-5 h-5" />
                                </button>
                            </div>
                        </template>
                    </div>

                    <div class="flex-1 flex justify-between items-center p-4">
                        <h2 class="font-bold text-lg">{{ group.name }}</h2>
                        <PrimaryButton v-if="!authUser"><a :href="route('login')">Login to Join</a></PrimaryButton>
                        <PrimaryButton v-if="authUser && isCurrentUserAdmin" @click="showInviteUserModal">Invite
                        </PrimaryButton>
                        <PrimaryButton v-if="
                            authUser && !group.role && !group.auto_approve
                        " @click="joinGroup()">Request to join</PrimaryButton>
                        <PrimaryButton @click="joinGroup()" v-if="authUser && !group.role && group.auto_approve">Join
                            group
                        </PrimaryButton>
                    </div>
                </div>
            </div>

            <div class="">
                
                <TabGroup>
                   
                    <TabList class="flex bg-white">
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Posts" :selected="selected" />
                        </Tab>
                        <Tab v-slot="{ selected }" as="template" v-if="isCurrentUserJoinGroup || isCurrentUserAdmin">
                            <TabItem text="Users" :selected="selected" />
                        </Tab>
                        <Tab v-slot="{ selected }" v-if="isCurrentUserAdmin" as="template">
                            <TabItem text="Pending Requests" :selected="selected" />
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Photos" :selected="selected" />
                        </Tab>
                        <Tab v-if="isCurrentUserAdmin" v-slot="{ selected }" as="template">
                            <TabItem text="About" :selected="selected" />
                        </Tab>
                    </TabList>

                    <TabPanels class="mt-2">
                        <TabPanel class=" ">
                           <div v-if="posts">
                            <CreatePost :group="group"/>
                            <PostList :posts="posts.data" />
                           </div>
                           <div v-else  class="text-center text-xl  ">You don't have permission to view posts</div>
                        </TabPanel>
                        <TabPanel>
                            <div class="mb-3">
                                <TextInput class="w-full" :model-value="searchKeyword" placeholder="Search User " />
                            </div>
                            <div class="grid gap-3 grid-cols-2">
                                <UserListItem v-for="user of users" :key="user.id" :user="user"
                                    :showRoleDropDown="isCurrentUserAdmin" @roleChange="onRoleChange"
                                    :disable-role-dropdown="group.user_id === user.id" />
                            </div>
                        </TabPanel>
                        <TabPanel v-if="isCurrentUserAdmin" class="bg-white p-3 shadow">
                           
                            <div v-if="pendingUsers.length">
                                <div class="grid gap-3 grid-cols-2">
                                    <UserListItem v-for="user of pendingUsers" :key="user.id" :user="user"
                                        :for-approve="true" @approve="approveRequest" @reject="rejectRequest" />
                                </div>
                            </div>
                            <div v-else class="flex justify-center items-center">
                                <h3>Don't have any request</h3>
                            </div>
                       
                        </TabPanel>
                        <TabPanel class="bg-white p-3 shadow">
                            Photos
                        </TabPanel>
                        <TabPanel class="bg-white p-3 shadow">
                            <div class="m-4">
                                <GroupForm :form="groupForm" />
                                <div class="flex justify-end my-3">
                                    <div class="flex gap-2 items-center">

                                        <button type="button"
                                            class="bg-indigo-600 w-full flex items-center gap-1 justify-center hover:bg-indigo-500 text-sm font-semibold py-2 px-3 text-white rounded transition-colors shadow-sm focus-visible:outline"
                                            @click="updateGroup()">
                                            <BookmarkIcon class="h-4 w-4" />
                                            Update
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </TabPanel>
                    </TabPanels>
                </TabGroup>
            </div>
        </div>
        <InviteUserModal v-model="openInviteUserModal" />
    </AuthenticatedLayout>
</template>

<script setup>
import { usePage, Head, router, useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { Tab, TabGroup, TabList, TabPanel, TabPanels } from "@headlessui/vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { CameraIcon, XMarkIcon, CheckIcon } from "@heroicons/vue/24/outline";
import { BookmarkIcon } from "@heroicons/vue/24/outline";
import PostList from "@/Components/app/PostList.vue";
import TabItem from "../Profile/Partials/TabItem.vue";
import InviteUserModal from "@/Components/app/InviteUserModal.vue";
import UserListItem from "@/Components/app/UserListItem.vue";
import TextInput from "@/Components/TextInput.vue";
import GroupForm from "@/Components/app/GroupForm.vue";
import CreatePost from "@/Components/app/CreatePost.vue";

const props = defineProps({
    group: {
        type: Object,
    },
    posts: {
        type: Object,
        default:null
    },
    success: {
        type: String,
    },
    users: {
        type: Array,
    },
    pendingUsers: {
        type: Array,
    },
});

const thumbnailImageSc = ref("");
const coverImageSrc = ref("");
let showNotification = ref(true);
const openInviteUserModal = ref(false);
const authUser = usePage().props.auth.user;
const searchKeyword = ref("");
const isCurrentUserAdmin = computed(() => props.group.role === "admin");
const isCurrentUserJoinGroup = computed(
    () => props.group.role && props.group.status === "approved"
);
const imageFile = useForm({
    cover: null,
    thumbnail: null,
});
const groupForm = useForm({
    name: usePage().props.group.name,
    auto_approve: !!parseInt(usePage().props.group.auto_approve),
    about: usePage().props.group.about || ''
})

const showInviteUserModal = () => {
    openInviteUserModal.value = true;
};



const updateGroup = () => {
    groupForm.put(route('group.update', props.group.slug), { preserveScroll: true })
}

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

const onthumbnailChange = (e) => {
    imageFile.thumbnail = e.target.files[0];
    if (imageFile.thumbnail) {
        const reader = new FileReader();
        reader.onload = () => {
            thumbnailImageSc.value = reader.result;
        };
        reader.readAsDataURL(imageFile.thumbnail);
    }
};
const cancelCoverImge = () => {
    imageFile.cover = null;
    coverImageSrc.value = null;
};
const cancelThumbnailImage = () => {
    imageFile.thumbnail = null;
    thumbnailImageSc.value = null;
    console.log("thumbnail iamge canceled ");
};
const submitThumbnailImage = (group) => {
    console.log(imageFile);
    imageFile.post(route("group.updateImage", props.group.slug), {
        preserveScroll: true,
        onSuccess: () => {
            cancelThumbnailImage();
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
const submitCoverImage = (group) => {
    console.log(imageFile);

    imageFile.post(route("group.updateImage", props.group.slug), {
        preserveScroll: true,
        onSuccess: (s) => {
            console.log(s);
            cancelCoverImge();
            setTimeout(() => {
                showNotification.value = false;
            }, 3000);
        },
        onError: (e) => {
            console.log(e);
            setTimeout(() => {
                showNotification.value = false;
            }, 3000);
        },
    });
};

//approve group request
function approveRequest(user) {
    const form = useForm({
        user_id: user.id,
        action: "approve",
    });
    form.post(route("group.approveRequest", props.group.slug), {
        preserveScroll: true,
    });
}
//reject group request
function rejectRequest(user) {
    const form = useForm({
        user_id: user.id,
        action: "reject",
    });
    form.post(route("group.approveRequest", props.group.slug), {
        preserveScroll: true,
    });
}
//join to group
function joinGroup() {
    const form = useForm({});
    form.post(route("group.joinGroup", props.group.slug), {
        preserveScroll: true,
    });
}

//change user role in group
function onRoleChange(user, role) {
    const form = useForm({
        user_id: user.id,
        role,
    });
    console.log(user);
    form.post(route("group.changeRole", props.group.slug), {
        preserveScroll: true,
    });
}
</script>
<style lang="scss" scoped></style>
