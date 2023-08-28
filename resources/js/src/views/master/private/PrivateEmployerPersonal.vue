<template>
    <b-card-code>
        <validation-observer ref="simpleRules">
            <b-form enctype="multipart/form-data">
                <b-row class="border-3 border-primary pt-1">

                    <b-col md="12">
                        <h1 class="text-center text-primary m-2">
                            Personal Details
                        </h1>
                    </b-col>
                    <!--mobile -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Mobile Number </label>
                            <validation-provider
                                #default="{ errors }"
                                name="Mobile Number"
                                rules="required|min:10"
                            >
                                <b-form-input
                                    v-model="data.phonenumber"
                                    :state="errors.length > 0 ? false:null"

                                    maxlength="10"
                                    minlength="10"
                                    oninput="this.value=this.value.replace(/[^\d]/,'')"
                                    placeholder="Mobile Number"
                                    type="text"
                                    @input="mobilenumber_check"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>
                    <!--name -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Name </label>
                            <validation-provider
                                #default="{ errors }"
                                name="Name"
                                rules="required"
                            >
                                <b-form-input
                                    v-model="data.firstname"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="Name"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!-- Designation -->

                    <b-col md="4">
                        <div v-if="isdecspiner == true">
                            <b-spinner
                                v-for="variant in variants"
                                :key="variant"
                                :variant="variant"
                                class="mr-1"
                                type="grow"
                            />
                        </div>
                        <b-form-group>
                            <label class="required">Designation </label>
                            <span v-if="isDecTrue==true">  Old Designation : {{ decstring }}</span>
                            <v-select
                                v-model="data.designation"
                                :options="designation"
                                :reduce="(designation) => designation.value"
                                :state="data.designation === null ? false : true"
                                label="text"
                            />

                        </b-form-group>
                    </b-col>

                    <b-col
                        class="mt-2"
                        md="2"
                    >

                        <b-button
                            v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                            class="btn-icon"
                            variant="info"
                            @click="getdes"
                        >
                            <feather-icon icon="RefreshCcwIcon"/>
                        </b-button>
                        <b-button
                            v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                            class="btn-icon ml-1"
                            :href="hostNameEdit+'designation'"
                            target="_blank"
                            variant="success"
                        >
                            <feather-icon icon="PlusIcon"/>
                        </b-button>
                    </b-col>

                    <!-- email -->
                    <b-col md="6">
                        <b-form-group>
                            <label>Email</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Email"
                                rules="email"
                            >
                                <b-form-input
                                    v-model="data.email"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="Email"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!-- Authorised Person Proof Type  -->
                    <b-col md="6">
                        <b-form-group>
                            <label>Authorised Person Proof Type </label>
                            <v-select
                                v-model="data.person_proof_type"
                                :options="PersonProof"
                                :reduce="(PersonProof) => PersonProof.value"
                                :state="data.person_proof_type === null ? false : true"
                                label="text"
                                @input="change_lable"
                            />
                        </b-form-group>
                    </b-col>
                    <!-- Authorised Person Proof  -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Authorised Person Proof </label>
                            <validation-provider
                                #default="{ errors }"
                                name="Authorised Person Proof"
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
                        <span v-if="data.person_proofImg!=''">
                  <div class="image-holder">
                    <img
                        :src="data.person_proofImg"
                        alt=""
                        height="100"
                        width="100"
                    >
                    <b-button
                        variant="gradient-danger"
                        @click="removeImage('person_proofImg')"
                    >
                      <feather-icon
                          class="mr-50"
                          icon="XSquareIcon"
                      />
                      <span class="align-middle">Remove</span>
                    </b-button>
                  </div>
                </span>

                    </b-col>
                    <!--                    whats app number-->
                    <b-col md="6">
                        <b-form-group>
                            <label class="">Whats App Number </label>
                            <validation-provider
                                #default="{ errors }"
                                :rules="{
                      regex : /^[0-9]{10}$/,
                    }"
                                name="Whats App Number"
                            >
                                <b-form-input
                                    v-model="data.whatsapp"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="Whats App Number"
                                    type="number"
                                    @input=""
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                            <a
                                class="badge badge-primary ml-25 text-white"
                                @click="copyNumber"
                            >📋 Copy from
                                Mobile Number</a>
                            <a
                                class="badge badge-secondary ml-25 text-white"
                                @click="clearNumber"
                            >Clear</a>
                        </b-form-group>

                    </b-col>

                    <!-- Authorised Person Proof Type Name -->

                    <b-col md="6">
                        <b-form-group>
                            <label>{{ person_proof_lable }} Number </label>
                            <validation-provider
                                #default="{ errors }"
                                :rules="is_rules"
                                name=""
                            >
                                <b-form-input
                                    v-model="data.person_proof_text"
                                    :placeholder="person_proof_lable"
                                    :state="errors.length > 0 ? false:null"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-row class="border-3 border-primary mt-5 ">
                    <b-col md="12">
                        <h1 class="text-center text-primary m-2">
                            Company Details
                        </h1>
                    </b-col>

                    <!--  Company Name in english-->

                    <b-col md="5">
                        <div v-if="isShow==true">
                            <b-spinner
                                v-for="variant in variants"
                                :key="variant"
                                :variant="variant"
                                class="mr-1"
                                type="grow"
                            />
                        </div>

                        <b-form-group>
                            <label class="required">Company Name in English</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Company Name in English"
                                rules="required"
                            >
                                <b-form-input
                                    v-model="data.companyname"
                                    :disabled="isDisabled"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="Company Name in English"
                                    @keyup="checkLetter"
                                    @paste="onPaste"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>

                        </b-form-group>
                        <b-list-group v-if="ismatchedCompany==true">

                            <b-list-group-item
                                v-for="(item, index) in matchedCompany"
                                :key="item.id"
                                :data-index="index"
                                variant="danger"
                            >
                                {{ item.name }} - {{ item.id }} - {{ item.dist }}
                                <b-button
                                    v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                                    class="btn-icon rounded-circle"
                                    variant="warning"
                                    @click.prevent="viewEmployer(item.id)"
                                >
                                    <feather-icon icon="EyeIcon"/>
                                </b-button>
                                <b-button
                                    v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                                    :href="hostNameEdit+'/employer/private/?id='+item.id"
                                    class="btn-icon rounded-circle"
                                    target="_blank"
                                    variant="primary"
                                >
                                    <feather-icon icon="EditIcon"/>
                                </b-button>
                            </b-list-group-item>

                        </b-list-group>

                    </b-col>
                    <b-col
                        v-if="isCheckbtn==true"
                        md="1"
                    >
                        <b-form-group>
                            <b-button
                                class="float-right ml-1"
                                style="margin-top: 25px"
                                type="submit"
                                variant="primary"
                                @click.prevent="checkcompanyname"
                            >
                                Check
                            </b-button>
                        </b-form-group>
                    </b-col>
                    <!--Company Name in Tamil -->

                    <b-col md="6">
                        <b-form-group>
                            <label class="">Company Name in Telugu</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Company Name in Telugu"
                                rules=""
                            >
                                <b-form-input
                                    v-model="data.tcompanyname"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="Company Name in Telugu"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!--Street Address -->

                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Street Address ( D.No, Floor, Building Name, Street Name and Area
                                Name)</label>
                            <validation-provider
                                #default="{ errors }"
                                :rules="{
                      required,
                    }"
                                name="Street Address"
                            >
                                <b-form-input
                                    v-model="data.address"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="Street Address"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>
                    <!--Landmark -->

                    <b-col md="6">
                        <b-form-group>
                            <label>Landmark</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Landmark"

                            >
                                <b-form-input
                                    v-model="data.landmark"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="Landmark"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>
                    <!-- state -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">State </label>
                            <v-select
                                v-model="data.state"
                                :options="stateSelect"
                                :reduce="(stateSelect) => stateSelect.value"
                                :state="data.state === null ? false : true"
                                label="text"
                                @input="change_dist(data.state,1)"
                            />
                        </b-form-group>
                    </b-col>

                    <!-- District -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">District </label>
                            <v-select
                                v-model="data.district"
                                :options="distSelect"
                                :reduce="(distSelect) => distSelect.value"
                                :state="data.district === null ? false : true"
                                :value="data.district"
                                label="text"
                                @input="check(data.district)"
                            />

                        </b-form-group>
                    </b-col>

                    <!-- city -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">City </label>
                            <v-select
                                v-model="data.city"
                                :options="citySelect"
                                :reduce="(citySelect) => citySelect.value"
                                :state="data.city === null ? false : true"
                                label="text"
                                @input="City_text"
                            />
                        </b-form-group>
                    </b-col>

                    <!-- pincode-->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Pincode</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Pincode"
                                rules="required|min:6"
                            >
                                <b-form-input
                                    v-model="data.pincode"
                                    :state="errors.length > 0 ? false:null"
                                    maxlength="6"
                                    minlength="6"
                                    oninput="this.value=this.value.replace(/[^\d]/,'')"
                                    placeholder="Pincode"
                                    type="text"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>
                    <!--website-->
                    <b-col md="6">
                        <b-form-group>
                            <label>Website</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Website"
                                rules="url"
                            >
                                <b-form-input
                                    v-model="data.website"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="Enter Website"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!-- Company Registration Type -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Company Registration Type </label>
                            <v-select
                                v-model="data.industry_type"
                                :options="industry_type"
                                :reduce="(industry_type) => industry_type.value"
                                :state="data.industry_type === null ? false : true"
                                label="text"
                                @input="cmpaddproof"
                            />
                        </b-form-group>
                    </b-col>

                    <!-- Business Category* -->
                    <b-col md="6">
                        <b-form-group>
                            <label class="required">Business Category </label>
                            <v-select
                                v-model="data.company_type"
                                :options="company_type"
                                :reduce="(company_type) => company_type.value"
                                :state="data.company_type === null ? false : true"
                                label="text"
                            />
                        </b-form-group>
                    </b-col>
                    <!--                    Company ID Proof Type-->
                    <b-col md="6">
                        <b-form-group>
                            <label>Company ID Proof Type </label>
                            <v-select
                                v-model="data.company_proof_type"
                                :options="companyProof"
                                :reduce="(companyProof) => companyProof.value"
                                :state="data.company_proof_type === null ? false : true"
                                label="text"
                            />
                        </b-form-group>
                    </b-col>
                    <!-- Company id Proof  -->
                    <b-col md="6">
                        <b-form-group>
                            <label>Company ID Proof </label>
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
                                <span v-if="selectid_proofImg!=''">
                      <div class="image-holder">
                        <img
                            :src="selectid_proofImg"
                            alt=""
                            height="100"
                            width="100"
                        >
                      </div>
                    </span>
                                <span v-if="data.id_proofImg!=''">
                      <div class="image-holder">
                        <img
                            :src="data.id_proofImg"
                            alt=""
                            height="100"
                            width="100"
                        >
                        <b-button
                            variant="gradient-danger"
                            @click="removeImage('id_proofImg')"
                        >
                          <feather-icon
                              class="mr-50"
                              icon="XSquareIcon"
                          />
                          <span class="align-middle">Remove</span>
                        </b-button>
                      </div>
                    </span>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <b-col md="6"/>

                    <!--                    pancard-->
                    <b-col md="6">
                        <b-form-group>
                            <label>Pancard Number</label>
                            <validation-provider
                                #default="{ errors }"
                                :rules="{
                      regex : /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/
                    }"
                                name="Pancard Number"
                            >
                                <b-form-input
                                    v-model="data.pan_card"
                                    :state="errors.length > 0 ? false:null"
                                    class="text-uppercase"
                                    placeholder="Pancard Number"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>
                    <!--                    Company Address Proof Type-->
                    <b-col md="6">

                        <b-form-group>
                            <label>Company Address Proof Type </label>
                            <v-select
                                v-model="data.addressProof"
                                :options="selcetaddressProof"
                                :reduce="(selcetaddressProof) => selcetaddressProof.value"
                                :state="data.addressProof === null ? false : true"
                                label="text"
                                @input="cmpaddcheck(data.addressProof)"
                            />
                        </b-form-group>
                    </b-col>
                    <!-- Company Address Proof  -->
                    <b-col md="6">
                        <b-form-group>
                            <label>Company Address Proof </label>
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
                            <span v-if="selectcompanyProofImg!=''">
                    <div class="image-holder">
                      <img
                          :src="selectcompanyProofImg"
                          alt=""
                          height="100"
                          width="100"
                      >

                    </div>
                  </span>
                            <span v-if="data.companyProofImg!=''">
                    <div class="image-holder">
                      <img
                          :src="data.companyProofImg"
                          alt=""
                          height="100"
                          width="100"
                      >
                      <b-button
                          variant="gradient-danger"
                          @click="removeImage('companyProofImg')"
                      >
                        <feather-icon
                            class="mr-50"
                            icon="XSquareIcon"
                        />
                        <span class="align-middle">Remove</span>
                      </b-button>
                    </div>
                  </span>
                        </b-form-group>
                    </b-col>
                    <!--company logo-->

                    <b-col md="6">
                        <b-form-group>
                            <label>Company Logo </label>
                            <validation-provider
                                #default="{ errors }"
                                name="Company Logo"
                            >
                                <b-form-file
                                    ref="file"
                                    v-model="data.image"
                                    accept="image/jpeg, image/png, image/gif"
                                    drop-placeholder="Drop file here..."
                                    placeholder="Choose a file or drop it here..."
                                    @change="onIdCompanyImageChange"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>

                                <span v-if="data.company_logo!=''">
                      <div class="image-holder">
                        <img
                            :src="data.company_logo"
                            alt=""
                            height="100"
                            width="100"
                        >
                        <b-button
                            variant="gradient-danger"
                            @click="removeImage('company_logo')"
                        >
                          <feather-icon
                              class="mr-50"
                              icon="XSquareIcon"
                          />
                          <span class="align-middle">Remove</span>
                        </b-button>
                      </div>
                    </span>
                            </validation-provider>
                        </b-form-group>
                    </b-col>
                    <!--gst number-->
                    <b-col md="6">
                        <b-form-group>
                            <label :class="dynamicClass">GST Number</label>
                            <validation-provider
                                #default="{ errors }"
                                :rules="is_gst"
                                name="GST Number"
                            >
                                <b-form-input
                                    v-model="data.gst"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="GST Number"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                </b-row>
                <b-row>
                    <b-col
                        class="mt-1"
                        cols="12"
                    >
                        <span v-if="btnShow">
                        <b-button
                            class="float-right ml-1"
                            type="submit"
                            variant="primary"
                            @click.prevent="validationForm"
                        >
                            Submit
                        </b-button>
                        </span>
                        <b-button
                            class="float-right "
                            type="reset"
                            variant="danger"
                        >
                            Cancel
                        </b-button>
                    </b-col>
                </b-row>
            </b-form>

        </validation-observer>
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
                                    <b-col v-if="this.hdata.pan_card!='undefined'" class=" border-dark" md="8">
                                        {{ this.hdata.pan_card }}
                                    </b-col>
                                </b-row>
                                <b-row class="w-100 border">
                                    <b-col class="border-warning" md="4">
                                        GST Number
                                    </b-col>
                                    <b-col v-if="this.hdata.gst !='undefined'" class=" border-dark" md="8">
                                        {{ this.hdata.gst }}
                                    </b-col>
                                </b-row>
                                <b-row class="w-100 border">
                                    <b-col class="border-warning" md="4">
                                        Website
                                    </b-col>
                                    <b-col v-if="this.hdata.website != 'undefined'" class=" border-dark" md="8">
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
import BCardCode from '@core/components/b-card-code'
import { VueGoodTable } from 'vue-good-table'
import { ValidationObserver, ValidationProvider } from 'vee-validate'
import { getUserData } from '@/auth/utils'

const userData = getUserData()
import {
    BButton,
    BCol,
    BForm,
    BFormFile,
    BPagination,
    BFormGroup,
    BFormInput,
    BFormSelect,
    BListGroup,
    BCard,
    BCardText,
    BCardTitle,
    BListGroupItem,
    BRow,
    BSpinner,
    BImg,
    BLink,
} from 'bootstrap-vue'
import { email, length, max, required, url, } from '@validations'

import vSelect from 'vue-select'
import Ripple from 'vue-ripple-directive'
import store from '@/store'

export default {
    components: {
        BCardCode,
        ValidationProvider,
        ValidationObserver,
        VueGoodTable,
        BPagination,
        BFormInput,
        BListGroupItem,
        BFormSelect,
        BFormGroup,
        BImg,
        BLink,
        BCard,
        BCardText,
        BCardTitle,
        BForm,
        BSpinner,
        max,
        BFormFile,
        BRow,
        BCol,
        BButton,
        vSelect,
        BListGroup,
    },
    directives: {
        Ripple,
    },
    data() {
        return {
            data: {
                firstname: '',
                email: '',
                phonenumber: '',
                landmark: '',
                designation: '',
                person_proof_type: '',
                person_proof_text: '',
                whatsapp: '',
                person_proof: [],
                person_proofImg: '',
                id: '',
                companyname: '',
                tcompanyname: '',
                id_proof: [],
                id_proofImg: '',
                companyProof: [],
                companyProofImg: '',
                company_type: '',
                id_proof_type: '',
                address: '',
                pincode: '',
                gst: '',
                image: [],
                company_logo: '',
                website: '',
                cityText: '',
                industry_type: '',
                pan_card: '',
                company_proof_type: '',
                companyNature: '',
                state: 1,
                district: '',
                city: '',
                type: '1',
                inby: userData.id,

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
            district_id: {
                disid: '',
            },
            proofImg: {
                imgType: '',
                id: '',
            },
            person_proof_type: [],
            number: '',
            btnShow: true,
            person_proof_lable: 'ID Card',
            URL: '',
            value: '',
            hostNameEdit: '',
            required,
            email,
            url,
            length,
            PersonProof: [],
            designation: [],
            industry_type: [],
            companyProof: [],
            selcetaddressProof: [],
            company_type: [],
            distSelect: [],
            stateSelect: [],
            citySelect: [],
            jobDetailsrows: [],
            paymentDetailsrows: [],
            searchTerm: '',
            addModel: false,
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
            dir: false,
            matchedCompany: [],
            ismatchedCompany: false,
            isdecspiner: false,
            isDisabled: false,
            isDisabledvalue: false,
            isCheckbtn: false,
            isShow: false,
            dynamicClass: '',
            is_rules: {},
            is_gst: {},
            selectperson_proofImg: '',
            selectid_proofImg: '',
            selectcompanyProofImg: '',
            isDecTrue: false,
            decstring: '',
            bodyBgVariant: 'light',
            bodyTextVariant: 'dark',
            footerBgVariant: 'warning',
            footerTextVariant: 'dark',
            headerBgVariant: 'primary',
            headerTextVariant: 'light',
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
    mounted() {

    },
    async created() {
        // console.log(userData.id)
        if (location.hostname == 'localhost') {
            // this.hostNameEdit = 'https://' + location.hostname + '/'
            this.hostNameEdit = '/'
        } else {
            this.hostNameEdit = 'https://' + location.hostname + '/jobspro/'
        }
        // this.hostNameEdit = location.hostname;
        console.log(this.hostNameEdit)
        // console.log('here');
        this.total = 'Loading...'
        axios.post('/person-proof')
            .then(res => {
                // console.log(res.data)
                // console.log(res.data.personProof)
                this.PersonProof = res.data.personProof
                this.company_type = res.data.company_type
                this.industry_type = res.data.industry_type
                this.companyProof = res.data.companyProof
                // this.total = res.data.length;
                // this.rows = res.data
            })

        axios.post('/state-dist')
            .then(res => {
                // this.distSelect = res.data.dist;
                this.stateSelect = res.data.state
            })
        this.getSelectDesignation()
        // axios.post('/getSelectDesignation')
        //     .then(res => {
        //         this.designation = res.data;
        //
        //     });

        if (this.$route.query.id) {
            this.data.id = this.$route.query.id
            // alert("edit"+this.$route.query.id)
            const callAxios1 = await this.callAxios('/getEditPrivatedetails', this.data, 'post')
            // console.log(callAxios1.data[0])
            const return_data = callAxios1.data[0]
            // console.log(return_data.firstname)
            const address_check = return_data.address.split('#@#')
            this.data.address = address_check[0]
            this.data.cityText = address_check[2]
            this.data.pincode = address_check[3]
            this.data.firstname = return_data.firstname
            this.data.email = return_data.email
            this.data.landmark = return_data.landmark
            this.data.phonenumber = return_data.phonenumber
            this.data.whatsapp = return_data.whatsapp
            // this.data.designation = return_data.designation
            // console.log(return_data.person_proof_id)
            this.data.person_proof_type = (return_data.person_proof_id > 0) ? return_data.person_proof_id : ''
            this.data.person_proof_text = return_data.person_proof_text
            this.data.id = return_data.id
            this.data.companyname = return_data.companyname
            this.data.tcompanyname = (return_data.companynameTamil != 'null') ? return_data.companynameTamil : ''
            this.data.company_type = return_data.company_type
            this.data.id_proof_type = (return_data.id_proof_type > 0) ? return_data.id_proof_type : ''
            this.data.gst = return_data.gst
            this.data.company_logo = return_data.image
            this.data.website = return_data.website
            this.data.industry_type = (return_data.industry_type_id > 0) ? return_data.industry_type_id : ''
            this.data.pan_card = return_data.pan_card
            // this.data.company_proof_type = return_data.addproof_id
            this.data.state = return_data.state
            this.data.companyProofImg = return_data.company_proof
            this.data.id_proofImg = return_data.id_proof
            this.data.person_proofImg = return_data.person_proof

            // this.data.city = return_data.city
            this.data.type = '1'
            this.person_proof_lable = return_data.personproof_type
            // this.cmpaddproof()

            this.change_dist(return_data.state, 2)

            this.check(return_data.district)
            this.cmpaddproof()
            // console.log('pandiya')
            console.log(return_data.district)
            this.data.district = (return_data.district > 0) ? return_data.district : ''
            // console.log(`chelvns${this.data.district}`)
            this.data.city = (return_data.city > 0) ? return_data.city : ''
            this.data.company_proof_type = (return_data.industry_type_id > 0) ? return_data.id_proof_type : ''
            this.data.addressProof = (return_data.addproof_id > 0) ? return_data.addproof_id : ''

            // console.log(return_data.designation);
            if (Number.isInteger(parseInt(return_data.designation))) {
                this.isDecTrue = false
                this.getSelectDesignation()
                this.data.designation = parseInt(return_data.designation)
                // console.log('Integer')
            } else {
                this.data.designation = ''
                this.isDecTrue = true
                this.decstring = return_data.designation
                // console.log('non Integer')
            }
        } else {
            // alert("New")
        }

        this.change_dist(this.data.state, 1)
    },
    methods: {
        validationForm() {
            this.$refs.simpleRules.validate()
                .then(async success => {
                    if (success) {
                        this.btnShow = false
                        const data = new FormData()
                        data.append('person_proof', this.data.person_proof)
                        data.append('firstname', this.data.firstname)
                        data.append('email', this.data.email)
                        data.append('phonenumber', this.data.phonenumber)
                        data.append('designation', this.data.designation)
                        data.append('person_proof_type', this.data.person_proof_type)
                        data.append('person_proof_text', this.data.person_proof_text)
                        data.append('person_proof', this.data.person_proof)
                        data.append('person_proofImg', this.data.person_proofImg)
                        data.append('landmark', this.data.landmark)
                        data.append('whatsapp', this.data.whatsapp)

                        data.append('id', this.data.id)
                        data.append('companyname', this.data.companyname)
                        data.append('tcompanyname', this.data.tcompanyname)
                        data.append('id_proof', this.data.id_proof)
                        data.append('id_proofImg', this.data.id_proofImg)
                        data.append('companyProof', this.data.companyProof)
                        data.append('companyProofImg', this.data.companyProofImg)
                        data.append('company_type', this.data.company_type)
                        data.append('id_proof_type', this.data.id_proof_type)
                        data.append('address', this.data.address)
                        data.append('pincode', this.data.pincode)
                        data.append('gst', this.data.gst)
                        data.append('image', this.data.image)
                        data.append('website', this.data.website)
                        data.append('industry_type', this.data.industry_type)
                        data.append('pan_card', this.data.pan_card)
                        data.append('company_proof_type', this.data.company_proof_type)
                        data.append('companyNature', this.data.companyNature)
                        data.append('state', this.data.state)
                        data.append('district', this.data.district)
                        data.append('city', this.data.city)
                        data.append('type', this.data.type)
                        data.append('cityText', this.data.cityText)
                        data.append('addressProofType', this.data.addressProof)
                        data.append('inby', this.data.inby)
                        // console.log(this.data)
                        const config = {
                            'content-type': 'multipart/form-data',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        }
                        const callAxios = await this.callAxiosWithConfig('/savePrivateEmployer', data, 'post', config)
                        // console.log("00"+callAxios);
                        // console.log(callAxios);
                        // console.log("11");
                        if (callAxios.status === 200) {
                            this.sweetAlertmethod('success', callAxios.data.type, callAxios.data.msg, 'btn-primary')

                            this.data.person_proof = ''
                            this.data.firstname = ''
                            this.data.email = ''
                            this.data.phonenumber = ''
                            this.data.designation = ''
                            this.data.person_proof_type = ''
                            this.data.person_proof_text = ''
                            this.data.person_proof = ''
                            this.data.id = ''
                            this.data.companyname = ''
                            this.data.tcompanyname = ''
                            this.data.id_proof = ''
                            this.data.companyProof = ''
                            this.data.company_type = ''
                            this.data.id_proof_type = ''
                            this.data.address = ''
                            this.data.pincode = ''
                            this.data.gst = ''
                            this.data.image = ''
                            this.data.website = ''
                            this.data.industry_type = ''
                            this.data.pan_card = ''
                            this.data.company_proof_type = ''
                            this.data.companyNature = ''
                            this.data.state = ''
                            this.data.district = ''
                            this.data.city = ''
                            this.data.type = ''
                            this.data.cityText = ''
                            this.$router.replace('/employer/privateDetails/')
                        } else {
                            this.btnShow = true
                            // console.log(callAxios.response.data.message);
                            this.sweetAlertmethod('error', 'Alert', callAxios.response.data.message, 'btn-primary')
                            // this.toast("Error", callAxios.response.data.message, 'danger');
                        }
                    }
                })
        },
        async validationForm1() {
            const data = new FormData()
            data.append('person_proof', this.data.person_proof)
            data.append('firstname', this.data.firstname)
            data.append('email', this.data.email)
            data.append('phonenumber', this.data.phonenumber)
            data.append('designation', this.data.designation)
            data.append('person_proof_type', this.data.person_proof_type)
            data.append('person_proof_text', this.data.person_proof_text)
            data.append('person_proof', this.data.person_proof)
            data.append('person_proofImg', this.data.person_proofImg)
            data.append('landmark', this.data.landmark)
            data.append('whatsapp', this.data.whatsapp)

            data.append('id', this.data.id)
            data.append('companyname', this.data.companyname)
            data.append('tcompanyname', this.data.tcompanyname)
            data.append('id_proof', this.data.id_proof)
            data.append('id_proofImg', this.data.id_proofImg)
            data.append('companyProof', this.data.companyProof)
            data.append('companyProofImg', this.data.companyProofImg)
            data.append('company_type', this.data.company_type)
            data.append('id_proof_type', this.data.id_proof_type)
            data.append('address', this.data.address)
            data.append('pincode', this.data.pincode)
            data.append('gst', this.data.gst)
            data.append('image', this.data.image)
            data.append('website', this.data.website)
            data.append('industry_type', this.data.industry_type)
            data.append('pan_card', this.data.pan_card)
            data.append('company_proof_type', this.data.company_proof_type)
            data.append('companyNature', this.data.companyNature)
            data.append('state', this.data.state)
            data.append('district', this.data.district)
            data.append('city', this.data.city)
            data.append('type', this.data.type)
            data.append('cityText', this.data.cityText)
            data.append('addressProofType', this.data.addressProof)
            // console.log(this.data)
            const config = {
                'content-type': 'multipart/form-data',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
            const callAxios = await this.callAxiosWithConfig('/savePrivateEmployer', data, 'post', config)
            // console.log("00"+callAxios);
            // console.log(callAxios);
            // console.log("11");
            if (callAxios.status === 200) {
                this.sweetAlertmethod('success', callAxios.data.type, callAxios.data.msg, 'btn-primary')

                this.data.person_proof = ''
                this.data.firstname = ''
                this.data.email = ''
                this.data.phonenumber = ''
                this.data.designation = ''
                this.data.person_proof_type = ''
                this.data.person_proof_text = ''
                this.data.person_proof = ''
                this.data.id = ''
                this.data.companyname = ''
                this.data.tcompanyname = ''
                this.data.id_proof = ''
                this.data.companyProof = ''
                this.data.company_type = ''
                this.data.id_proof_type = ''
                this.data.address = ''
                this.data.pincode = ''
                this.data.gst = ''
                this.data.image = ''
                this.data.website = ''
                this.data.industry_type = ''
                this.data.pan_card = ''
                this.data.company_proof_type = ''
                this.data.companyNature = ''
                this.data.state = ''
                this.data.district = ''
                this.data.city = ''
                this.data.type = ''
                this.data.cityText = ''
                this.$router.replace('/employer/privateDetails/')
            } else {
                // console.log(callAxios.response.data.message);
                this.sweetAlertmethod('error', 'Alert', callAxios.response.data.message, 'btn-primary')
                // this.toast("Error", callAxios.response.data.message, 'danger');
            }

            // this.data+=formdata;
            //             const callAxios = await this.callAxios('/savePrivateEmployer', this.data, 'post');
            //             // console.log("00"+callAxios);
            //             console.log(callAxios);
            //             // console.log("11");
            //             if (callAxios.status === 200) {
            //                 this.toast("Success", "Successfully created", 'success');
            //                 // this.rows = callAxios.data;
            //                 // this.$refs['addForm'].hide();
            //                 // this.data.companyname = '';
            //                 // this.data.image = '';
            //             } else {
            //                 // console.log(callAxios.response.data.message);
            //                 this.toast("Error", callAxios.response.data.message, 'danger');
            //             }
        },

        onIdProofImageChange(e) {
            // console.log('here')
            const files = e.target.files[0]
            // console.log(e.target.files[0])

            this.data.id_proof = e.target.files[0]
            this.selectid_proofImg = URL.createObjectURL(files)
        },
        onIdCompanyImageChange(e) {
            const files = e.target.files[0]
            this.data.image = e.target.files[0]
        },

        onAuthorisedPersonProofImageChange(e) {
            // console.log('here')
            const files = e.target.files[0]
            // console.log(e.target.files[0]);

            this.data.person_proof = e.target.files[0]
            this.selectperson_proofImg = URL.createObjectURL(files)
            // console.log( this.data.id_proof);
        },

        onCompanyAddressProofImageChange(e) {
            // console.log('here')
            const files = e.target.files[0]
            // console.log(e.target.files[0]);

            this.data.companyProof = e.target.files[0]
            this.selectcompanyProofImg = URL.createObjectURL(files)
        },
        change_dist(value, empty) {
            if (empty != 1) {
                this.data.district = ''
            }
            const axiosSource = axios.CancelToken.source()
            this.request = { cancel: axiosSource.cancel }
            axios
                .post('/getDist', {
                    cancelToken: axiosSource.token,
                    value,
                })
                .then(res => {
                    this.distSelect = res.data.dist
                })
        },
        check(value) {
            if (value) {
                this.district_id.disid = value
                this.data.city = ''
                // this.citySelect = ''
                this.citySelect = []
                const axiosSource = axios.CancelToken.source()
                this.request = { cancel: axiosSource.cancel }

                axios
                    .post('/getCity', {
                        cancelToken: axiosSource.token,
                        value,
                    })
                    .then(res => {
                        this.citySelect = res.data.areas
                    })
            }
        },

        change_lable() {
            const type = this.data.person_proof_type
            const aa = this.PersonProof.find(x => x.value === type)
            this.person_proof_lable = aa.text
            // console.log(type)
            if (type === 11) {
                this.is_rules = {
                    // required,
                    regex: /^[A-PR-WY][1-9]\d\s?\d{4}[1-9]$/,
                }
            } else if (type === 12) {
                this.is_rules = {
                    // required,
                    regex: /^([a-zA-Z]){3}([0-9]){7}?$/,
                }
            } else if (type === 13) {
                this.is_rules = {
                    // required,
                    regex: /^([A-Z]{2})(\d{2}|\d{3})[a-zA-Z]{0,1}(\d{4})(\d{7})$/,
                }
            } else if (type === 14) {
                this.is_rules = {
                    // required,
                    regex: /^(\d{3,12})$/,
                }
            } else if (type === 15) {
                this.is_rules = {
                    // required,
                    regex: /^[2-9]{1}[0-9]{3}\s{1}[0-9]{4}\s{1}[0-9]{4}$/,
                }
            } else {
                this.is_rules = {
                    // required,
                }
            }
            // console.log(this.is_rules)
        },
        City_text() {
            const type = this.data.city
            const aa = this.citySelect.find(x => x.value === type)
            this.data.cityText = aa.text
        },
        mobilenumber_check() {
            const value = this.data.phonenumber
            this.keywords = value
            if (value.length === 10) {
                const axiosSource = axios.CancelToken.source()
                this.request = { cancel: axiosSource.cancel }
                axios
                    .post('/checkPrivateEmployer', {
                        cancelToken: axiosSource.token,
                        value,
                    })
                    .then(res => {
                        // console.log(res.data)
                        // console.log(res.status)
                        if (res.data.length > 0) {
                            // console.log('data here')
                            // console.log(res.data[0].phonenumber)
                            const messge_txt = `<span class="text-primary"> Company Name : </span><span class="text-danger">${res.data[0].companyname} </span><br> <span class="text-primary mt-1">Mobile Number : </span><span class="text-danger">${res.data[0].phonenumber}</span>`
                            this.sweetAlertmethod('error', 'Already Register', messge_txt, 'btn-primary')
                            this.data.phonenumber = ''
                        }
                    })
            }
        },

        cmpaddproof() {
            const value = this.data.industry_type

            if (value) {
                const axiosSource = axios.CancelToken.source()
                this.request = { cancel: axiosSource.cancel }

                axios
                    .post('/getAddressProof', {
                        cancelToken: axiosSource.token,
                        value,
                    })
                    .then(res => {
                        this.selcetaddressProof = res.data.addressProof
                    })
            }
        },
        getSelectDesignation() {
            axios.post('/getSelectDesignation')
                .then(res => {
                    this.designation = res.data
                    this.isdecspiner = false
                })
            // this.isdecspiner=false
        },
        getdes() {
            // alert('here');
            this.isdecspiner = true
            this.getSelectDesignation()
        },

        checkcompanyname() {
            this.isShow = true
            if (this.data.companyname.trim() == '') return this.toast('Required', 'Please Enter Company Name ', 'danger')
            const value = this.data.companyname
            const empid = this.data.id

            this.isDisabled = false
            this.isDisabledvalue = true

            const axiosSource = axios.CancelToken.source()
            this.request = { cancel: axiosSource.cancel }
            axios
                .post('/checkCompanyName', {
                    cancelToken: axiosSource.token,
                    value,
                    empid,
                })
                .then(res => {
                    // console.log(res.data.duplicate_match)
                    // console.log(res.data.duplicateName[0]);
                    //
                    // console.log(res.status);
                    this.ismatchedCompany = true
                    this.matchedCompany = res.data.duplicate_match
                    if (res.data.duplicateName.length > 0) {
                        const messge_txt = `<span class="text-primary"> Employer ID : </span><span class="text-danger">${res.data.duplicateName[0].id} </span><br> <span class="text-primary mt-1">Company Name : </span><span class="text-danger">${res.data.duplicateName[0].companyname}</span>`
                        this.sweetAlertmethod('error', 'Already Register', messge_txt, 'btn-primary')
                        this.data.companyname = ''
                    } else if (res.data.duplicate_match.length == 0) {
                        this.matchedCompany = ''
                        this.ismatchedCompany = false

                        this.sweetAlertmethod('error', 'No Duplicate Company', 'No Duplicate Company', 'btn-primary')
                    }
                    this.isShow = false
                })
        },

        removeImage(value) {
            // alert(value)
            this.$swal({
                title: 'Are you sure?',
                text: 'You won\'t be able to remove this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, remove it!',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ml-1',
                },
                buttonsStyling: false,
            })
                .then(async result => {
                    this.proofImg.imgType = value
                    this.proofImg.id = this.data.id
                    if (result.value) {
                        const callAxios = await this.callAxios('/removeProofImg', this.proofImg, 'post')
                        if (callAxios.status === 200) {
                            this.sweetAlertmethod('success', 'Image Removed', '', 'btn-success')
                            if (value === 'companyProofImg') {
                                this.data.companyProofImg = ''
                            } else if (value === 'id_proofImg') {
                                this.data.id_proofImg = ''
                            } else if (value === 'person_proofImg') {
                                this.data.person_proofImg = ''
                            } else if (value === 'company_logo') {
                                this.data.company_logo = ''
                            }
                        } else {
                            this.sweetAlertmethod('warning', 'Warning!', callAxios.response.data.message, 'btn-success')
                            // console.log('1')
                        }
                    } else if (result.dismiss === 'cancel') {
                        this.$swal({
                            title: 'Cancelled',
                            text: 'Your imaginary file is safe :)',
                            icon: 'error',
                            customClass: {
                                confirmButton: 'btn btn-success',
                            },
                        })
                    }
                })
        },

        checkLetter() {
            const value = this.data.companyname
            if (this.isDisabledvalue == false) {
                if (value.length == 5) {
                    this.isDisabled = true
                    this.isCheckbtn = true
                }
            }
        },
        onPaste() {
            const value = this.data.companyname

            alert(value.substr(0, value.length))
        },
        copyNumber() {
            const mobileNumber = this.data.phonenumber
            this.data.whatsapp = mobileNumber
        },
        clearNumber() {
            this.data.whatsapp = ''
        },
        cmpaddcheck(value) {
            // console.log(value)
            if (value == 17) {
                this.is_gst = {
                    required,
                    regex: /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/,
                }

                this.dynamicClass = 'required'
            } else {
                this.is_gst = {}

                this.dynamicClass = ''
            }
        },

        async viewEmployer(value) {
            // console.log(value)
            if (value) {
                // this.data.id = value;
                const callAxios = await this.callAxios('/privateEmployerHistory', { id: value }, 'post')
                // console.log(callAxios.data.employerDeails[0])
                this.hdata = callAxios.data.employerDeails[0]
                this.jobDetailsrows = callAxios.data.jobs_details
                this.paymentDetailsrows = callAxios.data.payment_details
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
    },

}
</script>
