<template>
    <b-row class="match-height">
        <b-col cols="12">
            <b-overlay
                :show="show"
                rounded="sm"
            >
                <b-card-code
                    title=""
                >
                    <b-alert
                        v-height-fade.appear
                        variant="warning"
                        :show="sentStatus"
                        class="mb-1"
                    >
                        <div class="alert-body" style="text-align: center;">
                            Tomorrow Free Expiry Count : <span>{{ this.totalCount }}</span>
                            <b-button
                                v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                                v-show="this.totalCount > 0"
                                variant="success"
                                class="mr-1 float-right"
                                @click="msg91FreeExpirySend"
                            >Send
                            </b-button>
                        </div>
                    </b-alert>
                    <b-alert
                        v-height-fade.appear
                        variant="danger"
                        :show="!sentStatus"
                        class="mb-1"
                    >
                        <div class="alert-body" style="text-align: center;">
                            Tomorrow Free Expiry Count : <span>{{ this.totalCount }} Already Sent</span>
                        </div>
                    </b-alert>
                    <b-table
                        :items="historyItems"
                        class="rounded-bottom"
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
        BOverlay
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
            totalCount: 'loading...',
            sentStatus: true,
            show: false,
            historyItems: [],
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
        const callAxios = await this.callAxios('/msg91FreeExpiry', {
            localUserId: localUserId,
            segment: segment
        }, 'post')
        if (callAxios.data.status) {
            this.sentStatus = callAxios.data.status
            this.totalCount = callAxios.data.getSizeOf
            this.historyItems = callAxios.data.message
        } else {
            this.sentStatus = callAxios.data.status
            this.totalCount = callAxios.data.getSizeOf
            this.historyItems = callAxios.data.message
        }
        this.show = false
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
        async msg91FreeExpirySend() {
            this.show = true
            var localUserId = JSON.parse(localStorage.getItem('userData')).id
            const callAxios = await this.callAxios('/msg91FreeExpirySend', {
                localUserId: localUserId
            }, 'post')
            if (callAxios.data.status) {
                this.sentStatus = callAxios.data.status
                this.totalCount = callAxios.data.getSizeOf
                this.historyItems = callAxios.data.message
            } else {
                this.sentStatus = callAxios.data.status
                this.totalCount = callAxios.data.getSizeOf
            }
            this.show = false
            alert(callAxios.data.test)
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
