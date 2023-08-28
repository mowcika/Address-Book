<template>
    <b-button
        v-b-modal.modal-addForm
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
import { BButton } from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'

export default {
    name: 'add-button-icons',
    props: ['buttonText', 'buttonIcon', 'buttonColor'],
    components: {
        BButton,
    },
    directives: {
        Ripple,
    },
}
</script>
