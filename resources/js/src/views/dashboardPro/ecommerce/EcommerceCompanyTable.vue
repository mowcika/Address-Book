<template>
    <b-card
        v-if="tableData"
        class="card-company-table"
        no-body
    >
        <b-table
            :fields="fields"
            :items="tableData"
            class="mb-0"
            responsive
        >
            <!-- company -->
            <template #cell(company)="data">
                <div class="d-flex align-items-center">
                    <b-avatar
                        rounded
                        size="32"
                        variant="light-company"
                    >
                        <b-img
                            :src="data.item.avatarImg"
                            alt="avatar img"
                        />
                    </b-avatar>
                    <div>
                        <div class="font-weight-bolder">
                            {{ data.item.title }}
                        </div>
                        <div class="font-small-2 text-muted">
                            {{ data.item.subtitle }}
                        </div>
                    </div>
                </div>
            </template>

            <!-- category -->
            <template #cell(category)="data">
                <div class="d-flex align-items-center">
                    <b-avatar
                        :variant="data.item.avatarColor"
                        class="mr-1"
                    >
                        <feather-icon
                            :icon="data.item.avatarIcon"
                            size="18"
                        />
                    </b-avatar>
                    <span>{{ data.item.avatarTitle }}</span>
                </div>
            </template>

            <!-- views -->
            <template #cell(views)="data">
                <div class="d-flex flex-column">
                    <span class="font-weight-bolder mb-25">{{ data.item.viewTitle }}</span>
                    <span class="font-small-2 text-muted text-nowrap">{{ data.item.viewsub }}</span>
                </div>
            </template>

            <!-- revenue -->
            <template #cell(revenue)="data">
                {{ '$'+data.item.revenue }}
            </template>

            <!-- sales -->
            <template #cell(sales)="data">
                <div class="d-flex align-items-center">
                    <span class="font-weight-bolder mr-1">{{ data.item.sales+'%' }}</span>
                    <feather-icon
                        :class="data.item.loss ? 'text-danger':'text-success'"
                        :icon="data.item.loss ? 'TrendingDownIcon':'TrendingUpIcon'"
                    />
                </div>
            </template>
        </b-table>
    </b-card>
</template>

<script>
import {
    BCard, BTable, BAvatar, BImg,
} from 'bootstrap-vue'

export default {
    components: {
        BCard,
        BTable,
        BAvatar,
        BImg,
    },
    props: {
        tableData: {
            type: Array,
            default: () => [],
        },
    },

    data() {
        return {
            fields: [
                {
                    key: 'company',
                    label: 'COMPANY'
                },
                {
                    key: 'category',
                    label: 'CATEGORY'
                },
                {
                    key: 'views',
                    label: 'VIEWS'
                },
                {
                    key: 'revenue',
                    label: 'REVENUE'
                },
                {
                    key: 'sales',
                    label: 'SALES'
                },
            ],
        }
    },
}
</script>

<style lang="scss" scoped>
@import '~@resources/scss/base/bootstrap-extended/include';
@import '~@resources/scss/base/components/variables-dark';

.card-company-table ::v-deep td .b-avatar.badge-light-company {
    .dark-layout & {
        background: $theme-dark-body-bg !important;
    }
}
</style>
