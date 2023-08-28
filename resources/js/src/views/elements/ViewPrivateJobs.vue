<template>
    <b-modal
        id="modal-footer-lg"
        centered
        hide-backdrop
        ok-only
        ok-title="Close"
        size="xl"
        title="Job Details"

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
            <h1>Job ID - #{{ id }}</h1>
            <b-row>
                <b-col
                    cols="12"
                    lg="8"
                    md="7"
                    xl="9"
                >
                    <b-card>


                        <b-row>

                            <!-- User Info: Left col -->
                            <b-col
                                class="d-flex justify-content-between flex-column"
                                cols="21"
                                xl="6"
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
                                            <h4 class="mb-0 text-capitalize">
                                                {{ data.cname }}
                                            </h4>
                                            <span v-if="data.jobtype===1"
                                                  class="card-text text-capitalize"
                                            >Private Employer</span>
                                            <span v-else-if="data.jobtype===2" class="card-text text-capitalize">central government</span>
                                            <span v-else class="card-text text-capitalize">State government</span>
                                        </div>
                                        <div class="d-flex flex-wrap">
                                            <b-button
                                                :href="'/posting/private/?table=jobs&id='+data.id"
                                                variant="primary"
                                            >
                                                Edit
                                            </b-button>
                                            <b-button
                                                class="ml-1"
                                                variant="outline-danger"
                                            >
                                                Delete
                                            </b-button>
                                        </div>
                                    </div>
                                </div>

                                <!-- User Stats -->

                            </b-col>

                            <!-- Right Col: Table -->
                            <b-col
                                cols="12"
                                xl="6"
                            >
                                <table class="mt-2 mt-xl-0 w-100">

                                    <tr>
                                        <th class="pb-50">
                                            <feather-icon
                                                class="mr-75"
                                                icon="CheckIcon"
                                            />
                                            <span class="font-weight-bold">Job Status</span>
                                        </th>
                                        <td class="pb-50 text-capitalize">
                                        <span v-if="data.jobstatus==='Live'" class="text-success">{{
                                                data.jobstatus
                                            }}</span>
                                            <span v-else class="text-danger">{{ data.jobstatus }}</span>


                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="pb-50">
                                            <feather-icon
                                                class="mr-75"
                                                icon="StarIcon"
                                            />
                                            <span class="font-weight-bold ">Job Title</span>
                                        </th>
                                        <td class="pb-50 text-capitalize text-dark">
                                            {{ data.jobtitle }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="pb-50">
                                            <feather-icon
                                                class="mr-75"
                                                icon="FlagIcon"
                                            />
                                            <span class="font-weight-bold">Job Category</span>
                                        </th>
                                        <td class="pb-50 text-dark">
                                            {{ data.jobcat }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <feather-icon
                                                class="mr-75"
                                                icon="PhoneIcon"
                                            />
                                            <span class="font-weight-bold">Contact</span>
                                        </th>
                                        <td>


                                <span v-if="data.phone_new" class="text-success">
                                    {{ data.phone_new }}
                                </span>


                                            <span v-else class="text-danger text-capitalize"> not mention</span>
                                        </td>


                                    </tr>

                                    <tr>
                                        <th>
                                            <feather-icon
                                                class="mr-75"
                                                icon="PhoneIcon"
                                            />
                                            <span class="font-weight-bold text-capitalize">whatsapp number</span>
                                        </th>
                                        <td>
                                        <span v-if="data.whatsapp_number !=''"
                                              class="text-success text-capitalize"
                                        > {{ data.whatsapp_number }}</span>
                                            <span v-else class="text-danger text-capitalize"> not mention</span>
                                        </td>


                                    </tr>
                                    <tr>
                                        <th>
                                            <feather-icon
                                                class="mr-75"
                                                icon="PocketIcon"
                                            />
                                            <span class="font-weight-bold text-capitalize">POSTED BY</span>
                                        </th>
                                        <td>
                                        <span v-if="data.inbyName !=''"
                                              class="text-success text-capitalize"
                                        > {{ data.inbyName }}</span>
                                            <span v-else class="text-danger text-capitalize"> not mention</span>
                                        </td>


                                    </tr>
                                    <tr>
                                        <th>
                                            <feather-icon
                                                class="mr-75"
                                                icon="EditIcon"
                                            />
                                            <span class="font-weight-bold text-capitalize">UPDATED BY</span>
                                        </th>
                                        <td>
                                        <span v-if="data.inbyName !=''"
                                              class="text-success text-capitalize"
                                        > {{ data.upbyName }}</span>
                                            <span v-else class="text-danger text-capitalize"> not mention</span>
                                        </td>


                                    </tr>

                                </table>
                            </b-col>
                        </b-row>

                    </b-card>
                </b-col>
                <b-col
                    cols="12"
                    lg="4"
                    md="5"
                    xl="3"
                >
                    <b-card
                        class="border-primary"
                        no-body
                    >
                        <b-card-header class="d-flex justify-content-between align-items-center pt-75 pb-25">
                            <h4 class="mb-0 text-primary">
                                Current Plan
                            </h4>
                            <b-badge v-if="data.txn_amt > 0" variant="light-success">
                                Paid - ( {{ data.txn_amt }} )
                            </b-badge>

                            <b-badge v-else variant="light-warning">
                                Free
                            </b-badge>

                            <h6 class="text-dark w-100 text-capitalize">plan name : <span
                                class="text-warning"
                            >  {{ data.plan_name }}  <span
                                v-if="data.post_count!='' && data.post_count > 0 "
                                class="text-danger"
                            >( {{ data.post_count }} )</span>  </span></h6>
                            <h6 class="text-dark w-100 text-capitalize">date of job post : {{ data.postedDate }} </h6>
                            <h6 class="text-dark w-100 text-capitalize">UPDATED DATE : {{ data.job_edit_date }} </h6>
                            <h6 class="text-dark w-100 text-capitalize">plan start date : {{ data.starting_date }} </h6>
                            <h6 class="text-dark w-100 text-capitalize">plan end date : {{ data.expired_date }} </h6>
                            <h6 class="text-dark w-100 text-capitalize">job start date : {{ data.starting }} </h6>
                            <h6 class="text-dark w-100 text-capitalize">job end date : {{ data.ending }} </h6>
                            <h6 class="text-dark w-100 text-capitalize">total validity days : {{ data.dateDiff }} </h6>
                            <h6 class="text-dark w-100 text-capitalize">remaining days : {{
                                data.days_diff_remain
                                }} </h6>
                        </b-card-header>

                        <b-card-body>
                            <b-button
                                v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                                block
                                variant="primary"
                            >
                                Upgrade Plan
                            </b-button>
                        </b-card-body>
                    </b-card>
                </b-col>
            </b-row>
            <b-row>
                <b-col
                    cols="12"
                    lg="12"
                    md="12"
                    xl="12"
                >
                    <b-card>


                        <b-row align-h="center">

                            <!-- User Info: Left col -->
                            <b-col
                                class="d-flex justify-content-between flex-column"
                                cols="21"

                                xl="12"
                            >
                                <!-- User Avatar & Action Buttons -->


                                <!-- User Stats -->
                                <div class="d-flex align-items-center mt-2">
                                    <div v-if="data.from_salary!=''" class="d-flex align-items-center mr-2">
                                        <b-avatar
                                            rounded
                                            variant="light-primary"
                                        >
                                            <feather-icon
                                                icon="DollarSignIcon"
                                                size="18"
                                            />
                                        </b-avatar>
                                        <div class="ml-1">
                                            <h5 class="mb-0">
                                                {{ data.from_salary }}- {{ data.to_salary }}
                                            </h5>
                                            <small>Monthly Salary</small>
                                        </div>
                                    </div>

                                    <div v-if="data.from_exp!=0" class="d-flex align-items-center mr-2">
                                        <b-avatar
                                            rounded
                                            variant="light-success"
                                        >
                                            <feather-icon
                                                icon="TrendingUpIcon"
                                                size="18"
                                            />
                                        </b-avatar>
                                        <div class="ml-1">
                                            <h5 class="mb-0">
                                                {{ data.from_exp }} - {{ data.to_exp }}
                                            </h5>
                                            <small>Experience</small>
                                        </div>
                                    </div>
                                    <div v-if="data.noofvancancy!=0" class="d-flex align-items-center mr-2">
                                        <b-avatar
                                            rounded
                                            variant="light-success"
                                        >
                                            <feather-icon
                                                icon="UsersIcon"
                                                size="18"
                                            />
                                        </b-avatar>
                                        <div class="ml-1">
                                            <h5 class="mb-0">
                                                {{ data.noofvancancy }}
                                            </h5>
                                            <small class="text-capitalize">vacancy</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mr-2">
                                        <b-avatar
                                            rounded
                                            variant="light-primary"
                                        >
                                            <feather-icon
                                                icon="AirplayIcon"
                                                size="18"
                                            />
                                        </b-avatar>
                                        <div class="ml-1">
                                            <h5 class="mb-0">
                                                <span v-if="data.workmode==1"> FULL-TIME</span>
                                                <span v-else> PART-TIME</span>
                                            </h5>
                                            <small class="text-capitalize">work mode</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mr-2">
                                        <b-avatar
                                            rounded
                                            variant="light-warning"
                                        >
                                            <feather-icon
                                                icon="EyeIcon"
                                                size="18"
                                            />
                                        </b-avatar>
                                        <div class="ml-1">
                                            <h5 class="mb-0">
                                                {{ data.viewcount }}
                                            </h5>
                                            <small class="text-capitalize">view count</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mr-2">
                                        <b-avatar
                                            rounded
                                            variant="light-success"
                                        >
                                            <feather-icon
                                                icon="PhoneCallIcon"
                                                size="18"
                                            />
                                        </b-avatar>
                                        <div class="ml-1">
                                            <h5 class="mb-0">
                                                {{ data.call_count }}
                                            </h5>
                                            <small class="text-capitalize">call count</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mr-2">
                                        <b-avatar
                                            rounded
                                            variant="light-warning"
                                        >
                                            <feather-icon
                                                icon="PhoneCallIcon"
                                                size="18"
                                            />
                                        </b-avatar>
                                        <div class="ml-1">
                                            <h5 class="mb-0">
                                                {{ data.call_confirm_count }}
                                            </h5>
                                            <small class="text-capitalize">confirm call count</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mr-2">
                                        <b-avatar
                                            rounded
                                            variant="light-warning"
                                        >
                                            <feather-icon
                                                icon="UserIcon"
                                                size="18"
                                            />
                                        </b-avatar>
                                        <div class="ml-1">
                                            <h5 v-if="data.gender===0" class="mb-0">
                                                Male
                                            </h5>
                                            <h5 v-else-if="data.gender===1" class="mb-0">
                                                Female
                                            </h5>
                                            <h5 v-else class="mb-0">
                                                All
                                            </h5>
                                            <small class="text-capitalize">gender</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mr-2">
                                        <b-avatar
                                            rounded
                                            variant="light-danger"
                                        >
                                            <feather-icon
                                                icon="UsersIcon"
                                                size="18"
                                            />
                                        </b-avatar>
                                        <div class="ml-1">
                                            <h5 class="mb-0">
                                                {{ data.marital_status }}
                                            </h5>
                                            <small class="text-capitalize">marital status</small>
                                        </div>
                                    </div>
                                </div>
                            </b-col>

                            <!-- Right Col: Table -->

                        </b-row>

                    </b-card>
                </b-col>
            </b-row>
            <b-list-group>
                <b-list-group-item class="text-capitalize"><b>job location :</b> {{ data.jobLocation_string }}
                </b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>candidate can apply from :</b> {{
                    data.candidate_location
                    }}
                </b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>candidate can apply location :</b> {{
                    data.candidates_apply_location
                    }}
                </b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>district :</b> {{ data.dist }}</b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>address :</b> {{ data.postaladdr }}</b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>email :</b> {{ data.email }}</b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>email new :</b> {{ data.email_new }}</b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>Group qualification :</b> {{ data.qualigroup }}
                </b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>Single qualification :</b> {{ data.qualisingle }}
                </b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>job details :</b> {{ data.description }}
                </b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>skills :</b> {{ data.skills_string }}</b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>contact detail :</b> {{ data.contact_detail }}
                </b-list-group-item>
                <b-list-group-item v-if="data.how_to_apply!=''" class="text-capitalize"><b>how to apply :</b>
                    {{ data.how_to_apply }}
                </b-list-group-item>
                <b-list-group-item v-if="data.gover_addresspost!=''" class="text-capitalize"><b>POSTAL ADDRESS :</b>
                    {{ data.gover_addresspost }}
                </b-list-group-item>
                <b-list-group-item v-if="data.selection!=''" class="text-capitalize"><b>selection process :</b>
                    {{ data.selection }}
                </b-list-group-item>
                <b-list-group-item v-if="data.website2!='' && data.website!=''" class="text-capitalize"><b>website :</b>
                    {{ data.website }} - {{ data.website2 }}
                </b-list-group-item>
                <b-list-group-item v-else-if="data.website!=''" class="text-capitalize"><b>website :</b> {{
                    data.website
                    }}
                </b-list-group-item>
                <b-list-group-item v-else-if="data.website2!=''" class="text-capitalize"><b>website :</b> {{
                    data.website2
                    }}
                </b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>contact person detail :</b> {{
                    data.contact_person_detail
                    }}
                </b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>Referred By :</b> {{ data.referredby }}
                </b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>keyword :</b> {{ data.keyword }}</b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>salary remarks :</b> {{ data.salary_remarks }}
                </b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>map url :</b> {{ data.map_url }}</b-list-group-item>
                <!--                <b-list-group-item class="text-capitalize"><b>job source :</b> {{ data.jsource }}</b-list-group-item>-->
                <b-list-group-item class="text-capitalize"><b>call from_time :</b> {{ data.call_from_time }}
                </b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>call to time :</b> {{ data.call_to_time }}
                </b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>tags :</b> {{ data.tags }}</b-list-group-item>
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
                'emp_id': '',
                'groupqualification': '',
                'call_from_time': '',
                'call_to_time': '',
                'how_to_apply': '',
                'gover_addresspost': '',
                'map_url': '',
                'referredby': '',
                'singlequalification': '',
                'candidate_location': '',
                'candidates_apply_location': '',
                'jobLocation_string': '',
                'skills_string': '',
                'salary_remarks': '',
                'dist': '',
                'tags': '',
                'whatsapp_number': '',
                'contact_person_detail': '',
                'contact_detail': '',
                'quali': '',
                'address': '',
                'email_new': '',
                'email': '',
                'gender': '',
                'post_count': '',
                'txn_amt': '',
                'plan_name': '',
                'days_diff_remain': '',
                'description': '',
                'website': '',
                'website2': '',
                'keyword': '',
                'feesDetails': '',
                'agelimit': '',
                'call_count': '',
                'noofvancancy': '',
                'dateDiff': '',
                'phone_new': '',
                'paid_post': '',
                'phone': '',
                'upbyName': '',
                'job_edit_date': '',
                'jobtitle': '',
                'ending': '',
                'examdate': '',
                'fees': '',
                'from_exp': '',
                'from_salary': '',
                'govtSalary': '',
                'howToApply': '',
                'inbyName': '',
                'job_id': '',
                'jobcat': '',
                'jobstatus': '',
                'jobtype': '',
                'jsource': '',
                'marital_status': '',
                'name': '',
                'cname': '',
                'postaladdr': '',
                'postedDate': '',
                'starting_date': '',
                'expired_date': '',
                'postedIp': '',
                'selection': '',
                'starting': '',
                'status': '',
                'to_exp': '',
                'to_salary': '',
                'viewcount': '',
                'workmode': '',
                'image': '',
            },
            listQuali: [],
            qualisingle: [],
            qualigroup: [],
            keyWord: [],
            areasArray: [],
            courseArray: [],
            jobscatArray: [],
            jobsourceArray: [],
            distArray: [],
            skilsArray: [],
            userArray: [],
            initial_loading: true,
            variants: ['light', 'primary', 'secondary', 'danger', 'warning', 'success', 'info', 'dark'],
        }

    },
    watch: {

        id: function (newVal) {
            this.data.name = 'Loading... Please Wait..'
            this.model_check = newVal
            console.log(this.table)
            this.getDataGovent(this.model_check, 'private', this.table, this.radiesArray)
        }
    },
    methods: {

        async getDataGovent(value, type, table = 'jobs', rediesArray) {
            console.log('nithra')
            console.log(this.table)
            this.areasArray = this.radiesArray.areasArray
            this.courseArray = this.radiesArray.courseArray
            this.jobscatArray = this.radiesArray.jobscatArray
            this.jobsourceArray = this.radiesArray.jobsourceArray
            this.skilsArray = this.radiesArray.skilsArray
            this.userArray = this.radiesArray.userArray
            this.distArray = this.radiesArray.distArray
            this.initial_loading = true
            const callAxios = await this.callAxios('/getSinglejobs', {
                id: value,
                jobtype: type,
                table: table,
            }, 'post')
            console.log(callAxios)
            var return_data = callAxios.data[0]
            this.initial_loading = false
            if (callAxios.status === 200) {
                const logo_image = ''
                this.data = return_data
                this.data.postedDate = moment(callAxios.data[0].postedDate)
                    .format('DD/MM/YYYY, h:mm:ss a')
                this.data.starting_date = moment(callAxios.data[0].starting_date)
                    .format('DD/MM/YYYY')
                this.data.expired_date = moment(callAxios.data[0].expired_date)
                    .format('DD/MM/YYYY')
                this.data.starting = moment(callAxios.data[0].starting)
                    .format('DD/MM/YYYY')
                this.data.ending = moment(callAxios.data[0].ending)
                    .format('DD/MM/YYYY')
                this.data.job_edit_date = moment(callAxios.data[0].job_edit_date)
                    .format('DD/MM/YYYY, h:mm:ss a')
                this.data.examdate = moment(callAxios.data[0].examdate)
                    .format('DD/MM/YYYY')
                // this.data.call_to_time = moment(callAxios.data[0].call_to_time).format("hh:mm:ss a");
                // this.data.call_from_time = moment(callAxios.data[0].call_from_time).format('hh:mm');
                const currentDate = moment(new Date(callAxios.data[0].start_check))
                const currentDate_rem = moment(new Date())
                const currentDate_rem1 = currentDate_rem.subtract(1, 'days')
                const returnDate = moment(new Date(callAxios.data[0].end_check))
                var days_diff_remain = returnDate.diff(currentDate_rem1, 'days')
                var days_diff = returnDate.diff(currentDate, 'days')
                // console.log(this.data.examdate)
                this.data.dateDiff = days_diff
                this.data.days_diff_remain = days_diff_remain
                this.logo_image = callAxios.data[0].logo_image

                const keyword = callAxios.data[0].keyword
                const jobtype = callAxios.data[0].jobtype
                const marital_status = callAxios.data[0].marital_status
                if (marital_status == 1) {
                    this.data.marital_status = 'Married'
                } else if (marital_status == 0) {
                    this.data.marital_status = 'Unmarried'
                } else {
                    this.data.marital_status = 'All'

                }

                this.keyWord = keyword && keyword.split(',')
                const district = callAxios.data[0].district
                if (district) {
                    console.log('here12')
                    console.log(district)
                    this.data.dist = this.distArray.find(food1 => food1.id === district).dist_tamil
                }

                if (this.table != 'draft_jobs') {
                    const inbyid = callAxios.data[0].inby
                    if (inbyid) {
                        console.log('here22')
                        // console.log(inbyid)
                        this.data.inbyName = this.userArray.find(food1 => food1.id === inbyid).title
                    }

                    const upbyid = callAxios.data[0].upby
                    console.log(upbyid)
                    if (upbyid > 0 && upbyid != '') {
                        console.log('here1')
                        this.data.upbyName = this.userArray.find(food2 => food2.id === upbyid).title
                    }
                }

                if (jobtype == 1) {
                    if (this.table != 'draft_jobs') {
                        const referredbyid = callAxios.data[0].referredby
                        if (referredbyid != '' && referredbyid > 0) {
                            this.data.referredby = this.userArray.find(food3 => food3.id === referredbyid).title
                        }
                    }

                    // singlequalification
                    const singlequalification = callAxios.data[0].singlequalification.split(',')
                    // console.log(this.courseArray.find((item) => item.id == 1204).course)
                    this.singlequalificationcheck = []
                    singlequalification.forEach(checka => {
                        if (checka) {
                            this.singlequalificationcheck.push(this.courseArray.find((item) => item.id == checka).course)
                        }
                    })
                    this.data.qualisingle = this.singlequalificationcheck.join()

                    // groupqualification
                    const groupqualification = callAxios.data[0].groupqualification.split(',')
                    // console.log(groupqualification)
                    this.groupqualificationcheck = []
                    groupqualification.forEach(checka => {
                        if (checka) {
                            this.groupqualificationcheck.push(this.courseArray.find((item) => item.id == checka).course)
                        }
                    })
                    // console.log(this.groupqualificationcheck)
                    this.data.qualigroup = this.groupqualificationcheck.join()

                    // skills
                    const skills = callAxios.data[0].skills.split(',')
                    this.skillscheck = []
                    skills.forEach(checka => {
                        if (checka) {
                            this.skillscheck.push(this.skilsArray.find((item) => item.id == checka).skills)
                        }
                    })
                    this.data.skills_string = this.skillscheck.join()

                    // location
                    if (callAxios.data[0].location) {
                        const location = callAxios.data[0].location.split(',')
                        // console.log(location)
                        this.locationcheck = []
                        location.forEach(checka => {
                            console.log(this.areasArray.find((item) => item.id == checka).district)
                            if (checka) {
                                this.locationcheck.push(this.areasArray.find((item) => item.id == checka).district)
                            }
                        })
                        // var collectArray=this.locationcheck.join()
                        var collectArray = this.locationcheck.filter((item, index) => this.locationcheck.indexOf(item) === index)
// console.log(collectArray)
                        this.data.candidate_location = collectArray.join()
                    }

                    // joblocation
                    if (callAxios.data[0].joblocation) {
                        const joblocation = callAxios.data[0].joblocation.split(',')
                        this.joblocationcheck = []
                        joblocation.forEach(checka => {
                            if (checka) {
                                this.joblocationcheck.push(this.areasArray.find((item) => item.id == checka).area_name)
                            }
                        })
                        this.data.jobLocation_string = this.joblocationcheck.join()
                    }

                    // candidates_apply_location
                    if (callAxios.data[0].candidates_apply_location) {
                        const candidates_apply_location = callAxios.data[0].candidates_apply_location.split(',')
                        this.candidates_apply_locationcheck = []
                        candidates_apply_location.forEach(checka => {
                            if (checka) {
                                this.candidates_apply_locationcheck.push(this.areasArray.find((item) => item.id == checka).area_name)
                            }
                        })
                        this.data.candidates_apply_location = this.candidates_apply_locationcheck.join()
                    }

                }

                // mul_category
                const mul_category = callAxios.data[0].mul_category.split(',')
                this.mul_categorycheck = []
                mul_category.forEach(checka => {
                    if (checka) {
                        this.mul_categorycheck.push(this.jobscatArray.find((item) => item.id == checka).job_cat)
                    }
                })
                this.data.jobcat = this.mul_categorycheck.join()

                // jsource
                // const jsource = callAxios.data[0].jobsource
                // console.log(jsource)
                // if(jsource > 0){
                // this.data.jsource=this.jobsourceArray.find((item) => item.id == jsource).title
                //                     }

                if (logo_image != '') {
                    this.data.image = logo_image
                } else {
                    this.data.image = 'https://nithrajobs.com/assets/dist/img/private_job.webp'
                }

            } else {
                // console.log(callAxios.response.data.message);
                // this.toast("Error", callAxios.response.data.message, 'danger');
                this.sweetAlertmethod('error', 'Error', callAxios.response.data.message, 'btn-primary')
            }
        },
    }

}
</script>
