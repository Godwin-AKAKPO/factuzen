<template>
  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
      <div class="flex items-center justify-between">
        <div>
          <dt class="text-sm font-medium text-gray-500 truncate">{{ title }}</dt>
          <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ value }}</dd>
        </div>
        <div class="flex-shrink-0">
          <div :class="iconClasses">
            <component :is="iconComponent" class="w-6 h-6" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { 
  UsersIcon, 
  DocumentTextIcon, 
  ClockIcon, 
  CurrencyDollarIcon 
} from '@heroicons/vue/24/outline'

const props = defineProps({
  title: String,
  value: [String, Number],
  icon: String,
  color: String,
})

const iconComponent = computed(() => {
  const icons = {
    'Users': UsersIcon,
    'FileText': DocumentTextIcon,
    'Clock': ClockIcon,
    'DollarSign': CurrencyDollarIcon,
  }
  return icons[props.icon] || DocumentTextIcon
})

const iconClasses = computed(() => {
  const colors = {
    blue: 'bg-blue-100 text-blue-600',
    green: 'bg-green-100 text-green-600',
    orange: 'bg-orange-100 text-orange-600',
    purple: 'bg-purple-100 text-purple-600',
  }
  return `p-3 rounded-lg ${colors[props.color] || colors.blue}`
})
</script>