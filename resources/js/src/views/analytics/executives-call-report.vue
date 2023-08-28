<template>
    <b-row class="match-height">
        <b-col cols="12">
            <b-card-code
                title="Executives call report"
            >
                <b-form @submit.prevent>
                    <b-overlay
                        :show="show"
                        rounded="sm"
                    >
                        <b-row aria-hidden="true" class="mb-2">
                            <b-col md="3">
                                <label class="required">Select Date</label><br>
                                <date-range-picker
                                    style="width: 100%;"
                                    ref="picker"
                                    :opens="dateRangeConf.opens"
                                    :locale-data="{ firstDay: 1, format: 'dd-mm-yyyy' }"
                                    :minDate="dateRangeConf.minDate" :maxDate="dateRangeConf.maxDate"
                                    :singleDatePicker="dateRangeConf.singleDatePicker"
                                    :timePicker="dateRangeConf.timePicker"
                                    :timePicker24Hour="dateRangeConf.timePicker24Hour"
                                    :showWeekNumbers="dateRangeConf.showWeekNumbers"
                                    :showDropdowns="dateRangeConf.showDropdowns"
                                    :autoApply="dateRangeConf.autoApply"
                                    v-model="expiryDate"
                                    @update="onLoadTableData"
                                >
                                </date-range-picker>
                            </b-col>
                        </b-row>
                        <b-row aria-hidden="true">
                            <b-col md="12" v-show="segment != 'statistic'">
                                <small class="text-danger"></small>
                                <b-table
                                    :sort-by.sync="sortBy"
                                    :sort-desc.sync="sortDesc"
                                    :fields="fields"
                                    :items="historyItems"
                                    class="rounded-bottom"
                                >
                                    <template #cell(total)="data">
                                        <button @click="historyIconName(data.item.id, data.item.name)">{{ data.value
                                            }}
                                        </button>
                                    </template>
                                </b-table>
                            </b-col>
                            <!--                            <b-col md="9" v-show="segment == 'statistic'">-->
                            <!--                                <b-row class="match-height">-->
                            <!--                                    <b-col cols="12">-->
                            <!--                                        <b-card no-body>-->
                            <!--                                            <b-card-header class="align-items-baseline">-->
                            <!--                                                <div>-->
                            <!--                                                    <b-card-title class="mb-25">-->
                            <!--                                                        Conversion %-->
                            <!--                                                    </b-card-title>-->
                            <!--                                                    <b-card-text class="mb-0 ml-1">-->
                            <!--                                                        Total : {{ convertion.totalConvert }} % <br>-->
                            <!--                                                        Free : {{ convertion.freeConvert }} % <br>-->
                            <!--                                                        Paid : {{ convertion.paidConvert }} % <br>-->
                            <!--                                                        within Date Selection (Free + Paid) : {{ convertion.todayConvert }} %-->
                            <!--                                                    </b-card-text>-->
                            <!--                                                </div>-->
                            <!--                                                <feather-icon-->
                            <!--                                                    icon="SettingsIcon"-->
                            <!--                                                    size="18"-->
                            <!--                                                    class="text-muted cursor-pointer"-->
                            <!--                                                />-->
                            <!--                                            </b-card-header>-->
                            <!--                                            <b-card-body class="pb-0">-->
                            <!--                                                &lt;!&ndash; apex chart &ndash;&gt;-->
                            <!--                                                <vue-apex-charts-->
                            <!--                                                    ref="realtimeChart"-->
                            <!--                                                    type="bar"-->
                            <!--                                                    height="240"-->
                            <!--                                                    :options="salesLine.chartOptions"-->
                            <!--                                                    :series="salesLine.series"-->
                            <!--                                                />-->
                            <!--                                            </b-card-body>-->
                            <!--                                        </b-card>-->
                            <!--                                    </b-col>-->
                            <!--                                </b-row>-->
                            <!--                            </b-col>-->
                        </b-row>
                        <template #overlay>
                            <div class="text-center">
                                <feather-icon
                                    icon="ClockIcon"
                                    size="24"
                                />
                                <b-card-text id="cancel-label">
                                    Please wait...
                                </b-card-text>
                            </div>
                        </template>
                    </b-overlay>
                </b-form>
                <b-modal
                    id="modal-call-history"
                    cancel-variant="outline-secondary"
                    no-close-on-backdrop
                    centered
                    size="lg"
                    :title="this.localUserName"
                >
                    <!--                    <b-table-->
                    <!--                        :items="historyItems"-->
                    <!--                        class="rounded-bottom"-->
                    <!--                    />-->
                    <b-table
                        :items="historyItemsModal"
                        responsive
                        class="mb-0"
                    >
                        <template #cell(event)="data">
                            <feather-icon
                                icon="PhoneIncomingIcon"
                                class="mr-50 text-primary"
                                size="15"
                                v-show="data.value == 1"
                            />
                            <feather-icon
                                icon="PhoneOutgoingIcon"
                                class="mr-50 text-success"
                                size="15"
                                v-show="data.value == 2"
                            />
                            <feather-icon
                                icon="PhoneMissedIcon"
                                class="mr-50 text-danger"
                                size="15"
                                v-show="data.value == 3"
                            />
                        </template>
                    </b-table>
                </b-modal>
            </b-card-code>
        </b-col>
    </b-row>
</template>

<script>
import BCardCode from '@core/components/b-card-code'
import {
    BButton,
    BListGroup,
    BListGroupItem,
    BBadge,
    BForm,
    BRow,
    BCol,
    BAlert,
    BMediaBody,
    BMedia,
    BMediaAside,
    BAvatar,
    BFormCheckbox,
    BCard,
    BImg,
    BCardText,
    BModal,
    VBModal,
    BCardHeader,
    BCardTitle,
    BCardBody,
    BTable,
    BOverlay
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import { ref } from '@vue/composition-api'
import { max, min, required } from '@validations'
import { heightFade } from '@core/directives/animations'
import VueApexCharts from 'vue-apexcharts'
import { $themeColors } from '@themeConfig'
import DateRangePicker from 'vue2-daterange-picker'
//you need to import the CSS manually
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'

export default {
    directives: {
        Ripple,
        'height-fade': heightFade,
    },
    components: {
        BForm,
        BRow,
        BCol,
        BAlert,
        BButton,
        BListGroup,
        BListGroupItem,
        BBadge,
        BMediaBody,
        BMedia,
        BMediaAside,
        BAvatar,
        BFormCheckbox,
        BCard,
        BImg,
        BCardText,
        BCardCode,
        BModal,
        VBModal,
        VueApexCharts,
        BCardHeader,
        BCardTitle,
        BCardBody,
        BTable,
        BOverlay,
        DateRangePicker
    },
    props: {},
    data() {
        const now = new Date()
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate())

        // 15th two months prior
        const minDate = new Date(today)
        // minDate.setMonth(minDate.getMonth() - 2)
        // minDate.setDate(15)

        // 15th in two months
        const maxDate = new Date(today)
        const nextFollowDate = new Date()
        // maxDate.setMonth(maxDate.getMonth() + 2)
        maxDate.setDate(now.getDate() + 15)
        nextFollowDate.setDate(now.getDate() + 3)
        return {
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
            expiryDate: {
                'startDate': now,
                'endDate': now
            },
            data: {
                min: minDate,
                max: maxDate,
                nextFollowDate: nextFollowDate,
                isMobileDisabled: false,
                sample: [],
            },
            convertion: {
                'totalConvert': 0,
                'todayConvert': 0,
                'freeConvert': 0,
                'paidConvert': 0
            },
            salesLine: {
                series: [
                    {
                        name: 'Employer',
                        data: [],
                    },
                ],
                chartOptions: {
                    chart: {
                        toolbar: { show: true },
                        zoom: { enabled: false },
                        type: 'line',
                        dropShadow: {
                            enabled: true,
                            top: 18,
                            left: 2,
                            blur: 5,
                            opacity: 0.2,
                        },
                        offsetX: -10,
                    },
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
                        curve: 'smooth',
                        width: 4,
                    },
                    grid: {
                        borderColor: '#ebe9f1',
                        padding: {
                            top: -20,
                            bottom: 5,
                            left: 20,
                        },
                    },
                    legend: {
                        show: false,
                    },
                    colors: ['#df87f2'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'dark',
                            inverseColors: false,
                            gradientToColors: [$themeColors.primary],
                            shadeIntensity: 1,
                            type: 'horizontal',
                            opacityFrom: 1,
                            opacityTo: 1,
                            stops: [0, 100, 100, 100],
                        },
                    },
                    markers: {
                        size: 0,
                        hover: {
                            size: 5,
                        },
                    },
                    xaxis: {
                        labels: {
                            offsetY: 5,
                            style: {
                                colors: '#b9b9c3',
                                fontSize: '0.857rem',
                            },
                        },
                        axisTicks: {
                            show: false,
                        },
                        categories: [],
                        axisBorder: {
                            show: false,
                        },
                        tickPlacement: 'on',
                    },
                    yaxis: {
                        tickAmount: 5,
                        labels: {
                            style: {
                                colors: '#b9b9c3',
                                fontSize: '0.857rem',
                            },
                            formatter(val) {
                                return val > 999 ? `${(val / 1000).toFixed(1)}k` : val
                            },
                        },
                    },
                    tooltip: {
                        x: { show: true },
                    },
                },
            },
            fields: [
                {
                    key: 'name',
                    label: 'NAME',
                    sortable: true,
                },
                {
                    key: 'incoming',
                    label: 'INCOMING COUNT / DURATION',
                    thClass: 'text-right',
                    tdClass: 'text-right',
                    sortable: true,
                },
                {
                    key: 'outgoing',
                    label: 'OUTGOING COUNT / DURATION',
                    thClass: 'text-right',
                    tdClass: 'text-right',
                    sortable: true,
                },
                {
                    key: 'missed',
                    label: 'MISSED COUNT',
                    thClass: 'text-right',
                    tdClass: 'text-right',
                    sortable: true,
                },
                {
                    key: 'total',
                    label: 'TOTAL',
                    thClass: 'text-right',
                    tdClass: 'text-right',
                    sortable: true,
                }
            ],
            sortBy: 'total',
            localUserName: 'CALL HISTORY',
            // sortBy: ['year', 'period'],
            sortDesc: true,
            historyItems: [],
            historyItemsModal: [],
            show: false,
            segment: this.$route.params.segment,
            initial_loading: true,
        }

    },
    beforeMount() {
        // this.onLoadTableData()
    },
    async created() {
        // this.show = true
        this.segment = this.$route.params.segment
        const segment = this.$route.params.segment
        var localUserId = JSON.parse(localStorage.getItem('userData')).id
        this.onLoadTableData(15596)
    },
    async mounted() {
        this.$root.$on('btnCellRenderer1Event()', (index, url, data, dataJson) => {
            this.btnCellRenderer1(index, url, data, dataJson)
        })
    },
    watch: {
        async $route(to, from) {
            this.segment = this.$route.params.segment
            this.onLoadTableData()
        }
    },
    methods: {
        async onLoadTableData() {
            const expiryDate = {
                startDate: new Date(this.expiryDate.startDate),
                endDate: new Date(this.expiryDate.endDate)
            }
            this.show = true
            const segment = this.$route.params.segment
            var localUserId = JSON.parse(localStorage.getItem('userData')).id
            const callAxios = await this.callAxios('/executivesCallReport', {
                localUserId: localUserId,
                segment: segment,
                dateRange: expiryDate
            }, 'post')
            this.historyItems = callAxios.data
            this.show = false
        },
        async historyIconName(getUserId, getUserName) {
            const expiryDate = {
                startDate: new Date(this.expiryDate.startDate),
                endDate: new Date(this.expiryDate.endDate)
            }
            this.historyItemsModal = []
            this.localUserName = getUserName + ' CALL HISTORY'
            var localUserId = JSON.parse(localStorage.getItem('userData')).id
            this.$bvModal.show('modal-call-history')
            const callAxios = await this.callAxios('/getCallHistory', {
                localUserId: getUserId,
                dateRange: expiryDate
            }, 'post')
            this.historyItemsModal = callAxios.data
        }
    },
}
</script>
<style>
.disableOpacityCustom {
    /*opacity: 0.4;*/
    /*cursor: not-allowed;*/
    display: none
}

div.list-group.list-group-labels a.list-group-item.list-group-item-action {
    opacity: 0.4;
    cursor: not-allowed;
    pointer-events: none;
}
</style>
