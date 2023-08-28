<template>
    <b-overlay
        :show="show"
        rounded="sm"
    >
        <b-row class="match-height">
            <b-col cols="12">
                <b-card-code
                    title=""
                >
                    <b-form @submit.prevent>
                        <b-row>
                            <b-col md="12">
                                <div class="form-group-compose text-center compose-btn">
                                    <b-button
                                        v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                                        variant="dark"
                                        size="lg"
                                        block
                                    >
                                        <feather-icon
                                            icon="SkipBackIcon"
                                            class="mr-50 float-left"
                                            v-show="this.getJobId > 0"
                                            size="20"
                                            @click="historyIconName(false)"
                                        />
                                        <b>{{ employerCard.phonenumber }}</b> <small>{{ employerCard.event }}</small>
                                        <feather-icon
                                            icon="SkipForwardIcon"
                                            class="mr-50 float-right"
                                            v-show="this.getJobId < (this.getSizeOf-1)"
                                            size="20"
                                            @click="historyIconName(true)"
                                        />
                                    </b-button>
                                    <br>
                                </div>
                            </b-col>
                        </b-row>
                        <b-row class="match-height" v-show="employerCard.status"
                               style="border: 1px solid gold;padding: 20px;margin: 5px;"
                        >
                            <b-col
                                lg="6"
                                md="6"
                            >
                                <div>
                                    <label for="rating-inline"><b>Call Status:</b></label>
                                    <div class="demo-inline-spacing">
                                        <b-form-radio
                                            v-model="callType"
                                            name="some-radio9"
                                            value="0"
                                            class="custom-control-info"
                                            @change="onCallTypeChange"
                                        >
                                            No FollowUp Made
                                        </b-form-radio>
                                        <b-form-radio
                                            v-model="callType"
                                            name="some-radio9"
                                            value="1"
                                            class="custom-control-success"
                                            @change="onCallTypeChange"
                                        >
                                            Spoken
                                        </b-form-radio>
                                        <b-form-radio
                                            v-model="callType"
                                            name="some-radio9"
                                            value="2"
                                            class="custom-control-warning"
                                            @change="onCallTypeChange"
                                        >
                                            Not Spoken
                                        </b-form-radio>
                                    </div>
                                </div>
                            </b-col>
                            <b-col
                                lg="6"
                                md="6"
                            >
                                <div v-show="callType == 1">
                                    <label for="rating-inline"><b>Employer Satisfaction:</b></label>
                                    <b-form-rating v-model="emprMark" variant="warning"
                                                   @change="onCallTypeChange"
                                    ></b-form-rating>
                                </div>
                            </b-col>
                        </b-row>
                        <b-row class="match-height" v-show="employerCard.status">
                            <b-col
                                lg="7"
                                md="6"
                            >
                                <b-card
                                    text-variant="center"
                                    class="card card-congratulations"
                                >
                                    <!-- images -->
                                    <b-img
                                        :src="require('@/assets/images/elements/decore-left.png')"
                                        class="congratulations-img-left"
                                    />
                                    <b-img
                                        :src="require('@/assets/images/elements/decore-right.png')"
                                        class="congratulations-img-right"
                                    />
                                    <!--/ images -->

                                    <b-avatar
                                        variant="primary"
                                        size="70"
                                        class="shadow mb-2"
                                    >
                                        <!--                                    {{ employerCard.list.image }}-->
                                        <feather-icon
                                            size="28"
                                            icon="AwardIcon"
                                        />
                                    </b-avatar>
                                    <h1 class="mb-1 mt-50 text-white">
                                        {{ employerCard.list.companyName }}
                                    </h1>
                                    <b-card-text class="m-auto w-75"> {{
                                        employerCard.list.address.replaceAll('#@#', ', ')
                                        }}
                                    </b-card-text>
                                    <b-card-text class="m-auto w-75"> Employer ID - <strong>{{
                                        employerCard.list.id
                                        }}</strong>
                                    </b-card-text>
                                    <b-card-text class="m-auto w-75"> Contact Person - <strong>{{
                                        employerCard.list.firstname
                                        }}</strong></b-card-text>
                                    <b-card-text class="m-auto w-75"> Email - <strong>{{
                                        employerCard.list.email
                                        }}</strong>
                                    </b-card-text>
                                </b-card>
                            </b-col>
                            <b-col
                                lg="5"
                                md="6"
                            >
                                <b-card class="card-congratulation-medal" style="border: 1px solid gold;"
                                >
                                    <b-img
                                        :src="require('@/assets/images/illustration/email.svg')"
                                        class="congratulations-img-right"
                                        style="position: absolute;right: 0;bottom: 0;"
                                    />
                                    <h4 class="mb-1"><span class="text-primary"><b>Executive</b></span> : <span
                                        class="text-danger"
                                    > <b> {{ employerCard.executive }}</b></span></h4>
                                    <h5><span v-if="employerCard.payment.amount > 0">Paid 🎉</span><span v-else
                                    >Free</span>
                                        Employer!</h5>
                                    <b-card-text class="font-small-3">
                                        This Employer made {{ employerCard.payment.count }} payments with us
                                    </b-card-text>
                                    <h3 class="mb-30 mt-4">
                                        Rs. {{ employerCard.payment.amount }}/-
                                    </h3>
                                    <b-img
                                        :src="require('@/assets/images/illustration/badge.svg')"
                                        class="congratulation-medal"
                                        alt="Medal Pic"
                                        v-show="employerCard.payment.amount > 0"
                                    />
                                </b-card>
                            </b-col>
                        </b-row>
                        <b-alert
                            v-height-fade.appear
                            variant="danger"
                            :show="employerCard.status == false"
                            class="mb-1"
                        >
                            <div class="alert-body" style="text-align: center;">
                                <feather-icon
                                    icon="InfoIcon"
                                    class="mr-50"
                                />
                                {{ employerCard.message }}
                            </div>
                        </b-alert>
                        <b-row aria-hidden="true" v-bind:class="{'disableOpacityCustom': employerCard.status == false}">
                            <b-col md="3">
                                <div class="email-app-menu">

                                    <vue-perfect-scrollbar
                                        :settings="perfectScrollbarSettings"
                                        class="sidebar-menu-list scroll-area"
                                    >
                                        <!-- Filters -->
                                        <b-list-group class="list-group-messages">
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

                                        <!-- Labels -->
                                        <h6 class="section-label mt-3 mb-1 px-2">
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

                                    </vue-perfect-scrollbar>
                                </div>
                            </b-col>
                            <b-col md="9" v-show="segment != 'statistic'" style="overflow-y: scroll;max-height: 250px;">
                                <vue-perfect-scrollbar
                                    :settings="perfectScrollbarSettings"
                                    class="email-user-list scroll-area"
                                >
                                    <ul class="email-media-list">
                                        <b-media
                                            v-for="email in emails"
                                            :key="email.id"
                                            tag="li"
                                            no-body
                                            :class="{ 'mail-read': email.isRead }"
                                        >

                                            <b-media-aside class="media-left mr-50">
                                                <b-avatar
                                                    class="avatar"
                                                    size="40"
                                                    variant="primary"
                                                    :src="'https://nithrajobs.com/assets/dist/img/jobs_round_logo_black.webp'"
                                                />
                                            </b-media-aside>

                                            <b-media-body>
                                                <div class="mail-items">
                                                    <h5 class="mb-25">
                                                        ID : #{{ email.id }}
                                                    </h5>
                                                    <span class="text-truncate">{{ email.subject }}</span>
                                                    <b-button v-show="segment == 'jobs'" @click="ClickMethod(email.id)"
                                                              class="float-right"
                                                    >View
                                                    </b-button>
                                                </div>
                                                <div class="mail-message">
                                                    <p class="text-truncate mb-0">
                                                        Validity : {{ email.message }}
                                                    </p>
                                                    <span class="mail-date">Created on : {{ email.time }}</span>
                                                    <b-badge v-if="email.status == 'Live'" variant="success" pill
                                                             class="float-right"
                                                    >
                                                        {{ email.status }}
                                                    </b-badge>
                                                    <b-badge v-if="email.status == 'Expired'" variant="danger" pill
                                                             class="float-right"
                                                    >
                                                        {{ email.status }}
                                                    </b-badge>
                                                </div>
                                                <hr>
                                            </b-media-body>
                                        </b-media>
                                    </ul>
                                    <div
                                        v-show="!this.emails.length"
                                        class="no-results"
                                    >
                                        <center><h5>No Items Found </h5></center>
                                    </div>
                                </vue-perfect-scrollbar>
                            </b-col>
                            <b-col md="9" v-show="segment == 'statistic'">
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
                            :items="historyItems"
                            class="rounded-bottom"
                        />
                    </b-modal>
                </b-card-code>
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
    BOverlay,
    BFormRating,
    BFormRadio
} from 'bootstrap-vue'
import { isDynamicRouteActive } from '@core/utils/utils'
import Ripple from 'vue-ripple-directive'
import { ref } from '@vue/composition-api'
import { max, min, required } from '@validations'
import { heightFade } from '@core/directives/animations'
import ViewPrivateJobs from '@/views/elements/ViewPrivateJobs.vue'
import VueApexCharts from 'vue-apexcharts'
import { $themeColors } from '@themeConfig'

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
        BFormRating,
        BFormRadio
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
                title: 'Post',
                icon: 'MailIcon',
                count: 0,
                route: {
                    name: 'jobs-feedback',
                    params: { segment: 'jobs' }
                }
            },
            {
                title: 'Draft',
                icon: 'Edit3Icon',
                count: 0,
                route: {
                    name: 'jobs-feedback',
                    params: { segment: 'draft_jobs' }
                }
            },
            {
                title: 'Invoice',
                icon: 'SendIcon',
                count: 0,
                route: {
                    name: 'jobs-feedback',
                    params: { segment: 'franchise_company_debits' }
                }
            }
        ]

        const emailLabel = [
            {
                title: 'Payment',
                color: 'success',
                route: {
                    name: 'jobs-feedback',
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
        // maxDate.setMonth(maxDate.getMonth() + 2)
        maxDate.setDate(now.getDate() + 15)
        nextFollowDate.setDate(now.getDate() + 3)
        return {
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
                phonenumber: 'Loading...',
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
            model_id: '',
            table: '',
            historyItems: [],
            getJobId: 0,
            getSizeOf: 0,
            callType: 0,
            emprMark: 0,
            show: false,
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
        var localUserId = JSON.parse(localStorage.getItem('userData')).id
        const callAxios = await this.callAxios('/getJobsFollowUpStatus', {
            localUserId: localUserId,
            limit: this.getJobId,
            segment: segment
        }, 'post')
        if (callAxios.data.status) {
            this.employerCard = callAxios.data
            callAxios.data.count.forEach((value, index) => {
                this.emailFilters[index].count = value.count
            })
            this.onLoadTableData(this.employerCard.list.id)
        } else {
            callAxios.data.list = { address: '' }
            this.employerCard = callAxios.data
            this.emails = []
        }
        this.show = false
        this.getSizeOf = callAxios.data.getSizeOf
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
            this.onLoadTableData(this.employerCard.list.id)
        }
    },
    methods: {
        ClickMethod(jobId, segment) {
            this.$bvModal.show('modal-footer-lg')
            this.model_id = jobId
            this.table = this.segment
        },
        async onCallTypeChange() {
            if (this.callType != 1) {
                this.emprMark = 0
            }
            let getConfirm = false
            if (this.callType == 0) {
                getConfirm = false
            } else if (this.callType == 1 && this.emprMark > 0) {
                getConfirm = confirm('Are you sure for this feedback and mark?')
            } else if (this.callType == 2) {
                getConfirm = confirm('Are you sure for this Not Spoken feedback?')
            } else {
                getConfirm = false
            }
            if (getConfirm) {
                this.show = true
                this.segment = this.$route.params.segment
                const segment = this.$route.params.segment
                var localUserId = JSON.parse(localStorage.getItem('userData')).id
                const callAxios = await this.callAxios('/addJobsFollowUpStatus', {
                    localUserId: localUserId,
                    jobId: this.employerCard.jobId,
                    callType: this.callType,
                    emprMark: this.emprMark,
                    segment: segment
                }, 'post')
                if (callAxios.data.status) {
                    alert(callAxios.data.message)
                    if (this.callType == 0) {
                        this.show = false
                    } else if (this.callType == 1 && this.emprMark > 0) {
                        this.historyIconName(true)
                    } else if (this.callType == 2) {
                        this.historyIconName(null)
                    } else {
                        this.show = false
                    }
                } else {
                    this.show = false
                }
                this.getSizeOf = callAxios.data.getSizeOf
                this.callType = 0
                this.emprMark = 0
                // this.show = false
                // this.getSizeOf = callAxios.data.getSizeOf
            }
        },
        async historyIconName(event) {
            this.show = true
            if (event == true) {
                this.getJobId++
            } else if (event == false) {
                this.getJobId--
            }
            this.getJobId = (this.getJobId < 0) ? 0 : this.getJobId
            this.getJobId = ((this.getSizeOf - 1) < this.getJobId) ? 0 : this.getJobId
            this.segment = this.$route.params.segment
            const segment = this.$route.params.segment
            var localUserId = JSON.parse(localStorage.getItem('userData')).id
            const callAxios = await this.callAxios('/getJobsFollowUpStatus', {
                localUserId: localUserId,
                limit: this.getJobId,
                segment: segment
            }, 'post')
            if (callAxios.data.status) {
                this.employerCard = callAxios.data
                callAxios.data.count.forEach((value, index) => {
                    this.emailFilters[index].count = value.count
                })
                this.onLoadTableData(this.employerCard.list.id)
            } else {
                callAxios.data.list = { address: '' }
                this.employerCard = callAxios.data
                this.emails = []
            }
            this.show = false
            this.getSizeOf = callAxios.data.getSizeOf
        },
        async onLoadTableData(employerID) {
            const segment = this.$route.params.segment
            var localUserId = JSON.parse(localStorage.getItem('userData')).id
            if (segment == 'statistic') {
                const chartData = []
                const chartLabel = []
                const callAxios = await this.callAxios('/getEmployerStatistic', {
                    localUserId: localUserId,
                    segment: segment,
                    employerID: employerID
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
                const callAxios = await this.callAxios('/getEmployerSegment', {
                    localUserId: localUserId,
                    segment: segment,
                    employerID: employerID
                }, 'post')
                this.emails = callAxios.data
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
</style>
