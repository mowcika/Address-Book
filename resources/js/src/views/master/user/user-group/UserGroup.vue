<template>
    <b-card-code title="User Group">


        <!-- input search -->
        <div class="custom-search justify-content-end">
            <b-row>
                <b-col md="2">
                    <add-button-icons
                        buttonText="Add New"
                        buttonIcon="PlusIcon"
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
                            name="User-Group.xls"
                            :stringifyLongNum=true
                        >
                            <img width="50" src="@/assets/images/icons/xls.png">
                        </downloadexcel>
                    </span>
                    <span>
                        <img height="45" @click="exportPdf"
                             src="@/assets/images/icons/pdf.png"
                        >
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
            max-height="900px"
            :fixed-header="true"
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


                <!-- Column: Action -->
                <span v-if="props.column.field === 'action'">
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
              <b-dropdown-item @click="showModal(props.row.id)">
                <feather-icon
                    icon="Edit2Icon"
                    class="mr-50"
                />
                <span>Edit</span>
              </b-dropdown-item>
              <b-dropdown-item @click="callDeleteMethod('/deleteUserGroup', data={id:props.row.id}, 'post')">
                <feather-icon
                    icon="TrashIcon"
                    class="mr-50"
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
            no-close-on-esc
            hide-header-close
            no-close-on-backdrop
            id="modal-addForm"
            ref="addForm"
            cancel-variant="outline-secondary"
            hide-footer
            centered
            title="Add User Group"
        >
            <validation-observer ref="simpleRules">
                <b-form>
                    <b-form-group v-if="data.id"
                                  label="Id"
                                  label-for="Id"
                    >
                        <b-form-input readonly
                                      v-model="data.id"
                                      placeholder="ID"
                        >
                        </b-form-input>
                    </b-form-group>
                    <b-form-group
                        label="User Group"
                        label-for="User Group"
                    >
                        <validation-provider
                            #default="{ errors }"
                            name="User Group"
                            rules="required|min:3"
                        >
                            <b-form-input
                                v-model="data.name"
                                :state="errors.length > 0 ? false:null"
                                placeholder="User Group"
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
                                v-if="!data.id"
                                variant="primary"
                                type="submit"
                                @click.prevent="validationForm"
                            >
                                Submit
                            </b-button>
                            <b-button
                                v-else
                                variant="primary"
                                type="submit"
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
    required, url, alpha, alphaDash
} from '@validations'

import { VueGoodTable } from 'vue-good-table'
import store from '@/store'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
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
    data() {
        return {
            json_fields: {
                '#ID': 'id',
                'Name': 'name',
            },
            data: {
                id: '',
                name: '',
            },
            pageLength: 10,
            dir: false,
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
                    label: 'Name',
                    field: 'name',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Group Name',
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
        axios.post('/user-group')
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
                const callAxios = await this.callAxios('/getSingleUserGroup', { id: value }, 'post')
                console.log(callAxios)
                if (callAxios.status === 200) {
                    this.data.name = callAxios.data[0]['name']

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
        validationForm() {
            this.$refs.simpleRules.validate()
                .then(async success => {
                    if (success) {
                        const callAxios = await this.callAxios('/saveUserGroup', this.data, 'post')
                        var desc = this.data.id ? 'Successfully Updated' : 'Successfully created'
                        if (callAxios.status === 200) {
                            this.sweetAlertmethod('success', 'Success', desc, 'btn-primary')
                            // this.toast("Success", "Successfully created", 'success');
                            this.total = callAxios.data.length
                            this.rows = callAxios.data
                            this.$refs['addForm'].hide()
                            this.data = {
                                id: '',
                                name: '',
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
            var validKeys = ['id', 'name']

            Object.keys(table)
                .forEach(function (k) {
                    var pdfdataarray = []
                    // console.log(k + ' - ' + table[k]);
                    pdfdataarray['#ID'] = table[k]['id'].toString()
                    pdfdataarray['Name'] = table[k]['name']
                    pdfdata.push(pdfdataarray)
                })

            console.log(pdfdata)
            var headers = createHeaders(['#ID', 'Name'])

            pdfdoc.table(1, 1, pdfdata, headers, {
                left: 10,
                top: 10,
                bottom: 10,
                width: 170,
                autoSize: false,
                printHeaders: true
            })
            pdfdoc.save('User-Group.pdf')
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
@import '~@resources/scss/vue/libs/vue-good-table.scss';
</style>
