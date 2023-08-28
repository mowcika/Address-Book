<template>
    <b-card-code title="TEAM-WISE RUN RATE">
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
            <b-col cols="3">
                <!--                <date-range-picker-->
                <!--                    ref="picker"-->
                <!--                    v-model="dates"-->
                <!--                    :autoApply="dateRangeConf.autoApply"-->
                <!--                    :locale-data="{ firstDay: 1, format: 'dd-mm-yyyy' }"-->
                <!--                    :maxDate="dateRangeConf.maxDate" :minDate="dateRangeConf.minDate"-->
                <!--                    :opens="dateRangeConf.opens"-->
                <!--                    :showDropdowns="dateRangeConf.showDropdowns"-->
                <!--                    :showWeekNumbers="dateRangeConf.showWeekNumbers"-->
                <!--                    :singleDatePicker="dateRangeConf.singleDatePicker"-->
                <!--                    :timePicker="dateRangeConf.timePicker"-->
                <!--                    :timePicker24Hour="dateRangeConf.timePicker24Hour"-->
                <!--                    style="width: 100%;"-->
                <!--                    @update="onLoadTableData"-->
                <!--                >-->
                <!--                </date-range-picker>-->
            </b-col>


            <b-col cols="3">
                <b-form-group>
                    <v-select
                        id="userGroup"
                        v-model="userGroup"
                        :clearable="false"
                        :options="userGroupArray"
                        :reduce="item=>item.id"
                        label="teamName"
                        multiple
                        placeholder="(Default All Teams) "
                        @input="onLoadTableData"
                    />

                </b-form-group>
            </b-col>

            <b-col cols="3">
                <!--                <b-form-group>-->
                <!--                    <v-select-->
                <!--                        id="newOldTotal"-->
                <!--                        v-model="newOldTotal"-->
                <!--                        :clearable="false"-->
                <!--                        :options="newOldTotalArray"-->
                <!--                        :reduce="item=>item.id"-->
                <!--                        label="name"-->
                <!--                        @input="onLoadTableData"-->
                <!--                    />-->
                <!--                </b-form-group>-->
            </b-col>


            <b-col cols="3">
                <!--                <b-form-group>-->
                <!--                    <v-select-->
                <!--                        id="countPayment"-->
                <!--                        v-model="countPayment"-->
                <!--                        :clearable="false"-->
                <!--                        :options="countPaymentArray"-->
                <!--                        :reduce="item=>item.id"-->
                <!--                        label="name"-->
                <!--                        @input="onLoadTableData"-->
                <!--                    />-->
                <!--                </b-form-group>-->
            </b-col>

        </b-row>

        <b-row>
            <b-col cols="6">

            </b-col>

            <b-col cols="6"></b-col>

        </b-row>


        <b-row>
            <b-col cols="12">
                <div id="chart">
                    <vue-apex-charts
                        ref="realtimeChart"
                        :options="chartOptions"
                        :series="series"
                        height="500"
                        type="bar"
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

import DateRangePicker from 'vue2-daterange-picker'
//you need to import the CSS manually
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'

import flatPickr from 'vue-flatpickr-component'
import { required_if } from 'vee-validate/dist/rules'
import moment from 'moment-timezone'
import VueApexCharts from 'vue-apexcharts'

export default {
    components: {
        BSpinner,
        BCardCode,
        BCardBody,
        BFormInput,
        BFormRadio,
        BFormRadioGroup,
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
        VueApexCharts,
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
        const now = new Date()
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate())
        const firstDay = new Date(now.getFullYear(), now.getMonth(), 1)
        const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0)

        return {
            old_array: [],
            labelArray: [],
            amountArray: [],
            userGroupChartTitle: '',
            filterView: '1',
            add_users: '',
            dateRangeConf: {
                opens: 'center',
                minDate: 'center',
                maxDate: 'center',
                singleDatePicker: false,
                timePicker: false,
                timePicker24Hour: false,
                showWeekNumbers: false,
                showDropdowns: false,
                autoApply: false,
            },
            // dates: {
            //     'startDate': firstDay,
            //     'endDate': lastDay
            // },
            dates: {
                'startDate': moment()
                    .startOf('month')
                    .format('YYYY-MM-DD'),
                'endDate': moment()
                    .endOf('month')
                    .format('YYYY-MM-DD')
            },
            forFilterPurpose: [['total_amount', 'nv_amount', 'ol_amount', 'dl_amount', 'exc_gst_amount'], ['company_count', 'nv', 'ol', 'dl', 'company_count'], ['company_count', 'nv', 'ol', 'dl', 'company_count'], ['company_count', 'nv', 'ol', 'dl', 'company_count']],
            forFilterPurposeOrderBy: [['sum(fcs.txn_amt) DESC', 'sum(IFNULL(if(fcs.company_status = "New",fcs.txn_amt,0),0)) DESC', 'sum(IFNULL(if(fcs.company_status = "Old",fcs.txn_amt,0),0)) DESC', 'sum(IFNULL(if(fcs.discountIncluded = 1,fcs.txn_amt,0),0)) DESC', 'sum(fcs.txn_amt) DESC'],
                ['sum(IFNULL(if(fcs.company_status != "",1,0),0)) DESC', 'sum(IFNULL(if(fcs.company_status = "New",1,0),0)) DESC', 'sum(IFNULL(if(fcs.company_status = "Old",1,0),0)) DESC', 'sum(IFNULL(if(fcs.discountIncluded = 1,1,0),0)) DESC', 'sum(IFNULL(if(fcs.company_status != "",1,0),0)) DESC'],
                ['sum(IFNULL(if(fcs.company_status != "",1,0),0)) DESC', 'sum(IFNULL(if(fcs.company_status = "New",1,0),0)) DESC', 'sum(IFNULL(if(fcs.company_status = "Old",1,0),0)) DESC', 'sum(IFNULL(if(fcs.discountIncluded = 1,1,0),0)) DESC', 'sum(IFNULL(if(fcs.company_status != "",1,0),0)) DESC'],
                ['sum(IFNULL(if(fcs.company_status != "",1,0),0)) DESC', 'sum(IFNULL(if(fcs.company_status = "New",1,0),0)) DESC', 'sum(IFNULL(if(fcs.company_status = "Old",1,0),0)) DESC', 'sum(IFNULL(if(fcs.discountIncluded = 1,1,0),0)) DESC', 'sum(IFNULL(if(fcs.company_status != "",1,0),0)) DESC']
            ],
            newOldTotal: '4',
            newOldTotalArray: [
                {
                    id: '0',
                    name: 'With GST',
                },
                {
                    id: '1',
                    name: 'New Amount',
                },
                {
                    id: '2',
                    name: 'Old Amount',
                },
                {
                    id: '3',
                    name: 'Discount Amount',
                },
                {
                    id: '4',
                    name: 'Total Amount',
                },
            ],
            countPayment: '0',
            countPaymentArray: [
                {
                    id: '0',
                    name: 'Payment-wise',
                },
                {
                    id: '1',
                    name: 'Count-wise (Include Free)',
                },
                {
                    id: '2',
                    name: 'Count-wise (Exclude Free)',
                },
                {
                    id: '3',
                    name: 'Count-wise (Free Only)',
                },
            ],
            userGroup: [],
            userGroupArray: [],
            initial_loading: true,
            variants: ['light', 'primary', 'secondary', 'danger', 'warning', 'success', 'info', 'dark'],
            required,
            min,
            max,
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
                // colors: [ // this array contains different color code for each data
                //     "#00b894",
                //     "#0984e3",
                //     "#e84393"
                // ],
                plotOptions: {
                    bar: {
                        // borderRadius: 10,
                        dataLabels: {
                            position: 'top', // top, center, bottom
                        },
                        endingShape: 'rounded'
                        // distributed: true,
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return val
                    },
                    offsetY: -20,
                    style: {
                        fontSize: '15px',
                        colors: ['#304758']
                    }
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
            series: [],
        }
    },
    async created() {
        const callAxios = await this.callAxios('/getTeamCounts', {}, 'post')
        console.log('bala :' + JSON.stringify(callAxios.data))
        this.userGroupArray = callAxios.data.gTeamCount
        // this.userGroupArray.unshift({"teamName":"All","id":"all"});
        this.userGroupArray.unshift({
            'teamName': 'All Teams',
            'id': 'all_team'
        })

        this.onLoadTableData()
    },
    async mounted() {
        // this.onLoadTableData();
    },

    methods: {

        async onLoadTableData() {

            var localUserId = JSON.parse(localStorage.getItem('userData')).id
            console.log('localUserId : ' + localUserId)
            if (!Array.isArray(this.userGroup)) {
                this.userGroup = ['']
            }
            var difference = []
            var differenceTemp = []
            // alert("$scope.userGroup 19 : "+this.userGroup);
            if (this.userGroup == '') {
                differenceTemp = ['all_team']
                this.userGroup = differenceTemp
            } else {
                var arr = this.userGroup.filter(function (entry) {
                    return /\S/.test(entry)
                })
                this.userGroup = arr
                this.userGroup.forEach((value, index) => {
                    console.log('bala 1 : ' + this.userGroup)
                    if (this.userGroup.length > 1) {
                        if (index == 0) {
                            if (value == 'all') {
                                this.userGroup.splice(index, 1)
                            }
                            if (value == 'all_team') {
                                this.userGroup.splice(index, 1)
                            }
                        } else {
                            if (value == 'all') {
                                this.userGroup = []
                                this.userGroup.push(value)
                            }
                            if (value == 'all_team') {
                                this.userGroup = []
                                this.userGroup.push(value)
                            }
                        }
                    }

                    console.log('bala 2 : ' + this.userGroup)

                    //if (Array.inArray(value, this.old_array) !== -1) {
                    // if (this.old_array.includes(value) !== -1) {
                    //     // if( this.old_array.indexOf(value) !== -1){
                    //     // alert("if 347 : "+this.old_array)
                    // } else {
                    //     // alert("else 348 : "+this.old_array)
                    //     alert("$scope.userGroup 12 : " + this.userGroup)
                    //     difference.push(value);
                    // }
                })
            }
            console.log('b : ' + this.userGroup)
            console.log('difference : ' + difference.length)
            // alert("difference.length : "+difference.length)
            // if (difference.length) {
            //     if (difference[0] == '') {
            //         differenceTemp = [''];
            //     } else {
            //         // $scope.userGroup.forEach(function (item) {
            //         this.userGroup.forEach((value, index) => {
            //             if (difference[0] == '') {
            //                 differenceTemp = [''];
            //             } else if (!isNaN(difference[0]) && !isNaN(value) && value != '') {
            //                 differenceTemp.push(value);
            //             } else if (isNaN(difference[0]) && isNaN(value) && value != '') {
            //                 differenceTemp.push(value);
            //             } else {
            //
            //             }
            //         });
            //     }
            //     setTimeout(function () {
            //         // $(document).find('#userGroup').val(differenceTemp);
            //
            //         var ugid = document.getElementById("userGroup");
            //         ugid.value = differenceTemp;
            //         console.log("ugid.value 377 : " + ugid)
            //         // $('.select2').select2({});
            //         this.userGroup = differenceTemp;
            //         alert("this.userGroup : " + this.userGroup);
            //         this.old_array = this.userGroup;
            //         console.log(this.old_array);
            //         this.timeOutHttp()
            //     }, 100);
            // } else {
            this.timeOutHttp()
            // }

        },
        async timeOutHttp() {
            this.initial_loading = false
            console.log('this.dates : ' + JSON.stringify(this.dates))
            var countPayment = 0
            if (this.countPayment == 2) {
                countPayment = 2
            } else {
                countPayment = this.countPayment
            }

            // if (Array.inArray("all", this.userGroup) !== -1) {
            // alert(this.userGroup.indexOf("all") !== -1);
            if (this.userGroup.indexOf('all') !== -1) {
                // if ($scope.userGroup == 'all')
                if (this.countPayment == 0) {
                    this.userGroupChartTitle = 'All User-wise Payment Status'
                } else {
                    this.userGroupChartTitle = 'All User-wise Count Status'
                }
                // else if ($scope.userGroup)
                //     $scope.userGroupChartTitle = $filter('filter')($scope.userGroupArray, {id: $scope.userGroup})[0].teamName + ' Team';
            } else {
                if (this.countPayment == 0) {
                    this.userGroupChartTitle = 'Team-wise Payment Status'
                } else {
                    this.userGroupChartTitle = 'Team-wise Count Status'
                }
            }

            const callAxios = await this.callAxios('/getpaymentTeamWise', {
                date: this.dates,
                user: this.add_users,
                userGroup: this.userGroup,
                orderBy: this.forFilterPurposeOrderBy[this.countPayment][this.newOldTotal],
                countPayment: countPayment,
                filter: this.filterView,
                uid: JSON.parse(localStorage.getItem('userData')).id
            }, 'post')
            // console.log("callAxios : "+JSON.stringify(callAxios.data))
            if (callAxios.data.getData.length > 0) {
                this.initial_loading = true
                // alert("callAxios.data.getData.length : "+callAxios.data.getData.length);
                if (this.filterView == 1) {
                    // bar chart
                    this.labelArray = []
                    this.amountArray = []
                    callAxios.data.getData.forEach((value, index) => {
                        this.labelArray.push(value.teamName)
                        this.amountArray.push(parseInt(value[this.forFilterPurpose[this.countPayment][this.newOldTotal]]))
                    })
                    console.log(this.labelArray)
                    console.log(this.amountArray)
                    this.dynamicChartSection('bar', this.labelArray, this.amountArray)
                } else {
                    // line chart
                    // this.dynamicChartSectionLine('line', this.labelArray, this.amountArray);
                }
            } else {
                // alert("callAxios.data.getData.length else : "+callAxios.data.getData.length);
            }

        },

        async dynamicChartSection(chartType, labelArray, amountArray) {
            console.log('amountArray : ' + amountArray)

            // 25012023
            var barColors = ['red', 'green', 'blue', 'orange', 'brown', 'black', 'yellow']

            var getSumOfArray = 0
            var getSumOfProjected = 0
            var getSumOfRequired = 0
            var getDaysInMonth = parseInt(moment()
                .daysInMonth())
            var getDaysUptoToday = parseInt(moment()
                .startOf('day')
                .format('DD'))
            var currentAmtArray = []
            var projectedAmtArray = []
            var requiredAmtArray = []
            var dynamicTarget = 100000
            var dynamicTargetForLabel = 100000
            var requiredSetZero = 0
            var getRemainDaysForRequired = (getDaysInMonth - getDaysUptoToday)
            getRemainDaysForRequired = (getRemainDaysForRequired < 1) ? 1 : getRemainDaysForRequired
            var TeamsMembers = 'Count'
            var toolTipFontSize = 12

            if (this.userGroup == 'all') {
                var rgb = []
                for (var i = 0; i < amountArray.length; i++) {
                    rgb.push('#' + (Math.random() * 0xFFFFFF << 0).toString(16)
                        .padStart(6, '0'))
                }
                toolTipFontSize = 11
                barColors = rgb
                TeamsMembers = 'Members'
                // } else if (this.userGroup.indexOf("all_team") !== -1) {
            } else if (this.userGroup.indexOf('all_team') !== -1) {
                TeamsMembers = 'Teams'
                dynamicTarget = 600000
            } else if (this.userGroup) {

                var filteredArray = this.userGroup.filter(function (e) {
                    return e !== 'all_team'
                })

                if (filteredArray) {
                    TeamsMembers = 'Members'
                    dynamicTarget = 100000
                    toolTipFontSize = 11
                    dynamicTargetForLabel = 600000
                }

            } else {

                TeamsMembers = 'Teams'
                dynamicTarget = 600000
            }

            amountArray.forEach((value, index) => {
                if (parseInt(value) > 0) {
                    getSumOfArray += parseInt(value)

                    currentAmtArray.push(Math.round((parseInt(value) / getDaysUptoToday).toFixed(2)))
                    projectedAmtArray.push(Math.round((parseInt(value) / getDaysUptoToday) * getDaysInMonth))
                    requiredSetZero = Math.ceil((dynamicTarget - parseInt(value)) / getRemainDaysForRequired)
                    requiredAmtArray.push((requiredSetZero < 0) ? 0 : requiredSetZero)

                    console.log('requiredSetZero : ' + Math.ceil((dynamicTarget - parseInt(value)) / getRemainDaysForRequired))
                    // console.log("requiredSetZero : "+(requiredSetZero < 0) ? 0 : requiredSetZero);
                }
            })
            console.log('currentAmtArray : ' + currentAmtArray.toLocaleString('en-IN'))
            console.log('bala here 2')
            // console.log("projectedAmtArray : "+projectedAmtArray);
            // console.log("requiredAmtArray : "+requiredAmtArray);

            projectedAmtArray.forEach((value, index) => {
                getSumOfProjected += parseInt(value)
            })

            requiredAmtArray.forEach((value, index) => {
                getSumOfRequired += parseInt(value)
            })
            labelArray = labelArray.slice(0, currentAmtArray.length)
            let thisArrayMin = parseInt(Math.min.apply(null, amountArray))
            let thisArrayMax = parseInt(Math.max.apply(null, projectedAmtArray))
            var getMaxLength = thisArrayMax.toString().length
            if (getMaxLength > 4) {
                var getMaxCeil = Math.ceil(thisArrayMax / 10000) * 10000
            } else if (getMaxLength > 3) {
                var getMaxCeil = Math.ceil(thisArrayMax / 1000) * 1000
            } else if (getMaxLength > 2) {
                var getMaxCeil = Math.ceil(thisArrayMax / 100) * 100
            } else {
                var getMaxCeil = Math.ceil(thisArrayMax / 10) * 10
            }
            var getMinRound = Math.round((thisArrayMin / 100) * 10)
            // alert(getMinRound + " / " + getMaxCeil);
            getMinRound = 0
            if (dynamicTarget == 600000) {
                dynamicTargetForLabel = (dynamicTarget * labelArray.length)
            }
            var getSelectedTeamsLength = this.userGroup.length
            if (getSelectedTeamsLength > 1) {
                getSumOfRequired = (dynamicTargetForLabel * getSelectedTeamsLength) - getSumOfArray
            } else {
                getSumOfRequired = dynamicTargetForLabel - getSumOfArray
            }

            dynamicTargetForLabel = (dynamicTargetForLabel * getSelectedTeamsLength)
            getSumOfArray = getSumOfArray.toLocaleString('en-IN')
            getSumOfProjected = getSumOfProjected.toLocaleString('en-IN')
            // getSumOfRequired = (getSumOfRequired * getRemainDaysForRequired).toLocaleString('en-IN');
            getSumOfRequired = (getSumOfRequired < 0) ? 0 : getSumOfRequired
            getSumOfRequired = getSumOfRequired.toLocaleString('en-IN')

            const COLORS = [
                '#4dc9f6',
                '#f67019',
                '#f53794',
                '#537bc4',
                '#acc236',
                '#166a8f',
                '#00a950',
                '#58595b',
                '#8549ba'
            ]
            const CHART_COLORS = {
                red: 'rgb(255, 99, 132)',
                orange: 'rgb(255, 159, 64)',
                yellow: 'rgb(255, 205, 86)',
                green: 'rgb(75, 192, 192)',
                blue: 'rgb(54, 162, 235)',
                purple: 'rgb(153, 102, 255)',
                grey: 'rgb(201, 203, 207)'
            }

            var dataFirst = {
                name: 'Current',
                data: currentAmtArray,
            }
            var dataProjected = {
                name: 'Projected',
                data: projectedAmtArray,

            }
            var dataRequired = {
                name: 'Required',
                data: requiredAmtArray,
            }
            // 25012023 /.

            var TeamsMembers = 'Count'
            var toolTipFontSize = 18

            if (this.userGroup == 'all') {
                // var rgb = [];
                // for (var i = 0; i < amountArray.length; i++)
                //     rgb.push('#' + (Math.random() * 0xFFFFFF << 0).toString(16).padStart(6, '0'));
                toolTipFontSize = 11
                // barColors = rgb;
                TeamsMembers = 'Members'
            } else if (this.userGroup.indexOf('all_team') !== -1) {
                TeamsMembers = 'Teams'
            } else if (this.userGroup) {
                var filteredArray1 = this.userGroup.filter(function (e) {
                    return e !== 'all_team'
                })

                if (filteredArray1) {
                    TeamsMembers = 'Members'
                }
            } else {
                TeamsMembers = 'Teams'
            }
            console.log(labelArray)
            console.log(amountArray)
            console.log('bala here')
            // this.$refs.realtimeChart.updateSeries([{
            //     series : [dataFirst, dataProjected, dataRequired]
            // }])
            this.$refs.realtimeChart.updateOptions({
                series: [
                    {
                        name: 'Current',
                        data: currentAmtArray
                    },
                    {
                        name: 'Projected',
                        data: projectedAmtArray
                    },
                    {
                        name: 'Required',
                        data: requiredAmtArray
                    },
                ]
            })

            this.$refs.realtimeChart.updateOptions({
                xaxis: {
                    categories: labelArray,
                }
            })

            this.$refs.realtimeChart.updateOptions({
                title: {
                    text: this.userGroupChartTitle + ' [ Target : ' + dynamicTargetForLabel.toLocaleString('en-IN') + '    ' + 'Current : ' + getSumOfArray + '    ' + 'Projected : ' + getSumOfProjected + '    ' + 'Required : ' + getSumOfRequired + '    ' + TeamsMembers + ' : ' + currentAmtArray.length + ' ]'
                }
            })

        }

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
