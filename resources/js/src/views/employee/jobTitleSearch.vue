<template>
    <b-card-code title="Employee Job Title Search">
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
            <b-col cols="6">
                <b-form-group>
                    <label class="required">Job Title</label>

                    <v-select
                        v-model="data.jobtitle"
                        :options="getjobtitleArray"
                        :reduce="item=>item.id"
                        label="english"
                        multiple
                        placeholder="Select Job Title"
                        @input="getEmployeeDetailsReorts"
                    />

                </b-form-group>
            </b-col>

            <b-col cols="6">
                <b-form-group>
                    <label class="required">District</label>

                    <v-select
                        v-model="data.district"
                        :options="getDistrictArray"
                        :reduce="item=>item.id"
                        label="name"
                        placeholder="Select District"
                    />

                </b-form-group>
            </b-col>

            <b-col cols="6">
                <b-form-group>
                    <label class="required">City</label>
                    <v-select
                        v-model="data.city"
                        :options="getCityArray.filter(function(item){
                                            if(item.dist_id == data.district){
                                            return item
                                            }
                                          })"
                        :reduce="item=>item.id"
                        label="name"
                        placeholder="Select City"
                        @input="getEmployeeDetailsReorts"
                    />
                </b-form-group>
            </b-col>

        </b-row>

        <b-row v-show="getTableDataLength > 0" class="mb-1">
            <b-col cols="4">
                <total-results :countText="getTableData.length"></total-results>
                <!--                <h4>Total Rows : {{totalRows}}</h4>-->
            </b-col>

            <b-col cols="4"></b-col>

            <b-col cols="3"></b-col>
        </b-row>

        <b-row>
            <b-col cols="12">
                <span v-show="getTableData.length > 0">
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
// import 'ag-grid-enterprise'
import { AgGridVue } from 'ag-grid-vue'
import 'ag-grid-community/styles/ag-grid.css' // Core grid CSS, always needed
import 'ag-grid-community/styles/ag-theme-alpine.css' // Optional theme CSS

import flatPickr from 'vue-flatpickr-component'
import { required_if } from 'vee-validate/dist/rules'

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
                    headerName: 'Employee Name',
                    field: 'employeeName',
                    width: 190,
                },
                {
                    headerName: 'Mobile1',
                    field: 'mobile1',
                    width: 190,
                },
                {
                    headerName: 'Mobile2',
                    field: 'mobile2',
                    width: 190,
                },
                {
                    headerName: 'Job Title',
                    field: 'jobtitleName',
                    width: 190,
                },
                {
                    headerName: 'City',
                    field: 'city',
                    width: 190,
                },
                {
                    headerName: 'District',
                    field: 'district',
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
            variants: ['light', 'primary', 'secondary', 'danger', 'warning', 'success', 'info', 'dark'],
            maxChar500: 500,
            maxChar1000: 1000,
            required,
            min,
            max,
            max,
        }
    },
    async created() {
        const callAxios = await this.callAxios('/getDistrictData', {}, 'post')
        this.getDistrictArray = callAxios.data.distData
        this.getCityArray = callAxios.data.cityData

        // get job titles data
        const callAxios_jobtitle = await this.callAxios('/getJobTitlesData', this.data, 'post')
        if (callAxios.status === 200) {
            this.getjobtitleArray = callAxios_jobtitle.data.getJobTitles
        }
    },
    async mounted() {

    },

    methods: {
        // get Employee Details Reorts

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
                    this.getTableData = []
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
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
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
