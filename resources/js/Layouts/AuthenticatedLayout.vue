<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation Header Professionnel -->
    <nav class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          
          <!-- Logo et Branding -->
          <div class="flex items-center space-x-4">
            <!-- Logo avec fallback -->
            <div class="flex-shrink-0">
              <Link :href="route('dashboard')" class="flex items-center space-x-3 group">
                <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-gradient-to-r from-emerald-600 to-green-600 text-white font-semibold text-lg shadow-sm group-hover:shadow-md transition-all" id="logo-fallback">
                  FZ
                </div>
                <img 
                  src="/images/WhatsApp Image 2025-08-06 at 01.20.20.jpeg" 
                  alt="FactureZen Logo" 
                  class="h-10 w-auto hidden" 
                  id="main-logo"
                  @load="showMainLogo"
                  @error="handleImageError"
                >
                <div class="hidden sm:block">
                  <h1 class="text-xl font-bold text-gray-900 group-hover:text-emerald-600 transition-colors">
                    FactureZen
                  </h1>
                </div>
              </Link>
            </div>
            
            <!-- Séparateur visuel -->
            <div class="hidden lg:block w-px h-6 bg-gray-300"></div>
            
            <!-- Navigation principale -->
            <div class="hidden lg:flex items-center space-x-1">
              <NavLink 
                :href="route('dashboard')" 
                :active="route().current('dashboard')"
                class="nav-item"
              >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 5v6m4-6v6m4-6v6" />
                </svg>
                <span>Tableau de bord</span>
              </NavLink>
              
              <NavLink 
                :href="route('clients.index')" 
                :active="route().current('clients.*')"
                class="nav-item"
              >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>Clients</span>
              </NavLink>
              
              <NavLink 
                :href="route('invoices.index')" 
                :active="route().current('invoices.*')"
                class="nav-item"
              >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Factures</span>
              </NavLink>
            </div>
          </div>

          <!-- Actions et Profil Utilisateur -->
          <div class="flex items-center space-x-3">
          
            <!-- Notifications (optionnel) -->

            <!-- Menu Utilisateur -->
            <div class="relative">
              <Dropdown align="right" width="56">
                <template #trigger>
                  <button class="flex items-center space-x-3 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                    <!-- Avatar -->
                    <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-r from-emerald-500 to-green-500 text-white text-sm font-semibold rounded-full shadow-sm">
                      {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="hidden sm:block text-left">
                      <div class="font-medium text-gray-900 truncate max-w-32">
                        {{ $page.props.auth.user.name }}
                      </div>
                    </div>
                    <svg class="hidden sm:block h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                </template>

                <template #content>
                  <div class="py-1">
                    <!-- Info utilisateur -->
                    <div class="px-4 py-3 border-b border-gray-100">
                      <p class="text-sm font-medium text-gray-900">{{ $page.props.auth.user.name }}</p>
                      <p class="text-xs text-gray-500 truncate">{{ $page.props.auth.user.email }}</p>
                    </div>
                    
                    <!-- Menu items -->
                    <DropdownLink :href="route('profile.edit')" class="dropdown-item">
                      <svg class="h-4 w-4 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                      Mon Profil
                    </DropdownLink>
                    
                    <div class="border-t border-gray-100 my-1"></div>
                    
                    <DropdownLink :href="route('logout')" method="post" as="button" class="dropdown-item text-red-600 hover:text-red-700 hover:bg-red-50">
                      <svg class="h-4 w-4 mr-3 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                      </svg>
                      Se déconnecter
                    </DropdownLink>
                  </div>
                </template>
              </Dropdown>
            </div>

            <!-- Menu Mobile -->
            <button
              @click="showingNavigationDropdown = !showingNavigationDropdown"
              class="lg:hidden flex items-center justify-center w-10 h-10 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300"
            >
              <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path
                  :class="{'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown}"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"
                />
                <path
                  :class="{'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown}"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Menu Mobile -->
      <div :class="{'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown}" class="lg:hidden border-t border-gray-200 bg-gray-50">
        <div class="px-4 py-3 space-y-1">
          <!-- CTA Mobile -->
          <Link 
            :href="route('invoices.create')"
            class="flex items-center justify-center w-full px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors mb-3"
          >
            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Nouvelle facture
          </Link>
          
          <!-- Navigation Mobile -->
          <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')" class="mobile-nav-item">
            <svg class="h-4 w-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
            </svg>
            Tableau de bord
          </ResponsiveNavLink>
          
          <ResponsiveNavLink :href="route('clients.index')" :active="route().current('clients.*')" class="mobile-nav-item">
            <svg class="h-4 w-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857" />
            </svg>
            Clients
          </ResponsiveNavLink>
          
          <ResponsiveNavLink :href="route('invoices.index')" :active="route().current('invoices.*')" class="mobile-nav-item">
            <svg class="h-4 w-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Factures
          </ResponsiveNavLink>
        </div>
      </div>
    </nav>

    <!-- Page Heading -->
    <header class="bg-white shadow" v-if="$slots.header">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <slot name="header" />
      </div>
    </header>

    <!-- Page Content -->
    <main>
      <slot />
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import NavLink from '@/Components/NavLink.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'

const showingNavigationDropdown = ref(false)

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

<style scoped>
/* Navigation Items */
.nav-item {
  @apply inline-flex items-center space-x-2 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200;
  @apply text-gray-600 hover:text-gray-900 hover:bg-gray-100;
  @apply focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2;
}

.nav-item.router-link-active {
  @apply text-emerald-700 bg-emerald-50 shadow-sm;
}

/* Dropdown Items */
.dropdown-item {
  @apply flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors;
  @apply focus:outline-none focus:bg-gray-50;
}

/* Mobile Navigation */
.mobile-nav-item {
  @apply flex items-center w-full px-3 py-2 text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-white rounded-lg transition-colors;
  @apply focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2;
}

.mobile-nav-item.router-link-active {
  @apply text-emerald-700 bg-white shadow-sm;
}

/* États de focus accessibles */
*:focus {
  @apply outline-none;
}

/* Transition fluides */
* {
  @apply transition-colors duration-200;
}
</style>