<template>
    <b-modal
        ref="my-modal"

        :body-bg-variant="bodyBgVariant"
        :body-text-variant="bodyTextVariant"
        :footer-text-variant="footerTextVariant"
        :header-bg-variant="headerBgVariant"
        :header-text-variant="headerTextVariant"
        centered
        class=""
        content-class="shadow"
        hide-backdrop
        no-close-on-backdrop
        ok-only
        ok-title="Close"
        size="xl"
        title="EMPLOYER HISTORY"

        title-tag="div"
    >

        <section>

            <h3 class="mt-1 mb-1 text-primary text-center">
                EMPLOYER PERSONAL DETAILS
            </h3>

            <!-- outline color -->
            <b-row>
                <b-col

                    md="12"
                    xl="12"
                >

                    <b-card
                        bg-variant="transparent"
                        border-variant="primary"
                        class="shadow-none"
                    >

                        <b-card-text>
                            <b-row class="w-100 border">
                                <b-col class="border-warning" md="4">
                                    Name
                                </b-col>
                                <b-col class=" border-dark" md="8">
                                    {{ this.hdata.firstname }}
                                </b-col>
                            </b-row>
                            <b-row class="w-100 border">
                                <b-col class="border-warning" md="4">
                                    E-mail
                                </b-col>
                                <b-col class=" border-dark" md="8">
                                    {{ this.hdata.email }}
                                </b-col>
                            </b-row>
                            <b-row class="w-100 border">
                                <b-col class="border-warning" md="4">
                                    Mobile Number
                                </b-col>
                                <b-col class=" border-dark" md="8">
                                    {{ this.hdata.phonenumber }}
                                </b-col>
                            </b-row>
                            <b-row class="w-100 border">
                                <b-col class="border-warning" md="4">
                                    Designation
                                </b-col>
                                <b-col class=" border-dark" md="8">
                                    {{ this.hdata.designation }}
                                </b-col>
                            </b-row>
                        </b-card-text>

                    </b-card>
                </b-col>
            </b-row>
        </section>

        <section>

            <h3 class="mt-1 mb-2 text-primary text-center">
                COMPANY DETAILS
            </h3>

            <!-- outline color -->
            <b-row>
                <b-col

                    md="12"
                    xl="12"
                >
                    <b-card
                        bg-variant="transparent"
                        border-variant="primary"
                        class="shadow-none"
                    >

                        <b-card-text>
                            <b-row class="w-100 border">
                                <b-col class="border-warning" md="4">
                                    Company Name
                                </b-col>
                                <b-col class=" border-dark" md="8">
                                    {{ this.hdata.companyname }}
                                </b-col>
                            </b-row>
                            <b-row class="w-100 border">
                                <b-col class="border-warning" md="4">
                                    Industry Type
                                </b-col>
                                <b-col class=" border-dark" md="8">
                                    {{ this.hdata.industry_type }}
                                </b-col>
                            </b-row>
                            <b-row class="w-100 border">
                                <b-col class="border-warning" md="4">
                                    Address
                                </b-col>
                                <b-col class=" border-dark" md="8">
                                    {{ this.hdata.address }}
                                </b-col>
                            </b-row>
                            <b-row class="w-100 border">
                                <b-col class="border-warning" md="4">
                                    Pan Card
                                </b-col>
                                <b-col class=" border-dark" md="8">
                                    {{ this.hdata.pan_card }}
                                </b-col>
                            </b-row>
                            <b-row class="w-100 border">
                                <b-col class="border-warning" md="4">
                                    GST Number
                                </b-col>
                                <b-col class=" border-dark" md="8">
                                    {{ this.hdata.gst }}
                                </b-col>
                            </b-row>
                            <b-row class="w-100 border">
                                <b-col class="border-warning" md="4">
                                    Website
                                </b-col>
                                <b-col class=" border-dark" md="8">
                                    {{ this.hdata.website }}
                                </b-col>
                            </b-row>

                        </b-card-text>
                    </b-card>


                </b-col>
            </b-row>
        </section>
        <section>

            <h3 class="mt-1 mb-2 text-primary text-center">
                PROOF DETAILS
            </h3>

            <!-- outline color -->
            <b-row>
                <b-col

                    md="12"
                    xl="12"
                >
                    <b-card
                        bg-variant="transparent"
                        border-variant="primary"
                        class="shadow-none"
                    >

                        <b-card-text>
                            <b-row class="w-100 border">
                                <b-col class="border-warning" md="4">
                                    <h4 class="text-center">PERSON PROOF<h6 class="text-center">PASSPORT</h6></h4>
                                    <span v-if="hdata.person_proof!=''">
          <b-link class="card">

                                            <div class="img-container w-50 mx-auto py-75">
                                                <b-img
                                                    :src="hdata.person_proof"
                                                    fluid
                                                />
<span v-if="hdata.is_person_proof === 1" class="d-block text-center">
<b-button
    v-ripple.400="'rgba(40, 199, 111, 0.15)'"
    class="btn-icon mt-2 "
    disabled=""
    variant="gradient-success"
>
        <feather-icon
            class="mr-50"
            icon="CheckIcon"
        />
        <span class="align-middle">Verified</span>
      </b-button>
</span>
                                                <span v-else-if="hdata.is_person_proof === 2"
                                                      class="d-block text-center"
                                                >
<b-button
    v-ripple.400="'rgba(40, 199, 111, 0.15)'"
    class="btn-icon mt-2 "
    disabled=""
    variant="gradient-danger"
>
        <feather-icon
            class="mr-50"
            icon="XOctagonIcon"
        />
        <span class="align-middle">Blocked</span>
      </b-button>
                                                      <b-row class="mt-2 d-block text-center">

                                                     <b-button
                                                         class="btn-icon"
                                                         variant="gradient-success"
                                                         @click="proofAction('/proofAction', data={id:hdata.id,value:1,type:'personProof'}, 'post','Yes, Verify it!','Verified')"
                                                     >
          <feather-icon icon="CheckCircleIcon"/>
        </b-button>


                                                </b-row>
</span>

                                                <span v-else>
                                                      <b-row class="mt-2 d-block text-center">

                                                     <b-button
                                                         class="btn-icon"
                                                         variant="gradient-success"
                                                         @click="proofAction('/proofAction', data={id:hdata.id,value:1,type:'personProof'}, 'post','Yes, Verify it!','Verified')"
                                                     >
          <feather-icon icon="CheckCircleIcon"/>
        </b-button>
                                                  <b-button
                                                      class="btn-icon "
                                                      variant="gradient-danger"
                                                      @click="proofAction('/proofAction', data={id:hdata.id,value:2,type:'personProof'}, 'post','Yes, Block it!','Blocked')"
                                                  >
          <feather-icon icon="XCircleIcon"/>
        </b-button>

                                                </b-row>
                                                </span>

                                            </div>

                                        </b-link>
</span>
                                    <span v-else> <br><h3
                                        class="text-center text-danger"
                                    >Not Attached Proof</h3></span>

                                </b-col>

                                <b-col class=" border-dark" md="4">
                                    <h4 class="text-center">ADDRESS PROOF<h6 class="text-center">GST REGISTRATION
                                        CERTIFICATE</h6></h4>
                                    <span v-if="hdata.address_proof!=''">
      <b-link class="card">

                                            <div class="img-container w-50 mx-auto py-75">
                                                <b-img
                                                    :src="hdata.address_proof"
                                                    fluid
                                                />

                                                  <span v-if="hdata.is_com_add_proof==1" class="d-block text-center">
<b-button
    v-ripple.400="'rgba(40, 199, 111, 0.15)'"
    class="btn-icon mt-2 "
    disabled=""
    variant="gradient-success"
>
        <feather-icon
            class="mr-50"
            icon="CheckIcon"
        />
        <span class="align-middle">Verified</span>
      </b-button>
</span>
                                                   <span v-else-if="hdata.is_com_add_proof==2"
                                                         class="d-block text-center"
                                                   >
<b-button
    v-ripple.400="'rgba(40, 199, 111, 0.15)'"
    class="btn-icon mt-2 "
    disabled=""
    variant="gradient-danger"
>
        <feather-icon
            class="mr-50"
            icon="XOctagonIcon"
        />
        <span class="align-middle">Blocked</span>
      </b-button>
                                                         <b-row class="mt-2 d-block text-center">

                                                     <b-button
                                                         class="btn-icon"
                                                         variant="gradient-success"
                                                         @click="proofAction('/proofAction', data={id:hdata.id,value:1,type:'addressProof'}, 'post','Yes, Verify it!','Verified')"
                                                     >
          <feather-icon icon="CheckCircleIcon"/>
        </b-button>


                                                </b-row>
</span>
                                                <span v-else>
                                                     <b-row class="mt-2 d-block text-center">

                                                     <b-button
                                                         class="btn-icon"
                                                         variant="gradient-success"
                                                         @click="proofAction('/proofAction', data={id:hdata.id,value:1,type:'addressProof'}, 'post','Yes, Verify it!','Verified')"
                                                     >
          <feather-icon icon="CheckCircleIcon"/>
        </b-button>
                                                  <b-button
                                                      class="btn-icon "
                                                      variant="gradient-danger"
                                                      @click="proofAction('/proofAction', data={id:hdata.id,value:2,type:'addressProof'}, 'post','Yes, Block it!','Blocked')"
                                                  >
          <feather-icon icon="XCircleIcon"/>
        </b-button>

                                                </b-row>
                                                </span>


                                            </div>

                                        </b-link>
</span>
                                    <span v-else> <br><h3
                                        class="text-center text-danger"
                                    >Not Attached Proof</h3></span>

                                </b-col>
                                <b-col class=" border-dark" md="4">
                                    <h4 class="text-center">ID PROOF</h4>
                                    <span v-if="hdata.id_proof!=''">
                                            <b-link class="card">


                                            <div class="img-container w-50 mx-auto py-75">
                                                <b-img
                                                    :src="hdata.id_proof"
                                                    fluid
                                                />

                                                <span v-if="hdata.is_com_id_proof==1" class="d-block text-center">
<b-button
    v-ripple.400="'rgba(40, 199, 111, 0.15)'"
    class="btn-icon mt-2 "
    disabled=""
    variant="gradient-success"
>
        <feather-icon
            class="mr-50"
            icon="CheckIcon"
        />
        <span class="align-middle">Verified</span>
      </b-button>
</span>
                                                 <span v-else-if="hdata.is_com_id_proof==2" class="d-block text-center">
<b-button
    v-ripple.400="'rgba(40, 199, 111, 0.15)'"
    class="btn-icon mt-2 "
    disabled=""
    variant="gradient-danger"
>
        <feather-icon
            class="mr-50"
            icon="XOctagonIcon"
        />
        <span class="align-middle">Blocked</span>
      </b-button>
                                                         <b-row class="mt-2 d-block text-center">

                                                     <b-button
                                                         class="btn-icon"
                                                         variant="gradient-success"
                                                         @click="proofAction('/proofAction', data={id:hdata.id,value:1,type:'idProof'}, 'post','Yes, Verify it!','Verified')"
                                                     >
          <feather-icon icon="CheckCircleIcon"/>
        </b-button>


                                                </b-row>
</span>
                                                <span v-else>

                                                       <b-row class="mt-2 d-block text-center">

                                                     <b-button
                                                         class="btn-icon"
                                                         variant="gradient-success"
                                                         @click="proofAction('/proofAction', data={id:hdata.id,value:1,type:'idProof'}, 'post','Yes, Verify it!','Verified',)"
                                                     >
          <feather-icon icon="CheckCircleIcon"/>
        </b-button>
                                                  <b-button
                                                      class="btn-icon "
                                                      variant="gradient-danger"
                                                      @click="proofAction('/proofAction', data={id:hdata.id,value:2,type:'idProof'}, 'post','Yes, Block it!','Blocked')"
                                                  >
          <feather-icon icon="XCircleIcon"/>
        </b-button>

                                                </b-row>

                                                </span>


                                            </div>

                                        </b-link>
                                        </span>
                                    <span v-else> <br><h3
                                        class="text-center text-danger"
                                    >Not Attached Proof</h3></span>

                                </b-col>
                            </b-row>
                        </b-card-text>
                    </b-card>


                </b-col>
            </b-row>
        </section>
        <section>

            <h3 class="mt-1 mb-2 text-primary text-center">
                PLAN DETAILS
            </h3>

            <!-- outline color -->
            <b-card>

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
                    :columns="paymentDetails"
                    :pagination-options="{
        enabled: true,
        perPage:pageLength
      }"
                    :rows="paymentDetailsrows"

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

                <template #code>
                    {{ codeColumnSearch }}
                </template>
            </b-card>
        </section>
        <section>

            <h3 class="mt-1 mb-2 text-primary text-center">
                JOB DETAILS
            </h3>

            <!-- outline color -->
            <b-card>

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
                    :columns="jobDetails"
                    :pagination-options="{
        enabled: true,
        perPage:pageLength
      }"
                    :rows="jobDetailsrows"

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

                <template #code>
                    {{ codeColumnSearch }}
                </template>
            </b-card>
        </section>
    </b-modal>
</template>

<script>
import { VueGoodTable } from 'vue-good-table'
import {
    BAvatar,
    BCard,
    BButton,
    BModal,
    VBModal,
    BCardText,
    BRow,
    BCol,
    BPagination,
    BFormGroup,
    BFormInput,
    BFormSelect,
    BDropdown,
    BDropdownItem,
    BCardHeader,
    BCardBody,
    BBadge,
    BTab,
    BTabs,
    BListGroup,
    BListGroupItem,
    BSpinner,
    BCardTitle,
} from 'bootstrap-vue'
import BCardCode from '@core/components/b-card-code'
import Ripple from 'vue-ripple-directive'
import useUsersList from '@/views/apps/user/users-list/useUsersList'
import { avatarText } from '@core/utils/filter'
import VueMoment from 'vue-moment'
import moment from 'moment-timezone'
import store from '@/store'

export default {
    props: ['id'],
    components: {
        BCardCode,
        BCardText,
        BButton,
        BModal,
        BCard,
        BPagination,
        BFormGroup,
        BFormInput,
        BFormSelect,
        BDropdown,
        BDropdownItem,
        BRow,
        BCol,
        VueGoodTable,
        BCardTitle,
        BAvatar,
        BCardHeader,
        BCardBody,
        BBadge,
        VueMoment,
        BTab,
        BTabs,
        BListGroup,
        BListGroupItem,
        BSpinner,
    },
    directives: {
        'b-modal': VBModal,
        Ripple,
    },
    data() {
        return {
            model_check: '',
            data: {
                'emp_id': '',
                'groupqualification': '',
                'call_from_time': '',
                'call_to_time': '',
                'how_to_apply': '',
                'gover_addresspost': '',
                'map_url': '',
                'referredby': '',
                'singlequalification': '',
                'candidate_location': '',
                'candidates_apply_location': '',
                'jobLocation_string': '',
                'skills_string': '',
                'salary_remarks': '',
                'dist': '',
                'tags': '',
                'whatsapp_number': '',
                'contact_person_detail': '',
                'contact_detail': '',
                'quali': '',
                'address': '',
                'email_new': '',
                'email': '',
                'gender': '',
                'post_count': '',
                'txn_amt': '',
                'plan_name': '',
                'days_diff_remain': '',
                'description': '',
                'website': '',
                'website2': '',
                'keyword': '',
                'feesDetails': '',
                'agelimit': '',
                'call_count': '',
                'noofvancancy': '',
                'dateDiff': '',
                'phone_new': '',
                'paid_post': '',
                'phone': '',
                'upbyName': '',
                'job_edit_date': '',
                'jobtitle': '',
                'ending': '',
                'examdate': '',
                'fees': '',
                'from_exp': '',
                'from_salary': '',
                'govtSalary': '',
                'howToApply': '',
                'inbyName': '',
                'job_id': '',
                'jobcat': '',
                'jobstatus': '',
                'jobtype': '',
                'jsource': '',
                'marital_status': '',
                'name': '',
                'cname': '',
                'postaladdr': '',
                'postedDate': '',
                'starting_date': '',
                'expired_date': '',
                'postedIp': '',
                'selection': '',
                'starting': '',
                'status': '',
                'to_exp': '',
                'to_salary': '',
                'viewcount': '',
                'workmode': '',
                'image': '',
            },
            hdata: {
                addproof_type: '',
                companyname: '',
                companynameTamil: '',
                companytype: '',
                designation: '',
                email: '',
                firstname: '',
                gst: '',
                id: '',
                idproof_type: '',
                industry_type: '',
                personproof_type: '',
                phonenumber: '',
                address: '',
                pan_card: '',
                website: '',
                person_proof: '',
                address_proof: '',
                id_proof: '',
                company_proof: '',
                is_com_add_proof: '',
                is_person_proof: '',
                is_com_id_proof: '',
            },
            jobDetails: [

                {
                    label: 'Job id',
                    width: '150px',
                    field: 'id',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Verified',
                    },

                },
                {
                    label: 'Job Title',
                    width: '150px',
                    field: 'jobtitle',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Verified',
                    },

                },
                {
                    label: 'Post Status',
                    width: '150px',
                    field: 'PostStatus',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Verified',
                    },

                },
                {
                    label: 'Job Status',
                    width: '150px',
                    field: 'jobstatus',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Verified',
                    },

                },
                {
                    label: 'Plan Name',
                    width: '150px',
                    field: 'plan_name',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Verified',
                    },

                },
                {
                    label: 'Days',
                    width: '150px',
                    field: 'days',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Verified',
                    },

                },
                {
                    label: 'End Date',
                    width: '150px',
                    field: 'ending',
                    dateInputFormat: 'YYYY-mm-dd HH:MM:SS',
                    dateOutputFormat: 'dd-mm-yyyy',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Verified',
                    },

                },

            ],
            paymentDetails: [

                {

                    label: 'Plan Name',
                    width: '150px',
                    field: 'plan_name',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Verified',
                    },

                },

                {

                    label: 'Post Count',
                    width: '150px',
                    field: 'post_count',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Verified',
                    },

                },

                {

                    label: 'Validity',
                    width: '150px',
                    field: 'validity',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Verified',
                    },

                },

                {

                    label: 'Actual Amount',
                    width: '150px',
                    field: 'actual_amount',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Verified',
                    },

                },

                {

                    label: 'Transaction Amount',
                    width: '150px',
                    field: 'txn_amt',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Verified',
                    },

                },

                {

                    label: 'Starting Date',
                    width: '150px',
                    field: 'starting_date',
                    dateInputFormat: 'yyyy-MM-dd',
                    dateOutputFormat: 'dd-MM-yyyy',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Verified',
                    },

                },

                {

                    label: 'Expired Date',
                    width: '150px',
                    field: 'expired_date',
                    dateInputFormat: 'yyyy-MM-dd',
                    dateOutputFormat: 'dd-MM-yyyy',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Verified',
                    },

                },

            ],
            modal_history: false,
            pageLength: 10,
            searchTerm: '',
            dir: false,
            bodyBgVariant: 'light',
            bodyTextVariant: 'dark',
            footerBgVariant: 'warning',
            footerTextVariant: 'dark',
            headerBgVariant: 'primary',
            headerTextVariant: 'light',
            jobDetailsrows: [],
            paymentDetailsrows: [],
            listQuali: [],
            qualisingle: [],
            qualigroup: [],
            keyWord: [],
            initial_loading: true,
            variants: ['light', 'primary', 'secondary', 'danger', 'warning', 'success', 'info', 'dark'],
        }

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
    watch: {

        id: function (newVal) {
            this.data.name = 'Loading... Please Wait..'
            this.model_check = newVal
            // alert( 'pandiya'+this.model_check)
            this.getDataGovent(this.model_check, 'private', this.table)
        }
    },
    methods: {

        async getDataGovent(value) {
// alert('here');
            this.data.id = value
            const callAxios = await this.callAxios('/privateEmployerHistory', { id: value }, 'post')
            // console.log(callAxios.data.employerDeails[0])
            this.hdata = callAxios.data.employerDeails[0]
            this.jobDetailsrows = callAxios.data.jobs_details
            this.paymentDetailsrows = callAxios.data.payment_details
            const address_check = callAxios.data.employerDeails[0].address.split('#@#')
            this.hdata.address = address_check[0]
            this.$refs['my-modal'].show()
        },
    }

}
</script>
