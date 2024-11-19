<template>
    <TextInput
                 placeholder="Type to search"
                 class="w-full mt-3"
                 v-model="searchKeywork"
             />
             <div class="overflow-auto flex-1 lg:h-full h-[200px]">
                 <div
                     v-if="false"
                     class="text-gray-400 mt-3 flex justify-center items-center"
                 >
                     You are not join to any group
                 </div>
                 <div v-else v-for="group of groups" class="mt-3">
                     <GroupItem
                         :group="group"
                     />
                    
                 </div>
             </div>
 </template>
 
 <script setup>
 import { ref } from 'vue';
 import GroupItem from './GroupItem.vue';
 import TextInput from '../TextInput.vue';
 
 defineProps({
     groups:{
         type:Array
     }
 })
     const searchKeywork = ref('')
 </script>
 
 <style lang="scss" scoped>
 
 </style>