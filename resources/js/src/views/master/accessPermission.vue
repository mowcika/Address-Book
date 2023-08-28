<template>
    <b-card-code title="Access Permision Master">
        <b-overlay
            :show="initial_loading"
            rounded="lg"
        >
            <!-- input search -->
            <div class="custom-search justify-content-end">
                <b-row>
                    <b-col md="2"/>
                    <b-col md="3">
                        <total-results :count-text="total"/>
                    </b-col>
                    <b-col
                        md="3"
                        class="mt-n2"
                    >
                        Export To :
                        <span>
              <downloadexcel
                  class="btn p-0"
                  :fetch="exportOption"
                  :fields="json_fields"
                  type="xls"
                  name="Menu-Group.xls"
                  :stringify-long-num="true"
              >
                <img
                    width="50"
                    src="@/assets/images/icons/xls.png"
                >
              </downloadexcel>
            </span>
                        <span>
              <img
                  height="45"
                  src="@/assets/images/icons/pdf.png"
                  @click="exportPdf"
              >
            </span>
                    </b-col>
                    <b-col md="4">
                        <b-form-group>
                            <div class="d-flex align-items-center">
                                <label class="mr-1">Search</label>
                                <b-form-input
                                    v-model="searchuserRole"
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
                v-show="!initial_loading"
                ref="myTable"
                :line-numbers="true"
                :columns="columns"
                :rows="rows"
                :rtl="direction"
                max-height="900px"
                :fixed-header="true"
                :search-options="{
          enabled: true,
          externalQuery: searchuserRole }"
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
                <b-dropdown-item @click="showModal(props.row.role_id)">
                  <feather-icon
                      icon="Edit2Icon"
                      class="mr-50"
                  />
                  <span>Edit</span>
                </b-dropdown-item>
              </b-dropdown>
            </span>
            <button
                type="button"
                class="btn btn-success"
                @click="showModal(props.row.role_id)"
            ><feather-icon
                icon="Edit2Icon"
                class="mr-50"
            />Edit</button>
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
                v-show="!initial_loading"
                id="modal-addForm"
                ref="addForm"
                no-close-on-esc
                hide-header-close
                no-close-on-backdrop
                cancel-variant="outline-secondary"
                hide-footer
                centered
                title="Add User Role"
            >
                <validation-observer ref="simpleRules">
                    <b-form>
                        <b-form-group
                            v-if="data.role_id"
                            label="Role Id"
                            label-for="Role Id"
                        >
                            <b-form-input
                                v-model="data.role_id"
                                readonly
                                placeholder="Role ID"
                            />
                        </b-form-group>
                        <b-form-group
                            label="Role"
                            label-for="role"
                        >
                            <b-form-input
                                v-model="data.role"
                                readonly
                                placeholder="Role"
                            />
                        </b-form-group>
                        <b-form-group>
                            <label class="required">Menu Access Permision</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Menu Access"
                            >
                                <treeselect
                                    :value="data.menu_id"
                                    :clearable="true"
                                    value-consists-of="LEAF_PRIORITY"
                                    :default-expand-level="1"
                                    :multiple="true"
                                    :options="getMenuSelect"
                                    placeholder="Select Menu..."
                                    @input="updateValue"
                                >
                                    <div
                                        slot="before-list"
                                        class="d-flex justify-content-center"
                                    >
                                        <button
                                            type="button"
                                            class="btn btn-light"
                                            @click="selectAllMenu()"
                                        >
                                            Select All
                                        </button>
                                        <button
                                            type="button"
                                            @click="deselectAllMenu()"
                                        >Deselect All
                                        </button>
                                    </div>
                                </treeselect>
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
        </b-overlay>
    </b-card-code>
</template>

<script>
import BCardCode from '@core/components/b-card-code/BCardCode.vue'
import AddButtonIcons from '@/views/elements/AddButtonIcons'
import TotalResults from '@/views/elements/TotalResults'
import Ripple from 'vue-ripple-directive'
import downloadexcel from 'vue-json-excel'
import { jsPDF } from 'jspdf'
import vSelect from 'vue-select'

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
    BDropdownItem, BOverlay,
} from 'bootstrap-vue'
import {
    required, url, alpha, alphaDash,
} from '@validations'

import { VueGoodTable } from 'vue-good-table'
import store from '@/store'
import { ValidationProvider, ValidationObserver, extend } from 'vee-validate'
import Treeselect from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'
import Button from '@/views/components/button/Button'

const pdfdoc = new jsPDF('landscape')

function createHeaders(keys) {
    const result = []
    for (let i = 0; i < keys.length; i += 1) {
        result.push({
            id: keys[i],
            name: keys[i],
            prompt: keys[i],
            width: 65,
            align: 'center',
            padding: 0,
        })
    }
    return result
}

export default {
    components: {
        BOverlay,
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
        Treeselect,
        BBadge,
        BPagination,
        BFormGroup,
        BFormInput,
        BFormSelect,
        BDropdown,
        BDropdownItem,
        ValidationProvider,
        ValidationObserver,
        vSelect,
    },
    directives: {
        Ripple,
    },
    data() {
        return {
            initial_loading: true,
            json_fields: {
                // '#ID': 'id',
                Role: 'role',
                Menu: 'menuName',
            },
            data: {
                id: '',
                menu_id: [],
                menuName: [],
                role_id: '',
                role: '',
            },
            pageLength: 10,
            dir: false,
            getMenuSelect: [],
            columns: [
                {
                    label: 'Action',
                    field: 'action',
                },
                {
                    label: 'Role',
                    field: 'role',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Role',
                    },
                },
                {
                    label: 'Menu',
                    field: 'menuName',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Menu',
                    },
                },
            ],
            rows: [],
            allUserRole: [],
            searchuserRole: '',
            addModel: false,
            emailValue: '',
            name: '',
            required,
            url,
            alpha,
            alphaDash,
            total: 0,
            roleSelect: [],
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
        this.getRoleWithAccessPremission()
    },
    methods: {
        updateValue(value) {
            console.log(value)
            this.data.menu_id = value
        },
        async showModal(value) {
            this.initial_loading = true
            if (value) {
                this.data.role_id = value

                const callAxios = await this.callAxios('/getSingleRoleAccess', { id: this.data.role_id }, 'post')
                // console.log(callAxios)
                if (callAxios.status === 200) {
                    this.data.role_id = callAxios.data[0].role_id
                    this.data.role = callAxios.data[0].role
                    const check = callAxios.data[0].menu_id.split(',')
                    // const checked = JSON.stringify(check).replace(/"/g, '');
                    const menuSelectedArray = []
                    check.forEach((value, index) => {
                        menuSelectedArray.push(parseInt(value))
                    })
                    if (menuSelectedArray != 0) {
                        this.data.menu_id = menuSelectedArray
                    }
                    this.$refs.addForm.show()
                    const getMenuSelectData = await this.callAxios('/getMenuSelect', {}, 'post')
                    if (getMenuSelectData.status === 200) {
                        this.initial_loading = false
                        this.getMenuSelect = getMenuSelectData.data
                    }
                    // console.log(JSON.parse(JSON.stringify(this.getMenuSelect)))
                } else {
                    // console.log(callAxios.response.data.message);
                    // this.toast("Error", callAxios.response.data.message, 'danger');
                    this.sweetAlertmethod('error', 'Error', callAxios.response.data.message, 'btn-primary')
                }
            } else {
                this.data = {
                    id: '',
                    menu_id: [],
                    role: '',
                    role_id: '',
                }
            }
        },
        hideModal() {
            this.$refs.addForm.hide()
            this.data = {
                // id: '',
                role_id: '',
                role: '',
                menu_id: [],
            }
        },
        validationForm() {
            this.$refs.simpleRules.validate()
                .then(async success => {
                    if (success) {
                        const localUserId = JSON.parse(localStorage.getItem('userData')).id
                        this.data.cby = localUserId
                        const callAxios = await this.callAxios('/saveAccessPermission', this.data, 'post')
                        const desc = this.data.id ? 'Access Permission Updated Successfully' : 'Access Permission Added Successfully'
                        // console.log(callAxios)
                        const msg = 'success'
                        if (callAxios.status === 200) {
                            this.sweetAlertmethod(msg, msg, desc, 'btn-primary')
                            // this.toast("Success", "Successfully created", 'success');
                            this.total = callAxios.data.check ? callAxios.data.query.length : callAxios.data.length
                            this.rows = callAxios.data.check ? callAxios.data.query : callAxios.data
                            this.$refs.addForm.hide()
                            this.getRoleWithAccessPremission()
                            // this.data = {
                            // id: '',
                            // role_id: '',
                            // role: '',
                            // menu_id: [],
                            // }
                        } else {
                            // console.log(callAxios.response.data.message);
                            // this.toast("Error", callAxios.response.data.message, 'danger');
                            this.sweetAlertmethod('error', 'Error', callAxios.response.data.message, 'btn-primary')
                        }
                    }
                })
        },
        selectAllMenu() {
            const menuArray = []
            // console.log(JSON.parse(JSON.stringify(this.getMenuSelect)))
            this.getMenuSelect.forEach((value, index) => {
                menuArray.push(parseInt(value.id))
                value.children.forEach((val, inx) => {
                    menuArray.push(parseInt(val.id))
                    if (val.children) {
                        val.children.forEach((v, i) => {
                            menuArray.push(parseInt(v.id))
                        })
                    }
                })
            })
            // this.data.menu_id = menuArray.join(',').split(',')
            this.data.menu_id = menuArray
        },
        deselectAllMenu() {
            this.data.menu_id = []
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

            const pdfdata = []
            // console.log(this.$refs.myTable.filteredRows[0].children);
            const table = this.$refs.myTable.filteredRows[0].children
            const validKeys = ['id', 'userRole']

            Object.keys(table)
                .forEach(k => {
                    const pdfdataarray = []
                    // console.log(k + ' - ' + table[k]);
                    pdfdataarray['#ID'] = table[k].id.toString()
                    pdfdataarray.Name = table[k].userTerm
                    pdfdata.push(pdfdataarray)
                })

            // console.log(pdfdata)
            const headers = createHeaders(['#ID', 'userTerm'])

            pdfdoc.table(1, 1, pdfdata, headers, {
                left: 10,
                top: 10,
                bottom: 10,
                width: 170,
                autoSize: false,
                printHeaders: true,
            })
            pdfdoc.save('Menu-Group.pdf')
        },
        startDownload() {
            alert('show loading')
        },
        finishDownload() {
            alert('hide loading')
        },
        async getRoleWithAccessPremission() {
            this.total = 'Loading...'
            await axios.post('/getRoleWithAccessPremission')
                .then(res => {
                    // console.log(res.data)
                    this.total = res.data.length
                    this.initial_loading = false
                    const modifiedArray = res.data.map(item => ({
                        role_id: item.role_id, // Change the key name from 'id' to 'newId'
                        role: item.role,
                        menu_id: item.menu_id,
                        menuName: item.menuName,
                    }))
                    this.rows = modifiedArray
                    this.data.role = res.data.role
                    // this.data.menu_id = res.data.menu_id
                    this.data.menuName = res.data.menuName
                })
        },
    },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
@import '~@resources/scss/vue/libs/vue-good-table.scss';

.modal-dialog {
    max-width: 1000px;
}
</style>
