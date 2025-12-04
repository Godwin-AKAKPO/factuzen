<template>
  <AuthenticatedLayout>
    <Head :title="type === 'quote' ? 'Devis' : 'Factures'" />
    
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Header avec onglets et bouton d'ajout -->
        <div class="mb-6">
          <div class="flex justify-between items-center mb-4">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">
                {{ type === 'quote' ? 'Devis' : 'Factures' }}
              </h1>
              <p class="text-gray-600">
                {{ type === 'quote' ? 'Gérez vos devis' : 'Gérez vos factures' }}
              </p>
            </div>
            
            <Link 
              :href="route('invoices.create', { type })"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center"
            >
              <DocumentPlusIcon class="w-5 h-5 mr-2" />
              {{ type === 'quote' ? 'Nouveau devis' : 'Nouvelle facture' }}
            </Link>
          </div>
          
          <!-- Onglets Type -->
          <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
              <Link 
                :href="route('invoices.index', { type: 'invoice' })"
                :class="[
                  'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm',
                  type === 'invoice' 
                    ? 'border-blue-500 text-blue-600' 
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                Factures
              </Link>
              <Link 
                :href="route('invoices.index', { type: 'quote' })"
                :class="[
                  'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm',
                  type === 'quote' 
                    ? 'border-blue-500 text-blue-600' 
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                Devis
              </Link>
            </nav>
          </div>
        </div>
        
        <!-- Filtres et recherche -->
        <div class="mb-6 bg-white p-4 rounded-lg shadow">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            
            <!-- Recherche -->
            <div class="relative">
              <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
              <input
                v-model="searchQuery"
                @input="handleSearch"
                type="text"
                placeholder="Rechercher..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            
            <!-- Filtre par statut -->
            <select
              v-model="statusFilter"
              @change="handleStatusFilter"
              class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">Tous les statuts</option>
              <option value="draft">Brouillons</option>
              <option value="sent">Envoyées</option>
              <option value="paid">Payées</option>
              <option value="overdue">En retard</option>
             
            </select>
            
            <!-- Statistiques -->
            <div class="flex items-center text-sm text-gray-600">
              <span class="font-medium">{{ stats.total }}</span>
              <span class="ml-1">{{ type === 'quote' ? 'devis' : 'factures' }}</span>
            </div>
          </div>
          
          <!-- Badges de filtres rapides -->
          <div class="flex flex-wrap gap-2 mt-4">
            <button
              @click="setStatusFilter('')"
              :class="[
                'px-3 py-1 text-xs rounded-full border transition-colors',
                !statusFilter
                  ? 'bg-blue-100 text-blue-800 border-blue-300'
                  : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'
              ]"
            >
              Tous ({{ stats.total }})
            </button>
            <button
              @click="setStatusFilter('draft')"
              :class="[
                'px-3 py-1 text-xs rounded-full border transition-colors',
                statusFilter === 'draft'
                  ? 'bg-gray-100 text-gray-800 border-gray-300'
                  : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'
              ]"
            >
              Brouillons ({{ stats.draft }})
            </button>
            <button
              @click="setStatusFilter('sent')"
              :class="[
                'px-3 py-1 text-xs rounded-full border transition-colors',
                statusFilter === 'sent'
                  ? 'bg-blue-100 text-blue-800 border-blue-300'
                  : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'
              ]"
            >
              Envoyées ({{ stats.sent }})
            </button>
            <button
              @click="setStatusFilter('paid')"
              :class="[
                'px-3 py-1 text-xs rounded-full border transition-colors',
                statusFilter === 'paid'
                  ? 'bg-green-100 text-green-800 border-green-300'
                  : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'
              ]"
            >
              Payées ({{ stats.paid }})
            </button>
            <button
              @click="setStatusFilter('overdue')"
              :class="[
                'px-3 py-1 text-xs rounded-full border transition-colors',
                statusFilter === 'overdue'
                  ? 'bg-red-100 text-red-800 border-red-300'
                  : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'
              ]"
            >
              En retard ({{ stats.overdue }})
            </button>
          </div>
        </div>
        
        <!-- Tableau des factures -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div v-if="invoices.data.length === 0" class="p-8 text-center">
            <DocumentTextIcon class="w-16 h-16 mx-auto text-gray-300 mb-4" />
            <h3 class="text-lg font-medium text-gray-900 mb-2">
              {{ search ? 'Aucun résultat trouvé' : (type === 'quote' ? 'Aucun devis trouvé' : 'Aucune facture trouvée') }}
            </h3>
            <p class="text-gray-600 mb-4">
              {{ search 
                ? 'Aucune correspondance avec votre recherche.' 
                : (type === 'quote' ? 'Commencez par créer votre premier devis.' : 'Commencez par créer votre première facture.')
              }}
            </p>
            <Link 
              :href="route('invoices.create', { type })"
              class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
            >
              <DocumentPlusIcon class="w-5 h-5 mr-2" />
              {{ type === 'quote' ? 'Créer un devis' : 'Créer une facture' }}
            </Link>
          </div>
          
          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Référence
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Client
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Montant
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Statut
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ type === 'quote' ? 'Créé le' : 'Échéance' }}
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr 
                  v-for="invoice in invoices.data" 
                  :key="invoice.id"
                  class="hover:bg-gray-50"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ invoice.reference }}</div>
                    <div class="text-sm text-gray-500">{{ formatDate(invoice.date) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div>
                      <div class="text-sm font-medium text-gray-900">{{ invoice.client.name }}</div>
                      <div v-if="invoice.client.company" class="text-sm text-gray-500">{{ invoice.client.company }}</div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ formatCurrency(invoice.total_ttc) }}</div>
                    <div class="text-sm text-gray-500">TTC</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                      :class="getStatusClass(invoice.status)"
                    >
                      {{ getStatusLabel(invoice.status) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ type === 'quote' ? formatDate(invoice.created_at) : formatDate(invoice.due_date) }}
                    <div v-if="type === 'invoice' && isOverdue(invoice)" class="text-xs text-red-600 font-medium">
                      En retard
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                      <Link 
                        :href="route('invoices.show', invoice.id, )"
                        class="text-blue-600 hover:text-blue-900 p-1 rounded"
                        title="Voir"
                      >
                        <EyeIcon class="w-5 h-5" />
                      </Link>
                      <Link 
                        v-if="invoice.status !== 'paid'"
                        :href="route('invoices.edit', invoice.id)"
                        class="text-green-600 hover:text-green-900 p-1 rounded"
                        title="Modifier"
                      >
                        <PencilIcon class="w-5 h-5" />
                      </Link>
                      <button 
                        @click="showActionMenu(invoice)"
                        class="text-gray-600 hover:text-gray-900 p-1 rounded"
                        title="Plus d'actions"
                      >
                        <EllipsisVerticalIcon class="w-5 h-5" />
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        
        <!-- Pagination -->
        <div v-if="invoices.links.length > 3" class="mt-6">
          <Pagination :links="invoices.links" />
        </div>
        
      </div>
    </div>
    
    <!-- Modal de menu d'actions -->
    <ActionModal 
      :show="actionModalOpen" 
      @close="actionModalOpen = false"
      :invoice="selectedInvoice"
      :type="type"
      @action="handleAction"
    />
    
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import ActionModal from '@/Components/ActionModal.vue'
import { 
  DocumentPlusIcon,
  DocumentTextIcon,
  MagnifyingGlassIcon,
  EyeIcon,
  PencilIcon,
  EllipsisVerticalIcon
} from '@heroicons/vue/24/outline'
import { debounce } from 'lodash'

const props = defineProps({
  invoices: Object,
  search: String,
  status: String,
  type: String,
  stats: Object,
})

// État local
const searchQuery = ref(props.search || '')
const statusFilter = ref(props.status || '')
const actionModalOpen = ref(false)
const selectedInvoice = ref(null)

// Recherche avec debounce
const handleSearch = debounce(() => {
  router.get(route('invoices.index'), 
    { 
      search: searchQuery.value,
      status: statusFilter.value,
      type: props.type
    },
    { 
      preserveState: true,
      replace: true 
    }
  )
}, 300)

// Filtre par statut
function handleStatusFilter() {
  router.get(route('invoices.index'), 
    { 
      search: searchQuery.value,
      status: statusFilter.value,
      type: props.type
    },
    { 
      preserveState: true,
      replace: true 
    }
  )
}

function setStatusFilter(status) {
  statusFilter.value = status
  handleStatusFilter()
}

// Actions
function showActionMenu(invoice) {
  selectedInvoice.value = invoice
  actionModalOpen.value = true
}

function handleAction(action, invoice) {
  switch (action) {
    case 'duplicate':
      router.post(route('invoices.duplicate', invoice.id))
      break
    case 'convert':
      router.post(route('invoices.convert', invoice.id))
      break
    case 'delete':
      if (confirm('Êtes-vous sûr de vouloir supprimer cette facture ?')) {
        router.delete(route('invoices.destroy', invoice.id))
      }
      break
  }
  actionModalOpen.value = false
}

// Utilitaires
function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('fr-FR')
}

function formatCurrency(amount) {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'XOF'
  }).format(amount)
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

function isOverdue(invoice) {
  if (invoice.status === 'paid') return false
  return new Date(invoice.due_date) < new Date()
}
</script>