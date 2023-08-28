<template>
    <b-card
        v-if="data"
        no-body
    >
        <!-- title and dropdown -->
        <b-card-header class="pb-0">
            <b-card-title>{{ data.title }}</b-card-title>
            <b-dropdown
                class="chart-dropdown"
                no-caret
                right
                size="sm"
                text="Last 7 Days"
                toggle-class="p-50"
                variant="transparent"
            >
                <b-dropdown-item
                    v-for="day in data.lastDays"
                    :key="day"
                >
                    {{ day }}
                </b-dropdown-item>
            </b-dropdown>
        </b-card-header>
        <!--/ title and dropdown -->

        <b-card-body>
            <b-row>
                <b-col
                    class="d-flex flex-column flex-wrap text-center"
                    sm="2"
                >
                    <h1 class="font-large-2 font-weight-bolder mt-2 mb-0">
                        {{ data.totalTicket }}
                    </h1>
                    <small>Tickets</small>
                </b-col>

                <!-- chart -->
                <b-col
                    class="d-flex justify-content-center"
                    sm="10"
                >

                    <!-- apex chart -->
                    <vue-apex-charts
                        :options="supportTrackerRadialBar.chartOptions"
                        :series="data.supportTrackerRadialBar.series"
                        height="270"
                        type="radialBar"
                    />
                </b-col>
                <!--/ chart -->
            </b-row>

            <!-- chart info -->
            <div class="d-flex justify-content-between">
                <div class="text-center">
                    <b-card-text class="mb-50">
                        New Tickets
                    </b-card-text>
                    <span class="font-large-1 font-weight-bold">{{ data.newTicket }}</span>
                </div>
                <div class="text-center">
                    <b-card-text class="mb-50">
                        Open Tickets
                    </b-card-text>
                    <span class="font-large-1 font-weight-bold">{{ data.openTicket }}</span>
                </div>
                <div class="text-center">
                    <b-card-text class="mb-50">
                        Response Time
                    </b-card-text>
                    <span class="font-large-1 font-weight-bold">{{ data.responseTime }}d</span>
                </div>
            </div>
        </b-card-body>
    </b-card>
</template>

<script>
import {
    BCard, BCardHeader, BCardTitle, BDropdown, BDropdownItem, BCardBody, BRow, BCol, BCardText,
} from 'bootstrap-vue'
import VueApexCharts from 'vue-apexcharts'
import { $themeColors } from '@themeConfig'

export default {
    components: {
        VueApexCharts,
        BCard,
        BCardHeader,
        BCardTitle,
        BDropdown,
        BDropdownItem,
        BCardText,
        BCardBody,
        BRow,
        BCol,
    },
    props: {
        data: {
            type: Object,
            default: () => {
            },
        },
    },
    data() {
        return {
            supportTrackerRadialBar: {
                chartOptions: {
                    plotOptions: {
                        radialBar: {
                            size: 150,
                            offsetY: 20,
                            startAngle: -150,
                            endAngle: 150,
                            hollow: {
                                size: '65%',
                            },
                            track: {
                                background: '#fff',
                                strokeWidth: '100%',
                            },
                            dataLabels: {
                                name: {
                                    offsetY: -5,
                                    color: '#5e5873',
                                    fontSize: '1rem',
                                },
                                value: {
                                    offsetY: 15,
                                    color: '#5e5873',
                                    fontSize: '1.714rem',
                                },
                            },
                        },
                    },
                    colors: [$themeColors.danger],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'dark',
                            type: 'horizontal',
                            shadeIntensity: 0.5,
                            gradientToColors: [$themeColors.primary],
                            inverseColors: true,
                            opacityFrom: 1,
                            opacityTo: 1,
                            stops: [0, 100],
                        },
                    },
                    stroke: {
                        dashArray: 8,
                    },
                    labels: ['Completed Tickets'],
                },
            },
        }
    },
}
</script>
