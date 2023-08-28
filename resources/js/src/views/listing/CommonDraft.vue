<template>
    <b-card-code :title=title>
        <!-- input search -->
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
        <ag-grid-vue
            v-show="!initial_loading"
            :columnDefs="columnDefs"
            :defaultColDef="defaultColDef"
            :overlayNoRowsTemplate="overlayNoRowsTemplate"
            :rowData="rowData"
            animateRows="true"
            class="ag-theme-alpine"
            rowSelection="multiple"
            style="height: 500px"
            @grid-ready="onGridReady"
        >
        </ag-grid-vue>
        Showing {{ totalData.from }} - {{ totalData.to }} results
        <b-pagination
            v-model="totalData.current_page"
            :per-page="totalData.per_page"
            :total-rows="totalData.total"
            align="right"
            pills
            @input="getPrivateJob"
        ></b-pagination>

        <p v-if="this.selectedIds">{{ this.selectedIds.join() }}</p>
        <view-private-jobs :id="this.model_id" :radiesArray="this.radiesArray" :table=this.table></view-private-jobs>
        <b-modal
            id="view-model"
            centered
            hide-backdrop
            ok-only
            ok-title="Close"
            :title="jobTitle"
            size="xl"
        >
            <div class="custom-search d-flex justify-content-end">
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
            </div>

            <!-- table -->
            <vue-good-table
                :columns="columnsView"
                :pagination-options="{
        enabled: true,
        perPage:pageLength
      }"
                :rows="rowsView"
                :search-options="{
        enabled: true,
        externalQuery: searchTerm }"
                :select-options="{
        enabled: true,
        selectOnCheckboxOnly: true, // only select when checkbox is clicked instead of the row
        selectionInfoClass: 'custom-class',
        selectionText: 'rows selected',
        clearSelectionText: 'clear',
        disableSelectInfo: true, // disable the select info panel on top
        selectAllByGroup: true, // when used in combination with a grouped table, add a checkbox in the header row to check/uncheck the entire group
      }"
            >
                <template
                    slot="table-row"
                    slot-scope="props"
                >

                    <!-- Column: Name -->
                    <span
                        v-if="props.column.field === 'fullName'"
                        class="text-nowrap"
                    >
          <b-avatar
              :src="props.row.avatar"
              class="mx-1"
          />
          <span class="text-nowrap">{{ props.row.fullName }}</span>
        </span>

                    <!-- Column: Status -->
                    <span v-else-if="props.column.field === 'status'">
          <b-badge :variant="statusVariant(props.row.status)">
            {{ props.row.status }}
          </b-badge>
        </span>

                    <!-- Column: Action -->
                    <span v-else-if="props.column.field === 'action'">
          <span>
            <b-dropdown
                no-caret
                toggle-class="text-decoration-none"
                variant="link"
            >
              <template v-slot:button-content>
                <feather-icon
                    class="text-body align-middle mr-25"
                    icon="MoreVerticalIcon"
                    size="16"
                />
              </template>
              <b-dropdown-item>
                <feather-icon
                    class="mr-50"
                    icon="Edit2Icon"
                />
                <span>Edit</span>
              </b-dropdown-item>
              <b-dropdown-item>
                <feather-icon
                    class="mr-50"
                    icon="TrashIcon"
                />
                <span>Delete</span>
              </b-dropdown-item>
            </b-dropdown>
          </span>
        </span>

                    <!-- Column: Common -->
                    <span v-else>
          {{ props.formattedRow[props.column.field] }}
        </span>
                </template>

                <!-- pagination -->
                <template
                    slot="pagination-bottom"
                    slot-scope="props"
                >
                    <div class="d-flex justify-content-between flex-wrap">
                        <div class="d-flex align-items-center mb-0 mt-1">
            <span class="text-nowrap ">
              Showing 1 to
            </span>
                            <b-form-select
                                v-model="pageLength"
                                :options="['3','5','10']"
                                class="mx-1"
                                @input="(value)=>props.perPageChanged({currentPerPage:value})"
                            />
                            <span class="text-nowrap"> of {{ props.total }} entries </span>
                        </div>
                        <div>
                            <b-pagination
                                :per-page="pageLength"
                                :total-rows="props.total"
                                :value="1"
                                align="right"
                                class="mt-1 mb-0"
                                first-number
                                last-number
                                next-class="next-item"
                                prev-class="prev-item"
                                @input="(value)=>props.pageChanged({currentPage:value})"
                            >
                                <template #prev-text>
                                    <feather-icon
                                        icon="ChevronLeftIcon"
                                        size="18"
                                    />
                                </template>
                                <template #next-text>
                                    <feather-icon
                                        icon="ChevronRightIcon"
                                        size="18"
                                    />
                                </template>
                            </b-pagination>
                        </div>
                    </div>
                </template>
            </vue-good-table>
        </b-modal>
        <b-modal
            id="switchbox"
            centered
            hide-backdrop
            hide-footer
            no-close-on-backdrop
            no-close-on-esc
            size="lg"
            title="Inactive Reason"
        >
            Job ID # {{ inactive.id }}
            <div>
                <v-select
                    v-model="inactive.inactiveReason"
                    :options="inactiveReason"
                    :reduce="item=>item.id"
                    label="name"
                    placeholder="Select Inactive Reason"
                />
                <!-- submit button -->
                <div class="float-right">
                    <b-col>
                        <b-button
                            v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                            variant="outline-danger"
                            @click="hideModal"
                        >Close
                        </b-button>

                        <b-button
                            v-if="inactive.id"
                            type="submit"
                            variant="primary"
                            @click.prevent="inactiveForm"
                        >
                            Submit
                        </b-button>
                    </b-col>
                </div>
            </div>

            <!-- table -->
        </b-modal>
    </b-card-code>
</template>

<script>
import { VueGoodTable } from 'vue-good-table'
import BCardCode from '@core/components/b-card-code/BCardCode.vue'
import TotalResults from '@/views/elements/TotalResults'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'

import {
    BAvatar,
    BBadge,
    BButton,
    BCol,
    BDropdown,
    BDropdownItem,
    BForm,
    BFormCheckbox,
    BFormGroup,
    BFormInput,
    BFormSelect,
    BIcon,
    BModal,
    BPagination,
    BRow, BSpinner,
    VBModal,
} from 'bootstrap-vue'

import 'ag-grid-community/styles/ag-grid.css'
import 'ag-grid-community/styles/ag-theme-alpine.css'
import { AgGridVue } from 'ag-grid-vue'
import ViewPrivateJobs from '@/views/elements/ViewNotification.vue'
import Treeselect from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'
import DateRangePicker from 'vue2-daterange-picker'
//you need to import the CSS manually
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'

export default {
    props: ['title', 'table', 'page', 'filterTable'],
    components: {
        BSpinner,
        BIcon,
        BButton,
        BRow,
        BCol,
        BModal,
        VBModal,
        BForm,
        BCardCode,
        TotalResults,
        BAvatar,
        BBadge,
        BPagination,
        BFormGroup,
        BFormInput,
        BFormSelect,
        BDropdown,
        BDropdownItem,
        vSelect,
        AgGridVue,
        ViewPrivateJobs,
        Treeselect,
        VueGoodTable,
        DateRangePicker,
        imageComponent: {
            template: '<img style="width: 100px" :src="this.params.data.img">',
        },
        actionsComponent: {
            template: `
                <span>
                        <b-button variant="warning" @click="openModel(params.data.id)">View</b-button>
                        <b-button variant="success"
                                  @click="sendNoti(params.data.id)"
                        >Send</b-button>

                        <b-button variant="primary"
                                  target="_blank"
                                  :href="linkURL+\'save/notification/?id='+params.data.id"
                        >Edit</b-button>
                        <!--                        <b-button variant="danger"-->
                        <!--                                  @click="callDeleteMethod(\'/deleteJobs\', {table:params.api.table,id:params.data.id}, \'post\')">Delete</b-button>-->

              <div>
                  <b-button variant="danger"
                            @click="callDeleteMethod(\'/deleteNotification\', {table:params.api.table,id:params.data.id}, \'post\')"
                  >Delete</b-button>
              </div>
                    </span>`,
            components: {
                ViewPrivateJobs,
                BButton,
                BModal,
                VBModal,
                BFormCheckbox,
            },
            data() {
                return {
                    linkURL: null,
                    idVariable: null,
                }
            },
            mounted() {
                console.log(location.hostname)
                if (location.hostname == 'localhost') {
                    this.linkURL = '/'
                } else {
                    this.linkURL = '/smartCalendar/'
                }

                if (this.params.api.table == 'jobs') {
                    this.idVariable = 'id'
                } else {
                    this.idVariable = 'draft_id'
                }
                // console.log(this.params)
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
                sendNoti(value) {
                    this.$root.$emit('sendNoti()', value)

                },
                callDeleteMethod(url, dataa, requestMethod) {
                    // console.log(url, data, requestMethod)
                    this.$root.$emit('callDeleteMethod()', url, dataa, requestMethod)
                },
                switchbox(id) {
                    // console.log(id)
                    this.$root.$emit('switchbox()', id)
                },
                switchboxInvert(id) {
                    // console.log(id)
                    this.$root.$emit('switchboxInvert()', id)
                },
            }
        },
        actionsInby: {
            template: `
                <span>
                        {{ cratedName }}
                    </span>`,
            components: {
                ViewPrivateJobs,
                BButton,
                BModal,
                VBModal,
                BFormCheckbox,
            },
            data() {
                return {
                    linkURL: null,
                    idVariable: null,
                    cratedName: '',
                }
            },

            created() {

                let userArraylist = this.params.api.users
                let inbyid = this.params.data.inby
                console.log(userArraylist)
                console.log('bala')
                if (inbyid) {
                    this.cratedName = userArraylist.find(food => food.id === inbyid).title

                } else {
                    this.cratedName = '-'
                }
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
                callDeleteMethod(url, dataa, requestMethod) {
                    // console.log(url, data, requestMethod)
                    this.$root.$emit('callDeleteMethod()', url, dataa, requestMethod)
                },
                switchbox(id) {
                    // console.log(id)
                    this.$root.$emit('switchbox()', id)
                },
                switchboxInvert(id) {
                    // console.log(id)
                    this.$root.$emit('switchboxInvert()', id)
                },
            }
        },
        actionsexcutive: {
            template: `
                <span>
                        {{ cratedName }}
                    </span>`,
            components: {
                ViewPrivateJobs,
                BButton,
                BModal,
                VBModal,
                BFormCheckbox,
            },
            data() {
                return {
                    linkURL: null,
                    idVariable: null,
                    cratedName: '',
                }
            },

            created() {

                let userArraylist = this.params.api.users
                let inbyid = this.params.data.excutive
                if (inbyid) {
                    this.cratedName = userArraylist.find(food => food.id === inbyid).title
                } else {
                    this.cratedName = '-'
                }
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
                callDeleteMethod(url, dataa, requestMethod) {
                    // console.log(url, data, requestMethod)
                    this.$root.$emit('callDeleteMethod()', url, dataa, requestMethod)
                },
                switchbox(id) {
                    // console.log(id)
                    this.$root.$emit('switchbox()', id)
                },
                switchboxInvert(id) {
                    // console.log(id)
                    this.$root.$emit('switchboxInvert()', id)
                },
            }
        },
        actionsUpby: {
            template: `
                <span>
                        {{ cratedName }}
                    </span>`,
            components: {
                ViewPrivateJobs,
                BButton,
                BModal,
                VBModal,
                BFormCheckbox,
            },
            data() {
                return {
                    linkURL: null,
                    idVariable: null,
                    cratedName: '',
                }
            },

            created() {

                let userArraylist = this.params.api.users
                let inbyid = this.params.data.upby
                if (inbyid) {
                    this.cratedName = userArraylist.find(food => food.id === inbyid).title
                } else {
                    this.cratedName = '-'
                }
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
                callDeleteMethod(url, dataa, requestMethod) {
                    // console.log(url, data, requestMethod)
                    this.$root.$emit('callDeleteMethod()', url, dataa, requestMethod)
                },
                switchbox(id) {
                    // console.log(id)
                    this.$root.$emit('switchbox()', id)
                },
                switchboxInvert(id) {
                    // console.log(id)
                    this.$root.$emit('switchboxInvert()', id)
                },
            }
        },
        actionsGender: {
            template: `
                <span>
                        {{ cratedName }}
                    </span>`,
            components: {
                ViewPrivateJobs,
                BButton,
                BModal,
                VBModal,
                BFormCheckbox,
            },
            data() {
                return {
                    linkURL: null,
                    idVariable: null,
                    cratedName: '',
                }
            },

            created() {

                let inbyid = this.params.data.gender
                if (inbyid == 1) {
                    this.cratedName = 'Female'
                } else if (inbyid == 0) {
                    this.cratedName = 'Male'
                } else {
                    this.cratedName = 'ALL'
                }
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
                callDeleteMethod(url, dataa, requestMethod) {
                    // console.log(url, data, requestMethod)
                    this.$root.$emit('callDeleteMethod()', url, dataa, requestMethod)
                },
                switchbox(id) {
                    // console.log(id)
                    this.$root.$emit('switchbox()', id)
                },
                switchboxInvert(id) {
                    // console.log(id)
                    this.$root.$emit('switchboxInvert()', id)
                },
            }
        },

        viewCountComponent: {
            template: '<button v-if="params.api.table==\'jobs\'" class="btn btn-danger" @click="openModelViewCount(params.data.id, params.data.jobtitle)" style="position: relative;" >View</button>',

            methods: {

                openModelViewCount(value, title) {
                    var rowNode = this.params.node.rowIndex
                    this.$root.$emit('openModelView()', value, title)
                },
            }
        },
        paymentStatusComponent: {

            template: `
                <span
                    v-if="params.data.paid_post==1"
                    class="text-success">
                            {{ this.cratedName }} ({{ params.data.txn_amt }})<br>
                            {{ this.paid_date }}
                        </span>
                <span v-else class="text-danger">{{ this.cratedName }}</span>
            `,
            data() {
                return {
                    cratedName: '',
                    paid_date: '',
                }
            },
            created() {
                var txn_amt = (this.params.data.txn_amt) != '' ? this.params.data.txn_amt : 0
                var paid_post = this.params.data.paid_post
                this.paid_date = new Date(this.params.data.paid_date).toLocaleDateString('en-IN')

                if (paid_post === 1) {
                    this.cratedName = 'Paid'
                } else if (parseInt(txn_amt) > 0) {
                    this.cratedName = 'Free'
                } else {
                    this.cratedName = 'Not_Paid'
                }
            }

        },
        proofComponent: {

            template: '<span v-if="params.data.company_proof_type != 0 && params.data.id_proof_type != 0 && params.data.person_proof_type != 0 && params.data.is_com_add_proof === 1 && params.data.is_com_id_proof === 1 && params.data.is_person_proof === 1" class="text-success" > verified</span><span v-else class="text-danger">Not verified</span>',
        },
    },
    directives: {
        Ripple,
    },

    data() {
        return {

            data: {
                table: this.table,
                from_admin: null,
                shown: '',
                fromArea: '',
                filterField: '',
                jobtitle_id: '',
                joblocation: [],
                candidateLocation: [],
                postedDate: {
                    'startDate': '',
                    'endDate': ''
                },
                expiryDate: {
                    'startDate': '',
                    'endDate': ''
                },
            },
            initial_loading: false,
            variants: ['light', 'primary', 'secondary', 'danger', 'warning', 'success', 'info', 'dark'],
            inactive: {
                id: '',
                inactiveReason: '',
            },
            active: {
                id: '',
            },
            gridApi: null,
            columnApi: null,
            columnDefs: null,
            rowData: null,
            overlayLoadingTemplate: null,
            overlayNoRowsTemplate: null,
            totalData: {
                'current_page': 1,
                'total': 1,
            },
            defaultColDef: null,
            last_url: null,
            searchTerm: '',
            search_text: '',
            total: '',
            model_id: '',
            rowsView: [],
            areasArray: [],
            skilsArray: [],
            jobsourceArray: [],
            courseArray: [],
            jobscatArray: [],
            radiesArray: [],
            distArray: [],
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
            selectField: [
                {
                    id: 1,
                    title: 'Job'
                },
                {
                    id: 2,
                    title: 'Number'
                },
                {
                    id: 3,
                    title: 'Address'
                },
                {
                    id: 4,
                    title: 'Email'
                },
                {
                    id: 5,
                    title: 'Company'
                },
            ],
            selectArea: [],
            inactiveReason: [],
            jobtitle_options: [],
            selectedIds: [],
            jobTitle: '',
            dateRangeConf: {
                opens: 'center',
                minDate: 'center',
                maxDate: 'center',
                singleDatePicker: false,
                timePicker: false,
                timePicker24Hour: false,
                showWeekNumbers: false,
                showDropdowns: false,
                autoApply: false,
            },
            appNameArray: [],
            notiTypeArray: [],
            msgTypeArray: [],
        }
    },
    async mounted() {
        this.$root.$on('openModel()', (data) => {
            this.openModel(data)
        })

        this.$root.$on('sendNoti()', (data) => {
            this.sendNoti(data)
        })

        this.$root.$on('openModelView()', (data, title) => {
            this.openModelView(data, title)
        })

        this.$root.$on('callDeleteMethod()', (url, data, requestMethod) => {
            this.callDeleteMethod(url, data, requestMethod)
        })

        this.$root.$on('switchbox()', (id) => {
            this.switchbox(id)
        })
        this.$root.$on('switchboxInvert()', (id) => {
            this.switchboxInvert(id)
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
    async created() {

        const masterRequest = await this.callAxios('/getMaster', {}, 'post')
        this.notiTypeArray = masterRequest.data.notiType
        this.msgTypeArray = masterRequest.data.msgType
        this.appNameArray = masterRequest.data.appMaster
        this.vcodeArray = masterRequest.data.vcode

        this.radiesArray = {
            'notiTypeArray': this.notiTypeArray,
            'msgTypeArray': this.msgTypeArray,
            'appNameArray': this.appNameArray,
            'vcodeArray': this.vcodeArray,
        }

        // console.log(this.userArray)
        // console.log("123" + process.env.MIX_REQUEST_BASE_URL+"456")

        this.overlayLoadingTemplate =
            '<span style="padding: 10px; border: 2px solid #444; background: lightgoldenrodyellow;" class="ag-overlay-loading-center">Please wait while your rows are loading</span>'
        this.overlayNoRowsTemplate =
            '<span style="padding: 10px; border: 2px solid #444; background: lightgoldenrodyellow;">No rows</span>'
        this.last_url = window.location.pathname.split('/')
            .pop()
        // this.$bvModal.show('modal-footer-lg')
        console.log(this.last_url)
        switch (this.last_url) {
            case 'sendNotification':
                this.data.shown = '2'
                break
            case 'notification':
                this.data.fromArea = 1
                break

        }
        this.columnDefs = [
            {
                headerName: 'S.No',
                valueGetter: 'node.rowIndex + 1',
                width: 90,
            },
            {
                headerName: '#ID',
                field: 'id',
                width: 90,
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
                headerName: 'Title',
                field: 'title',
                width: 150,
            },

            {
                headerName: 'Image',
                cellRendererSelector: params => {
                    if (params.data.img && params.data.img != 'undefined') {
                        return {
                            component: 'imageComponent',
                        }
                    }
                },
                autoHeight: true,
                width: 140,
            },
            {
                headerName: 'APP NAME',
                field: 'app_name',
                width: 200,
            },
            {
                headerName: 'Notification Type',
                field: 'noti',
                width: 200,
            },
            {
                headerName: 'Send Type',
                field: 'sendType',
                width: 250,
            },

            {
                headerName: 'Send Date',
                field: 'sendDate',
                width: 90,
            },

            {
                headerName: 'Added Date',
                field: 'addedDate',
                width: 90,
            },

        ]
        this.defaultColDef = {
            sortable: true,
            filter: true,
            flex: 0,
            resizable: true,
            editable: true,
        }
        this.getNotification()
    },
    methods: {
        printNode(node, index) {
            this.selectedIds.push(node.data.id)
            // alert()
        },

        async onGridReady(params) {
            // const callgetAllUser = await this.callAxios('/getAllUser', {}, 'post');
            // // console.log(callgetAllUser)
            // this.userArray = callgetAllUser.data.users
            // params.api.users = this.userArray
            params.api.hostname = location.hostname
            params.api.MIX_REQUEST_BASE_URL = process.env.MIX_REQUEST_BASE_URL
            params.api.table = this.data.table
            params.api.showNoRowsOverlay()
            // params.users='pandiya'
            console.log(params)
            this.gridApi = params.api
            // console.log(this.gridApi);
            this.gridColumnApi = params.columnApi
        },

        async getNotification(page = 1) {
            console.log(this.data)
            if (this.gridApi) {
                this.gridApi.showLoadingOverlay()
            }
            this.total = 'Loading...'
            // alert(page)

            // console.log(this.data)
            const callAxios = await this.callAxios('/getNotification?page=' + page, this.data, 'post')
            // var return_data = callAxios.data[0];
            if (callAxios.status === 200) {
                this.totalData = callAxios.data
                this.total = this.totalData.total
                this.rowData = callAxios.data.data
            } else {
                // console.log(callAxios.response.data.message);
                // this.toast("Error", callAxios.response.data.message, 'danger');
                this.sweetAlertmethod('error', 'Error', callAxios.response.data.message, 'btn-primary')
            }

            // console.log(selectedNodes);
        },
        onFilterTextBoxChanged() {
            // console.log(document.getElementById('searchTerm').value)
            this.gridApi.setQuickFilter(
                document.getElementById('searchTerm').value
            )
        },
        onBtnExport() {
            const params = {
                suppressQuotes: false,
            }
            this.gridApi.exportDataAsCsv(params)
        },
        open() {
            // alert();
            // this.model_id = '1'
            // this.$bvModal.show('modal-footer-lg')
            // this.$refs['view-jobs-modal'].show();
        },
        openModel(value) {
            // alert("123" + value);
            this.model_id = value
        },
        async sendNoti(value) {
            this.initial_loading = true
            // alert('123 pandiya' + value)
            // this.model_id = value
            const callAxios = await this.callAxios('/sendNotification', {
                id: value,
                fromArea: this.data.fromArea,
                userid: JSON.parse(localStorage.getItem('userData')).id
            }, 'post')

            console.log(callAxios)
            if (callAxios.status === 200) {
                this.sweetAlertmethod('success', 'notification Sent Successfully!', '', 'btn-success')
                this.totalData = callAxios.data
                this.total = this.totalData.total
                this.rowData = callAxios.data.data
                this.initial_loading = false

            } else {
                // console.log(callAxios.response.data.message);
                // this.toast("Error", callAxios.response.data.message, 'danger');
                this.sweetAlertmethod('error', 'Error', callAxios.response.data.message, 'btn-primary')
            }
        },
        async openModelView(value, title) {
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
                this.jobTitle = 'ID : ' + value + ' - ' + title
            } else {
                this.rowsView = ''
            }
        },
        callDeleteMethod(url, data, requestMethod) {
            // console.log("123", url, data, requestMethod)
            this.sweetAlertDeleteMethod(url, data, requestMethod, 'ag-grid')
        },
        switchbox(id) {
            this.inactive.id = id
            // console.log("123", url, data, requestMethod)
            this.$bvModal.show('switchbox')
        },
        async switchboxInvert(id) {
            this.active.id = id
            const callAxios = await this.callAxios('/activeJobs', this.active, 'post')
            if (callAxios.status === 200) {
                this.sweetAlertmethod('success', 'Activated!', '', 'btn-success')
            } else {
                this.sweetAlertmethod('warning', 'Warning!', callAxios.response.data.message, 'btn-success')
                console.log('1')
            }
        },
        hideModal() {
            // alert()
            this.$bvModal.hide('switchbox')
            this.inactive = {
                id: '',
                inactiveReason: '',
            }
        },
        async inactiveForm() {
            console.log(this.rowData)
            if (this.inactive.id && this.inactive.inactiveReason) {
                this.sweetAlertDeleteMethod('/inactiveJobs', this.inactive, 'post', 'ag-grid')
                this.$bvModal.hide('switchbox')
                this.inactive = {
                    id: '',
                    inactiveReason: '',
                }
            } else {
                this.sweetAlertmethod('warning', 'Warning!', 'Select Inactive Reason', 'btn-success')
            }
        },
        clearFields(dateField) {
            switch (dateField) {
                case 'postedDate':
                    this.data.postedDate.startDate = ''
                    this.data.postedDate.endDate = ''
                    break
                case 'expiryDate':
                    this.data.expiryDate.startDate = ''
                    this.data.expiryDate.endDate = ''
                    break
            }
        },
        deselectAlljoblocation() {
            this.data.joblocation = null
        },
        deselectAllcandidateLocation() {
            this.data.candidateLocation = null
        },
    },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
@import '~@resources/scss/vue/libs/vue-good-table.scss';
</style>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
