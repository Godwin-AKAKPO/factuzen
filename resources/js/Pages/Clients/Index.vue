<template>
  <AuthenticatedLayout>
    <Head title="Clients" />
    
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Header avec recherche et bouton d'ajout -->
        <div class="mb-6 flex justify-between items-center">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Clients</h1>
            <p class="text-gray-600">Gérez vos clients</p>
          </div>
          
          <button 
            @click="showCreateModal = true"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center"
          >
            <UserPlusIcon class="w-5 h-5 mr-2" />
            Nouveau client
          </button>
        </div>
        
        <!-- Barre de recherche -->
        <div class="mb-6 bg-white p-4 rounded-lg shadow">
          <div class="relative">
            <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
            <input
              v-model="searchQuery"
              @input="handleSearch"
              type="text"
              placeholder="Rechercher un client..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
        </div>
        
        <!-- Tableau des clients -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div v-if="clients.data.length === 0" class="p-8 text-center">
            <UsersIcon class="w-16 h-16 mx-auto text-gray-300 mb-4" />
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun client trouvé</h3>
            <p class="text-gray-600 mb-4">
              {{ search ? 'Aucun client ne correspond à votre recherche.' : 'Commencez par ajouter votre premier client.' }}
            </p>
            <button 
              @click="showCreateModal = true"
              class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
            >
              <UserPlusIcon class="w-5 h-5 mr-2" />
              Ajouter un client
            </button>
          </div>
          
          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Nom
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Contact
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Factures
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Créé le
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr 
                  v-for="client in clients.data" 
                  :key="client.id"
                  class="hover:bg-gray-50"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div>
                      <div class="text-sm font-medium text-gray-900">{{ client.name }}</div>
                      <div v-if="client.company" class="text-sm text-gray-500">{{ client.company }}</div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div>
                      <div class="text-sm text-gray-900">{{ client.email }}</div>
                      <div v-if="client.phone" class="text-sm text-gray-500">{{ client.phone }}</div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                      {{ client.invoices_count }} facture(s)
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(client.created_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                      <button 
                        @click="showClient(client)"
                        class="text-blue-600 hover:text-blue-900 p-1 rounded"
                        title="Voir"
                      >
                        <EyeIcon class="w-5 h-5" />
                      </button>
                      <button 
                        @click="editClient(client)"
                        class="text-green-600 hover:text-green-900 p-1 rounded"
                        title="Modifier"
                      >
                        <PencilIcon class="w-5 h-5" />
                      </button>
                      <button 
                        @click="confirmDelete(client)"
                        class="text-red-600 hover:text-red-900 p-1 rounded"
                        title="Supprimer"
                      >
                        <TrashIcon class="w-5 h-5" />
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        
        <!-- Pagination -->
        <div v-if="clients.links.length > 3" class="mt-6">
          <Pagination :links="clients.links" />
        </div>
        
      </div>
    </div>
    
    <!-- Modal de création -->
    <Create 
      :show="showCreateModal"
      @close="showCreateModal = false"
      @created="handleClientCreated"
    />
    
    <!-- Modal d'affichage -->
    <Show 
      :show="showShowModal"
      :client="selectedClient"
      @close="showShowModal = false"
      @edit="editClient"
      @delete="confirmDelete"
    />
    
    <!-- Modal d'édition -->
    <Edit 
      :show="showEditModal"
      :client="selectedClient"
      @close="showEditModal = false"
      @updated="handleClientUpdated"
    />
    
    <!-- Modal de confirmation de suppression -->
    <ConfirmModal 
      :show="showDeleteModal" 
      @close="showDeleteModal = false"
      @confirm="deleteClient"
      title="Supprimer le client"
      :message="`Êtes-vous sûr de vouloir supprimer ${clientToDelete?.name} ? Cette action est irréversible.`"
      confirm-text="Supprimer"
      confirm-class="bg-red-600 hover:bg-red-700"
    />
    
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import ConfirmModal from '@/Components/ConfirmModal.vue'
import Create from '@/Pages/Clients/Create.vue'
import Show from '@/Pages/Clients/Show.vue'
import Edit from '@/Pages/Clients/Edit.vue'
import { 
  UserPlusIcon, 
  UsersIcon,
  MagnifyingGlassIcon,
  EyeIcon,
  PencilIcon,
  TrashIcon
} from '@heroicons/vue/24/outline'
import { debounce } from 'lodash'

const props = defineProps({
  clients: Object,
  search: String,
})

// État local
const searchQuery = ref(props.search || '')
const showCreateModal = ref(false)
const showShowModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const selectedClient = ref(null)
const clientToDelete = ref(null)

// Recherche avec debounce
const handleSearch = debounce(() => {
  router.get(route('clients.index'), 
    { search: searchQuery.value },
    { 
      preserveState: true,
      replace: true 
    }
  )
}, 300)

// Afficher un client
function showClient(client) {
  selectedClient.value = client
  showShowModal.value = true
}

// Modifier un client
function editClient(client) {
  selectedClient.value = client
  showShowModal.value = false // Fermer le modal show si ouvert
  showEditModal.value = true
}

// Confirmation de suppression
function confirmDelete(client) {
  clientToDelete.value = client
  showShowModal.value = false // Fermer le modal show si ouvert
  showEditModal.value = false // Fermer le modal edit si ouvert
  showDeleteModal.value = true
}

function deleteClient() {
  if (clientToDelete.value) {
    router.delete(route('clients.destroy', clientToDelete.value.id), {
      onSuccess: () => {
        showDeleteModal.value = false
        clientToDelete.value = null
      }
    })
  }
}

// Gestionnaires d'événements pour les modals
function handleClientCreated() {
  router.reload({ only: ['clients'] })
}

function handleClientUpdated() {
  router.reload({ only: ['clients'] })
}

// Utilitaires
function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('fr-FR')
}
</script>