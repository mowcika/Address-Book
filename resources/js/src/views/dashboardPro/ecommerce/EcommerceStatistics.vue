<template>
    <b-card
        v-if="data"
        class="card-statistics"
        no-body
    >
        <b-card-header>
            <b-card-title>Statistics</b-card-title>
            <b-card-text class="font-small-2 mr-25 mb-0">
                Updated 1 month ago
            </b-card-text>
        </b-card-header>
        <b-card-body class="statistics-body">
            <b-row>
                <b-col
                    v-for="item in data"
                    :key="item.icon"
                    :class="item.customClass"
                    sm="6"
                    xl="3"
                >
                    <b-media no-body>
                        <b-media-aside

                            class="mr-2"
                        >
                            <b-avatar
                                :variant="item.color"
                                size="48"
                            >
                                <feather-icon
                                    :icon="item.icon"
                                    size="24"
                                />
                            </b-avatar>
                        </b-media-aside>
                        <b-media-body class="my-auto">
                            <h4 class="font-weight-bolder mb-0">
                                {{ item.title }}
                            </h4>
                            <b-card-text class="font-small-3 mb-0">
                                {{ item.subtitle }}
                            </b-card-text>
                        </b-media-body>
                    </b-media>
                </b-col>
            </b-row>
        </b-card-body>
    </b-card>
</template>

<script>
import {
    BCard, BCardHeader, BCardTitle, BCardText, BCardBody, BRow, BCol, BMedia, BMediaAside, BAvatar, BMediaBody,
} from 'bootstrap-vue'

export default {
    components: {
        BRow,
        BCol,
        BCard,
        BCardHeader,
        BCardTitle,
        BCardText,
        BCardBody,
        BMedia,
        BAvatar,
        BMediaAside,
        BMediaBody,
    },
    props: {
        data: {
            type: Array,
            default: () => [],
        },
    },
}
</script>
