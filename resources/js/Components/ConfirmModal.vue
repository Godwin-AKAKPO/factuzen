<template>
  <TransitionRoot appear :show="show" as="template">
    <Dialog as="div" @close="$emit('close')" class="relative z-50">
      <TransitionChild
        as="template"
        enter="duration-300 ease-out"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="duration-200 ease-in"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black bg-opacity-25" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
          <TransitionChild
            as="template"
            enter="duration-300 ease-out"
            enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95"
          >
            <DialogPanel class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
              <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900 flex items-center">
                <ExclamationTriangleIcon class="w-6 h-6 text-red-500 mr-3" />
                {{ title }}
              </DialogTitle>
              
              <div class="mt-3">
                <p class="text-sm text-gray-500">{{ message }}</p>
              </div>

              <div class="mt-6 flex justify-end space-x-3">
                <button
                  type="button"
                  @click="$emit('close')"
                  class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                  Annuler
                </button>
                <button
                  type="button"
                  @click="$emit('confirm')"
                  :class="[
                    'inline-flex justify-center rounded-md border border-transparent px-4 py-2 text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors',
                    confirmClass || 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
                  ]"
                >
                  {{ confirmText || 'Confirmer' }}
                </button>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import {
  Dialog,
  DialogPanel,
  DialogTitle,
  TransitionChild,
  TransitionRoot,
} from '@headlessui/vue'
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline'

defineProps({
  show: Boolean,
  title: String,
  message: String,
  confirmText: String,
  confirmClass: String,
})

defineEmits(['close', 'confirm'])
</script>