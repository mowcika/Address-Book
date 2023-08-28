<template>
    <b-card-code title="Executive Master">
        <b-form v-if="useridCheck==156 || useridCheck==203 || useridCheck==228">
            <b-row>
                <b-col v-if="data.inby==156 || useridCheck==203 || useridCheck==228" md="3">
                    <b-form-group>
                        <label>Select User</label>
                        <v-select
                            v-model="data.filterTableUser"
                            :options="selectUser"
                            :reduce="item=>item.id"
                            label="title"
                            multiple
                            placeholder="All"
                            @input="changeUser(data.filterTableUser)"

                        />
                    </b-form-group>
                </b-col>
                <!--                <b-col md="3">-->
                <!--                    <div style="margin-bottom: 5px;">-->
                <!--                        <input type="text" v-on:input="onQuickFilterChanged()" id="quickFilter" placeholder="quick filter...">-->
                <!--                    </div>-->
                <!--                </b-col>-->
                <b-col v-if="data.inby==156 || useridCheck==203 || useridCheck==228" md="3">
                    <b-form-group>
                        <label>Assign To</label>
                        <v-select
                            v-model="data.filterTable"
                            :options="selectUser"
                            :reduce="item=>item.id"
                            label="title"
                            multiple
                            placeholder="All"

                        />
                    </b-form-group>
                </b-col>
                <b-col v-if="data.inby==156 || useridCheck==203 || useridCheck==228" md="3">
                    <b-form-group>
                        <label>Assign Filter</label>
                        <v-select
                            v-model="data.assignFilter"
                            :options="assignStatus"
                            :reduce="item=>item.id"
                            label="title"
                            placeholder="All"
                            @input="changeUser(data.assignFilter)"
                        />
                    </b-form-group>
                </b-col>
            </b-row>
        </b-form>
        <!-- input search -->
        <div class="custom-search justify-content-end mt-1">
            <b-row>
                <b-col md="3">
                    <total-results :countText="total"></total-results>
                </b-col>
                <b-col class="mt-n2" md="3">
                    Export To :
                    <span>
                        <button @click="onBtnExport()">
                            <img src="@/assets/images/icons/xls.png" width="50">
                        </button>
                    </span>
                </b-col>
                <b-col md="4">
                    <b-form-group>
                        <div class="d-flex align-items-center">
                            <label class="mr-1">Search</label>
                            <input id="quickFilter" placeholder="quick filter..." type="text"
                                   v-on:input="onQuickFilterChanged()"
                            >

                        </div>
                    </b-form-group>
                </b-col>
            </b-row>
        </div>
        <b-button v-if="showBtn==true" class="mb-1" variant="gradient-primary" @click="assignedUser()">
            Assign ( {{ showBtnLen }} )
        </b-button>
        <ag-grid-vue
            ref="myTable"
            :columnDefs="columnDefs"
            :defaultColDef="defaultColDef"
            :rowData="getTableData"
            animateRows="true"
            class="ag-theme-alpine"
            :rowSelection="rowSelection"
            @selection-changed="onSelectionChanged"
            style="height: 500px"
            @grid-ready="onGridReady"
        >
        </ag-grid-vue>
        <view-private-employer :id="this.model_id"></view-private-employer>
        <b-modal
            ref="EditRequestmodal"

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
                    Edit Request Form
                </h3>
                <b-overlay

                    id="overlay-background"
                    :blur="blur"
                    :opacity="opacity"
                    :show="this.overlay"
                    :variant="variant"
                    rounded="sm"
                >
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
                                <validation-observer ref="simpleRules">
                                    <b-form>
                                        <b-row>

                                            <!-- username -->


                                            <!-- remark -->
                                            <b-col cols="12">
                                                <b-form-group
                                                    class="text-danger"
                                                    label="Remark *"
                                                    label-for="Remark"
                                                >
                                                    <validation-provider
                                                        #default="{ errors }"
                                                        name="remark"
                                                        rules="required"
                                                    >
                                                        <b-form-textarea
                                                            id="textarea-default"
                                                            v-model="data.remark"
                                                            placeholder="Enter Remark"
                                                            rows="3"
                                                        />
                                                        <small class="text-danger">{{ errors[0] }}</small>
                                                    </validation-provider>
                                                </b-form-group>
                                            </b-col>

                                            <b-col md="4">
                                                <b-form-group
                                                    label="Person Proof"
                                                    label-for="Person Proof"
                                                >
                                                    <validation-provider
                                                        #default="{ errors }"
                                                        name="Person Proof"
                                                        rules=""
                                                    >
                                                        <b-form-file
                                                            v-model="data.person_proof"
                                                            accept="image/jpeg, image/png, image/gif"
                                                            drop-placeholder="Drop file here..."
                                                            placeholder="Choose a file or drop it here..."
                                                            @change="onAuthorisedPersonProofImageChange"
                                                        />
                                                        <small class="text-danger">{{ errors[0] }}</small>
                                                        <span v-if="selectperson_proofImg!=''">
                      <div class="image-holder">
                        <img
                            :src="selectperson_proofImg"
                            alt=""
                            height="100"
                            width="100"
                        >

                      </div>
                    </span>
                                                    </validation-provider>
                                                </b-form-group>
                                            </b-col>
                                            <b-col md="4">
                                                <b-form-group
                                                    label="Company ID Proof"
                                                    label-for="Company ID Proof"
                                                >
                                                    <validation-provider
                                                        #default="{ errors }"
                                                        name="Company ID Proof"
                                                    >
                                                        <b-form-file
                                                            ref="file"
                                                            v-model="data.id_proof"
                                                            accept="image/jpeg, image/png, image/gif"
                                                            drop-placeholder="Drop file here..."
                                                            placeholder="Choose a file or drop it here..."
                                                            @change="onIdProofImageChange"
                                                        />
                                                        <small class="text-danger">{{ errors[0] }}</small>
                                                        <span v-if="selectid_proofImg">
                      <div class="image-holder">
                        <img
                            :src="selectid_proofImg"
                            alt=""
                            height="100"
                            width="100"
                        >
                      </div>
                    </span>
                                                        <span v-if="data.id_proofImg">
                      <div class="image-holder">
                        <img
                            :src="data.id_proofImg"
                            alt=""
                            height="100"
                            width="100"
                        >

                      </div>
                    </span>
                                                    </validation-provider>
                                                </b-form-group>
                                            </b-col>
                                            <b-col md="4">
                                                <b-form-group
                                                    label="Company Address Proof"
                                                    label-for="Company Address Proof"
                                                >
                                                    <validation-provider
                                                        #default="{ errors }"
                                                        name="Company Address Proof"
                                                    >
                                                        <b-form-file
                                                            v-model="data.companyProof"
                                                            accept="image/jpeg, image/png, image/gif"
                                                            drop-placeholder="Drop file here..."
                                                            no-drop
                                                            placeholder="Choose a file or drop it here..."
                                                            @change="onCompanyAddressProofImageChange"
                                                        />
                                                        <small class="text-danger">{{ errors[0] }}</small>
                                                    </validation-provider>
                                                    <span v-if="selectcompanyProofImg">
                    <div class="image-holder">
                      <img
                          :src="selectcompanyProofImg"
                          alt=""
                          height="100"
                          width="100"
                      >

                    </div>
                  </span>
                                                    <span v-if="data.companyProofImg">
                    <div class="image-holder">
                      <img
                          :src="data.companyProofImg"
                          alt=""
                          height="100"
                          width="100"
                      >

                    </div>
                  </span>
                                                </b-form-group>
                                            </b-col>


                                            <!-- login button -->
                                            <b-col cols="12">
                                                <b-button
                                                    class="float-right"
                                                    type="submit"
                                                    variant="primary"
                                                    @click.prevent="validationForm"
                                                >
                                                    Send Request
                                                </b-button>
                                            </b-col>
                                        </b-row>
                                    </b-form>
                                </validation-observer>


                            </b-card>
                        </b-col>
                    </b-row>
                </b-overlay>
            </section>


        </b-modal>
        <b-modal
            ref="FollowupModal"

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
                    Followup Form
                </h3>
                <b-overlay

                    id="overlay-background"
                    :blur="blur"
                    :opacity="opacity"
                    :show="this.followupoverlay"
                    :variant="variant"
                    rounded="sm"
                >
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
                                <validation-observer ref="simpleRules">
                                    <b-form>
                                        <b-row>
                                            <b-col md="4">
                                                <b-form-group
                                                    class="text-danger"
                                                    label="Followup Status *"
                                                    label-for="Followup Status *"
                                                >
                                                    <validation-provider
                                                        #default="{ errors }"
                                                        name="Followup Status *"
                                                        rules="required"
                                                    >
                                                        <v-select
                                                            v-model="fData.followup_status"
                                                            :options="expiry_followup_status"
                                                            :reduce="(expiry_followup_status) => expiry_followup_status.value"
                                                            :state="fData.followup_status === null ? false : true"
                                                            label="text"
                                                            @input="followup_status(fData.followup_status)"

                                                        />
                                                        <small class="text-danger">{{ errors[0] }}</small>

                                                    </validation-provider>
                                                </b-form-group>
                                            </b-col>
                                            <b-col md="4">
                                                <b-form-group
                                                    class="text-danger"
                                                    label="Select Employer Feedback *"
                                                    label-for="Select Employer Feedback *"
                                                >
                                                    <validation-provider
                                                        #default="{ errors }"
                                                        name="Select Employer Feedback *"
                                                        rules="required"
                                                    >
                                                        <v-select
                                                            v-model="fData.feedback"
                                                            :options="employer_feedback"
                                                            :reduce="(employer_feedback) => employer_feedback.value"
                                                            :state="fData.feedback === null ? false : true"
                                                            label="text"

                                                        />
                                                        <small class="text-danger">{{ errors[0] }}</small>

                                                    </validation-provider>
                                                </b-form-group>
                                            </b-col>
                                            <b-col md="4">
                                                <b-form-group
                                                    class="text-danger"
                                                    label="Select Target Source *"
                                                    label-for="Select Target Source *"
                                                >
                                                    <validation-provider
                                                        #default="{ errors }"
                                                        name="Select Target Source *"
                                                        rules="required"
                                                    >
                                                        <v-select
                                                            v-model="fData.target_source"
                                                            :options="followup_source"
                                                            :reduce="(followup_source) => followup_source.value"
                                                            :state="fData.target_source === null ? false : true"
                                                            label="text"

                                                        />
                                                        <small class="text-danger">{{ errors[0] }}</small>

                                                    </validation-provider>
                                                </b-form-group>
                                            </b-col>
                                            <b-col md="4">
                                                <b-form-group
                                                    class="text-danger"
                                                    label="Select Employer Mindset *"
                                                    label-for="Select Employer Mindset *"
                                                >
                                                    <validation-provider
                                                        #default="{ errors }"
                                                        name="Select Employer Mindset *"
                                                        rules="required"
                                                    >
                                                        <v-select
                                                            v-model="fData.mindset"
                                                            :options="employer_mindset"
                                                            :reduce="(employer_mindset) => employer_mindset.value"
                                                            :state="fData.mindset === null ? false : true"
                                                            label="text"

                                                        />
                                                        <small class="text-danger">{{ errors[0] }}</small>

                                                    </validation-provider>
                                                </b-form-group>
                                            </b-col>
                                        </b-row>
                                        <!--followup extend-->
                                        <b-row v-if="isAdextend==true">
                                            <b-col md="6">
                                                <b-form-group
                                                    class="text-danger"
                                                    label="Select Followup Date *"
                                                    label-for="Select Followup Date *"
                                                >
                                                    <validation-provider
                                                        #default="{ errors }"
                                                        name="Select Followup Date *"
                                                        rules="required"
                                                    >
                                                        <b-form-datepicker
                                                            v-model="fData.followup_date"
                                                            :date-disabled-fn="dateDisabled"
                                                            :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                                                            locale="en"
                                                        />
                                                        <small class="text-danger">{{ errors[0] }}</small>

                                                    </validation-provider>
                                                </b-form-group>
                                            </b-col>
                                            <b-col md="6">
                                                <b-form-group
                                                    class="text-danger"
                                                    label="Select Reason *"
                                                    label-for="Select Reason *"
                                                >
                                                    <validation-provider
                                                        #default="{ errors }"
                                                        name="Select Reason *"
                                                        rules="required"
                                                    >
                                                        <v-select
                                                            v-model="fData.adextend"
                                                            :options="adextend"
                                                            :reduce="(adextend) => adextend.value"
                                                            :state="fData.adextend === null ? false : true"
                                                            label="text"

                                                        />
                                                        <small class="text-danger">{{ errors[0] }}</small>

                                                    </validation-provider>
                                                </b-form-group>
                                            </b-col>
                                        </b-row>
                                        <!--followup close-->

                                        <b-row v-if="isAdclode==true">
                                            <b-col md="6">
                                                <b-form-group
                                                    class="text-danger"
                                                    label="Select Reason For Ad Closing *"
                                                    label-for="Select Reason For Ad Closing *"
                                                >
                                                    <validation-provider
                                                        #default="{ errors }"
                                                        name="Select Reason For Ad Closing *"
                                                        rules="required"
                                                    >
                                                        <v-select
                                                            v-model="fData.adclosing"
                                                            :options="adclosing"
                                                            :reduce="(adclosing) => adclosing.value"
                                                            :state="fData.adclosing === null ? false : true"
                                                            label="text"

                                                        />
                                                        <small class="text-danger">{{ errors[0] }}</small>

                                                    </validation-provider>
                                                </b-form-group>
                                            </b-col>
                                        </b-row>
                                        <!-- remark -->
                                        <b-row>
                                            <b-col cols="12">
                                                <b-form-group
                                                    class="text-danger"
                                                    label="Remark *"
                                                    label-for="Remark"

                                                >
                                                    <validation-provider
                                                        #default="{ errors }"
                                                        name="remark"
                                                        rules="required"
                                                    >
                                                        <b-form-textarea
                                                            id="textarea-default"
                                                            v-model="fData.remark"
                                                            placeholder="Enter Remark"
                                                            rows="3"
                                                        />
                                                        <small class="text-danger">{{ errors[0] }}</small>
                                                    </validation-provider>
                                                </b-form-group>
                                            </b-col>
                                        </b-row>
                                        <b-row>
                                            <b-col cols="12">
                                                <b-button
                                                    class="float-right"
                                                    type="submit"
                                                    variant="primary"
                                                    @click.prevent="validationFollowupForm"
                                                >
                                                    Save
                                                </b-button>
                                            </b-col>
                                        </b-row>

                                    </b-form>
                                </validation-observer>


                            </b-card>
                        </b-col>
                    </b-row>
                </b-overlay>
            </section>


        </b-modal>
        <b-modal
            ref="FollowupModalHistory"

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
            title="EMPLOYER Followup HISTORY"

            title-tag="div"
        >


            <section>

                <h3 class="mt-1 mb-2 text-primary text-center">
                    Followup History
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
                        :columns="followupHistoryColum"
                        :pagination-options="{
        enabled: true,
        perPage:pageLength
      }"
                        :rows="followupHistoryDetailsrows"

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

                    </vue-good-table>

                    <template #code>
                        {{ codeColumnSearch }}
                    </template>
                </b-card>
            </section>


        </b-modal>
    </b-card-code>
</template>

<script>
import BCardCode from '@core/components/b-card-code/BCardCode.vue'
import AddButtonIcons from '@/views/elements/AddButtonIcons'
import AddButtonIconsLink from '@/views/elements/AddButtonIconsLink.vue'
import TotalResults from '@/views/elements/TotalResults'
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
    BFormCheckbox,
    BFormDatepicker,
    BFormFile,
    BFormGroup,
    BFormInput,
    BFormSelect,
    BFormTextarea,
    BIcon,
    BImg,
    BLink,
    BListGroup,
    BListGroupItem,
    BModal,
    BOverlay,
    BPagination,
    BRow,
    BTab,
    BTabs,
    VBModal,
    VBTooltip,
} from 'bootstrap-vue'

import { VueGoodTable } from 'vue-good-table'

import store from '@/store'
import { ValidationObserver, ValidationProvider } from 'vee-validate'
import Button from '@/views/components/button/Button'
import { AgGridVue } from 'ag-grid-vue'
// import { reactive, onMounted, ref } from "vue"
import 'ag-grid-community/styles/ag-grid.css' // Core grid CSS, always needed
import 'ag-grid-community/styles/ag-theme-alpine.css'
import { getUserData } from '@/auth/utils' // Optional theme CSS
import ViewPrivateEmployer from '@/views/elements/ViewPrivateEmployer.vue'
import { required } from '@validations'

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

const userData = getUserData()
export default {
    props: ['id'],
    components: {
        BFormDatepicker,
        BIcon,
        Button,
        BOverlay,
        BButton,
        BFormTextarea,
        BRow,
        BCol,
        BModal,
        VBModal,
        BFormFile,
        BImg,
        BLink,
        BForm,
        BCardCode,
        AddButtonIcons,
        AddButtonIconsLink,
        ViewPrivateEmployer,
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
            template: '<span><b-button class="btn-icon rounded-circle" style="margin: 3px" variant="gradient-warning"  @click="openModel(params.data.employer_id)"><feather-icon icon="EyeIcon" /></b-button><b-button class="btn-icon rounded-circle" style="margin: 3px" target="_blank" :href="hostNameEdit+\'/employer/private/?id=\'+params.data.employer_id" variant="gradient-danger"  ><feather-icon icon="EditIcon" /></b-button><b-button class="btn-icon rounded-circle" style="margin: 3px" variant="gradient-primary"  @click="openFollowupModel(params.data.employer_id,params.data.id)"><feather-icon icon="CalendarIcon" /></b-button><b-button class="btn-icon rounded-circle" style="margin: 3px" variant="gradient-success"  @click="FollowupModalHistory(params.data.employer_id,params.data.id)"><feather-icon icon="ClockIcon" /></b-button><button class="btn btn-info"  @click="openEditModel(params.data.employer_id)">Edit Request</button></span>',
            components: {
                ViewPrivateEmployer,
                BButton,
                BModal,
                VBModal,
                BFormCheckbox,
            },
            directives: {
                Ripple,
            },
            data() {
                return {
                    linkURL: null,
                    dir: false,
                    editModelId: '',
                    bodyBgVariant: 'light',
                    bodyTextVariant: 'dark',
                    footerBgVariant: 'warning',
                    footerTextVariant: 'dark',
                    headerBgVariant: 'primary',
                    headerTextVariant: 'light',
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
            methods: {

                openModel(value) {
                    // alert(value)
                    var rowNode = this.params.node.rowIndex
                    // this.model_id = value
                    // alert(this.model_id)
                    this.$bvModal.show('modal-footer-lg')
                    this.$root.$emit('openModel()', value)

                },
                openEditModel(value) {
                    // alert(value)
                    var rowNode = this.params.node.rowIndex
                    this.editModelId = value
                    // alert(this.model_id)
                    // this.$bvModal.show('EditRequestmodal')
                    // this.$refs['EditRequestmodal'].show();
                    this.$root.$emit('openEditModel()', value)

                },
                openFollowupModel(value, exid) {
                    // alert(value)
                    var rowNode = this.params.node.rowIndex
                    this.editModelId = value
                    // alert(this.model_id)
                    // this.$bvModal.show('EditRequestmodal')
                    // this.$refs['EditRequestmodal'].show();
                    this.$root.$emit('openFollowupModel()', value, exid)

                },
                FollowupModalHistory(value, exid) {
                    // alert(value)
                    var rowNode = this.params.node.rowIndex
                    this.editModelId = value
                    // alert(this.model_id)
                    // this.$bvModal.show('EditRequestmodal')
                    // this.$refs['EditRequestmodal'].show();
                    this.$root.$emit('FollowupModalHistory()', value, exid)

                },
                openModell(value) {
                    var rowNode = this.params.node.rowIndex
                    this.$root.$emit('openModel()', value)
                },
                callDeleteMethod(url, data, requestMethod) {
                    this.$root.$emit('callDeleteMethod()', url, data, requestMethod)
                },
            },
            created() {
                if (location.hostname == 'localhost') {
                    this.hostNameEdit = 'http://' + location.hostname + ':3000'
                } else {
                    this.hostNameEdit = 'https://' + location.hostname + '/jobspro/'
                }
            },
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
    directives: {
        Ripple,
        'b-tooltip': VBTooltip,
    },
    data() {
        return {
            data: {

                remark: '',
                id: '',
                feedback: '',
                adextend: '',
                adclosing: '',
                followup_date: '',
                followup_status: '',
                mindset: '',
                target_source: '',
                inby: userData.id,
                filterTable: '',
                filterTableUser: '',
                assignFilter: '',

            },
            useridCheck: userData.id,
            fData: {
                feedback: '',
                adextend: '',
                adclosing: '',
                followup_date: '',
                followup_status: '',
                mindset: '',
                target_source: '',
                inby: userData.id,
                employer_id: '',
                executive_id: '',
                remark: '',
            },
            gridApi: null,
            showBtn: false,
            showBtnLen: 0,
            selectedData: [],
            selectUser: [],
            rowSelection: null,
            columnApi: null,
            columnDefs: null,
            overlay: false,
            followupoverlay: false,
            isAdextend: false,
            isAdclode: false,
            showEditmodel: true,
            variant: 'light',
            opacity: 0.85,
            blur: '2px',
            followupHistoryColum: [

                {

                    label: 'Employer ID',
                    width: '150px',
                    field: 'employer_id',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Verified',
                    },

                },

                {

                    label: 'Employer Feedback',
                    width: '150px',
                    field: 'employerfeedback',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Verified',
                    },

                },

                {

                    label: 'Employer Mindset',
                    width: '150px',
                    field: 'employermindset',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Verified',
                    },

                },

                {

                    label: 'Expiry Followup Status',
                    width: '150px',
                    field: 'last_followup_status',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Verified',
                    },

                },

                {

                    label: 'Followup Source',
                    width: '150px',
                    field: 'followupsource',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Verified',
                    },

                },
                {

                    label: 'Ad Closing',
                    width: '150px',
                    field: 'masteradclosing',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Verified',
                    },

                },
                {

                    label: 'Ad Extend',
                    width: '150px',
                    field: 'masteradextend',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Verified',
                    },

                },

            ],
            selectcompanyProofImg: [],
            followupHistoryDetailsrows: [],
            selectid_proofImg: [],
            selectperson_proofImg: [],
            getTableData: [],
            searchTerm: '',
            rowsView: [],
            expiry_followup_status: [],
            adextend: [],
            adclosing: [],
            employer_feedback: [],
            followup_source: [],
            employer_mindset: [],
            model_id: '',
            editModelId: '',
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
            userArray: [],
            mindsetArray: [],
            followupsourceArray: [],
            employerfeedbackArray: [],
            expiryfollowupstatusArray: [],
            companytypeArray: [],
            areasArray: [],
            userIds: [],
            total: 0,

            bodyBgVariant: 'light',
            bodyTextVariant: 'dark',
            footerBgVariant: 'warning',
            footerTextVariant: 'dark',
            headerBgVariant: 'primary',
            headerTextVariant: 'light',
            required,
            assignStatus: [
                {
                    id: '1',
                    title: 'Assign'
                },
                {
                    id: '2',
                    title: 'UnAssign'
                },
            ],
            select_Table: [],
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
        this.$root.$on('hereCheck()', () => {
            this.hereCheck()
        })
        this.$root.$on('openEditModel()', (data) => {
            this.openEditModel(data)
        })
        this.$root.$on('openFollowupModel()', (data, exid) => {
            this.openFollowupModel(data, exid)
        })
        this.$root.$on('FollowupModalHistory()', (data, exid) => {
            this.FollowupModalHistory(data, exid)
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

    },
    async created() {
        this.rowSelection = 'multiple'
        this.isRowSelectable = (rowNode) => {
            console.log(rowNode.data)
        }
        this.checkin()
        this.defaultColDef = {
            sortable: true,
            filter: true,
            flex: 0,
            resizable: true,
            editable: false,
        }

        if (userData.id === 156 || userData.id === 203 || userData.id === 228) {
            this.columnDefs = [
                {
                    headerName: 'S.No',
                    valueGetter: 'node.rowIndex + 1',

                },
                {

                    headerCheckboxSelection: true,
                    checkboxSelection: true,
                    showDisabledCheckboxes: true,
                    field: 'employer_id',

                },
                {
                    headerName: '#ID',
                    field: 'id',

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
                    headerName: 'Company ID',
                    field: 'employer_id',

                },
                {
                    headerName: 'Company Name',
                    field: 'cname',

                },
                {
                    headerName: 'Company Register Mobile number ',
                    field: 'phonenumber',
                    width: 200,
                },
                {
                    headerName: 'Company Post Mobile number',
                    field: 'phone_new',

                },
                {
                    headerName: 'Whatsapp Number',
                    field: 'whatsapp',

                },
                {
                    headerName: 'Followup Status',
                    field: 'last_followup_status',

                },
                {
                    headerName: 'Employer Feedback',
                    field: 'last_feedback',

                },
                {
                    headerName: 'Followup Source',
                    field: 'last_target_source',

                },
                {
                    headerName: 'Employer Mindset',
                    field: 'last_mindset',

                },
                {
                    headerName: 'District',
                    field: 'district',

                },
                {
                    headerName: 'Company Type',
                    field: 'company_type',

                },
                {
                    headerName: 'Job Post Count',
                    field: 'overallJobPost',

                },
                {
                    headerName: 'Over All Payment',
                    field: 'overallpaymentAMt',

                },
                {
                    headerName: 'Assign Status',
                    field: 'Assign',

                },
                {
                    headerName: 'Company Status',
                    field: 'companyStatus',

                },
                {
                    headerName: 'Post Status',
                    field: 'jobstatus',

                },
                {
                    headerName: 'Payment Status',
                    field: 'paymentstatus',

                },

                {
                    headerName: 'employer indate',
                    field: 'employerindate',

                },
                {
                    headerName: 'job Expiry Date',
                    field: 'jobExpiryDate',

                },
                {
                    headerName: 'Last Post Date',
                    field: 'lastpostdate',

                },
                {
                    headerName: 'Last Expiry Date',
                    // field: 'ending',
                    field: 'lastexpiry',
                    width: 110,
                },
                {
                    headerName: 'Executive Name',
                    field: 'user_name',

                },
                {
                    headerName: 'Executive ID',
                    field: 'userid',

                },
            ]
        } else {
            this.columnDefs = [
                {
                    headerName: 'S.No',
                    valueGetter: 'node.rowIndex + 1',

                },

                {
                    headerName: '#ID',
                    field: 'id',

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
                    headerName: 'Company ID',
                    field: 'employer_id',

                },
                {
                    headerName: 'Company Name',
                    field: 'cname',

                },
                {
                    headerName: 'Company Register Mobile number ',
                    field: 'phonenumber',
                    width: 200,
                },
                {
                    headerName: 'Company Post Mobile number',
                    field: 'phone_new',

                },
                {
                    headerName: 'Whatsapp Number',
                    field: 'whatsapp',

                },
                {
                    headerName: 'Followup Status',
                    field: 'last_followup_status',

                },
                {
                    headerName: 'Employer Feedback',
                    field: 'last_feedback',

                },
                {
                    headerName: 'Followup Source',
                    field: 'last_target_source',

                },
                {
                    headerName: 'Employer Mindset',
                    field: 'last_mindset',

                },
                {
                    headerName: 'District',
                    field: 'district',

                },
                {
                    headerName: 'Company Type',
                    field: 'company_type',

                },
                {
                    headerName: 'Job Post Count',
                    field: 'overallJobPost',

                },
                {
                    headerName: 'Over All Payment',
                    field: 'overallpaymentAMt',

                },
                {
                    headerName: 'Assign Status',
                    field: 'Assign',

                },
                {
                    headerName: 'Company Status',
                    field: 'companyStatus',

                },
                {
                    headerName: 'Post Status',
                    field: 'jobstatus',

                },
                {
                    headerName: 'Payment Status',
                    field: 'paymentstatus',

                },
                {
                    headerName: 'employer indate',
                    field: 'employerindate',

                },
                {
                    headerName: 'job Expiry Date',
                    field: 'jobExpiryDate',

                },
                {
                    headerName: 'Last Post Date',
                    field: 'lastpostdate',

                },
                {
                    headerName: 'Last Expiry Date',
                    // field: 'ending',
                    field: 'lastexpiry',
                    width: 110,
                },
                {
                    headerName: 'Executive Name',
                    field: 'user_name',

                },
                {
                    headerName: 'Executive ID',
                    field: 'userid',

                },
            ]
        }
        const callgetAllUser = await this.callAxios('/getAllUser', {}, 'post')
        // console.log(callgetAllUser)
        this.userArray = callgetAllUser.data.users
        this.selectUser = callgetAllUser.data.users

        const callgetMindset = await this.callAxios('/getMindset', {}, 'post')
        // console.log(callgetMindset)
        this.mindsetArray = callgetMindset.data.users

        const callgetFollowupSource = await this.callAxios('/getFollowupSource', {}, 'post')
        // console.log(callgetFollowupSource)
        this.followupsourceArray = callgetFollowupSource.data.users

        const callgetEmployerFeedback = await this.callAxios('/getEmployerFeedback', {}, 'post')
        // console.log(callgetEmployerFeedback)
        this.employerfeedbackArray = callgetEmployerFeedback.data.users

        const callgetExpiryFollowupStatus = await this.callAxios('/getExpiryFollowupStatus', {}, 'post')
        // console.log(callgetExpiryFollowupStatus)
        this.expiryfollowupstatusArray = callgetExpiryFollowupStatus.data.users

        const callgetCompanyType = await this.callAxios('/getCompanyType', {}, 'post')
        // console.log(callgetCompanyType)
        this.companytypeArray = callgetCompanyType.data.users

        const callgetAreas = await this.callAxios('/getAreas', {}, 'post')
        // console.log(callgetAreas)
        this.areasArray = callgetAreas.data.users
        this.getPrivateJob()
        // this.getAllUser()
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
    methods: {
        changeUser(userid) {
            this.getPrivateJob()
        },
        onQuickFilterChanged() {
            this.gridApi.setQuickFilter(document.getElementById('quickFilter').value)

        },
        async assignedUser() {

            if (this.data.filterTable == '') return this.toast('Required', 'Select User', 'danger')

            const AssignData = {
                'assignUser': this.data.filterTable,
                'employerIds': this.selectedData,
                'loginId': userData.id,
            }
            this.total = 'Loading...'
            const callAxios = await this.callAxios('/assignExecutiveEmployer', AssignData, 'post')
            if (callAxios.status === 200) {
                // console.log(callAxios.data);
                // this.sweetAlertmethod('success', 'Assign', 'Executive Assigned Successfully', 'btn-primary')
                this.selectedData = []
                this.data.filterTable = ''
                this.data.filterTableUser = ''
                this.showBtn = false
                this.showBtnLen = 0
                // alert('Executive Assigned Successfully');
                this.sweetAlertmethod('success', 'Assign', 'Executive Assigned Successfully', 'btn-primary')
                this.getPrivateJob()
                // this.$router.replace('/executive/master/')
                // this.$router.go(0)
            } else {
                console.log(callAxios)
                // console.log(callAxios.response.data.message);
                // this.toast("Error", callAxios.response.data.message, 'danger');
                this.sweetAlertmethod('error', 'Error', 'Something Went Wrong', 'btn-primary')
            }

        },
        confirmText() {
            this.$swal({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ml-1',
                },
                buttonsStyling: false,
            })
                .then(result => {
                    if (result.value) {
                        this.$swal({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Your file has been deleted.',
                            customClass: {
                                confirmButton: 'btn btn-success',
                            },
                        })
                    }
                })
        },
        onSelectionChanged() {
            this.selectedData = this.gridApi.getSelectedRows()

            if (this.selectedData.length > 0) {
                this.showBtn = true
                this.showBtnLen = this.selectedData.length
            } else {
                this.selectedData = []
                this.showBtn = false
                this.showBtnLen = this.selectedData.length

            }
        },
        checkin() {
            this.isRowSelectable = (rowNode) => {
                console.log(rowNode.data)
            }
        },
        onGridReady(params) {
            params.api.hostname = location.hostname
            params.api.MIX_REQUEST_BASE_URL = process.env.MIX_REQUEST_BASE_URL
            params.api.table = this.data.table
            params.api.showNoRowsOverlay()
            // console.log(params);
            this.gridApi = params.api
            console.log(this.gridApi)
            this.gridColumnApi = params.columnApi
        },
        followup_status(value) {
            // alert(value)
            if (value == 1) {
                this.isAdextend = true
                this.isAdclode = false
            } else if (value == 2) {
                this.isAdextend = false
                this.isAdclode = true
            } else if (value == 5) {

            } else {
                this.isAdextend = false
                this.isAdclode = false
            }
        },
        dateDisabled(ymd, date) {
            // Disable weekends (Sunday = `0`, Saturday = `6`) and
            // disable days that fall on the 13th of the month
            const weekday = date.getDay()
            const day = date.getDate()

            // Return `true` if the date should be disabled
            // return weekday === 0 || weekday === 6 || day === 13 || min === 1
            return weekday === 0
        },
        validationForm() {
            this.$refs.simpleRules.validate()
                .then(async success => {
                    if (success) {
                        this.btnShow = false
                        const data = new FormData()
                        data.append('person_proof', this.data.person_proof)
                        data.append('id_proof', this.data.id_proof)
                        data.append('companyProof', this.data.companyProof)
                        data.append('inby', this.data.inby)
                        data.append('remark', this.data.remark)
                        data.append('id', this.data.id)
                        data.append('inby', this.data.inby)
                        this.overlay = 'show'
                        // return 0
                        console.log(this.data)
                        const config = {
                            'content-type': 'multipart/form-data',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        }
                        const callAxios = await this.callAxiosWithConfig('/saveEditRequest', data, 'post', config)
                        // console.log("00"+callAxios);
                        // console.log(callAxios);
                        // console.log("11");
                        if (callAxios.status === 200) {
                            this.sweetAlertmethod('success', callAxios.data.type, callAxios.data.msg, 'btn-primary')

                            this.data.person_proof = ''
                            this.data.id_proof = ''
                            this.data.companyProof = ''
                            this.data.remark = ''
                            this.selectcompanyProofImg = ''
                            this.selectid_proofImg = ''
                            this.selectperson_proofImg = ''
                            this.$refs['EditRequestmodal'].hide()
                            this.overlay = false
                        } else {
                            this.overlay = false
                            this.btnShow = true
                            this.sweetAlertmethod('error', 'Alert', callAxios.response.data.message, 'btn-primary')

                        }
                    }
                })
        },
        validationFollowupForm() {
            this.$refs.simpleRules.validate()
                .then(async success => {
                    if (success) {
                        this.btnShow = false

                        this.followupoverlay = true
                        // return 0
                        console.log(this.fData)

                        const callAxios = await this.callAxios('/saveFollowup', this.fData, 'post')
                        // console.log("00"+callAxios);
                        // console.log(callAxios);
                        // console.log("11");
                        if (callAxios.status === 200) {
                            this.sweetAlertmethod('success', callAxios.data.type, callAxios.data.msg, 'btn-primary')
                            this.fData.employer_id = ''
                            this.fData.executive_id = ''
                            this.fData.followup_status = ''
                            this.fData.feedback = ''
                            this.fData.followup_date = ''
                            this.fData.target_source = ''
                            this.fData.adextend = ''
                            this.fData.mindset = ''
                            this.fData.adclosing = ''
                            this.fData.remark = ''
                            this.fData.inby = ''
                            this.$refs['FollowupModal'].hide()
                            this.followupoverlay = false
                        } else {
                            this.followupoverlay = false
                            this.sweetAlertmethod('error', 'Alert', callAxios.response.data.message, 'btn-primary')

                        }
                    }
                })
        },

        async getPrivateJob() {
            this.total = 'Loading...'
            this.data.table = 'jobs'
            this.data.jobtype = [1]
            this.data.verify = this.data.filterverified
            this.data.userid = this.data.filterTableUser
            const callAxios = await this.callAxios('/getExecutiveEmployer', this.data, 'post')
            if (callAxios.status === 200) {
                console.log(callAxios.data)
                var newArray = callAxios.data.executive

// console.log(newArray)
                newArray.forEach((value, index) => {
                    // arr.push(value);
                    // console.log(value);
                    // console.log(index);
                    let last_feedback_string = ''
                    let followup_status_string = ''
                    let last_mindset_string = ''
                    let last_target_source_string = ''
                    let user_name_string = ''
                    let district_string = ''
                    let company_type_string = ''
                    let Assign_string = ''
                    let is_block_string = ''
                    let last_followup_status = value['last_followup_status']
                    let last_feedback = value['last_feedback']
                    let last_mindset = value['last_mindset']
                    let last_target_source = value['last_target_source']
                    let user_name = value['user_name']
                    let district = value['district']
                    let company_type = value['company_type']
                    let Assign = value['assigned']
                    let is_block = value['companyStatus']
                    if (last_followup_status > 0) {
                        followup_status_string = this.expiryfollowupstatusArray.find(food => food.id === last_followup_status).source_name
                    } else {
                        followup_status_string = ''
                    }

                    if (last_feedback > 0) {
                        last_feedback_string = this.employerfeedbackArray.find(food => food.id === last_feedback).source_name
                    } else {
                        last_feedback_string = ''
                    }

                    if (last_mindset > 0) {
                        last_mindset_string = this.mindsetArray.find(food => food.id === last_mindset).source_name
                    } else {
                        last_mindset_string = ''
                    }

                    if (last_target_source > 0) {
                        last_target_source_string = this.followupsourceArray.find(food => food.id === last_target_source).source_name
                    } else {
                        last_target_source_string = ''
                    }

                    if (user_name > 0) {
                        user_name_string = this.userArray.find(food => food.id === user_name).title
                    } else {
                        user_name_string = ''
                    }

                    if (district > 0) {

                        district_string = this.areasArray.find(food => food.id === district).district
                    } else {
                        district_string = ''
                    }

                    if (company_type > 0) {
                        company_type_string = this.companytypeArray.find(food => food.id === company_type).name
                    } else {
                        company_type_string = ''
                    }

                    if (Assign) {
                        Assign_string = 'Assigned'
                    } else {
                        Assign_string = 'Unassigned'
                    }

                    if (is_block) {
                        is_block_string = 'Blocked'
                    } else {
                        is_block_string = 'Active'
                    }

                    value['last_followup_status'] = followup_status_string
                    value['last_feedback'] = last_feedback_string
                    value['last_mindset'] = last_mindset_string
                    value['last_target_source'] = last_target_source_string
                    value['user_name'] = user_name_string
                    value['district'] = district_string
                    value['company_type'] = company_type_string
                    value['assigned'] = Assign_string
                    value['companyStatus'] = is_block_string

                    // this.newArray[index]['last_followup_status']=followup_status_string

                })
                console.log(callAxios.data)
                // this.total = this.newArray.length;
                this.total = callAxios.data.executive.length
                this.rowData = callAxios.data.executive
                this.getTableData = callAxios.data.executive
            } else {
                // console.log(callAxios.response.data.message);
                // this.toast("Error", callAxios.response.data.message, 'danger');
                this.sweetAlertmethod('error', 'Error', callAxios.response.data.message, 'btn-primary')
            }
        },
        getAllUser() {

            axios.post('/getAllUser', '')
                .then(res => {
                    // console.log(res.data)
                    this.select_Table = res.data
                })
        },
        onFilterTextBoxChanged() {
            // console.log(document.getElementById('searchTerm').value)
            this.gridApi.setQuickFilter(
                document.getElementById('searchTerm').value
            )
        },

        onBtnExportDataAsExcel() {
            this.gridApi.exportDataAsExcel()

        },
        onBtnExport() {
            const params = {
                suppressQuotes: false,
            }
            this.gridApi.exportDataAsCsv(params)
            // console.log(this.gridApi)
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
            // this.$bvModal.show('modal-footer-lg')

            // this.getDataGovent(value, 'private')

        },
        hereCheck() {
            // alert('gs');
            console.log('pandiya_check')

        },
        openEditModel(value) {
            // alert('gs');
            this.editModelId = value
            this.data.id = value
            this.data.person_proof = ''
            this.data.id_proof = ''
            this.data.companyProof = ''
            this.data.remark = ''
            this.selectcompanyProofImg = ''
            this.selectid_proofImg = ''
            this.selectperson_proofImg = ''
            this.overlay = false
            this.$refs['EditRequestmodal'].show()
        },
        openFollowupModel(value, exid) {
            // alert(exid);
            this.fData.employer_id = value
            this.fData.executive_id = exid
            this.editModelId = value
            this.data.id = value
            this.data.person_proof = ''
            this.data.id_proof = ''
            this.data.companyProof = ''
            this.data.remark = ''
            this.selectcompanyProofImg = ''
            this.selectid_proofImg = ''
            this.selectperson_proofImg = ''
            this.followupoverlay = false

            axios.post('/getFollowupStatus', '')
                .then(res => {
                    // console.log(res.data)
                    this.expiry_followup_status = res.data.expiry_followup_status
                    this.employer_feedback = res.data.employer_feedback
                    this.followup_source = res.data.followup_source
                    this.employer_mindset = res.data.employer_mindset
                    this.adextend = res.data.adextend
                    this.adclosing = res.data.adclosing
                })
            this.$refs['FollowupModal'].show()
        },
        FollowupModalHistory(value, exid) {
            // alert(exid);
            this.history =
                {
                    'empId': value,
                    'exid': exid,
                }

            axios.post('/getFollowupHistory', this.history)
                .then(res => {
                    console.log(res.data)
                    this.followupHistoryDetailsrows = res.data
                })
            this.$refs['FollowupModalHistory'].show()
        },

        onAuthorisedPersonProofImageChange(e) {
            // console.log('here')
            const files = e.target.files[0]
            // console.log(e.target.files[0]);

            this.data.person_proof = e.target.files[0]
            this.selectperson_proofImg = URL.createObjectURL(files)
            // console.log( this.data.id_proof);
        },
        onIdProofImageChange(e) {
            // console.log('here')
            const files = e.target.files[0]
            // console.log(e.target.files[0])

            this.data.id_proof = e.target.files[0]
            this.selectid_proofImg = URL.createObjectURL(files)
        },
        onCompanyAddressProofImageChange(e) {
            // console.log('here')
            const files = e.target.files[0]
            // console.log(e.target.files[0]);

            this.data.companyProof = e.target.files[0]
            this.selectcompanyProofImg = URL.createObjectURL(files)
        },

    },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
@import '~@resources/scss/vue/libs/vue-good-table.scss';
</style>
