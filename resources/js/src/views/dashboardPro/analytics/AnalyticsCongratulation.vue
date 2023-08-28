<template>
    <b-card
        v-if="data"
        class="card card-congratulations"
        text-variant="center"
    >
        <!-- images -->
        <b-img
            :src="require('@/assets/images/elements/decore-left.png')"
            class="congratulations-img-left"
        />
        <b-img
            :src="require('@/assets/images/elements/decore-right.png')"
            class="congratulations-img-right"
        />
        <!--/ images -->

        <b-avatar
            class="shadow mb-2"
            size="70"
            variant="primary"
        >
            <feather-icon
                icon="AwardIcon"
                size="28"
            />
        </b-avatar>
        <h1 class="mb-1 mt-50 text-white">
            Congratulations {{ data.name }},
        </h1>
        <b-card-text class="m-auto w-75">
            You have done <strong>{{ data.saleToday }}%</strong> more sales today. Check your new badge in your profile.
        </b-card-text>
    </b-card>
</template>

<script>
import {
    BCard, BImg, BAvatar, BCardText,
} from 'bootstrap-vue'

export default {
    components: {
        BCard,
        BAvatar,
        BImg,
        BCardText,
    },
    props: {
        data: {
            type: Object,
            default: () => {
            },
        },
    },
}
</script>
