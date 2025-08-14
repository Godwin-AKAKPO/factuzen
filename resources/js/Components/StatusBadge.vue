<template>
  <span :class="badgeClasses" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
    {{ statusText }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: String,
  isOverdue: {
    type: Boolean,
    default: false,
  }
})

const statusText = computed(() => {
  if (props.isOverdue) return 'En retard'
  
  const statusMap = {
    draft: 'Brouillon',
    sent: 'Envoyée',
    paid: 'Payée',
    overdue: 'En retard',
    cancelled: 'Annulée',
  }
  return statusMap[props.status] || props.status
})

const badgeClasses = computed(() => {
  if (props.isOverdue || props.status === 'overdue') {
    return 'bg-red-100 text-red-800'
  }
  
  const classMap = {
    draft: 'bg-gray-100 text-gray-800',
    sent: 'bg-blue-100 text-blue-800',
    paid: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
  }
  return classMap[props.status] || 'bg-gray-100 text-gray-800'
})
</script>