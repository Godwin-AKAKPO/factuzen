<template>
  <div :class="containerClass">
    <div :class="spinnerClass">
      <svg class="animate-spin h-full w-full text-current" fill="none" viewBox="0 0 24 24">
        <circle 
          class="opacity-25" 
          cx="12" 
          cy="12" 
          r="10" 
          stroke="currentColor" 
          stroke-width="4"
        />
        <path 
          class="opacity-75" 
          fill="currentColor" 
          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
        />
      </svg>
    </div>
    <span v-if="text" :class="textClass">{{ text }}</span>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
  },
  color: {
    type: String,
    default: 'blue',
  },
  text: String,
  center: Boolean,
})

const containerClass = computed(() => {
  return [
    'flex items-center',
    props.center ? 'justify-center' : '',
    props.text ? 'space-x-2' : '',
  ]
})

const spinnerClass = computed(() => {
  const sizes = {
    xs: 'w-4 h-4',
    sm: 'w-6 h-6',
    md: 'w-8 h-8',
    lg: 'w-12 h-12',
    xl: 'w-16 h-16',
  }
  
  const colors = {
    blue: 'text-blue-600',
    green: 'text-green-600',
    red: 'text-red-600',
    gray: 'text-gray-600',
  }
  
  return [
    sizes[props.size],
    colors[props.color] || colors.blue,
  ]
})

const textClass = computed(() => {
  const colors = {
    blue: 'text-blue-600',
    green: 'text-green-600',
    red: 'text-red-600',
    gray: 'text-gray-600',
  }
  
  return [
    'text-sm font-medium',
    colors[props.color] || colors.blue,
  ]
})
</script>