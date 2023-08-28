<template>
    <b-card-code title="Private Employer">


        <!-- input search -->
        <div class="custom-search justify-content-end">
            <b-row>

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
                            name="Private-Employer.xls"
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
            :fixed-header="false"
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
              <b-dropdown-item
                  :href="hostNameEdit+'/employer/private/?id='+props.row.id"
              >

                <feather-icon
                    class="mr-50"
                    icon="Edit2Icon"
                />
                <span>Edit</span>
              </b-dropdown-item>
              <b-dropdown-item @click="callDeleteMethod('/deletePrivateEmployer', data={id:props.row.id}, 'post')">
                <feather-icon
                    class="mr-50"
                    icon="TrashIcon"
                />
                <span>Delete</span>
              </b-dropdown-item>
                <span v-if="props.row.is_block==0">
                    <b-dropdown-item
                        @click="callBlockMethod('/blockPrivateEmployer', data={id:props.row.id}, 'post','Yes, Block it!','Blocked')"
                    >
                <feather-icon
                    class="mr-50"
                    icon="AlertOctagonIcon"
                />
                <span>Block</span>
              </b-dropdown-item>
                </span>
                <span v-else>
                    <b-dropdown-item
                        @click="callUnblockMethod('/unblockPrivateEmployer', data={id:props.row.id}, 'post','Yes, Unblock it!','Unblock')"
                    >
                <feather-icon
                    class="mr-50"
                    icon="AlertOctagonIcon"
                />
                <span>Unblock</span>
              </b-dropdown-item>
                </span>

            </b-dropdown>
          </span>
        </span>
                <span v-else-if="props.column.field === 'history'">
          <b-button
              v-ripple.400="'rgba(255, 255, 255, 0.15)'"
              class="btn-icon rounded-circle m-auto d-block"
              variant="success"
              @click="showModalHistroy(props.row.id)"

          >
          <feather-icon icon="EyeIcon"/>
        </b-button>
        </span>
                <span v-else-if="props.column.field === 'verified'">

                      <b-badge v-if="props.row.verified === 1"
                               class="badge-glow"
                               variant="success"
                      >
        Verified
      </b-badge>

                     <b-badge v-if="props.row.verified === 0"
                              class="badge-glow"
                              variant="danger"
                     >
        Not Verified
      </b-badge>


        </span>
                <span v-else-if="props.column.field === 'viewcan'">
                    <span
                        v-if="props.row.is_com_id_proof === 1 && props.row.is_com_add_proof === 1 && props.row.is_person_proof === 1  "
                    >
<span v-if="props.row.isupdate === 1 && props.row.empstatus === 1 ">
<b-button
    class="btn-icon rounded-circle "
    variant="gradient-success"
>
          <feather-icon icon="CheckCircleIcon"/>
        </b-button>
</span>
                        <span
                            v-else-if="props.row.empstatus === 1 && props.row.isupdate === 2 && props.row.isdelay === 0"
                        >
                            <b-button
                                v-b-tooltip.hover.v-warning
                                class="btn-icon rounded-circle "
                                title="Already Verified "
                                variant="gradient-warning"
                            >
          <feather-icon icon="CheckCircleIcon"/>
        </b-button>

                         <b-button

                             v-b-tooltip.hover.v-info
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
                                @click="verifyMethod('/verifyPrivateEmployer', data={id:props.row.id}, 'post','Yes, Verify it!','Verified')"
                            >
          <feather-icon icon="CheckCircleIcon"/>
        </b-button>

                         <b-button

                             v-b-tooltip.hover.v-info
                             class="btn-icon rounded-circle "
                             title="View Candidate Profile Eligible "
                             variant="outline-info"

                         >

                              <feather-icon icon="UserCheckIcon"/>
      </b-button>
                        </span>
                    </span>

<span v-else-if="props.row.empstatus === 1 && props.row.isupdate === 2 && props.row.isdelay === 0">
<b-button
    v-b-tooltip.hover.v-warning
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
          @click="verifyMethod('/verifyPrivateEmployer', data={id:props.row.id}, 'post','Yes, Verify it!','Verified')"
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

                                        {{ this.hdata.website != 'undefined' ? this.hdata.website : '' }} pandiya
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
    VBTooltip,
    BCard,
    BCardText,
    BCardTitle,
    BImg,
    BLink,
} from 'bootstrap-vue'
import {
    required, url, alpha, alphaDash
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
    props: ['companyId'],
    components: {
        Button,
        BButton,
        BRow,
        BImg,
        BCol,
        BLink,
        BModal,
        VBModal,
        BForm,
        BCardCode,
        BCard,
        BCardText,
        BCardTitle,
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
        VBTooltip,
    },
    directives: {
        Ripple,
        'b-tooltip': VBTooltip,
    },
    data() {
        return {
            json_fields: {
                '#ID': 'id',
                'Company Name': 'company-name',
                'Mobile Number': 'phonenumber',
                'City': 'area_name',
                'District': 'district',
            },
            data: {
                id: '',
                companyname: '',
                image: '',
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
            pageLength: 10,
            dir: false,
            hostNameEdit: '',
            codeColumnSearch,
            solidColor: [
                {
                    bg: 'primary',
                    title: 'Primary card title'
                },
                {
                    bg: 'secondary',
                    title: 'Secondary card title'
                },
                {
                    bg: 'success',
                    title: 'Success card title'
                },
                {
                    bg: 'danger',
                    title: 'Danger card title'
                },
                {
                    bg: 'warning',
                    title: 'Warning card title'
                },
                {
                    bg: 'info',
                    title: 'Info card title'
                },
            ],
            columns: [

                {
                    label: '#ID',
                    width: '150px',
                    field: 'id',
                    type: 'number',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search id',
                    },
                },
                {
                    label: 'Action',
                    width: '150px',
                    field: 'action',

                },
                {
                    label: 'History',
                    width: '150px',
                    field: 'history',
                },
                {
                    label: 'Verified',
                    width: '150px',
                    field: 'verified',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Verified',
                    },
                },
                {

                    label: 'View Candidate Profile',
                    width: '150px',
                    field: 'viewcan',

                },
                {

                    label: 'Image',
                    width: '150px',
                    field: 'image',

                },
                {

                    label: 'Company Name(English)',
                    width: '150px',
                    field: 'companyname',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Company Name(English)',
                    },
                },
                {

                    label: 'Company Name(Tamil)',
                    width: '150px',
                    field: 'companynameTamil',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Company Name(Tamil)',
                    },
                },
                {

                    label: 'Company Type',
                    width: '150px',
                    field: 'companytype',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Company Type',
                    },
                },
                {

                    label: 'Phone',
                    width: '150px',
                    field: 'phonenumber',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Phone',
                    },
                },
                {

                    label: 'District',
                    width: '150px',
                    field: 'district',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search District',
                    },
                },
                {

                    label: 'City',
                    width: '150px',
                    field: 'area_name',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search City',
                    },
                },
                {

                    label: 'State',
                    width: '150px',
                    field: 'state',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search State',
                    },
                },
                {

                    label: 'Type',
                    width: '150px',
                    field: 'emptype',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Type',
                    },
                },
                {

                    label: 'GST',
                    width: '150px',
                    field: 'gst',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search GST',
                    },
                },
                {

                    label: 'Proof',
                    width: '150px',
                    field: 'proof',

                },
                {

                    label: 'Blocking Reason',
                    width: '150px',
                    field: 'blocking_reason',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search Blocking Reason',
                    },
                },
                {

                    label: 'In By',
                    width: '150px',
                    field: 'inby',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'In By',
                    },
                },
                {

                    label: 'In Date',
                    width: '150px',
                    field: 'cdate',
                    dateInputFormat: 'YYYY-mm-dd HH:MM:SS',
                    dateOutputFormat: 'dd-MM-yyyy',
                    filterOptions: {
                        enabled: true,
                        placeholder: 'Search In Date',
                    },
                },

            ],
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
            rows: [],
            jobDetailsrows: [],
            paymentDetailsrows: [],
            searchTerm: '',
            addModel: false,
            emailValue: '',
            name: '',
            required,
            url,
            alpha,
            alphaDash,
            total: 0,
            bodyBgVariant: 'light',
            bodyTextVariant: 'dark',
            footerBgVariant: 'warning',
            footerTextVariant: 'dark',
            headerBgVariant: 'primary',
            headerTextVariant: 'light',
            show: false,
            variants: ['primary', 'secondary', 'success', 'warning', 'danger', 'info', 'light', 'dark'],
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
        if (location.hostname == 'localhost') {
            this.hostNameEdit = 'https://' + location.hostname + '/'
        } else {
            this.hostNameEdit = 'https://' + location.hostname + '/jobspro/'
        }
        this.total = 'Loading...'
        axios.post('/employer-private')
            .then(res => {
                console.log(res.data)
                this.total = res.data.length
                this.rows = res.data
            })
    },
    methods: {
        async showModal(value) {
            if (value) {
                this.data.id = value
                const callAxios = await this.callAxios('/getSingleGovtEmployer', { id: value }, 'post')
                console.log(callAxios)
                this.data.companyname = callAxios.data[0]['company-name']
                this.data.image = callAxios.data[0].image

                this.$refs['addForm'].show()
            } else {
                this.data = {
                    id: '',
                    companyname: '',
                    image: '',
                }
            }
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
            pdfdoc.save('Private-Employer.pdf')
        },
        startDownload() {
            alert('show loading')
        },
        finishDownload() {
            alert('hide loading')
        },
        callDeleteMethod(url, data, requestMethod) {
            console.log('999' + this.sweetAlertDeleteMethod(url, data, requestMethod))
        },
        callBlockMethod(url, data, requestMethod, btnname, rentxt) {
            console.log('999' + this.sweetAlertcommon(url, data, requestMethod, btnname, rentxt))
        },
        callUnblockMethod(url, data, requestMethod, btnname, rentxt) {
            console.log('999' + this.sweetAlertcommon(url, data, requestMethod, btnname, rentxt))
        },
        verifyMethod(url, data, requestMethod, btnname, rentxt) {
            console.log('999' + this.sweetAlertcommon(url, data, requestMethod, btnname, rentxt))
        },

        async showModalHistroy(value) {
            console.log(value)
            if (value) {
                this.data.id = value
                const callAxios = await this.callAxios('/privateEmployerHistory', { id: value }, 'post')
                console.log(callAxios.data.employerDeails[0])
                this.hdata = callAxios.data.employerDeails[0]
                this.jobDetailsrows = callAxios.data.jobs_details
                this.paymentDetailsrows = callAxios.data.payment_details
                console.log(paymentDetailsrows)
                const address_check = callAxios.data.employerDeails[0].address.split('#@#')
                this.hdata.address = address_check[0]
                this.$refs['my-modal'].show()
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
@import '~@resources/scss/vue/libs/vue-good-table.scss';
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';

.modal .modal-header .close {
    background: none !important;
}

.modal-content {
    border: 2px solid #7367f0 !important;

}

</style>
