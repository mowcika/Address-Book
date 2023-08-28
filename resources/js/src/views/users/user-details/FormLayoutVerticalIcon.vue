<template>
    <b-row>
        <b-col cols="12">
            <b-card-code
                title="Vertical Form With Icons"
            >
                <b-form @submit.prevent>
                    <b-row>

                        <!-- first name -->
                        <b-col cols="12">
                            <b-form-group
                                label="First Name"
                                label-for="vi-first-name"
                            >
                                <b-input-group class="input-group-merge">
                                    <b-input-group-prepend is-text>
                                        <feather-icon icon="UserIcon"/>
                                    </b-input-group-prepend>
                                    <b-form-input
                                        id="vi-first-name"
                                        v-model="data.name"
                                        placeholder="First Name"
                                    />
                                </b-input-group>
                            </b-form-group>
                        </b-col>

                        <!-- first name -->
                        <b-col cols="12">
                            <b-form-group
                                label="User Name"
                                label-for="vi-user-name"
                            >
                                <b-input-group class="input-group-merge">
                                    <b-input-group-prepend is-text>
                                        <feather-icon icon="UserIcon"/>
                                    </b-input-group-prepend>
                                    <b-form-input
                                        id="vi-user-name"
                                        v-model="data.uname"
                                        placeholder="User Name"
                                    />
                                </b-input-group>
                            </b-form-group>
                        </b-col>

                        <!-- mobile -->
                        <b-col cols="12">
                            <b-form-group
                                label="Mobile"
                                label-for="vi-mobile"
                            >
                                <b-input-group class="input-group-merge">
                                    <b-input-group-prepend is-text>
                                        <feather-icon icon="SmartphoneIcon"/>
                                    </b-input-group-prepend>
                                    <b-form-input
                                        id="vi-mobile"
                                        v-model="data.mobile"
                                        placeholder="Mobile"
                                        type="number"
                                    />
                                </b-input-group>
                            </b-form-group>
                        </b-col>

                        <!-- password -->
                        <b-col cols="12">
                            <b-form-group
                                label="Password"
                                label-for="vi-password"
                            >
                                <b-input-group class="input-group-merge">
                                    <b-input-group-prepend is-text>
                                        <feather-icon icon="LockIcon"/>
                                    </b-input-group-prepend>
                                    <b-form-input
                                        id="vi-password"
                                        v-model="data.password"
                                        placeholder="Password"
                                        type="password"
                                    />
                                </b-input-group>
                            </b-form-group>
                        </b-col>

                        <!-- reset and submit -->
                        <b-col cols="12">
                            <b-button
                                v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                                class="mr-1"
                                type="submit"
                                variant="primary"
                            >
                                Submit
                            </b-button>
                            <b-button
                                v-ripple.400="'rgba(186, 191, 199, 0.15)'"
                                type="reset"
                                variant="outline-secondary"
                            >
                                Reset
                            </b-button>
                        </b-col>
                    </b-row>
                </b-form>

            </b-card-code>
        </b-col>
        <b-col cols="12">
            <b-card-code title="Column Search Table">

                <!-- input search -->
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
                    :columns="columns"
                    :pagination-options="{
        enabled: true,
        perPage:pageLength
      }"
                    :rows="rows"
                    :rtl="direction"
                    :search-options="{
        enabled: true,
        externalQuery: searchTerm }"
                >
                    <template
                        slot="table-row"
                        slot-scope="props"
                    >

                        <!-- Column: Name -->
                        <div
                            v-if="props.column.field === 'name'"
                            class="text-nowrap"
                        >
                            <b-avatar
                                :src="props.row.avatar"
                                class="mx-1"
                            />
                            <span class="text-nowrap">{{ props.row.name }}</span>
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

            </b-card-code>
        </b-col>
    </b-row>
</template>

<script>
import BCardCode from '@core/components/b-card-code'
import {
    BRow,
    BCol,
    BFormGroup,
    BFormInput,
    BFormCheckbox,
    BForm,
    BButton,
    BInputGroup,
    BInputGroupPrepend,
    BAvatar,
    BBadge,
    BPagination,
    BFormSelect,
    BDropdown,
    BDropdownItem,

} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import { codeVerticalIcon, codeColumnSearch } from './code'

import { VueGoodTable } from 'vue-good-table'
import store from '@/store/index'

export default {
    components: {
        BCardCode,
        BRow,
        BCol,
        BFormGroup,
        BFormInput,
        BFormCheckbox,
        BInputGroup,
        BInputGroupPrepend,
        BForm,
        BButton,
        VueGoodTable,
        BAvatar,
        BBadge,
        BPagination,
        BFormSelect,
        BDropdown,
        BDropdownItem,
    },
    directives: {
        Ripple,
    },
    data() {
        return {
            data: {
                name: '',
                uname: '',
                mobile: '',
                password: '',
            },
            pageLength: 3,
            dir: false,
            codeColumnSearch,
            columns: [
                {
                    label: 'Name',
                    field: 'name',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Name',
                    },
                },
                {
                    label: 'User Name',
                    field: 'uname',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search User Name',
                    },
                },
                {
                    label: 'Mobile',
                    field: 'mobile',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Mobile',
                    },
                },
                {
                    label: 'Action',
                    field: 'action',
                },
            ],
            rows: [],
            searchTerm: '',
            codeVerticalIcon,
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
    // created() {
    //   this.$http.get('/good-table/basic')
    //     .then(res => { this.rows = res.data })
    // },
    created() {
    },
}
</script>
