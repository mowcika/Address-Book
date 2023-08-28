<template>
    <b-card-code title="Month-wise Comparison">
        <div v-show="!initial_loading" class="text-center m-5">
            <b-spinner
                v-for="variant in variants"
                :key="variant"
                :variant="variant"
                class="m-2"
                style="width: 3rem; height: 3rem;"
                type="grow"
            />
        </div>


        <b-row>
            <b-col cols="4">
                <v-select
                    v-model="dateMonth"
                    :options="monthRange"
                    :reduce="item=>item.id"
                    label="value"
                    multiple
                    placeholder="Select Month"
                    @input="onLoadTableData"
                />
            </b-col>


            <b-col cols="4">
                <v-select
                    v-model="dayDateFilter"
                    :options="dayDateFilterArray"
                    :reduce="item=>item.id"
                    :clearable="false"
                    label="name"
                    @input="onLoadTableData"
                />
            </b-col>

            <b-col cols="3">

            </b-col>

            <b-col cols="3">

            </b-col>

        </b-row>

        <b-row>
            <b-col cols="12">
                <div id="chart">
                    <vue-apex-charts
                        ref="realtimeChart"
                        :options="chartOptions"
                        :series="series"
                        height="500"
                        type="line"
                    />
                </div>
            </b-col>
        </b-row>
    </b-card-code>

</template>

<script>
import BCardCode from '@core/components/b-card-code'
import TotalResults from '@/views/elements/TotalResults'
import {
    BSpinner,
    BFormInput,
    BFormGroup,
    BForm,
    BRow,
    BCol,
    BButton,
    BFormFile,
    BFormSelect,
    BFormTextarea,
    BFormCheckbox,
    BTable,
    BCardBody,
    BFormRadio,
    BFormRadioGroup,
} from 'bootstrap-vue'
import vSelect from 'vue-select'
import Ripple from 'vue-ripple-directive'
import { ref } from '@vue/composition-api'
import { max, min, required } from '@validations'
import { heightFade } from '@core/directives/animations'
import VueApexCharts from 'vue-apexcharts'
import { $themeColors } from '@themeConfig'
import DateRangePicker from 'vue2-daterange-picker'
//you need to import the CSS manually
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'

import flatPickr from 'vue-flatpickr-component'
import { required_if } from 'vee-validate/dist/rules'
import moment from 'moment-timezone'

export default {
    components: {
        BSpinner,
        BCardCode,
        BCardBody,
        BFormInput,
        BFormRadio,
        BFormRadioGroup,
        VueApexCharts,
        BFormSelect,
        BFormGroup,
        BForm,
        BFormFile,
        BRow,
        BCol,
        BButton,
        BFormTextarea,
        TotalResults,
        vSelect,
        flatPickr,
        DateRangePicker,
        // flatPickr,
        // BFormCheckbox,
        // BTable
    },
    setup() {

    },
    directives: {
        Ripple,
    },
    data() {

        var mS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec']
        var mSreverse = mS.reverse()
        var getMomentYear = moment()
            .year()
        var getMomentMonth = moment()
            .format('M')
        var getMomentYearMinus = (getMomentYear - 3)

        console.log('getMomentYear : ' + getMomentYear)
        console.log('getMomentMonth : ' + getMomentMonth)

        return {
            getSumOfArray: [],
            getSumOfArrayString: '',
            userGroupChartTitle: '',
            dataSetsArray: [],
            dataSets: '',
            currentAmtArray: [],
            lablenameArrays: [],
            labelArray: [],
            dateMonth: [],
            monthRange: [],
            dateTemp: [],
            dayDateFilter: '3',
            dayDateFilterArray: [
                {
                    id: '1',
                    name: 'Date-wise',
                },
                {
                    id: '2',
                    name: 'Day-wise',
                }, {
                    id: '3',
                    name: 'Date-Flow',
                },
            ],
            getMomentMonth: getMomentMonth,
            getMomentYear: getMomentYear,
            getMomentYearMinus: getMomentYearMinus,
            mSreverse: mSreverse,
            mS: mS,
            initial_loading: true,
            variants: ['light', 'primary', 'secondary', 'danger', 'warning', 'success', 'info', 'dark'],
            required,
            min,
            max,
            pass_getFinal: [], // amount
            pass_getLabel: [], // label
            pass_finalLabel: [], // final label
            chartOptions: {
                legend: {
                    display: true, // true
                    position: 'bottom',
                    filter: false,
                    labels: {
                        boxWidth: 80,
                        fontColor: 'black'
                    }
                },
                chart: {
                    toolbar: {
                        show: true
                    },
                    dropShadow: {
                        enabled: true,
                        color: '#000',
                        top: 18,
                        left: 7,
                        blur: 10,
                        opacity: 0.2
                    },
                },

                stroke: {
                    show: true,
                    curve: 'smooth',
                    // lineCap: 'butt',
                    // colors: undefined,
                    width: 3,
                    dashArray: 0,
                },
                markers: {
                    size: [4, 4, 4]
                },
                tooltips: {
                    enabled: true,
                    backgroundColor: 'rgba(f, f, f, f)',
                    displayColors: true,
                    mode: 'index',
                    shared: false,
                    intersect: false,
                },
                showTooltips: true,
                title: {},
                xaxis: {
                    // categories: [1991, 1992, 1993, 1994, 1995, 1996, 1997, 1998]
                    // categories: this.pass_getLabel,
                },

            },
            series: [{
                name: 'Month-1',
                data: []
            }]

        }
    },
    async created() {

        const monthArray = []
        let i = 0
        // alert("this.getMomentYearMinus : "+this.getMomentYearMinus+ "this getMomentYear : "+this.getMomentYear);
        while (this.getMomentYear > this.getMomentYearMinus) {
            // alert("in");
            this.mSreverse.forEach((value, index) => {
                let monthInitial = this.mS.findIndex(x => x === value)
                monthInitial = 12 - parseInt(monthInitial)
                if (monthInitial <= this.getMomentMonth || (this.getMomentYear != moment()
                    .year())) {

                    this.monthRange.push({
                        'id': this.getMomentYear.toString() + ((monthInitial < 10) ? '0' + monthInitial : monthInitial),
                        'value': value + '-' + this.getMomentYear
                    })

                    if (i < 3) {
                        this.dateTemp.push(this.getMomentYear.toString() + ((monthInitial < 10) ? '0' + monthInitial : monthInitial))
                    }
                    i++
                }
            })
            this.getMomentYear--
        }
        console.log('this.monthRange 12 : ' + JSON.stringify(this.dateTemp))
        this.dateMonth = this.dateTemp
        console.log('this.dateMonth : ' + this.dateMonth)

        this.onLoadTableData()
    },
    async mounted() {
        //this.dynamicChartSection()
    },

    methods: {

        onLoadTableData: async function () {
            if (Array.isArray(this.dateMonth) && this.dateMonth.length) {
                // alert("is array")
                this.timeOutHttp()
            } else {
                // alert("is not array")
                this.dateMonth = this.dateTemp
                this.timeOutHttp()
                // $timeout(function () {
                // setTimeout(function () {
                //     this.timeOutHttp()
                // }, 300)
            }

        },
        //console.log("this.currentAmtArray : "+this.currentAmtArray)
        async timeOutHttp() {
            this.initial_loading = false
            this.userGroupChartTitle = 'Month-wise Payment Status'
            var localUserId = JSON.parse(localStorage.getItem('userData')).id
            console.log('localUserId : ' + localUserId)
            console.log('dateMonth : ' + this.dateMonth)
            const callAxios = await this.callAxios('/getpaymentMonthWise', {
                date: this.dateMonth,
                dayDateFilter: this.dayDateFilter,
                uid: localUserId
            }, 'post')

            console.log('callAxios.data : ' + JSON.stringify(callAxios.data.getFinal))
            this.pass_getFinal = callAxios.data.getFinal // amount
            this.pass_getLabel = callAxios.data.getLabel // label
            this.pass_finalLabel = callAxios.data.finalLabel //final label
            console.log('pass_getLabel : ' + typeof (this.pass_getLabel))

            if (callAxios.data.getFinal.length > 0) {
                this.initial_loading = true
                this.dynamicChartSection(this.pass_getFinal, this.pass_getLabel, this.pass_finalLabel)
            } else {
                // remove chart
            }
        },

        async dynamicChartSection(amountArray, pass_getLabel, pass_finalLabel) {
            // console.log("labelArray : "+ labelArray)
            console.log('pass_getLabel : ' + JSON.stringify(pass_getLabel))
            // console.log("finalLabel : "+finalLabel)

            pass_finalLabel.forEach((value, index) => {
                this.lablenameArrays.push(value)
            })

            console.log('lablenameArrays 12 : ' + JSON.stringify(this.lablenameArrays))
            var newar = []
            var getSumOfArray = []
            this.currentAmtArray = []
            amountArray.forEach((value, index) => {
                this.currentAmtArray.push(Object.keys(value)
                    .map((index) => value[index]))
                var tempOBJ = {
                    name: this.pass_finalLabel[index],
                    data: this.currentAmtArray[index],
                    pointRadius: 10,
                    lineTension: 0,
                    fill: false,
                }
                newar.push(tempOBJ)
                console.log('bala tempOBJ : ' + JSON.stringify(tempOBJ))

                var sumObj = {}
                sumObj[pass_finalLabel[index]] = 0
                var totalTaxes = 0

                console.log(value)
                // value.forEach((value1, index1) => {
                //     totalTaxes += parseInt(value[index1]);
                // });

                Object.values(value)
                    .forEach(function (value, key) { // alert(value)
                        console.log('B1 : ' + value)
                        totalTaxes += parseInt(value)
                    })
                sumObj[pass_finalLabel[index]] = totalTaxes
                getSumOfArray.push(sumObj)
                console.log('B12 : ' + totalTaxes)

                // totalTaxes = value.reduce((acc, item) => acc + item.value, 0);

                // sumObj[pass_finalLabel[index]] = totalTaxes;
                // this.getSumOfArray.push(sumObj);
            })
            var getSumOfArrayString = ''
            this.getSumOfArrayString = ''
            console.log('getSumOfArray')
            console.log(getSumOfArray)
            getSumOfArray.forEach((values, key) => {
                this.getSumOfArrayString += Object.keys(values)
                    .map((key) => key + ' : ' + values[key].toLocaleString('en-IN'))
                if (key == (getSumOfArray.length - 1)) {
                    this.getSumOfArrayString += ' '
                } else {
                    this.getSumOfArrayString += ', '
                }
            })
            // alert(this.getSumOfArrayString)
            console.log('lablenameArrays : ' + JSON.stringify(this.lablenameArrays))
            this.$refs.realtimeChart.updateSeries(newar)

            if (this.dayDateFilter == 3) {
            } else {
                // alert("this.dayDateFilter : "+this.dayDateFilter)
                this.$refs.realtimeChart.updateOptions({
                    title: {
                        text: this.userGroupChartTitle + ' [ ' + this.getSumOfArrayString + ' ]'
                    }
                })
            }
            this.$refs.realtimeChart.updateOptions({
                xaxis: {
                    categories: pass_getLabel,
                }
            })
            this.$refs.realtimeChart.updateOptions({
                yaxis: {
                    forceNiceScale: true,
                    decimalsInFloat: 0
                }
            })

            // dataSetsArray

            console.log('this.lablenameArrays : ' + (this.lablenameArrays))
            console.log('this.currentAmtArray : ' + JSON.stringify(this.currentAmtArray))
            console.log(this.pass_getLabel)
        },

    },
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
@import '~@resources/scss/vue/libs/vue-good-table.scss';

@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>

<style>
::selection {
    color: #fff;
    background: #28c76f;
}

.outer-div {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.button-bar {
    padding-bottom: 4px;
}

.grid-wrapper {
    height: 20px;
    flex: 1 1 auto;
}
</style>
