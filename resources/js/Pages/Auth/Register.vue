<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

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
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-green-50 dark:from-gray-900 dark:via-gray-800 dark:to-emerald-900 flex flex-col justify-center py-12">
        <Head title="Inscription - FactureZen" />
        
        <!-- Header avec logo -->
        <div class="sm:mx-auto sm:w-full sm:max-w-md mb-8">
            <div class="flex justify-center items-center space-x-3">
                <!-- Logo FactureZen avec fallback -->
                <div class="flex items-center justify-center h-12 w-12 rounded-xl bg-gradient-to-r from-emerald-600 to-green-600 text-white font-bold text-xl shadow-lg" id="logo-fallback">
                    FZ
                </div>
                <img 
                    src="/images/WhatsApp Image 2025-08-06 at 01.20.20.jpeg" 
                    alt="FactureZen Logo" 
                    class="h-12 w-auto hidden" 
                    id="main-logo"
                    @load="showMainLogo"
                    @error="handleImageError"
                >
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">FactureZen</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Facturation Simplifiée</p>
                </div>
            </div>
        </div>

        <!-- Formulaire d'inscription -->
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white dark:bg-gray-800 py-12 px-6 shadow-2xl rounded-3xl border border-green-100 dark:border-green-800/30 relative overflow-hidden">
                <!-- Éléments décoratifs -->
                <div class="absolute -top-6 -left-6 h-32 w-32 rounded-full bg-gradient-to-r from-green-200 to-emerald-200 opacity-20 blur-2xl dark:from-green-800 dark:to-emerald-800"></div>
                <div class="absolute -bottom-6 -right-6 h-32 w-32 rounded-full bg-gradient-to-r from-emerald-200 to-green-200 opacity-20 blur-2xl dark:from-emerald-800 dark:to-green-800"></div>
                
                <div class="relative">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                            Créer votre compte
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400">
                            Rejoignez FactureZen et simplifiez votre facturation
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <InputLabel for="name" value="Nom complet" class="text-gray-700 dark:text-gray-300 font-medium" />
                            
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-2 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-300 dark:focus:ring-emerald-400 dark:focus:border-emerald-400 transition-all duration-200"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="Jean Dupont"
                            />
                            
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="email" value="Adresse email" class="text-gray-700 dark:text-gray-300 font-medium" />
                            
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-2 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-300 dark:focus:ring-emerald-400 dark:focus:border-emerald-400 transition-all duration-200"
                                v-model="form.email"
                                required
                                autocomplete="username"
                                placeholder="jean@exemple.com"
                            />
                            
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
                                autocomplete="new-password"
                                placeholder="••••••••"
                            />
                            
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div>
                            <InputLabel
                                for="password_confirmation"
                                value="Confirmer le mot de passe"
                                class="text-gray-700 dark:text-gray-300 font-medium"
                            />
                            
                            <TextInput
                                id="password_confirmation"
                                type="password"
                                class="mt-2 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-300 dark:focus:ring-emerald-400 dark:focus:border-emerald-400 transition-all duration-200"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="••••••••"
                            />
                            
                            <InputError
                                class="mt-2"
                                :message="form.errors.password_confirmation"
                            />
                        </div>

                        <div class="space-y-4">
                            <PrimaryButton
                                class="w-full justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5"
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing" class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Création du compte...
                                </span>
                                <span v-else class="flex items-center justify-center">
                                    <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    Créer mon compte 
                                </span>
                            </PrimaryButton>
                            

                        </div>
                    </form>

                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-center text-sm text-gray-600 dark:text-gray-400">
                            Déjà un compte ?
                            <Link
                                :href="route('login')"
                                class="font-medium text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300 transition-colors duration-200 ml-1"
                            >
                                Se connecter
                            </Link>
                        </p>
                    </div>
                    
                    <!-- Retour à l'accueil -->
                    <div class="mt-4 text-center">
                        <Link
                            :href="route('welcome')"
                            class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200"
                        >
                            <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Retour à l'accueil
                        </Link>
                    </div>

                    <!-- Mentions légales -->
                    <div class="mt-6 text-center">
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            En créant un compte, vous acceptez nos 
                            <a href="#" class="text-emerald-600 hover:text-emerald-500 dark:text-emerald-400">conditions d'utilisation</a>
                            et notre 
                            <a href="#" class="text-emerald-600 hover:text-emerald-500 dark:text-emerald-400">politique de confidentialité</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-12 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                © 2024 FactureZen. Tous droits réservés.
            </p>
        </div>
    </div>
</template>