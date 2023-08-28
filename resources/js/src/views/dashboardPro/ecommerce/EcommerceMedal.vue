<template>
    <b-card
        v-if="data"
        class="card-congratulation-medal"
    >
        <h5>Welcome 🎉 {{ data.name }}!</h5>
        <b-card-text class="font-small-3 ">
            Today Payment - <span class="text-bold text-danger font-small-4"> {{ TodayDate }} </span>
        </b-card-text>
        <h3 class="mb-75 mt-2 pt-50">
            <b-link>₹{{ todayPaymentAmt }}</b-link>
        </h3>

        <b-img
            :src="require('@/assets/images/illustration/badge.svg')"
            alt="Medal Pic"
            class="congratulation-medal"
        />
    </b-card>
</template>

<script>
import {
    BCard, BCardText, BLink, BButton, BImg,
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import { kFormatter } from '@core/utils/filter'
import { alpha, regex, required } from '@validations'
import VueMoment from 'vue-moment'
import moment from 'moment-timezone'

export default {
    components: {
        BCard,
        BCardText,
        BLink,
        BImg,
        BButton,
    },
    directives: {
        Ripple,
    },
    data() {
        return {

            todayPaymentAmt: 0,
            TodayDate: '',

        }
    },
    created() {
        this.todayPayment()
    },
    props: {
        data: {
            type: Object,
            default: () => {
            },

        },
    },
    methods: {
        todayPayment() {
            // alert('here');
            axios.post('/todayPayment', '')
                .then(res => {
                    // console.log(res.data)
                    this.todayPaymentAmt = res.data[0].todayPayment
                    this.TodayDate = moment(new Date().toLocaleString())
                        .format('DD/MM/YYYY')
                })
        },
    },
}
</script>
