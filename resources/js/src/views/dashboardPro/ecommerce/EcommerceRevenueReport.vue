<template>
    <b-card
        v-if="data"
        class="card-revenue-budget"
        no-body
    >
        <div v-if="spinerShow==true" class="demo-inline-spacing m-auto">
            <b-spinner
                v-for="variant in variants"
                :key="variant"
                :variant="variant"
                class="mr-1"
                type="grow"
            />
        </div>
        <b-row v-if="spinerShow==false" class="mx-0">
            <b-col v-if="isShow==true"
                   class="revenue-report-wrapper"
                   md="8"
            >
                <div class="d-sm-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-50 mb-sm-0">
                        Job Post Report
                    </h4>
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center mr-2">
                            <span class="bullet bullet-primary svg-font-small-3 mr-50 cursor-pointer"/>
                            <span>FREE</span>
                        </div>
                        <div class="d-flex align-items-center ml-75">
                            <span class="bullet bullet-warning svg-font-small-3 mr-50 cursor-pointer"/>
                            <span>PAID</span>
                        </div>
                    </div>
                </div>

                <!-- chart -->
                <vue-apex-charts
                    id="revenue-report-chart"
                    :options="revenueReport.chartOptions"
                    :series="totalArray"
                    height="230"
                    type="bar"
                />
            </b-col>
            <b-col
                class="budget-wrapper"
                md="4"
            >
                <b-dropdown
                    :text=year_text
                    class="budget-dropdown"
                    size="sm"
                    variant="outline-primary"
                >
                    <b-dropdown-item
                        v-for="year in years"
                        :key="year"
                        @click="check(year)"
                    >
                        {{ year }}
                    </b-dropdown-item>
                </b-dropdown>
                <div class="d-flex justify-content-center">
                    <span class="font-weight-bolder mr-25">Paid Post:</span>
                    <span>{{ paidCount }}</span>
                </div>
                <div class="d-flex justify-content-center">
                    <span class="font-weight-bolder mr-25">Free Post:</span>
                    <span>{{ freeCount }}</span>
                </div>
                <vue-apex-charts
                    id="budget-chart"
                    :options="budgetChart.options"
                    :series="data.budgetChart.series"
                    height="80"
                    type="line"
                />
            </b-col>
        </b-row>
    </b-card>
</template>
<script>
import { BButton, BCard, BCol, BDropdown, BDropdownItem, BRow, BSpinner, } from 'bootstrap-vue'
import VueApexCharts from 'vue-apexcharts'
import { $themeColors } from '@themeConfig'
import Ripple from 'vue-ripple-directive'

const date = new Date()
export default {
    components: {
        VueApexCharts,
        BDropdown,
        BDropdownItem,
        BCard,
        BSpinner,
        BButton,
        BRow,
        BCol,
    },
    directives: {
        Ripple,
    },
    data() {
        return {
            revenue_report: {},
            revenueReport: {
                chartOptions: {
                    chart: {
                        stacked: true,
                        type: 'bar',
                        toolbar: { show: true },
                    },
                    grid: {
                        padding: {
                            top: -20,
                            bottom: -10,
                        },
                        yaxis: {
                            lines: { show: false },
                        },
                    },
                    xaxis: {
                        categories: [],
                        labels: {
                            style: {
                                colors: '#6E6B7B',
                                fontSize: '0.86rem',
                                fontFamily: 'Montserrat',
                            },
                        },
                        axisTicks: {
                            show: false,
                        },
                        axisBorder: {
                            show: false,
                        },
                    },
                    legend: {
                        show: false,
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    colors: [$themeColors.primary, $themeColors.warning],
                    plotOptions: {
                        bar: {
                            columnWidth: '17%',
                            endingShape: 'rounded',
                        },
                        distributed: true,
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#6E6B7B',
                                fontSize: '0.86rem',
                                fontFamily: 'Montserrat',
                            },
                        },
                    },
                },
            },
            // budget chart
            budgetChart: {
                options: {
                    chart: {
                        height: 80,
                        toolbar: { show: false },
                        zoom: { enabled: false },
                        type: 'line',
                        sparkline: { enabled: true },
                    },
                    stroke: {
                        curve: 'smooth',
                        dashArray: [0, 10],
                        width: [2],
                    },
                    colors: [$themeColors.primary, '#dcdae3'],
                    tooltip: {
                        enabled: true,
                    },
                },
            },
            lablemonth: [],
            freePost: {
                data: [],
                name: 'FREE'
            },
            totalArray: [
                {

                    data: [],
                    name: 'FREE'
                },
                {
                    data: [],
                    name: 'PAID'
                },
            ],
            paidPost: [],
            isShow: false,
            spinerShow: true,
            years: [],
            year_text: '2022',
            yearArray: {
                year: ''
            },
            freeCount: 0,
            paidCount: 0,
            variants: ['primary', 'secondary', 'danger', 'warning', 'success', 'info', 'light', 'dark'],
        }
    },
    created() {

        this.getJobPostReport(this.year_text)
        const year_check = 4
        this.years.push(date.getFullYear())
        for (let i = 1; i <= 4; i++) {
            this.years.push(date.getFullYear() - i)
        }
        // console.log(this.data.budgetChart.series)
    },
    props: {
        data: {
            type: Object,
            default: () => {
            },
        },
    },

    methods: {
        check(value) {
            this.year_text = value
            this.getJobPostReport(value)
            // alert(value)
        },
        getJobPostReport(value) {
            this.revenueReport.chartOptions.xaxis.categories = []
            this.isShow = false
            this.lablemonth = []
            this.yearArray.year = value

            axios.post('/getJobPostReport', this.yearArray)
                .then(res => {
                    this.totalArray[0].data = []
                    this.totalArray[1].data = []
                    this.data.budgetChart.series[0].data = []
                    this.data.budgetChart.series[1].data = []
                    // this.totalArray[1]=[]
                    this.isShow = true
                    // var chartDate = [
                    //     res.data[0].free_post,
                    //     res.data[0].paid_post
                    // ]
                    const resData = res.data
                    let free_post = 0
                    let paid_post = 0
                    this.freeCount = 0
                    this.paidCount = 0
                    if (resData.length > 0) {

                        resData.forEach((item, index) => {
                            this.lablemonth.push(item.month_year)
                            this.totalArray[0].data.push(item.free_post)
                            this.totalArray[1].data.push(item.paid_post)
                            this.data.budgetChart.series[0].data.push(item.paid_post)
                            this.data.budgetChart.series[1].data.push(item.free_post)
                            free_post += item.free_post
                            paid_post += item.paid_post
                        })
                        // this.lablemonth.push({ a: 'Hello World!' });
                    }
                    this.freeCount = free_post
                    this.paidCount = paid_post
                    this.revenueReport.chartOptions.xaxis.categories = this.lablemonth
                    this.spinerShow = false
                })

        }

    },
}
</script>
