<template>
    <section>
        <div class="text-center m-5" v-show="initial_loading">
            <b-spinner
                v-for="variant in variants"
                :key="variant"
                :variant="variant"
                style="width: 3rem; height: 3rem;"
                class="m-2"
                type="grow"
            />
        </div>
        <validation-observer v-show="!initial_loading" ref="simpleRules">
            <b-form>
                <b-card-actions
                    border-variant="primary"
                    title="Private Job Posting - Primary Fields"
                    action-collapse
                >
                    <b-row>
                        <!--Select Employer -->
                        <b-col md="6">
                            <b-form-group>
                                <label class="required">Select Employer</label> <small class="text-muted">Enter at
                                least
                                5
                                characters to Search</small>
                                <validation-provider
                                    #default="{ errors }"
                                    name="Employer"
                                    rules="required"
                                >
                                    <!--                                <b-form-input-->
                                    <!--                                    v-model="data.typed_employername"-->
                                    <!--                                    v-on:keyup="keymonitor"-->
                                    <!--                                    :state="errors.length > 0 ? false:null"-->
                                    <!--                                    placeholder="Employer Name"-->
                                    <!--                                />-->
                                    <!--                                @keydown.native.enter="keymonitor"-->
                                    <!--                                @search="fetchOptions"-->
                                    <!--                                @search:blur=""-->
                                    <!--                                :map-keydown="vselect_handlers"-->
                                    <v-select
                                        :clearSearchOnSelect="false"
                                        placeholder="Select Employer"
                                        :options="options"
                                        v-model="data.employerid"
                                        label="name"
                                        :reduce="item=>item.id"
                                        @search="onemployersearch"
                                        @input="employerSelected"
                                        :selectable="item => item.is_block==0"
                                    />

                                    <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                                <span>{{ this.data.employerName }}</span>
                            </b-form-group>
                        </b-col>

                        <!--Plan Details -->
                        <b-col md="6">
                            <div class="text-center" v-if="current_plan_data.plan_name">
                                <p class="font-weight-bold m-0 text-primary">Employer ID : {{ this.data.employerid }} |
                                    {{ current_plan_data.plan_name }}
                                    | {{ current_plan_data.inv_prefix }}{{ current_plan_data.inv_no }}
                                    | ₹{{ current_plan_data.txn_amt }}/- </p>
                                <p class="font-weight-bold m-0 text-dark">Plan Start Date :
                                    {{ current_plan_data.starting_date }} | Plan End Date :
                                    {{ current_plan_data.expired_date }}</p>
                                <p class="font-weight-bold m-0 text-danger">Total count : {{
                                    current_plan_data.post_count
                                    }} - Balance Count : {{ current_plan_data.balance_count }} | Job validity :
                                    {{ current_plan_data.job_validity }} days</p>
                                <b-badge
                                    @click="employerSelected(data.employerid,'','','1')" pill
                                    variant="primary"
                                >
                                    Change Plan Details
                                </b-badge>
                            </div>
                        </b-col>

                        <!--Select Job Title -->
                        <b-col md="6">
                            <b-form-group>
                                <label class="required">Select Job title</label> <small class="text-muted">Enter at
                                least 3
                                characters to Search</small>
                                <validation-provider
                                    #default="{ errors }"
                                    name="Select Job title"
                                    rules="required"
                                >
                                    <v-select
                                        :clearSearchOnSelect="false"
                                        placeholder="Select Job title"
                                        :options="jobtitle_options"
                                        v-model="data.jobtitle_id"
                                        label="name"
                                        :reduce="item=>item.id"
                                        @input="settitle"
                                    />

                                    <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                                <span>{{ this.data.jobtitleName }}</span>
                            </b-form-group>
                        </b-col>

                        <!--Showing Job Title -->
                        <b-col md="6">
                            <b-form-group>
                                <label class="required">Showing Job Title</label>
                                <validation-provider
                                    #default="{ errors }"
                                    name="Showing Job Title"
                                    rules="required|min:3|max:100"
                                >
                                    <b-form-input
                                        v-model="data.jobtitle"
                                        :state="errors.length > 0 ? false:null"
                                        placeholder="Showing Job Title"
                                    />
                                    <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                            </b-form-group>
                        </b-col>

                        <!--Job Description -->
                        <b-col md="6">
                            <b-form-group>
                                <label class="required">Job Description</label>
                                <validation-provider
                                    #default="{ errors }"
                                    name="Job Description"
                                    rules="required|max:1000"
                                >
                                    <b-form-textarea
                                        rows="8"
                                        v-model="data.jobDetails"
                                        placeholder="Job Description"
                                        class="char-textarea"
                                        :class="data.jobDetails ? data.jobDetails.length >= maxChar1000 ? 'text-danger' : '' : ''"
                                    />
                                    <small
                                        class="textarea-counter-value float-right"
                                        :class="data.jobDetails ? data.jobDetails.length >= maxChar1000 ? 'bg-danger' : '' : ''"
                                    >
                                        <span class="char-count">{{
                                                data.jobDetails ? data.jobDetails.length : 0
                                            }}</span> /
                                        {{ maxChar1000 }}
                                    </small>
                                    <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                            </b-form-group>
                        </b-col>

                        <b-col md="6">
                            <b-row>
                                <!--Select Gender -->
                                <b-col md="5">
                                    <b-form-group
                                        label="Select Gender"
                                    >
                                        <b-form-group>
                                            <b-form-radio-group v-model="data.gender">
                                                <b-form-radio value="2"><small>All</small></b-form-radio>
                                                <b-form-radio value="0"><small>Male</small></b-form-radio>
                                                <b-form-radio value="1"><small>Female</small></b-form-radio>
                                            </b-form-radio-group>
                                        </b-form-group>
                                    </b-form-group>
                                </b-col>

                                <!--Select Marital Status -->
                                <b-col md="7">
                                    <b-form-group
                                        label="Select Marital Status"
                                    >
                                        <b-form-group>
                                            <b-form-radio-group v-model="data.marital_status">
                                                <b-form-radio value="2">All</b-form-radio>
                                                <b-form-radio value="0">Unmarried</b-form-radio>
                                                <b-form-radio value="1">Married</b-form-radio>
                                            </b-form-radio-group>
                                        </b-form-group>
                                    </b-form-group>
                                </b-col>

                                <!--Select Age limit -->
                                <b-col md="5">
                                    <b-form-group
                                        label="Select Age limit"
                                    >
                                        <b-form-group>
                                            <ValidationProvider rules="" vid="isAgeLimit">
                                                <b-form-radio-group v-model="isAgeLimit">
                                                    <b-form-radio value="0"><small>No</small></b-form-radio>
                                                    <b-form-radio value="1"><small>Enter Age Limit</small>
                                                    </b-form-radio>
                                                </b-form-radio-group>
                                            </ValidationProvider>
                                        </b-form-group>
                                    </b-form-group>
                                </b-col>
                                <!--Age limit From -->
                                <b-col md="4" v-if="isAgeLimit=='1'">
                                    <b-form-group>
                                        <label>Age limit From</label>
                                        <validation-provider
                                            #default="{ errors }"
                                            name="AgelimitFrom"
                                            rules="digits:2|lessthan:@AgelimitTo,int|greaterthan:18,int|required_if:isAgeLimit,1"
                                        >
                                            <b-form-input
                                                type="number"
                                                v-model="data.ageLimitFrom"
                                                :state="errors.length > 0 ? false:null"
                                                placeholder="Age limit From"
                                            />
                                            <small class="text-danger">{{ errors[0] }}</small>
                                        </validation-provider>
                                    </b-form-group>
                                </b-col>

                                <!--Age limit To -->
                                <b-col md="3" v-if="isAgeLimit=='1'">
                                    <b-form-group>
                                        <label>Age limit To</label>
                                        <validation-provider
                                            #default="{ errors }"
                                            name="AgelimitTo"
                                            rules="digits:2|greaterthan:@AgelimitFrom,int"
                                        >
                                            <b-form-input
                                                type="number"
                                                v-model="data.ageLimitTo"
                                                :state="errors.length > 0 ? false:null"
                                                placeholder="Age limit To"
                                            />
                                            <small class="text-danger">{{ errors[0] }}</small>
                                        </validation-provider>
                                    </b-form-group>
                                </b-col>

                                <!--No of Vacancy -->
                                <b-col md="6">
                                    <b-form-group>
                                        <label>No of Vacancy</label>
                                        <validation-provider
                                            #default="{ errors }"
                                            name="No of Vacancy"
                                            rules="integer|max:50"
                                        >
                                            <b-form-input
                                                type="number"
                                                v-model="data.noofvancancy"
                                                :state="errors.length > 0 ? false:null"
                                                placeholder="No of Vacancy"
                                            />
                                            <small class="text-danger">{{ errors[0] }}</small>
                                        </validation-provider>
                                    </b-form-group>
                                </b-col>

                                <!--Tags -->
                                <b-col md="6">
                                    <b-form-group>
                                        <label>Tags</label>
                                        <validation-provider
                                            #default="{ errors }"
                                            name="Tags"
                                            rules="max:100"
                                        >
                                            <b-form-input
                                                v-model="data.tags"
                                                :state="errors.length > 0 ? false:null"
                                                placeholder="Tags"
                                            />
                                            <small class="text-danger">{{ errors[0] }}</small>
                                        </validation-provider>
                                    </b-form-group>
                                </b-col>

                                <!--                                &lt;!&ndash;Select Job Source &ndash;&gt;-->
                                <!--                                <b-col md="6">-->
                                <!--                                    <b-form-group>-->
                                <!--                                        <label class="required">Select Job Source</label>-->
                                <!--                                        <validation-provider-->
                                <!--                                            #default="{ errors }"-->
                                <!--                                            name="Select Job Source"-->
                                <!--                                            rules="required"-->
                                <!--                                        >-->
                                <!--                                            <v-select-->
                                <!--                                                placeholder="Select Job Source"-->
                                <!--                                                v-model="data.jobSource"-->
                                <!--                                                label="name"-->
                                <!--                                                :reduce="item=>item.id"-->
                                <!--                                                :options="select_job_source"-->
                                <!--                                            />-->

                                <!--                                            <small class="text-danger">{{ errors[0] }}</small>-->
                                <!--                                        </validation-provider>-->
                                <!--                                    </b-form-group>-->
                                <!--                                </b-col>-->

                            </b-row>
                        </b-col>
                        <b-col md="12">
                            <b-row>
                                <!--Select Experience -->
                                <b-col md="3">
                                    <b-form-group
                                        label="Select Experience"
                                    >
                                        <b-form-group>
                                            <ValidationProvider rules="" vid="isExperience">
                                                <b-form-radio-group v-model="isExperience">
                                                    <b-form-radio value="0"><small>No details</small></b-form-radio>
                                                    <b-form-radio value="1"><small>Freshers</small></b-form-radio>
                                                    <b-form-radio value="2"><small>Experience</small></b-form-radio>
                                                </b-form-radio-group>
                                            </ValidationProvider>
                                        </b-form-group>
                                    </b-form-group>
                                </b-col>

                                <!--Experience From -->
                                <b-col md="2" v-if="isExperience!='0'">
                                    <b-form-group>
                                        <label>Experience From</label>
                                        <validation-provider
                                            #default="{ errors }"
                                            name="ExperienceFrom"
                                            rules="max:2|lessthan:@ExperienceTo,int|required_if:isExperience,1,2"
                                        >
                                            <b-form-input
                                                type="number"
                                                v-model="data.experienceFrom"
                                                :state="errors.length > 0 ? false:null"
                                                placeholder="Experience From"
                                            />
                                            <small class="text-danger">{{ errors[0] }}</small>
                                        </validation-provider>
                                    </b-form-group>
                                </b-col>

                                <!--Experience To -->
                                <b-col md="2" v-if="isExperience!='0'">
                                    <b-form-group>
                                        <label>Experience To</label>
                                        <validation-provider
                                            #default="{ errors }"
                                            name="ExperienceTo"
                                            rules="max:2|greaterthan:@ExperienceFrom,int"
                                        >
                                            <b-form-input
                                                type="number"
                                                v-model="data.experienceTo"
                                                :state="errors.length > 0 ? false:null"
                                                placeholder="Experience To"
                                            />
                                            <small class="text-danger">{{ errors[0] }}</small>
                                        </validation-provider>
                                    </b-form-group>
                                </b-col>

                                <!--Experience Remarks -->
                                <b-col md="5">
                                    <b-form-group>
                                        <label>Experience Remarks</label>
                                        <validation-provider
                                            #default="{ errors }"
                                            name="Experience Remarks"
                                        >
                                            <b-form-input
                                                v-model="data.experienceRemarks"
                                                :state="errors.length > 0 ? false:null"
                                                placeholder="Experience Remarks"
                                            />
                                            <small class="text-danger">{{ errors[0] }}</small>
                                        </validation-provider>
                                    </b-form-group>
                                </b-col>

                            </b-row>
                        </b-col>
                        <!--Select Group Qualification -->
                        <b-col md="6">
                            <b-form-group>
                                <label>Select Group Qualification</label>
                                <v-select
                                    placeholder="Select Group Qualification"
                                    multiple
                                    :clearSearchOnSelect=false
                                    :closeOnSelect=false
                                    v-model="data.groupqualification"
                                    label="name"
                                    :reduce="item=>item.id"
                                    :options="selectGroupQualification"
                                    @input="setsingleFromGroupQualification"
                                />
                            </b-form-group>
                        </b-col>

                        <!--Select Single Qualification -->
                        <b-col md="6">
                            <b-form-group>
                                <label>Select Single Qualification</label>
                                <treeselect
                                    :multiple="true"
                                    :options="selectSingleQualification"
                                    :limit="10"
                                    placeholder="Select Single Qualification"
                                    v-model="data.singlequalification"
                                    @input="singlequalificationSelected"
                                >
                                </treeselect>
                                <!--                                <v-select-->
                                <!--                                    placeholder="Select Single Qualification"-->
                                <!--                                    multiple-->
                                <!--                                    :clearSearchOnSelect=false-->
                                <!--                                    :closeOnSelect=false-->
                                <!--                                    v-model="data.singlequalification"-->
                                <!--                                    label="name"-->
                                <!--                                    :reduce="item=>item.id"-->
                                <!--                                    :options="selectSingleQualification"-->
                                <!--                                />-->
                            </b-form-group>
                        </b-col>
                        <b-col md="12">
                            <b-row>
                                <!--Select Salary -->
                                <b-col md="3">
                                    <b-form-group
                                        label="Select Salary"
                                    >
                                        <b-form-group>
                                            <ValidationProvider rules="" vid="isSalary">
                                                <b-form-radio-group v-model="isSalary">
                                                    <b-form-radio value="0"><small>As per company Norms</small>
                                                    </b-form-radio>
                                                    <b-form-radio value="1"><small>Enter Salary</small></b-form-radio>
                                                </b-form-radio-group>
                                            </ValidationProvider>
                                        </b-form-group>
                                    </b-form-group>
                                </b-col>

                                <!--Salary From-->
                                <b-col md="2" v-if="isSalary=='1'">
                                    <b-form-group>
                                        <label>Salary From</label>
                                        <validation-provider
                                            #default="{ errors }"
                                            name="SalaryFrom"
                                            rules="lessthan:@SalaryTo,int|greaterthan:6000,int|required_if:isSalary,1"
                                        >
                                            <b-form-input
                                                type="number"
                                                v-model="data.salaryFrom"
                                                :state="errors.length > 0 ? false:null"
                                                placeholder="Salary From"
                                            />
                                            <small class="text-danger">{{ errors[0] }}</small>
                                        </validation-provider>
                                    </b-form-group>
                                </b-col>

                                <!--Salary To -->
                                <b-col md="2" v-if="isSalary=='1'">
                                    <b-form-group>
                                        <label>Salary To</label>
                                        <validation-provider
                                            #default="{ errors }"
                                            name="SalaryTo"
                                            rules="greaterthan:@SalaryFrom,int"
                                        >
                                            <b-form-input
                                                type="number"
                                                v-model="data.salaryTo"
                                                :state="errors.length > 0 ? false:null"
                                                placeholder="Salary To"
                                            />
                                            <small class="text-danger">{{ errors[0] }}</small>
                                        </validation-provider>
                                    </b-form-group>
                                </b-col>

                                <!--Salary Remarks -->
                                <b-col md="5">
                                    <b-form-group>
                                        <label>Salary Remarks</label>
                                        <validation-provider
                                            #default="{ errors }"
                                            name="Salary Remarks"
                                        >
                                            <b-form-input
                                                v-model="data.salaryRemarks"
                                                :state="errors.length > 0 ? false:null"
                                                placeholder="Salary Remarks"
                                            />
                                            <small class="text-danger">{{ errors[0] }}</small>
                                        </validation-provider>
                                    </b-form-group>
                                </b-col>

                            </b-row>
                        </b-col>
                    </b-row>
                </b-card-actions>
                <b-card-actions
                    border-variant="primary"
                    title="Private Job Posting - Auto Generated Fields"
                    action-collapse
                >
                    <b-row>

                        <!--Select Work-mode -->
                        <b-col md="4">
                            <b-form-group
                                label="Select Work-mode"
                            >
                                <b-form-group>
                                    <b-form-radio-group v-model="data.workmode">
                                        <b-form-radio value="1">FULL-TIME</b-form-radio>
                                        <b-form-radio value="2">PART-TIME</b-form-radio>
                                    </b-form-radio-group>
                                </b-form-group>
                            </b-form-group>
                        </b-col>

                        <!--                        &lt;!&ndash;Verified&ndash;&gt;-->
                        <!--                        <b-col md="2">-->
                        <!--                            <b-form-group>-->
                        <!--                                <label class="required">Verified</label>-->
                        <!--                                <validation-provider-->
                        <!--                                    name="Verified"-->
                        <!--                                    #default="{ errors }"-->
                        <!--                                    rules="required"-->
                        <!--                                >-->
                        <!--                                    <b-form-checkbox-->
                        <!--                                        v-model="data.verified"-->
                        <!--                                        value="1"-->
                        <!--                                    > Verified-->
                        <!--                                    </b-form-checkbox>-->
                        <!--                                    <small class="text-danger">{{ errors[0] }}</small>-->
                        <!--                                </validation-provider>-->
                        <!--                            </b-form-group>-->
                        <!--                        </b-col>-->

                        <!--Job Skills -->
                        <b-col md="6">
                            <b-form-group>
                                <label class="required">Job Skills</label>
                                <validation-provider
                                    #default="{ errors }"
                                    name="Job Skills"
                                    rules="required"
                                >
                                    <v-select
                                        placeholder="Select Job Skills"
                                        :clearSearchOnSelect=true
                                        :closeOnSelect=false
                                        multiple
                                        v-model="data.skills"
                                        label="name"
                                        :reduce="item=>item.id"
                                        :options="selectSkills"
                                    />
                                    <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>

                            </b-form-group>
                        </b-col>

                        <!--Job Location -->
                        <b-col md="6">
                            <b-form-group>
                                <label class="required">Job Location</label>
                                <validation-provider
                                    #default="{ errors }"
                                    name="Job Location"
                                    rules="required"
                                >
                                    <!--                                    <multiselect-->
                                    <!--                                        :clearSearchOnSelect=false-->
                                    <!--                                        :closeOnSelect=false-->
                                    <!--                                        :multiple="true"-->
                                    <!--                                        group-values="cities"-->
                                    <!--                                        group-label="district"-->
                                    <!--                                        :group-select="true"-->
                                    <!--                                        placeholder="Job Location"-->
                                    <!--                                        track-by="id"-->
                                    <!--                                        label="name"-->
                                    <!--                                        v-model="data.joblocation"-->
                                    <!--                                        :options="selectArea"-->
                                    <!--                                        :searchable="true"-->
                                    <!--                                    />-->
                                    <treeselect
                                        :clearable=true
                                        value-consists-of="LEAF_PRIORITY"
                                        :multiple="true"
                                        :options="selectArea"
                                        :limit="10"
                                        placeholder="Select Job Location..."
                                        @input="joblocationSelected"
                                        v-model="data.joblocation"
                                    >
                                        <div class="d-flex justify-content-center" slot="before-list">
                                            <button @click="selectAlljoblocation()" type="button" class="btn btn-light">
                                                Select
                                                All
                                            </button>
                                            <button @click="deselectAlljoblocation()" type="button"
                                                    class="btn btn-light"
                                            >Deselect
                                                All
                                            </button>
                                        </div>
                                    </treeselect>
                                    <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>

                            </b-form-group>
                        </b-col>

                        <!--Job Location Remarks -->
                        <b-col md="6">
                            <b-form-group>
                                <label>Job Location Remarks</label>
                                <b-form-input
                                    v-model="data.joblocationRemarks"
                                    placeholder="Job Location Remarks"
                                />
                            </b-form-group>
                        </b-col>

                        <!--Candidate apply location -->
                        <b-col md="6">
                            <b-form-group>
                                <label class="required">Candidate apply location</label>
                                <validation-provider
                                    #default="{ errors }"
                                    name="Candidate apply location"
                                    rules="required"
                                >
                                    <treeselect
                                        :clearable=true
                                        value-consists-of="LEAF_PRIORITY"
                                        :multiple="true"
                                        :options="selectArea"
                                        :limit="10"
                                        placeholder="Select Candidate apply location..."
                                        v-model="data.candidateLocation"
                                    >
                                        <div class="d-flex justify-content-center" slot="before-list">
                                            <button @click="selectAllcandidateLocation()" type="button"
                                                    class="btn btn-light"
                                            >Select
                                                All
                                            </button>
                                            <button @click="deselectAllcandidateLocation()" type="button"
                                                    class="btn btn-light"
                                            >Deselect
                                                All
                                            </button>
                                        </div>
                                    </treeselect>
                                    <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>

                            </b-form-group>
                        </b-col>

                        <!--Search Keyword -->
                        <b-col md="6">
                            <b-form-group>
                                <label>Search Keyword</label>
                                <v-select
                                    placeholder="Search Keyword"
                                    multiple
                                    taggable
                                    :closeOnClick="false"
                                    :closeOnSelect="false"
                                    label="key_word"
                                    v-model="data.key_word_db_web"
                                    :options="key_word_db_web"
                                    @input="selected_keywords"
                                />
                            </b-form-group>
                        </b-col>

                        <!--Mobile (,) separated -->
                        <b-col md="5">
                            <b-form-group>
                                <label>Mobile (,) separated</label>
                                <validation-provider
                                    #default="{ errors }"
                                    name="Mobile (,) separated"
                                    rules="min:10|max:100"
                                >
                                    <b-form-input
                                        v-model="data.mobileSeperate"
                                        :state="errors.length > 0 ? false:null"
                                        placeholder="Mobile (,) separated"
                                    />
                                    <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                            </b-form-group>
                        </b-col>

                        <!--Get response by Call-->
                        <b-col md="3">
                            <b-form-group>
                                <label>Get response by Call</label>
                                <b-form-checkbox
                                    v-model="data.responseCall"
                                    value="1"
                                > Get response by Call
                                </b-form-checkbox>
                            </b-form-group>
                        </b-col>

                        <!--Whatsapp Number -->
                        <b-col md="4">
                            <b-form-group>
                                <label>Whatsapp Number</label>
                                <validation-provider
                                    #default="{ errors }"
                                    name="Whatsapp Number"
                                    rules="min:10"
                                >
                                    <b-form-input
                                        v-model="data.whatsappNumber"
                                        :state="errors.length > 0 ? false:null"
                                        placeholder="Whatsapp Number"
                                    />
                                    <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                                <a @click="this.copyFromMobile" class="badge badge-primary ml-25 text-white">📋 Copy from
                                    Mobile Number</a>
                                <a @click="this.clearWhatsappNumber" class="badge badge-secondary ml-25 text-white">Clear</a>
                            </b-form-group>
                        </b-col>

                        <!--Address -->
                        <b-col md="6">
                            <b-form-group>
                                <label>Address</label>
                                <validation-provider
                                    #default="{ errors }"
                                    name="Address"
                                    rules="max:500"
                                >
                                    <b-form-textarea
                                        rows="5"
                                        v-model="data.address"
                                        placeholder="Address"
                                        class="char-textarea"
                                        :class="data.address ? data.address.length >= maxChar500 ? 'text-danger' : '' : ''"
                                    />
                                    <small
                                        class="textarea-counter-value float-right"
                                        :class="data.address ? data.address.length >= maxChar500 ? 'bg-danger' : '' : ''"
                                    >
                                        <span class="char-count">{{
                                                data.address ? data.address.length : 0
                                            }}</span>
                                        / {{ maxChar500 }}
                                    </small>
                                    <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                            </b-form-group>
                        </b-col>

                        <b-col md="6">
                            <b-row>

                                <!--Map URL -->
                                <b-col md="12">
                                    <b-form-group>
                                        <label>Map URL</label>
                                        <a v-if="this.data.employerid" target="_blank"
                                           :href="'https://www.google.com/maps/search/'+this.data.employerName+' '+ this.employer_data.area_name"
                                           class="badge badge-primary ml-25 text-white"
                                        >🗺 Search in Google Map</a>
                                        <validation-provider
                                            #default="{ errors }"
                                            name="Map URL"
                                            rules="url|min:10|max:100"
                                        >
                                            <b-form-input
                                                type="url"
                                                v-model="data.mapURL"
                                                :state="errors.length > 0 ? false:null"
                                                placeholder="Map URL"
                                            />
                                            <small class="text-danger">{{ errors[0] }}</small>
                                        </validation-provider>
                                    </b-form-group>
                                </b-col>

                                <!--Start Date -->
                                <b-col md="6">
                                    <b-form-group>
                                        <label class="required">Start Date</label>
                                        <validation-provider
                                            #default="{ errors }"
                                            name="StartDate"
                                            rules="required|lessthan:@EndDate,date"
                                        >
                                            <flat-pickr
                                                :disabled=disableStartEnddate
                                                :config="datePickerConfig"
                                                placeholder="Select Start date"
                                                v-model="data.startDate"
                                                class="form-control"
                                            />
                                            <small class="text-danger">{{ errors[0] }}</small>
                                        </validation-provider>
                                    </b-form-group>
                                </b-col>

                                <!--End Date -->
                                <b-col md="6">
                                    <b-form-group>
                                        <label class="required">End Date</label>
                                        <validation-provider
                                            #default="{ errors }"
                                            name="EndDate"
                                            rules="required|greaterthan:@StartDate,date"
                                        >
                                            <flat-pickr
                                                :disabled=disableStartEnddate
                                                :config="datePickerConfig"
                                                placeholder="Select End date"
                                                v-model="data.endDate"
                                                class="form-control"
                                            />
                                            <small class="text-danger">{{ errors[0] }}</small>
                                        </validation-provider>
                                    </b-form-group>
                                </b-col>
                            </b-row>
                        </b-col>

                        <!--Contact Person Details [Name, Number, Designation]-->
                        <b-col md="6">
                            <b-form-group>
                                <label>Contact Person Details [Name, Number, Designation]</label>
                                <validation-provider
                                    name="Contact Person Details [Name, Number, Designation]"
                                    #default="{ errors }"
                                >
                                    <b-form-input
                                        v-model="data.contactperson"
                                        :state="errors.length > 0 ? false:null"
                                        placeholder="Contact Person Details [Name, Number, Designation]"
                                    />
                                    <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                            </b-form-group>
                        </b-col>

                        <!--Contact Details -->
                        <b-col md="6">
                            <b-form-group>
                                <label>Contact Details</label>
                                <validation-provider
                                    #default="{ errors }"
                                    name="Contact Details"
                                    rules="max:500"
                                >
                                    <b-form-textarea
                                        v-model="data.contactDetails"
                                        placeholder="Contact Details"
                                        class="char-textarea"
                                        :class="data.contactDetails ? data.contactDetails.length >= maxChar500 ? 'text-danger' : '' : ''"
                                    />
                                    <small
                                        class="textarea-counter-value float-right"
                                        :class="data.contactDetails ? data.contactDetails.length >= maxChar500 ? 'bg-danger' : '' : ''"
                                    >
                                        <span class="char-count">{{
                                                data.contactDetails ? data.contactDetails.length : 0
                                            }}</span>
                                        / {{ maxChar500 }}
                                    </small>
                                    <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                            </b-form-group>
                        </b-col>

                    </b-row>
                </b-card-actions>
                <b-card-actions
                    border-variant="secondary"
                    title="Private Job Posting - Secondary Fields"
                    collapsed
                    action-collapse
                >
                    <b-row>

                        <!--Call Timing From -->
                        <b-col md="3">
                            <b-form-group>
                                <label>Call Timing From</label>
                                <b-form-input
                                    type="time"
                                    v-model="data.callTimeFrom"
                                    placeholder="Call Timing From"
                                />
                            </b-form-group>
                        </b-col>

                        <!--Call Timing To -->
                        <b-col md="3">
                            <b-form-group>
                                <label>Call Timing To</label>
                                <b-form-input
                                    type="time"
                                    v-model="data.callTimeTo"
                                    placeholder="Call Timing To"
                                />
                            </b-form-group>
                        </b-col>

                        <!--Email (,) separated -->
                        <b-col md="6">
                            <b-form-group>
                                <label>Email (,) separated</label>
                                <validation-provider
                                    #default="{ errors }"
                                    name="Email (,) separated"
                                    rules="min:10|max:100"
                                >
                                    <b-form-input
                                        v-model="data.email"
                                        :state="errors.length > 0 ? false:null"
                                        placeholder="Email (,) separated"
                                    />
                                    <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                            </b-form-group>
                        </b-col>

                        <!--Get response-->
                        <b-col md="12">
                            <b-row v-if="data.email">
                                <!--Get response by E-mail-->
                                <b-col md="4">
                                    <b-form-group>
                                        <label>Get response by E-mail</label>
                                        <b-form-checkbox
                                            v-model="data.responseMail"
                                            value="1"
                                        > Get response by E-mail
                                        </b-form-checkbox>
                                    </b-form-group>
                                </b-col>

                                <!--Get resume by E-mail-->
                                <b-col md="4">
                                    <b-form-group>
                                        <label>Get resume by E-mail</label>
                                        <b-form-checkbox
                                            v-model="data.resumeMail"
                                            value="1"
                                        > Get resume by E-mail
                                        </b-form-checkbox>
                                    </b-form-group>
                                </b-col>

                                <!--Send Verification Link-->
                                <b-col md="4">
                                    <b-button
                                        v-if="this.data.employerid"
                                        @click="sendVerificationLink"
                                        variant="primary"
                                    >
                                        Send Verification Link
                                    </b-button>
                                </b-col>

                            </b-row>
                        </b-col>

                        <!--Interview Date -->
                        <b-col md="3">
                            <b-form-group>
                                <label>Interview Date</label>
                                <validation-provider
                                    #default="{ errors }"
                                    name="Interview Date"
                                    rules="greaterthan:@StartDate,date"
                                >
                                    <flat-pickr
                                        :config="datePickerConfig"
                                        placeholder="Select Interview Date"
                                        v-model="data.interviewDate"
                                        class="form-control"
                                    />
                                    <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                            </b-form-group>
                        </b-col>

                        <!--Website-->
                        <b-col md="9">
                            <b-form-group>
                                <label>Website</label>
                                <validation-provider
                                    name="Website"
                                    #default="{ errors }"
                                    rules="url"
                                >
                                    <b-form-input
                                        v-model="data.website"
                                        :state="errors.length > 0 ? false:null"
                                        type="url"
                                        placeholder="Website"
                                    />
                                    <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                            </b-form-group>
                        </b-col>

                        <!--List View Image URL-->
                        <b-col md="6">
                            <b-form-group>
                                <label>List View Image URL</label>
                                <validation-provider
                                    name="List View Image URL"
                                    #default="{ errors }"
                                    rules="url"
                                >
                                    <b-form-input
                                        v-model="data.listViewImageURL"
                                        :state="errors.length > 0 ? false:null"
                                        type="url"
                                        placeholder="List View Image URL"
                                    />
                                    <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                            </b-form-group>
                        </b-col>

                        <!--Detailed View Image URL-->
                        <b-col md="6">
                            <b-form-group>
                                <label>Detailed View Image URL</label>
                                <validation-provider
                                    name="Detailed View Image URL"
                                    #default="{ errors }"
                                    rules="url"
                                >
                                    <b-form-input
                                        v-model="data.detailedViewImageURL"
                                        :state="errors.length > 0 ? false:null"
                                        type="url"
                                        placeholder="Detailed View Image URL"
                                    />
                                    <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                            </b-form-group>
                        </b-col>

                        <!--Banner Coding-->
                        <b-col md="12">
                            <b-form-group>
                                <label>Banner Coding</label>
                                <validation-provider
                                    name="Banner Web URL"
                                    #default="{ errors }"
                                    rules=""
                                >
                                    <b-form-input
                                        v-model="data.bannerWebURL"
                                        :state="errors.length > 0 ? false:null"
                                        placeholder="Banner Coding"
                                    />
                                    <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                            </b-form-group>
                        </b-col>

                    </b-row>
                </b-card-actions>
                <b-card-actions
                    border-variant="secondary"
                    action-collapse
                >
                    <b-row>
                        <b-col md="12">
                            <b-row class="mb-2">
                                <!--Single Group Post-->
                                <b-col md="4">
                                    <b-form-group>
                                        <b-form-radio-group
                                            @change="isDisabled"
                                            v-model="data.singleGroupPost"
                                        >
                                            <b-form-radio disabled value="1">Single Post</b-form-radio>
                                            <b-form-radio disabled value="2">Group Post</b-form-radio>
                                        </b-form-radio-group>
                                    </b-form-group>
                                </b-col>
                                <!--Send Notification-->
                                <b-col md="4">
                                    <b-form-checkbox
                                        disabled
                                        v-model="data.sendNotification"
                                        value="1"
                                        :disabled="data.singleGroupPost === '1' ? true : false"
                                    > Send Notification
                                    </b-form-checkbox>
                                    <!--                                        :disabled="data.singleGroupPost === '1' ? true : false"-->
                                </b-col>
                                <!-- remark show -->
                                <b-col md="4">
                                    <b-form-checkbox
                                        v-model="data.show_remark"
                                        value="1"
                                    > Remark Show
                                    </b-form-checkbox>
                                </b-col>
                                <!-- remark show /.-->
                            </b-row>
                        </b-col>
                        <b-col md="12">
                            <b-row>
                                <!-- Draft button -->
                                <b-col md="2">
                                    <b-button
                                        variant="danger"
                                        type="submit"
                                        @click.prevent="savedraft"
                                    >
                                        Save Draft
                                    </b-button>
                                </b-col>
                                <!-- Cancel button -->
                                <b-col md="2" offset-md="6">
                                    <b-button
                                        type="reset"
                                        v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                                        variant="outline-danger"
                                    >Cancel
                                    </b-button>
                                </b-col>
                                <b-col md="2">

                                    <!-- Save button -->
                                    <b-button
                                        v-if="!data.id && data.job_plan_id"
                                        variant="primary"
                                        type="submit"
                                        @click.prevent="validationForm"
                                    >
                                        Submit
                                    </b-button>
                                    <!-- Update button -->
                                    <b-button
                                        v-else-if="data.job_plan_id"
                                        variant="primary"
                                        type="submit"
                                        @click.prevent="validationForm"
                                    >
                                        Update
                                    </b-button>
                                </b-col>
                            </b-row>
                        </b-col>
                    </b-row>
                </b-card-actions>
            </b-form>
        </validation-observer>
        <b-modal
            no-close-on-esc
            hide-header-close
            no-close-on-backdrop
            id="modal-postCountForm"
            ref="postCountForm"
            cancel-variant="outline-secondary"
            hide-footer
            centered
            title="Total post"
        >
            <validation-observer ref="simpleRules1">
                <b-form>
                    <b-form-group
                        label="Post Count"
                        label-for="Post Count"
                    >
                        <validation-provider
                            #default="{ errors }"
                            name="Post Count"
                            rules="required|max:1|lessthan:5,int"
                        >
                            <b-form-input
                                type="number"
                                v-model="postCount"
                                :state="errors.length > 0 ? false:null"
                                placeholder="Post Count"
                            />
                            <small class="text-danger">{{ errors[0] }}</small>
                        </validation-provider>
                    </b-form-group>

                    <!-- submit button -->
                    <div class="float-right">
                        <b-col>
                            <b-button
                                variant="primary"
                                type="submit"
                                @click.prevent="postCountUpdate"
                            >
                                Submit
                            </b-button>
                        </b-col>
                    </div>
                </b-form>
            </validation-observer>
        </b-modal>
    </section>
</template>

<script>
import BCardActions from '@core/components/b-card-actions/BCardActions.vue'
import { ValidationProvider, ValidationObserver, extend } from 'vee-validate'
import {
    BCard,
    BSpinner,
    BFormInput,
    BFormGroup,
    BForm,
    BRow,
    BCol,
    BButton,
    BFormFile,
    BFormSelect,
    BFormTextarea,
    BFormCheckbox,
    BFormRadio,
    BFormRadioGroup,
    BCardText,
    BBadge,
} from 'bootstrap-vue'
import { required, required_if } from 'vee-validate/dist/rules'
import {
    email, confirmed, url, between, alpha, integer, password, min, digits, alphaDash, length, max,
} from '@validations'

import vSelect from 'vue-select'
import flatPickr from 'vue-flatpickr-component'
import FirebaseDataService from '@/libs/firebase-keyword-db-web'
import Ripple from 'vue-ripple-directive'
import Multiselect from 'vue-multiselect'
import Treeselect from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'
import VueMoment from 'vue-moment'
import moment from 'moment-timezone'

extend('required_if', required_if)

extend('lessthan', {
    params: ['toValue', 'type'],
    validate(value, {
        toValue,
        type
    }) {
        if (toValue) {
            console.log(type + ' typed value = ' + value + ' lessthan' + toValue)
            if (type === 'date') {
                return new Date(value) <= new Date(toValue)
            } else {
                return Number(value) <= Number(toValue)
            }
        } else {
            return true
        }
    },
    message: 'Invalid Range'
})

extend('greaterthan', {
    params: ['fromValue', 'type'],
    validate(value, {
        fromValue,
        type
    }) {
        if (fromValue) {
            console.log(type + ' typed value = ' + value + ' greaterthan ' + fromValue)
            if (type === 'date') {
                return new Date(value) >= new Date(fromValue)
            } else {
                return Number(value) >= Number(fromValue)
            }
        } else {
            return true
        }
    },
    message: 'Invalid Range'
})

export default {
    components: {
        BSpinner,
        BCardActions,
        BCardText,
        ValidationProvider,
        ValidationObserver,
        extend,
        BFormInput,
        BFormSelect,
        BFormGroup,
        BForm,
        BFormFile,
        BRow,
        BCol,
        BButton,
        BFormTextarea,
        BBadge,
        vSelect,
        Multiselect,
        Treeselect,
        flatPickr,
        BFormCheckbox,
        BFormRadio,
        BFormRadioGroup,
        VueMoment,
    },
    directives: {
        Ripple,
    },
    data() {
        return {
            data: {
                jobtype: 1,
                employerName: '',
                employerid: '',
                employer: '',
                job_plan_id: '',
                paid_post: '',
                paid_date: '',
                paid_amt: '',
                jobtitle_id: '',
                jobtitle: '',
                jobtitleName: '',
                jobDetails: '',
                skills: [],
                startDate: '',
                endDate: '',
                experienceFrom: '',
                experienceTo: '',
                experienceRemarks: '',
                joblocation: [],
                joblocationRemarks: '',
                candidateLocation: [],
                gender: 2,
                marital_status: 2,
                ageLimitFrom: '',
                ageLimitTo: '',
                noofvancancy: '',
                tags: '',
                // jobSource: "",
                address: '',
                landmark: '',
                mapURL: '',
                singlequalification: [],
                groupqualification: [],
                singleTextFromGroupQualification: '',
                mobileSeperate: '',
                whatsappNumber: '',
                email: '',
                responseCall: 1,
                responseMail: 0,
                resumeMail: 0,
                callTimeFrom: '',
                callTimeTo: '',
                salaryFrom: '',
                salaryTo: '',
                salaryRemarks: '',
                key_word_db_web: [],
                interviewDate: '',
                workmode: 1,
                contactperson: '',
                contactDetails: '',
                website: '',
                listViewImageURL: '',
                detailedViewImageURL: '',
                bannerWebURL: '',
                verified: '1',
                singleGroupPost: '',
                sendNotification: '',
                show_remark: '',
            },
            initial_loading: true,
            datePickerConfig: {
                altFormat: 'd - M - Y',
                altInput: true,
                dateFormat: 'Y-m-d',
            },
            job_plan_id: null,
            disableStartEnddate: false,
            postCount: '',
            isExperience: '0',
            isAgeLimit: '0',
            isSalary: '0',
            variants: ['light', 'primary', 'secondary', 'danger', 'warning', 'success', 'info', 'dark'],
            options: [],
            selectArea: [],
            jobtitle_options: [],
            current_plan_data: [],
            employer_data: [],
            select_job_source: [],
            selectReferredby: [],
            selectSkills: [],
            selectSingleQualification: [],
            selectGroupQualification: [],
            singleFromGroupQualification: [],
            key_word_db_web: [],
            jobtitleResponse: [],
            city_keyword_data: [],
            sQual_keyword_data: [],
            gQual_keyword_data: [],
            location_keyword_data: [],
            continuePosting: '',
            maxChar500: 500,
            maxChar1000: 1000,
            required,
            min,
            max,
            userData: JSON.parse(localStorage.getItem('userData')),
        }
    },
    async created() {
        const callAxios = await this.callAxios('/getprivatejobpostmaster', {}, 'post')
        this.jobtitle_options = callAxios.data.searchJobTitle_v1
        this.selectSkills = callAxios.data.skillsDrop
        // this.select_job_source = callAxios.data.jobSource;
        this.selectReferredby = callAxios.data.Referredby
        this.selectSingleQualification = callAxios.data.SingleQualification
        // console.log(this.selectSingleQualification)
        this.selectGroupQualification = callAxios.data.GroupQualification
        // console.log(typeof callAxios.data.GroupQualification);
        // console.log(callAxios.data.GroupQualification);
        // console.log(typeof callAxios.data.areaDrop)
        // console.log(callAxios.data.areaDrop)
        this.selectArea = callAxios.data.areaDrop
        // console.log(this.selectArea)
        // console.log(this.$route.query.id)
        // console.log(this.$route.query.table)
        if (this.$route.query.table) {
            this.data = this.$route.query
            this.data.jobtype = 1
            const callAxios1 = await this.callAxios('/getjobdetails', this.data, 'post')
            // console.log(callAxios1.data[0])
            var return_data = callAxios1.data[0]
            if (return_data) {
                this.options = [{
                    id: return_data.employerid,
                    name: return_data.companyname
                }]
                this.employerSelected(return_data.employerid, return_data.job_plan_id, (return_data.id) ? return_data.id : return_data.draft_id, '1')
                // this.jobtitle_options = [{id: return_data.jobtitle_id, name: return_data.jobtitlename}]
                return_data.singleTextFromGroupQualification = return_data.qualification
                return_data.singlequalification = return_data.singlequalification.split(',')
                    .filter(element => element)
                    .map(Number)
                return_data.groupqualification = return_data.groupqualification.split(',')
                    .filter(element => element)
                    .map(Number)
                if (return_data.singlequalification.includes(1206)) {
                    return_data.groupqualification.push(1206)
                    return_data.singlequalification.pop(1206)
                }
                if (!return_data.verified) {
                    return_data.verified = null
                }
                return_data.key_word_db_web = return_data.keyword.split(',')
                    .filter(element => element)
                return_data.key_word_db_web = this.removeDuplicates(return_data.key_word_db_web)
                return_data.skills = return_data.skills.split(',')
                    .filter(element => element)
                    .map(Number)
                return_data.jobtitle_id = (return_data.jobtitle_id) ? Number(return_data.jobtitle_id) : ''
                // return_data.jobSource = (return_data.jobSource) ? Number(return_data.jobSource) : '';
                return_data.experienceFrom = (return_data.experienceFrom || return_data.experienceFrom == '0') ? Number(return_data.experienceFrom) : ''
                return_data.experienceTo = (return_data.experienceTo || return_data.experienceTo == '0') ? Number(return_data.experienceTo) : ''
                // console.log(return_data.location);

                return_data.joblocation = (return_data.joblocation) ? return_data.joblocation.split(',')
                    .filter(element => element) : []
                return_data.candidateLocation = (return_data.candidateLocation) ? return_data.candidateLocation.split(',')
                    .filter(element => element) : []
                return_data.responseCall = (return_data.responseCall) ? 0 : 1
                return_data.responseMail = (return_data.responseMail) ? 1 : 0
                return_data.resumeMail = (return_data.resumeMail) ? 1 : 0
                return_data.interviewDate = (return_data.interviewDate == '0000-00-00') ? '' : return_data.interviewDate
                this.isAgeLimit = (return_data.ageLimitFrom || return_data.ageLimitTo) ? 1 : 0
                this.isSalary = (return_data.salaryFrom || return_data.salaryTo) ? 1 : 0
                this.isExperience = (return_data.experienceFrom || return_data.experienceTo) ? 2 : (return_data.experienceFrom == '0' && return_data.experienceTo == '0' ? 1 : 0)
                // console.log(return_data)
                this.settitle(return_data.jobtitle_id)
                return_data.show_remark = (return_data.show_remark) ? 1 : 0
                this.data = return_data
                this.$router.replace('/posting/private/')
            } else {
                this.sweetAlertmethod('error', 'Warning', 'No Job Found', 'btn-primary')
                this.$router.go(-1)
            }
        } else {
            // alert("New")
        }
        // console.log(this.getWithExpiry('key_word_db_web'));
        if (this.getWithExpiry('key_word_db_web')) {
            // console.log('local');
            this.key_word_db_web = this.getWithExpiry('key_word_db_web')
            this.key_word_db_web = this.key_word_db_web.sort()
        } else {
            // console.log('server');
            this.key_word_db_web = await FirebaseDataService.getAll()
            this.key_word_db_web = this.key_word_db_web.sort()
            this.setWithExpiry('key_word_db_web', this.key_word_db_web, 43200) //12 hr
        }
        this.initial_loading = false
        // console.log(callAxios2)
        // console.log(this.check())
    },
    methods: {
        onemployersearch(search, loading) {
            if (search.length > 4) {
                loading(true)
                this.employersearch(loading, search, this)
            }
        },
        employersearch: _.debounce(async (loading, search, vm) => {
            try {
                const response = await axios.post('/searchPrivateEmployer', {
                    search_term: search
                })
                vm.options = response.data
                // console.log(response.data);
            } catch (error) {
                console.error(error)
            } finally {
                loading(false)
            }

            loading(false)
        }, 350),
        selectAlljoblocation() {
            var locations = []
            this.selectArea.forEach((value, index) => {
                locations.push(value.id)
            })
            this.data.joblocation = locations.join(',')
                .split(',')
            // console.log(typeof this.data.joblocation);
        },
        deselectAlljoblocation() {
            this.data.joblocation = null
        },
        selectAllcandidateLocation() {
            var locations = []
            this.selectArea.forEach((value, index) => {
                locations.push(value.id)
                // this.data.candidateLocation.push(value.id);
            })
            // console.log(this.data.candidateLocation);
            this.data.candidateLocation = locations.join(',')
                .split(',')
        },
        deselectAllcandidateLocation() {
            this.data.candidateLocation = null
        },
        sendVerificationLink() {
            var email_array = this.data.email.split(',')
            window.open('https://nithrajobs.com/admin/employer_invoice.php?employer_id=' + this.data.employer + '&email=' + email_array[0], '_blank')
        },
        isDisabled() {
            if (this.data.singleGroupPost === '1') {
                this.data.sendNotification = 1
            } else {
                this.data.sendNotification = ''
            }
        },
        selected_keywords(values) {
            var last_value = values.at(-1)
            if (this.key_word_db_web.includes(last_value)) {
            } else {
                FirebaseDataService.create(last_value)
            }

            // console.log();

        },
        validationForm() {
            // console.log(this.$refs.simpleRules)
            // return;
            this.$refs.simpleRules.validate()
                .then(async success => {
                    if (success) {
                        this.initial_loading = true
                        // eslint-disable-next-line
                        // alert('form submitted!')
                        // this.data.draft_id = 0;
                        this.data.table = 'jobs'
                        this.data.inby = this.userData.id
                        // console.log(this.data)
                        const config = {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        }
                        var callAxios = await this.callAxiosWithConfig('/savegovtjobs', this.data, 'post', config)
                        if (callAxios.status === 200) {
                            console.log(this.data)
                            let successText = 'Success 👍'
                            if (this.data.id && this.data.table == 'jobs') {
                                successText = 'Updated 👍'
                            }
                            this.sweetAlertmethod('success', successText, '', 'btn-primary')
                            this.initial_loading = false
                            if (this.current_plan_data.balance_count > 1) {
                                this.employerSelected(this.data.employerid, this.data.job_plan_id, 0, 1)
                                this.data.jobtitle_id = ''
                                this.data.jobtitle = ''
                                this.data.jobtitleName = ''
                                this.data.remarkShow = ''
                                this.data.skills = []
                            } else {
                                this.$router.go()
                            }
                            // if (this.data.jobtype === 1) {
                            //     this.$router.push('/listing/private');
                            // } else {
                            //     this.$router.push('/listing/appName');
                            // }
                        } else {
                            this.initial_loading = false
                            this.sweetAlertmethod('error', 'Alert', callAxios.response.data.message, 'btn-primary')
                        }
                    } else {
                        const onlyErrors = []
                        console.log(this.$refs.simpleRules.errors)
                        for (var key in this.$refs.simpleRules.errors) {
                            if (this.$refs.simpleRules.errors.hasOwnProperty(key) && this.$refs.simpleRules.errors[key] != '') {
                                onlyErrors.push(key + ' -> ' + this.$refs.simpleRules.errors[key])
                            }
                        }
                        this.sweetAlertmethod('error', 'Alert', onlyErrors.join('<br><br>'), 'btn-primary')
                        console.log(onlyErrors)
                    }
                })
        },
        async savedraft() {
            this.initial_loading = true
            this.data.id = 0
            this.data.table = 'draft_jobs'
            this.data.inby = this.userData.id
            // console.log(this.data)
            const config = {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
            var callAxios = await this.callAxiosWithConfig('/savegovtjobs', this.data, 'post', config)
            if (callAxios.status === 200) {
                this.sweetAlertmethod('success', 'Success', '', 'btn-primary')
                // this.$router.go();
                if (this.current_plan_data.balance_count > 1) {
                    this.employerSelected(this.data.employerid, this.data.job_plan_id, 0, 1)
                    this.data.jobtitle_id = ''
                    this.data.jobtitle = ''
                    this.data.jobtitleName = ''
                    this.data.skills = []
                } else {
                    this.continuePosting = ''
                    this.$bvModal.msgBoxConfirm('Do you want to continue this posting ?', {
                        title: 'Please Confirm',
                        size: 'lg',
                        okVariant: 'primary',
                        okTitle: 'YES',
                        cancelTitle: 'NO',
                        footerClass: 'p-2',
                        hideHeaderClose: true,
                        centered: true,
                        noCloseOnEsc: true,
                        noCloseOnBackdrop: true
                    })
                        .then(value => {
                            this.continuePosting = value
                            if (this.continuePosting) {
                                this.initial_loading = false
                                this.employerSelected(this.data.employerid, this.data.job_plan_id, 0, 1)
                                this.data.jobtitle_id = ''
                                this.data.jobtitle = ''
                                this.data.jobtitleName = ''
                                this.data.skills = []
                            } else {
                                this.$router.go()
                            }
                            // console.log(this.continuePosting)
                        })
                        .catch(err => {
                            // An error occurred
                        })
                    // this.$router.go()
                    // alert(this.current_plan_data.balance_count)
                    // return;
                }
            } else {
                this.initial_loading = false
                this.sweetAlertmethod('error', 'Alert', callAxios.response.data.message, 'btn-primary')
            }

        },
        removeDuplicates(array_passed) {
            return array_passed.filter(function (value, index, array) {
                if (value) {
                    return array.indexOf(value) === index
                }
            })
        },
        async settitle(value) {
            // console.log(value);
            if (value) {
                // console.log(this.jobtitleResponse.key_words)
                // console.log(typeof this.jobtitleResponse.key_words)
                // console.log(this.jobtitleResponse)
                //Remove Existing Keywords based on jobtitle
                if (this.jobtitleResponse.key_words) {
                    this.data.key_word_db_web = this.data.key_word_db_web.filter((el) => !this.jobtitleResponse.key_words.includes(el))
                }

                var aa = this.jobtitle_options.find(x => x.id === value)
                console.log(this.jobtitle_options)
                console.log('bala')
                this.data.jobtitle = this.data.jobtitleName = aa.name

                if (this.data.table === 'jobs' && this.data.id) {

                } else {

                    var callAxios = await this.callAxios('/keywords_temp', {
                        table_name: 'jobtitle_temp',
                        columns: ['jobtitle_id', 'jobskills_ids', 'keywords'],
                        conditions: [{
                            'is_active': 1,
                            'jobtitle_id': value
                        }]
                    }, 'post')
                    this.jobtitleResponse = callAxios.data

                    this.data.skills = []

                    if (this.jobtitleResponse) {
                        this.data.skills = this.jobtitleResponse.skills
                        // console.log(this.data.key_word_db_web)
                        // console.log(this.jobtitleResponse.key_words)
                        this.data.key_word_db_web = this.data.key_word_db_web
                            .concat(
                                this.jobtitleResponse.key_words
                            )
                        this.data.key_word_db_web = this.removeDuplicates(this.data.key_word_db_web)
                        // console.log(this.data.key_word_db_web)
                    }
                }
            } else {
                this.data.jobtitle = this.data.jobtitleName = ''
            }
        },
        async setsingleFromGroupQualification(value) {
            // console.log(value);
            const selectGroupQualification1 = this.selectGroupQualification
            const singleFromGroupQualification = []
            value.forEach(function (value, key) {
                // console.log(key+"--"+value);
                var aa = selectGroupQualification1.find(x => x.id === value)
                // console.log(aa.groupcourse);
                singleFromGroupQualification.push(aa.groupcourse)
            })
            this.data.singlequalification = [],
                this.data.singleTextFromGroupQualification = singleFromGroupQualification.join()
            var callAxios = await this.callAxios('/getModifiedSingleQualification', {
                id: this.data.singleTextFromGroupQualification,
            }, 'post')
            // console.log(callAxios.data)
            this.selectSingleQualification = callAxios.data

            if (this.gQual_keyword_data.key_words) {
                // console.log("11"+this.gQual_keyword_data.key_words);
                this.data.key_word_db_web = this.data.key_word_db_web.filter((el) => !this.gQual_keyword_data.key_words.includes(el))
            }

            var callAxios = await this.callAxios('/keywords_temp', {
                table_name: 'group_qualification_temp',
                columns: ['course_id', 'keywords'],
                conditions: [{
                    'is_active': 1,
                    'course_id': value
                }]
            }, 'post')
            this.gQual_keyword_data = callAxios.data

            if (this.gQual_keyword_data) {
                this.data.key_word_db_web = this.data.key_word_db_web
                    .concat(
                        this.gQual_keyword_data.key_words
                    )
                this.data.key_word_db_web = this.removeDuplicates(this.data.key_word_db_web)
                // console.log(this.data.key_word_db_web)
            } else {
                this.data.key_word_db_web = this.data.key_word_db_web.filter((el) => !this.gQual_keyword_data.key_words.includes(el))
            }

            // console.log(this.data.singleTextFromGroupQualification);
        },
        async employerSelected(value, job_plan_id = '', job_id = 0, continuePosting = 0) {
            // console.log(value)
            if (value) {
                // console.log(this.city_keyword_data.key_words)
                // console.log(typeof this.city_keyword_data.key_words)
                if (this.city_keyword_data.key_words) {
                    this.data.key_word_db_web = this.data.key_word_db_web.filter((el) => !this.city_keyword_data.key_words.includes(el))
                }

                this.employer_data = this.current_plan_data = []
                var aa = this.options.find(x => x.id === value)
                this.data.employerName = aa.name.trim()
                const callAxios = await this.callAxios('/getPrivateEmployerDetails', {
                    'job_plan_id': job_plan_id,
                    'employer_id': value,
                }, 'post')
                const employerDetails = callAxios.data
                console.log(callAxios.data)
                console.log('bala')
                this.employer_data = employerDetails.employer_data[0]
                if (!this.employer_data) {
                    this.sweetAlertmethod('error', 'Alert!', employerDetails.message, 'btn-primary')
                    this.data.employerid = this.data.employerName = ''
                    this.data.joblocation = this.data.candidateLocation = []
                } else {
                    this.data.sendNotification = ''
                    this.data.singleGroupPost = '2'
                    if (!employerDetails.current_plan_data) {
                        // alert()
                        this.sweetAlertmethod('error', 'No Invoice Found!', '', 'btn-primary')
                        // this.data.employerid = this.data.employerName = "";
                        // this.data.joblocation = this.data.candidateLocation = [];
                        this.current_plan_data.job_validity = 4
                        this.job_plan_id = this.data.job_plan_id = null
                    } else {
                        this.current_plan_data = employerDetails.current_plan_data
                        if (this.current_plan_data.total_posted_jobs === 0 && Number(this.current_plan_data.txn_amt) < 1) {
                            this.$refs['postCountForm'].show()
                        }
                        this.job_plan_id = this.data.job_plan_id = this.current_plan_data.debit_id
                        this.data.paid_post = (this.current_plan_data.txn_amt > 0) ? 1 : 0
                        this.data.paid_amt = this.current_plan_data.txn_amt
                        this.data.paid_date = this.current_plan_data.starting_date
                        this.current_plan_data.starting_date = moment(this.current_plan_data.starting_date)
                            .format('DD / MM / YYYY')
                        this.current_plan_data.expired_date = moment(this.current_plan_data.expired_date)
                            .format('DD / MM / YYYY')
                        if (this.current_plan_data.balance_count === 1) {
                            this.data.sendNotification = '1'
                        }
                        if (this.current_plan_data.post_count === 1) {
                            this.data.singleGroupPost = '1'
                        }
                    }

                    if (job_id) {
                        // alert(1)
                        this.data.employerName = this.employer_data.name
                    } else {
                        // alert(2)
                        if (!this.data.joblocation) {
                            this.data.joblocation = this.data.candidateLocation = [this.employer_data.area_city_id]
                        }
                        // console.log(this.data.joblocation)
                        if (employerDetails.city_keyword_data) {
                            this.city_keyword_data = employerDetails.city_keyword_data
                            this.data.key_word_db_web = this.data.key_word_db_web
                                .concat(
                                    this.city_keyword_data.key_words
                                )
                            this.data.key_word_db_web = this.removeDuplicates(this.data.key_word_db_web)
                        }
                        if (!continuePosting) {
                            this.data.mobileSeperate = this.employer_data.phonenumber
                            this.data.whatsappNumber = this.employer_data.whatsappNumber
                            this.data.email = this.employer_data.email
                            this.data.employer = this.employer_data.employer_register_id
                            this.data.contactperson = this.employer_data.firstname + ', ' + this.employer_data.phonenumber
                            this.data.contactDetails = 'Telecalling By ' + this.userData.name

                            var fullAddress = this.employer_data.address.split('#@#')
                            var streetAddress = fullAddress[0].trim()
                            var area_name = this.employer_data.area_name
                            var dist = this.employer_data.dist
                            var display_dist = area_name === dist ? area_name : area_name + ', ' + dist
                            var pincode = fullAddress[3].trim()
                            this.data.address = streetAddress + ',\n'
                                + display_dist + ' - '
                                + pincode + '.'
                            if (this.employer_data.landmark) {
                                this.data.address += '\nLandmark : ' + this.employer_data.landmark
                            }

                        }
                        var checkStartDate = this.checkStartDate(this.current_plan_data.job_validity)
                        console.log(checkStartDate)
                        this.data.startDate = checkStartDate.start_date_v1
                        this.data.endDate = checkStartDate.end_date
                    }
                }
            } else {
                this.data.employerName = ''
            }

        },
        copyFromMobile() {
            this.data.whatsappNumber = this.data.mobileSeperate.replace(/\D/g, '')
                .split('')
                .slice(0, 10)
                .join('')
            // console.log(this.data.mobileSeperate.replace(/\D/g,'').split('').slice(0,10));
        },
        clearWhatsappNumber() {
            this.data.whatsappNumber = ''
        },
        checkStartDate(validity) {
            var date = new Date()
            var now = moment(date)
            var checked_time = moment()
            var timeToCheck = checked_time.hour(12)
                .minute(1)
            // console.log(now.format("LLLL"))
            // console.log(timeToCheck.format("LLLL"))

            if (now.isAfter(timeToCheck)) {
                var start_date = now.add(1, 'day')
            } else {
                var start_date = now
            }
            var start_date_v1 = start_date.format('YYYY-MM-DD')
            var end_date = start_date.add(validity, 'days')
                .format('YYYY-MM-DD')
            return {
                'start_date': start_date,
                'start_date_v1': start_date_v1,
                'end_date': end_date
            }
        },
        async singlequalificationSelected(value) {
            var selected_value = []
            // console.log(selected_value)
            value.forEach(loopFunction)

            function loopFunction(item, index) {
                if (typeof item === 'string') {
                    var new_items = item.split(',')
                        .map(Number)
                    selected_value = selected_value.concat(new_items)
                } else {
                    selected_value.push(item)
                }
            }

            // console.log("00" + selected_value)

            if (selected_value.length) {
                // console.log("---" + this.sQual_keyword_data);
                if (this.sQual_keyword_data.key_words) {
                    // console.log("11"+this.sQual_keyword_data.key_words);
                    this.data.key_word_db_web = this.data.key_word_db_web.filter((el) => !this.sQual_keyword_data.key_words.includes(el))
                }

                var callAxios = await this.callAxios('/keywords_temp', {
                    table_name: 'qualification_temp',
                    columns: ['course_id', 'keywords'],
                    conditions: [{
                        'is_active': 1,
                        'course_id': selected_value
                    }]
                }, 'post')
                this.sQual_keyword_data = callAxios.data

                if (this.sQual_keyword_data) {
                    this.data.key_word_db_web = this.data.key_word_db_web
                        .concat(
                            this.sQual_keyword_data.key_words
                        )
                    this.data.key_word_db_web = this.removeDuplicates(this.data.key_word_db_web)
                    // console.log(this.data.key_word_db_web)
                } else {
                    this.data.key_word_db_web = this.data.key_word_db_web.filter((el) => !this.sQual_keyword_data.key_words.includes(el))
                }
            } else {
                this.data.key_word_db_web = this.data.key_word_db_web.filter((el) => !this.sQual_keyword_data.key_words.includes(el))
            }

        },
        async joblocationSelected(value) {
            // console.log(value);
            var selected_value = []
            value.forEach(loopFunction)

            console.log(value)

            function loopFunction(item, index) {
                console.log(typeof item)
                if (typeof item === 'number') {
                    selected_value.push(item)
                } else {
                    var new_items = item.split(',')
                        .map(Number)
                    selected_value = selected_value.concat(new_items)
                }
            }

            // console.log("00" + selected_value)

            if (selected_value.length) {
                // console.log("---" + this.location_keyword_data);
                if (this.location_keyword_data.key_words) {
                    // console.log("11"+this.location_keyword_data.key_words);
                    this.data.key_word_db_web = this.data.key_word_db_web.filter((el) => !this.location_keyword_data.key_words.includes(el))
                }

                var callAxios = await this.callAxios('/keywords_temp', {
                    table_name: 'city_temp',
                    columns: ['city_id', 'keywords'],
                    conditions: [{
                        'is_active': 1,
                        'city_id': selected_value
                    }]
                }, 'post')
                this.location_keyword_data = callAxios.data

                if (this.location_keyword_data) {
                    this.data.key_word_db_web = this.data.key_word_db_web
                        .concat(
                            this.location_keyword_data.key_words
                        )
                    this.data.key_word_db_web = this.removeDuplicates(this.data.key_word_db_web)
                    // console.log(this.data.key_word_db_web)
                } else {
                    this.data.key_word_db_web = this.data.key_word_db_web.filter((el) => !this.location_keyword_data.key_words.includes(el))
                }
            } else {
                this.data.key_word_db_web = this.data.key_word_db_web.filter((el) => !this.location_keyword_data.key_words.includes(el))
            }

        },
        async postCountUpdate() {
            if (this.postCount) {
                const passData = {
                    debit_id: this.current_plan_data.debit_id,
                    post_count: Number(this.postCount),
                }
                // console.log(passData);
                const callAxios = await this.callAxios('/freePostCountUpdate', passData, 'post')
                if (callAxios.status === 200) {
                    // console.log(passData);
                    this.$refs['postCountForm'].hide()
                    this.sweetAlertmethod('success', 'Count Updated 👍', '', 'btn-primary')
                    this.current_plan_data.post_count = this.current_plan_data.balance_count = this.postCount
                    this.data.sendNotification = ''
                    this.data.singleGroupPost = '2'
                    if (this.current_plan_data.balance_count === '1') {
                        this.data.sendNotification = '1'
                        this.data.singleGroupPost = '1'
                    }
                    if (this.current_plan_data.post_count === 1) {
                        this.data.singleGroupPost = '1'
                    }
                } else {
                    this.sweetAlertmethod('error', 'Error', callAxios.response.data.message, 'btn-primary')
                }
            } else {
                this.sweetAlertmethod('error', 'Error', 'Please Enter Valid Data', 'btn-primary')
            }
        }
    },
    watch: {
        isExperience: function (val) {
            switch (val) {
                case '0':
                    this.data.experienceFrom = this.data.experienceTo = ''
                    break
                case '1':
                    this.data.experienceFrom = this.data.experienceTo = 0
                    break
                case '2':
                    this.data.experienceFrom = this.data.experienceTo = ''
            }
        },
        isSalary: function (val) {
            if (val == '0') {
                this.data.salaryFrom = this.data.salaryTo = ''
            }
        },
        isAgeLimit: function (val) {
            if (val == '0') {
                this.data.ageLimitFrom = this.data.ageLimitTo = ''
            }
        },
        job_plan_id: function (val) {
            this.disableStartEnddate = false
            if (val) {
                this.disableStartEnddate = true
            }
        },
    },
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';

</style>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
