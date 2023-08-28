<template>
    <div class="demo-spacing-0">
        <b-alert
            variant="primary"
            show
        >
            <div class="alert-body text-center">
                <span>Total Results : <strong>{{countText}}</strong></span>
            </div>
        </b-alert>
    </div>
</template>

<script>
import { BAlert, BCardText } from 'bootstrap-vue'

export default {
    name: 'total-results',
    props: ['countText'],
    components: {
        BAlert,
        BCardText,
    },
}
</script>
