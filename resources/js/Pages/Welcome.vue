<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    // Cache le logo principal et affiche le fallback
    const mainLogo = document.getElementById('main-logo');
    const fallback = document.getElementById('logo-fallback');
    if (mainLogo) mainLogo.classList.add('hidden');
    if (fallback) fallback.classList.remove('hidden');
}

function showMainLogo() {
    // Affiche le logo principal et cache le fallback
    const mainLogo = document.getElementById('main-logo');
    const fallback = document.getElementById('logo-fallback');
    if (mainLogo) mainLogo.classList.remove('hidden');
    if (fallback) fallback.classList.add('hidden');
}
</script>

<template>
    <Head title="FactuZen - Gestion de Facturation Simplifiée" />
    <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-green-50 dark:from-gray-900 dark:via-gray-800 dark:to-emerald-900">
        <!-- Navigation Header FIXÉ -->
        <header class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-200 dark:bg-gray-900 dark:border-gray-700 shadow-sm">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between py-4 lg:py-6">
                    <!-- Logo et nom -->
                    <div class="flex items-center space-x-3">
                        <!-- Logo FactureZen avec fallback -->
                        <div class="flex items-center justify-center h-10 w-10 lg:h-12 lg:w-12 rounded-xl bg-gradient-to-r from-emerald-600 to-green-600 text-white font-bold text-lg lg:text-xl shadow-lg" id="logo-fallback">
                            FZ
                        </div>
                        <img 
                            src="/images/WhatsApp Image 2025-08-06 at 01.20.20.jpeg" 
                            alt="FactureZen Logo" 
                            class="h-10 lg:h-12 w-auto hidden" 
                            id="main-logo"
                            @load="showMainLogo"
                            @error="handleImageError"
                        >
                        <div>
                            <h1 class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white">FactureZen</h1>
                            <p class="text-xs lg:text-sm text-gray-600 dark:text-gray-400">Facturation Simplifiée</p>
                        </div>
                    </div>

                    <!-- Navigation buttons -->
                    <nav v-if="canLogin" class="flex items-center space-x-4">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-all hover:bg-emerald-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                        >
                            Dashboard
                        </Link>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-all hover:bg-gray-50 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                            >
                                Se connecter
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-all hover:bg-emerald-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                            >
                                S'enregistrer
                            </Link>
                        </template>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Hero Section avec padding-top pour compenser le header fixe -->
        <main class="relative pt-20 lg:pt-28">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-12 lg:grid-cols-2 lg:gap-16 py-12 lg:py-20">
                    <!-- Contenu principal -->
                    <div class="flex flex-col justify-center">
                        <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl dark:text-white">
                            Gérez vos 
                            <span class="bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                                factures
                            </span> 
                            en toute simplicité
                        </h1>
                        
                        <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300">
                            FactureZen révolutionne la gestion de vos factures avec une interface intuitive, 
                            des fonctionnalités avancées et une automatisation intelligente. Créez, envoyez 
                            et suivez vos factures en quelques clics.
                        </p>

                        <div class="mt-10 flex flex-col sm:flex-row gap-4">
                            <Link
                                v-if="!$page.props.auth.user"
                                :href="route('register')"
                                class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-emerald-600 to-green-600 px-8 py-3 text-base font-semibold text-white shadow-lg transition-all hover:from-emerald-700 hover:to-green-700 hover:shadow-xl hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                            >
                                Commencer maintenant
                                <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </Link>
                            <!-- <button class="inline-flex items-center justify-center rounded-lg border-2 border-emerald-300 bg-white px-8 py-3 text-base font-semibold text-emerald-700 transition-all hover:bg-emerald-50 hover:border-emerald-400 hover:shadow-lg dark:border-emerald-600 dark:bg-gray-800 dark:text-emerald-300 dark:hover:border-emerald-500 dark:hover:bg-emerald-900/20">
                                <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293H15M9 10V9a2 2 0 012-2h2a2 2 0 012 2v1M9 10v5a2 2 0 002 2h2a2 2 0 002-2v-5" />
                                </svg>
                                Voir la démo
                            </button> -->
                        </div>

                        <!-- Stats améliorées -->
                        <div class="mt-12 grid grid-cols-3 gap-8">
                            <div class="text-center group">
                                <div class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent group-hover:from-emerald-600 group-hover:to-green-600 transition-all duration-300">1000+</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 font-medium">Clients satisfaits</div>
                            </div>
                            <div class="text-center group">
                                <div class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent group-hover:from-emerald-600 group-hover:to-green-600 transition-all duration-300">25k+</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 font-medium">Factures créées</div>
                            </div>
                            <div class="text-center group">
                                <div class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent group-hover:from-emerald-600 group-hover:to-green-600 transition-all duration-300">99.9%</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 font-medium">Disponibilité</div>
                            </div>
                        </div>
                    </div>

                    <!-- Image/Visual améliorée -->
                    <div class="relative flex items-center justify-center">
                        <div class="relative">
                            <!-- Decorative elements avec animation -->
                            <div class="absolute -top-6 -left-6 h-80 w-80 rounded-full bg-gradient-to-r from-green-200 to-emerald-200 opacity-30 blur-2xl animate-pulse dark:from-green-800 dark:to-emerald-800"></div>
                            <div class="absolute -bottom-6 -right-6 h-80 w-80 rounded-full bg-gradient-to-r from-emerald-200 to-green-200 opacity-30 blur-2xl animate-pulse animation-delay-1000 dark:from-emerald-800 dark:to-green-800"></div>
                            
                            <!-- Main visual améliorée -->
                            <div id="hero-image" class="relative z-10 rounded-3xl bg-white p-10 shadow-2xl border border-gray-100 dark:bg-gray-800 dark:border-gray-700 transform hover:scale-105 transition-transform duration-300">
                                <div class="space-y-6">
                                    <!-- Mock invoice header -->
                                    <div class="flex items-center justify-between">
                                        <div class="h-10 w-32 rounded-lg bg-gradient-to-r from-emerald-500 to-green-500 shadow-sm"></div>
                                        <div class="text-right space-y-2">
                                            <div class="h-4 w-24 rounded bg-gray-300 dark:bg-gray-600"></div>
                                            <div class="h-3 w-20 rounded bg-gray-200 dark:bg-gray-700"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Mock invoice content -->
                                    <div class="border-t pt-6 dark:border-gray-700">
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                <div class="h-3 w-40 rounded bg-gray-300 dark:bg-gray-600"></div>
                                                <div class="h-3 w-20 rounded bg-green-500"></div>
                                            </div>
                                            <div class="flex justify-between items-center p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                <div class="h-3 w-36 rounded bg-gray-300 dark:bg-gray-600"></div>
                                                <div class="h-3 w-18 rounded bg-green-500"></div>
                                            </div>
                                            <div class="flex justify-between items-center p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                <div class="h-3 w-44 rounded bg-gray-300 dark:bg-gray-600"></div>
                                                <div class="h-3 w-22 rounded bg-green-500"></div>
                                            </div>
                                        </div>
                                        
                                        <!-- Total section -->
                                        <div class="mt-6 border-t pt-4 dark:border-gray-700 bg-gradient-to-r from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20 rounded-lg p-4">
                                            <div class="flex justify-between items-center">
                                                <div class="h-5 w-24 rounded bg-gradient-to-r from-emerald-600 to-green-600"></div>
                                                <div class="h-5 w-28 rounded bg-gradient-to-r from-emerald-600 to-green-600"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Section améliorée -->
            <div class="bg-gradient-to-r from-green-50/80 via-white/80 to-emerald-50/80 py-24 dark:from-gray-800/80 dark:via-gray-800/60 dark:to-emerald-900/20 backdrop-blur-sm">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-20">
                        <h2 class="text-4xl font-bold text-gray-900 sm:text-5xl dark:text-white mb-4">
                            Tout ce dont vous avez besoin
                        </h2>
                        <div class="w-24 h-1 bg-gradient-to-r from-emerald-600 to-green-600 mx-auto mb-6"></div>
                        <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                            Des fonctionnalités puissantes conçues pour simplifier votre gestion de facturation et faire croître votre entreprise
                        </p>
                    </div>

                    <div class="grid gap-8 lg:grid-cols-3">
                        <!-- Feature 1 -->
                        <div class="group rounded-3xl bg-white p-10 shadow-lg transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 dark:bg-gray-800 border border-green-100 dark:border-green-800/30">
                            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-green-100 to-emerald-100 text-green-600 dark:from-green-900/50 dark:to-emerald-900/50 dark:text-green-400 group-hover:scale-110 transition-transform duration-300">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <h3 class="mt-8 text-2xl font-bold text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">
                                Création Ultra-Rapide
                            </h3>
                            <p class="mt-4 text-gray-600 dark:text-gray-300 text-lg leading-relaxed">
                                Créez des factures professionnelles en moins de 30 secondes avec nos modèles intelligents et personnalisables.
                            </p>
                            <!-- <div class="mt-6 flex items-center text-green-600 dark:text-green-400 font-semibold group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                <span class="text-sm">En savoir plus</span>
                                <svg class="ml-2 h-4 w-4 group-hover:translate-x-2 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </div> -->
                        </div>

                        <!-- Feature 2 -->
                        <div class="group rounded-3xl bg-white p-10 shadow-lg transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 dark:bg-gray-800 border border-green-100 dark:border-green-800/30">
                            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-emerald-100 to-green-100 text-emerald-600 dark:from-emerald-900/50 dark:to-green-900/50 dark:text-emerald-400 group-hover:scale-110 transition-transform duration-300">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <h3 class="mt-8 text-2xl font-bold text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                Suivi Intelligent
                            </h3>
                            <p class="mt-4 text-gray-600 dark:text-gray-300 text-lg leading-relaxed">
                                Automatisez vos relances, suivez vos paiements en temps réel et analysez vos performances avec des tableaux de bord avancés.
                            </p>
                            <!-- <div class="mt-6 flex items-center text-emerald-600 dark:text-emerald-400 font-semibold group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">
                                <span class="text-sm">En savoir plus</span>
                                <svg class="ml-2 h-4 w-4 group-hover:translate-x-2 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </div> -->
                        </div>

                        <!-- Feature 3 -->
                        <div class="group rounded-3xl bg-white p-10 shadow-lg transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 dark:bg-gray-800 border border-green-100 dark:border-green-800/30">
                            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-green-100 to-emerald-100 text-green-600 dark:from-green-900/50 dark:to-emerald-900/50 dark:text-green-400 group-hover:scale-110 transition-transform duration-300">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <h3 class="mt-8 text-2xl font-bold text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">
                                Sécurité Bancaire
                            </h3>
                            <p class="mt-4 text-gray-600 dark:text-gray-300 text-lg leading-relaxed">
                                Protection maximale avec chiffrement SSL, authentification à deux facteurs et sauvegardes automatiques quotidiennes.
                            </p>
                            <!-- <div class="mt-6 flex items-center text-green-600 dark:text-green-400 font-semibold group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                <span class="text-sm">En savoir plus</span>
                                <svg class="ml-2 h-4 w-4 group-hover:translate-x-2 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA Section améliorée -->
            <div class="py-20 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-600/10 via-green-600/5 to-emerald-600/10"></div>
                <div class="mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8 relative">
                    <h2 class="text-4xl font-bold text-gray-900 sm:text-5xl dark:text-white mb-6">
                        Prêt à 
                        <span class="bg-gradient-to-r from-emerald-600 to-green-600 bg-clip-text text-transparent">révolutionner</span> 
                        votre facturation ?
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300 mb-4 max-w-2xl mx-auto">
                        Rejoignez les milliers d'entrepreneurs qui font confiance à FactureZen pour gérer leur facturation
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <Link
                            v-if="!$page.props.auth.user"
                            :href="route('register')"
                            class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-emerald-600 to-green-600 px-10 py-4 text-lg font-bold text-white shadow-xl transition-all hover:from-emerald-700 hover:to-green-700 hover:shadow-2xl hover:-translate-y-2 focus:outline-none focus:ring-4 focus:ring-emerald-500 focus:ring-offset-2 group"
                        >
                            Commencer maintenant
                            <svg class="ml-3 h-6 w-6 group-hover:translate-x-2 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </Link>
                        
                        <div class="flex items-center text-gray-500 text-sm">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            100% sécurisé et confidentiel
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer amélioré -->
        <footer class="border-t border-gray-200 bg-gradient-to-r from-white/90 via-green-50/50 to-white/90 py-12 backdrop-blur-sm dark:border-gray-700 dark:from-gray-800/90 dark:via-green-900/20 dark:to-gray-800/90">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <div class="flex justify-center items-center mb-4">
                        <div class="w-8 h-1 bg-gradient-to-r from-emerald-600 to-green-600"></div>
                        <p class="mx-4 text-lg font-semibold text-gray-700 dark:text-gray-300">FactureZen</p>
                        <div class="w-8 h-1 bg-gradient-to-r from-green-600 to-emerald-600"></div>
                    </div>
                    <!-- <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                        FactureZen v{{ laravelVersion }} - Propulsé par Laravel (PHP v{{ phpVersion }})
                    </p> -->
                    <p class="text-xs text-gray-500 dark:text-gray-500">
                        © 2024 FactureZen. Tous droits réservés. 
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style>
@keyframes pulse {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 0.5; }
}

.animation-delay-1000 {
    animation-delay: 1s;
}
</style>