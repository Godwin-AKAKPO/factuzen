<template>
  <TransitionRoot :show="show" as="template">
    <Dialog as="div" class="relative z-50" @close="$emit('close')">
      <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
        leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
      </TransitionChild>

      <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <TransitionChild as="template" enter="ease-out duration-300"
            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
            leave-from="opacity-100 translate-y-0 sm:scale-100"
            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl">
              <form @submit.prevent="submit">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 max-h-[80vh] overflow-y-auto">
                  
                  <!-- Header -->
                  <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">
                      {{ type === 'quote' ? 'Nouveau devis' : 'Nouvelle facture' }}
                    </h1>
                    <p class="text-gray-600">
                      {{ type === 'quote' ? 'Créez un nouveau devis pour votre client' : 'Créez une nouvelle facture pour votre client' }}
                    </p>
                  </div>

                  <div class="space-y-6">
                    <!-- Informations générales -->
                    <div class="bg-gray-50 p-4 rounded-lg">
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
                          <select id="client_id" v-model="form.client_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            :class="{ 'border-red-500': errors.client_id }">
                            <option value="">Sélectionner un client</option>
                            <option v-for="client in clients" :key="client.id" :value="client.id">
                              {{ client.name }}{{ client.company ? ` (${client.company})` : '' }}
                            </option>
                          </select>
                          <p v-if="errors.client_id" class="mt-1 text-sm text-red-600">{{ errors.client_id }}</p>
                        </div>

                        <input type="hidden" v-model="form.type" />

                        <!-- Date d'émission -->
                        <div>
                          <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                            Date d'émission *
                          </label>
                          <input id="date" v-model="form.date" type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            :class="{ 'border-red-500': errors.date }" />
                          <p v-if="errors.date" class="mt-1 text-sm text-red-600">{{ errors.date }}</p>
                        </div>

                        <!-- Date d'échéance -->
                        <div>
                          <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ type === 'quote' ? 'Valable jusqu\'au' : 'Date d\'échéance' }} *
                          </label>
                          <input id="due_date" v-model="form.due_date" type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            :class="{ 'border-red-500': errors.due_date }" />
                          <p v-if="errors.due_date" class="mt-1 text-sm text-red-600">{{ errors.due_date }}</p>
                        </div>
                      </div>
                    </div>

                    <!-- Articles -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                      <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                          <ListBulletIcon class="w-6 h-6 mr-2 text-blue-600" />
                          Articles
                        </h2>
                        <button type="button" @click="addItem"
                          class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center">
                          <PlusIcon class="w-4 h-4 mr-1" />
                          Ajouter
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
                            <tr v-for="(item, index) in form.items" :key="index" class="border-b border-gray-100">
                              <td class="py-3 pr-3">
                                <input v-model="item.description" type="text" placeholder="Description..."
                                  class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-sm"
                                  :class="{ 'border-red-500': errors[`items.${index}.description`] }" />
                              </td>
                              <td class="py-3 px-2 text-center">
                                <input v-model.number="item.quantity" type="number" min="1"
                                  class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-sm text-center"
                                  @input="updateItemTotals(index)" />
                              </td>
                              <td class="py-3 px-2 text-right">
                                <input v-model.number="item.unit_price" type="number" step="0.01" min="0"
                                  class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-sm text-right"
                                  @input="updateItemTotals(index)" />
                              </td>
                              <td class="py-3 px-2 text-center">
                                <input v-model.number="item.tva_rate" type="number" step="0.01" min="0" max="100"
                                  class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-sm text-center"
                                  @input="updateItemTotals(index)" />
                              </td>
                              <td class="py-3 px-2 text-right">
                                <span class="text-sm font-medium text-gray-900">
                                  {{ formatCurrency(item.total_ht || 0) }}
                                </span>
                              </td>
                              <td class="py-3 text-center">
                                <button type="button" @click="removeItem(index)"
                                  class="text-red-600 hover:text-red-800 p-1 rounded">
                                  <TrashIcon class="w-4 h-4" />
                                </button>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>

                      <div v-if="form.items.length === 0" class="text-center py-8 text-gray-500">
                        <ListBulletIcon class="w-12 h-12 mx-auto text-gray-300 mb-3" />
                        <p class="text-sm">Aucun article ajouté</p>
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
                    </div>

                    <!-- Notes -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                      <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <ChatBubbleBottomCenterTextIcon class="w-6 h-6 mr-2 text-blue-600" />
                        Notes (optionnel)
                      </h2>
                      <textarea v-model="form.notes" rows="4" placeholder="Notes ou conditions particulières..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                  </div>
                </div>

                <!-- Boutons -->
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-3">
                  <button type="submit" :disabled="processing || form.items.length === 0"
                    class="inline-flex w-full justify-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 sm:w-auto disabled:opacity-50">
                    <span v-if="processing">Enregistrement...</span>
                    <span v-else>{{ type === 'quote' ? 'Créer le devis' : 'Créer la facture' }}</span>
                  </button>
                  <button type="button" @click="$emit('close')"
                    class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                    Annuler
                  </button>
                </div>
              </form>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import { computed, onMounted, watch } from 'vue';
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { useForm } from '@inertiajs/vue3'
import {
  DocumentTextIcon,
  ListBulletIcon,
  PlusIcon,
  TrashIcon,
  ChatBubbleBottomCenterTextIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  show: Boolean,
  clients: Array,
  selectedClientId: [String, Number],
  type: String,
})

const emit = defineEmits(['close', 'created'])

const form = useForm({
  client_id: props.selectedClientId || '',
  type: props.type || 'invoice',
  date:    new Date().toLocaleDateString("fr"),
  due_date: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], // +30 jours
  notes: '',
  items:[]
})

watch(() => props.show, (newShow) => {
  if (!newShow) {
    form.reset()
    form.clearErrors()
  } else if (form.items.length === 0) {
    addItem()
  }
})

const errors = computed(() => form.errors)
const processing = computed(() => form.processing)

const totals = computed(() => {
  const totalHt = form.items.reduce((sum, item) => sum + (item.total_ht || 0), 0)
  const totalTva = form.items.reduce((sum, item) => sum + (item.total_tva || 0), 0)
  const totalPromo=form.items.reduce((sum,item) => sum + (item.total_promo || 0), 0)
  const totalTtc = totalHt + totalTva-totalPromo
  
  return { totalHt, totalTva, totalTtc , totalPromo}
})


// Méthodes

//Fonction annuler , retourne à l'action précedente
function goBack(){
  window.history.back()
}

function addItem() {
  form.items.push({
    description: '',
    quantity: 1,
    unit_price: 0,
    tva_rate: 18.00,
    tva_promo:0,
    total_promo:0,
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
    item.total_promo = item.total_ht * (item.tva_promo / 100)
    item.total_ttc = item.total_ht + item.total_tva
  } else {
    item.total_ht = 0
    item.total_tva = 0
    item.total_promo=0
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
      emit('created')
      emit('close')
      form.reset()
    },
  })
}

onMounted(() => {
  if (form.items.length === 0) {
    addItem()
  }
})

watch(() => form.items, () => {
  form.items.forEach((item, index) => {
    updateItemTotals(index)
  })
}, { deep: true })
</script>