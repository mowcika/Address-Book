<template>
    <b-card-code border-variant="primary" title="Add Notification">
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
        <validation-observer v-show="!initial_loading" ref="simpleRules">
            <b-form>
                <b-row>

                    <!--Select App name -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Select App Name</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Select App Name"
                                rules="required"
                            >
                                <v-select
                                    v-model="data.appName"
                                    :options="appNameArray"
                                    :reduce="item=>item.id"
                                    label="name"
                                />

                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>


                    <!--Select Message Type -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Select Message Type</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Select Message Type"
                                rules="required"
                            >
                                <v-select
                                    v-model="data.msgType"
                                    :options="msgTypeArray"
                                    :reduce="item=>item.id"
                                    label="name"
                                    @input="msgtypechange()"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--Select Notification Type -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Select Notification Type</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Select Notification Type"
                                rules="required"
                            >
                                <v-select
                                    v-model="data.notiType"
                                    :options="notiTypeArray"
                                    :reduce="item=>item.id"
                                    label="name"
                                    @input="notiTypeChange()"
                                />

                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>
                    <!--Notification Title -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Title</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Title"
                                rules="required"
                            >
                                <b-form-input
                                    v-model="data.title"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="Title"
                                    type="text"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--Img-->
                    <b-col md="6" v-if="bigImgRequired">
                        <b-form-group>
                            <label class="required">Image</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Image"
                                rules="url|required"
                            >
                                <b-form-input
                                    v-model="data.img"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="Image"
                                    type="url"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--description -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Message</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Message"
                                rules="required"
                            >
                                <b-form-textarea
                                    v-model="data.msg"
                                    :class="data.msg ? data.msg.length >= maxChar500 ? 'text-danger' : '' : ''"
                                    class="char-textarea"
                                    placeholder="Application Postal Address"
                                    size="sm"
                                />
                                <small
                                    :class="data.msg ? data.msg.length >= maxChar500 ? 'bg-danger' : '' : ''"
                                    class="textarea-counter-value float-right"
                                >
                                    <span class="char-count">{{
                                        data.msg ? data.msg.length : 0
                                      }}</span> / {{ maxChar500 }}
                                </small>
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--Action Url -->
                    <b-col md="6" v-if="packagenameRequired">
                        <b-form-group>
                            <label class="required">Action Url</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Action Url"
                                rules="required"
                            >
                                <b-form-input
                                    v-model="data.pname"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="Action Url"
                                    type="text"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--class name -->
                    <b-col md="6" v-if="classRequired==1">
                        <b-form-group>
                            <label :class="`${classRequired==1 ? 'required' : ''}`">Class Name</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Class Name"

                                :rules="`${classRequired==1 ? 'required' : ''}`"
                            >
                                <b-form-input
                                    v-model="data.className"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="Class Name"
                                    type="text"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>
                    <b-col md="6" v-if="weburlRequired">
                        <b-form-group>
                            <label class="required">Website URL</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Website URL"
                                rules="url|required"
                            >
                                <b-form-input
                                    v-model="data.weburl"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="Image"
                                    type="url"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>
                    <b-col md="6">
                        <label class="required">Send Type</label>
                        <validation-provider
                            #default="{ errors }"
                            name="Send Type"
                            rules="required"
                        >

                            <b-form-group class="text-dark">
                                <b-form-radio-group v-model="data.sendType" @change="clickRadio()">
                                    <b-form-radio value="1"><small>Email</small></b-form-radio>
                                    <b-form-radio value="2"><small>Version</small></b-form-radio>
                                    <b-form-radio value="3"><small>ALL</small></b-form-radio>
                                    <b-form-radio value="4"><small>Vcodes</small></b-form-radio>
                                </b-form-radio-group>
                            </b-form-group>
                            <small class="text-danger">{{ errors[0] }}</small>
                        </validation-provider>
                    </b-col>
                    <b-col v-if="isversion!=0 && isAll==0" md="6">

                        <b-row>
                            <b-col md="6">
                                <b-form-group>
                                    <label class="required">Select Vcode</label>
                                    <validation-provider
                                        #default="{ errors }"
                                        name="Select Vcode"
                                        rules="required"
                                    >
                                        <v-select
                                            v-model="data.vcode"
                                            :options="vcodeArray"
                                            :reduce="item=>item.id"
                                            label="name"
                                        />

                                        <small class="text-danger">{{ errors[0] }}</small>
                                    </validation-provider>
                                </b-form-group>
                            </b-col>
                            <b-col md="6">
                                <b-form-group>
                                    <label class="required">Select App Name</label>
                                    <validation-provider
                                        #default="{ errors }"
                                        name="Select App Name"
                                        rules="required"
                                    >
                                        <v-select
                                            v-model="data.typeCondition"
                                            :options="sendTypeCondition"
                                            :reduce="item=>item.id"
                                            label="name"
                                        />

                                        <small class="text-danger">{{ errors[0] }}</small>
                                    </validation-provider>
                                </b-form-group>
                            </b-col>

                        </b-row>

                    </b-col>
                    <b-col v-if="isEmaill!=0 && isAll==0" md="6">
                        <b-form-group>
                            <label class="required">Send Type Value</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Action Url"
                                rules="required"
                            >
                                <b-form-input
                                    v-model="data.sendTypeValue"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="Action Url"
                                    type="text"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>

                    </b-col>
                    <b-col v-if="isAll==1" md="6">
                        <b-col md="6">
                            <b-form-group>
                                <label class="required">Send Type Value</label>

                            </b-form-group>
                        </b-col>
                    </b-col>
                    <b-col md="6">
                        <b-form-group>
                            <h5>Send Date Time</h5>
                            <flat-pickr
                                v-model="data.sendDate"
                                :config="{ enableTime: true,dateFormat: 'Y-m-d H:i'}"
                                class="form-control"
                            />
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-row>

                    <!-- Cancel button -->
                    <b-col md="6">
                        <b-button
                            v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                            type="reset"
                            variant="outline-danger"
                        >Cancel
                        </b-button>
                    </b-col>
                    <b-col md="6">

                        <!-- Save button -->
                        <b-button
                            v-if="!data.id"
                            type="submit"
                            variant="primary"
                            @click.prevent="validationForm"
                        >
                            Submit
                        </b-button>
                        <!-- Update button -->
                        <b-button
                            v-else
                            type="submit"
                            variant="primary"
                            @click.prevent="validationForm"
                        >
                            Update
                        </b-button>
                    </b-col>
                </b-row>
            </b-form>
        </validation-observer>


    </b-card-code>
</template>

<script>
import BCardCode from '@core/components/b-card-code'
import { ValidationObserver, ValidationProvider } from 'vee-validate'
import flatPickr from 'vue-flatpickr-component'
import {
    BButton,
    BCol,
    BForm,
    BFormCheckbox,
    BFormFile,
    BFormGroup,
    BFormInput,
    BFormRadio,
    BFormRadioGroup,
    BFormSelect,
    BFormTextarea,
    BRow,
    BSpinner,
} from 'bootstrap-vue'
import { max, min, required, } from '@validations'

import vSelect from 'vue-select'

import Ripple from 'vue-ripple-directive'

export default {
    components: {
        BFormRadioGroup,
        BFormRadio,
        BSpinner,
        BCardCode,
        ValidationProvider,
        ValidationObserver,
        BFormInput,
        BFormSelect,
        BFormGroup,
        BForm,
        BFormFile,
        BRow,
        BCol,
        BButton,
        BFormTextarea,
        vSelect,
        flatPickr,
        BFormCheckbox,
    },
    directives: {
        Ripple,
    },
    data() {
        return {
            data: {
                id: '',
                appName: '',
                notiType: '',
                msgType: '',
                title: '',
                img: '',
                msg: '',
                pname: '',
                className: '',
                rurl: '',
                sendType: '',
                sendTypeValue: '',
                typeCondition: '',
                vcode: '',
                weburl: '',
                sendDate: null,
                cby: JSON.parse(localStorage.getItem('userData')).id,

            },
            classRequired: 0,
            packagenameRequired: 0,
            bigImgRequired: 0,
            weburlRequired: 0,
            isEmaill: 0,
            isversion: 0,
            isAll: 0,
            initial_loading: true,
            variants: ['light', 'primary', 'secondary', 'danger', 'warning', 'success', 'info', 'dark'],
            appNameArray: [],
            options: [],
            jobtitle_options: [],
            notiTypeArray: [],
            msgTypeArray: [],
            sendTypeCondition: [{
                id: 1,
                name: '='
            }, {
                id: 2,
                name: '<'
            }, {
                id: 3,
                name: '>'
            }],
            vcodeArray: [],
            maxChar500: 500,
            required,
            min,
            max,
            userData: JSON.parse(localStorage.getItem('userData')),
        }
    },
    async created() {
        const callAxios = await this.callAxios('/getMaster', {}, 'post')
        this.notiTypeArray = callAxios.data.notiType
        this.msgTypeArray = callAxios.data.msgType
        this.appNameArray = callAxios.data.appMaster
        this.vcodeArray = callAxios.data.vcode
        if (this.$route.query.id) {
            this.data.id = this.$route.query.id
            const callAxiosEdit = await this.callAxios('/getSingleNotification', this.data, 'post')
            // console.log(callAxiosEdit.data[0])
            // console.log(callAxiosEdit.data.status)
            if (callAxiosEdit.status == 200) {
// alert(callAxiosEdit.data[0].id)
                this.data.id = callAxiosEdit.data[0].id
                this.data.appName = callAxiosEdit.data[0].app_name
                this.data.notiType = callAxiosEdit.data[0].notiType
                this.data.msgType = callAxiosEdit.data[0].msgType
                this.data.title = callAxiosEdit.data[0].title
                this.data.img = callAxiosEdit.data[0].img
                this.data.msg = callAxiosEdit.data[0].msg
                this.data.pname = callAxiosEdit.data[0].package_name
                this.data.className = callAxiosEdit.data[0].className
                this.data.sendType = callAxiosEdit.data[0].sendType
                this.data.sendTypeValue = callAxiosEdit.data[0].sendTypeValue
                this.data.typeCondition = callAxiosEdit.data[0].typeCondition
                this.data.vcode = callAxiosEdit.data[0].vcode
                this.data.weburl = callAxiosEdit.data[0].weburl
                this.data.sendDate = callAxiosEdit.data[0].sendDate
                this.notiTypeChange()
                this.msgtypechange()
                this.clickRadio()
            }
        }
        this.initial_loading = false
        // console.log( JSON.parse(localStorage.getItem('userData')).id)
    },
    methods: {
        notiTypeChange() {
            if (this.data.notiType == 2) {
                this.bigImgRequired = 1
            } else {
                this.bigImgRequired = 0
            }
        },
        msgtypechange() {
            if (this.data.msgType == 3) {
                this.classRequired = 1
                this.weburlRequired = 0
                this.packagenameRequired = 0
            } else if (this.data.msgType == 4) {
                this.weburlRequired = 1
                this.classRequired = 0
                this.packagenameRequired = 0
            } else if (this.data.msgType == 2) {
                this.weburlRequired = 0
                this.classRequired = 0
                this.packagenameRequired = 1
            } else {
                this.classRequired = 0
                this.weburlRequired = 0
            }
        },

        clickRadio() {
            const sendtype = this.data.sendType
            // alert(sendtype)
            if (sendtype == 1 || sendtype == 4) {
                this.isEmaill = 1
                this.isversion = 0
                this.isAll = 0
            } else if (sendtype == 2) {
                this.isEmaill = 0
                this.isversion = 1
                this.isAll = 0
            } else {
                this.isEmaill = 0
                this.isversion = 0
                this.isAll = 0
            }
        },
        validationForm() {
            this.$refs.simpleRules.validate()
                .then(async success => {
                    if (success) {
                        this.initial_loading = true
                        // eslint-disable-next-line
                        // alert('form submitted!')
                        // this.data.draft_id = 0;
                        // this.data.table = 'jobs';
                        // this.data.inby = this.userData.id;
                        console.log(this.data)
                        const config = {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        }
                        var callAxios = await this.callAxiosWithConfig('/saveNotification', this.data, 'post', config)
                        if (callAxios.status === 200) {
                            if (this.data.id) {

                                this.sweetAlertmethod('success', 'The notification was Updated successfully', '', 'btn-primary')
                                this.$router.push('/save/notification')
                            } else {
                                this.sweetAlertmethod('success', 'The notification was added successfully', '', 'btn-primary')
                            }

                            this.data.appName = ''
                            this.data.notiType = ''
                            this.data.msgType = ''
                            this.data.title = ''
                            this.data.img = ''
                            this.data.msg = ''
                            this.data.pname = ''
                            this.data.className = ''
                            this.data.rurl = ''
                            this.data.sendType = ''
                            this.data.sendTypeValue = ''
                            this.data.typeCondition = ''
                            this.data.vcode = ''
                            this.data.weburl = ''
                            this.data.sendDate = null
                            this.classRequired = 0
                            this.packagenameRequired = 0
                            this.bigImgRequired = 0
                            this.weburlRequired = 0
                            this.isEmaill = 0
                            this.isversion = 0
                            this.isAll = 0
                            this.initial_loading = false
                        } else {
                            this.sweetAlertmethod('error', 'Alert', callAxios.response.data.message, 'btn-primary')
                        }
                    }
                })
        },
        async savedraft() {
            this.data.id = 0
            this.data.table = 'draft_jobs'
            this.data.inby = this.userData.id
            console.log(this.data)
            const config = {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
            var callAxios = await this.callAxiosWithConfig('/savegovtjobs', this.data, 'post', config)
            if (callAxios.status === 200) {
                this.sweetAlertmethod('success', 'Success', '', 'btn-primary')
                this.$router.push('/listing/draft')
            } else {
                this.sweetAlertmethod('error', 'Alert', callAxios.response.data.message, 'btn-primary')
            }

        },

    },

}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>
