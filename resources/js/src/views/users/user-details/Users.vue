<template>
    <b-row class="match-height">
        <b-col cols="12">
            <form-layout-vertical-icon/>
        </b-col>
    </b-row>
</template>

<script>
import { BRow, BCol } from 'bootstrap-vue'
import FormLayoutVerticalIcon from './FormLayoutVerticalIcon.vue'
import { InertiaProgress } from '@inertiajs/progress'

InertiaProgress.init()

export default {
    components: {
        BRow,
        BCol,
        FormLayoutVerticalIcon,
    },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-good-table.scss';
</style>
