<template>
    <b-card-code title="Private Jobs">
        <b-form>
            <b-row>
                <b-col md="3">
                    <b-form-group>
                        <label>Table</label>
                        <v-select
                            v-model="data.filterTable"
                            :options="select_Table"
                            :reduce="item=>item.id"
                            label="title"
                            placeholder="All"
                            @input="getPrivateJob()"
                        />
                    </b-form-group>
                </b-col>
                <b-col md="3">
                    <b-form-group>
                        <label>Verified Status</label>
                        <v-select
                            v-model="data.filterverified"
                            :options="select_verified"
                            :reduce="item=>item.id"
                            label="title"
                            placeholder="All"
                            @input="getPrivateJob()"

                        />
                    </b-form-group>
                </b-col>
            </b-row>
        </b-form>
        <!-- input search -->
        <div class="custom-search justify-content-end">
            <b-row>
                <b-col md="2">
                    <add-button-icons-link
                        buttonColor="primary"
                        buttonIcon="PlusIcon"
                        buttonLink="/posting/govt"
                        buttonText="Add New"
                    ></add-button-icons-link>
                </b-col>
                <b-col md="3">
                    <total-results :countText="total"></total-results>
                </b-col>
                <b-col class="mt-n2" md="3">
                    Export To :
                    <span>
                        <button @click="onBtnExportDataAsExcel">
                            <img src="@/assets/images/icons/xls.png" width="50">
                        </button>
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

        <ag-grid-vue
            ref="myTable"
            :columnDefs="columnDefs.value"
            :defaultColDef="defaultColDef"
            :excelStyles="excelStyles"
            :rowData="getTableData"
            animateRows="true"
            class="ag-theme-alpine"
            rowSelection="multiple"
            style="height: 500px"
            @cell-clicked="cellWasClicked"
            @grid-ready="onGridReady"
        >
        </ag-grid-vue>
        <b-modal
            id="view-model"
            centered
            hide-backdrop
            ok-only
            ok-title="Close"
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
                :rtl="direction"
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
            id="modal-footer-lg"
            centered
            hide-backdrop
            ok-only
            ok-title="Close"
            size="xl"
            title="Job Details"

        >
            <h1>Job ID - #{{ id }}</h1>
            <b-row>
                <b-col
                    cols="12"
                    lg="8"
                    md="7"
                    xl="9"
                >
                    <b-card>


                        <b-row>

                            <!-- User Info: Left col -->
                            <b-col
                                class="d-flex justify-content-between flex-column"
                                cols="21"
                                xl="6"
                            >
                                <!-- User Avatar & Action Buttons -->
                                <div class="d-flex justify-content-start">
                                    <b-avatar
                                        :src="data.image"
                                        :text="data.image"
                                        rounded
                                        size="104px"
                                        variant="light-primary"
                                    />
                                    <div class="d-flex flex-column ml-1">
                                        <div class="mb-1">
                                            <h4 class="mb-0 text-capitalize">
                                                {{ data.name }}
                                            </h4>
                                            <span v-if="data.jobtype===1"
                                                  class="card-text text-capitalize"
                                            >Private Employer</span>
                                            <span v-else-if="data.jobtype===2" class="card-text text-capitalize">central government</span>
                                            <span v-else class="card-text text-capitalize">State government</span>
                                        </div>
                                        <div class="d-flex flex-wrap">
                                            <b-button
                                                :href="'/posting/private/?table=jobs&id='+data.id"
                                                variant="primary"
                                            >
                                                Edit
                                            </b-button>
                                            <b-button
                                                class="ml-1"
                                                variant="outline-danger"
                                            >
                                                Delete
                                            </b-button>
                                        </div>
                                    </div>
                                </div>

                                <!-- User Stats -->

                            </b-col>

                            <!-- Right Col: Table -->
                            <b-col
                                cols="12"
                                xl="6"
                            >
                                <table class="mt-2 mt-xl-0 w-100">

                                    <tr>
                                        <th class="pb-50">
                                            <feather-icon
                                                class="mr-75"
                                                icon="CheckIcon"
                                            />
                                            <span class="font-weight-bold">Job Status</span>
                                        </th>
                                        <td class="pb-50 text-capitalize">
                                        <span v-if="data.jobstatus==='Live'" class="text-success">{{
                                                data.jobstatus
                                            }}</span>
                                            <span v-else class="text-danger">{{ data.jobstatus }}</span>


                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="pb-50">
                                            <feather-icon
                                                class="mr-75"
                                                icon="StarIcon"
                                            />
                                            <span class="font-weight-bold ">Job Title</span>
                                        </th>
                                        <td class="pb-50 text-capitalize text-dark">
                                            {{ data.jobtitle }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="pb-50">
                                            <feather-icon
                                                class="mr-75"
                                                icon="FlagIcon"
                                            />
                                            <span class="font-weight-bold">Job Category</span>
                                        </th>
                                        <td class="pb-50 text-dark">
                                            {{ data.jobcat }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <feather-icon
                                                class="mr-75"
                                                icon="PhoneIcon"
                                            />
                                            <span class="font-weight-bold">Contact1</span>
                                        </th>
                                        <td>


                                <span v-if="data.phone_new > 0" class="text-success">
                                    {{ data.phone_new }}
                                </span>

                                            <span v-else-if="data.phone > 0" class="text-success">
                                    {{ data.phone }}
                                </span>

                                            <span v-else class="text-danger text-capitalize"> not mention</span>
                                        </td>


                                    </tr>
                                    <tr>
                                        <th>
                                            <feather-icon
                                                class="mr-75"
                                                icon="PhoneIcon"
                                            />
                                            <span class="font-weight-bold">Contact2</span>
                                        </th>
                                        <td>


                                <span v-if="data.phone_new > 0" class="text-success">
                                    {{ data.phone_new }}
                                </span>

                                            <span v-else-if="data.phone > 0" class="text-success">
                                    {{ data.phone }}
                                </span>

                                            <span v-else class="text-danger text-capitalize"> not mention</span>
                                        </td>


                                    </tr>
                                    <tr>
                                        <th>
                                            <feather-icon
                                                class="mr-75"
                                                icon="PhoneIcon"
                                            />
                                            <span class="font-weight-bold text-capitalize">whatsapp number</span>
                                        </th>
                                        <td>
                                        <span v-if="data.whatsapp_number !=''"
                                              class="text-success text-capitalize"
                                        > {{ data.whatsapp_number }}</span>
                                            <span v-else class="text-danger text-capitalize"> not mention</span>
                                        </td>


                                    </tr>
                                </table>
                            </b-col>
                        </b-row>

                    </b-card>
                </b-col>
                <b-col
                    cols="12"
                    lg="4"
                    md="5"
                    xl="3"
                >
                    <b-card
                        class="border-primary"
                        no-body
                    >
                        <b-card-header class="d-flex justify-content-between align-items-center pt-75 pb-25">
                            <h4 class="mb-0 text-primary">
                                Current Plan
                            </h4>
                            <b-badge v-if="data.txn_amt > 0" variant="light-success">
                                Paid - ( {{ data.txn_amt }} )
                            </b-badge>

                            <b-badge v-else variant="light-warning">
                                Free
                            </b-badge>

                            <h6 class="text-dark w-100 text-capitalize">plan name : <span
                                class="text-warning"
                            >  {{ data.plan_name }}  <span
                                v-if="data.post_count!='' && data.post_count > 0 "
                                class="text-danger"
                            >( {{ data.post_count }} )</span>  </span></h6>
                            <h6 class="text-dark w-100 text-capitalize">date of job post : {{ data.postedDate }} </h6>
                            <h6 class="text-dark w-100 text-capitalize">plan start date : {{ data.starting_date }} </h6>
                            <h6 class="text-dark w-100 text-capitalize">plan end date : {{ data.expired_date }} </h6>
                            <h6 class="text-dark w-100 text-capitalize">job start date : {{ data.starting }} </h6>
                            <h6 class="text-dark w-100 text-capitalize">job end date : {{ data.ending }} </h6>
                            <h6 class="text-dark w-100 text-capitalize">total validity days : {{ data.dateDiff }} </h6>
                            <h6 class="text-dark w-100 text-capitalize">remaining days : {{
                                data.days_diff_remain
                                }} </h6>
                        </b-card-header>

                        <b-card-body>
                            <b-button
                                v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                                block
                                variant="primary"
                            >
                                Upgrade Plan
                            </b-button>
                        </b-card-body>
                    </b-card>
                </b-col>
            </b-row>
            <b-row>
                <b-col
                    cols="12"
                    lg="12"
                    md="12"
                    xl="12"
                >
                    <b-card>


                        <b-row align-h="center">

                            <!-- User Info: Left col -->
                            <b-col
                                class="d-flex justify-content-between flex-column"
                                cols="21"

                                xl="12"
                            >
                                <!-- User Avatar & Action Buttons -->


                                <!-- User Stats -->
                                <div class="d-flex align-items-center mt-2">
                                    <div v-if="data.from_salary!=''" class="d-flex align-items-center mr-2">
                                        <b-avatar
                                            rounded
                                            variant="light-primary"
                                        >
                                            <feather-icon
                                                icon="DollarSignIcon"
                                                size="18"
                                            />
                                        </b-avatar>
                                        <div class="ml-1">
                                            <h5 class="mb-0">
                                                {{ data.from_salary }}- {{ data.to_salary }}
                                            </h5>
                                            <small>Monthly Salary</small>
                                        </div>
                                    </div>

                                    <div v-if="data.from_exp!=0" class="d-flex align-items-center mr-2">
                                        <b-avatar
                                            rounded
                                            variant="light-success"
                                        >
                                            <feather-icon
                                                icon="TrendingUpIcon"
                                                size="18"
                                            />
                                        </b-avatar>
                                        <div class="ml-1">
                                            <h5 class="mb-0">
                                                {{ data.from_exp }} - {{ data.to_exp }}
                                            </h5>
                                            <small>Experience</small>
                                        </div>
                                    </div>
                                    <div v-if="data.noofvancancy!=0" class="d-flex align-items-center mr-2">
                                        <b-avatar
                                            rounded
                                            variant="light-success"
                                        >
                                            <feather-icon
                                                icon="UsersIcon"
                                                size="18"
                                            />
                                        </b-avatar>
                                        <div class="ml-1">
                                            <h5 class="mb-0">
                                                {{ data.noofvancancy }}
                                            </h5>
                                            <small class="text-capitalize">vacancy</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mr-2">
                                        <b-avatar
                                            rounded
                                            variant="light-primary"
                                        >
                                            <feather-icon
                                                icon="AirplayIcon"
                                                size="18"
                                            />
                                        </b-avatar>
                                        <div class="ml-1">
                                            <h5 class="mb-0">
                                                <span v-if="data.workmode==1"> FULL-TIME</span>
                                                <span v-else> PART-TIME</span>
                                            </h5>
                                            <small class="text-capitalize">work mode</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mr-2">
                                        <b-avatar
                                            rounded
                                            variant="light-warning"
                                        >
                                            <feather-icon
                                                icon="EyeIcon"
                                                size="18"
                                            />
                                        </b-avatar>
                                        <div class="ml-1">
                                            <h5 class="mb-0">
                                                {{ data.viewcount }}
                                            </h5>
                                            <small class="text-capitalize">view count</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mr-2">
                                        <b-avatar
                                            rounded
                                            variant="light-success"
                                        >
                                            <feather-icon
                                                icon="PhoneCallIcon"
                                                size="18"
                                            />
                                        </b-avatar>
                                        <div class="ml-1">
                                            <h5 class="mb-0">
                                                {{ data.call_count }}
                                            </h5>
                                            <small class="text-capitalize">call count</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mr-2">
                                        <b-avatar
                                            rounded
                                            variant="light-warning"
                                        >
                                            <feather-icon
                                                icon="PhoneCallIcon"
                                                size="18"
                                            />
                                        </b-avatar>
                                        <div class="ml-1">
                                            <h5 class="mb-0">
                                                {{ data.call_confirm_count }}
                                            </h5>
                                            <small class="text-capitalize">confirm call count</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mr-2">
                                        <b-avatar
                                            rounded
                                            variant="light-warning"
                                        >
                                            <feather-icon
                                                icon="UserIcon"
                                                size="18"
                                            />
                                        </b-avatar>
                                        <div class="ml-1">
                                            <h5 v-if="data.gender===0" class="mb-0">
                                                Male
                                            </h5>
                                            <h5 v-else-if="data.gender===1" class="mb-0">
                                                Female
                                            </h5>
                                            <h5 v-else class="mb-0">
                                                All
                                            </h5>
                                            <small class="text-capitalize">gender</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mr-2">
                                        <b-avatar
                                            rounded
                                            variant="light-danger"
                                        >
                                            <feather-icon
                                                icon="UsersIcon"
                                                size="18"
                                            />
                                        </b-avatar>
                                        <div class="ml-1">
                                            <h5 class="mb-0">
                                                {{ data.marital_status }}
                                            </h5>
                                            <small class="text-capitalize">marital status</small>
                                        </div>
                                    </div>
                                </div>
                            </b-col>

                            <!-- Right Col: Table -->

                        </b-row>

                    </b-card>
                </b-col>
            </b-row>
            <b-list-group>
                <b-list-group-item class="text-capitalize"><b>job location :</b> {{ data.jobLocation_string }}
                </b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>candidate can apply from :</b> {{
                    data.candidate_location
                    }}
                </b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>candidate can apply location :</b> {{
                    data.candidates_apply_location
                    }}
                </b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>address :</b> {{ data.address }}</b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>email :</b> {{ data.email }}</b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>email new :</b> {{ data.email_new }}</b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>qualification :</b> {{ data.quali }}</b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>job details :</b> {{ data.description }}
                </b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>skills :</b> {{ data.skills_string }}</b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>contact detail :</b> {{ data.contact_detail }}
                </b-list-group-item>
                <b-list-group-item v-if="data.website2!='' && data.website!=''" class="text-capitalize"><b>website :</b>
                    {{ data.website }} - {{ data.website2 }}
                </b-list-group-item>
                <b-list-group-item v-else-if="data.website!=''" class="text-capitalize"><b>website :</b> {{
                    data.website
                    }}
                </b-list-group-item>
                <b-list-group-item v-else-if="data.website2!=''" class="text-capitalize"><b>website :</b> {{
                    data.website2
                    }}
                </b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>contact person detail :</b> {{
                    data.contact_person_detail
                    }}
                </b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>keyword :</b> {{ data.keyword }}</b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>salary remarks :</b> {{ data.salary_remarks }}
                </b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>map url :</b> {{ data.map_url }}</b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>call from_time :</b> {{ data.call_from_time }}
                </b-list-group-item>
                <b-list-group-item class="text-capitalize"><b>call to time :</b> {{ data.call_to_time }}
                </b-list-group-item>
            </b-list-group>
        </b-modal>

    </b-card-code>
</template>

<script>
import BCardCode from '@core/components/b-card-code/BCardCode.vue'
import AddButtonIcons from '@/views/elements/AddButtonIcons'
import AddButtonIconsLink from '@/views/elements/AddButtonIconsLink.vue'
import TotalResults from '@/views/elements/TotalResults'
import ViewPrivateJobs from '@/views/elements/ViewPrivateJobs.vue'
import Ripple from 'vue-ripple-directive'
import downloadexcel from 'vue-json-excel'
import { jsPDF } from 'jspdf'
import vSelect from 'vue-select'

import {
    BAvatar,
    BBadge,
    BButton,
    BCard,
    BCardBody,
    BCardHeader,
    BCardText,
    BCol,
    BDropdown,
    BDropdownItem,
    BForm,
    BFormGroup,
    BFormInput,
    BFormSelect,
    BIcon,
    BListGroup,
    BListGroupItem,
    BModal,
    BPagination,
    BRow,
    BTab,
    BTabs,
    VBModal,
} from 'bootstrap-vue'

import { VueGoodTable } from 'vue-good-table'
import store from '@/store'
import { ValidationObserver, ValidationProvider } from 'vee-validate'
import Button from '@/views/components/button/Button'
import { AgGridVue } from 'ag-grid-vue'
// import { reactive, onMounted, ref } from "vue"
import 'ag-grid-community/styles/ag-grid.css' // Core grid CSS, always needed
import 'ag-grid-community/styles/ag-theme-alpine.css'
import moment from 'moment-timezone' // Optional theme CSS

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
    props: ['id'],
    components: {
        BIcon,
        Button,
        BButton,
        BRow,
        BCol,
        BModal,
        VBModal,
        BForm,
        BCardCode,
        AddButtonIcons,
        AddButtonIconsLink,
        ViewPrivateJobs,
        TotalResults,
        VueGoodTable,
        downloadexcel,
        BPagination,
        BFormGroup,
        BFormInput,
        BFormSelect,
        BDropdown,
        BDropdownItem,
        ValidationProvider,
        ValidationObserver,
        vSelect,
        AgGridVue,
        BAvatar,
        BCard,
        BCardText,
        BCardHeader,
        BCardBody,
        BBadge,
        BTab,
        BTabs,
        BListGroup,
        BListGroupItem,
        imageComponent: {
            template: '<img style="width: 100px" :src="this.params.data.logo">',
        },
        actionsComponent: {
            template: '<span><button class="btn btn-warning"  @click="openModell(params.data.id)">View</button> <a :href="\'/posting/private/?table=jobs&id=\'+params.data.id"><button class="btn btn-primary"  >Edit</button></a>    <button class="btn btn-danger" @click="callDeleteMethod(\'/deleteJobs\', data={table:\'jobs\',id:params.data.id}, \'post\')">Delete</button>   </span>',

            methods: {

                openModell(value) {
                    var rowNode = this.params.node.rowIndex
                    this.$root.$emit('openModel()', value)
                },
                callDeleteMethod(url, data, requestMethod) {
                    this.$root.$emit('callDeleteMethod()', url, data, requestMethod)
                },
            }
        },
        viewCountComponent: {
            template: '<button class="btn btn-danger" @click="openModelViewCount(params.data.id)" style="position: relative;" >View</button>',

            methods: {

                openModelViewCount(value) {
                    var rowNode = this.params.node.rowIndex
                    this.$root.$emit('openModelView()', value)
                },
            }
        },
        paymentStatusComponent: {

            template: '<span v-if="params.data.payment_status==\'Paid\'" class="text-success" > {{ params.data.payment_status }} ({{ params.data.txn_amt }})<br> {{ params.data.paid_date }}</span><span v-else class="text-danger">{{ params.data.payment_status }}</span>',
        },
        proofComponent: {

            template: '<span v-if="params.data.company_proof_type != 0 && params.data.id_proof_type != 0 && params.data.person_proof_type != 0 && params.data.is_com_add_proof === 1 && params.data.is_com_id_proof === 1 && params.data.is_person_proof === 1" class="text-success" > verified</span><span v-else class="text-danger">Not verified</span>',
        },
    },
    setup() {
        // const gridApi = ref(); // Optional - for accessing Grid's API
        const gridApi = {} // Optional - for accessing Grid's API
        // console.log(gridApi)
        // Obtain API from grid's onGridReady event
        const onGridReady = (params) => {
            gridApi.value = params.api
        }
        // const rowData = reactive({}); // Set rowData to Array of Objects, one Object per Row
        const rowData = {} // Set rowData to Array of Objects, one Object per Row
        // Each Column Definition results in one Column.
        const excelStyles = [
            {
                id: 'dateUK',
                dataType: 'DateTime',
                numberFormat: {
                    format: 'dd/mm/yy',
                },
            },
        ]
        const columnDefs = {
            value: [
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
                    headerName: 'Job Status',
                    field: 'jobpoststatus',
                    width: 90,
                },

                {
                    headerName: 'Proof',
                    cellRendererSelector: params => {
                        return {
                            component: 'proofComponent'
                        }

                    },
                    cellStyle: { 'white-space': 'normal' },
                    autoHeight: true,
                },
                {
                    headerName: 'Payment Status',
                    cellRendererSelector: params => {
                        return {
                            component: 'paymentStatusComponent'
                        }

                    },
                    cellStyle: { 'white-space': 'normal' },
                    autoHeight: true,
                },
                {
                    headerName: 'View Count',
                    cellRendererSelector: params => {
                        return {
                            component: 'viewCountComponent'
                        }

                    },
                    cellStyle: { 'white-space': 'normal' },
                    autoHeight: true,
                },

                {
                    headerName: 'Company Name',
                    field: 'company',
                    width: 200,
                },
                {
                    headerName: 'Jobtitle',
                    field: 'jobtitle',
                    width: 200,
                },
                {
                    headerName: 'Phone New',
                    field: 'phone_new',
                    width: 90,
                },
                {
                    headerName: 'Starting Date',
                    // field: 'ending',
                    cellClass: 'dateUK',
                    valueFormatter: (params) => {
                        if (params.data.starting != '0000-00-00') {
                            var date = new Date(params.data.ending)
                            var day = date.getDate()
                                .toString()
                                .padStart(2, '0')
                            var month = (date.getMonth() + 1).toString()
                                .padStart(2, '0')
                            var year = date.getFullYear()
                                .toString()
                                .substring(4, '0')
                            return day + '/' + month + '/' + year
                        } else {
                            return ''
                        }
                    },
                    width: 110,
                },
                {
                    headerName: 'Ending Date',
                    // field: 'ending',
                    cellClass: 'dateUK',
                    valueFormatter: (params) => {
                        if (params.data.ending != '0000-00-00') {
                            var date = new Date(params.data.ending)
                            var day = date.getDate()
                                .toString()
                                .padStart(2, '0')
                            var month = (date.getMonth() + 1).toString()
                                .padStart(2, '0')
                            var year = date.getFullYear()
                                .toString()
                                .substring(4, '0')
                            return day + '/' + month + '/' + year
                        } else {
                            return ''
                        }
                    },
                    width: 110,
                },
                {
                    headerName: 'Vacancy',
                    field: 'noofvancancy',
                    width: 90,
                },
                {
                    headerName: 'Gender',
                    field: 'gender',
                    width: 90,
                },
                {
                    headerName: 'Executive Employer',
                    field: 'excutive',
                    width: 150,
                },
                {
                    headerName: 'In Date',
                    // field: 'ending',
                    cellClass: 'dateUK',
                    valueFormatter: (params) => {
                        if (params.data.indate_job != '0000-00-00') {
                            var date = new Date(params.data.ending)
                            var day = date.getDate()
                                .toString()
                                .padStart(2, '0')
                            var month = (date.getMonth() + 1).toString()
                                .padStart(2, '0')
                            var year = date.getFullYear()
                                .toString()
                                .substring(4, '0')
                            return day + '/' + month + '/' + year
                        } else {
                            return ''
                        }
                    },
                    width: 110,
                },

                {
                    headerName: 'In By',
                    field: 'iname',
                    width: 90,
                },
                {
                    headerName: 'Up Date',
                    // field: 'ending',
                    cellClass: 'dateUK',
                    valueFormatter: (params) => {
                        if (params.data.update_job != '0000-00-00') {
                            var date = new Date(params.data.ending)
                            var day = date.getDate()
                                .toString()
                                .padStart(2, '0')
                            var month = (date.getMonth() + 1).toString()
                                .padStart(2, '0')
                            var year = date.getFullYear()
                                .toString()
                                .substring(4, '0')
                            return day + '/' + month + '/' + year
                        } else {
                            return ''
                        }
                    },
                    width: 110,
                },
                {
                    headerName: 'Up By',
                    field: 'upby',
                    width: 90,
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
            getQuickFilterText: params => {
                return params.value.name
            }
        }
        return {
            onGridReady,
            columnDefs,
            rowData,
            defaultColDef,
            excelStyles,
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
                table: 'jobs',
                filterTable: 'live',
                filterverified: '',
            },
            getTableData: [],
            searchTerm: '',
            rowsView: [],
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
            listQuali: [],
            qualisingle: [],
            qualigroup: [],
            keyWord: [],
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
            select_verified: [{
                id: 1,
                title: 'Verified'
            }, {
                id: 2,
                title: 'Not Verified'
            }],
        }
    },
    async mounted() {
        this.$root.$on('openModel()', (data) => {
            this.openModel(data)
        })
        this.$root.$on('openModelView()', (data) => {
            this.openModelView(data)
        })

        this.$root.$on('callDeleteMethod()', (url, data, requestMethod) => {
            this.callDeleteMethod(url, data, requestMethod)
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
        this.getPrivateJob()
    },
    methods: {

        getPrivateJob() {
            this.total = 'Loading...'
            this.data.table = 'jobs'
            this.data.jobtype = [1]
            this.data.jobstatus = this.data.filterTable
            this.data.verify = this.data.filterverified

            axios.post('/viewPrivateJobs', this.data)
                .then(res => {
                    // console.log(res.data)
                    this.total = res.data.length
                    this.total = res.data.length
                    this.getTableData = res.data.data
                })
        },

        onBtnExportDataAsExcel() {
            this.gridApi.exportDataAsExcel()
        },
        startDownload() {
            alert('show loading')
        },
        finishDownload() {
            alert('hide loading')
        },
        callDeleteMethod(url, data, requestMethod) {
            console.log(this.sweetAlertDeleteMethod(url, data, requestMethod))
        },
        async openModelView(value) {
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
            } else {
                this.rowsView = ''
            }
        },
        openModel(value) {
            // alert('gs');
            this.model_id = value
            this.$bvModal.show('modal-footer-lg')

            this.getDataGovent(value, 'private')

        },
        async getDataGovent(value, type) {

            const callAxios = await this.callAxios('/getSinglejobs', {
                id: value,
                jobtype: type,
            }, 'post')
            console.log(callAxios)
            var return_data = callAxios.data[0]
            if (callAxios.status === 200) {
                const logo_image = ''
                this.data = return_data
                this.data.postedDate = moment(callAxios.data[0].postedDate)
                    .format('DD/MM/YYYY')
                this.data.starting_date = moment(callAxios.data[0].starting_date)
                    .format('DD/MM/YYYY')
                this.data.expired_date = moment(callAxios.data[0].expired_date)
                    .format('DD/MM/YYYY')
                this.data.starting = moment(callAxios.data[0].starting)
                    .format('DD/MM/YYYY')
                this.data.ending = moment(callAxios.data[0].ending)
                    .format('DD/MM/YYYY')
                this.data.examdate = moment(callAxios.data[0].examdate)
                    .format('DD/MM/YYYY')
                // this.data.call_to_time = moment(callAxios.data[0].call_to_time).format("hh:mm:ss a");
                // this.data.call_from_time = moment(callAxios.data[0].call_from_time).format('hh:mm');
                const currentDate = moment(new Date(callAxios.data[0].start_check))
                const currentDate_rem = moment(new Date())
                const currentDate_rem1 = currentDate_rem.subtract(1, 'days')
                const returnDate = moment(new Date(callAxios.data[0].end_check))
                var days_diff_remain = returnDate.diff(currentDate_rem1, 'days')
                var days_diff = returnDate.diff(currentDate, 'days')
                console.log(this.data.examdate)
                this.data.dateDiff = days_diff
                this.data.days_diff_remain = days_diff_remain
                this.logo_image = callAxios.data[0].logo_image
                const quali = callAxios.data[0].quali
                const keyword = callAxios.data[0].keyword
                const qualisingle = callAxios.data[0].qualisingle
                const qualigroup = callAxios.data[0].qualigroup
                const jobtype = callAxios.data[0].jobtype
                this.listQuali = quali.split(',')
                this.qualisingle = qualisingle.split(',')
                this.qualigroup = qualigroup.split(',')
                this.keyWord = keyword.split(',')

                if (logo_image != '') {
                    this.data.image = logo_image
                } else {
                    this.data.image = 'https://nithrajobs.com/assets/dist/img/private_job.webp'
                }

            } else {
                // console.log(callAxios.response.data.message);
                // this.toast("Error", callAxios.response.data.message, 'danger');
                this.sweetAlertmethod('error', 'Error', callAxios.response.data.message, 'btn-primary')
            }
        },
        jobtypeText(jobtype) {
            switch (jobtype) {
                case 1:
                    return 'Private'
                    break
                case 2:
                    return 'State'
                    break
                case 3:
                    return 'Central'
                    break
            }
        },
        jobtypeVarient(jobtype) {
            switch (jobtype) {
                case 1:
                    return 'light-info'
                    break
                case 2:
                    return 'light-success'
                    break
                case 3:
                    return 'light-danger'
                    break
            }
        },
        async showModal(value) {
            if (value) {
                this.data.id = value
                const callAxios = await this.callAxios('/getSingleUser', { id: value }, 'post')
                console.log(callAxios)
                var return_data = callAxios.data[0]
                if (callAxios.status === 200) {
                    this.data = {
                        id: return_data.id,
                        name: return_data.name,
                        phonenumber: return_data.phonenumber,
                        uname: return_data.uname,
                        usergroup: return_data.usergroup,
                    }
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

    },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
@import '~@resources/scss/vue/libs/vue-good-table.scss';
</style>
