<template>
  <div>
    <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 mb-2">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    
    <select
      :id="id"
      v-model="modelValue"
      @change="$emit('update:modelValue', $event.target.value)"
      :required="required"
      :disabled="disabled"
      :class="[
        'w-full px-3 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors',
        error ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300',
        disabled ? 'bg-gray-100 cursor-not-allowed' : 'bg-white'
      ]"
    >
      <option v-if="placeholder" value="">{{ placeholder }}</option>
      <option 
        v-for="option in options" 
        :key="option.value" 
        :value="option.value"
      >
        {{ option.label }}
      </option>
    </select>
    
    <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    <p v-else-if="help" class="mt-1 text-sm text-gray-500">{{ help }}</p>
  </div>
</template>

<script setup>
defineProps({
  id: String,
  label: String,
  modelValue: [String, Number],
  options: Array,
  placeholder: String,
  required: Boolean,
  disabled: Boolean,
  error: String,
  help: String,
})

defineEmits(['update:modelValue'])
</script>