<template>
    <b-row>
        <b-col cols="12">
            <govt-employer-table/>
        </b-col>
    </b-row>
</template>

<script>
import { BRow, BCol } from 'bootstrap-vue'
import GovtEmployerTable from './FontSizeMasterTable.vue'

export default {
    components: {
        BRow,
        BCol,
        GovtEmployerTable,
    },
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-good-table.scss';
</style>
