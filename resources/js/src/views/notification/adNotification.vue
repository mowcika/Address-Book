<template>
    <b-card-code title="Add Notification">
        <b-overlay
            :show="overlayShow"
            rounded="sm"
        >
            <div v-show="!initial_loading" class="text-center m-5">
                <b-spinner
                    v-for="variant in variants"
                    :key="variant"
                    :variant="variant"
                    class="m-2"
                    style="width: 3rem; height: 3rem;"
                    type="grow"
                />
            </div>

            <b-form>
                <b-row>
                    <b-col cols="12">
                        <b-form-group>
                            <label>Message Title</label>
                            <b-form-input
                                id="msg_title"
                                v-model="formDetails_emp.msg_title"
                                placeholder="Enter Message Title"
                                type="text"
                            />
                        </b-form-group>
                    </b-col>

                    <b-col cols="12">
                        <h4 class="text-danger pt-2 pb-2">EMPLOYEE FILTER :</h4>
                    </b-col>

                    <b-col cols="3">
                        <b-form-group>
                            <label class="required">Single Qualification</label>

                            <v-select
                                v-model="formDetails_emp.sQualification"
                                :options="squalifyDrop"
                                :reduce="item=>item.id"
                                label="course"
                                multiple
                                placeholder="Select Single Qualification"
                            />

                        </b-form-group>
                    </b-col>

                    <b-col cols="3">
                        <b-form-group>
                            <label class="required">Group Qualification</label>

                            <v-select
                                v-model="formDetails_emp.gQualification"
                                :options="gqualifyDrop"
                                :reduce="item=>item.id"
                                label="course"
                                multiple
                                placeholder="Select Group Qualification"
                            />

                        </b-form-group>
                    </b-col>

                    <b-col cols="3">
                        <b-form-group>
                            <label class="required">Skills</label>

                            <v-select
                                v-model="formDetails_emp.jobskills"
                                :options="skillsDrop"
                                :reduce="item=>item.id"
                                label="skills"
                                multiple
                                placeholder="Select Job Skills"
                            />

                        </b-form-group>
                    </b-col>

                    <b-col cols="3">
                        <b-form-group>
                            <label class="required">Job Location</label>

                            <v-select
                                v-model="formDetails_emp.joblocation"
                                :options="distDrop"
                                :reduce="item=>item.dist_id"
                                label="dist"
                                multiple
                                placeholder="Select District"
                            />

                        </b-form-group>
                    </b-col>


                    <b-col cols="3">
                        <b-form-group>
                            <label class="required">Select Gender</label>

                            <v-select
                                v-model="formDetails_emp.gender"
                                :options="genderDrop"
                                :reduce="item=>item.id"
                                label="name"
                                placeholder="Select Gender"
                            />

                        </b-form-group>
                    </b-col>

                    <b-col cols="3">
                        <b-form-group>
                            <label class="required">Job Title</label>

                            <v-select
                                v-model="formDetails_emp.job_title"
                                :options="getjobtitleArray"
                                :reduce="item=>item.id"
                                label="jobtitles"
                                multiple
                                placeholder="Select Job Title"
                            />

                        </b-form-group>
                    </b-col>

                    <b-col cols="3">
                        <b-form-group>
                            <label class="required">Marital Status</label>

                            <v-select
                                v-model="formDetails_emp.marital"
                                :options="maritalDrop"
                                :reduce="item=>item.id"
                                label="name"
                                placeholder="Select Marital Status"
                            />

                        </b-form-group>
                    </b-col>

                    <b-col cols="3">
                        <b-form-group>
                            <label class="required">Work Status</label>

                            <v-select
                                v-model="formDetails_emp.work_status"
                                :options="wrokstatusDrop"
                                :reduce="item=>item.id"
                                label="name"
                                placeholder="Select Marital Status"
                            />

                        </b-form-group>
                    </b-col>

                    <b-col class="text-right" cols="6">
                        <b-button
                            v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                            class="mr-1"
                            type="submit"
                            variant="primary"
                            @click.prevent="search_emp"
                        >
                            Search Employee
                        </b-button>
                    </b-col>
                    <b-col cols="6">
                        <span class="text-right text-success">Total Employees : {{ employeeCount || 0 }}</span>
                    </b-col>
                </b-row>

                <!--job search -->
                <b-row>
                    <b-col cols="12">
                        <h4 class="text-danger pt-2 pb-2">JOB FILTER :</h4>
                    </b-col>

                    <b-col cols="6">
                        <b-form-group>
                            <label class="required">District</label>

                            <v-select
                                v-model="formDetails_job.district"
                                :options="distDrop"
                                :reduce="item=>item.dist_id"
                                label="dist"
                                multiple
                                placeholder="Select District"
                            />
                        </b-form-group>
                    </b-col>

                    <b-col cols="6">
                        <b-form-group>
                            <label class="required">Single Qualification</label>

                            <v-select
                                v-model="formDetails_job.sQualification"
                                :options="squalifyDrop"
                                :reduce="item=>item.id"
                                label="course"
                                multiple
                                placeholder="Select Single Qualification"
                            />

                        </b-form-group>
                    </b-col>

                    <b-col cols="6">
                        <b-form-group>
                            <label class="required">Employer
                                <small class="text-muted">Enter at least 3 characters to Search</small>
                            </label>

                            <v-select
                                v-model="formDetails_job.gQualification"
                                :clear-on-select="false"
                                :close-on-select="false"
                                :options="options"
                                :preselect-first="true"
                                :reduce="item=>item.id"
                                :selectable="item => item.is_block==0"
                                label="name"
                                multiple
                                placeholder="Select Employer"
                                track-by="name"
                                @search="onemployersearch"
                            />


                            <!--                            <multiselect-->
                            <!--                                v-model="formDetails_job.gQualification"-->
                            <!--                                :clear-on-select="false"-->
                            <!--                                :internal-search="false"-->
                            <!--                                :loading="isLoading"-->
                            <!--                                :multiple="true"-->
                            <!--                                :options="options"-->
                            <!--                                :searchable="true"-->
                            <!--                                :selectable="item => item.is_block==0"-->
                            <!--                                :show-no-results="false"-->
                            <!--                                class="header-select mr-3"-->
                            <!--                                label="name"-->
                            <!--                                placeholder="Select Employer"-->
                            <!--                                track-by="id"-->
                            <!--                                @search-change="onemployersearch"-->
                            <!--                            />-->

                        </b-form-group>

                        <!--                    <b-form-group>-->
                        <!--                        <label class="required">Group Qualification</label>-->

                        <!--                        <v-select-->
                        <!--                            v-model="formDetails_job.gQualification"-->
                        <!--                            :options="gqualifyDrop"-->
                        <!--                            :reduce="item=>item.id"-->
                        <!--                            label="course"-->
                        <!--                            multiple-->
                        <!--                            placeholder="Select Group Qualification"-->
                        <!--                        />-->

                        <!--                    </b-form-group>-->
                    </b-col>

                    <b-col cols="6">
                        <b-form-group>
                            <label class="required">Job Title</label>

                            <v-select
                                v-model="formDetails_job.job_title"
                                :options="getjobtitleArray"
                                :reduce="item=>item.id"
                                label="jobtitles"
                                multiple
                                placeholder="Select Job Title"
                            />
                        </b-form-group>
                    </b-col>

                    <b-col class="text-right" cols="6">
                        <b-button
                            v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                            class="mr-1"
                            type="submit"
                            variant="primary"
                            @click.prevent="search_job"
                        >
                            Search Jobs
                        </b-button>
                    </b-col>
                    <b-col cols="6">
                        <span class="text-right text-success">Total Jobs : {{ jobsCount || 0 }}</span>
                    </b-col>

                </b-row>
                <!--job search /. -->

                <b-row>
                    <b-col class="text-center p-2" cols="12">
                        <b-button
                            v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                            class="mr-1"
                            type="submit"
                            variant="success"
                            @click.prevent="save_notification"
                        >
                            Save Notification
                        </b-button>
                    </b-col>
                </b-row>

            </b-form>

            <b-row class="mb-1">
                <b-col cols="4">
                    <total-results :countText="getTableData.length"></total-results>
                    <!--                <h4>Total Rows : {{totalRows}}</h4>-->
                </b-col>

                <b-col cols="4"></b-col>

                <b-col cols="3"></b-col>

            </b-row>

            <b-row>
                <b-col cols="12">
                <span>
            <ag-grid-vue
                ref="myTable"
                :columnDefs="columnDefs.value"
                :defaultColDef="defaultColDef"
                :enableCharts="true"
                :enableRangeSelection="true"
                :rowData="getTableData"
                animateRows="true"
                class="ag-theme-alpine"
                rowSelection="multiple"
                style="height: 500px"
                @cell-clicked="cellWasClicked"
                @grid-ready="onGridReady"
            >
            </ag-grid-vue>
        </span>
                </b-col>


            </b-row>
        </b-overlay>

    </b-card-code>

</template>

<script>
import BCardCode from '@core/components/b-card-code'
import TotalResults from '@/views/elements/TotalResults'
import { ValidationObserver, ValidationProvider } from 'vee-validate'
import {
    BButton,
    BCardBody,
    BCol,
    BForm,
    BFormFile,
    BFormGroup,
    BFormInput,
    BFormRadio,
    BFormRadioGroup,
    BFormSelect,
    BFormTextarea,
    BOverlay,
    BRow,
    BSpinner,
} from 'bootstrap-vue'
import { max, min, required, } from '@validations'
import vSelect from 'vue-select'
import Ripple from 'vue-ripple-directive'
// import 'ag-grid-enterprise'
import { AgGridVue } from 'ag-grid-vue'
import 'ag-grid-community/styles/ag-grid.css' // Core grid CSS, always needed
import 'ag-grid-community/styles/ag-theme-alpine.css' // Optional theme CSS
import flatPickr from 'vue-flatpickr-component'
import Multiselect from 'vue-multiselect'

export default {
    components: {
        BSpinner,
        BCardCode,
        BCardBody,
        ValidationProvider,
        ValidationObserver,
        BFormInput,
        BFormRadio,
        BFormRadioGroup,
        BFormSelect,
        BFormGroup,
        BForm,
        BFormFile,
        BOverlay,
        BRow,
        BCol,
        BButton,
        BFormTextarea,
        AgGridVue,
        TotalResults,
        vSelect,
        Multiselect,
        flatPickr,
        ActionComponent: {
            template: '<span><button class="btn btn-warning" @click="NotifyRenderer(params.data.id)" style="position: relative;">Noti</button> <button class="btn btn-danger" @click="callDeleteMethod(\'/delNotification\', {id:params.data.id}, \'post\')">Delete</button> </span>',
            methods: {
                callDeleteMethod(url, dataa, requestMethod) {
                    // console.log(url, data, requestMethod)
                    this.$root.$emit('callDeleteMethod()', url, dataa, requestMethod)
                },

                // send notify
                async NotifyRenderer(getId) {
                    var rowNode = this.params.node.rowIndex
                    // alert("tableindex : "+rowNode);
                    if (confirm('Are You Sure Notify')) {
                        this.$root.$emit('sendNotifyRenderer1Event()', rowNode, '/sendNotification', {
                            id: this.params.data.id,
                            userid: JSON.parse(localStorage.getItem('userData')).id
                        }, this.params.data)
                    } else {
                        alert('Notify Cancel')
                    }

                },
            }
        },

        // flatPickr,
        // BFormCheckbox,
        // BTable
    },
    setup() {

        const gridApi = {} // Optional - for accessing Grid's API
        const onGridReady = (params) => {
            params.api.showLoadingOverlay()
            gridApi.value = params.api
        }
        const rowData = {} // Set rowData to Array of Objects, one Object per Row
        // Each Column Definition results in one Column.

        const columnDefs = {
            value: [
                {
                    headerName: 'S.No',
                    valueGetter: 'node.rowIndex + 1',
                    width: 90,
                },
                {
                    headerName: 'Action',
                    cellRendererSelector: params => {
                        return {
                            component: 'ActionComponent'
                        }
                    },
                    cellStyle: { 'white-space': 'normal' },
                    autoHeight: true,
                },
                {
                    headerName: 'Notification Title',
                    field: 'title',
                    width: 240,
                },
                {
                    headerName: 'Created Date',
                    field: 'added_date',
                    width: 190,
                },
                {
                    headerName: 'Create By',
                    field: 'cby',
                    width: 190,
                },
            ],
        }
        // DefaultColDef sets props common to all Columns
        const defaultColDef = {
            sortable: true,
            filter: true,
            flex: 0,
            resizable: true,
            defaultColDef: {
                resizable: true,
            },

        }
        return {
            onGridReady,
            columnDefs,
            rowData,
            defaultColDef,
            cellWasClicked: (event) => { // Example of consuming Grid Event
                // console.log("cell was clicked", event);
            },
            deselectRows: () => {
                // gridApi.value.deselectAll()
            },
        }
    },
    directives: {
        Ripple,
    },
    data() {
        return {
            overlayShow: false,
            formDetails_emp: {
                msg_title: '',
                sQualification: [],
                gQualification: [],
                jobskills: [],
                joblocation: [],
                gender: '',
                marital: '',
                work_status: '',
                job_title: [],
            },
            formDetails_job: {
                district: [],
                sQualification: [],
                gQualification: [],
                job_title: [],
            },
            squalifyDrop: [],
            gqualifyDrop: [],
            skillsDrop: [],
            distDrop: [],
            job_title: [],
            genderDrop: [
                {
                    id: 0,
                    name: 'Male'
                },
                {
                    id: 1,
                    name: 'Female'
                },
                {
                    id: 2,
                    name: 'All'
                },
            ],
            maritalDrop: [
                {
                    id: 0,
                    name: 'Unmarried'
                },
                {
                    id: 1,
                    name: 'Married'
                },
                {
                    id: 2,
                    name: 'None'
                },
            ],
            wrokstatusDrop: [
                {
                    id: 0,
                    name: 'Fresher'
                },
                {
                    id: 1,
                    name: 'Experienced'
                },
                {
                    id: 2,
                    name: 'None'
                },
            ],
            employeeCount: '',
            jobsCount: '',
            options: [],
            isLoading: false,
            data: {
                jobtitle: '',
                district: '',
                city: '',
            },
            getjobtitleArray: [],

            getTableDataLength: '',
            getDistrictArray: [],
            getCityArray: [],
            disableStartEnddate: false,
            getTableData: [],
            EmployeeHistoryData: [],
            initial_loading: true,
            initial_loading_job: true,
            variants: ['light', 'primary', 'secondary', 'danger', 'warning', 'success', 'info', 'dark'],
            maxChar500: 500,
            maxChar1000: 1000,
            required,
            min,
            max,
        }
    },
    async created() {
        this.getDetails()
        const callAxios = await this.callAxios('/getDistrictData', {}, 'post')
        this.getDistrictArray = callAxios.data.distData
        this.getCityArray = callAxios.data.cityData

        // 03012023 get signle qualification
        const callAxios_sQualification = await this.callAxios('/getsingleQualification', this.data, 'post')
        if (callAxios_sQualification.status === 200) {
            this.squalifyDrop = callAxios_sQualification.data.sQualification
        }
        // 03012023 get signle qualification /.

        // 03012023 get group qualification
        const callAxios_gQualification = await this.callAxios('/getgroupQualification', {}, 'post')
        if (callAxios_sQualification.status === 200) {
            this.gqualifyDrop = callAxios_gQualification.data.gQualification
        }
        // 03012023 get group qualification /.

        // 03012023 get Job Skills
        const callAxios_jobSkills = await this.callAxios('/getskillsDrop', {}, 'post')
        if (callAxios_jobSkills.status === 200) {
            this.skillsDrop = callAxios_jobSkills.data.skillsDrop
        }
        // 03012023 get Job Skills /.

        // 03012023 get district for job location
        const callAxios_distDrop = await this.callAxios('/getdistDrop', {}, 'post')
        if (callAxios_distDrop.status === 200) {
            this.distDrop = callAxios_distDrop.data.distDrop
        }
        // 03012023 get Job Skills /.

        // get job titles data
        const callAxios_jobtitle = await this.callAxios('/getJobTitlesData', this.data, 'post')
        if (callAxios.status === 200) {
            this.getjobtitleArray = callAxios_jobtitle.data.getJobTitles
        }
    },
    async mounted() {
        this.$root.$on('callDeleteMethod()', (url, data, requestMethod) => {
            this.callDeleteMethod(url, data, requestMethod)
        })

        // send notify
        this.$root.$on('sendNotifyRenderer1Event()', (index, url, data, dataJson) => {
            this.NotifybtnCellRenderer1(index, url, data, dataJson)
        })
    },

    methods: {
        // select employer
        async onemployersearch(query) {

            if (query.length > 3) {
                this.isLoading = true
                console.log('query : ' + query)
                const response = await await axios.post('/searchPrivateEmployer', {
                    search_term: query
                })
                this.options = response.data
                this.isLoading = false
            }
            // this.isLoading = true;
            // // make API request to load websites
            // this.websites = await loadWebsites(query);
            // this.isLoading = false;
        },
        // onemployersearch(search, loading) {
        //     // alert("ok")
        //     if (search.length > 3) {
        //         loading(true);
        //         this.employersearch(loading, search, this);
        //     }
        //
        // },
        // employersearch: _.debounce(async (loading, search, vm) => {
        //     try {
        //         const response = await axios.post('/searchPrivateEmployer', {
        //             search_term: search
        //         })
        //         vm.options = response.data
        //         // console.log("bala : "+response.data);
        //     } catch (error) {
        //         console.error(error);
        //     } finally {
        //         loading(false);
        //     }
        //
        //     loading(false);
        // }, 350),

        // delete noti
        callDeleteMethod(url, data, requestMethod) {
            // console.log("123", url, data, requestMethod)
            this.sweetAlertDeleteMethod(url, data, requestMethod, 'ag-grid')
        },

        // send notifycation
        async NotifybtnCellRenderer1(index, url, data, dataJson) {
            this.overlayShow = true
            const callAxios = await this.callAxios(url, data, 'post')
            if (callAxios.status === 200) {
                this.sweetAlertmethod('success', 'Send Notification Successfully', 'Send Notification Successfully', 'btn-primary')
                this.overlayShow = false
            }

        },

        // get Employee Details Reorts
        getDetails() {
            this.total = 'Loading...'
            axios.post('/getNotification', this.data)
                .then(res => {
                    console.log(res.data.getNotifiData)
                    this.totalRows = res.data.getNotifiData.length
                    this.getTableData = res.data.getNotifiData
                })
        },

        async search_emp() {
            this.initial_loading = false
            console.log('sq value : ' + this.formDetails_emp.sQualification)
            const callAxios_empCounts = await this.callAxios('/addNotification_emplyee_count', this.formDetails_emp, 'post')
            if (callAxios_empCounts.status === 200) {
                this.employeeCount = callAxios_empCounts.data.eCounts
                this.initial_loading = true
            }
        },

        // search jobs
        async search_job() {
            console.log('this.formDetails_job : ' + JSON.stringify(this.formDetails_job.gQualification))
            this.initial_loading = false
            console.log('sq value : ' + this.formDetails_emp.sQualification)
            console.log(this.formDetails_job)
            const callAxios_jobCounts = await this.callAxios('/addNotification_job_count', this.formDetails_job, 'post')
            if (callAxios_jobCounts.status === 200) {
                this.jobsCount = callAxios_jobCounts.data.jCounts
                this.initial_loading = true
            }
        },

        // save notification
        async save_notification() {
            const job_plan_id = this.formDetails_emp.msg_title
            if (job_plan_id != '') {
                var localUserId = JSON.parse(localStorage.getItem('userData')).id
                this.initial_loading = false
                console.log('sq value : ' + this.formDetails_emp.sQualification)
                const callAxios_saveNoti = await this.callAxios('/saveNotificationPart', {
                    formDetails_emp: this.formDetails_emp,
                    formDetails_job: this.formDetails_job,
                    localUserid: localUserId,
                }, 'post')
                if (callAxios_saveNoti.status === 200) {
                    if (callAxios_saveNoti.data.adStatus == true) {
                        this.getTableData = ''
                        this.getDetails()
                        this.sweetAlertmethod('success', 'success', callAxios_saveNoti.data.adMessage)
                        this.formDetails_emp = {
                            msg_title: '',
                            sQualification: '',
                            gQualification: '',
                            jobskills: '',
                            joblocation: '',
                            gender: '',
                            marital: '',
                            work_status: '',
                            job_title: '',
                        }
                        this.formDetails_job = {
                            district: '',
                            sQualification: '',
                            gQualification: '',
                            job_title: '',
                        },
                            this.squalifyDrop = []
                        this.gqualifyDrop = []
                        this.skillsDrop = []
                        this.distDrop = []
                        this.job_title = []
                        this.genderDrop = []
                        this.maritalDrop = []
                        this.wrokstatusDrop = []
                        this.employeeCount = ''
                        this.jobsCount = ''
                        this.getjobtitleArray = []
                        this.getDistrictArray = []
                        this.getCityArray = []
                        this.initial_loading = true
                    } else {
                        this.sweetAlertmethod('warning', 'warning', callAxios_saveNoti.data.adMessage)
                        this.initial_loading = true
                    }
                    // this.jobsCount = callAxios_saveNoti.data.jCounts;

                }
            } else {
                this.sweetAlertmethod('warning', 'warning', 'Please Enter Message Title')
            }
        },

        async getEmployeeDetailsReorts() {
            // console.log("b1 : "+ this.data.jobtitle)
            this.initial_loading = false
            let res = await axios.post('/getSearchEmployeesData', this.data)
            this.initial_loading = false
            if (res.status === 200) {
                console.log('b : ' + res.data.getStatus)
                if (res.data.getStatus != false) {
                    // alert("if : "+asd.data.empCounts);
                    this.getTableData = res.data.getSearchData
                    this.getTableDataLength = this.getTableData.length
                    this.initial_loading = true
                } else {
                    // alert("else : "+asd.data.empCounts);
                    this.sweetAlertmethod('warning', 'warning', 'NO Data')
                    this.initial_loading = true
                }
            }
        },

        // get employes counts city based
        async getEmployeesCount() {
            const asd = await axios.post('/getEmployeesCounts', this.data)
            if (asd.status === 200) {
                if (asd.data.empCounts != undefined) {
                    // alert("if : "+asd.data.empCounts);
                    this.getTableData = asd.data.empCounts
                    this.getTableDataLength = this.getTableData.length
                } else {
                    // alert("else : "+asd.data.empCounts);
                    this.sweetAlertmethod('warning', 'warning', 'NO Data')
                }
            }
            console.log(this.getTableData)
        },

    },
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
@import '~@resources/scss/vue/libs/vue-good-table.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';

</style>

<style>
::selection {
    color: #fff;
    background: #28c76f;
}

.outer-div {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.button-bar {
    padding-bottom: 4px;
}

.grid-wrapper {
    height: 20px;
    flex: 1 1 auto;
}
</style>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
