<template>
  <AuthenticatedLayout>
    <Head :title="type === 'quote' ? 'Nouveau devis' : 'Nouvelle facture'" />
    
    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6">
          <Link 
            :href="route('invoices.index', { type })"
            class="text-blue-600 hover:text-blue-800 text-sm font-medium mb-2 inline-flex items-center"
          >
            <ArrowLeftIcon class="w-4 h-4 mr-1" />
            Retour aux {{ type === 'quote' ? 'devis' : 'factures' }}
          </Link>
          <h1 class="text-2xl font-bold text-gray-900">
            {{ type === 'quote' ? 'Nouveau devis' : 'Nouvelle facture' }}
          </h1>
          <p class="text-gray-600">
            {{ type === 'quote' 
              ? 'Créez un nouveau devis pour votre client' 
              : 'Créez une nouvelle facture pour votre client' 
            }}
          </p>
        </div>
        
        <!-- Formulaire -->
        <form @submit.prevent="submit" class="space-y-6">
          
          <!-- Informations générales -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <DocumentTextIcon class="w-6 h-6 mr-2 text-blue-600" />
                Informations générales
              </h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Client -->
                <div>
                  <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Client *
                  </label>
                  <select
                    id="client_id"
                    v-model="form.client_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-500': errors.client_id }"
                    required
                  >
                    <option value="">Sélectionner un client</option>
                    <option 
                      v-for="client in clients" 
                      :key="client.id" 
                      :value="client.id"
                    >
                      {{ client.name }}{{ client.company ? ` (${client.company})` : '' }}
                    </option>
                  </select>
                  <p v-if="errors.client_id" class="mt-1 text-sm text-red-600">{{ errors.client_id }}</p>
                  <div class="mt-1 text-sm text-gray-500 flex items-center">
                    <span>Pas de client ?</span>
                    <Link 
                      :href="route('clients.create')"
                      class="ml-1 text-blue-600 hover:text-blue-800 font-medium"
                    >
                      Créer un nouveau client
                    </Link>
                  </div>
                </div>
                
                <!-- Type (hidden, contrôlé par l'URL) -->
                <input type="hidden" v-model="form.type" />
                
                <!-- Date d'émission -->
                <div>
                  <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                    Date d'émission *
                  </label>
                  <input
                    id="date"
                    v-model="form.date"
                    type="date"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-500': errors.date }"
                    required
                  />
                  <p v-if="errors.date" class="mt-1 text-sm text-red-600">{{ errors.date }}</p>
                </div>
                
                <!-- Date d'échéance -->
                <div>
                  <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ type === 'quote' ? 'Valable jusqu\'au' : 'Date d\'échéance' }} *
                  </label>
                  <input
                    id="due_date"
                    v-model="form.due_date"
                    type="date"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-500': errors.due_date }"
                    required
                  />
                  <p v-if="errors.due_date" class="mt-1 text-sm text-red-600">{{ errors.due_date }}</p>
                </div>
                
              </div>
            </div>
          </div>
          
          <!-- Articles -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                  <ListBulletIcon class="w-6 h-6 mr-2 text-blue-600" />
                  Articles
                </h2>
                <button
                  type="button"
                  @click="addItem"
                  class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center"
                >
                  <PlusIcon class="w-4 h-4 mr-1" />
                  Ajouter une ligne
                </button>
              </div>
              
              <!-- Tableau des articles -->
              <div class="overflow-x-auto">
                <table class="min-w-full">
                  <thead>
                    <tr class="border-b border-gray-200">
                      <th class="text-left py-2 text-sm font-medium text-gray-700 w-2/5">Description</th>
                      <th class="text-center py-2 text-sm font-medium text-gray-700 w-20">Qté</th>
                      <th class="text-right py-2 text-sm font-medium text-gray-700 w-24">Prix unit.</th>
                      <th class="text-center py-2 text-sm font-medium text-gray-700 w-20">TVA %</th>
                      <th class="text-right py-2 text-sm font-medium text-gray-700 w-24">Total HT</th>
                      <th class="w-10"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr 
                      v-for="(item, index) in form.items" 
                      :key="index"
                      class="border-b border-gray-100"
                    >
                      <!-- Description -->
                      <td class="py-3 pr-3">
                        <input
                          v-model="item.description"
                          type="text"
                          placeholder="Description de l'article..."
                          class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-sm"
                          :class="{ 'border-red-500': errors[`items.${index}.description`] }"
                        />
                        <p v-if="errors[`items.${index}.description`]" class="mt-1 text-xs text-red-600">
                          {{ errors[`items.${index}.description`] }}
                        </p>
                      </td>
                      
                      <!-- Quantité -->
                      <td class="py-3 px-2 text-center">
                        <input
                          v-model.number="item.quantity"
                          type="number"
                          min="1"
                          class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-sm text-center"
                          :class="{ 'border-red-500': errors[`items.${index}.quantity`] }"
                          @input="updateItemTotals(index)"
                        />
                        <p v-if="errors[`items.${index}.quantity`]" class="mt-1 text-xs text-red-600">
                          {{ errors[`items.${index}.quantity`] }}
                        </p>
                      </td>
                      
                      <!-- Prix unitaire -->
                      <td class="py-3 px-2 text-right">
                        <input
                          v-model.number="item.unit_price"
                          type="number"
                          step="0.01"
                          min="0"
                          class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-sm text-right"
                          :class="{ 'border-red-500': errors[`items.${index}.unit_price`] }"
                          @input="updateItemTotals(index)"
                        />
                        <p v-if="errors[`items.${index}.unit_price`]" class="mt-1 text-xs text-red-600">
                          {{ errors[`items.${index}.unit_price`] }}
                        </p>
                      </td>
                      
                      <!-- TVA -->
                      <td class="py-3 px-2 text-center">
                        <input
                          v-model.number="item.tva_rate"
                          type="number"
                          step="0.01"
                          min="0"
                          max="100"
                          class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-sm text-center"
                          :class="{ 'border-red-500': errors[`items.${index}.tva_rate`] }"
                          @input="updateItemTotals(index)"
                        />
                        <p v-if="errors[`items.${index}.tva_rate`]" class="mt-1 text-xs text-red-600">
                          {{ errors[`items.${index}.tva_rate`] }}
                        </p>
                      </td>
                      
                      <!-- Total HT -->
                      <td class="py-3 px-2 text-right">
                        <span class="text-sm font-medium text-gray-900">
                          {{ formatCurrency(item.total_ht || 0) }}
                        </span>
                      </td>
                      
                      <!-- Actions -->
                      <td class="py-3 text-center">
                        <button
                          type="button"
                          @click="removeItem(index)"
                          class="text-red-600 hover:text-red-800 p-1 rounded"
                          title="Supprimer cette ligne"
                        >
                          <TrashIcon class="w-4 h-4" />
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              
              <!-- Message si aucun article -->
              <div v-if="form.items.length === 0" class="text-center py-8 text-gray-500">
                <ListBulletIcon class="w-12 h-12 mx-auto text-gray-300 mb-3" />
                <p class="text-sm">Aucun article ajouté</p>
                <p class="text-xs">Cliquez sur "Ajouter une ligne" pour commencer</p>
              </div>
              
              <!-- Totaux -->
              <div v-if="form.items.length > 0" class="mt-6 border-t pt-4">
                <div class="flex justify-end">
                  <div class="w-64 space-y-2">
                    <div class="flex justify-between text-sm">
                      <span class="text-gray-600">Total HT:</span>
                      <span class="font-medium">{{ formatCurrency(totals.totalHt) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                      <span class="text-gray-600">Total TVA:</span>
                      <span class="font-medium">{{ formatCurrency(totals.totalTva) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t pt-2">
                      <span>Total TTC:</span>
                      <span>{{ formatCurrency(totals.totalTtc) }}</span>
                    </div>
                  </div>
                </div>
              </div>
              
              <p v-if="errors.items" class="mt-2 text-sm text-red-600">{{ errors.items }}</p>
            </div>
          </div>
          
          <!-- Notes -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <ChatBubbleBottomCenterTextIcon class="w-6 h-6 mr-2 text-blue-600" />
                Notes (optionnel)
              </h2>
              
              <textarea
                v-model="form.notes"
                rows="4"
                placeholder="Notes ou conditions particulières..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'border-red-500': errors.notes }"
              ></textarea>
              <p v-if="errors.notes" class="mt-1 text-sm text-red-600">{{ errors.notes }}</p>
            </div>
          </div>
          
          <!-- Boutons -->
          <div class="flex justify-end space-x-3 bg-white p-6 rounded-lg shadow-sm">
            <Link 
              :href="route('invoices.index', { type })"
              class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
            >
              Annuler
            </Link>
            <button
              type="submit"
              :disabled="processing || form.items.length === 0"
              class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 inline-flex items-center"
            >
              <span v-if="processing">
                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Enregistrement...
              </span>
              <span v-else>
                <DocumentCheckIcon class="w-5 h-5 mr-2" />
                {{ type === 'quote' ? 'Créer le devis' : 'Créer la facture' }}
              </span>
            </button>
          </div>
          
        </form>
        
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { computed, onMounted, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { 
  ArrowLeftIcon,
  DocumentTextIcon,
  ListBulletIcon,
  PlusIcon,
  TrashIcon,
  ChatBubbleBottomCenterTextIcon,
  DocumentCheckIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  clients: Array,
  selectedClientId: [String, Number],
  type: String,
})

// Formulaire réactif avec Inertia
const form = useForm({
  client_id: props.selectedClientId || '',
  type: props.type || 'invoice',
  date: new Date().toISOString().split('T')[0],
  due_date: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], // +30 jours
  notes: '',
  items: []
})

// Erreurs de validation
const errors = computed(() => form.errors)
const processing = computed(() => form.processing)

// Totaux calculés
const totals = computed(() => {
  const totalHt = form.items.reduce((sum, item) => sum + (item.total_ht || 0), 0)
  const totalTva = form.items.reduce((sum, item) => sum + (item.total_tva || 0), 0)
  const totalTtc = totalHt + totalTva
  
  return { totalHt, totalTva, totalTtc }
})

// Méthodes
function addItem() {
  form.items.push({
    description: '',
    quantity: 1,
    unit_price: 0,
    tva_rate: 18.00,
    total_ht: 0,
    total_tva: 0,
    total_ttc: 0
  })
}

function removeItem(index) {
  form.items.splice(index, 1)
}

function updateItemTotals(index) {
  const item = form.items[index]
  if (item.quantity && item.unit_price) {
    item.total_ht = item.quantity * item.unit_price
    item.total_tva = item.total_ht * (item.tva_rate / 100)
    item.total_ttc = item.total_ht + item.total_tva
  } else {
    item.total_ht = 0
    item.total_tva = 0
    item.total_ttc = 0
  }
}

function formatCurrency(amount) {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'XOF'
  }).format(amount)
}

// Soumission du formulaire
function submit() {
  form.post(route('invoices.store'), {
    onSuccess: () => {
      // Redirection automatique après succès
    },
  })
}

// Initialisation
onMounted(() => {
  // Ajouter une ligne par défaut si aucune
  if (form.items.length === 0) {
    addItem()
  }
})

// Watcher pour recalculer les totaux quand les items changent
watch(() => form.items, () => {
  form.items.forEach((item, index) => {
    updateItemTotals(index)
  })
}, { deep: true })
</script>