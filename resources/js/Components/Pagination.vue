<template>
  <nav class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 rounded-lg shadow">
    <div class="hidden sm:block">
      <p class="text-sm text-gray-700">
        Affichage de
        <span class="font-medium">{{ links.from || 0 }}</span>
        à
        <span class="font-medium">{{ links.to || 0 }}</span>
        sur
        <span class="font-medium">{{ links.total || 0 }}</span>
        résultats
      </p>
    </div>
    <div class="flex flex-1 justify-between sm:justify-end">
      <div class="flex space-x-2">
        <Link
          v-for="(link, index) in links.data"
          :key="index"
          :href="link.url"
          v-html="link.label"
          :class="[
            'px-3 py-2 text-sm border rounded-md transition-colors',
            link.active
              ? 'bg-blue-600 text-white border-blue-600'
              : link.url
              ? 'text-gray-700 border-gray-300 hover:bg-gray-50'
              : 'text-gray-400 border-gray-300 cursor-not-allowed'
          ]"
          :disabled="!link.url"
        />
      </div>
    </div>
  </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  links: Object,
})
</script>