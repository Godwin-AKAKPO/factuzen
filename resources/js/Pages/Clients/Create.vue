<template>
  <AuthenticatedLayout>
    <Head title="Nouveau client" />
    
    <!-- Fond avec dégradé cohérent -->
    <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-green-50 dark:from-gray-900 dark:via-gray-800 dark:to-emerald-900">
      <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
          
          <!-- Header amélioré -->
          <div class="mb-8">
            <Link 
              :href="route('clients.index')"
              class="text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-300 text-sm font-semibold mb-4 inline-flex items-center group transition-colors duration-300"
            >
              <ArrowLeftIcon class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform duration-300" />
              Retour aux clients
            </Link>
            
            <div class="relative">
              <!-- Éléments décoratifs -->
              <div class="absolute -top-4 -left-4 h-20 w-20 rounded-full bg-gradient-to-r from-green-200 to-emerald-200 opacity-30 blur-2xl dark:from-green-800 dark:to-emerald-800"></div>
              
              <div class="relative">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">
                  Nouveau client
                </h1>
                <p class="text-gray-600 dark:text-gray-300 text-lg mb-4">
                  Ajoutez un nouveau client à votre carnet d'adresses FactureZen
                </p>
                <div class="w-20 h-1 bg-gradient-to-r from-emerald-600 to-green-600"></div>
              </div>
            </div>
          </div>
          
          <!-- Formulaire amélioré -->
          <div class="relative">
            <!-- Éléments décoratifs de fond -->
            <div class="absolute -top-6 -right-6 h-32 w-32 rounded-full bg-gradient-to-r from-emerald-200 to-green-200 opacity-20 blur-2xl dark:from-emerald-800 dark:to-green-800"></div>
            <div class="absolute -bottom-6 -left-6 h-24 w-24 rounded-full bg-gradient-to-r from-green-200 to-emerald-200 opacity-20 blur-xl dark:from-green-800 dark:to-emerald-800"></div>
            
            <div class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-2xl border border-green-100 dark:border-green-800/30 overflow-hidden">
              <form @submit.prevent="submit" class="p-8 space-y-8">
                
                <!-- Section Informations personnelles -->
                <div>
                  <div class="flex items-center mb-6">
                    <div class="p-2 rounded-xl bg-gradient-to-r from-emerald-100 to-green-100 dark:from-emerald-900/50 dark:to-green-900/50 mr-3">
                      <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Informations personnelles</h2>
                  </div>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom (requis) -->
                    <div class="md:col-span-2">
                      <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                        Nom complet *
                      </label>
                      <div class="relative">
                        <input
                          id="name"
                          v-model="form.name"
                          type="text"
                          class="w-full px-4 py-4 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:text-white transition-all duration-300 text-lg font-medium"
                          :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': errors.name }"
                          placeholder="Ex: Jean Dupont"
                          required
                        />
                        <div v-if="errors.name" class="absolute -bottom-6 left-0">
                          <p class="text-sm text-red-600 dark:text-red-400 font-medium">{{ errors.name }}</p>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Entreprise (optionnel) -->
                    <div class="md:col-span-2">
                      <label for="company" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                        Entreprise (optionnel)
                      </label>
                      <div class="relative">
                        <input
                          id="company"
                          v-model="form.company"
                          type="text"
                          class="w-full px-4 py-4 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:text-white transition-all duration-300"
                          :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': errors.company }"
                          placeholder="Ex: Dupont SARL"
                        />
                        <div v-if="errors.company" class="absolute -bottom-6 left-0">
                          <p class="text-sm text-red-600 dark:text-red-400 font-medium">{{ errors.company }}</p>
                        </div>
                      </div>
                      <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 italic">
                        Laissez vide si c'est un particulier
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Section Contact -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
                  <div class="flex items-center mb-6">
                    <div class="p-2 rounded-xl bg-gradient-to-r from-blue-100 to-indigo-100 dark:from-blue-900/50 dark:to-indigo-900/50 mr-3">
                      <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                      </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Informations de contact</h2>
                  </div>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Email (requis) -->
                    <div>
                      <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                        Email *
                      </label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                          </svg>
                        </div>
                        <input
                          id="email"
                          v-model="form.email"
                          type="email"
                          class="w-full pl-10 pr-4 py-4 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:text-white transition-all duration-300"
                          :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': errors.email }"
                          placeholder="jean@example.com"
                          required
                        />
                        <div v-if="errors.email" class="absolute -bottom-6 left-0">
                          <p class="text-sm text-red-600 dark:text-red-400 font-medium">{{ errors.email }}</p>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Téléphone -->
                    <div>
                      <label for="phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                        Téléphone
                      </label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                          </svg>
                        </div>
                        <input
                          id="phone"
                          v-model="form.phone"
                          type="tel"
                          class="w-full pl-10 pr-4 py-4 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:text-white transition-all duration-300"
                          :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': errors.phone }"
                          placeholder="+229 XX XX XX XX"
                        />
                        <div v-if="errors.phone" class="absolute -bottom-6 left-0">
                          <p class="text-sm text-red-600 dark:text-red-400 font-medium">{{ errors.phone }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Section Adresse -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
                  <div class="flex items-center mb-6">
                    <div class="p-2 rounded-xl bg-gradient-to-r from-purple-100 to-pink-100 dark:from-purple-900/50 dark:to-pink-900/50 mr-3">
                      <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Adresse (optionnel)</h2>
                  </div>
                  
                  <div>
                    <div class="relative">
                      <textarea
                        id="address"
                        v-model="form.address"
                        rows="4"
                        class="w-full px-4 py-4 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:text-white transition-all duration-300 resize-none"
                        :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': errors.address }"
                        placeholder="Adresse complète du client...&#10;Rue, Ville, Code postal, Pays"
                      ></textarea>
                      <div v-if="errors.address" class="absolute -bottom-6 left-0">
                        <p class="text-sm text-red-600 dark:text-red-400 font-medium">{{ errors.address }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Boutons d'action -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
                  <div class="flex flex-col sm:flex-row justify-end space-y-4 sm:space-y-0 sm:space-x-4">
                    <Link 
                      :href="route('clients.index')"
                      class="w-full sm:w-auto px-8 py-4 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 text-center font-semibold hover:shadow-lg hover:-translate-y-0.5"
                    >
                      Annuler
                    </Link>
                    <button
                      type="submit"
                      :disabled="processing"
                      class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white rounded-xl transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed font-semibold shadow-lg hover:shadow-xl hover:-translate-y-1 group"
                    >
                      <span v-if="processing" class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Enregistrement...
                      </span>
                      <span v-else class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Enregistrer le client
                      </span>
                    </button>
                  </div>
                </div>
                
              </form>
            </div>
          </div>

          <!-- Aide et conseils -->
          <div class="mt-8 bg-gradient-to-r from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20 rounded-2xl p-6 border border-emerald-200 dark:border-emerald-800/50">
            <div class="flex items-start space-x-3">
              <div class="p-2 rounded-lg bg-emerald-100 dark:bg-emerald-900/50">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-emerald-800 dark:text-emerald-300 mb-2">💡 Conseils pour une meilleure gestion</h3>
                <ul class="text-sm text-emerald-700 dark:text-emerald-400 space-y-1">
                  <li>• Vérifiez l'adresse email pour éviter les erreurs d'envoi de factures</li>
                  <li>• Ajoutez le nom de l'entreprise pour les clients professionnels</li>
                  <li>• L'adresse complète facilite l'édition des factures</li>
                </ul>
              </div>
            </div>
          </div>
          
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ArrowLeftIcon } from '@heroicons/vue/24/outline'

// Formulaire réactif avec Inertia
const form = useForm({
  name: '',
  company: '',
  email: '',
  phone: '',
  address: '',
})

// Erreurs de validation
const errors = computed(() => form.errors)
const processing = computed(() => form.processing)

// Soumission du formulaire
function submit() {
  form.post(route('clients.store'), {
    onSuccess: () => {
      // Redirection automatique vers l'index après succès
    },
  })
}
</script>

<style>
@keyframes pulse {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 0.5; }
}
</style>