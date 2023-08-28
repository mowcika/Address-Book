<template>
    <b-card
        v-if="data"
        no-body
    >
        <b-card-header>
            <h4 class="mb-0">
                Last Month Comparison
            </h4>
            <b-card-text class="font-medium-5 mb-0">
                <feather-icon
                    class="text-muted cursor-pointer"
                    icon="HelpCircleIcon"
                    size="21"
                />
            </b-card-text>
            <div v-if="isSpinerShow==true" class="demo-inline-spacing m-auto ">
                <b-spinner
                    v-for="variant in variants"
                    :key="variant"
                    :variant="variant"
                    class="mr-1"
                    type="grow"
                />
            </div>
        </b-card-header>


        <!-- apex chart -->
        <vue-apex-charts
            v-if="isShowMonth==true"
            :options="goalOverviewRadialBar"
            :series="data.series"
            class="my-2"
            height="245"
            type="radialBar"

        />
        <b-row v-if="isShowMonth==true"
               class="text-center mx-0"
        >
            <b-col
                class="border-top border-right d-flex align-items-between flex-column py-1"
                cols="6"
            >
                <b-card-text class="text-muted mb-0">
                    Last Month
                </b-card-text>
                <h3 class="font-weight-bolder mb-0">
                    {{ lastMonthCount }}
                </h3>
            </b-col>

            <b-col
                class="border-top d-flex align-items-between flex-column py-1"
                cols="6"
            >
                <b-card-text class="text-muted mb-0">
                    This Month
                </b-card-text>
                <h3 class="font-weight-bolder mb-0">
                    {{ thisMonthCount }}
                </h3>
            </b-col>
        </b-row>
    </b-card>
</template>

<script>
import { BCard, BCardHeader, BCardText, BCol, BRow, BSpinner, } from 'bootstrap-vue'
import VueApexCharts from 'vue-apexcharts'
import { $themeColors } from '@themeConfig'

const $strokeColor = '#ebe9f1'
const $textHeadingColor = '#5e5873'
const $goalStrokeColor2 = '#51e5a8'
export default {
    components: {
        VueApexCharts,
        BCard,
        BSpinner,
        BCardHeader,
        BRow,
        BCardText,
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
            goalOverviewRadialBar: {
                chart: {
                    height: 245,
                    type: 'radialBar',
                    sparkline: {
                        enabled: true,
                    },
                    dropShadow: {
                        enabled: true,
                        blur: 3,
                        left: 1,
                        top: 1,
                        opacity: 0.1,
                    },
                },
                colors: [$goalStrokeColor2],
                plotOptions: {
                    radialBar: {
                        offsetY: -10,
                        startAngle: -150,
                        endAngle: 150,
                        hollow: {
                            size: '77%',
                        },
                        track: {
                            background: $strokeColor,
                            strokeWidth: '50%',
                        },
                        dataLabels: {
                            name: {
                                show: false,
                            },
                            value: {
                                color: $textHeadingColor,
                                fontSize: '2.86rem',
                                fontWeight: '600',
                            },
                        },
                    },
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        type: 'horizontal',
                        shadeIntensity: 0.5,
                        gradientToColors: [$themeColors.success],
                        inverseColors: true,
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 100],
                    },
                },
                stroke: {
                    lineCap: 'round',
                },
                grid: {
                    padding: {
                        bottom: 30,
                    },
                },
            },
            thisMonthCount: 0,
            lastMonthCount: 0,
            isShowMonth: false,
            isSpinerShow: true,
            variants: ['primary', 'secondary', 'danger', 'warning', 'success', 'info', 'light', 'dark'],
        }
    },
    created() {
        this.getMonthCompar()
        console.log(this.data.series)
        console.log(this.data.inProgress)
    },
    methods: {
        getMonthCompar() {
            axios.post('/getMonthCompar', '')
                .then(res => {
                    this.isShowMonth = true
                    this.isSpinerShow = false

                    const resData = res.data
                    this.thisMonthCount = resData[0].this_month_amt
                    this.lastMonthCount = resData[0].last_month_amt
                    this.data.series[0] = Math.round((resData[0].this_month_amt / resData[0].last_month_amt) * 100)

                    console.log(this.data.series)
                })
        }
    }
}
</script>
