<template>
    <b-card-code title="Kilometer Based Employee Counts">
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
                    <label class="required">District</label>

                    <v-select
                        v-model="data.district"
                        :options="getDistrictArray"
                        :reduce="item=>item.id"
                        label="name"
                        placeholder="Select District"
                        @input="getCitys"
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
                        @input="getThisCityBasedList"
                    />
                </b-form-group>
            </b-col>

        </b-row>

        <b-row>
            <b-col cols="6">
                <b-form-group>
                    <label class="required">Kilometers</label>

                    <b-form-input
                        id="f-article-title"
                        v-model="data.kiloMeters"
                        placeholder="Enter Kilometers"
                        type="text"
                        @keyup.enter="getEmployeesCount"
                    />
                </b-form-group>
            </b-col>

            <b-col cols="6"></b-col>

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
            <b-col cols="6">
                <span v-show="getTableData.length > 0">
                    <ag-grid-vue
                        ref="myTable"
                        :columnDefs="columnDefs.value"
                        :defaultColDef="defaultColDef"
                        :rowData="getTableData"
                        animateRows="true"
                        class="ag-theme-alpine"
                        rowSelection="multiple"
                        style="height: 500px"
                        @cell-clicked="cellWasClicked"
                        @grid-ready="onGridReady"
                    >
                    </ag-grid-vue>
                    <b-col cols="12" style="background: #ff3a52;box-shadow: 0px 4px 3px #ccc;">
                    <b-row class="mt-2 mb-2">

                            <b-col class="text-right text-primary font-weight-bold" cols="6">  <span
                                style="text-size:14px;color:#fff;"
                            >Total Employees :</span> </b-col>
                            <b-col class="text-left text-success font-weight-bold" cols="6">  <span
                                style="text-size:14px;color:#fff;"
                            >{{ sum_empCounts }}</span> </b-col>
                    </b-row>
                    </b-col>

                </span>
            </b-col>
            <b-col v-if="data.city" cols="6">
                <div class="mb-0 b-table-sticky-header table-responsive">
                    <table class="table table-striped table-responsive table-bordered">
                        <thead style="position:sticky; top: 0 ;">
                        <tr>
                            <th scope="col" style="position: sticky; top:0;">KM</th>
                            <th scope="col" style="position: sticky; top:0;">City</th>
                            <th scope="col" style="position: sticky; top:0;">Employee Counts</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="r in getTableData1">
                            <td>{{ r.kilometerName }}</td>
                            <td>{{ r.cityName }}</td>
                            <td>{{ r.empeCount }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <b-col cols="12" style="background: #ff3a52;box-shadow: 0px 4px 3px #ccc;">
                    <b-row class="mt-2 mb-2">

                        <b-col class="text-right text-primary font-weight-bold" cols="6"><span
                            style="text-size:14px;color:#fff;"
                        >Total Employees :</span></b-col>
                        <b-col class="text-left text-success font-weight-bold" cols="6"><span
                            style="text-size:14px;color:#fff;"
                        >{{ sum_empCounts1 }}</span></b-col>
                    </b-row>
                </b-col>
            </b-col>

        </b-row>

        <!-- followup history -->
        <b-modal
            id="modal-EmployeeList"
            cancel-variant="outline-secondary"
            centered
            no-close-on-backdrop
            size="xl"
            title="Employees List"
        >
            <div class="mb-0 b-table-sticky-header table-responsive">
                <h1 class="text-success text-center">Employee List</h1>
                <p>Total : {{ EmployeeHistoryData.length }}</p>
                <table class="b-table-bordered table table-striped table-responsive">
                    <thead role="rowgroup">
                    <tr role="rowgroup">
                        <th cols="2">Employee Name</th>
                        <th>Mobile 1</th>
                        <th>Mobile 2</th>
                        <th>Email</th>
                        <th>City</th>
                    </tr>
                    </thead>
                    <tbody role="rowgroup">
                    <tr v-for="fh in EmployeeHistoryData" role="rowgroup">
                        <!--                    <td> {{ fh.employeeName }}</td>-->
                        <td> {{ fh.employeeName.substring(0, 50) }}</td>
                        <!--                    <td v-if="iffh.employeeName.length >=25"> {{  fh.employeeName.substring(0,25)+"..." }}</td>-->
                        <td> {{ fh.mobile1 }}</td>
                        <td> {{ fh.mobile2 }}</td>
                        <td> {{ fh.email }}</td>
                        <td> {{ fh.area_name }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

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
// import 'ag-grid-enterprise';
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
        ViewEmployeesComponent: {
            template: '<button class="btn btn-outline-primary btn-sm" @click="btnCellRenderer(params.data.native_location)" style="position: relative;">View All Employees</button>',
            methods: {
                async btnCellRenderer(getId) {
                    var rowNode = this.params.node.rowIndex
                    // alert("tableindex : "+rowNode);
                    var localUserId = JSON.parse(localStorage.getItem('userData')).id
                    this.$root.$emit('getAllEmployeesData()', rowNode, '/getAllCityEmployees', {
                        native_location: this.params.data.native_location,
                        // localUserid: localUserId,
                        // isVerify : isVerify
                    }, this.params.data)
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
                    headerName: 'City Name',
                    field: 'area_name',
                    width: 190,
                }, {
                    headerName: 'Total Count',
                    field: 'employeeCounts',
                    width: 190,
                },
                {
                    headerName: 'View All',
                    cellRendererSelector: params => {
                        return {
                            component: 'ViewEmployeesComponent'
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
                district: '',
                city: '',
                kiloMeters: '',
                native_location_id: '',
            },
            sum_empCounts: '',
            sum_empCounts1: '',
            getTableDataLength: '',
            getDistrictArray: [],
            getCityArray: [],
            disableStartEnddate: false,
            getTableData: [],
            getTableData1: [],
            EmployeeHistoryData: [],
            initial_loading: true,
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
    },
    async mounted() {
        this.$root.$on('getAllEmployeesData()', (index, url, data, dataJson) => {
            this.getAllEmployeesDataRenderer1(index, url, data, dataJson)
        })

        // this.props.
    },
    methods: {
        async getDetails() {
            this.total = 'Loading...'
            this.initial_loading = false
            let res = axios.post('/getFseReportData', this.data)
                // this.initial_loading = false
                .then(res => {
                    console.log(res.data.fseReportData)
                    // this.getTableData = res.data.fseReportData
                })
                .catch(error => {
                    this.errored = true
                })
                .finally(() => {
                    this.initial_loading = true
                })
        },

        // get citys
        async getCitys() {
            this.data.city = ''
            // let res = axios.post('/getDistrictBasedCitys', this.data)
            //     // this.initial_loading = false
            //     .then(res => {
            //         console.log(res.data.cityData)
            //         this.getCityArray = res.data.cityData
            //     })

        },

        async getThisCityBasedList() {
            this.initial_loading = false
            let res = await axios.post('/getCurrentCityBasedList', this.data)
            // this.initial_loading = false
            console.log('bala : ' + res.data)
            if (res.status === 200) {
                if (res.data.empCityLists != undefined || res.data.adStatus != false) {
                    // alert("if : "+asd.data.empCounts);
                    this.initial_loading = true
                    this.getTableData1 = res.data.empCityLists
                    this.sum_empCounts1 = this.getTableData1.reduce((acc, item) => acc + item.empeCount, 0)
                    console.log('sum : ' + this.sum_empCounts1)
                    // this.getTableDataLength = this.getTableData.length;
                } else {
                    // alert("else : "+asd.data.empCounts);
                    this.sweetAlertmethod('warning', 'warning', 'NO Data')
                    this.initial_loading = true
                }
            }
        },

        // get employes counts city based
        async getEmployeesCount() {
            this.initial_loading = false
            const asd = await axios.post('/getEmployeesCounts', this.data)
            if (asd.status === 200) {
                if (asd.data.empCounts != undefined) {
                    // alert("if : "+asd.data.empCounts);
                    this.getTableData = asd.data.empCounts
                    this.getTableDataLength = this.getTableData.length
                    this.sum_empCounts = this.getTableData.reduce((acc, item) => acc + item.employeeCounts, 0)
                    console.log('sum : ' + this.sum_empCounts)
                    this.initial_loading = true
                } else {
                    // alert("else : "+asd.data.empCounts);
                    this.sweetAlertmethod('warning', 'warning', 'NO Data')
                    this.initial_loading = true
                }
            }
            console.log(this.getTableData)
        },

        async getAllEmployeesDataRenderer1(index, url, data, dataJson) {
            this.initial_loading = false
            const callAxios = await this.callAxios(url, data, 'post')
            console.log('bala : ' + callAxios.data.empLists)
            if (callAxios.status === 200) {
                if (url == '/getAllCityEmployees') {
                    // alert("iif");
                    this.$bvModal.show('modal-EmployeeList')
                    this.EmployeeHistoryData = callAxios.data.empLists
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
