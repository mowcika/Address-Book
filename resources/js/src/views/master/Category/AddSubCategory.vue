<template>
    <div>
        <b-overlay
            :show="show"
            rounded="sm"
        >
            <b-card-code class="col-md-8 offset-md-2" title="Add Sub Category">
                <validation-observer ref="submitform">
                    <b-form enctype="multipart/form-data">
                        <b-row class="mt-1">
                            <b-col cols="12">
                                <b-form-group>
                                    <label class="required">Select Category</label>
                                    <validation-provider
                                        #default="{ errors }"
                                        name="Select Category"
                                        rules="required"
                                    >
                                        <v-select
                                            v-model="category"
                                            :options="openTypeArray"
                                            :reduce="item=>item.id"
                                            label="name"

                                        />

                                        <small class="text-danger">{{ errors[0] }}</small>
                                    </validation-provider>
                                </b-form-group>
                            </b-col>
                        </b-row>
                        <br>
                        <b-card-code border-variant="primary">
                            <b-row>
                                <b-col>
                                    <b-tabs>


                                        <template v-for="data in langArray"
                                        >
                                            <b-tab
                                                :title="data.lang"
                                            >
                                                <label>Sub Category Name In {{ data.lang }}</label>

                                                <validation-provider
                                                    #default="{ errors }"
                                                    :name="`Sub Category Name In ${data.lang}`"
                                                >
                                                    <b-form-input
                                                        v-model="Prolist[data.id]"
                                                        :placeholder="`Sub Category Name In ${data.lang}`"
                                                        :state="errors.length > 0 ? false:null"
                                                    />
                                                    <small class="text-danger">{{ errors[0] }}</small>
                                                </validation-provider>

                                            </b-tab>
                                        </template>
                                    </b-tabs>
                                </b-col>
                            </b-row>
                        </b-card-code>

                        <b-row class="mt-1">
                            <b-col>
                                <b-button
                                    variant="outline-secondary"
                                    @click="resetData"
                                >
                                    Reset
                                </b-button>
                                <b-button
                                    class="float-right"
                                    variant="primary"
                                    @click="validationForm"
                                >
                                    {{ EditID != '' ? 'Update' : 'Submit' }}
                                </b-button>
                            </b-col>
                        </b-row>
                    </b-form>
                </validation-observer>


            </b-card-code>
            <b-card-code title="Sub Category's">
                <div>
                    <!-- search input -->
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
                        ref="myTable"
                        :columns="columns"
                        :fixed-header="true"
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
                  @click="change_edit(props.row.id)"
              >
                <feather-icon
                    class="mr-50"
                    icon="Edit2Icon"
                />
                <span>Edit</span>
              </b-dropdown-item>
              <b-dropdown-item @click="callDeleteMethod('/deleteCat', data={id:props.row.id,user:cby}, 'post')">
                <feather-icon
                    class="mr-50"
                    icon="TrashIcon"
                />
                <span>Delete</span>
              </b-dropdown-item>
            </b-dropdown>
          </span>
        </span>

                            <!-- Column: Image -->
                            <span v-else-if="props.column.field === 'logo'">
          <span v-if="props.row.logo">
              <img :src="props.row.logo" height="50px" width="50px"/>
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
                </div>
            </b-card-code>
        </b-overlay>
    </div>
</template>

<script>

import { ValidationProvider, ValidationObserver } from 'vee-validate'
import BCardCode from '@core/components/b-card-code'
import {
    BFormInput, BFormGroup, BForm, BRow, BCol, BFormSelect, BDropdown,
    BDropdownItem, BFormFile, BButton, BPagination, BCardText, BOverlay, BTabs, BTab, BAlert, BAvatar, BBadge
} from 'bootstrap-vue'
import { required } from '@validations'
import { VueGoodTable } from 'vue-good-table'
import store from '@/store'
import { getUserData } from '@/auth/utils'
import vSelect from 'vue-select'

const userData = getUserData()
export default {

    components: {
        vSelect,
        BBadge,
        BAvatar,
        BTabs,
        BOverlay,
        BTab,
        BAlert,
        BFormFile,
        ValidationProvider,
        VueGoodTable,
        BPagination,
        ValidationObserver,
        BFormInput,
        BFormGroup,
        BCardText,
        BFormSelect,
        BForm,
        BRow,
        BCol,
        BButton,
        BCardCode,
        BDropdown,
        BDropdownItem,
    },
    data: () => ({
        pageLength: 5,
        dir: false,
        ImgShow: '',
        Prolist: {},
        person_proofImg: '',
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
                label: 'Main Category',
                field: 'category',
                filterOptions: {
                    enabled: true,
                    placeholder: 'Search Main Category',
                },
            },
            {
                label: 'Sub Category',
                field: 'sub_category',
                filterOptions: {
                    enabled: true,
                    placeholder: 'Search Sub Category',
                },
            },

        ],
        EditID: '',
        rows: [],
        langArray: [],
        total: 5,
        cby: userData.id,
        category: '',
        searchTerm: '',
        product_img: [],
        show: false,
        filesdata: '',
        required: true,
        required1: 1,

    }),
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
    async created() {
        this.common_category()
        this.subcategory()
        this.common_data()
        if (this.$route.query.id) {
            this.change_edit(this.$route.query.id)

        }
    },
    methods: {
        resetData() {
            this.Prolist = {}
            this.product_img = []
            this.ImgShow = this.EditID = ''
        },
        validationForm() {
            this.$refs.submitform.validate()
                .then(async success => {
                    if (success) {
                        this.show = true
                        const data = new FormData()
                        data.append('prolist', JSON.stringify(this.Prolist))
                        data.append('catname', this.Prolist[1])
                        if (this.$route.query.id) {
                            data.append('id', this.$route.query.id)
                        }

                        data.append('cby', userData.id)
                        data.append('mainCat', this.category)
                        const data_send = await this.callAxios('/addSubCat', data, 'post')
                        if (data_send.status === 200) {
                            this.show = false
                            this.sweetAlertmethod(data_send.data.button, data_send.data.button, data_send.data.notify, 'btn-primary')
                            if (!data_send.data.msg) {
                                this.Prolist = {}
                                this.product_img = []
                                this.ImgShow = this.EditID = this.category = ''
                                requestAnimationFrame(() => {
                                    this.$refs.submitform.reset()
                                    if (this.$route.query.id) {
                                        this.$router.push({ path: '/Add/SubCategory' })
                                    }

                                })
                                this.common_category()
                            }
                        }
                    }
                })
        },
        removeImage() {
            this.product_img = []
            this.ImgShow = ''
            this.required = true
        },
        async common_data() {
            const view_data = await this.callAxios('/getLangSelect', '', 'post')
            // view_data.data.shift();
            this.langArray = view_data.data
            this.Prolist = {}
            this.product_img = []
            this.ImgShow = ''
        },
        ImageAppend(e) {
            const files = e.target.files[0]
            this.filesdata = e.target.files[0]
            this.ImgShow = URL.createObjectURL(files)
        },
        async common_category() {
            const data_send = await this.callAxios('/getSubCat', '', 'post')
            this.rows = data_send.data
        },
        async subcategory() {
            const data_send = await this.callAxios('/getCatSelect', '', 'post')
            this.openTypeArray = data_send.data
        },
        async change_edit(idss) {
            this.EditID = idss
            if (this.$route.query.id != idss) {
                this.$router.replace({ query: { 'id': idss } })
            }
            const data_send1 = await this.callAxios('/getSingleSubCat', { 'data': idss }, 'post')
            let obj = []
            for (const [key, value] of Object.entries(data_send1.data.lang_based)) {
                obj[value.lid] = value.title
            }
            // console.log(obj)
            this.Prolist = obj
            this.category = data_send1.data.lang_based[0].cat_id
        },
        callDeleteMethod(url, data, requestMethod) {
            console.log('999' + this.sweetAlertDeleteMethod(url, data, requestMethod))
        }
    },
}
</script>
<style lang="scss">
[dir] .card .card-header {
    border-bottom: none;
    padding: 0.9rem;
    background-color: transparent;
}

@import '~@resources/scss/vue/libs/vue-good-table.scss';
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>

