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
            <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
              <form @submit.prevent="submit">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                  <!-- Header -->
                  <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Nouveau client</h1>
                    <p class="text-gray-600">Ajoutez un nouveau client à votre carnet d'adresses</p>
                  </div>
                  
                  <div class="space-y-6">
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
                  </div>
                </div>
                
                <!-- Boutons -->
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-3">
                  <button
                    type="submit"
                    :disabled="processing"
                    class="inline-flex w-full justify-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 sm:w-auto disabled:opacity-50"
                  >
                    <span v-if="processing">Enregistrement...</span>
                    <span v-else>Enregistrer</span>
                  </button>
                  <button
                    type="button"
                    @click="$emit('close')"
                    class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                  >
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
import { computed, watch } from 'vue'
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  show: Boolean
})

const emit = defineEmits(['close', 'created'])

const form = useForm({
  name: '',
  company: '',
  email: '',
  phone: '',
  address: '',
})

// Réinitialiser le formulaire quand le modal se ferme
watch(() => props.show, (newShow) => {
  if (!newShow) {
    form.reset()
    form.clearErrors()
  }
})

const errors = computed(() => form.errors)
const processing = computed(() => form.processing)

//Methode
//Fonction Annuler
function goBack(){
  window.history.back()
}

// Soumission du formulaire
function submit() {
  form.post(route('clients.store'), {
    onSuccess: () => {
      emit('created')
      emit('close')
      form.reset()
    },
  })
}
</script>