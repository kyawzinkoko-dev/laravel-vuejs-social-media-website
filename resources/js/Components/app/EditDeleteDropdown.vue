<template>
    <div class="relative">
               <Menu>
                   <MenuButton
                       class="w-8 h-8 flex items-center rounded-full hover:bg-black/10 transition justify-center"
                       ><EllipsisVerticalIcon
                           aria-hidden="true"
                           class="w-5 h-5"
                   /></MenuButton>
                   <transition
                       enter-active-class="transition duration-100 ease-out"
                       enter-from-class="transform scale-95 opacity-0"
                       enter-to-class="transform scale-100 opacity-100"
                       leave-active-class="transition duration-75 ease-in"
                       leave-from-class="transform scale-100 opacity-100"
                       leave-to-class="transform scale-95 opacity-0"
                   >
                       <MenuItems
                           class="absolute right-0 mt-2 z-30 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none"
                       >
                           <div class="p-1">
                               <MenuItem v-slot="{ active }">
                                   <button
                                       @click="$emit('edit')"
                                       :class="[
                                           active
                                               ? 'bg-indigo-500 text-white'
                                               : 'text-gray-800',
                                           'flex items-center rounded w-full p-2 text-sm',
                                       ]"
                                   >
                                       <PencilIcon
                                           aria-hidden="true"
                                           class="w-5 h-5 text-indigo-400 mr-2"
                                       />
                                       Edit
                                   </button>
                               </MenuItem>
                               <MenuItem v-slot="{ active }">
                                   <button
                                       @click="$emit('delete')"
                                       :class="[
                                           active
                                               ? 'bg-indigo-500 text-white'
                                               : 'text-gray-800',
                                           'flex items-center rounded w-full p-2 text-sm',
                                       ]"
                                   >
                                       <TrashIcon
                                           aria-hidden="true"
                                           class="w-5 h-5 text-indigo-400 mr-2"
                                       />
                                       Delete
                                   </button>
                               </MenuItem>
                           </div>
                       </MenuItems>
                   </transition>
               </Menu>
           </div>
</template>

<script setup>
import {
   PencilIcon,
   EllipsisVerticalIcon,
   TrashIcon,

} from "@heroicons/vue/24/outline";
import {
   Menu,
   MenuButton,
   MenuItems,
   MenuItem,
} from "@headlessui/vue";

defineEmits(['edit','delete'])
</script>

<style lang="scss" scoped>

</style>