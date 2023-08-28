<template>
    <b-card-code border-variant="primary" title="Company Details">
        <validation-observer ref="simpleRules">
            <b-form>
                <b-row>

                    <!--  Company Name in english-->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Company Name in English</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Company Name in English"
                                rules="required"
                            >
                                <b-form-input
                                    v-model="employer_reg_data.companyname"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="Company Name in English"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--Company Name in Tamil -->

                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Company Name in Telugu</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Company Name in Telugu"
                                rules="required"
                            >
                                <b-form-input
                                    v-model="employer_reg_data.tcompanyname"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="Company Name in Telugu"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--Street Address -->

                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Street Address</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Street Address"
                                rules="required"
                            >
                                <b-form-input
                                    v-model="employer_reg_data.address"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="Street Address"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!-- state -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">State </label>
                            <v-select
                                v-model="employer_reg_data.state"
                                :options="stateSelect"
                                :reduce="(stateSelect) => stateSelect.value"
                                :state="employer_reg_data.state === null ? false : true"
                                label="text"
                            />
                            <p class="mt-1 mb-0">
                                Selected: <strong>{{ employer_reg_data.state }}</strong>
                            </p>
                        </b-form-group>
                    </b-col>

                    <!-- District -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">District </label>
                            <v-select
                                v-model="employer_reg_data.district"
                                :options="distSelect"
                                :reduce="(distSelect) => distSelect.value"
                                :state="employer_reg_data.district === null ? false : true"
                                :value="employer_reg_data.district"
                                @input="check(employer_reg_data.district)"
                                label="text"

                            />

                        </b-form-group>
                    </b-col>

                    <!-- city -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">City </label>
                            <v-select
                                v-model="employer_reg_data.city"
                                :options="citySelect"
                                :reduce="(citySelect) => citySelect.value"
                                :state="employer_reg_data.city === null ? false : true"
                                label="text"
                            />
                        </b-form-group>
                    </b-col>

                    <!-- pincode-->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Pincode</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Pincode"
                                rules="required|between:10,20|max:6"
                            >
                                <b-form-input
                                    v-model="employer_reg_data.pincode"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="Pincode"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>
                    <!--website-->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Website</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Website"
                                rules="url"
                            >
                                <b-form-input
                                    v-model="employer_reg_data.website"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="Enter Website"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!-- Company Registration Type -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Company Registration Type </label>
                            <v-select
                                v-model="employer_reg_data.industry_type"
                                :options="industry_type"
                                :reduce="(industry_type) => industry_type.value"
                                :state="employer_reg_data.industry_type === null ? false : true"
                                label="text"
                            />
                            <p class="mt-1 mb-0">
                                Selected: <strong>{{ employer_reg_data.industry_type }}</strong>
                            </p>
                        </b-form-group>
                    </b-col>

                    <!-- Business Category* -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Business Category* </label>
                            <v-select
                                v-model="employer_reg_data.company_type"
                                :options="company_type"
                                :reduce="(company_type) => company_type.value"
                                :state="employer_reg_data.company_type === null ? false : true"
                                label="text"
                            />
                            <p class="mt-1 mb-0">
                                Selected: <strong>{{ employer_reg_data.company_type }}</strong>
                            </p>
                        </b-form-group>
                    </b-col>
                    <!--                    Company ID Proof Type-->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Company ID Proof Type </label>
                            <v-select
                                v-model="employer_reg_data.company_proof_type"
                                :options="companyProof"
                                :reduce="(companyProof) => companyProof.value"
                                :state="employer_reg_data.company_proof_type === null ? false : true"
                                label="text"
                            />
                            <p class="mt-1 mb-0">
                                Selected: <strong>{{ employer_reg_data.company_proof_type }}</strong>
                            </p>
                        </b-form-group>
                    </b-col>
                    <!-- Company id Proof  -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Company ID Proof </label>
                            <validation-provider
                                #default="{ errors }"
                                name="Company ID Proof"
                                rules="required"
                            >
                                <b-form-file
                                    v-model="employer_reg_data.id_proof"
                                    drop-placeholder="Drop file here..."
                                    no-drop
                                    placeholder="Choose a file or drop it here..."
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>
                    <!--                    Company Address Proof Type-->
                    <b-col md="6">

                        <b-form-group>
                            <label class="required">Company Address Proof Type </label>
                            <v-select
                                v-model="employer_reg_data.addressProof"
                                :options="companyProof"
                                :reduce="(companyProof) => companyProof.value"
                                :state="employer_reg_data.addressProof === null ? false : true"
                                label="text"
                            />
                            <p class="mt-1 mb-0">
                                Selected: <strong>{{ employer_reg_data.addressProof }}</strong>
                            </p>
                        </b-form-group>
                    </b-col>
                    <!-- Company Address Proof  -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Company Address Proof </label>
                            <validation-provider
                                #default="{ errors }"
                                name="Email"
                                rules="required|email"
                            >
                                <b-form-file
                                    v-model="employer_reg_data.companyProof"
                                    drop-placeholder="Drop file here..."
                                    no-drop
                                    placeholder="Choose a file or drop it here..."
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>
                    <!--Image-->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Company Logo</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Company Logo"
                                rules="url"
                            >
                                <b-form-input
                                    v-model="employer_reg_data.image"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="Enter Company Logo"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>
                    <!--gst number-->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">GST Number</label>
                            <validation-provider
                                #default="{ errors }"
                                name="GST Number"
                                rules="required"
                            >
                                <b-form-input
                                    v-model="employer_reg_data.gst"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="GST Number"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>


                    <!-- submit button -->

                </b-row>
            </b-form>
            <b-row>
                <b-col cols="12">
                    <b-button
                        variant="primary"
                        @click="add"
                    >
                        Submit
                    </b-button>
                </b-col>
            </b-row>

        </validation-observer>


    </b-card-code>
</template>

<script>
import BCardCode from '@core/components/b-card-code'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import {
    BFormInput, BFormGroup, BForm, BRow, BCol, BButton, BFormFile, BFormSelect,
} from 'bootstrap-vue'
import {
    required, email, confirmed, url, between, alpha, integer, password, min, digits, alphaDash, length,
} from '@validations'

import vSelect from 'vue-select'

export default {
    components: {
        BCardCode,
        ValidationProvider,
        ValidationObserver,
        vSelect,
        BFormInput,
        BFormGroup,
        BFormFile,
        BFormSelect,
        BForm,
        BRow,
        BCol,
        BButton,
    },

    data() {
        return {
            employer_reg_data: {
                id: '',
                companyname: '',
                tcompanyname: '',
                id_proof: '',
                companyProof: '',
                company_type: '',
                id_proof_type: '',
                address: '',
                pincode: '',
                gst: '',
                image: '',
                website: '',
                industry_type: '',
                pan_card: '',
                company_proof_type: '',
                companyNature: '',
                state: {
                    value: 1,
                    text: 'TAMIL NADU'
                },
                district: '',
                city: '',
                type: '1',
            },
            district_id: {
                disid: '',
            },
            required,
            email,
            min,
            url,
            length,

            industry_type: [],
            companyProof: [],
            addressProof: [],
            company_type: [],
            distSelect: [],
            stateSelect: [],
            citySelect: [],

        }
    },
    created() {
        this.total = 'Loading...'
        axios.post('/person-proof')
            .then(res => {
                // console.log(res.data)
                // console.log(res.data.personProof)
                this.company_type = res.data.company_type
                this.industry_type = res.data.industry_type
                this.companyProof = res.data.companyProof
                // this.total = res.data.length;
                // this.rows = res.data
            })

        axios.post('/state-dist')
            .then(res => {
                this.distSelect = res.data.dist
                this.stateSelect = res.data.state
            })

    },
    methods: {
        add() {
            console.log('nithra')
// this.$emit('pandiya');
        },

        check(value) {
            if (value) {
                this.district_id.disid = value
                this.employer_reg_data.city = ''
                this.citySelect = ''
                let axiosSource = axios.CancelToken.source()
                this.request = { cancel: axiosSource.cancel }

                axios
                    .post('/getCity', {
                        cancelToken: axiosSource.token,
                        value,
                    })
                    .then((res) => {
                        this.citySelect = res.data.areas
                    })
            }

        }
    },
}
</script>
