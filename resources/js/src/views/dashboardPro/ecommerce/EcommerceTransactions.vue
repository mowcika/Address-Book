<template>
    <b-card
        v-if="data"
        class="card-transaction"
        no-body
    >
        <b-card-header>
            <b-card-title>Transactions</b-card-title>

            <b-dropdown
                class="chart-dropdown"
                no-caret
                right
                toggle-class="p-0"
                variant="link"
            >
                <template #button-content>
                    <feather-icon
                        class="text-body cursor-pointer"
                        icon="MoreVerticalIcon"
                        size="18"
                    />
                </template>
                <b-dropdown-item href="#">
                    Last 28 Days
                </b-dropdown-item>
                <b-dropdown-item href="#">
                    Last Month
                </b-dropdown-item>
                <b-dropdown-item href="#">
                    Last Year
                </b-dropdown-item>
            </b-dropdown>
        </b-card-header>

        <b-card-body>
            <div
                v-for="transaction in data"
                :key="transaction.mode"
                class="transaction-item"
            >
                <b-media no-body>
                    <b-media-aside>
                        <b-avatar
                            :variant="transaction.avatarVariant"
                            rounded
                            size="42"
                        >
                            <feather-icon
                                :icon="transaction.avatar"
                                size="18"
                            />
                        </b-avatar>
                    </b-media-aside>
                    <b-media-body>
                        <h6 class="transaction-title">
                            {{ transaction.mode }}
                        </h6>
                        <small>{{ transaction.types }}</small>
                    </b-media-body>
                </b-media>
                <div
                    :class="transaction.deduction ? 'text-danger':'text-success'"
                    class="font-weight-bolder"
                >
                    {{ transaction.payment }}
                </div>
            </div>
        </b-card-body>
    </b-card>
</template>

<script>
import {
    BCard, BCardHeader, BCardTitle, BCardBody, BMediaBody, BMedia, BMediaAside, BAvatar, BDropdown, BDropdownItem,
} from 'bootstrap-vue'

export default {
    components: {
        BCard,
        BCardHeader,
        BCardTitle,
        BCardBody,
        BMediaBody,
        BMedia,
        BMediaAside,
        BAvatar,
        BDropdown,
        BDropdownItem,
    },
    props: {
        data: {
            type: Array,
            default: () => [],
        },
    },
}
</script>
