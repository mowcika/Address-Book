<template>
    <b-row>
        <b-col cols="12">
            <private-employer-personal/>

        </b-col>
    </b-row>
</template>

<script>
import { BRow, BCol } from 'bootstrap-vue'
import PrivateEmployerPersonal from './PrivateEmployerPersonal.vue'
import PrivateEmployerCompany from './PrivateEmployerCompany.vue'

export default {

    components: {
        BRow,
        BCol,
        PrivateEmployerPersonal,
        PrivateEmployerCompany,
    },

    methods: {},
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-good-table.scss';
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>
