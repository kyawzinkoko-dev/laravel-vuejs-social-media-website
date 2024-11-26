<template>
    <div>
        <div class="flex gap-2 mb-3">
            <a href="#">
                <img
                    :src="post.user.avatar_url"
                    class="rounded-full w-12 border-2 hover:border-blue-500"
                />
            </a>
            <div class="flex flex-1 ">
                <InputTextarea
                    v-model="newComment"
                    :placeholder="placeholder"
                    rows="1"
                    class="w-full overflow-auto rounded-r-none resize-none h-[160px] max-h-[160px]"
                ></InputTextarea>
                <IndigoButton
                    @click.prevent="createComment"
                    class="max-w-[90px] rounded-l-none"
                    >Submit</IndigoButton
                >
            </div>
        </div>
        <div v-for="comment of data.comments" class="mb-4">
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex gap-2 items-start">
                        <a href="#">
                            <img
                                :src="comment.user.avatar_url"
                                class="rounded-full transition-all w-12 border-2 hover:border-blue-500"
                            />
                        </a>
                        <div>
                            <h4 class="font-bold">
                                <a href="#" class="hover:underline">{{
                                    comment.user?.name
                                }}</a>
                            </h4>
                            <small class="text-gray-400 text-xs">
                                {{ comment.format_date }}</small
                            >
                        </div>
                    </div>
                </div>
                <pre>{{  }}</pre>
                    <EditDeleteDropdown
                        :post="post"
                        :comment="comment"
                        @edit="startCommentEdit(comment)"
                        @delete="deleteComment(comment)"
                    />
                
            </div>

            <div class="ml-14">
                <div v-if="editingComment && editingComment.id === comment.id">
                    <InputTextarea
                        class="w-full overflow-auto resize-none max-h-[160px]"
                        v-model="editingComment.comment"
                        :placeholder="placeholder"
                    />
                    <div class="flex gap-2 items-center w-full">
                        <button
                            @click="editingComment = null"
                            class="border border-indigo-500 text-indigo-500 rounded py-2 px-3"
                        >
                            Cancel
                        </button>
                        <IndigoButton @click="updateComment" class=""
                            >Update</IndigoButton
                        >
                    </div>
                </div>
               
                <div v-else>
                    <ReadMoreReadLess
                        :content="comment.comment"
                        content-class="tex-sm  flex flex-1"
                    />
                </div>
                <Disclosure as="div">
                    <div class="flex items-center gap-3 mt-1">
                        <button
                            :class="[
                                comment.current_user_has_reaction
                                    ? 'bg-indigo-50 hover:bg-indigo-100'
                                    : 'hover:bg-indigo-100',
                            ]"
                            class="flex items-center hover:bg-indigo-200 py-0.5 px-1 rounded gap-1 text-indigo-500"
                            @click="sendCommentReaction(comment)"
                        >
                            <HandThumbUpIcon class="w-3 h-3" />
                            {{ comment.num_of_reactions }}
                            {{
                                comment.current_user_has_reaction
                                    ? "unlike"
                                    : "like"
                            }}
                        </button>

                        <DisclosureButton>
                            <button
                                class="flex items-center gap-1 py-0.5 px-1 rounded hover:bg-indigo-100 text-indigo-500"
                            >
                                <ChatBubbleLeftEllipsisIcon class="w-3 h-3" />
                                {{ comment.num_of_comments }}
                                reply
                            </button>
                        </DisclosureButton>
                    </div>
                    <DisclosurePanel class="mt-3">
                        <CommentList
                        :placeholder="'Write a reply'"
                            :post="post"
                            :data="{ comments: comment.comments }"
                            :parent-comment="comment"
                            @create-comment="onCommentCreate"
                            @delete-comment="onDeleteComment"
                        />
                    </DisclosurePanel>
                </Disclosure>
            </div>
        </div>
    </div>
</template>

<script setup>
import EditDeleteDropdown from "./EditDeleteDropdown.vue";
import InputTextarea from "../InputTextarea.vue";
import IndigoButton from "./IndigoButton.vue";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import {
    HandThumbUpIcon,
    ChatBubbleLeftEllipsisIcon,
} from "@heroicons/vue/24/outline";
import ReadMoreReadLess from "../ReadMoreReadLess.vue";
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import axiosClient from "@/axiosClient";

const newComment = ref("");
const editingComment = ref({});
const authUser = usePage().props.auth.user;

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
    data: Object,
    parentComment: {
        type: [Object || null],
        default: null,
    },
    placeholder:{
        type:String,
    }
});
function onCommentCreate(){
    if(props.parentComment){
        props.parentComment.num_of_comments++;
        emit('createComment')
    }
}
function onDeleteComment(){
    if(props.parentComment){
        props.parentComment.num_of_comments--;
        emit('deleteComment')
    }
}
const emit = defineEmits(['createComment','deleteComment'])

//create a new comment
function createComment() {
    axiosClient
        .post(route("comment.create", props.post), {
            comment: newComment.value,
            parent_id: props.parentComment?.id || null,
        })
        .then(({ data }) => {
            emit('createComment', data)
            newComment.value = "";
            if (props.parentComment) {
                props.parentComment.comments.unshift(data);
                props.parentComment.num_of_comments++;
            } else {
                props.data.comments.unshift(data);
               
            }
            props.post.num_of_comments++;
        });
}

//editing of comments
function startCommentEdit(comment) {
    editingComment.value = {
        id: comment.id,
        comment: comment.comment.replace(/<br\s*\/?>/gi, "\n"),
    };
}
function updateComment() {
    axios
        .put(
            route("comment.update", editingComment.value.id),
            editingComment.value
        )
        .then(({ data }) => {
            editingComment.value = {};
            props.data.comments = props.data.comments.map((c) => {
                if (c.id === data.id) {
                    return data;
                }
                return c;
            });
        });
}

//delete comments
function deleteComment(comment) {
    if (window.confirm("Are you sure you want to delete")) {
        axios
            .delete(
                route("comment.delete", {
                    comment,
                })
            )
            .then(({ data }) => {
                emit('deleteComment',data)
                const commentIndex = props.data.comments.find((c)=>c.id===comment.id)
                props.data.comments.splice(commentIndex, 1)
                 if (props.parentComment) {
                     props.parentComment.num_of_comments--;
                 } 
                  if(comment.comments){
                  
                    console.log(props.post.num_of_comments)
                    props.post.num_of_comments-= comment.comments.length+1;
                    console.log(props.post.num_of_comments)
                    console.log(props.post.num_of_comments)
                }
                  else{
                    props.post.num_of_comments--;
                  }
                     
                   
             });
    }
}
//
function sendCommentReaction(comment) {
    axios
        .post(route("comment.reaction", comment.id), {
            reaction: "like",
        })
        .then(({ data }) => {
            comment.current_user_has_reaction = data.current_user_has_reaction;
            comment.num_of_reactions = data.num_of_reactions;
        });
}
</script>

<style lang="scss" scoped></style>
