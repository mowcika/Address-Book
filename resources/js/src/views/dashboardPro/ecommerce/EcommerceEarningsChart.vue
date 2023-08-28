<template>
    <b-card
        v-if="data"
        class="earnings-card"
    >
        <b-row>

            <b-col v-if="isShow==true" cols="12">
                <div class="font-small-2">
                    <h3>Today Post Count : {{ totalJobPostCount }}</h3>
                </div>
                <!-- chart -->
                <chartjs-component-doughnut-chart
                    :data="doughnutChart.data"
                    :height="80"
                    :options="doughnutChart.options"
                    class=""
                />
            </b-col>
        </b-row>
    </b-card>
</template>

<script>
import { BCard, BCardText, BCardTitle, BCol, BRow, } from 'bootstrap-vue'
import VueApexCharts from 'vue-apexcharts'
import { $themeColors } from '@themeConfig'
import ChartjsComponentDoughnutChart
    from './../../charts-and-maps/charts/chartjs/charts-components/ChartjsComponentDoughnutChart.vue'

const $earningsStrokeColor2 = '#28c76f66'
const $earningsStrokeColor3 = '#28c76f33'
const chartColors = {
    primaryColorShade: '#836AF9',
    yellowColor: '#ffe800',
    successColorShade: '#28dac6',
    warningColorShade: '#ffe802',
    warningLightColor: '#FDAC34',
    infoColorShade: '#299AFF',
    greyColor: '#4F5D70',
    blueColor: '#2c9aff',
    blueLightColor: '#84D0FF',
    greyLightColor: '#EDF1F4',
    tooltipShadow: 'rgba(0, 0, 0, 0.25)',
    lineChartPrimary: '#666ee8',
    lineChartDanger: '#ff4961',
    labelColor: '#6e6b7b',
    grid_line_color: 'rgba(200, 200, 200, 0.2)',
}
export default {
    components: {
        BCard,
        ChartjsComponentDoughnutChart,
        BRow,
        BCol,
        BCardTitle,
        BCardText,
        VueApexCharts,
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
            isShow: false,
            totalJobPostCount: 0,
            doughnutChart: {
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    responsiveAnimationDuration: 500,
                    cutoutPercentage: 60,
                    legend: { display: false },
                    tooltips: {
                        callbacks: {
                            label(tooltipItem, data) {
                                const label = data.datasets[0].labels[tooltipItem.index] || ''
                                const value = data.datasets[0].data[tooltipItem.index]
                                const output = ` ${label} : ${value}`
                                return output
                            },
                        },
                        // Updated default tooltip UI
                        shadowOffsetX: 1,
                        shadowOffsetY: 1,
                        shadowBlur: 8,
                        shadowColor: chartColors.tooltipShadow,
                        backgroundColor: $themeColors.light,
                        titleFontColor: $themeColors.dark,
                        bodyFontColor: $themeColors.dark,
                    },
                },
                data: {
                    datasets: [
                        {
                            labels: ['Free', 'PAID'],
                            data: [],
                            backgroundColor: [chartColors.successColorShade, chartColors.warningLightColor],
                            borderWidth: 0,
                            pointStyle: 'rectRounded',
                        },
                    ],
                },
            },

        }
    },
    created() {
        // console.log('start')
        // console.log(this.doughnutChart)
        this.getJobCount()
        // console.log('end')
        // console.log(this.doughnutChart)
    },
    methods: {
        getJobCount() {
            axios.post('/getTodayJobCount', '')
                .then(res => {
                    this.isShow = true
                    var chartDate = [
                        res.data[0].free_post,
                        res.data[0].paid_post
                    ]
                    // console.log(chartDate)

                    this.total = res.data.length
                    this.doughnutChart.data.datasets[0].data = chartDate
                    this.totalJobPostCount = res.data[0].todayJobPost
                    // console.log(this.doughnutChart.data.datasets[0].data)
                })
        }
    }
}
</script>
