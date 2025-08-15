<template>
  <AuthenticatedLayout>
    <Head :title="`Modifier - ${client.name}`" />
    
    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6">
          <Link 
            :href="route('clients.show', client.id)"
            class="text-blue-600 hover:text-blue-800 text-sm font-medium mb-2 inline-flex items-center"
          >
            <ArrowLeftIcon class="w-4 h-4 mr-1" />
            Retour au client
          </Link>
          <h1 class="text-2xl font-bold text-gray-900">Modifier le client</h1>
          <p class="text-gray-600">Modifiez les informations de {{ client.name }}</p>
        </div>
        
        <!-- Formulaire -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6 space-y-6">
            
            <!-- Nom (requis) -->
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                Nom complet *
              </label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'border-red-500': errors.name }"
                placeholder="Ex: Jean Dupont"
                required
              />
              <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
            </div>
            
            <!-- Entreprise (optionnel) -->
            <div>
              <label for="company" class="block text-sm font-medium text-gray-700 mb-2">
                Entreprise (optionnel)
              </label>
              <input
                id="company"
                v-model="form.company"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'border-red-500': errors.company }"
                placeholder="Ex: Dupont SARL"
              />
              <p v-if="errors.company" class="mt-1 text-sm text-red-600">{{ errors.company }}</p>
              <p class="mt-1 text-sm text-gray-500">
                Laissez vide si c'est un particulier
              </p>
            </div>
            
            <!-- Email (requis) -->
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                Email *
              </label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'border-red-500': errors.email }"
                placeholder="jean@example.com"
                required
              />
              <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
            </div>
            
            <!-- Téléphone -->
            <div>
              <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                Téléphone
              </label>
              <input
                id="phone"
                v-model="form.phone"
                type="tel"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'border-red-500': errors.phone }"
                placeholder="+229 XX XX XX XX"
              />
              <p v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ errors.phone }}</p>
            </div>
            
            <!-- Adresse -->
            <div>
              <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                Adresse
              </label>
              <textarea
                id="address"
                v-model="form.address"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'border-red-500': errors.address }"
                placeholder="Adresse complète du client..."
              ></textarea>
              <p v-if="errors.address" class="mt-1 text-sm text-red-600">{{ errors.address }}</p>
            </div>
            
            <!-- Informations de modification -->
            <div class="bg-gray-50 rounded-lg p-4">
              <div class="flex justify-between text-sm text-gray-600">
                <span>Client créé le {{ formatDate(client.created_at) }}</span>
                <span v-if="client.updated_at !== client.created_at">
                  Dernière modification : {{ formatDate(client.updated_at) }}
                </span>
              </div>
            </div>
            
            <!-- Boutons -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
              <Link 
                :href="route('clients.show', client.id)"
                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
              >
                Annuler
              </Link>
              <button
                type="submit"
                :disabled="processing || !hasChanges"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50"
              >
                <span v-if="processing">Modification...</span>
                <span v-else-if="!hasChanges">Aucun changement</span>
                <span v-else>Mettre à jour</span>
              </button>
            </div>
            
          </form>
        </div>
        
        <!-- Actions supplémentaires -->
        <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
            <div class="flex space-x-4">
              <Link 
                :href="route('invoices.create', { client: client.id })"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center"
              >
                <DocumentPlusIcon class="w-5 h-5 mr-2" />
                Créer une facture
              </Link>
              
              <button 
                @click="confirmDelete"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center"
              >
                <TrashIcon class="w-5 h-5 mr-2" />
                Supprimer ce client
              </button>
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
import { computed, ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ConfirmModal from '@/Components/ConfirmModal.vue'
import { 
  ArrowLeftIcon,
  DocumentPlusIcon,
  TrashIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  client: Object,
})

// État local
const showDeleteModal = ref(false)

// Formulaire réactif avec Inertia, pré-rempli avec les données du client
const form = useForm({
  name: props.client.name || '',
  company: props.client.company || '',
  email: props.client.email || '',
  phone: props.client.phone || '',
  address: props.client.address || '',
})

// Erreurs de validation
const errors = computed(() => form.errors)
const processing = computed(() => form.processing)

// Vérification des changements
const hasChanges = computed(() => {
  return form.name !== (props.client.name || '') ||
         form.company !== (props.client.company || '') ||
         form.email !== (props.client.email || '') ||
         form.phone !== (props.client.phone || '') ||
         form.address !== (props.client.address || '')
})

// Soumission du formulaire
function submit() {
  form.put(route('clients.update', props.client.id), {
    onSuccess: () => {
      // Redirection automatique vers la page show après succès
    },
  })
}

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
</script>