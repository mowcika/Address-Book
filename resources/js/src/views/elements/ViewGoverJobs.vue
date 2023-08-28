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
                                    :text="data.name"
                                    rounded
                                    size="104px"
                                    variant="light-primary"
                                />
                                <div class="d-flex flex-column ml-1">
                                    <div class="mb-1">
                                        <h4 class="mb-0 text-capitalize">
                                            {{ data.name }}
                                        </h4>
                                        <span v-if="data.job_id===2"
                                              class="card-text text-capitalize"
                                        >state government</span>
                                        <span v-else class="card-text text-capitalize">central government</span>
                                    </div>
                                    <div class="d-flex flex-wrap">
                                        <b-button

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
                                        <span v-else class="text-success">{{ data.jobstatus }}</span>


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
                                        <span class="font-weight-bold">Contact1</span>
                                    </th>
                                    <td>


                                <span v-if="data.phone_new > 0" class="text-success">
                                    {{ data.phone_new }}
                                </span>

                                        <span v-else-if="data.phone > 0" class="text-success">
                                    {{ data.phone }}
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
                                        <span class="font-weight-bold">Contact2</span>
                                    </th>
                                    <td>


                                <span v-if="data.phone_new > 0" class="text-success">
                                    {{ data.phone_new }}
                                </span>

                                        <span v-else-if="data.phone > 0" class="text-success">
                                    {{ data.phone }}
                                </span>

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
                        <h5 class="mb-0">
                            Current Plan
                        </h5>
                        <b-badge v-if="data.paid_post==0" variant="light-success">
                            Free
                        </b-badge>
                        <small class="text-muted w-100 text-capitalize">date of post : {{ data.postedDate }}</small>
                    </b-card-header>

                    <b-card-body>
                        <ul class="list-unstyled my-1">
                            <li>
                                <span class="align-middle text-capitalize">job start date : {{ data.starting }}</span>
                            </li>
                            <li>
                                <span class="align-middle text-capitalize">job end date : {{ data.ending }}</span>
                            </li>
                            <li class="my-25">
                                <span class="align-middle">Total Validity Days : {{ data.dateDiff }}</span>
                            </li>
                            <li class="my-25">
                                <span v-if="data.days_diff_remain > 0" class="align-middle text-capitalize">remaining days : {{
                                        data.days_diff_remain
                                    }}</span>
                                <span v-else class="align-middle text-capitalize">remaining days : 0</span>
                            </li>
                            <li>
                                <span v-if="data.examdate!='Invalid date'" class="align-middle text-capitalize">exam date : {{
                                        data.examdate
                                    }}</span>
                                <span v-else
                                      class="align-middle text-danger text-capitalize"
                                >exam date : not mention</span>
                            </li>
                        </ul>
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
                            offset-md="3"
                            xl="9"
                        >
                            <!-- User Avatar & Action Buttons -->


                            <!-- User Stats -->
                            <div class="d-flex align-items-center mt-2">
                                <div v-if="data.govtSalary!=''" class="d-flex align-items-center mr-2">
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
                                            {{ data.govtSalary }}
                                        </h5>
                                        <small>Monthly Salary</small>
                                    </div>
                                </div>

                                <div v-if="data.from_exp!=0" class="d-flex align-items-center">
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
                                            <sapn v-if="data.workmode==1"> FULL-TIME</sapn>
                                            <sapn v-else> PART-TIME</sapn>
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
                                        <small class="text-capitalize">views</small>
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
                                        <small class="text-capitalize">calls</small>
                                    </div>
                                </div>
                            </div>
                        </b-col>

                        <!-- Right Col: Table -->

                    </b-row>

                </b-card>
            </b-col>
        </b-row>
        <section id="card-navigation">
            <b-row>

                <!-- pill tabs inside card -->
                <b-col md="12">
                    <b-card class="text-center">
                        <b-tabs
                            nav-class="mb-3"
                            pills
                        >
                            <b-tab v-if="data.howToApply!=''" title="HOW TO APPLY"

                            >
                                <b-card>
                                    <b-card-title class="text-dark">{{ data.howToApply }}</b-card-title>
                                </b-card>


                            </b-tab>
                            <b-tab v-if="data.feesDetails!=''" title="FEES DETAILS"

                            >
                                <b-card-title>{{ data.feesDetails }}</b-card-title>

                            </b-tab>
                            <b-tab v-if="data.agelimit!=''" title="AGE LIMIT"

                            >
                                <b-card-title class="text-dark">{{ data.agelimit }}</b-card-title>

                            </b-tab>
                            <b-tab v-if="data.description!=''" title="POSTING DETAILS">

                                <b-card-text class="text-dark text-justify">
                                    {{ data.description }}
                                </b-card-text>

                            </b-tab>
                            <b-tab v-if="data.selection!=''" title="SELECTION PROCESS">
                                <b-card-title class="text-dark">{{ data.selection }}</b-card-title>

                            </b-tab>
                            <b-tab v-if="data.keyword!=''" title="KEYWORD">
                                <b-badge v-for="(item, index) in qualisingle"
                                         :key="item.id"
                                         class="badge-glow mr-1"
                                         pill
                                         variant="primary"
                                >
                                    {{ item }} <br>
                                </b-badge>
                            </b-tab>
                            <b-tab v-if="data.website!=''" title="WEBSITE">
                                <b-card-title class="text-danger">
                                    <h3><a :href="data.website" class="text-dark">{{ data.website }}</a></h3>
                                    <h3><a :href="data.website2" class="text-dark">{{ data.website2 }}</a></h3>

                                </b-card-title>


                            </b-tab>
                            <b-tab v-if="data.quali!=''" title="QUALIFICATION">
                                <b-card-title class="text-danger">
                                    <b-list-group class="text-left w-50">

                                        <b-list-group-item v-for="(item, index) in listQuali"
                                                           :key="item.id"
                                                           :data-index="index" variant="primary"
                                        >
                                            {{ index + 1 }}).{{ item }}
                                        </b-list-group-item>

                                    </b-list-group>


                                </b-card-title>


                            </b-tab>
                            <b-tab v-if="data.groupqualification!=''" title="GROUP QUALIFICATION">
                                <b-card-title class="text-danger">
                                    <b-list-group class="text-left w-50">

                                        <b-list-group-item v-for="(item, index) in qualigroup"
                                                           :key="item.id"
                                                           :data-index="index" variant="primary"
                                        >
                                            {{ index + 1 }}).{{ item }}
                                        </b-list-group-item>

                                    </b-list-group>


                                </b-card-title>


                            </b-tab>
                            <b-tab v-if="data.singlequalification!=''" title="SINGLE QUALIFICATION">
                                <b-card-title class="text-danger">
                                    <b-list-group class="text-left w-50">

                                        <b-list-group-item v-for="(item, index) in qualisingle"
                                                           :key="item.id"
                                                           :data-index="index" variant="primary"
                                        >
                                            {{ index + 1 }}).{{ item }}
                                        </b-list-group-item>

                                    </b-list-group>


                                </b-card-title>


                            </b-tab>

                        </b-tabs>
                    </b-card>
                </b-col>


            </b-row>
        </section>
    </b-modal>
</template>

<script>
import {
    BAvatar,
    BCard,
    BButton,
    BModal,
    VBModal,
    BCardText,
    BRow,
    BCol,
    BCardHeader,
    BCardBody,
    BBadge,
    BTab,
    BTabs,
    BListGroup,
    BListGroupItem,
} from 'bootstrap-vue'
import BCardCode from '@core/components/b-card-code'
import Ripple from 'vue-ripple-directive'
import useUsersList from '@/views/apps/user/users-list/useUsersList'
import { avatarText } from '@core/utils/filter'
import VueMoment from 'vue-moment'
import moment from 'moment-timezone'

export default {
    props: ['id'],
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
                'singlequalification': '',
                'quali': '',
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
                'postaladdr': '',
                'postedDate': '',
                'postedIp': '',
                'selection': '',
                'starting': '',
                'status': '',
                'to_exp': '',
                'to_salary': '',
                'upbyName': '',
                'viewcount': '',
                'workmode': '',
                'image': '',
            },
            listQuali: [],
            qualisingle: [],
            qualigroup: [],
            keyWord: [],

        }

    },
    watch: {

        id: function (newVal) {
            this.model_check = newVal
            this.getDataGovent(this.model_check, 'government')
        }
    },
    methods: {

        async getDataGovent(value, type) {
// alert('here');
            const callAxios = await this.callAxios('/getSinglejobs', {
                id: value,
                jobtype: type
            }, 'post')
            console.log(callAxios)
            var return_data = callAxios.data[0]
            if (callAxios.status === 200) {
                this.data = return_data
                this.data.postedDate = moment(callAxios.data[0].postedDate)
                    .format('DD/MM/YYYY')
                this.data.starting = moment(callAxios.data[0].starting)
                    .format('DD/MM/YYYY')
                this.data.ending = moment(callAxios.data[0].ending)
                    .format('DD/MM/YYYY')
                this.data.examdate = moment(callAxios.data[0].examdate)
                    .format('DD/MM/YYYY')
                const currentDate = moment(new Date(callAxios.data[0].start_check))
                const currentDate_rem = moment(new Date())
                const currentDate_rem1 = currentDate_rem.subtract(1, 'days')
                const returnDate = moment(new Date(callAxios.data[0].end_check))
                var days_diff_remain = returnDate.diff(currentDate_rem1, 'days')
                var days_diff = returnDate.diff(currentDate, 'days')
                console.log(this.data.examdate)
                this.data.dateDiff = days_diff
                this.data.days_diff_remain = days_diff_remain
                const logo_image = callAxios.data[0].logo_image
                const quali = callAxios.data[0].quali
                const keyword = callAxios.data[0].keyword
                const qualisingle = callAxios.data[0].qualisingle
                const qualigroup = callAxios.data[0].qualigroup
                const jobtype = callAxios.data[0].jobtype
                this.listQuali = quali.split(',')
                this.qualisingle = qualisingle.split(',')
                this.qualigroup = qualigroup.split(',')
                this.keyWord = keyword.split(',')

                if (logo_image != '') {
                    this.data.image = logo_image
                } else {
                    if (jobtype === 2) {
                        this.data.image = 'https://d2hy6ree306xec.cloudfront.net/APP/icons/state.webp'
                    } else {
                        this.data.image = 'https://d2hy6ree306xec.cloudfront.net/APP/icons/central.webp'
                    }
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
