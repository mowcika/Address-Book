<template>
    <b-card-code title="Field Sales Executive Report">
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


        <b-row>
            <b-col cols="3">
                <total-results :countText="getTableData.length"></total-results>
                <!--                <h4>Total Rows : {{totalRows}}</h4>-->
            </b-col>

            <b-col cols="3">
                <b-form-group>
                    <validation-provider
                        #default="{ errors }"
                        name="FromDate"
                        rules="required|lessthan:@toDate,date"
                    >
                        <flat-pickr
                            v-model="data.fromDate"
                            :config="datePickerConfig"
                            :disabled=disableStartEnddate
                            class="form-control"
                            placeholder="Select From date"
                            @input="getDetails"
                        />
                        <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                </b-form-group>
            </b-col>

            <b-col cols="3">
                <b-form-group>
                    <validation-provider
                        #default="{ errors }"
                        name="ToDate"
                        rules="required|greaterthan:@fromDate,date"
                    >
                        <flat-pickr
                            v-model="data.toDate"
                            :config="datePickerConfig"
                            :disabled=disableStartEnddate
                            class="form-control"
                            placeholder="Select To date"
                            @input="getDetails"
                        />
                        <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                </b-form-group>
            </b-col>
            <b-col cols="3">
                <span>
                <button
                    @click="onBtnExport()"
                >
                <img src="@/assets/images/icons/xls.png" width="50">
                </button>
                </span>
            </b-col>
        </b-row>

        <ag-grid-vue
            :columnDefs="columnDefs"
            :rowData="rowData"
            class="ag-theme-alpine"
            @grid-ready="onGridReady"
            :defaultColDef="defaultColDef"
            animateRows="true"
            rowSelection="multiple"
            style="height: 500px"
        >
        </ag-grid-vue>

        <!-- followup history -->
        <b-modal
            id="modal-followupHistory"
            cancel-variant="outline-secondary"
            centered
            no-close-on-backdrop
            size="lg"
            title="Followup History"
        >
            <div class="text-primary p-1" style="font-weight:bold;">
                Total Rows : {{ followupHistoryData_length }}
            </div>
            <table class="table-bordered table table-striped">
                <thead>
                <tr>
                    <th>Followup Date</th>
                    <th>Company Name</th>
                    <th>Last Followup Status</th>
                    <th>Followup Remarks</th>
                    <th>Location</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="fh in followupHistoryData">
                    <td>
                        <span v-if="fh.lastFollowRemarks == 'Follow'" class="bg-success"
                              style="color:#fff;padding:5px;"
                        >
                            {{ fh.lastFollowDate }}
                        </span>
                        <span v-else>
                            {{ fh.lastFollowDate }}
                        </span>

                    </td>
                    <td>{{ fh.companyName }}</td>
                    <td>{{ fh.lastFollowupstatus }}</td>
                    <td>
                        <span v-if="fh.lastFollowRemarks == 'No Vaccancy'" class="bg-warning"
                              style="color:#fff;padding:5px;"
                        >
                            {{ fh.lastFollowRemarks }}
                        </span>
                        <span v-else>
                            {{ fh.lastFollowRemarks }}
                        </span>
                    </td>
                    <td>
                        <a :href="'https://maps.google.com/?q=' + fh.latlongs" target="blank"> View Location </a>

                    </td>
                </tr>
                </tbody>
            </table>

        </b-modal>
        <!-- followup history /. -->
    </b-card-code>

</template>

<script>
import BCardCode from '@core/components/b-card-code'
import TotalResults from '@/views/elements/TotalResults'
import { ValidationProvider, ValidationObserver, extend } from 'vee-validate'
import {
    BSpinner,
    BFormInput,
    BFormGroup,
    BForm,
    BRow,
    BCol,
    BButton,
    BFormFile,
    BFormSelect,
    BFormTextarea,
    BFormCheckbox,
    BTable,
    BCardBody,
    BFormRadio,
    BFormRadioGroup,
} from 'bootstrap-vue'
import {
    required, between, alpha, integer, password, min, digits, alphaDash, length, max,
} from '@validations'
import vSelect from 'vue-select'
import Ripple from 'vue-ripple-directive'

import { AgGridVue } from 'ag-grid-vue'
import 'ag-grid-community/styles/ag-grid.css' // Core grid CSS, always needed
import 'ag-grid-community/styles/ag-theme-alpine.css' // Optional theme CSS

import flatPickr from 'vue-flatpickr-component'
import { required_if } from 'vee-validate/dist/rules'

extend('required_if', required_if)

extend('lessthan', {
    params: ['toValue', 'type'],
    validate(value, {
        toValue,
        type
    }) {
        if (toValue) {
            console.log(type + ' typed value = ' + value + ' lessthan' + toValue)
            if (type === 'date') {
                return new Date(value) <= new Date(toValue)
            } else {
                return Number(value) <= Number(toValue)
            }
        } else {
            return true
        }
    },
    message: 'Invalid Range'
})

extend('greaterthan', {
    params: ['fromValue', 'type'],
    validate(value, {
        fromValue,
        type
    }) {
        if (fromValue) {
            console.log(type + ' typed value = ' + value + ' greaterthan ' + fromValue)
            if (type === 'date') {
                return new Date(value) >= new Date(fromValue)
            } else {
                return Number(value) >= Number(fromValue)
            }
        } else {
            return true
        }
    },
    message: 'Invalid Range'
})

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
        BRow,
        BCol,
        BButton,
        BFormTextarea,
        AgGridVue,
        TotalResults,
        vSelect,
        flatPickr,
        ViewFollowupsComponent: {
            template: '<button class="btn btn-outline-primary btn-sm" @click="btnCellRenderer(params.data.fsid)" style="position: relative;">View Followups</button>',
            methods: {
                async btnCellRenderer(getId) {
                    var rowNode = this.params.node.rowIndex
                    // alert("tableindex : "+rowNode);
                    var localUserId = JSON.parse(localStorage.getItem('userData')).id
                    this.$root.$emit('getFollowupDataEvent()', rowNode, '/getFollowupData', {
                        userid: this.params.data.user_id,
                        lastFollowDat1: this.params.data.lastFollowDat1,
                        // localUserid: localUserId,
                        // isVerify : isVerify
                    }, this.params.data)
                },
            }
        },
        // vSelect,
        // flatPickr,
        // BFormCheckbox,
        // BTable
    },
    setup() {
        // const gridApi = {} // Optional - for accessing Grid's API
        // const onGridReady = (params) => {
        //     params.api.showLoadingOverlay()
        //     gridApi.value = params.api
        // }
        // const rowData = {} // Set rowData to Array of Objects, one Object per Row
        // Each Column Definition results in one Column.

        // const columnDefs = {
        //     value: [
        //         {
        //             headerName: 'S.No',
        //             valueGetter: 'node.rowIndex + 1',
        //             width: 90,
        //         },
        //
        //         // {
        //         //     headerName: '#ID',
        //         //     field: 'id',
        //         //     width: 90,
        //         // },
        //         {
        //             headerName: 'Username',
        //             field: 'username',
        //             width: 190,
        //         }, {
        //             headerName: 'Last Follow Date',
        //             field: 'lastFollowDate',
        //             width: 190,
        //         },
        //         {
        //             headerName: 'Total Count',
        //             field: 'totalCount',
        //             width: 130,
        //         },
        //         {
        //             headerName: 'No vacancy',
        //             field: 'Novacancy',
        //             width: 120,
        //         },
        //         {
        //             headerName: 'Not interested',
        //             field: 'Notinterested',
        //             width: 120,
        //         },
        //         {
        //             headerName: 'Followup',
        //             field: 'Followup',
        //             width: 120,
        //         },
        //         {
        //             headerName: 'Interested to Post',
        //             field: 'InterestedtoPost',
        //             width: 120,
        //         },
        //         {
        //             headerName: 'History',
        //             cellRendererSelector: params => {
        //                 return {
        //                     component: 'ViewFollowupsComponent'
        //                 }
        //             },
        //             cellStyle: {'white-space': 'normal'},
        //             autoHeight: true,
        //         },
        //
        //     ],
        // }
        // DefaultColDef sets props common to all Columns
    },
    directives: {
        Ripple,
    },
    data() {
        return {
            data: {
                editId: '',
                EditfileUpload: '',
                totalRows: '',
                verifyStatus: '',
                isVerify: '',
                userid: '',
                Vcontent: '',
                empORadmin: '',
                fromDate: '',
                toDate: '',
            },
            followupHistoryData_length: '',
            disableStartEnddate: false,
            datePickerConfig: {
                altFormat: 'd - M - Y',
                altInput: true,
                dateFormat: 'Y-m-d',
            },
            gridApi: null,
            columnApi: null,
            columnDefs: null,
            rowData: null,
            defaultColDef: null,
            getTableData: [],
            getTableDataSearch: [],
            editData: [],
            followupHistoryData: [],
            initial_loading: true,
            variants: ['light', 'primary', 'secondary', 'danger', 'warning', 'success', 'info', 'dark'],
            maxChar500: 500,
            maxChar1000: 1000,
            required,
            min,
            max,
            agCommonSearchBox: '',
        }
    },
    created() {
        this.getDetails()

        this.columnDefs = [
            {
                headerName: 'S.No',
                valueGetter: 'node.rowIndex + 1',
                width: 90,
                sortable: true,
                filter: true,
            },
            // {
            //     headerName: '#ID',
            //     field: 'id',
            //     width: 90,
            // },
            {
                headerName: 'Username',
                field: 'username',
                width: 190,
                sortable: true,
                filter: true,
            }, {
                headerName: 'Last Follow Date',
                field: 'lastFollowDate',
                width: 190,
                sortable: true,
                filter: true,
            },
            {
                headerName: 'Total Count',
                field: 'totalCount',
                width: 130,
                sortable: true,
                filter: true,
            },
            {
                headerName: 'No vacancy',
                field: 'Novacancy',
                width: 120,
                sortable: true,
                filter: true,
            },
            {
                headerName: 'Not interested',
                field: 'Notinterested',
                width: 120,
                sortable: true,
                filter: true,
            },
            {
                headerName: 'Followup',
                field: 'Followup',
                width: 120,
                sortable: true,
                filter: true,
            },
            {
                headerName: 'Interested to Post',
                field: 'InterestedtoPost',
                width: 120,
                sortable: true,
                filter: true,
            },
            {
                headerName: 'History',
                cellRendererSelector: params => {
                    return {
                        component: 'ViewFollowupsComponent'
                    }
                },
                cellStyle: { 'white-space': 'normal' },
                autoHeight: true,
            },
        ]

    },
    async mounted() {
        this.$root.$on('getFollowupDataEvent()', (index, url, data, dataJson) => {
            this.getFollowupDataEventRenderer1(index, url, data, dataJson)
        })

        // this.props.
    },
    methods: {
        onGridReady(params) {
            this.gridApi = params.api
            console.log(this.gridApi)
            this.gridColumnApi = params.columnApi
        },
        onBtnExport() {
            const params = {
                suppressQuotes: false,
            }
            this.gridApi.exportDataAsCsv(params)
        },
        async getDetails() {
            this.total = 'Loading...'
            this.initial_loading = false
            let res = axios.post('/getFseReportData', this.data)
                // this.initial_loading = false
                .then(res => {
                    console.log(res.data.fseReportData)
                    this.getTableData = res.data.fseReportData
                    this.rowData = res.data.fseReportData
                })
                .catch(error => {
                    this.errored = true
                })
                .finally(() => {
                    this.initial_loading = true
                })
        },

        async getFollowupDataEventRenderer1(index, url, data, dataJson) {
            this.initial_loading = false
            const callAxios = await this.callAxios(url, data, 'post')
            console.log('bala : ' + callAxios.data.fseFollowupHistory)

            if (callAxios.status === 200) {
                if (url == '/getFollowupData') {
                    this.$bvModal.show('modal-followupHistory')
                    this.followupHistoryData = callAxios.data.fseFollowupHistory
                    this.followupHistoryData_length = this.followupHistoryData.length
                    // this.data.editId = this.verifyData[0].id
                    // this.data.isVerify = this.verifyData[0].isVerify
                    // this.data.Vcontent = this.verifyData[0].content
                    // // alert("bala "+this.data.isVerify);
                    // console.log(this.verifyData[0].isVerify)
                    this.initial_loading = true
                }
            }
        },

    },
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
@import '~@resources/scss/vue/libs/vue-good-table.scss';

@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>

<style>
::selection {
    color: #fff;
    background: #28c76f;
}
</style>
