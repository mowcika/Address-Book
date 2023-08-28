<template>
    <b-card-code title="Government Employer">


        <!-- input search -->
        <div class="custom-search justify-content-end">
            <b-row>
                <b-col md="2">
                    <add-button-icons buttonText="Add New" buttonIcon="PlusIcon"
                                      buttonColor="primary"
                    ></add-button-icons>
                </b-col>
                <b-col md="3">
                    <total-results :countText="total"></total-results>
                </b-col>
                <b-col md="3" class="mt-n2">
                    Export To :
                    <span>
                        <downloadexcel
                            class="btn p-0"
                            :fetch="exportOption"
                            :fields="json_fields"
                            type="xls"
                            name="Govt-Employer.xls"
                            :stringifyLongNum=true
                        >
                            <img width="50" src="@/assets/images/icons/xls.png">
                        </downloadexcel>
                    </span>
                    <span>
                        <img width="35" @click="exportPdf" src="@/assets/images/icons/pdf.png">
                    </span>
                </b-col>
                <b-col md="4">
                    <b-form-group>
                        <div class="d-flex align-items-center">
                            <label class="mr-1">Search</label>
                            <b-form-input
                                v-model="searchTerm"

                                placeholder="Search"
                                type="text"
                                class="d-inline-block"
                            />

                        </div>
                    </b-form-group>
                </b-col>
            </b-row>
        </div>

        <!-- table -->
        <vue-good-table
            ref="myTable"
            :line-numbers="true"
            :columns="columns"
            :rows="rows"
            :rtl="direction"
            :search-options="{
        enabled: true,
        externalQuery: searchTerm }"
            :pagination-options="{
        enabled: true,
        perPage:pageLength
      }"
        >
            <template
                slot="table-row"
                slot-scope="props"
            >


                <!-- Column: Name -->
                <div
                    v-if="props.column.field === 'fullName'"
                    class="text-nowrap"
                >
                    <b-avatar
                        :src="props.row.avatar"
                        class="mx-1"
                    />
                    <span class="text-nowrap">{{ props.row.fullName }}</span>
                </div>

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
                variant="link"
                toggle-class="text-decoration-none"
                no-caret
            >
              <template v-slot:button-content>
                <feather-icon
                    icon="MoreVerticalIcon"
                    size="16"
                    class="text-body align-middle mr-25"
                />
              </template>
              <b-dropdown-item>
                <feather-icon
                    icon="Edit2Icon"
                    class="mr-50"
                />
                <span>Edit</span>
              </b-dropdown-item>
              <b-dropdown-item>
                <feather-icon
                    icon="TrashIcon"
                    class="mr-50"
                />
                <span>Delete</span>
              </b-dropdown-item>
            </b-dropdown>
          </span>
        </span>

                <!-- Column: Image -->
                <span v-else-if="props.column.field === 'image'">
          <span v-if="props.row.image">
              <img width="50px" height="50px" :src="props.row.image"/>
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
            <span class="text-nowrap">
              Showing 1 to
            </span>
                        <b-form-select
                            v-model="pageLength"
                            :options="['3','5','10']"
                            class="mx-1"
                            @input="(value)=>props.perPageChanged({currentPerPage:value})"
                        />
                        <span class="text-nowrap "> of {{ props.total }} entries </span>
                    </div>
                    <div>
                        <b-pagination

                            :value="1"
                            :total-rows="props.total"
                            :per-page="pageLength"
                            first-number
                            last-number
                            align="right"
                            prev-class="prev-item"
                            next-class="next-item"
                            class="mt-1 mb-0"
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
        <b-modal
            id="modal-addForm"
            ref="addForm"
            cancel-variant="outline-secondary"
            hide-footer
            centered
            title="Add Government Employer"
        >
            <validation-observer ref="simpleRules">
                <b-form>
                    <b-form-group
                        label="Company Name"
                        label-for="Company Name"
                    >
                        <validation-provider
                            #default="{ errors }"
                            name="Company Name"
                            rules="required"
                        >
                            <b-form-input
                                v-model="data.companyname"
                                :state="errors.length > 0 ? false:null"
                                placeholder="Company Name"
                            />
                            <small class="text-danger">{{ errors[0] }}</small>
                        </validation-provider>
                    </b-form-group>
                    <b-form-group
                        label="Company Logo"
                        label-for="Company Logo"
                    >
                        <validation-provider
                            name="Company Logo"
                            #default="{ errors }"
                            rules="url"
                        >
                            <b-form-input
                                v-model="data.image"
                                :state="errors.length > 0 ? false:null"
                                type="url"
                                placeholder="Company Logo"
                            />
                            <small class="text-danger">{{ errors[0] }}</small>
                        </validation-provider>
                    </b-form-group>

                    <!-- submit button -->
                    <div class="float-right">
                        <b-col>
                            <b-button
                                @click="hideModal"
                                v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                                variant="outline-danger"
                            >Close
                            </b-button>

                            <b-button
                                variant="primary"
                                type="submit"
                                @click.prevent="validationForm"
                            >
                                Submit
                            </b-button>
                        </b-col>
                    </div>
                </b-form>
            </validation-observer>
        </b-modal>


    </b-card-code>
</template>

<script>
import BCardCode from '@core/components/b-card-code/BCardCode.vue'
import AddButtonIcons from '@/views/elements/AddButtonIcons'
import TotalResults from '@/views/elements/TotalResults'
import Ripple from 'vue-ripple-directive'
import downloadexcel from 'vue-json-excel'
import { jsPDF } from 'jspdf'

const pdfdoc = new jsPDF('landscape')

import {
    BButton,
    BRow,
    BCol,
    BModal,
    VBModal,
    BForm,
    BAvatar,
    BBadge,
    BPagination,
    BFormGroup,
    BFormInput,
    BFormSelect,
    BDropdown,
    BDropdownItem,
} from 'bootstrap-vue'
import {
    required, url,
} from '@validations'

import { VueGoodTable } from 'vue-good-table'
import store from '@/store'
import { codeColumnSearch } from './code'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import Button from '@/views/components/button/Button'

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

export default {
    components: {
        Button,
        BButton,
        BRow,
        BCol,
        BModal,
        VBModal,
        BForm,
        BCardCode,
        AddButtonIcons,
        TotalResults,
        VueGoodTable,
        downloadexcel,
        BAvatar,
        BBadge,
        BPagination,
        BFormGroup,
        BFormInput,
        BFormSelect,
        BDropdown,
        BDropdownItem,
        ValidationProvider,
        ValidationObserver,
    },
    directives: {
        Ripple,
    },
    data() {
        return {
            json_fields: {
                '#id': 'id',
                'Company Name': 'company-name',
                'Image': 'image',
            },
            data: {
                companyname: '',
                image: '',
            },
            pageLength: 10,
            dir: false,
            codeColumnSearch,
            columns: [
                {
                    label: 'Action',
                    field: 'action',
                },
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
                    label: 'Company Name',
                    field: 'company-name',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Company Name',
                    },
                },
                {
                    label: 'Company Logo',
                    field: 'image',
                    filterOptions: {
                        enabled: false,
                        placeholder: 'Search Logo',
                    },
                },
            ],
            rows: [],
            searchTerm: '',
            addModel: false,
            emailValue: '',
            name: '',
            required,
            url,
            total: 0,
        }
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
    },
    created() {
        this.total = 'Loading...'
        axios.post('/employer-govt')
            .then(res => {
                // console.log(res.data)
                this.total = res.data.length
                this.rows = res.data
            })
    },
    methods: {
        hideModal() {
            this.$refs['addForm'].hide()
        },
        validationForm() {
            this.$refs.simpleRules.validate()
                .then(async success => {
                    if (success) {
                        // if (this.data.companyname.trim() === '') return this.toast("Required", "Company Name is Required", 'danger')
                        // const callAxios = this.callAxios('/saveGovtEmployer', this.data, 'post');
                        const callAxios = await this.callAxios('/saveGovtEmployer', this.data, 'post')
                        // console.log("00"+callAxios);
                        // console.log(callAxios);
                        // console.log("11");
                        if (callAxios.status === 200) {
                            this.toast('Success', 'Successfully created', 'success')
                            this.rows = callAxios.data
                            this.$refs['addForm'].hide()
                            this.data.companyname = ''
                            this.data.image = ''
                        } else {
                            // console.log(callAxios.response.data.message);
                            this.toast('Error', callAxios.response.data.message, 'danger')
                        }
                    }
                })
        },
        exportOption() {
            // console.log(this.$refs.myTable.filteredRows[0].children);
            return this.$refs.myTable.filteredRows[0].children
        },
        exportPdf() {
            function omit(obj, key) {
                const {
                    [key]: ignore,
                    ...rest
                } = obj
                return rest
            }

            var pdfdata = []
            // console.log(this.$refs.myTable.filteredRows[0].children);
            var table = this.$refs.myTable.filteredRows[0].children
            var validKeys = ['id', 'company-name', 'image']

            Object.keys(table)
                .forEach(function (k) {
                    var pdfdataarray = []
                    // console.log(k + ' - ' + table[k]);
                    pdfdataarray['#ID'] = table[k]['id'].toString()
                    pdfdataarray['Company Name'] = table[k]['company-name']
                    pdfdataarray['Image'] = (table[k]['image'] ? table[k]['image'] : ' ').toString()
                    pdfdata.push(pdfdataarray)
                })

            console.log(pdfdata)
            var headers = createHeaders(['#ID', 'Company Name', 'Image'])

            pdfdoc.table(1, 1, pdfdata, headers, {
                left: 10,
                top: 10,
                bottom: 10,
                width: 170,
                autoSize: false,
                printHeaders: true
            })
            pdfdoc.save('Govt-Employer.pdf')
        },
        startDownload() {
            alert('show loading')
        },
        finishDownload() {
            alert('hide loading')
        },
    },
}
</script>
