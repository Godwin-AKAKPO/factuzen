<template>
  <div>
    <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 mb-2">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    
    <div class="relative">
      <input
        :id="id"
        v-model="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :type="type"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :class="[
          'w-full px-3 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors',
          error ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300',
          disabled ? 'bg-gray-100 cursor-not-allowed' : 'bg-white'
        ]"
      />
      
      <div v-if="icon" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <component :is="icon" class="h-5 w-5 text-gray-400" />
      </div>
    </div>
    
    <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    <p v-else-if="help" class="mt-1 text-sm text-gray-500">{{ help }}</p>
  </div>
</template>

<script setup>
defineProps({
  id: String,
  label: String,
  type: {
    type: String,
    default: 'text',
  },
  modelValue: [String, Number],
  placeholder: String,
  required: Boolean,
  disabled: Boolean,
  error: String,
  help: String,
  icon: Object,
})

defineEmits(['update:modelValue'])
</script>