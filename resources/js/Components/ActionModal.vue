<template>
  <TransitionRoot as="template" :show="show">
    <Dialog as="div" class="relative z-10" @close="$emit('close')">
      <TransitionChild
        as="template"
        enter="ease-out duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in duration-200"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
      </TransitionChild>

      <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <TransitionChild
            as="template"
            enter="ease-out duration-300"
            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to="opacity-100 translate-y-0 sm:scale-100"
            leave="ease-in duration-200"
            leave-from="opacity-100 translate-y-0 sm:scale-100"
            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          >
            <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm sm:p-6">
              <div>
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                  <EllipsisVerticalIcon class="h-6 w-6 text-blue-600" aria-hidden="true" />
                </div>
                <div class="mt-3 text-center sm:mt-5">
                  <DialogTitle as="h3" class="text-base font-semibold leading-6 text-gray-900">
                    Actions disponibles
                  </DialogTitle>
                  <div class="mt-2">
                    <p class="text-sm text-gray-500">
                      {{ invoice?.reference }}
                    </p>
                  </div>
                </div>
              </div>
              
              <div class="mt-5 sm:mt-6">
                <div class="space-y-2">
                  
                  <!-- Télécharger PDF -->
                  <button
                    @click="handleAction('download')"
                    class="w-full flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                  >
                    <DocumentArrowDownIcon class="w-5 h-5 mr-3 text-gray-400" />
                    Télécharger PDF
                  </button>
                  
                  <!-- Envoyer par email -->
                  <button
                    v-if="invoice?.status !== 'sent' && invoice?.status !== 'paid'"
                    @click="handleAction('send')"
                    class="w-full flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                  >
                    <EnvelopeIcon class="w-5 h-5 mr-3 text-gray-400" />
                    Envoyer par email
                  </button>
                  
                  <!-- Marquer comme payée (factures uniquement) -->
                  <button
                    v-if="type === 'invoice' && invoice?.status === 'sent'"
                    @click="handleAction('mark_paid')"
                    class="w-full flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                  >
                    <CheckCircleIcon class="w-5 h-5 mr-3 text-green-500" />
                    Marquer comme payée
                  </button>
                  
                  <!-- Convertir en facture (devis uniquement) -->
                  <button
                    v-if="type === 'quote' && invoice?.status !== 'cancelled'"
                    @click="handleAction('convert')"
                    class="w-full flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                  >
                    <ArrowRightIcon class="w-5 h-5 mr-3 text-blue-500" />
                    Convertir en facture
                  </button>
                  
                  <!-- Dupliquer -->
                  <button
                    @click="handleAction('duplicate')"
                    class="w-full flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                  >
                    <DocumentDuplicateIcon class="w-5 h-5 mr-3 text-gray-400" />
                    Dupliquer
                  </button>
                  
                  <!-- Séparateur -->
                  <div class="border-t border-gray-200 my-2"></div>
                  
                  <!-- Modifier le statut -->
                  <div class="px-4 py-2">
                    <label class="block text-xs font-medium text-gray-700 mb-2">
                      Changer le statut
                    </label>
                    <select
                      v-model="newStatus"
                      @change="handleStatusChange"
                      class="w-full text-sm border border-gray-300 rounded-md px-3 py-1 focus:ring-blue-500 focus:border-blue-500"
                    >
                      <option value="draft">Brouillon</option>
                      <option value="sent">Envoyée</option>
                      <option value="paid">Payée</option>
                      <option value="cancelled">Annulée</option>
                    </select>
                  </div>
                  
                  <!-- Séparateur -->
                  <div class="border-t border-gray-200 my-2"></div>
                  
                  <!-- Supprimer -->
                  <button
                    v-if="canDelete"
                    @click="handleAction('delete')"
                    class="w-full flex items-center px-4 py-2 text-sm text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                  >
                    <TrashIcon class="w-5 h-5 mr-3 text-red-500" />
                    Supprimer
                  </button>
                  
                </div>
                
                <!-- Bouton Fermer -->
                <button
                  type="button"
                  class="mt-4 w-full rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                  @click="$emit('close')"
                >
                  Fermer
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
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import {
  EllipsisVerticalIcon,
  DocumentArrowDownIcon,
  EnvelopeIcon,
  CheckCircleIcon,
  ArrowRightIcon,
  DocumentDuplicateIcon,
  TrashIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  show: Boolean,
  invoice: Object,
  type: String,
})

const emit = defineEmits(['close', 'action'])

// État local
const newStatus = ref('')

// Calculé
const canDelete = computed(() => {
  return props.invoice && !['paid', 'sent'].includes(props.invoice.status)
})

// Watcher pour réinitialiser le statut quand la modal s'ouvre
watch(() => props.show, (newValue) => {
  if (newValue && props.invoice) {
    newStatus.value = props.invoice.status
  }
})

// Méthodes
function handleAction(action) {
  if (action === 'mark_paid') {
    router.put(route('invoices.status', props.invoice.id), {
      status: 'paid'
    }, {
      onSuccess: () => emit('close')
    })
  } else if (action === 'send') {
    router.post(route('invoices.send', props.invoice.id), {}, {
      onSuccess: () => emit('close')
    })
  } else if (action === 'download') {
    window.open(route('invoices.pdf', props.invoice.id), '_blank')
    emit('close')
  } else {
    emit('action', action, props.invoice)
  }
}

function handleStatusChange() {
  if (newStatus.value !== props.invoice.status) {
    router.put(route('invoices.status', props.invoice.id), {
      status: newStatus.value
    }, {
      onSuccess: () => emit('close')
    })
  }
}
</script>