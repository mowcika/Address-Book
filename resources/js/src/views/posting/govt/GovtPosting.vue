<template>
    <b-card-code border-variant="primary" title="Government Posting">
        <div class="text-center m-5" v-show="initial_loading">
            <b-spinner
                v-for="variant in variants"
                :key="variant"
                :variant="variant"
                style="width: 3rem; height: 3rem;"
                class="m-2"
                type="grow"
            />
        </div>
        <validation-observer v-show="!initial_loading" ref="simpleRules">
            <b-form>
                <b-row>
                    <!--Select State / Central -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Select State / Central</label>
                            <v-select
                                v-model="data.jobtype"
                                label="title"
                                :reduce="item=>item.id"
                                :options="select_employer"
                            />
                        </b-form-group>
                    </b-col>

                    <!--Select Employer -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Select Employer</label> <small class="text-muted">Enter at least 3
                            characters to Search</small>
                            <validation-provider
                                #default="{ errors }"
                                name="Employer"
                                rules="required"
                            >
                                <!--                                <b-form-input-->
                                <!--                                    v-model="data.typed_employername"-->
                                <!--                                    v-on:keyup="keymonitor"-->
                                <!--                                    :state="errors.length > 0 ? false:null"-->
                                <!--                                    placeholder="Employer Name"-->
                                <!--                                />-->
                                <!--                                @keydown.native.enter="keymonitor"-->
                                <!--                                @search="fetchOptions"-->
                                <!--                                @search:blur=""-->
                                <!--                                :map-keydown="vselect_handlers"-->
                                <v-select
                                    :options="options"
                                    v-model="data.employerid"
                                    label="name"
                                    :reduce="item=>item.id"
                                    @search="onemployersearch"
                                />

                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--Select Job Title -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Select Job title</label> <small class="text-muted">Enter at least 3
                            characters to Search</small>
                            <validation-provider
                                #default="{ errors }"
                                name="Select Job title"
                                rules="required"
                            >
                                <v-select
                                    :options="jobtitle_options"
                                    v-model="data.jobtitle_id"
                                    label="name"
                                    :reduce="item=>item.id"
                                    @search="ontitlesearch"
                                    @input="settitle"
                                />

                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--Job Title Text -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Job title</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Job title"
                                rules="required|min:3|max:100"
                            >
                                <b-form-input
                                    v-model="data.jobtitle"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="Job title"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--Select Work-mode -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Select Work-mode</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Select Work-mode"
                                rules="required"
                            >
                                <v-select
                                    v-model="data.workmode"
                                    label="title"
                                    :reduce="item=>item.id"
                                    :options="select_work_mode"
                                />

                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--No of Vacancy -->
                    <b-col md="6">
                        <b-form-group>
                            <label>No of Vacancy</label>
                            <validation-provider
                                #default="{ errors }"
                                name="No of Vacancy"
                                rules="integer|max:50"
                            >
                                <b-form-input
                                    type="number"
                                    v-model="data.noofvancancy"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="No of Vacancy"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--Select Single Qualification -->
                    <b-col md="6">
                        <b-form-group>
                            <label>Select Single Qualification</label>
                            <v-select
                                :clearSearchOnSelect=false
                                :closeOnSelect=false
                                multiple
                                v-model="data.singlequalification"
                                label="name"
                                :reduce="item=>item.id"
                                :options="selectSingleQualification"
                            />
                        </b-form-group>
                    </b-col>

                    <!--Select Group Qualification -->
                    <b-col md="6">
                        <b-form-group>
                            <label>Select Group Qualification</label>
                            <v-select
                                :clearSearchOnSelect=false
                                :closeOnSelect=false
                                multiple
                                v-model="data.groupqualification"
                                label="name"
                                :reduce="item=>item.id"
                                :options="selectGroupQualification"
                                @input="setsingleFromGroupQualification"
                            />
                        </b-form-group>
                    </b-col>

                    <!--Job Detail & Posting Details -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Job Detail & Posting Details</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Job Detail & Posting Details"
                                rules="required|max:500"
                            >
                                <b-form-textarea
                                    v-model="data.jobDetails"
                                    placeholder="Job Detail & Posting Details"
                                    size="sm"
                                    class="char-textarea"
                                    :class="data.jobDetails ? data.jobDetails.length >= maxChar500 ? 'text-danger' : '' : ''"
                                />
                                <small
                                    class="textarea-counter-value float-right"
                                    :class="data.jobDetails ? data.jobDetails.length >= maxChar500 ? 'bg-danger' : '' : ''"
                                >
                                    <span class="char-count">{{ data.jobDetails ? data.jobDetails.length : 0 }}</span> /
                                    {{ maxChar500 }}
                                </small>
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--Age Limit -->
                    <b-col md="6">
                        <b-form-group>
                            <label>Age Limit</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Age Limit"
                                rules="max:500"
                            >
                                <b-form-textarea
                                    v-model="data.agelimit"
                                    placeholder="Age Limit"
                                    size="sm"
                                    class="char-textarea"
                                    :class="data.agelimit ? data.agelimit.length >= maxChar500 ? 'text-danger' : '' : ''"
                                />
                                <small
                                    class="textarea-counter-value float-right"
                                    :class="data.agelimit ? data.agelimit.length >= maxChar500 ? 'bg-danger' : '' : ''"
                                >
                                    <span class="char-count">{{ data.agelimit ? data.agelimit.length : 0 }}</span> /
                                    {{ maxChar500 }}
                                </small>
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--Salary -->
                    <b-col md="6">
                        <b-form-group>
                            <label>Salary</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Salary"
                                rules="max:500"
                            >
                                <b-form-textarea
                                    v-model="data.salary"
                                    placeholder="Salary"
                                    size="sm"
                                    class="char-textarea"
                                    :class="data.salary ? data.salary.length >= maxChar500 ? 'text-danger' : '' : ''"
                                />
                                <small
                                    class="textarea-counter-value float-right"
                                    :class="data.salary ? data.salary.length >= maxChar500 ? 'bg-danger' : '' : ''"
                                >
                                    <span class="char-count">{{ data.salary ? data.salary.length : 0 }}</span> /
                                    {{ maxChar500 }}
                                </small>
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--Selection Process -->
                    <b-col md="6">
                        <b-form-group>
                            <label>Selection Process</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Selection Process"
                                rules="max:500"
                            >
                                <b-form-textarea
                                    v-model="data.selectionProcess"
                                    placeholder="Selection Process"
                                    size="sm"
                                    class="char-textarea"
                                    :class="data.selectionProcess ? data.selectionProcess.length >= maxChar500 ? 'text-danger' : '' : ''"
                                />
                                <small
                                    class="textarea-counter-value float-right"
                                    :class="data.selectionProcess ? data.selectionProcess.length >= maxChar500 ? 'bg-danger' : '' : ''"
                                >
                                    <span class="char-count">{{
                                            data.selectionProcess ? data.selectionProcess.length : 0
                                        }}</span> / {{
                                    maxChar500
                                    }}
                                </small>
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--Fees Details -->
                    <b-col md="6">
                        <b-form-group>
                            <label>Fees Details</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Fees Details"
                                rules="max:500"
                            >
                                <b-form-textarea
                                    v-model="data.feesDetails"
                                    placeholder="Fees Details"
                                    size="sm"
                                    class="char-textarea"
                                    :class="data.feesDetails ? data.feesDetails.length >= maxChar500 ? 'text-danger' : '' : ''"
                                />
                                <small
                                    class="textarea-counter-value float-right"
                                    :class="data.feesDetails ? data.feesDetails.length >= maxChar500 ? 'bg-danger' : '' : ''"
                                >
                                    <span class="char-count">{{ data.feesDetails ? data.feesDetails.length : 0 }}</span>
                                    / {{ maxChar500 }}
                                </small>
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--Exam Center -->
                    <b-col md="6">
                        <b-form-group>
                            <label>Exam Center</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Exam Center"
                                rules="max:500"
                            >
                                <b-form-textarea
                                    v-model="data.examCenter"
                                    placeholder="Exam Center"
                                    size="sm"
                                    class="char-textarea"
                                    :class="data.examCenter ? data.examCenter.length >= maxChar500 ? 'text-danger' : '' : ''"
                                />
                                <small
                                    class="textarea-counter-value float-right"
                                    :class="data.examCenter ? data.examCenter.length >= maxChar500 ? 'bg-danger' : '' : ''"
                                >
                                    <span class="char-count">{{ data.examCenter ? data.examCenter.length : 0 }}</span> /
                                    {{ maxChar500 }}
                                </small>
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--How to Apply ? -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">How to Apply ?</label>
                            <validation-provider
                                #default="{ errors }"
                                name="How to Apply ?"
                                rules="required|max:500"
                            >
                                <b-form-textarea
                                    v-model="data.howToApply"
                                    placeholder="How to Apply ?"
                                    size="sm"
                                    class="char-textarea"
                                    :class="data.howToApply ? data.howToApply.length >= maxChar500 ? 'text-danger' : '' : ''"
                                />
                                <small
                                    class="textarea-counter-value float-right"
                                    :class="data.howToApply ? data.howToApply.length >= maxChar500 ? 'bg-danger' : '' : ''"
                                >
                                    <span class="char-count">{{ data.howToApply ? data.howToApply.length : 0 }}</span> /
                                    {{ maxChar500 }}
                                </small>
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--Application Postal Address -->
                    <b-col md="6">
                        <b-form-group>
                            <label>Application Postal Address</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Application Postal Address"
                                rules="max:500"
                            >
                                <b-form-textarea
                                    v-model="data.postalAddress"
                                    placeholder="Application Postal Address"
                                    size="sm"
                                    class="char-textarea"
                                    :class="data.postalAddress ? data.postalAddress.length >= maxChar500 ? 'text-danger' : '' : ''"
                                />
                                <small
                                    class="textarea-counter-value float-right"
                                    :class="data.postalAddress ? data.postalAddress.length >= maxChar500 ? 'bg-danger' : '' : ''"
                                >
                                    <span class="char-count">{{
                                            data.postalAddress ? data.postalAddress.length : 0
                                        }}</span> / {{ maxChar500 }}
                                </small>
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--Exam Date -->
                    <b-col md="4">
                        <b-form-group>
                            <label>Exam Date</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Exam Date"
                            >
                                <flat-pickr
                                    :config="datePickerConfig"
                                    placeholder="Select Exam date"
                                    v-model="data.examDate"
                                    class="form-control"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--Start Date -->
                    <b-col md="4">
                        <b-form-group>
                            <label>Start Date</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Start Date"
                            >
                                <flat-pickr
                                    :config="datePickerConfig"
                                    placeholder="Select Start date"
                                    v-model="data.startDate"
                                    class="form-control"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--End Date -->
                    <b-col md="4">
                        <b-form-group>
                            <label class="required">End Date</label>
                            <validation-provider
                                #default="{ errors }"
                                name="End Date"
                                rules="required"
                            >
                                <flat-pickr
                                    :config="datePickerConfig"
                                    placeholder="Select End date"
                                    v-model="data.endDate"
                                    class="form-control"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--Website 1-->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Website 1</label>
                            <validation-provider
                                name="Website 1"
                                #default="{ errors }"
                                rules="required|url"
                            >
                                <b-form-input
                                    v-model="data.website1"
                                    :state="errors.length > 0 ? false:null"
                                    type="url"
                                    placeholder="Website 1"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--Website 2-->
                    <b-col md="6">
                        <b-form-group>
                            <label>Website 2</label>
                            <validation-provider
                                name="Website 2"
                                #default="{ errors }"
                                rules="url"
                            >
                                <b-form-input
                                    v-model="data.website2"
                                    :state="errors.length > 0 ? false:null"
                                    type="url"
                                    placeholder="Website 2"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--Select Job Source -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Select Job Source</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Select Job Source"
                                rules="required"
                            >
                                <v-select
                                    v-model="data.jobSource"
                                    label="name"
                                    :reduce="item=>item.id"
                                    :options="select_job_source"
                                />

                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--Select Referred By -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Select Referred By</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Select Job title"
                                rules="required"
                            >
                                <v-select
                                    v-model="data.referredby"
                                    label="name"
                                    :reduce="item=>item.id"
                                    :options="selectReferredby"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--Search Keyword -->
                    <b-col md="6">
                        <b-form-group>
                            <label>Search Keyword</label>
                            <v-select
                                :clearSearchOnSelect=false
                                :closeOnSelect=false
                                :closeOnClick="false"
                                multiple
                                taggable
                                label="key_word"
                                v-model="data.key_word_db_web"
                                :options="key_word_db_web"
                                @input="selected_keywords"
                            />
                        </b-form-group>
                    </b-col>

                    <!--Verified-->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Verified</label>
                            <validation-provider
                                name="Verified"
                                #default="{ errors }"
                                rules="required"
                            >
                                <b-form-checkbox
                                    v-model="data.verified"
                                    value="1"
                                > Verified
                                </b-form-checkbox>
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--List View Image URL-->
                    <b-col md="6">
                        <b-form-group>
                            <label>List View Image URL</label>
                            <validation-provider
                                name="List View Image URL"
                                #default="{ errors }"
                                rules="url"
                            >
                                <b-form-input
                                    v-model="data.listViewImageURL"
                                    :state="errors.length > 0 ? false:null"
                                    type="url"
                                    placeholder="List View Image URL"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--Detailed View Image URL-->
                    <b-col md="6">
                        <b-form-group>
                            <label>Detailed View Image URL</label>
                            <validation-provider
                                name="Detailed View Image URL"
                                #default="{ errors }"
                                rules="url"
                            >
                                <b-form-input
                                    v-model="data.detailedViewImageURL"
                                    :state="errors.length > 0 ? false:null"
                                    type="url"
                                    placeholder="Detailed View Image URL"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--Banner Web URL-->
                    <b-col md="12">
                        <b-form-group>
                            <label>Banner Web URL</label>
                            <validation-provider
                                name="Banner Web URL"
                                #default="{ errors }"
                                rules="url"
                            >
                                <b-form-input
                                    v-model="data.bannerWebURL"
                                    :state="errors.length > 0 ? false:null"
                                    type="url"
                                    placeholder="Banner Web URL"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                </b-row>
                <b-row>
                    <!-- Draft button -->
                    <b-col md="2">
                        <b-button
                            variant="danger"
                            type="submit"
                            @click.prevent="savedraft"
                        >
                            Save Draft
                        </b-button>
                    </b-col>
                    <!-- Cancel button -->
                    <b-col md="2" offset-md="6">
                        <b-button
                            type="reset"
                            v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                            variant="outline-danger"
                        >Cancel
                        </b-button>
                    </b-col>
                    <b-col md="2">

                        <!-- Save button -->
                        <b-button
                            v-if="!data.id"
                            variant="primary"
                            type="submit"
                            @click.prevent="validationForm"
                        >
                            Submit
                        </b-button>
                        <!-- Update button -->
                        <b-button
                            v-else
                            variant="primary"
                            type="submit"
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
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import {
    BSpinner, BFormInput, BFormGroup, BForm, BRow, BCol, BButton, BFormFile, BFormSelect, BFormTextarea, BFormCheckbox,
} from 'bootstrap-vue'
import {
    required, email, confirmed, url, between, alpha, integer, password, min, digits, alphaDash, length, max,
} from '@validations'

import vSelect from 'vue-select'
import flatPickr from 'vue-flatpickr-component'
import FirebaseDataService from '@/libs/firebase-keyword-db-web'
import Ripple from 'vue-ripple-directive'

export default {
    components: {
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
                jobtype: '',
                employerid: '',
                jobtitle_id: '',
                jobtitle: '',
                noofvancancy: '',
                workmode: 1,
                jobDetails: '',
                agelimit: '',
                salary: '',
                selectionProcess: '',
                feesDetails: '',
                examCenter: '',
                howToApply: '',
                postalAddress: '',
                examDate: '',
                startDate: '',
                endDate: '',
                website1: '',
                website2: '',
                jobSource: 5,
                referredby: '',
                singlequalification: [],
                groupqualification: [],
                singleTextFromGroupQualification: '',
                key_word_db_web: [],
                listViewImageURL: '',
                detailedViewImageURL: '',
                bannerWebURL: '',
                verified: '',

            },
            initial_loading: true,
            datePickerConfig: {
                altFormat: 'd - M - Y',
                altInput: true,
                dateFormat: 'Y-m-d',
            },
            variants: ['light', 'primary', 'secondary', 'danger', 'warning', 'success', 'info', 'dark'],
            select_employer: [{
                id: 2,
                title: 'State Government'
            }, {
                id: 3,
                title: 'Central Government'
            }],
            select_work_mode: [{
                id: 1,
                title: 'FULL-TIME'
            }, {
                id: 2,
                title: 'PART-TIME'
            }],
            options: [],
            jobtitle_options: [],
            select_job_source: [],
            selectReferredby: [],
            selectSingleQualification: [],
            selectGroupQualification: [],
            singleFromGroupQualification: [],
            key_word_db_web: [],
            maxChar500: 500,
            required,
            min,
            max,
            userData: JSON.parse(localStorage.getItem('userData')),
        }
    },
    async created() {
        const callAxios = await this.callAxios('/getgovtjobpostmaster', {}, 'post')
        this.select_job_source = callAxios.data.jobSource
        this.selectReferredby = callAxios.data.Referredby
        this.selectSingleQualification = callAxios.data.SingleQualification
        this.selectGroupQualification = callAxios.data.GroupQualification
        // console.log(this.$route.query.id)
        // console.log(this.$route.query.table)
        if (this.$route.query.table) {
            this.data = this.$route.query
            const callAxios1 = await this.callAxios('/getjobdetails', this.data, 'post')
            // console.log(callAxios1.data[0])
            var return_data = callAxios1.data[0]
            this.options = [{
                id: return_data.employerid,
                name: return_data.companyname
            }]
            this.jobtitle_options = [{
                id: return_data.jobtitle_id,
                name: return_data.jobtitlename
            }]
            return_data.singlequalification = return_data.singlequalification.split(',')
                .filter(element => element)
                .map(Number)
            return_data.groupqualification = return_data.groupqualification.split(',')
                .filter(element => element)
                .map(Number)
            if (return_data.singlequalification.includes(1206)) {
                return_data.groupqualification.push(1206)
                return_data.singlequalification.pop(1206)
            }
            if (!return_data.verified) {
                return_data.verified = null
            }
            return_data.key_word_db_web = return_data.keyword.split(',')
                .filter(element => element)
            console.log(return_data)
            this.data = return_data
            this.$router.replace('/posting/appName/')
        } else {
            // alert("New")
        }
        // console.log(this.getWithExpiry('key_word_db_web'));
        if (this.getWithExpiry('key_word_db_web')) {
            // console.log('local');
            this.key_word_db_web = this.getWithExpiry('key_word_db_web')
        } else {
            // console.log('server');
            this.key_word_db_web = await FirebaseDataService.getAll()
            this.setWithExpiry('key_word_db_web', this.key_word_db_web, 43200) //12 hr
        }
        this.initial_loading = false
        // console.log(callAxios2)
    },
    methods: {
        onemployersearch(search, loading) {
            if (search.length > 2) {
                loading(true)
                this.employersearch(loading, search, this)
            }
        },
        employersearch: _.debounce(async (loading, search, vm) => {
            try {
                const response = await axios.post('/searchGovtEmployer', {
                    search_term: search
                })
                vm.options = response.data
                console.log(response)
            } catch (error) {
                console.error(error)
            } finally {
                loading(false)
            }

            // const callAxios2 = await this.callAxios('/searchGovtEmployer', {search_term: search}, 'get');
            // console.log(callAxios2)
            // if (callAxios2.status === 200) {
            //     vm.options = callAxios2.items
            // } else {
            //     this.sweetAlertmethod('error', "Error", callAxios2.response.data.message, 'btn-primary');
            // }
            loading(false)
        }, 350),
        ontitlesearch(search, loading) {
            if (search.length > 2) {
                loading(true)
                this.titlesearch(loading, search, this)
            }
        },
        titlesearch: _.debounce(async (loading, search, vm) => {
            try {
                const response = await axios.post('/searchJobTitle', {
                    search_term: search
                })
                vm.jobtitle_options = response.data
                console.log(response)
            } catch (error) {
                console.error(error)
            } finally {
                loading(false)
            }
            loading(false)
        }, 350),
        selected_keywords(values) {
            var last_value = values.at(-1)
            if (this.key_word_db_web.includes(last_value)) {
            } else {
                FirebaseDataService.create(last_value)
            }

            console.log()

        },
        validationForm() {
            this.$refs.simpleRules.validate()
                .then(async success => {
                    if (success) {
                        // eslint-disable-next-line
                        // alert('form submitted!')
                        // this.data.draft_id = 0;
                        this.data.table = 'jobs'
                        this.data.inby = this.userData.id
                        console.log(this.data)
                        const config = {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        }
                        var callAxios = await this.callAxiosWithConfig('/savegovtjobs', this.data, 'post', config)
                        if (callAxios.status === 200) {
                            this.sweetAlertmethod('success', 'Success', '', 'btn-primary')
                            this.$router.push('/listing/appName')
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
        settitle(value) {
            console.log(value)
            var aa = this.jobtitle_options.find(x => x.id === value)
            this.data.jobtitle = aa.name
        },
        setsingleFromGroupQualification(value) {
            console.log(value)
            const selectGroupQualification1 = this.selectGroupQualification
            const singleFromGroupQualification = []
            value.forEach(function (value, key) {
                // console.log(key+"--"+value);
                var aa = selectGroupQualification1.find(x => x.id === value)
                console.log(aa.groupcourse)
                singleFromGroupQualification.push(aa.groupcourse)
            })
            this.data.singleTextFromGroupQualification = singleFromGroupQualification.join()
            // console.log(this.data.singleTextFromGroupQualification);
        },
        vselect_handlers: (map, vm) => ({
            ...map,
            //enter
            13: (e) => {
                loading(true)
                e.preventDefault()
                if (vm.search.length > 2) {
                    vm.search = `${vm.search}@gmail.com`
                    return this.typeAheadSelect()
                    // vm.onSearchBlur()
                }
            },
        }),
        fetchOptions(search, loading) {
            console.log(search)
        },
    },

}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>
