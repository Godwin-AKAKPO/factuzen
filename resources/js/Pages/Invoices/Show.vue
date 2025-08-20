<template>
  <AuthenticatedLayout>

    <Head :title="`${invoice.type === 'quote' ? 'Devis' : 'Facture'} ${invoice.reference}`" />

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

        <!-- Header avec actions -->
        <div class="mb-6">
          <Link :href="route('invoices.index', { type: invoice.type })"
            class="text-blue-600 hover:text-blue-800 text-sm font-medium mb-2 inline-flex items-center">
          <ArrowLeftIcon class="w-4 h-4 mr-1" />
          Retour aux {{ invoice.type === 'quote' ? 'devis' : 'factures' }}
          </Link>

          <div class="flex justify-between items-start">
            <div>
              <div class="flex items-center space-x-3 mb-2">
                <h1 class="text-2xl font-bold text-gray-900">
                  {{ invoice.type === 'quote' ? 'Devis' : 'Facture' }} {{ invoice.reference }}
                </h1>
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full"
                  :class="getStatusClass(invoice.status)">
                  {{ getStatusLabel(invoice.status) }}
                </span>
              </div>
              <p class="text-gray-600">
                Créé le {{ formatDate(invoice.date) }}
                <span v-if="invoice.type === 'invoice'"> • Échéance : {{ formatDate(invoice.due_date) }}</span>
                <span v-if="invoice.type === 'quote'"> • Valable jusqu'au {{ formatDate(invoice.due_date) }}</span>
              </p>
            </div>

            <!-- Actions rapides -->
            <div class="flex space-x-2">
              <!-- Bouton Aperçu PDF -->
              <!-- <button
                @click="previewPDF"
                class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center"
                title="Aperçu PDF"
              >
                <EyeIcon class="w-5 h-5 mr-2" />
                Aperçu
              </button> -->

              <!-- Bouton Télécharger PDF -->
              <button @click="downloadPDF"
                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                <DocumentArrowDownIcon class="w-5 h-5 mr-2" />
                PDF
              </button>

              <!-- Bouton Email -->
              <button @click="sendEmail"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                <EnvelopeIcon class="w-5 h-5 mr-2" />
                Envoyer
              </button>

              <!-- Bouton Modifier (si pas payé) -->
              <Link v-if="invoice.status !== 'paid'" :href="route('invoices.edit', invoice.id)"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
              <PencilIcon class="w-5 h-5 mr-2" />
              Modifier
              </Link>

              <!-- Menu actions -->
              <button @click="showActionsMenu = !showActionsMenu"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-lg transition-colors relative">
                <EllipsisVerticalIcon class="w-5 h-5" />

                <!-- Dropdown menu -->
                <div v-if="showActionsMenu"
                  class="absolute right-0 top-full mt-1 w-48 bg-white rounded-lg shadow-lg border z-10" @click.stop>
                  <div class="py-1">
                    <!-- Conversion devis -> facture -->
                    <button v-if="invoice.type === 'quote' && invoice.status !== 'paid'" @click="convertToInvoice"
                      class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                      <ArrowRightIcon class="w-4 h-4 mr-2" />
                      Convertir en facture
                    </button>

                    <!-- Dupliquer -->
                    <button @click="duplicate"
                      class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                      <DocumentDuplicateIcon class="w-4 h-4 mr-2" />
                      Dupliquer
                    </button>

                    <!-- Supprimer -->
                    <button v-if="!['paid', 'sent'].includes(invoice.status)" @click="deleteInvoice"
                      class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                      <TrashIcon class="w-4 h-4 mr-2" />
                      Supprimer
                    </button>
                  </div>
                </div>
              </button>
            </div>
          </div>
        </div>

        <!-- Gestion du statut -->
        <div v-if="invoice.type === 'invoice'" class="mb-6 bg-white p-4 rounded-lg shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-1">Statut de la facture</h3>
              <p class="text-sm text-gray-600">Mettre à jour le statut selon l'avancement</p>
            </div>
            <div class="flex space-x-2">
              <button v-for="status in statusOptions" :key="status.value" @click="updateStatus(status.value)" :class="[
                'px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                invoice.status === status.value
                  ? status.activeClass
                  : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
              ]">
                {{ status.label }}
              </button>
            </div>
          </div>
        </div>

        <!-- Contenu principal -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

          <!-- Facture détaillée -->
          <div class="lg:col-span-2">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

              <!-- En-tête facture -->
              <div class="p-6 border-b border-gray-200">
                <div class="grid grid-cols-2 gap-6">

                  <!-- Informations émetteur -->
                  <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">De :</h3>
                    <div class="text-sm text-gray-600 space-y-1">
                      <p class="font-medium text-gray-900">{{ invoice.user.name }}</p>
                      <p>{{ invoice.user.email }}</p>
                    </div>
                  </div>

                  <!-- Informations client -->
                  <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">À :</h3>
                    <div class="text-sm text-gray-600 space-y-1">
                      <p class="font-medium text-gray-900">{{ invoice.client.name }}</p>
                      <p v-if="invoice.client.company" class="text-gray-700">{{ invoice.client.company }}</p>
                      <p v-if="invoice.client.address">{{ invoice.client.address }}</p>
                      <p>{{ invoice.client.email }}</p>
                      <p v-if="invoice.client.phone">{{ invoice.client.phone }}</p>
                    </div>
                  </div>
                </div>

                <!-- Informations facture -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                  <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                      <p class="text-gray-600">Référence :</p>
                      <p class="font-medium text-gray-900">{{ invoice.reference }}</p>
                    </div>
                    <div>
                      <p class="text-gray-600">Date d'émission :</p>
                      <p class="font-medium text-gray-900">{{ formatDate(invoice.date) }}</p>
                    </div>
                    <div>
                      <p class="text-gray-600">
                        {{ invoice.type === 'quote' ? 'Valable jusqu\'au :' : 'Échéance :' }}
                      </p>
                      <p class="font-medium text-gray-900">{{ formatDate(invoice.due_date) }}</p>
                    </div>
                    <div v-if="invoice.type === 'invoice' && isOverdue(invoice)">
                      <p class="text-red-600 font-medium">⚠️ En retard</p>
                      <p class="text-xs text-red-500">{{ getDaysOverdue(invoice) }} jour(s)</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Articles -->
              <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Articles</h3>

                <div class="overflow-x-auto">
                  <table class="min-w-full">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Description
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Qté
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Prix unit.
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                          TVA %
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Total HT
                        </th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      <tr v-for="item in invoice.items" :key="item.id">
                        <td class="px-4 py-4 text-sm text-gray-900">
                          {{ item.description }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-900 text-center">
                          {{ item.quantity }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-900 text-right">
                          {{ formatCurrency(item.unit_price) }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-900 text-center">
                          {{ item.tva_rate }}%
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-900 text-right font-medium">
                          {{ formatCurrency(item.total_ht) }}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <!-- Totaux -->
                <div class="mt-6 border-t pt-4">
                  <div class="flex justify-end">
                    <div class="w-64 space-y-2">
                      <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Total HT :</span>
                        <span class="font-medium">{{ formatCurrency(invoice.total_ht) }}</span>
                      </div>
                      <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Total TVA :</span>
                        <span class="font-medium">{{ formatCurrency(invoice.total_tva) }}</span>
                      </div>
                      <div class="flex justify-between text-lg font-bold border-t pt-2">
                        <span>Total TTC :</span>
                        <span>{{ formatCurrency(invoice.total_ttc) }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Notes -->
                <div v-if="invoice.notes" class="mt-6 pt-6 border-t border-gray-200">
                  <h4 class="text-sm font-medium text-gray-900 mb-2">Notes :</h4>
                  <p class="text-sm text-gray-600 whitespace-pre-wrap">{{ invoice.notes }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar avec informations complémentaires -->
          <div class="space-y-6">

            <!-- Historique -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                  <ClockIcon class="w-5 h-5 mr-2 text-blue-600" />
                  Historique
                </h3>

                <div class="space-y-3">
                  <div class="flex items-center text-sm">
                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                    <div>
                      <p class="text-gray-900 font-medium">{{ invoice.type === 'quote' ? 'Devis créé' : 'Facture créée'
                        }}</p>
                      <p class="text-gray-500">{{ formatDateTime(invoice.created_at) }}</p>
                    </div>
                  </div>

                  <div v-if="invoice.sent_at" class="flex items-center text-sm">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                    <div>
                      <p class="text-gray-900 font-medium">Envoyé par email</p>
                      <p class="text-gray-500">{{ formatDateTime(invoice.sent_at) }}</p>
                    </div>
                  </div>

                  <div v-if="invoice.paid_at" class="flex items-center text-sm">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full mr-3"></div>
                    <div>
                      <p class="text-gray-900 font-medium">Marqué comme payé</p>
                      <p class="text-gray-500">{{ formatDateTime(invoice.paid_at) }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Informations client -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                  <UserIcon class="w-5 h-5 mr-2 text-blue-600" />
                  Client
                </h3>

                <div class="space-y-2 text-sm">
                  <div>
                    <p class="text-gray-600">Nom :</p>
                    <p class="font-medium text-gray-900">{{ invoice.client.name }}</p>
                  </div>
                  <div v-if="invoice.client.company">
                    <p class="text-gray-600">Entreprise :</p>
                    <p class="font-medium text-gray-900">{{ invoice.client.company }}</p>
                  </div>
                  <div>
                    <p class="text-gray-600">Email :</p>
                    <a :href="`mailto:${invoice.client.email}`" class="text-blue-600 hover:text-blue-800">
                      {{ invoice.client.email }}
                    </a>
                  </div>
                  <div v-if="invoice.client.phone">
                    <p class="text-gray-600">Téléphone :</p>
                    <a :href="`tel:${invoice.client.phone}`" class="text-blue-600 hover:text-blue-800">
                      {{ invoice.client.phone }}
                    </a>
                  </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200">
                  <Link :href="route('clients.show', invoice.client.id)"
                    class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                  Voir toutes les factures de ce client →
                  </Link>
                </div>
              </div>
            </div>

            <!-- Actions rapides -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h3>

                <div class="space-y-2">
                  <Link :href="route('invoices.create', { client: invoice.client.id, type: invoice.type })"
                    class="w-full text-left bg-blue-50 text-blue-700 px-3 py-2 rounded-lg hover:bg-blue-100 transition-colors text-sm font-medium flex items-center">
                  <DocumentPlusIcon class="w-4 h-4 mr-2" />
                  Nouveau {{ invoice.type === 'quote' ? 'devis' : 'facture' }} pour ce client
                  </Link>

                  <button @click="duplicate"
                    class="w-full text-left bg-gray-50 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors text-sm font-medium flex items-center">
                    <DocumentDuplicateIcon class="w-4 h-4 mr-2" />
                    Dupliquer ce {{ invoice.type === 'quote' ? 'devis' : 'facture' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import {
  ArrowLeftIcon,
  DocumentArrowDownIcon,
  EnvelopeIcon,
  PencilIcon,
  EllipsisVerticalIcon,
  ArrowRightIcon,
  DocumentDuplicateIcon,
  TrashIcon,
  ClockIcon,
  UserIcon,
  DocumentPlusIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  invoice: Object,
})

// État local
const showActionsMenu = ref(false)

// Options de statut
const statusOptions = computed(() => [
  { value: 'draft', label: 'Brouillon', activeClass: 'bg-gray-100 text-gray-800' },
  { value: 'sent', label: 'Envoyé', activeClass: 'bg-blue-100 text-blue-800' },
  { value: 'paid', label: 'Payé', activeClass: 'bg-green-100 text-green-800' },
  { value: 'overdue', label: 'En retard', activeClass: 'bg-red-100 text-red-800' },
  { value: 'cancelled', label: 'Annulé', activeClass: 'bg-gray-100 text-gray-800' },
])

// Méthodes
function updateStatus(status) {
  router.patch(route('invoices.update-status', props.invoice.id), { status })
}

function downloadPDF() {
  // Télécharger le PDF
  window.open(route('invoices.pdf.download', props.invoice.id), '_blank')
}

// function previewPDF() {
//   // Prévisualiser le PDF dans un nouvel onglet
//   window.open(route('invoices.pdf.preview', props.invoice.id), '_blank')
// }

function sendEmail() {
  if (confirm('Envoyer cette facture par email au client ?')) {
    router.post(route('invoices.send-email', props.invoice.id), {}, {
      onSuccess: () => {
        // Le message de succès sera affiché automatiquement
      },
      onError: (errors) => {
        console.error('Erreur envoi email:', errors)
      }
    })
  }
}

function convertToInvoice() {
  router.post(route('invoices.convert', props.invoice.id))
  showActionsMenu.value = false
}

function duplicate() {
  router.post(route('invoices.duplicate', props.invoice.id))
  showActionsMenu.value = false
}

function deleteInvoice() {
  if (confirm(`Êtes-vous sûr de vouloir supprimer ce ${props.invoice.type === 'quote' ? 'devis' : 'facture'} ?`)) {
    router.delete(route('invoices.destroy', props.invoice.id))
  }
  showActionsMenu.value = false
}

// Utilitaires
function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('fr-FR')
}

function formatDateTime(dateString) {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
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
    'sent': 'Envoyé',
    'paid': 'Payé',
    'overdue': 'En retard',
    'cancelled': 'Annulé',
  }
  return labels[status] || status
}

function isOverdue(invoice) {
  if (invoice.status === 'paid') return false
  return new Date(invoice.due_date) < new Date()
}

function getDaysOverdue(invoice) {
  const today = new Date()
  const dueDate = new Date(invoice.due_date)
  const diffTime = today - dueDate
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24))
}

// Fermer le menu quand on clique ailleurs
document.addEventListener('click', () => {
  showActionsMenu.value = false
})
</script>