<template>
    <b-modal
        id="modal-footer-lg"
        centered
        hide-backdrop
        ok-only
        ok-title="Close"
        size="xl"
        title="Notification Details"

    >
        <div v-show="initial_loading" class="text-center m-5">
            <b-spinner
                v-for="variant in variants"
                :key="variant"
                :variant="variant"
                class="m-2"
                style="width: 3rem; height: 3rem;"
                type="grow"
            />
        </div>
        <div v-show="!initial_loading">
            <h1>Notification ID - #{{ id }}</h1>
            <b-row>
                <b-col
                    cols="12"
                    lg="12"
                    md="12"
                    xl="12"
                >
                    <b-card>


                        <b-row>

                            <!-- User Info: Left col -->
                            <b-col
                                class="d-flex justify-content-between flex-column"
                                cols="21"
                                xl="12"
                            >
                                <!-- User Avatar & Action Buttons -->
                                <div class="d-flex justify-content-start">
                                    <b-avatar
                                        :src="data.image"
                                        :text="data.image"
                                        rounded
                                        size="104px"
                                        variant="light-primary"
                                    />
                                    <div class="d-flex flex-column ml-1">
                                        <div class="mb-1">
                                            <h4 class="mb-0 text-capitalize center">
                                                {{ data.title }}
                                            </h4>
                                        </div>
                                        <div class="d-flex flex-wrap">
                                            <b-button
                                                :href="'/save/notification/?table=jobs&id='+data.id"
                                                variant="primary"
                                            >
                                                Edit
                                            </b-button>

                                        </div>
                                    </div>
                                </div>

                                <!-- User Stats -->

                            </b-col>


                        </b-row>

                    </b-card>
                </b-col>

            </b-row>

            <b-list-group>
                <b-list-group-item class="text-capitalize"><b>App Name :</b> {{ data.appName }}
                </b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>Notification Type :</b> {{
                    data.noti
                    }}
                </b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>Message Type :</b> {{
                    data.msgType
                    }}
                </b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>Message :</b> {{ data.msg }}</b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>Package Name :</b> {{ data.package_name }}
                </b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>Class Name :</b> {{ data.className }}</b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>Redirect URL :</b> {{ data.weburl }}</b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>Send Type :</b> {{ data.sendType }}
                </b-list-group-item>

            </b-list-group>
        </div>
    </b-modal>
</template>

<script>
import {
    BAvatar,
    BBadge,
    BButton,
    BCard,
    BCardBody,
    BCardHeader,
    BCardText,
    BCol,
    BListGroup,
    BListGroupItem,
    BModal,
    BRow,
    BSpinner,
    BTab,
    BTabs,
    VBModal,
} from 'bootstrap-vue'
import BCardCode from '@core/components/b-card-code'
import Ripple from 'vue-ripple-directive'
import VueMoment from 'vue-moment'
import moment from 'moment-timezone'

export default {
    props: ['id', 'radiesArray', 'table'],
    components: {
        BCardCode,
        BCardText,
        BButton,
        BModal,
        BCard,
        BRow,
        BCol,
        BAvatar,
        BCardHeader,
        BCardBody,
        BBadge,
        VueMoment,
        BTab,
        BTabs,
        BListGroup,
        BListGroupItem,
        BSpinner,
    },
    directives: {
        'b-modal': VBModal,
        Ripple,
    },
    data() {
        return {
            model_check: '',
            data: {
                'title': '',
                'appName': '',
                'noti': '',
                'msgType': '',
                'image': '',
                'msg': '',
                'package_name': '',
                'className': '',
                'weburl': '',
                'sendType': '',
            },
            initial_loading: true,
            variants: ['light', 'primary', 'secondary', 'danger', 'warning', 'success', 'info', 'dark'],
        }

    },
    watch: {

        id: function (newVal) {
            this.data.name = 'Loading... Please Wait..'
            this.model_check = newVal
            console.log(this.table)
            this.getData(this.model_check)
        }
    },
    methods: {

        async getData(value) {

            this.initial_loading = true
            const callAxios = await this.callAxios('/getNotification', {
                id: value,
            }, 'post')
            console.log(callAxios.data.data[0])
            var return_data = callAxios.data.data[0]
            this.initial_loading = false
            if (callAxios.status === 200) {
                const bigImg = ''
                this.data = return_data
                this.data.title = callAxios.data.data[0].title
                this.data.appName = callAxios.data.data[0].app_name
                this.data.noti = callAxios.data.data[0].noti
                this.data.msgType = callAxios.data.data[0].msgType
                this.data.msg = callAxios.data.data[0].msg
                this.data.package_name = callAxios.data.data[0].package_name
                this.data.className = callAxios.data.data[0].className
                this.data.weburl = callAxios.data.data[0].weburl
                this.data.sendType = callAxios.data.data[0].sendType
                this.bigImg = callAxios.data.data[0].img
                console.log(this.bigImg)
                if (this.bigImg != '') {
                    this.data.image = this.bigImg
                } else {
                    this.data.image = 'https://nithrajobs.com/assets/dist/img/private_job.webp'
                }

            } else {
                this.sweetAlertmethod('error', 'Error', callAxios.response.data.message, 'btn-primary')
            }
        },
    }

}
</script>
