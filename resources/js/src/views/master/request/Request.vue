<template>
    <b-card-code title="Edit Request">
        <b-form>
            <b-row>
                <b-col md="3">
                    <b-form-group>
                        <label>Table</label>
                        <v-select
                            v-model="data.filterTable"
                            :options="select_Table"
                            :reduce="item=>item.id"
                            label="title"
                            placeholder="All"
                            @input="getPrivateJob()"
                        />
                    </b-form-group>
                </b-col>
                <b-col md="3">
                    <b-form-group>
                        <label>Verified Status</label>
                        <v-select
                            v-model="data.filterverified"
                            :options="select_verified"
                            :reduce="item=>item.id"
                            label="title"
                            placeholder="All"
                            @input="getPrivateJob()"

                        />
                    </b-form-group>
                </b-col>
            </b-row>
        </b-form>
        <!-- input search -->
        <div class="custom-search justify-content-end">
            <b-row>
                <b-col md="2">
                    <add-button-icons-link
                        buttonColor="primary"
                        buttonIcon="PlusIcon"
                        buttonLink="/posting/govt"
                        buttonText="Add New"
                    ></add-button-icons-link>
                </b-col>
                <b-col md="3">
                    <total-results :countText="total"></total-results>
                </b-col>
                <b-col class="mt-n2" md="3">
                    Export To :
                    <span>
                        <button @click="onBtnExportDataAsExcel">
                            <img src="@/assets/images/icons/xls.png" width="50">
                        </button>
                    </span>
                </b-col>
                <b-col md="4">
                    <b-form-group>
                        <div class="d-flex align-items-center">
                            <label class="mr-1">Search</label>
                            <b-form-input
                                v-model="searchTerm"
                                class="d-inline-block"
                                placeholder="Search"
                                type="text"
                            />

                        </div>
                    </b-form-group>
                </b-col>
            </b-row>
        </div>

        <ag-grid-vue
            ref="myTable"
            :columnDefs="columnDefs.value"
            :defaultColDef="defaultColDef"
            :excelStyles="excelStyles"
            :rowData="getTableData"
            animateRows="true"
            class="ag-theme-alpine"
            rowSelection="multiple"
            style="height: 500px"
            @cell-clicked="cellWasClicked"
            @grid-ready="onGridReady"
        >
        </ag-grid-vue>
        <view-private-employer :id="this.model_id"></view-private-employer>

    </b-card-code>
</template>

<script>
import BCardCode from '@core/components/b-card-code/BCardCode.vue'
import AddButtonIcons from '@/views/elements/AddButtonIcons'
import AddButtonIconsLink from '@/views/elements/AddButtonIconsLink.vue'
import TotalResults from '@/views/elements/TotalResults'
import Ripple from 'vue-ripple-directive'
import downloadexcel from 'vue-json-excel'
import { jsPDF } from 'jspdf'
import vSelect from 'vue-select'

import {
    BAvatar,
    BBadge,
    BButton,
    BCard,
    BOverlay,
    BCardBody,
    BCardHeader,
    BCardText,
    BCol,
    BDropdown,
    BDropdownItem,
    BForm,
    BFormCheckbox,
    BFormFile,
    BFormGroup,
    BFormInput,
    BFormSelect,
    BFormTextarea,
    BIcon,
    BImg,
    BLink,
    BListGroup,
    BListGroupItem,
    BModal,
    BPagination,
    BRow,
    BTab,
    BTabs,
    VBModal,
    VBTooltip,
} from 'bootstrap-vue'

import { VueGoodTable } from 'vue-good-table'
import store from '@/store'
import { ValidationObserver, ValidationProvider } from 'vee-validate'
import Button from '@/views/components/button/Button'
import { AgGridVue } from 'ag-grid-vue'
// import { reactive, onMounted, ref } from "vue"
import 'ag-grid-community/styles/ag-grid.css' // Core grid CSS, always needed
import 'ag-grid-community/styles/ag-theme-alpine.css'
import moment from 'moment-timezone'
import { getUserData } from '@/auth/utils' // Optional theme CSS
import ViewPrivateEmployer from '@/views/elements/ViewPrivateEmployer.vue'
import { required } from '@validations'

const pdfdoc = new jsPDF('landscape')

function createHeaders(keys) {
    var result = []
    for (var i = 0; i < keys.length; i += 1) {
        result.push({
            'id': keys[i],
            'name': keys[i],
            'prompt': keys[i],
            'width': 65,
            'align': 'center',
            'padding': 0
        })
    }
    return result
}

const userData = getUserData()
export default {
    props: ['id'],
    components: {
        BIcon,
        Button,
        BOverlay,
        BButton,
        BFormTextarea,
        BRow,
        BCol,
        BModal,
        VBModal,
        BFormFile,
        BImg,
        BLink,
        BForm,
        BCardCode,
        AddButtonIcons,
        AddButtonIconsLink,
        ViewPrivateEmployer,
        TotalResults,
        VueGoodTable,
        downloadexcel,
        BPagination,
        BFormGroup,
        BFormInput,
        BFormSelect,
        BDropdown,
        BDropdownItem,
        ValidationProvider,
        ValidationObserver,
        vSelect,
        AgGridVue,
        BAvatar,
        BCard,
        BCardText,
        BCardHeader,
        BCardBody,
        BBadge,
        BTab,
        BTabs,
        BListGroup,
        BListGroupItem,
        imageComponent: {
            template: '<img style="width: 100px" :src="this.params.data.logo">',
        },
        personProofComponent: {
            template: '<img style="width: 100px" :src="this.params.data.person_proof">',
        },
        companyProofComponent: {
            template: '<img style="width: 100px" :src="this.params.data.companyProof">',
        },
        idProofComponent: {
            template: '<img style="width: 100px" :src="this.params.data.id_proof">',
        },
        actionsComponent: {
            template: '<span><b-button class="btn-icon rounded-circle" style="margin: 3px" variant="gradient-warning" @click="openModel(params.data.id)"><feather-icon icon="EyeIcon" /></b-button><b-button class="btn-icon rounded-circle" style="margin: 3px" variant="gradient-primary" target="_blank" :href="hostNameEdit+\'/employer/private/?id=\'+params.data.id"><feather-icon icon="EditIcon" /></b-button><b-button class="btn-icon rounded-circle" style="margin: 3px" variant="gradient-success" @click="acceptEditRequest(params.data.edit_request_id)"><feather-icon icon="CheckCircleIcon" /></b-button><b-button class="btn-icon rounded-circle" style="margin: 3px" variant="gradient-danger" @click="rejectEditRequest(params.data.edit_request_id)"><feather-icon icon="XCircleIcon" /></b-button></span>',
            components: {
                ViewPrivateEmployer,
                BButton,
                BCardText,
                BModal,
                VBModal,
                BFormCheckbox,
            },
            directives: {
                Ripple,
            },
            created() {
                if (location.hostname == 'localhost') {
                    this.hostNameEdit = 'http://' + location.hostname + ':3000'
                } else {
                    this.hostNameEdit = 'https://' + location.hostname + '/tjobspro/'
                }
            },
            data() {
                return {
                    dir: false,
                    editModelId: '',
                    bodyBgVariant: 'light',
                    bodyTextVariant: 'dark',
                    footerBgVariant: 'warning',
                    footerTextVariant: 'dark',
                    headerBgVariant: 'primary',
                    headerTextVariant: 'light',
                }
            },
            direction() {
                if (store.state.appConfig.isRTL) {
                    // eslint-disable-next-line vue/no-side-effects-in-computed-properties
                    this.dir = true
                    return this.dir
                }
                // eslint-disable-next-line vue/no-side-effects-in-computed-properties
                this.dir = false
                return this.dir
            },
            methods: {
                openModel(value) {
                    // alert(value)
                    var rowNode = this.params.node.rowIndex
                    // this.model_id = value
                    // alert(this.model_id)
                    this.$bvModal.show('modal-footer-lg')
                    this.$root.$emit('openModel()', value)

                },
                openEditModel(value) {
                    // alert(value)
                    var rowNode = this.params.node.rowIndex
                    this.editModelId = value
                    // alert(this.model_id)
                    this.$bvModal.show('EditRequestmodal')
                    // this.$refs['EditRequestmodal'].show();
                    this.$root.$emit('openEditModel()', value)

                },
                openModell(value) {
                    var rowNode = this.params.node.rowIndex
                    this.$root.$emit('openModel()', value)
                },
                callDeleteMethod(url, data, requestMethod) {
                    this.$root.$emit('callDeleteMethod()', url, data, requestMethod)
                },
                acceptEditRequest(value) {
                    this.$root.$emit('acceptEditRequest()', value)
                },
                rejectEditRequest(value) {
                    this.$root.$emit('rejectEditRequest()', value)
                },
            }
        },
        viewCountComponent: {
            template: '<button class="btn btn-danger" @click="openModelViewCount(params.data.id)" style="position: relative;" >View</button>',

            methods: {

                openModelViewCount(value) {
                    var rowNode = this.params.node.rowIndex
                    this.$root.$emit('openModelView()', value)
                },
            }
        },
        paymentStatusComponent: {

            template: '<span v-if="params.data.payment_status==\'Paid\'" class="text-success" > {{ params.data.payment_status }} ({{ params.data.txn_amt }})<br> {{ params.data.paid_date }}</span><span v-else class="text-danger">{{ params.data.payment_status }}</span>',
        },
        proofComponent: {

            template: '<span v-if="params.data.company_proof_type != 0 && params.data.id_proof_type != 0 && params.data.person_proof_type != 0 && params.data.is_com_add_proof === 1 && params.data.is_com_id_proof === 1 && params.data.is_person_proof === 1" class="text-success" > verified</span><span v-else class="text-danger">Not verified</span>',
        },
    },
    setup() {
        // const gridApi = ref(); // Optional - for accessing Grid's API
        const gridApi = {} // Optional - for accessing Grid's API
        // console.log(gridApi)
        // Obtain API from grid's onGridReady event
        const onGridReady = (params) => {
            gridApi.value = params.api
        }
        // const rowData = reactive({}); // Set rowData to Array of Objects, one Object per Row
        const rowData = {} // Set rowData to Array of Objects, one Object per Row
        // Each Column Definition results in one Column.
        const excelStyles = [
            {
                id: 'dateUK',
                dataType: 'DateTime',
                numberFormat: {
                    format: 'dd/mm/yy',
                },
            },
        ]
        const columnDefs = {
            value: [
                {
                    headerName: 'S.No',
                    valueGetter: 'node.rowIndex + 1',

                },
                {
                    headerName: 'Action',
                    cellRendererSelector: params => {
                        return {
                            component: 'actionsComponent'
                        }

                    },
                    cellStyle: { 'white-space': 'normal' },
                    autoHeight: true,
                },
                {
                    headerName: 'Employer ID',
                    field: 'id',

                },
                {
                    headerName: 'Company Name',
                    field: 'companyname',

                },
                {
                    headerName: 'remark',
                    field: 'remark',
                    width: 200,
                },
                {
                    headerName: 'Person Proof',
                    cellRendererSelector: params => {
                        return {
                            component: 'personProofComponent'
                        }

                    },
                    cellStyle: { 'white-space': 'normal' },
                    autoHeight: true,
                },
                {
                    headerName: 'Company Proof',
                    cellRendererSelector: params => {
                        return {
                            component: 'companyProofComponent'
                        }

                    },
                    cellStyle: { 'white-space': 'normal' },
                    autoHeight: true,
                },
                {
                    headerName: 'ID Proof',
                    cellRendererSelector: params => {
                        return {
                            component: 'idProofComponent'
                        }

                    },
                    cellStyle: { 'white-space': 'normal' },
                    autoHeight: true,
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
            getQuickFilterText: params => {
                return params.value.name
            }
        }
        return {
            onGridReady,
            columnDefs,
            rowData,
            defaultColDef,
            excelStyles,
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
        'b-tooltip': VBTooltip,
    },
    data() {
        return {
            data: {
                person_proof: '',
                id_proof: '',
                companyProof: '',
                remark: '',
                id: '',
                inby: userData.id,
            },
            overlay: false,
            showEditmodel: true,
            variant: 'light',
            opacity: 0.85,
            blur: '2px',
            selectcompanyProofImg: '',
            selectid_proofImg: '',
            selectperson_proofImg: '',
            getTableData: [],
            searchTerm: '',
            rowsView: [],
            model_id: '',
            editModelId: '',
            pageLength: 100,
            columnsView: [
                {
                    label: '#ID',
                    field: 'id',
                    type: 'number',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search id',
                    },
                },

                {
                    label: 'View Count',
                    field: 'view_count',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search',
                    },
                },
                {
                    label: 'View Count Unique',
                    field: 'view_count_unique',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search',
                    },
                },
                {
                    label: 'Call Count',
                    field: 'call_count',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search',
                    },
                },
                {
                    label: 'Call Count Unique',
                    field: 'call_count_unique',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search',
                    },
                },
                {
                    label: 'Confirm Call Count',
                    field: 'confirm_call_count',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search',
                    },
                },
                {
                    label: 'Detail Count',
                    field: 'detail_count',
                    width: '50vh',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search',
                    },
                },
                {
                    label: 'Resume Count',
                    field: 'resume_count',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search',
                    },
                },

            ],
            listQuali: [],
            qualisingle: [],
            qualigroup: [],
            keyWord: [],
            bodyBgVariant: 'light',
            bodyTextVariant: 'dark',
            footerBgVariant: 'warning',
            footerTextVariant: 'dark',
            headerBgVariant: 'primary',
            headerTextVariant: 'light',
            required,
            select_Table: [
                {
                    id: 'live',
                    title: 'Live Table'
                },
                {
                    id: 'all',
                    title: 'All Jobs'
                },
                {
                    id: 'expired',
                    title: 'Expired Jobs'
                },
                {
                    id: 'deleted',
                    title: 'Deleted Jobs'
                },
            ],
            select_verified: [{
                id: 1,
                title: 'Verified'
            }, {
                id: 2,
                title: 'Not Verified'
            }],
        }
    },
    async mounted() {
        this.$root.$on('openModel()', (data) => {
            this.openModel(data)
        })
        this.$root.$on('acceptEditRequest()', (data) => {
            this.acceptEditRequest(data)
        })
        this.$root.$on('rejectEditRequest()', (data) => {
            this.rejectEditRequest(data)
        })
        this.$root.$on('openEditModel()', (data) => {
            this.openEditModel(data)
        })
        this.$root.$on('openModelView()', (data) => {
            this.openModelView(data)
        })

        this.$root.$on('callDeleteMethod()', (url, data, requestMethod) => {
            this.callDeleteMethod(url, data, requestMethod)
        })

        // this.props.
    },
    computed: {
        statusVariant() {
            const statusColor = {
                /* eslint-disable key-spacing */
                Current: 'light-primary',
                Professional: 'light-success',
                Rejected: 'light-danger',
                Resigned: 'light-warning',
                Applied: 'light-info',
                /* eslint-enable key-spacing */
            }

            return status => statusColor[status]
        },

    },
    created() {
        this.getPrivateJob()
    },
    direction() {
        if (store.state.appConfig.isRTL) {
            // eslint-disable-next-line vue/no-side-effects-in-computed-properties
            this.dir = true
            return this.dir
        }
        // eslint-disable-next-line vue/no-side-effects-in-computed-properties
        this.dir = false
        return this.dir
    },
    methods: {

        validationForm() {
            this.$refs.simpleRules.validate()
                .then(async success => {
                    if (success) {
                        this.btnShow = false
                        const data = new FormData()
                        data.append('person_proof', this.data.person_proof)
                        data.append('id_proof', this.data.id_proof)
                        data.append('companyProof', this.data.companyProof)
                        data.append('inby', this.data.inby)
                        data.append('remark', this.data.remark)
                        data.append('id', this.data.id)
                        data.append('inby', this.data.inby)
                        this.overlay = 'show'
                        // return 0
                        console.log(this.data)
                        const config = {
                            'content-type': 'multipart/form-data',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        }
                        const callAxios = await this.callAxiosWithConfig('/saveEditRequest', data, 'post', config)
                        // console.log("00"+callAxios);
                        // console.log(callAxios);
                        // console.log("11");
                        if (callAxios.status === 200) {
                            this.sweetAlertmethod('success', callAxios.data.type, callAxios.data.msg, 'btn-primary')

                            this.data.person_proof = ''
                            this.data.id_proof = ''
                            this.data.companyProof = ''
                            this.data.remark = ''
                            this.selectcompanyProofImg = ''
                            this.selectid_proofImg = ''
                            this.selectperson_proofImg = ''
                            this.$refs['EditRequestmodal'].hide()
                            this.overlay = false
                        } else {
                            this.overlay = false
                            this.btnShow = true
                            this.sweetAlertmethod('error', 'Alert', callAxios.response.data.message, 'btn-primary')

                        }
                    }
                })
        },

        getPrivateJob() {
            this.total = 'Loading...'
            this.data.table = 'jobs'
            this.data.jobtype = [1]
            this.data.jobstatus = this.data.filterTable
            this.data.verify = this.data.filterverified

            axios.post('/getEditRequest', this.data)
                .then(res => {
                    // console.log(res.data)
                    this.total = res.data.length
                    this.total = res.data.length
                    this.getTableData = res.data
                })
        },

        onBtnExportDataAsExcel() {
            this.gridApi.exportDataAsExcel()
        },
        startDownload() {
            alert('show loading')
        },
        finishDownload() {
            alert('hide loading')
        },
        callDeleteMethod(url, data, requestMethod) {
            console.log(this.sweetAlertDeleteMethod(url, data, requestMethod))
        },
        async openModelView(value) {
            if (value) {
                // alert(value)
                this.data.id = value
                const callAxios = await this.callAxios('/privateViewCount', { id: value }, 'post')
                console.log(callAxios)
                var return_data = callAxios.data[0]
                if (callAxios.status === 200) {
                    this.rowsView = callAxios.data
                    this.$bvModal.show('view-model')
                } else {
                    // console.log(callAxios.response.data.message);
                    // this.toast("Error", callAxios.response.data.message, 'danger');
                    this.sweetAlertmethod('error', 'Error', callAxios.response.data.message, 'btn-primary')
                }
            } else {
                this.rowsView = ''
            }
        },
        openModel(value) {
            // alert('gs');
            this.model_id = value
            // this.$bvModal.show('modal-footer-lg')

            // this.getDataGovent(value, 'private')

        },
        async acceptEditRequest(value) {
            alert('pandiya' + value)
            const callAxios = await this.callAxios('/acceptEditRequest', { id: value }, 'post')
            console.log(callAxios)
            var return_data = callAxios.data[0]
            if (callAxios.status === 200) {
                this.sweetAlertmethod('success', 'Edit Request Accepted ', 'Edit Request Accepted', 'btn-primary')
                this.total = callAxios.data.length
                this.total = callAxios.data.length
                this.getTableData = callAxios.data
            } else {
                // console.log(callAxios.response.data.message);
                // this.toast("Error", callAxios.response.data.message, 'danger');
                this.sweetAlertmethod('error', 'Error', callAxios.response.data.message, 'btn-primary')
            }
            // this.model_id = value
            // this.$bvModal.show('modal-footer-lg')

            // this.getDataGovent(value, 'private')

        },
        async rejectEditRequest(value) {
            // alert('pandiya'+value);
            const callAxios = await this.callAxios('/rejectEditRequest', { id: value }, 'post')
            console.log(callAxios)
            var return_data = callAxios.data[0]
            if (callAxios.status === 200) {
                this.sweetAlertmethod('success', 'Edit Request Rejected ', 'Edit Request Rejected', 'btn-primary')
                this.total = callAxios.data.length
                this.total = callAxios.data.length
                this.getTableData = callAxios.data
            } else {
                // console.log(callAxios.response.data.message);
                // this.toast("Error", callAxios.response.data.message, 'danger');
                this.sweetAlertmethod('error', 'Error', callAxios.response.data.message, 'btn-primary')
            }
            // this.model_id = value
            // this.$bvModal.show('modal-footer-lg')

            // this.getDataGovent(value, 'private')

        },
        openEditModel(value) {
            // alert('gs');
            this.editModelId = value
            this.data.id = value
            this.data.person_proof = ''
            this.data.id_proof = ''
            this.data.companyProof = ''
            this.data.remark = ''
            this.selectcompanyProofImg = ''
            this.selectid_proofImg = ''
            this.selectperson_proofImg = ''
            this.overlay = false
            this.$refs['EditRequestmodal'].show()
        },
        async getDataGovent(value, type) {

            const callAxios = await this.callAxios('/getSinglejobs', {
                id: value,
                jobtype: type,
            }, 'post')
            console.log(callAxios)
            var return_data = callAxios.data[0]
            if (callAxios.status === 200) {
                const logo_image = ''
                this.data = return_data
                this.data.postedDate = moment(callAxios.data[0].postedDate)
                    .format('DD/MM/YYYY')
                this.data.starting_date = moment(callAxios.data[0].starting_date)
                    .format('DD/MM/YYYY')
                this.data.expired_date = moment(callAxios.data[0].expired_date)
                    .format('DD/MM/YYYY')
                this.data.starting = moment(callAxios.data[0].starting)
                    .format('DD/MM/YYYY')
                this.data.ending = moment(callAxios.data[0].ending)
                    .format('DD/MM/YYYY')
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
                console.log(this.data.examdate)
                this.data.dateDiff = days_diff
                this.data.days_diff_remain = days_diff_remain
                this.logo_image = callAxios.data[0].logo_image
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
                    this.data.image = 'https://nithrajobs.com/assets/dist/img/private_job.webp'
                }

            } else {
                // console.log(callAxios.response.data.message);
                // this.toast("Error", callAxios.response.data.message, 'danger');
                this.sweetAlertmethod('error', 'Error', callAxios.response.data.message, 'btn-primary')
            }
        },
        jobtypeText(jobtype) {
            switch (jobtype) {
                case 1:
                    return 'Private'
                    break
                case 2:
                    return 'State'
                    break
                case 3:
                    return 'Central'
                    break
            }
        },
        jobtypeVarient(jobtype) {
            switch (jobtype) {
                case 1:
                    return 'light-info'
                    break
                case 2:
                    return 'light-success'
                    break
                case 3:
                    return 'light-danger'
                    break
            }
        },
        async showModal(value) {
            if (value) {
                this.data.id = value
                const callAxios = await this.callAxios('/getSingleUser', { id: value }, 'post')
                console.log(callAxios)
                var return_data = callAxios.data[0]
                if (callAxios.status === 200) {
                    this.data = {
                        id: return_data.id,
                        name: return_data.name,
                        phonenumber: return_data.phonenumber,
                        uname: return_data.uname,
                        usergroup: return_data.usergroup,
                    }
                    this.$refs['addForm'].show()
                } else {
                    // console.log(callAxios.response.data.message);
                    // this.toast("Error", callAxios.response.data.message, 'danger');
                    this.sweetAlertmethod('error', 'Error', callAxios.response.data.message, 'btn-primary')
                }
            } else {
                this.data = {
                    id: '',
                    name: '',
                }
            }
        },
        hideModal() {
            this.$refs['addForm'].hide()
            this.data = {
                id: '',
                name: '',
            }
        },
        onAuthorisedPersonProofImageChange(e) {
            // console.log('here')
            const files = e.target.files[0]
            // console.log(e.target.files[0]);

            this.data.person_proof = e.target.files[0]
            this.selectperson_proofImg = URL.createObjectURL(files)
            // console.log( this.data.id_proof);
        },
        onIdProofImageChange(e) {
            // console.log('here')
            const files = e.target.files[0]
            // console.log(e.target.files[0])

            this.data.id_proof = e.target.files[0]
            this.selectid_proofImg = URL.createObjectURL(files)
        },
        onCompanyAddressProofImageChange(e) {
            // console.log('here')
            const files = e.target.files[0]
            // console.log(e.target.files[0]);

            this.data.companyProof = e.target.files[0]
            this.selectcompanyProofImg = URL.createObjectURL(files)
        },

    },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
@import '~@resources/scss/vue/libs/vue-good-table.scss';
</style>
