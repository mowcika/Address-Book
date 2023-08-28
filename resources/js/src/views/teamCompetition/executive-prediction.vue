<template>
    <b-row class="match-height">
        <b-col cols="12">
            <b-overlay
                :show="show"
                rounded="sm"
            >
                <b-card-code
                    title="Tomorrow Follow up & Expiry"
                >
                    <b-row>
                        <b-col cols="4">
                            <label>Select FollowUp & Expiry Date</label>
                            <flat-pickr
                                :disabled=true
                                :config="datePickerConfig"
                                placeholder="Select Date"
                                v-model="startDate"
                                class="form-control"
                                @on-change="onLoadTableData"
                            />
                        </b-col>
                        <b-col cols="4">
                            <label>Select Teams</label>
                            <v-select
                                v-model="sourceFilter"
                                label="teamName"
                                :reduce="item=>item.members"
                                :options="getFilterSource"
                                placeholder="Select Teams"
                                @input="onLoadTableData"
                            />
                        </b-col>
                        <b-col cols="4">
                            <download-excel :data="historyItems">
                                Download Data
                                <img src="@/assets/images/icons/xls.png" width="50"/>
                            </download-excel>
                        </b-col>
                    </b-row>
                    <br>
                    <hr>
                    <br>
                    <b-table
                        striped hover bordered responsive foot-clone
                        :sort-by.sync="sortBy"
                        :sort-desc.sync="sortDesc"
                        :fields="fields"
                        :items="historyItems"
                        class="rounded-bottom"
                        responsive="sm"
                        @cell-clicked="alert()"
                    />
                </b-card-code>
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
        </b-col>
    </b-row>
</template>

<script>
import BCardCode from '@core/components/b-card-code'
import VuePerfectScrollbar from 'vue-perfect-scrollbar'
import { useClipboard } from '@vueuse/core'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
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
import vSelect from 'vue-select'
import flatPickr from 'vue-flatpickr-component'
import { isDynamicRouteActive } from '@core/utils/utils'
import Ripple from 'vue-ripple-directive'
import { ref } from '@vue/composition-api'
import { max, min, required } from '@validations'
import { heightFade } from '@core/directives/animations'
import ViewPrivateJobs from '@/views/elements/ViewPrivateJobs.vue'
import VueApexCharts from 'vue-apexcharts'
import downloadExcel from 'vue-json-excel'
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
        vSelect,
        flatPickr,
        downloadExcel
    },
    props: {},
    setup() {

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
        const tomorrow = new Date()
        // maxDate.setMonth(maxDate.getMonth() + 2)
        maxDate.setDate(now.getDate() + 15)
        nextFollowDate.setDate(now.getDate() + 3)
        tomorrow.setDate(now.getDate() + 1)
        return {
            data: {
                min: minDate,
                max: maxDate,
                nextFollowDate: nextFollowDate,
                isMobileDisabled: false,
                sample: [],
            },
            fields: [
                {
                    key: 'executive',
                    label: 'Executive',
                    sortable: true,
                },
                {
                    key: 'followUpPaid',
                    label: 'FollowUp Paid',
                    thClass: 'text-right',
                    tdClass: 'text-right',
                    sortable: true,
                },
                {
                    key: 'followUpFree',
                    label: 'FollowUp Free',
                    thClass: 'text-right',
                    tdClass: 'text-right',
                    sortable: true,
                },
                {
                    key: 'expiryPaid',
                    label: 'Expiry Paid',
                    thClass: 'text-right',
                    tdClass: 'text-right',
                    sortable: true,
                },
                {
                    key: 'expiryFree',
                    label: 'Expiry Free',
                    thClass: 'text-right',
                    tdClass: 'text-right',
                    sortable: true,
                },
                {
                    key: 'Total',
                    label: 'Total',
                    thClass: 'text-right',
                    tdClass: 'text-right',
                    sortable: true,
                }
            ],
            sortBy: 'executive',
            // sortBy: ['year', 'period'],
            sortDesc: false,
            sentStatus: true,
            show: false,
            historyItems: [],
            segment: this.$route.params.segment,
            initial_loading: true,
            datePickerConfig: {
                altFormat: 'd - M - Y',
                altInput: true,
                dateFormat: 'Y-m-d',
            },
            startDate: tomorrow,
            disableStartEnddate: false,
            sourceFilter: '',
            getFilterSource: []
        }

    },
    beforeMount() {
        // this.onLoadTableData()
    },
    async created() {
        const callAxios = await this.callAxios('/getTeamCounts', {}, 'post')
        this.getFilterSource = callAxios.data.gTeamCount
        this.onLoadTableData()
    },
    async mounted() {
        this.$root.$on('btnCellRenderer1Event()', (index, url, data, dataJson) => {
            this.btnCellRenderer1(index, url, data, dataJson)
        })
    },
    watch: {
        async $route(to, from) {
            this.segment = this.$route.params.segment
            this.onLoadTableData(this.employerCard.list.id)
        }
    },
    methods: {
        async onLoadTableData() {
            this.show = true
            this.segment = this.$route.params.segment
            const segment = this.$route.params.segment
            var localUserId = JSON.parse(localStorage.getItem('userData')).id
            const callAxios = await this.callAxios('/executivePrediction', {
                localUserId: localUserId,
                startDate: this.startDate,
                sourceFilter: this.sourceFilter
            }, 'post')
            if (callAxios.data.status) {
                this.sentStatus = callAxios.data.status
                this.historyItems = callAxios.data.message
            } else {
                this.sentStatus = callAxios.data.status
                this.historyItems = callAxios.data.message
            }
            this.show = false
        },
        onBtnExportDataAsExcel() {
            this.historyItems.exportDataAsExcel()
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
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';

</style>
