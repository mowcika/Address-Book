<template>
    <b-button
        :href="buttonLink"
        v-ripple.400="'rgba(113, 102, 240, 0.15)'"
        :variant="buttonColor"
    >
        <feather-icon
            :icon="buttonIcon"
            class="mr-50"
        />
        <span class="align-middle">{{ buttonText }}</span>
    </b-button>
</template>

<script>
import { BButton, } from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'

export default {
    name: 'add-button-icons-link',
    props: ['buttonText', 'buttonIcon', 'buttonColor', 'buttonLink'],
    components: {
        BButton,
    },
    directives: {
        Ripple,
    },
}
</script>
