<template>
    <b-card-code title="Msg Type">
        <!-- input search -->
        <div class="custom-search justify-content-end">
            <b-row>
                <b-col md="2">
                    <add-button-icons
                        buttonColor="primary"
                        buttonIcon="PlusIcon"
                        buttonText="Add New"
                    ></add-button-icons>
                </b-col>
                <b-col md="3">
                    <total-results :countText="total"></total-results>
                </b-col>
                <b-col class="mt-n2" md="3">
                    Export To :
                    <span>
                        <downloadexcel
                            :fetch="exportOption"
                            :fields="json_fields"
                            :stringifyLongNum=true
                            class="btn p-0"
                            name="Govt-Employer.xls"
                            type="xls"
                        >
                            <img height="50" src="@/assets/images/icons/xls.png">
                        </downloadexcel>
                    </span>
                    <span>
                        <img height="45" src="@/assets/images/icons/pdf.png"
                             @click="exportPdf"
                        >
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

        <!-- table -->
        <vue-good-table
            ref="myTable"
            :columns="columns"
            :fixed-header="true"
            :line-numbers="true"
            :pagination-options="{
        enabled: true,
        perPage:pageLength
      }"
            :rows="rows"
            :rtl="direction"
            :search-options="{
        enabled: true,
        externalQuery: searchTerm }"
            max-height="900px"
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
              <b-dropdown-item @click="showModal(props.row.id)">
                <feather-icon
                    class="mr-50"
                    icon="Edit2Icon"
                />
                <span>Edit</span>
              </b-dropdown-item>
              <b-dropdown-item @click="callDeleteMethod('/deletemsgType', data={id:props.row.id}, 'post')">
                <feather-icon
                    class="mr-50"
                    icon="TrashIcon"
                />
                <span>Delete</span>
              </b-dropdown-item>
            </b-dropdown>
          </span>
        </span>

                <!-- Column: Image -->
                <span v-else-if="props.column.field === 'image'">
          <span v-if="props.row.image">
              <img :src="props.row.image" height="50px" width="50px"/>
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
                            :options="['10','50','100']"
                            class="mx-1"
                            @input="(value)=>props.perPageChanged({currentPerPage:value})"
                        />
                        <span class="text-nowrap "> of {{ props.total }} entries </span>
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
        <b-modal
            id="modal-addForm"
            ref="addForm"
            cancel-variant="outline-secondary"
            centered
            hide-footer
            hide-header-close
            no-close-on-backdrop
            no-close-on-esc
            title="Msg Type"
        >
            <validation-observer ref="simpleRules">
                <b-form>
                    <b-form-group v-if="data.id"
                                  label="Id"
                                  label-for="Id"
                    >
                        <b-form-input v-model="data.id"
                                      placeholder="ID"
                                      readonly
                        >
                        </b-form-input>
                    </b-form-group>
                    <b-form-group
                        label="Msg Type"
                        label-for="Msg Type"
                    >
                        <validation-provider
                            #default="{ errors }"
                            name="Msg Type"
                            rules="required|min:2"
                        >
                            <b-form-input
                                v-model="data.msgType"
                                :state="errors.length > 0 ? false:null"
                                placeholder="Msg Type"
                            />
                            <small class="text-danger">{{ errors[0] }}</small>
                        </validation-provider>
                    </b-form-group>


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
                                v-if="!data.id"
                                type="submit"
                                variant="primary"
                                @click.prevent="validationForm"
                            >
                                Submit
                            </b-button>
                            <b-button
                                v-else
                                type="submit"
                                variant="primary"
                                @click.prevent="validationForm"
                            >
                                Update
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
import {
    BAvatar,
    BBadge,
    BButton,
    BCol,
    BDropdown,
    BDropdownItem,
    BForm,
    BFormGroup,
    BFormInput,
    BFormSelect,
    BModal,
    BPagination,
    BRow,
    VBModal,
} from 'bootstrap-vue'
import { alpha, alphaDash, required, url } from '@validations'

import { VueGoodTable } from 'vue-good-table'
import store from '@/store'
import { codeColumnSearch } from './code'
import { ValidationObserver, ValidationProvider } from 'vee-validate'
import Button from '@/views/components/button/Button'

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

export default {
    props: ['companyId'],
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
    data: function () {
        return {
            json_fields: {
                '#ID': 'id',
                'Company Name': 'company-name',
                'Image': 'image',
            },
            data: {
                id: '',
                msgType: '',
                msgValue: '',
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
                    label: 'Msg Type',
                    field: 'type',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Msg Type',
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
            alpha,
            alphaDash,
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
        axios.post('/getmsgType')
            .then(res => {
                // console.log(res.data)
                this.total = res.data.length
                this.rows = res.data
            })
    },
    methods: {
        async showModal(value) {
            if (value) {
                this.data.id = value
                const callAxios = await this.callAxios('/getSinglemsgType', { id: value }, 'post')
                console.log(callAxios)
                this.data.msgType = callAxios.data[0]['type']
                this.data.msgValue = callAxios.data[0]['type_value']

                this.$refs['addForm'].show()
            } else {
                this.data = {
                    id: '',
                    msgType: '',
                    msgValue: '',
                }
            }
        },
        hideModal() {
            this.$refs['addForm'].hide()
            this.data = {
                id: '',
                msgType: '',
                msgValue: '',
            }
        },
        validationForm() {
            this.$refs.simpleRules.validate()
                .then(async success => {
                    if (success) {
                        // if (this.data.companyname.trim() === '') return this.toast("Required", "Company Name is Required", 'danger')
                        // const callAxios = this.callAxios('/saveGovtEmployer', this.data, 'post');
                        const callAxios = await this.callAxios('/savemsgType', this.data, 'post')
                        // console.log("00"+callAxios);
                        // console.log(callAxios);
                        // console.log("11");
                        var desc = this.data.id ? 'Successfully Updated' : 'Successfully created'
                        if (callAxios.status === 200) {
                            this.sweetAlertmethod('success', 'Success', desc, 'btn-primary')
                            // this.toast("Success", "Successfully created", 'success');
                            this.total = callAxios.data.length
                            this.rows = callAxios.data
                            this.$refs['addForm'].hide()
                            this.data = {
                                id: '',
                                msgType: '',
                                msgValue: '',
                            }
                        } else {
                            // console.log(callAxios.response.data.message);
                            // this.toast("Error", callAxios.response.data.message, 'danger');
                            this.sweetAlertmethod('error', 'Error', callAxios.response.data.message, 'btn-primary')
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
                    console.log(k + ' - ' + table[k])
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
        callDeleteMethod(url, data, requestMethod) {
            console.log('999' + this.sweetAlertDeleteMethod(url, data, requestMethod))
        }
    },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>
