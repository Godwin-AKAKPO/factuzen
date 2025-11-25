<template>
  <AuthenticatedLayout>
    <Head title="Nouveau client" />
    
    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6">
          <Link 
            :href="route('clients.index')"
            class="text-blue-600 hover:text-blue-800 text-sm font-medium mb-2 inline-flex items-center"
          >
            <ArrowLeftIcon class="w-4 h-4 mr-1" />
            Retour aux clients
          </Link>
          <h1 class="text-2xl font-bold text-gray-900">Nouveau client</h1>
          <p class="text-gray-600">Ajoutez un nouveau client à votre carnet d'adresses</p>
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
            
            <!-- Boutons -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
              <Link 
                @click="goBack"
                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
              >
                Annuler
              </Link>
              <button
                type="submit"
                :disabled="processing"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50"
              >
                <span v-if="processing">Enregistrement...</span>
                <span v-else>Enregistrer</span>
              </button>
            </div>
            
          </form>
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

//Methode
//Fonction Annuler
function goBack(){
  window.history.back()
}

// Soumission du formulaire
function submit() {
  form.post(route('clients.store'), {
    onSuccess: () => {
      // Redirection automatique vers l'index après succès
    },
  })
}
</script>