<template>
    <b-card-code :title=title>
        <!-- input search -->
        <div class="custom-search justify-content-end">
            <b-row v-if="page!='search'" class="mb-3">
                <!--Select indate -->
                <b-col md="4">
                    <b-form-group>
                        <label class="required">Select Create Date</label>
                        <date-range-picker
                            ref="picker"
                            v-model="data.postedDate"
                            :autoApply="dateRangeConf.autoApply"
                            :locale-data="{ firstDay: 1, format: 'dd-mm-yyyy' }" :maxDate="dateRangeConf.maxDate"
                            :minDate="dateRangeConf.minDate"
                            :opens="dateRangeConf.opens"
                            :showDropdowns="dateRangeConf.showDropdowns"
                            :showWeekNumbers="dateRangeConf.showWeekNumbers"
                            :singleDatePicker="dateRangeConf.singleDatePicker"
                            :timePicker="dateRangeConf.timePicker"
                            :timePicker24Hour="dateRangeConf.timePicker24Hour"
                        >
                        </date-range-picker>
                        <b-button
                            v-if="data.postedDate"
                            @click="clearFields('postedDate')"
                        >Clear
                        </b-button>
                    </b-form-group>
                </b-col>
                <b-col md="4">
                    <div class="form-label-group">
                        <b-form-input
                            id="floating-label1"
                            v-model="data.filterSearch"
                            placeholder="Search Employer..."
                        />
                        <label for="floating-label1">Search Employer...</label>
                    </div>
                </b-col>
                <b-col md="4">
                    <b-button
                        variant="primary"
                        @click="searchEmployerFliter"
                    >
                        Search
                    </b-button>
                </b-col>

            </b-row>
            <div v-if="loadingShow==true" class="text-center mb-5">
                <b-spinner
                    v-for="variant in variants"
                    :key="variant"
                    :variant="variant"
                    class="mr-1"
                    type="grow"
                />

            </div>
            <b-row v-if="page=='search'">
                <b-col md="3">
                    <b-form-group>
                        <label>Table</label>
                        <v-select
                            v-model="data.filterField"
                            :options="selectField"
                            :reduce="item=>item.id"
                            label="title"
                            placeholder="All"
                        />
                    </b-form-group>
                </b-col>
                <b-col
                    class="mb-3"
                    md="3"
                >
                    <label for="input-valid1">Search Here:</label>
                    <b-form-input
                        id="input-valid1"
                        v-model="search_text"
                        :state="search_text.length > 0"
                        placeholder="Search Here..."
                        @keyup.enter="getPrivateJob()"
                    />

                </b-col>
            </b-row>
            <b-row>
                <b-col v-if="page=='private' || page=='appName'" md="3">
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
                    <total-results :countText="total"></total-results>
                </b-col>
                <b-col class="mt-n2" md="2">
                    Export To :
                    <span>
                        <button
                            @click="onBtnExport()"
                        >
                            <img src="@/assets/images/icons/xls.png" width="50">
                        </button>
                    </span>
                </b-col>
                <b-col md="4">
                    <b-form-group>
                        <div class="d-flex align-items-center">
                            <label class="mr-1">Search in the Table</label>
                            <b-form-input
                                id="searchTerm"
                                v-model="searchTerm"
                                class="d-inline-block"
                                placeholder="Search in the Table"
                                type="text"
                                v-on:input="onFilterTextBoxChanged()"
                            />

                        </div>
                    </b-form-group>
                </b-col>
            </b-row>
        </div>
        <ag-grid-vue
            :columnDefs="columnDefs"
            :defaultColDef="defaultColDef"
            :overlayNoRowsTemplate="overlayNoRowsTemplate"
            :rowData="rowData"
            animateRows="true"
            class="ag-theme-alpine"
            rowSelection="multiple"
            style="height: 500px"
            @grid-ready="onGridReady"
        >
        </ag-grid-vue>
        <b-button @click="onBtForEachNodeAfterFilterAndSort">
            Print Job IDs
        </b-button>
        <p v-if="this.selectedIds">{{ this.selectedIds.join() }}</p>
        <view-private-jobs :id="this.model_id" :table=this.table></view-private-jobs>
        <b-modal
            ref="my-modal"
            modal-class="modal-primary"

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

                                    <b-col v-if="this.hdata.mDesignation" class=" border-dark" md="8">
                                        {{ this.hdata.mDesignation }}
                                    </b-col>

                                    <b-col v-else class=" border-dark" md="8">
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
                                        {{ this.hdata.pan_card != 'undefined' ? this.hdata.pan_card : '' }}
                                    </b-col>
                                </b-row>
                                <b-row class="w-100 border">
                                    <b-col class="border-warning" md="4">
                                        GST Number
                                    </b-col>
                                    <b-col class=" border-dark" md="8">
                                        {{ this.hdata.gst != 'undefined' ? this.hdata.gst : '' }}
                                    </b-col>
                                </b-row>
                                <b-row class="w-100 border">
                                    <b-col class="border-warning" md="4">
                                        Website
                                    </b-col>
                                    <b-col class=" border-dark" md="8">
                                        {{ this.hdata.website != 'undefined' ? this.hdata.website : '' }}
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
                                        <span v-if="this.hdata.person_proof!=''">
          <b-link class="card">

                                            <div class="img-container w-50 mx-auto py-75">
                                                <b-img
                                                    :src="this.hdata.person_proof"
                                                    fluid
                                                />
<span v-if="this.hdata.is_person_proof === 1" class="d-block text-center">
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
                                                <span v-else-if="this.hdata.is_person_proof === 2"
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
                                        <span v-if="this.hdata.company_proof!=''">
      <b-link class="card">

                                            <div class="img-container w-50 mx-auto py-75">
                                                <b-img
                                                    :src="this.hdata.company_proof"
                                                    fluid
                                                />

                                                  <span v-if="this.hdata.is_com_add_proof==1"
                                                        class="d-block text-center"
                                                  >
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
                                                   <span v-else-if="this.hdata.is_com_add_proof==2"
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
                                        <span v-if="this.hdata.id_proof!=''">
                                            <b-link class="card">


                                            <div class="img-container w-50 mx-auto py-75">
                                                <b-img
                                                    :src="this.hdata.id_proof"
                                                    fluid
                                                />

                                                <span v-if="this.hdata.is_com_id_proof==1" class="d-block text-center">
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
                                                 <span v-else-if="this.hdata.is_com_id_proof==2"
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
    </b-card-code>
</template>

<script>
import { VueGoodTable } from 'vue-good-table'
import BCardCode from '@core/components/b-card-code/BCardCode.vue'
import TotalResults from '@/views/elements/TotalResults'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'

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
    BIcon,
    BLink,
    BModal,
    BPagination,
    BRow,
    VBModal,
    BCard,
    BCardText,
    BCardTitle,
    VBTooltip,
    BSpinner,
    BImg,
} from 'bootstrap-vue'

import 'ag-grid-community/styles/ag-grid.css'
import 'ag-grid-community/styles/ag-theme-alpine.css'
import { AgGridVue } from 'ag-grid-vue'
import ViewPrivateJobs from '@/views/elements/ViewPrivateJobs.vue'
import Treeselect from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'
import DateRangePicker from 'vue2-daterange-picker'
//you need to import the CSS manually
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'
import setData from 'lodash/_setData'
import store from '@/store'

let onBtForEachNodeAfterFilterAndSort
export default {
    props: ['title', 'table', 'page', 'filterTable'],
    components: {
        BIcon,
        BButton,
        BRow,
        BCol,
        BModal,
        VBModal,
        BForm,
        BSpinner,
        BCardCode,
        TotalResults,
        BAvatar,
        VBTooltip,
        BBadge,
        BPagination,
        BFormGroup,
        BFormInput,
        BFormSelect,
        BDropdown,
        BDropdownItem,
        vSelect,
        AgGridVue,
        ViewPrivateJobs,
        Treeselect,
        VueGoodTable,
        BCard,
        BCardText,
        BCardTitle,
        DateRangePicker,
        BImg,
        BLink,

        imageComponent: {
            template: '<img style="width: 100px" :src={{this.params.data.image}}>',
        },
        actionsComponent: {
            template: `
                <span>
 <b-button
     v-ripple.400="'rgba(255, 255, 255, 0.15)'"
     variant="primary"
     target="_blank"
     :href="hostNameEdit+'/employer/private/?id='+params.data.id"
 >
        Edit
      </b-button>
 <b-button
     v-ripple.400="'rgba(255, 255, 255, 0.15)'"
     variant="danger"
     target="_blank"
     @click="callDeleteMethod('/deletePrivateEmployer', data={id:params.data.id}, 'post')"
 >
        Delete
      </b-button>
 <span v-if="params.data.is_block==0">
     <b-button
         v-ripple.400="'rgba(255, 255, 255, 0.15)'"
         variant="warning"
         @click="callBlockMethod('/blockPrivateEmployer', data={id:params.data.id}, 'post','Yes, Block it!','Blocked')"

     >
        Block
      </b-button>
 </span>
                <span v-else>
     <b-button
         v-ripple.400="'rgba(255, 255, 255, 0.15)'"
         variant="warning"
         target="_blank"
         @click="callUnblockMethod('/unblockPrivateEmployer', data={id:params.data.id}, 'post','Yes, Unblock it!','Unblock')"
     >
        UnBlock
      </b-button>
 </span>



          </span>
            `,
            components: {
                ViewPrivateJobs,
                BButton,
                BModal,
                VBModal,
                BIcon,
                BRow,
                BCol,
                BForm,
                BAvatar,
                BBadge,
                BPagination,
                BFormGroup,
                BFormInput,
                BFormSelect,
                BDropdown,
                BDropdownItem,
                vSelect,
                BLink,
                VBTooltip,
            },
            data() {
                return {
                    linkURL: null,
                    idVariable: null,
                }
            },
            mounted() {
                console.log(location.hostname)
                if (location.hostname == 'localhost') {
                    this.linkURL = '/'
                } else {
                    this.linkURL = '/jobspro/'
                }
                if (this.params.api.table == 'jobs') {
                    this.idVariable = 'id'
                } else {
                    this.idVariable = 'draft_id'
                }
                // console.log(this.params)
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
                callDeleteMethod(url, dataa, requestMethod) {
                    // console.log(url, data, requestMethod)
                    this.$root.$emit('callDeleteMethod()', url, dataa, requestMethod)
                },
                callBlockMethod(url, data, requestMethod, btnname, rentxt) {
                    // console.log(url, data, requestMethod)
                    this.$root.$emit('callBlockMethod()', url, data, requestMethod, btnname, rentxt)
                },
                callUnblockMethod(url, data, requestMethod, btnname, rentxt) {
                    // console.log(url, data, requestMethod)
                    this.$root.$emit('callUnblockMethod()', url, data, requestMethod, btnname, rentxt)
                },
                switchbox(id) {
                    // console.log(id)
                    this.$root.$emit('switchbox()', id)
                },
                switchboxInvert(id) {
                    // console.log(id)
                    this.$root.$emit('switchboxInvert()', id)
                },
            },
            created() {
                if (location.hostname == 'localhost') {
                    this.hostNameEdit = 'http://' + location.hostname + ':3000'
                } else {
                    this.hostNameEdit = 'https://' + location.hostname + '/tjobspro/'
                }

            },
            directives: {
                Ripple,
            },
        },
        historyComponent: {
            template: `
                <span>
 <b-button
     v-ripple.400="'rgba(255, 255, 255, 0.15)'"
     variant="warning"
     class="btn-icon rounded-circle"
     @click="showModalHistroy(params.data.id)"
 >
          <feather-icon icon="RefreshCwIcon"/>
        </b-button>


          </span>
            `,
            components: {
                ViewPrivateJobs,
                BButton,
                BModal,
                VBModal,
                BIcon,
                BRow,
                BCol,
                BForm,
                BAvatar,
                BBadge,
                BPagination,
                BFormGroup,
                BFormInput,
                BFormSelect,
                BDropdown,
                BDropdownItem,
                vSelect,
                BLink,
                VBTooltip,
            },
            data() {
                return {
                    linkURL: null,
                    idVariable: null,
                }
            },
            mounted() {
                // console.log(location.hostname)
                // if (location.hostname == 'localhost') {
                //     this.linkURL = '/'
                // } else {
                //     this.linkURL = '/jobspro/';
                // }
                // if (this.params.api.table == 'jobs') {
                //     this.idVariable = 'id';
                // } else {
                //     this.idVariable = 'draft_id';
                // }
                // // console.log(this.params)
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
                showModalHistroy(value) {
                    // alert(value)
                    var rowNode = this.params.node.rowIndex
                    // this.model_id = value
                    // alert(this.model_id)
                    this.$bvModal.show('my-modal')
                    this.$root.$emit('showModalHistroy()', value)

                },
                callDeleteMethod(url, dataa, requestMethod) {
                    // console.log(url, data, requestMethod)
                    this.$root.$emit('callDeleteMethod()', url, dataa, requestMethod)
                },
                switchbox(id) {
                    // console.log(id)
                    this.$root.$emit('switchbox()', id)
                },
                switchboxInvert(id) {
                    // console.log(id)
                    this.$root.$emit('switchboxInvert()', id)
                },
            },
            created() {
                if (location.hostname == 'localhost') {
                    this.hostNameEdit = 'http://' + location.hostname + ':3000'
                } else {
                    this.hostNameEdit = 'https://' + location.hostname + '/tjobspro/'
                }
            },
            directives: {
                Ripple,
            },
        },
        verifyComponent: {
            template: `
                <span>

                      <b-badge v-if="params.data.verified === 1"
                               class="badge-glow"
                               variant="success">
        Verified
      </b-badge>

                     <b-badge v-if="params.data.verified === 0"
                              class="badge-glow"
                              variant="danger">
        Not Verified
      </b-badge>


        </span>
            `,
            components: {
                ViewPrivateJobs,
                BButton,
                BModal,
                VBModal,
                BIcon,
                BRow,
                BCol,
                BForm,
                BAvatar,
                BBadge,
                BPagination,
                BFormGroup,
                BFormInput,
                BFormSelect,
                BDropdown,
                BDropdownItem,
                vSelect,
                VBTooltip,
                BLink,
            },
            data() {
                return {
                    linkURL: null,
                    idVariable: null,
                }
            },
            mounted() {
                // console.log(location.hostname)
                // if (location.hostname == 'localhost') {
                //     this.linkURL = '/'
                // } else {
                //     this.linkURL = '/jobspro/';
                // }
                // if (this.params.api.table == 'jobs') {
                //     this.idVariable = 'id';
                // } else {
                //     this.idVariable = 'draft_id';
                // }
                // // console.log(this.params)
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
                showModalHistroy(value) {
                    // alert(value)
                    var rowNode = this.params.node.rowIndex
                    // this.model_id = value
                    // alert(this.model_id)
                    this.$bvModal.show('my-modal')
                    this.$root.$emit('showModalHistroy()', value)

                },
                callDeleteMethod(url, dataa, requestMethod) {
                    // console.log(url, data, requestMethod)
                    this.$root.$emit('callDeleteMethod()', url, dataa, requestMethod)
                },
                switchbox(id) {
                    // console.log(id)
                    this.$root.$emit('switchbox()', id)
                },
                switchboxInvert(id) {
                    // console.log(id)
                    this.$root.$emit('switchboxInvert()', id)
                },
            },
            created() {
                if (location.hostname == 'localhost') {
                    this.hostNameEdit = 'http://' + location.hostname + ':3000'
                } else {
                    this.hostNameEdit = 'https://' + location.hostname + '/tjobspro/'
                }
            },
            directives: {
                Ripple,
            },
        },
        viewcanComponent: {
            template: `
                <span>
                <span
                    v-if="params.data.is_com_id_proof === 1 && params.data.is_com_add_proof === 1 && params.data.is_person_proof === 1  ">
<span v-if="params.data.isupdate === 1 && params.data.empstatus === 1 ">
<b-button
    class="btn-icon rounded-circle "
    variant="gradient-success"
>
          <feather-icon icon="CheckCircleIcon"/>
        </b-button>
</span>
                        <span
                            v-else-if="params.data.empstatus === 1 && params.data.isupdate === 2 && params.data.isdelay === 0">
                            <b-button
                                class="btn-icon rounded-circle "
                                title="Already Verified "
                                variant="gradient-warning"
                            >
          <feather-icon icon="CheckCircleIcon"/>
        </b-button>

                         <b-button

                             class="btn-icon rounded-circle "
                             title="View Candidate Profile Eligible "
                             variant="outline-info"

                         >

                              <feather-icon icon="UserCheckIcon"/>
      </b-button>
                        </span>
<span v-else>
                            <b-button
                                class="btn-icon rounded-circle "
                                variant="gradient-success"
                                @click="verifyMethod('/verifyPrivateEmployer', data={id:params.data.id}, 'post','Yes, Verify it!','Verified')"
                            >
          <feather-icon icon="CheckCircleIcon"/>
        </b-button>

                         <b-button

                             class="btn-icon rounded-circle "
                             title="View Candidate Profile Eligible "
                             variant="outline-info"

                         >

                              <feather-icon icon="UserCheckIcon"/>
      </b-button>
                        </span>
                    </span>

                <span
                    v-else-if="params.data.empstatus === 1 && params.data.isupdate === 2 && params.data.isdelay === 0">
<b-button
    class="btn-icon rounded-circle "
    title="Already Verified"
    variant="gradient-warning"
>
          <feather-icon icon="CheckCircleIcon"/>
        </b-button>
    <b-button
        class="btn-icon rounded-circle "
        disabled=""
        variant="gradient-danger"
    >
          <feather-icon icon="XCircleIcon"/>
        </b-button>
</span>
                <span v-else>
<b-button class="btn-icon rounded-circle "
          variant="gradient-success"
          @click="verifyMethod('/verifyPrivateEmployer', data={id:params.data.id}, 'post','Yes, Verify it!','Verified')"
>
          <feather-icon icon="CheckCircleIcon"/>
        </b-button>
    <b-button
        class="btn-icon rounded-circle "
        disabled=""
        variant="gradient-danger"
    >
          <feather-icon icon="XCircleIcon"/>
        </b-button>
</span>
                </span>
            `,
            components: {
                ViewPrivateJobs,
                BButton,
                BModal,
                VBModal,
                BIcon,
                BRow,
                BCol,
                BForm,
                BAvatar,
                BBadge,
                BPagination,
                BFormGroup,
                BFormInput,
                BFormSelect,
                BDropdown,
                BDropdownItem,
                vSelect,
                VBTooltip,
                BLink,
            },
            data() {
                return {
                    linkURL: null,
                    idVariable: null,
                }
            },
            mounted() {
                // console.log(location.hostname)
                // if (location.hostname == 'localhost') {
                //     this.linkURL = '/'
                // } else {
                //     this.linkURL = '/jobspro/';
                // }
                // if (this.params.api.table == 'jobs') {
                //     this.idVariable = 'id';
                // } else {
                //     this.idVariable = 'draft_id';
                // }
                // // console.log(this.params)
            },
            methods: {
                verifyMethod(url, data, requestMethod, btnname, rentxt) {
                    // console.log(url, data, requestMethod)
                    this.$root.$emit('verifyMethod()', url, data, requestMethod, btnname, rentxt)
                },

            },

            directives: {
                Ripple,
            },
        },
        viewCountComponent: {
            template: '<button v-if="params.api.table==\'jobs\'" class="btn btn-danger" @click="openModelViewCount(params.data.id)" style="position: relative;" >View</button>',

            methods: {

                openModelViewCount(value) {
                    var rowNode = this.params.node.rowIndex
                    this.$root.$emit('openModelView()', value)
                },
            }
        },
        paymentStatusComponent: {

            template: `
                        <span
                            v-if="params.data.paid_post==1"
                            class="text-success" >
                            {{ params.data.payment_status }} ({{ params.data.txn_amt }})<br>
                            {{ params.data.paid_date }}
                        </span>
                        <span v-else class="text-danger">{{ params.data.payment_status }}</span>
                        `,
        },
        proofComponent: {

            template: '<span v-if="params.data.company_proof_type != 0 && params.data.id_proof_type != 0 && params.data.person_proof_type != 0 && params.data.is_com_add_proof === 1 && params.data.is_com_id_proof === 1 && params.data.is_person_proof === 1" class="text-success" > verified</span><span v-else class="text-danger">Not verified</span>',
        },
    },
    directives: {
        Ripple,
    },
    data() {
        return {
            data: {
                filterSearch: '',
                postedDate: {
                    'startDate': '',
                    'endDate': ''
                },
                expiryDate: {
                    'startDate': '',
                    'endDate': ''
                },
            },
            hdata: {
                addproof_type: '',
                mDesignation: '',
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
            localUserId: JSON.parse(localStorage.getItem('userData')).id,
            inactive: {
                id: '',
                inactiveReason: '',
            },
            active: {
                id: '',
            },
            loadingShow: false,
            gridApi: null,
            columnApi: null,
            columnDefs: null,
            rowData: null,
            overlayLoadingTemplate: null,
            overlayNoRowsTemplate: null,
            totalData: {
                'current_page': 1,
                'total': 1,
            },
            defaultColDef: null,
            last_url: null,
            searchTerm: '',
            search_text: '',
            total: '',
            model_id: '',
            jobDetailsrows: [],
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
            paymentDetailsrows: [],
            bodyBgVariant: 'light',
            bodyTextVariant: 'dark',
            footerBgVariant: 'warning',
            footerTextVariant: 'dark',
            headerBgVariant: 'danger',
            headerTextVariant: 'light',
            variants: ['primary', 'secondary', 'success', 'warning', 'danger', 'info', 'light', 'dark'],
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
            selectField: [
                {
                    id: 1,
                    title: 'Job'
                },
                {
                    id: 2,
                    title: 'Number'
                },
                {
                    id: 3,
                    title: 'Address'
                },
                {
                    id: 4,
                    title: 'Email'
                },
                {
                    id: 5,
                    title: 'Company'
                },
            ],
            selectArea: [],
            inactiveReason: [],
            jobtitle_options: [],
            selectedIds: [],
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
        }
    },
    async mounted() {
        this.$root.$on('openModel()', (data) => {
            this.openModel(data)
        })
        this.$root.$on('showModalHistroy()', (data) => {
            this.showModalHistroy(data)
        })
        this.$root.$on('openModelView()', (data) => {
            this.openModelView(data)
        })

        this.$root.$on('callDeleteMethod()', (url, data, requestMethod) => {
            this.callDeleteMethod(url, data, requestMethod)
        })
        this.$root.$on('callBlockMethod()', (url, data, requestMethod, btnname, rentxt) => {
            this.callBlockMethod(url, data, requestMethod, btnname, rentxt)
        })
        this.$root.$on('verifyMethod()', (url, data, requestMethod, btnname, rentxt) => {
            this.verifyMethod(url, data, requestMethod, btnname, rentxt)
        })
        this.$root.$on('callUnblockMethod()', (url, data, requestMethod, btnname, rentxt) => {
            this.callUnblockMethod(url, data, requestMethod, btnname, rentxt)
        })

        this.$root.$on('switchbox()', (id) => {
            this.switchbox(id)
        })
        this.$root.$on('switchboxInvert()', (id) => {
            this.switchboxInvert(id)
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
    async created() {
        // console.log("123" + process.env.MIX_REQUEST_BASE_URL+"456")
        const callAxios = await this.callAxios('/getJobViewMaster', {}, 'post')
        this.jobtitle_options = callAxios.data.searchJobTitle_v1
        this.selectArea = callAxios.data.areaDrop
        this.inactiveReason = callAxios.data.inactive_reason

        this.overlayLoadingTemplate =
            '<span style="padding: 10px; border: 2px solid #444; background: lightgoldenrodyellow;" class="ag-overlay-loading-center">Please wait while your rows are loading</span>'
        this.overlayNoRowsTemplate =
            '<span style="padding: 10px; border: 2px solid #444; background: lightgoldenrodyellow;">No rows</span>'
        this.last_url = window.location.pathname.split('/')
            .pop()
        switch (this.last_url) {
            case 'admin':
                this.data.from_admin = 1
                break
            case 'employer':
                this.data.from_admin = 2
                break
            case 'private':
                this.data.filterTable = 'live'
                this.data.jobtype = [1]
                break
            case 'appName':
                this.data.filterTable = 'live'
                this.data.jobtype = [2, 3]
                break
        }        // this.$bvModal.show('modal-footer-lg')
        // console.log(this.$bvModal);
        this.columnDefs = [
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
                    if (this.localUserId == 1 || this.localUserId == 2 || this.localUserId == 102 || this.localUserId == 110 || this.localUserId == 165 || this.localUserId == 197 || this.localUserId == 24 || this.localUserId == 163 || this.localUserId == 205 || this.localUserId == 123 || this.localUserId == 80 || this.localUserId == 179 || this.localUserId == 208 || this.localUserId == 215 || this.localUserId == 156) {
                        return {
                            component: 'actionsComponent'
                        }
                    }

                },
                cellStyle: { 'white-space': 'normal' },
                autoHeight: true,
            },
            {
                headerName: 'History',
                cellRendererSelector: params => {
                    return {
                        component: 'historyComponent'
                    }

                },
                cellStyle: { 'white-space': 'normal' },
                autoHeight: true,
            },
            {
                headerName: 'Verified',
                cellRendererSelector: params => {
                    return {
                        component: 'verifyComponent'
                    }

                },
                cellStyle: { 'white-space': 'normal' },
                autoHeight: true,
            },
            {
                headerName: 'View Candidate Profile',
                cellRendererSelector: params => {
                    return {
                        component: 'viewcanComponent'
                    }

                },
                cellStyle: { 'white-space': 'normal' },
                autoHeight: true,
            },
            {
                headerName: 'Logo',
                cellRendererSelector: params => {
                    if (params.data.logo && params.data.logo != 'undefined') {
                        return {
                            component: 'imageComponent',
                        }
                    }
                },
                autoHeight: true,
                width: 140,
            },
            {
                headerName: 'Company Name (English)',
                field: 'companyname',
                width: 200,
            },
            {
                headerName: 'Company Name (Tamil)',
                field: 'companynameTamil',
                width: 200,
            },
            {
                headerName: 'Company Type',
                field: 'companytype',
                width: 250,
            },
            {
                headerName: 'Phone',
                field: 'phonenumber',
                width: 250,
            },
            {
                headerName: 'District',
                field: 'district',
                width: 250,
            },
            {
                headerName: 'City',
                field: 'area_name',
                width: 250,
            },
            {
                headerName: 'State',
                field: 'state',
                width: 250,
            },
            {
                headerName: 'GST',
                field: 'gst',
                width: 250,
            },
            {
                headerName: 'In By',
                field: 'uname',
                width: 250,
            },
            {
                headerName: 'In Date',
                // field: 'ending',
                cellClass: 'dateUK',
                valueFormatter: (params) => {
                    if (params.data.cdate && params.data.cdate != '0000-00-00') {
                        return new Date(params.data.cdate).toLocaleDateString('en-IN')
                    } else {
                        return ''
                    }
                },
                width: 110,
            },
        ]
        this.defaultColDef = {
            sortable: true,
            filter: true,
            flex: 0,
            resizable: true,
            editable: true,
        }
        this.getPrivateJob()
    },
    methods: {
        printNode(node, index) {
            this.selectedIds.push(node.data.id)
            // alert()
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
        async onBtForEachNodeAfterFilterAndSort() {
            this.selectedIds.length = 0
            await this.gridApi.forEachNodeAfterFilterAndSort(this.printNode)
            // console.log(this.selectedIds.join());
            // alert()
        },
        async getPrivateJob() {
            // console.log(this.gridApi);
            if (this.gridApi) {
                this.gridApi.showLoadingOverlay()
            }
            this.total = 'Loading...'
            axios.post('/employer-private')
                .then(res => {
                    console.log(res.data)
                    // this.total = res.data.length;
                    // this.rows = res.data
                    this.searchTerm = ''
                    this.totalData = res.data
                    this.total = res.data.length
                    this.rowData = setData(res.data)
                })
            // console.log(selectedNodes);
        },
        async searchEmployerFliter() {
            this.loadingShow = true
            var array_check = this.data
            console.log(this.data)
            var search = this.data.filterSearch
            if (this.data.postedDate.startDate) {
                var dateFliter = this.data.postedDate.startDate
            }

            // if(search!='' || dateFliter!='' ){
            //     console.log('not empty')
            // }else{
            //     console.log('empty')
            // }

            if (search != '' || dateFliter != '') {

                const callAxios = await this.callAxios('/employer-private', this.data, 'post')
                this.searchTerm = ''
                this.totalData = callAxios.data
                this.total = callAxios.data.length
                this.rowData = setData(callAxios.data)
                this.loadingShow = false
            } else {
                this.sweetAlertmethod('warning', 'Warning!', 'Please Select Date OR Search String', 'btn-warning')
            }
        },

        onFilterTextBoxChanged() {
            // console.log(document.getElementById('searchTerm').value)
            this.gridApi.setQuickFilter(
                document.getElementById('searchTerm').value
            )
        },
        onBtnExport() {
            const params = {
                suppressQuotes: false,
            }
            this.gridApi.exportDataAsCsv(params)
        },
        open() {
            // alert();
            // this.model_id = '1'
            // this.$bvModal.show('modal-footer-lg')
            // this.$refs['view-jobs-modal'].show();
        },
        openModel(value) {
            // alert("123" + value);
            this.model_id = value
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
        callDeleteMethod(url, data, requestMethod) {
            // console.log("123", url, data, requestMethod)
            this.sweetAlertDeleteMethod(url, data, requestMethod, 'ag-grid')

        },
        callUnblockMethod(url, data, requestMethod, btnname, rentxt) {
            console.log('999' + this.sweetAlertcommon1(url, data, requestMethod, btnname, rentxt))

        },
        callBlockMethod(url, data, requestMethod, btnname, rentxt) {
            console.log('999' + this.sweetAlertcommon1(url, data, requestMethod, btnname, rentxt))

        },
        verifyMethod(url, data, requestMethod, btnname, rentxt) {
            // console.log(data);
            console.log('999' + this.sweetAlertcommon(url, data, requestMethod, btnname, rentxt))
        },
        switchbox(id) {
            this.inactive.id = id
            // console.log("123", url, data, requestMethod)
            this.$bvModal.show('switchbox')
        },
        async switchboxInvert(id) {
            this.active.id = id
            const callAxios = await this.callAxios('/activeJobs', this.active, 'post')
            if (callAxios.status === 200) {
                this.sweetAlertmethod('success', 'Activated!', '', 'btn-success')
            } else {
                this.sweetAlertmethod('warning', 'Warning!', callAxios.response.data.message, 'btn-success')
                console.log('1')
            }
        },
        hideModal() {
            // alert()
            this.$bvModal.hide('switchbox')
            this.inactive = {
                id: '',
                inactiveReason: '',
            }
        },
        async inactiveForm() {
            console.log(this.rowData)
            if (this.inactive.id && this.inactive.inactiveReason) {
                this.sweetAlertDeleteMethod('/inactiveJobs', this.inactive, 'post', 'ag-grid')
                this.$bvModal.hide('switchbox')
                this.inactive = {
                    id: '',
                    inactiveReason: '',
                }
            } else {
                this.sweetAlertmethod('warning', 'Warning!', 'Select Inactive Reason', 'btn-success')
            }
        },
        clearFields(dateField) {
            switch (dateField) {
                case 'postedDate':
                    this.data.postedDate.startDate = ''
                    this.data.postedDate.endDate = ''
                    break
                case 'expiryDate':
                    this.data.expiryDate.startDate = ''
                    this.data.expiryDate.endDate = ''
                    break
            }
        },
        deselectAlljoblocation() {
            this.data.joblocation = null
        },
        deselectAllcandidateLocation() {
            this.data.candidateLocation = null
        },
        async showModalHistroy(value) {
            this.loadingShow = true
            console.log(value)
            if (value) {
                this.data.id = value
                const callAxios = await this.callAxios('/privateEmployerHistory', { id: value }, 'post')
                console.log(callAxios.data.employerDeails[0])
                this.hdata = callAxios.data.employerDeails[0]
                // this.hdata.is_com_id_proof = 1
                this.jobDetailsrows = callAxios.data.jobs_details
                this.paymentDetailsrows = callAxios.data.payment_details
                console.log(callAxios)
                const address_check = callAxios.data.employerDeails[0].address.split('#@#')
                this.hdata.address = address_check[0] + ', ' + callAxios.data.employerDeails[0].area_name + ', ' + address_check[2] + ', ' + address_check[3]
                this.$refs['my-modal'].show()
                this.loadingShow = false
            } else {
                this.data = {
                    id: '',
                    companyname: '',
                    image: '',
                }
            }
        },
        proofAction(url, data, requestMethod, btnname, rentxt) {
            console.log('999' + this.sweetAlertVerifycommon(url, data, requestMethod, btnname, rentxt))
        },
    },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
@import '~@resources/scss/vue/libs/vue-good-table.scss';
</style>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
