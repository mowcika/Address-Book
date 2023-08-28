<template>
    <b-row class="match-height">
        <b-col cols="12">
            <b-card-code
                title="Employee job views / call report"
            >
                <b-form @submit.prevent>
                    <b-overlay
                        :show="show"
                        rounded="sm"
                    >
                        <b-row aria-hidden="true">
                            <b-col md="4">
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
                                <br>
                                <label class="required">Source Type</label>
                                <div class="demo-inline-spacing">
                                    <b-form-radio
                                        v-model="sourceType"
                                        name="some-radio8"
                                        value="date"
                                        class="custom-control-success"
                                        @change="onLoadTableData"
                                    >
                                        Date-wise
                                    </b-form-radio>
                                    <b-form-radio
                                        v-model="sourceType"
                                        name="some-radio8"
                                        value="district"
                                        class="custom-control-warning"
                                        @change="onLoadTableData"
                                    >
                                        District-wise
                                    </b-form-radio>
                                    <b-form-radio
                                        v-model="sourceType"
                                        name="some-radio8"
                                        value="city"
                                        class="custom-control-primary"
                                        @change="onLoadTableData"
                                    >
                                        City-wise
                                    </b-form-radio>
                                </div>
                                <br>
                                <label class="required">View Type</label>
                                <div class="demo-inline-spacing">
                                    <b-form-radio
                                        v-model="viewType"
                                        name="some-radio9"
                                        value="view"
                                        class="custom-control-success"
                                        @change="onLoadTableData"
                                    >
                                        View
                                    </b-form-radio>
                                    <b-form-radio
                                        v-model="viewType"
                                        name="some-radio9"
                                        value="call"
                                        class="custom-control-primary"
                                        @change="onLoadTableData"
                                    >
                                        Call
                                    </b-form-radio>
                                </div>
                            </b-col>
                            <b-col md="4">
                                <div class="email-app-menu">
                                    <vue-perfect-scrollbar
                                        :settings="perfectScrollbarSettings"
                                        class="sidebar-menu-list scroll-area"
                                    >
                                        <h6 class="section-label mt-1 mb-1 px-2">
                                            FILTERS
                                        </h6>
                                        <!-- Filters -->
                                        <b-list-group class="list-group-labels list-group-messages">
                                            <b-list-group-item
                                                v-for="filter in emailFilters"
                                                :key="filter.title + $route.path"
                                                :to="filter.route"
                                                :active="isDynamicRouteActive(filter.route)"
                                                @click="$emit('close-left-sidebar')"
                                            >
                                                <feather-icon
                                                    :icon="filter.icon"
                                                    size="18"
                                                    class="mr-75"
                                                />
                                                <span class="align-text-bottom line-height-1">{{ filter.title }}</span>
                                                <b-badge
                                                    v-if="filter.title.toLowerCase()"
                                                    pill
                                                    :variant="resolveFilterBadgeColor(filter.title)"
                                                    class="float-right"
                                                >
                                                    {{ filter.count }}
                                                </b-badge>
                                            </b-list-group-item>
                                        </b-list-group>
                                    </vue-perfect-scrollbar>
                                </div>
                            </b-col>
                            <b-col md="4">
                                <h6 class="section-label mt-1 mb-1 px-2">
                                    Statistic
                                </h6>
                                <b-list-group class="list-group-labels">
                                    <b-list-group-item
                                        v-for="label in emailLabel"
                                        :key="label.title + $route.path"
                                        :to="label.route"
                                        :active="isDynamicRouteActive(label.route)"
                                        @click="$emit('close-left-sidebar')"
                                    >
                                                    <span
                                                        class="bullet bullet-sm mr-1"
                                                        :class="`bullet-${label.color}`"
                                                    />
                                        <span>{{ label.title }}</span>
                                    </b-list-group-item>
                                </b-list-group>
                            </b-col>
                        </b-row>
                        <hr>
                        <b-row aria-hidden="true">
                            <b-col md="12" v-show="segment != 'statistic'">
                                <small class="text-danger">** Data updates every morning 6 am</small>
                                <b-table
                                    striped hover bordered responsive foot-clone
                                    :sort-by.sync="sortBy"
                                    :sort-desc.sync="sortDesc"
                                    :fields="fields"
                                    :items="historyItems"
                                    class="rounded-bottom"
                                    responsive="sm"
                                />
                            </b-col>
                            <b-col md="12" v-show="segment == 'statistic'">
                                <b-row class="match-height">
                                    <b-col cols="12">
                                        <b-card no-body>
                                            <b-card-header class="align-items-baseline">
                                                <div>
                                                    <b-card-title class="mb-25">
                                                        Payment Statistics
                                                    </b-card-title>
                                                    <b-card-text class="mb-0">
                                                        Paid: {{ employerCard.payment.amount }} /-
                                                    </b-card-text>
                                                </div>
                                                <feather-icon
                                                    icon="SettingsIcon"
                                                    size="18"
                                                    class="text-muted cursor-pointer"
                                                />
                                            </b-card-header>
                                            <b-card-body class="pb-0">
                                                <!-- apex chart -->
                                                <vue-apex-charts
                                                    ref="realtimeChart"
                                                    type="line"
                                                    height="240"
                                                    :options="salesLine.chartOptions"
                                                    :series="salesLine.series"
                                                />
                                            </b-card-body>
                                        </b-card>
                                    </b-col>
                                </b-row>
                            </b-col>
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
                <view-private-jobs :id="model_id" :table=table></view-private-jobs>
                <b-modal
                    id="modal-call-history"
                    cancel-variant="outline-secondary"
                    no-close-on-backdrop
                    centered
                    size="lg"
                    title="CALL HISTORY"
                >
                    <b-table
                        :fields="fields"
                        :items="historyItems"
                        class="rounded-bottom"
                    />
                </b-modal>
            </b-card-code>
        </b-col>
    </b-row>
</template>

<script>
import BCardCode from '@core/components/b-card-code'
import VuePerfectScrollbar from 'vue-perfect-scrollbar'
import { useClipboard } from '@vueuse/core'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import { useToast } from 'vue-toastification/composition'
import {
    BButton,
    BListGroup,
    BListGroupItem,
    BBadge,
    BForm,
    BFormRadio,
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
import { isDynamicRouteActive } from '@core/utils/utils'
import Ripple from 'vue-ripple-directive'
import { ref } from '@vue/composition-api'
import { max, min, required } from '@validations'
import { heightFade } from '@core/directives/animations'
import ViewPrivateJobs from '@/views/elements/ViewPrivateJobs.vue'
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
        BFormRadio,
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
        VuePerfectScrollbar,
        ViewPrivateJobs,
        BModal,
        VBModal,
        ToastificationContent,
        VueApexCharts,
        BCardHeader,
        BCardTitle,
        BCardBody,
        BTable,
        BOverlay,
        DateRangePicker
    },
    props: {},
    setup() {
        const toast = useToast()
        const { copy } = useClipboard()
        const copyIconName = iconName => {
            copy(iconName)

            toast({
                component: ToastificationContent,
                props: {
                    title: iconName + ' copied',
                    icon: 'CopyIcon',
                    variant: 'success',
                },
            })
        }
        const perfectScrollbarSettings = {
            maxScrollbarLength: 4,
        }
        const emails = ref([])
        emails.value = []
        const emailFilters = [
            {
                title: 'Most viewed job category',
                icon: 'XSquareIcon',
                count: 'Top 5',
                route: {
                    name: 'revenue-yhq',
                    params: { segment: 'y' }
                }
            },
            {
                title: 'Most wanted job category',
                icon: 'XOctagonIcon',
                count: 'Top 5',
                route: {
                    name: 'revenue-yhq',
                    params: { segment: 'h' }
                }
            },
            {
                title: 'Most jobs viewed employee-district',
                icon: 'XCircleIcon',
                count: 'Top 5',
                route: {
                    name: 'revenue-yhq',
                    params: { segment: 'q' }
                }
            },
            {
                title: 'Most jobs viewed location-district',
                icon: 'CircleIcon',
                count: 'Top 5',
                route: {
                    name: 'revenue-yhq',
                    params: { segment: 'm' }
                }
            },
            {
                title: 'Jobs vs Employees',
                icon: 'CircleIcon',
                count: 'Top 5',
                route: {
                    name: 'revenue-yhq',
                    params: { segment: 'm' }
                }
            }
        ]

        const emailLabel = [
            {
                title: 'Comparison',
                color: 'success',
                route: {
                    name: 'revenue-yhq',
                    params: { segment: 'statistic' }
                }
            }
        ]

        const resolveFilterBadgeColor = filter => {
            // if (filter === 'Draft') return 'light-warning'
            // if (filter === 'Spam') return 'light-danger'
            // return 'light-primary'
            return 'light-warning'
        }

        return {
            // UI
            copyIconName,
            perfectScrollbarSettings,
            isDynamicRouteActive,
            resolveFilterBadgeColor,

            // Filter & Labels
            emails,
            emailFilters,
            emailLabel,
        }
    },
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
        const yesterdayDate = new Date()
        const sevenDate = new Date()
        // maxDate.setMonth(maxDate.getMonth() + 2)
        maxDate.setDate(now.getDate() + 15)
        nextFollowDate.setDate(now.getDate() + 3)
        yesterdayDate.setDate(now.getDate() - 1)
        sevenDate.setDate(now.getDate() - 7)
        return {
            dateRangeConf: {
                opens: 'center',
                minDate: 'center',
                maxDate: yesterdayDate,
                singleDatePicker: false,
                timePicker: false,
                timePicker24Hour: false,
                showWeekNumbers: false,
                showDropdowns: false,
                autoApply: false,
            },
            expiryDate: {
                'startDate': sevenDate,
                'endDate': yesterdayDate
            },
            fields: [
                {
                    key: 'event',
                    label: 'EVENT',
                    sortable: true,
                    sortByFormatted: false,
                    formatter: (value, key, item) => {
                        if (this.sourceType == 'date') {
                            let date = new Date(item.event)
                            const day = date.toLocaleString('default', { day: '2-digit' })
                            const month = date.toLocaleString('default', { month: 'short' })
                            const year = date.toLocaleString('default', { year: 'numeric' })
                            return day + '-' + month + '-' + year
                        } else {
                            return item.event
                        }
                    },
                },
                {
                    key: 'total',
                    label: 'TOTAL',
                    sortable: true,
                    sortByFormatted: false,
                    formatter: (value, key, item) => {
                        return item.total.toLocaleString('en-IN')
                    },
                },
                {
                    key: '00:01 to 03:00',
                    label: '00:01 to 03:00',
                    thClass: 'text-right',
                    tdClass: 'text-right',
                    sortable: true,
                },
                {
                    key: '03:01 to 06:00',
                    label: '03:01 to 06:00',
                    thClass: 'text-right',
                    tdClass: 'text-right',
                    sortable: true,
                },
                {
                    key: '06:01 to 09:00',
                    label: '06:01 to 09:00',
                    thClass: 'text-right',
                    tdClass: 'text-right',
                    sortable: true,
                },
                {
                    key: '09:01 to 12:00',
                    label: '09:01 to 12:00',
                    thClass: 'text-right',
                    tdClass: 'text-right',
                    sortable: true,
                },
                {
                    key: '12:01 to 15:00',
                    label: '12:01 to 15:00',
                    thClass: 'text-right',
                    tdClass: 'text-right',
                    sortable: true,
                },
                {
                    key: '15:01 to 18:00',
                    label: '15:01 to 18:00',
                    thClass: 'text-right',
                    tdClass: 'text-right',
                    sortable: true,
                },
                {
                    key: '18:01 to 21:00',
                    label: '18:01 to 21:00',
                    thClass: 'text-right',
                    tdClass: 'text-right',
                    sortable: true,
                },
                {
                    key: '21:01 to 23:59',
                    label: '21:01 to 23:59',
                    thClass: 'text-right',
                    tdClass: 'text-right',
                    sortable: true,
                },
            ],
            sortBy: 'event',
            // sortBy: ['year', 'period'],
            sortDesc: true,
            data: {
                min: minDate,
                max: maxDate,
                nextFollowDate: nextFollowDate,
                isMobileDisabled: false,
                sample: [],
            },
            employerCard: {
                message: 'No data found with our record.',
                status: false,
                phonenumber: 'Loading',
                event: '',
                eventClass: '',
                payment: {
                    count: 0,
                    amount: 0
                },
                list: {
                    address: '',
                    companyName: '',
                    dist: '',
                    email: '',
                    firstname: '',
                    id: '',
                    phonenumber: '',
                    pincode: '',
                    image: ''
                }
            },
            salesLine: {
                series: [
                    {
                        name: 'Rs',
                        data: [],
                    },
                ],
                chartOptions: {
                    chart: {
                        toolbar: { show: false },
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
                        x: { show: false },
                    },
                },
            },
            show: false,
            model_id: '',
            table: '',
            historyItems: [],
            viewType: 'view',
            sourceType: 'date',
            segment: this.$route.params.segment,
            initial_loading: true,
        }

    },
    beforeMount() {
        // this.onLoadTableData()
    },
    async created() {
        this.show = true
        this.segment = this.$route.params.segment
        const segment = this.$route.params.segment
        const expiryDate = {
            startDate: new Date(this.expiryDate.startDate),
            endDate: new Date(this.expiryDate.endDate)
        }
        var localUserId = JSON.parse(localStorage.getItem('userData')).id
        const callAxios = await this.callAxios('/getEmployeeViewCallTiming', {
            localUserId: localUserId,
            viewType: this.viewType,
            sourceType: this.sourceType,
            segment: segment,
            dateRange: expiryDate
        }, 'post')
        callAxios.data.forEach((value, index) => {
            if (this.sourceType == 'date') {
                callAxios.data[index].event = value.date
            } else {
                callAxios.data[index].event = value.event
            }
        })
        this.historyItems = callAxios.data
        this.show = false
        // if (callAxios.data.status) {
        //     this.employerCard = callAxios.data
        //     callAxios.data.count.forEach((value, index) => {
        //         this.emailFilters[index].count = value.count
        //     })
        //     this.onLoadTableData(this.employerCard.list.id)
        // } else {
        //     callAxios.data.list = { address: '' }
        //     this.employerCard = callAxios.data
        //     this.emails = []
        // }
    },
    async mounted() {
        this.$root.$on('btnCellRenderer1Event()', (index, url, data, dataJson) => {
            this.btnCellRenderer1(index, url, data, dataJson)
        })
        // this.props.
    },
    watch: {
        async $route(to, from) {
            this.segment = this.$route.params.segment
            this.onLoadTableData()
        }
    },
    methods: {
        ClickMethod(jobId, segment) {
            this.$bvModal.show('modal-footer-lg')
            this.model_id = jobId
            this.table = this.segment
        },
        async historyIconName() {
            var localUserId = JSON.parse(localStorage.getItem('userData')).id
            this.$bvModal.show('modal-call-history')
            const callAxios = await this.callAxios('/getCallHistory', {
                localUserId: localUserId
            }, 'post')
            this.historyItems = callAxios.data
        },
        async onLoadTableData() {
            const segment = this.$route.params.segment
            var localUserId = JSON.parse(localStorage.getItem('userData')).id
            // this.fields[2].label = (this.viewType == 1) ? 'POST' : 'REVENUE'
            // this.fields[5].label = (this.viewType == 1) ? 'PER DAY POST' : 'PER DAY REVENUE'
            if (segment == 'statistic') {
                const chartData = []
                const chartLabel = []
                const callAxios = await this.callAxios('/getEmployerStatistic', {
                    localUserId: localUserId,
                    segment: segment,
                }, 'post')
                callAxios.data.forEach((value, index) => {
                    chartData.push(value.amount)
                    chartLabel.push(value.monthYear)
                })
                this.$refs.realtimeChart.updateSeries([{
                    data: callAxios.data.reverse()
                }])
                // this.$set(this.salesLine.series[0], 'data', [100,200]);
                // this.$set(this.salesLine.chartOptions.xaxis, 'categories', ['aa','bbb']);
                console.log(this.salesLine)
            } else {
                this.show = true
                this.historyItems = []
                const expiryDate = {
                    startDate: new Date(this.expiryDate.startDate),
                    endDate: new Date(this.expiryDate.endDate)
                }
                const callAxios = await this.callAxios('/getEmployeeViewCallTiming', {
                    localUserId: localUserId,
                    segment: segment,
                    viewType: this.viewType,
                    sourceType: this.sourceType,
                    dateRange: expiryDate
                }, 'post')
                // toLocaleString('en-IN')
                callAxios.data.forEach((value, index) => {
                    if (this.sourceType == 'date') {
                        callAxios.data[index].event = value.date
                    } else {
                        callAxios.data[index].event = value.event
                    }
                })
                this.historyItems = callAxios.data
                this.show = false
            }
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

table {
    font-family: monospace;
}

.table td:nth-child(1),
.table th:nth-child(1) {
    white-space: nowrap;
    width: fit-content;
}
</style>
