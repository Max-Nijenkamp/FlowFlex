<script setup lang="ts">
// Signature Switchboard+ control — 38×22 pill (sm: 32×19), white knob.
withDefaults(defineProps<{ on?: boolean; sm?: boolean; interactive?: boolean }>(), {
    on: false,
    sm: false,
    interactive: false,
})

defineEmits<{ toggle: [] }>()
</script>

<template>
    <component :is="interactive ? 'button' : 'span'" :type="interactive ? 'button' : undefined"
        :role="interactive ? 'switch' : undefined" :aria-checked="interactive ? on : undefined"
        class="relative inline-block shrink-0 rounded-full transition-colors ease-out duration-150"
        :class="[sm ? 'h-[19px] w-8' : 'h-[22px] w-[38px]', on ? 'bg-accent' : 'bg-[#E3E0D8]']"
        @click="interactive && $emit('toggle')">
        <span class="absolute top-[2px] rounded-full bg-white shadow-[0_1px_3px_rgba(17,24,39,0.3)]"
            :class="[sm ? 'h-[15px] w-[15px]' : 'h-[18px] w-[18px]', on ? 'right-[2px]' : 'left-[2px]']" />
    </component>
</template>
