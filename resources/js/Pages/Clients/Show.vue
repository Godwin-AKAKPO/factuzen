<template>
  <TransitionRoot :show="show" as="template">
    <Dialog as="div" class="relative z-50" @close="$emit('close')">
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
            <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl">
              <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <!-- Header -->
                <div class="mb-6 flex justify-between items-start">
                  <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ client.name }}</h1>
                    <p v-if="client.company" class="text-lg text-gray-600">{{ client.company }}</p>
                    <p v-else class="text-gray-600">Particulier</p>
                  </div>
                  <div class="flex space-x-3">
                    <button 
                      @click="$emit('edit', client)"
                      class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center"
                    >
                      <PencilIcon class="w-5 h-5 mr-2" />
                      Modifier
                    </button>
                    <button 
                      @click="$emit('delete', client)"
                      class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center"
                    >
                      <TrashIcon class="w-5 h-5 mr-2" />
                      Supprimer
                    </button>
                  </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                  <!-- Informations du client -->
                  <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden">
                      <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                          <UserIcon class="w-6 h-6 mr-2 text-blue-600" />
                          Informations du client
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                          <!-- Nom complet -->
                          <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                              Nom complet
                            </label>
                            <p class="text-gray-900 font-medium">{{ client.name }}</p>
                          </div>
                          
                          <!-- Entreprise -->
                          <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                              Entreprise
                            </label>
                            <p class="text-gray-900">
                              {{ client.company || 'Particulier' }}
                            </p>
                          </div>
                          
                          <!-- Email -->
                          <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                              Email
                            </label>
                            <a 
                              :href="`mailto:${client.email}`"
                              class="text-blue-600 hover:text-blue-800 font-medium"
                            >
                              {{ client.email }}
                            </a>
                          </div>
                          
                          <!-- Téléphone -->
                          <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                              Téléphone
                            </label>
                            <p v-if="client.phone" class="text-gray-900">
                              <a 
                                :href="`tel:${client.phone}`"
                                class="text-blue-600 hover:text-blue-800 font-medium"
                              >
                                {{ client.phone }}
                              </a>
                            </p>
                            <p v-else class="text-gray-500 italic">Non renseigné</p>
                          </div>
                        </div>
                        
                        <!-- Adresse -->
                        <div v-if="client.address" class="mt-6">
                          <label class="block text-sm font-medium text-gray-700 mb-1">
                            Adresse
                          </label>
                          <p class="text-gray-900 whitespace-pre-line">{{ client.address }}</p>
                        </div>
                        
                        <!-- Date de création -->
                        <div class="mt-6 pt-4 border-t">
                          <div class="flex justify-between text-sm text-gray-500">
                            <span>Client depuis le {{ formatDate(client.created_at) }}</span>
                            <span v-if="client.updated_at !== client.created_at">
                              Modifié le {{ formatDate(client.updated_at) }}
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Statistiques et factures -->
                  <div class="space-y-6">
                    <!-- Statistiques -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                      <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <ChartBarIcon class="w-6 h-6 mr-2 text-blue-600" />
                        Statistiques
                      </h3>
                      
                      <div class="space-y-4">
                        <div class="flex justify-between items-center">
                          <span class="text-gray-600">Nombre de factures</span>
                          <span class="inline-flex px-2 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ client.invoices ? client.invoices.length : 0 }}
                          </span>
                        </div>
                        
                        <!-- Actions rapides -->
                        <div class="pt-4 border-t">
                          <Link 
                            :href="route('invoices.create', { client: client.id })"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center justify-center"
                          >
                            <DocumentPlusIcon class="w-5 h-5 mr-2" />
                            Nouvelle facture
                          </Link>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Factures récentes -->
                    <div v-if="client.invoices && client.invoices.length > 0" class="bg-gray-50 p-4 rounded-lg">
                      <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <DocumentTextIcon class="w-6 h-6 mr-2 text-blue-600" />
                        Factures récentes
                      </h3>
                      
                      <div class="space-y-3">
                        <div 
                          v-for="invoice in client.invoices.slice(0, 5)" 
                          :key="invoice.id"
                          class="flex justify-between items-center p-3 bg-white rounded-lg"
                        >
                          <div>
                            <p class="font-medium text-sm">{{ invoice.reference }}</p>
                            <p class="text-xs text-gray-500">{{ formatDate(invoice.created_at) }}</p>
                          </div>
                          <span 
                            class="px-2 py-1 text-xs font-semibold rounded-full"
                            :class="getStatusClass(invoice.status)"
                          >
                            {{ getStatusLabel(invoice.status) }}
                          </span>
                        </div>
                        
                        <div v-if="client.invoices.length > 5" class="text-center pt-2">
                          <Link 
                            :href="route('invoices.index', { client: client.id })"
                            class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                          >
                            Voir toutes les factures ({{ client.invoices.length }})
                          </Link>
                        </div>
                      </div>
                    </div>
                    
                    <!-- État si aucune facture -->
                    <div v-else class="bg-gray-50 p-4 rounded-lg text-center">
                      <DocumentTextIcon class="w-12 h-12 mx-auto text-gray-300 mb-3" />
                      <h3 class="text-sm font-medium text-gray-900 mb-2">Aucune facture</h3>
                      <p class="text-xs text-gray-500 mb-4">Ce client n'a pas encore de factures.</p>
                      <Link 
                        :href="route('invoices.create', { client: client.id })"
                        class="inline-flex items-center px-3 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                      >
                        <DocumentPlusIcon class="w-4 h-4 mr-1" />
                        Créer une facture
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <button
                  type="button"
                  @click="$emit('close')"
                  class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
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
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { Link } from '@inertiajs/vue3'
import { 
  UserIcon, 
  ChartBarIcon, 
  DocumentTextIcon, 
  PencilIcon, 
  TrashIcon,
  DocumentPlusIcon
} from '@heroicons/vue/24/outline'

defineProps({
  show: Boolean,
  client: Object
})

defineEmits(['close', 'edit', 'delete'])

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('fr-FR')
}

function getStatusClass(status) {
  const classes = {
    'draft': 'bg-gray-100 text-gray-800',
    'sent': 'bg-blue-100 text-blue-800',
    'paid': 'bg-green-100 text-green-800',
    'overdue': 'bg-red-100 text-red-800',
    'cancelled': 'bg-gray-100 text-gray-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

function getStatusLabel(status) {
  const labels = {
    'draft': 'Brouillon',
    'sent': 'Envoyée',
    'paid': 'Payée',
    'overdue': 'En retard',
    'cancelled': 'Annulée',
  }
  return labels[status] || status
}
</script>