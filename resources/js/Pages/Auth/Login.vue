<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm} from '@inertiajs/vue3';
import { ref, onUnmounted} from 'vue';
import axios from 'axios'
const countdown = ref(0); 
let timer = null;

defineProps({
  canResetPassword: {
    type: Boolean,
  },
  status: {
    type: String,
  },
});

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

function submit() {
  // Reset des erreurs précédentes
  form.clearErrors();
  
  axios.post('/login', {
    email: form.email,
    password: form.password,
    remember: form.remember
  })
  .then((response) => {
    // ✅ Connexion réussie
    if (response.data.success && response.data.redirect) {
      window.location.href = response.data.redirect;
    }
  })
  .catch((error) => {
    if (error.response) {
      // ✅ CORRECTION : Gestion du throttling (429)
      if (error.response.status === 429) {
        // Démarrer le countdown
        countdown.value = Number(error.response.data.retry_after) || 0;

        startCountdown();
        
        // Afficher l'erreur de throttling
        form.setError('email', error.response.data.errors.email?.[0] || 'Trop de tentatives');
      }
      // ✅ Erreurs de validation normales (422)
      else if (error.response.status === 422) {
        const errors = error.response.data.errors || {};
        
        // Appliquer toutes les erreurs au formulaire
        Object.keys(errors).forEach(field => {
          if (errors[field] && errors[field].length > 0) {
            const fieldError = errors[field];
            form.setError(field, Array.isArray(fieldError) ? fieldError[0] : fieldError);
          }
        });
      }
    }
  });
}
function startCountdown() {
  // Nettoyer le timer existant si nécessaire
  if (timer) {
    clearInterval(timer);
    timer = null;
  }
  
  if (countdown.value > 0) {
    timer = setInterval(() => {
      countdown.value--;
      
      if (countdown.value <= 0) {
        clearInterval(timer);
        timer = null;
        // Nettoyer l'erreur de throttling
        form.clearErrors('email');
      }
    }, 1000);
  }
}
function handleImageError() {
  const mainLogo = document.getElementById('main-logo');
  const fallback = document.getElementById('logo-fallback');
  if (mainLogo) mainLogo.classList.add('hidden');
  if (fallback) fallback.classList.remove('hidden');
}

function showMainLogo() {
  const mainLogo = document.getElementById('main-logo');
  const fallback = document.getElementById('logo-fallback');
  if (mainLogo) mainLogo.classList.remove('hidden');
  if (fallback) fallback.classList.add('hidden');
}
    onUnmounted(() => {
    if (timer) {
        clearInterval(timer);
    }
    });
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-green-50 dark:from-gray-900 dark:via-gray-800 dark:to-emerald-900 flex flex-col justify-center">
    <Head title="Connexion - FactureZen" />

    <!-- Header avec logo -->
    <div class="sm:mx-auto sm:w-full sm:max-w-md mb-8">
      <div class="flex justify-center items-center space-x-3">
        <div class="flex items-center justify-center h-12 w-12 rounded-xl bg-gradient-to-r from-emerald-600 to-green-600 text-white font-bold text-xl shadow-lg" id="logo-fallback">FZ</div>
        <img 
          src="/images/WhatsApp Image 2025-08-06 at 01.20.20.jpeg" 
          alt="FactureZen Logo" 
          class="h-12 w-auto hidden" 
          id="main-logo"
          @load="showMainLogo"
          @error="handleImageError"
        >
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Fac</h1>
          <p class="text-sm text-gray-600 dark:text-gray-400">Facturation Simplifiée</p>
        </div>
      </div>
    </div>

    <!-- Formulaire de connexion -->
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white dark:bg-gray-800 py-12 px-6 shadow-2xl rounded-3xl border border-green-100 dark:border-green-800/30 relative overflow-hidden">
        
        <div class="relative">
          <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Bienvenue !</h2>
            <p class="text-gray-600 dark:text-gray-400">Connectez-vous à votre compte FactureZen</p>
          </div>

          <div v-if="status" class="mb-6 p-4 text-sm font-medium text-green-800 bg-green-100 rounded-lg dark:bg-green-900/50 dark:text-green-200 border border-green-200 dark:border-green-800">
            {{ status }}
          </div>

          <form @submit.prevent="submit" class="space-y-6">
            <div>
              <InputLabel for="email" value="Adresse email" class="text-gray-700 dark:text-gray-300 font-medium" />
              <TextInput
                id="email"
                type="email"
                class="mt-2 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-300 dark:focus:ring-emerald-400 dark:focus:border-emerald-400 transition-all duration-200"
                v-model="form.email"
                required
                autofocus
                autocomplete="username"
                placeholder="votre@email.com"
              />
              <!-- Utilisation de .[0] pour passer une string -->
              <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
              <InputLabel for="password" value="Mot de passe" class="text-gray-700 dark:text-gray-300 font-medium" />
              <TextInput
                id="password"
                type="password"
                class="mt-2 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-300 dark:focus:ring-emerald-400 dark:focus:border-emerald-400 transition-all duration-200"
                v-model="form.password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
              />
              <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between">
              <label class="flex items-center">
                <Checkbox 
                  name="remember" 
                  v-model:checked="form.remember"
                  class="rounded border-gray-300 text-emerald-600 shadow-sm focus:border-emerald-300 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:focus:border-emerald-400"
                />
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Se souvenir de moi</span>
              </label>

              <Link
                v-if="canResetPassword"
                :href="route('password.request')"
                class="text-sm text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300 font-medium transition-colors duration-200"
              >
                Mot de passe oublié ?
              </Link>
            </div>

            <div class="space-y-4">
              <PrimaryButton
                class="w-full justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing || countdown> 0"
              > 
                <span v-if="countdown > 0">Ressayer dans {{ countdown}}s</span> 
                <span v-else-if="form.processing" class="flex items-center justify-center">
                  <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Connexion...
                </span>
                <span v-else>Se connecter</span>
              </PrimaryButton>
              
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="mt-12 text-center">
      <p class="text-sm text-gray-500 dark:text-gray-400">© 2024 FactureZen. Tous droits réservés.</p>
    </div>
  </div>
</template>
