<template>
    <b-card-code title="Active Job Title With Live Post Counts and Employees Counts">
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


        <b-row class="mb-2">
            <b-col cols="3">
                <total-results :countText="getTableData.length"></total-results>
                <!--                <h4>Total Rows : {{totalRows}}</h4>-->
            </b-col>
            <b-col cols="3">
                <b-form-group>
                    <v-select
                        id="jobtitles"
                        v-model="jobTitle"
                        :clearable="false"
                        :options="jobTitleArray"
                        :reduce="item=>item.id"
                        label="name"
                        multiple
                        placeholder="(Default All Job Titles) "
                        @input="getDetails"
                    />

                </b-form-group>
            </b-col>

            <!--            <b-col cols="3">-->
            <!--                <b-form-group>-->
            <!--                    <validation-provider-->
            <!--                        #default="{ errors }"-->
            <!--                        name="FromDate"-->
            <!--                        rules="required|lessthan:@toDate,date"-->
            <!--                    >-->
            <!--                        <flat-pickr-->
            <!--                            v-model="data.fromDate"-->
            <!--                            :config="datePickerConfig"-->
            <!--                            :disabled=disableStartEnddate-->
            <!--                            class="form-control"-->
            <!--                            placeholder="Select From date"-->
            <!--                            @input="getDetails"-->
            <!--                        />-->
            <!--                        <small class="text-danger">{{ errors[0] }}</small>-->
            <!--                    </validation-provider>-->
            <!--                </b-form-group>-->
            <!--            </b-col>-->
            <!--            <b-col cols="3">-->
            <!--                <b-form-group>-->
            <!--                    <validation-provider-->
            <!--                        #default="{ errors }"-->
            <!--                        name="ToDate"-->
            <!--                        rules="required|greaterthan:@fromDate,date"-->
            <!--                    >-->
            <!--                        <flat-pickr-->
            <!--                            v-model="data.toDate"-->
            <!--                            :config="datePickerConfig"-->
            <!--                            :disabled=disableStartEnddate-->
            <!--                            class="form-control"-->
            <!--                            placeholder="Select To date"-->
            <!--                            @input="getDetails"-->
            <!--                        />-->
            <!--                        <small class="text-danger">{{ errors[0] }}</small>-->
            <!--                    </validation-provider>-->
            <!--                </b-form-group>-->
            <!--            </b-col>-->
            <b-col cols="3">
                <!-- search area -->
                <b-form-group>
                    <b-form-input
                        id="search"
                        v-model="searchWord"
                        placeholder="Enter Here To Search"
                        type="text"
                        @input="searchTable"
                    />
                </b-form-group>
                <!-- search area /.-->
            </b-col>

            <b-col cols="3">
                <span class="pr-1">
                    <button class="btn btn-outline-primary btn-sm">
                        Total Live Jobs : {{ jobsCounts }}
                    </button>
                </span>
                <span>
                <button
                    @click="onBtnExport()"
                >
                <img src="@/assets/images/icons/xls.png" width="50">
                </button>
                </span>
            </b-col>
        </b-row>

        <b-row class="pb-1">
            <b-col cols="3">
                <date-range-picker
                    ref="picker"
                    v-model="dates"
                    :autoApply="dateRangeConf.autoApply"
                    :locale-data="{ firstDay: 1, format: 'dd-mm-yyyy' }"
                    :maxDate="dateRangeConf.maxDate" :minDate="dateRangeConf.minDate"
                    :opens="dateRangeConf.opens"
                    :showDropdowns="dateRangeConf.showDropdowns"
                    :showWeekNumbers="dateRangeConf.showWeekNumbers"
                    :singleDatePicker="dateRangeConf.singleDatePicker"
                    :timePicker="dateRangeConf.timePicker"
                    :timePicker24Hour="dateRangeConf.timePicker24Hour"
                    style="width: 100%;"
                    @update="getDetails"
                >
                </date-range-picker>
                <small class="text-danger">Posted Date Filter</small>
            </b-col>
        </b-row>

        <ag-grid-vue
            :columnDefs="columnDefs"
            :defaultColDef="defaultColDef"
            :rowData="rowData"
            animateRows="true"
            class="ag-theme-alpine"
            rowSelection="multiple"
            style="height: 500px"
            @grid-ready="onGridReady"
        >
        </ag-grid-vue>


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
import DateRangePicker from 'vue2-daterange-picker'
//you need to import the CSS manually
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'

import { AgGridVue } from 'ag-grid-vue'
import 'ag-grid-community/styles/ag-grid.css' // Core grid CSS, always needed
import 'ag-grid-community/styles/ag-theme-alpine.css' // Optional theme CSS

import flatPickr from 'vue-flatpickr-component'
import { required_if } from 'vee-validate/dist/rules'
import moment from 'moment-timezone'

extend('required_if', required_if)

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
        DateRangePicker,
        // vSelect,
        // flatPickr,
        // BFormCheckbox,
        // BTable
    },
    setup() {

    },
    directives: {
        Ripple,
    },
    data() {
        return {
            data: {
                fromDate: '',
                toDate: '',
            },
            searchWord: null,
            jobTitleArray: [],
            jobTitle: [],
            disableStartEnddate: false,
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
            dates: {
                'startDate': '',
                'endDate': ''
            },
            // dates: {
            //     'startDate': moment().startOf('month').format('YYYY-MM-DD'),
            //     'endDate': moment().endOf('month').format('YYYY-MM-DD')
            // },
            // datePickerConfig: {
            //     altFormat: 'd - M - Y',
            //     altInput: true,
            //     dateFormat: 'Y-m-d',
            // },
            gridApi: null,
            columnApi: null,
            columnDefs: null,
            rowData: null,
            defaultColDef: null,
            getTableData: [],
            getTableDataSearch: [],
            editData: [],
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
    async created() {
        this.jobsCounts = 0
        const callAxios = await this.callAxios('/getprivatejobpostmaster', {}, 'post')
        this.jobTitleArray = callAxios.data.searchJobTitle_v1

        this.jobTitleArray.unshift({
            'id': 'all',
            'name': 'All'
        })

        this.getDetails()

        this.columnDefs = [
            {
                headerName: 'S.No',
                valueGetter: 'node.rowIndex + 1',
                width: 90,
                sortable: true,
                filter: true,
            },
            {
                headerName: 'Job Title Id',
                field: 'jobtitle_id',
                width: 130,
                sortable: true,
                filter: true,
                resizable: true,
                editable: true,
            },
            {
                headerName: 'Active Title Name',
                field: 'jtName',
                width: 340,
                sortable: true,
                filter: true,
                resizable: true,
                editable: true,
            },
            {
                headerName: 'Live Post Count',
                field: 'livepost',
                width: 160,
                sortable: true,
                filter: true,
                resizable: true,
                editable: true,
            },
            {
                headerName: 'Employee Count',
                field: 'ct',
                width: 160,
                sortable: true,
                filter: true,
                resizable: true,
                editable: true,
            },
            // {
            //     headerName: 'Job Expiry Date',
            //     field: 'endingDate',
            //     width: 130,
            //     sortable: true,
            //     filter: true,
            //     resizable: true,
            //     editable: true,
            // },
        ]

    },
    async mounted() {

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

            if (this.jobTitle == '') {
                this.jobTitle = ['all']
            } else {
                this.jobTitle.forEach((value, index) => {
                    if (this.jobTitle.length > 1) {
                        if (index == 0) {
                            if (value == 'all') {
                                this.jobTitle.splice(index, 1)
                            }
                        } else {
                            if (value == 'all') {
                                this.jobTitle = []
                                this.jobTitle.push(value)
                            }
                        }
                        // console.log(this.jobTitle)
                        // console.log("bala")
                    }

                })
            }
            this.total = 'Loading...'
            this.initial_loading = false

            const res = await this.callAxios('/getlivepostandempeecounts', {
                jt: this.jobTitle,
                pdate: this.dates
            }, 'post')
            console.log(res.data.getData)
            this.getTableData = res.data.getData
            this.rowData = res.data.getData
            this.initial_loading = true

            if (this.getTableData.length > 0) {
                this.jobsCounts = 0
                this.getTableData.forEach((value, index) => {
                    // console.log(value.livepost)
                    this.jobsCounts += parseInt(value.livepost)
                })
            }
        },

        // search
        async searchTable() {
            var test = this.gridApi.setQuickFilter(
                document.getElementById('search').value
            )
            this.jobsCounts = 0
            this.gridApi.forEachNodeAfterFilter(node => {
                this.jobsCounts += parseInt(node.data.livepost)
            })
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
