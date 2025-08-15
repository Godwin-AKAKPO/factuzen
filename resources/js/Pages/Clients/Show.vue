<template>
  <AuthenticatedLayout>
    <Head :title="`Client - ${client.name}`" />
    
    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6">
          <Link 
            :href="route('clients.index')"
            class="text-blue-600 hover:text-blue-800 text-sm font-medium mb-2 inline-flex items-center"
          >
            <ArrowLeftIcon class="w-4 h-4 mr-1" />
            Retour aux clients
          </Link>
          <div class="flex justify-between items-start">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">{{ client.name }}</h1>
              <p v-if="client.company" class="text-lg text-gray-600">{{ client.company }}</p>
              <p v-else class="text-gray-600">Particulier</p>
            </div>
            <div class="flex space-x-3">
              <Link 
                :href="route('clients.edit', client.id)"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center"
              >
                <PencilIcon class="w-5 h-5 mr-2" />
                Modifier
              </Link>
              <button 
                @click="confirmDelete"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center"
              >
                <TrashIcon class="w-5 h-5 mr-2" />
                Supprimer
              </button>
            </div>
          </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          
          <!-- Informations du client -->
          <div class="lg:col-span-2">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
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
            </div>
            
            <!-- Factures récentes -->
            <div v-if="client.invoices && client.invoices.length > 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                  <DocumentTextIcon class="w-6 h-6 mr-2 text-blue-600" />
                  Factures récentes
                </h3>
                
                <div class="space-y-3">
                  <div 
                    v-for="invoice in client.invoices.slice(0, 5)" 
                    :key="invoice.id"
                    class="flex justify-between items-center p-3 bg-gray-50 rounded-lg"
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
            </div>
            
            <!-- État si aucune facture -->
            <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 text-center">
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
        
      </div>
    </div>
    
    <!-- Modal de confirmation de suppression -->
    <ConfirmModal 
      :show="showDeleteModal" 
      @close="showDeleteModal = false"
      @confirm="deleteClient"
      title="Supprimer le client"
      :message="`Êtes-vous sûr de vouloir supprimer ${client.name} ? Cette action est irréversible.`"
      confirm-text="Supprimer"
      confirm-class="bg-red-600 hover:bg-red-700"
    />
    
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ConfirmModal from '@/Components/ConfirmModal.vue'
import { 
  ArrowLeftIcon,
  PencilIcon,
  TrashIcon,
  UserIcon,
  ChartBarIcon,
  DocumentTextIcon,
  DocumentPlusIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  client: Object,
})

// État local
const showDeleteModal = ref(false)

// Confirmation de suppression
function confirmDelete() {
  showDeleteModal.value = true
}

function deleteClient() {
  router.delete(route('clients.destroy', props.client.id), {
    onSuccess: () => {
      showDeleteModal.value = false
    }
  })
}

// Utilitaires
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