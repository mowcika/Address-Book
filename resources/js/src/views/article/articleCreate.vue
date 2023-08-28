<template>
    <b-card-code title="Article Create">
        <div v-show="!initial_loading" class="text-center m-5">
            <b-spinner
                v-for="variant in variants"
                :key="variant"
                :variant="variant"
                class="m-2"
                style="width: 3rem; height: 3rem;"
                type="grow"
            />
        </div>
        <!--        <validation-observer v-show="!initial_loading" ref="simpleRules">-->
        <validation-observer ref="simpleRules">
            <b-form>
                <b-row>
                    <b-col cols="6">
                        <b-form-group>
                            <label class="required">Article Title</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Article Name"
                                rules="required|min:3"
                            >
                                <b-form-input
                                    id="f-article-title"
                                    v-model="data.articleTitle"
                                    placeholder="Article Title"
                                    type="text"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>

                        <b-form-group>
                            <label class="required">Article Message</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Article Message"
                                rules="required|max:1000"
                            >
                                <b-form-textarea
                                    v-model="data.articleMessage"
                                    :class="data.articleMessage ? data.articleMessage.length >= maxChar1000 ? 'text-danger' : '' : ''"
                                    class="char-textarea"
                                    placeholder="Article Message"
                                    rows="5"
                                />
                                <small
                                    :class="data.articleMessage ? data.articleMessage.length >= maxChar1000 ? 'bg-danger' : '' : ''"
                                    class="textarea-counter-value float-right"
                                >
                                        <span class="char-count">{{
                                                data.articleMessage ? data.articleMessage.length : 0
                                            }}</span> /
                                    {{ maxChar1000 }}
                                </small>
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <b-col cols="6">
                        <b-form-group>
                            <label class="required">Upload Image</label>
                            <validation-provider
                                #default="{ errors }"
                                name="Upload Image"
                            >
                                <b-form-file
                                    id="imageUploads"
                                    v-model="data.fileUpload"
                                    accept="image/*"
                                    drop-placeholder="Drop file here..."
                                    placeholder="Choose a file"
                                    @change="onFileChange"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>

                            <div v-if="data.editId">
                                <!--                            <b-form-input-->
                                <!--                                v-model="data.EditfileUpload"-->
                                <!--                                id="f-edit-fileurl"-->
                                <!--                                type="text"-->
                                <!--                                placeholder="Edit File URL"-->
                                <!--                            />-->
                                <div v-if="!data.EditfileUpload">
                                    <img src="https://nithra.mobi/assets/images/nithra_logo_1.svg">
                                    <!--                                <img :src=require('https://nithra.mobi/assets/images/nithra_logo_1.svg') >-->
                                </div>
                                <div v-if="data.EditfileUpload">
                                    <img :src=data.EditfileUpload class="img-responsive" height="50" width="50">
                                </div>
                            </div>

                        </b-form-group>
                    </b-col>

                    <!-- submit and reset -->
                    <b-col offset-md="4">
                        <b-button
                            v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                            class="mr-1"
                            type="submit"
                            variant="primary"
                            @click.prevent="validationForm"
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
        </validation-observer>
        <hr class="mt-2 mb-2">
        <b-row>
            <b-col cols="4">
                <total-results :countText="getTableData.length"></total-results>
                <!--                <h4>Total Rows : {{totalRows}}</h4>-->
            </b-col>
            <b-col cols="4">
                <b-form-group>
                    <v-select
                        v-model="data.empORadmin"
                        :options="select_empORadmin"
                        :reduce="item=>item.id"
                        label="name"
                        placeholder="Select Employer/Admin"
                        @input="getDetails"
                    />
                </b-form-group>
            </b-col>
            <b-col cols="4">
                <!--                <b-form-group>-->
                <!--                    <b-form-input-->
                <!--                        v-model="agCommonSearchBox"-->
                <!--                        id="ag-common-search-box"-->
                <!--                        type="text"-->
                <!--                        placeholder="Search Here"-->
                <!--                        @input="agCommonSearchBoxMethod"-->
                <!--                        label-cols-md="8"-->
                <!--                    />-->
                <!--                </b-form-group>-->
            </b-col>
        </b-row>
        <ag-grid-vue
            ref="myTable"
            :columnDefs="columnDefs.value"
            :defaultColDef="defaultColDef"
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
            id="modal-login"
            cancel-variant="outline-secondary"
            centered
            no-close-on-backdrop
            size="lg"
            title="VERIFY UPDATED"
        >

            <validation-observer ref="verifyUpdateRules">
                <b-form>
                    <b-row>
                        <b-col md="12">
                            <h1 class="text-center text-success p-1">CONTENT</h1>
                            <p style="padding:10px; border:1px solid #333;text-align: justify;line-height:25px;box-shadow: -2px 2px 10px 0px #ccc; height: auto;">
                                {{ data.Vcontent }} </p>
                            <br>
                            <br>
                            <b-form-group>
                                <label class="required">Verify Status : </label>
                                <br>
                                <b-form-radio-group v-model="data.isVerify">
                                    <b-form-radio value="1"><span class="text success text-bold">Verify</span>
                                    </b-form-radio>
                                    <b-form-radio value="2"><span class="text text-danger text-bold">Reject</span>
                                    </b-form-radio>
                                </b-form-radio-group>
                            </b-form-group>
                        </b-col>
                    </b-row>
                    <br>
                    <br>
                    <b-row>
                        <b-col offset-md="4">
                            <b-button
                                v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                                class="mr-1"
                                type="submit"
                                variant="primary"
                                @click.prevent="VerifyvalidationForm"
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
            </validation-observer>
        </b-modal>
    </b-card-code>

</template>

<script>
import BCardCode from '@core/components/b-card-code'
import TotalResults from '@/views/elements/TotalResults'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import {
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
    BTable,
    BCardBody,
    BFormRadio,
    BFormRadioGroup,
} from 'bootstrap-vue'
import {
    required, between, alpha, integer, password, min, digits, alphaDash, length, max,
} from '@validations'
import vSelect from 'vue-select'
import Ripple from 'vue-ripple-directive'

import { AgGridVue } from 'ag-grid-vue'
import 'ag-grid-community/styles/ag-grid.css' // Core grid CSS, always needed
import 'ag-grid-community/styles/ag-theme-alpine.css' // Optional theme CSS

export default {
    components: {
        BSpinner,
        BCardCode,
        BCardBody,
        ValidationProvider,
        ValidationObserver,
        BFormInput,
        BFormRadio,
        BFormRadioGroup,
        BFormSelect,
        BFormGroup,
        BForm,
        BFormFile,
        BRow,
        BCol,
        BButton,
        BFormTextarea,
        AgGridVue,
        TotalResults,
        vSelect,
        CheckDupeComponent: {
            template: '<button class="btn btn-warning" @click="btnCellRenderer(params.data.id)" style="position: relative;">Edit</button>',
            methods: {
                async btnCellRenderer(getId) {
                    var rowNode = this.params.node.rowIndex
                    // alert("tableindex : "+rowNode);
                    this.$root.$emit('btnCellRenderer1Event()', rowNode, '/getArticleEditdata', {
                        id: this.params.data.id
                    }, this.params.data)
                },
            }
        },

        VerifyComponent: {
            template: '<button class="btn btn-outline-primary btn-sm" @click="btnCellRenderer(params.data.id)" style="position: relative;">Verify</button>',
            methods: {
                async btnCellRenderer(getId) {
                    var rowNode = this.params.node.rowIndex
                    // alert("tableindex : "+rowNode);
                    var localUserId = JSON.parse(localStorage.getItem('userData')).id
                    var isVerify = 1
                    this.$root.$emit('getVerifyDataEvent()', rowNode, '/getarticleVerifyData', {
                        id: this.params.data.id,
                        // localUserid: localUserId,
                        // isVerify : isVerify
                    }, this.params.data)
                },
            }
        },
        isVerifyComponent: {
            template: '<button class="btn btn-outline-success btn-sm" @click="btnCellRenderer(params.data.id)" style="position: relative;">Verified</button>',
            methods: {
                async btnCellRenderer(getId) {
                    var rowNode = this.params.node.rowIndex
                    // alert("tableindex : "+rowNode);
                    var localUserId = JSON.parse(localStorage.getItem('userData')).id
                    var isVerify = 1
                    this.$root.$emit('getVerifyDataEvent()', rowNode, '/getarticleVerifyData', {
                        id: this.params.data.id,
                        // localUserid: localUserId,
                        // isVerify : isVerify
                    }, this.params.data)
                },
            }
        },
        rejectVerifyComponent: {
            template: '<button class="btn btn-outline-danger btn-sm" @click="btnCellRenderer(params.data.id)" style="position: relative;">Reject</button>',
            methods: {
                async btnCellRenderer(getId) {
                    var rowNode = this.params.node.rowIndex
                    // alert("tableindex : "+rowNode);
                    var localUserId = JSON.parse(localStorage.getItem('userData')).id
                    var isVerify = 1
                    this.$root.$emit('getVerifyDataEvent()', rowNode, '/getarticleVerifyData', {
                        id: this.params.data.id,
                        // localUserid: localUserId,
                        // isVerify : isVerify
                    }, this.params.data)
                },
            }
        },
        ImageComponent: {
            // template: '<button class="btn btn-warning" @click="btnCellRenderer(params.data.id)" style="position: relative;">Edit</button>',
            template: '<img v-bind:src="params.data.imageUrl" />',

        },
        // vSelect,
        // flatPickr,
        // BFormCheckbox,
        // BTable
    },
    setup() {

        const gridApi = {} // Optional - for accessing Grid's API
        const onGridReady = (params) => {
            params.api.showLoadingOverlay()
            gridApi.value = params.api
        }
        const rowData = {} // Set rowData to Array of Objects, one Object per Row
        // Each Column Definition results in one Column.

        const columnDefs = {
            value: [
                {
                    headerName: 'S.No',
                    valueGetter: 'node.rowIndex + 1',
                    width: 90,
                },
                {
                    headerName: 'Action',
                    cellRendererSelector: params => {
                        return {
                            component: 'CheckDupeComponent'
                        }
                        // if ((!params.data.existStatus || params.data.existStatus == 0)) {
                        //     return {
                        //         component: 'CheckDupeComponent'
                        //     }
                        // } else {
                        //     return {
                        //         component: 'FollowHistoryComponent'
                        //     }
                        // }
                    },
                    cellStyle: { 'white-space': 'normal' },
                    autoHeight: true,
                },
                {
                    headerName: 'Verify',
                    cellRendererSelector: params => {
                        if ((params.data.isVerify == 0)) { // verified
                            return {
                                component: 'VerifyComponent'
                            }
                        } else if ((params.data.isVerify == 1)) { // verified
                            return {
                                component: 'isVerifyComponent'
                            }
                        } else { // rejected
                            return {
                                component: 'rejectVerifyComponent'
                            }
                        }
                    },
                    cellStyle: { 'white-space': 'normal' },
                    autoHeight: true,
                },
                {
                    headerName: 'Images',
                    cellRendererSelector: params => {
                        return {
                            component: 'ImageComponent'
                        }

                    },
                    cellStyle: { 'white-space': 'normal' },
                    autoHeight: true,
                },
                // {
                //     headerName: '#ID',
                //     field: 'id',
                //     width: 90,
                // },
                {
                    headerName: 'Title',
                    field: 'title',
                    width: 190,
                },
                {
                    headerName: 'Content',
                    field: 'content',
                    width: 240,
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

        }
        return {
            onGridReady,
            columnDefs,
            rowData,
            defaultColDef,
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
                articleTitle: '',
                articleMessage: '',
                fileUpload: null,
                editId: '',
                EditfileUpload: '',
                totalRows: '',
                verifyStatus: '',
                isVerify: '',
                userid: '',
                Vcontent: '',
                empORadmin: '',
            },
            select_empORadmin: [
                {
                    id: '1',
                    name: 'Employer'
                },
                {
                    id: '2',
                    name: 'Admin'
                },
            ],
            getTableData: [],
            getTableDataSearch: [],
            editData: [],
            verifyData: [],
            initial_loading: true,
            variants: ['light', 'primary', 'secondary', 'danger', 'warning', 'success', 'info', 'dark'],
            maxChar500: 500,
            maxChar1000: 1000,
            required,
            min,
            max,
            agCommonSearchBox: '',
        }
    },
    created() {
        this.getDetails()
    },
    async mounted() {
        this.$root.$on('btnCellRenderer1Event()', (index, url, data, dataJson) => {
            this.btnCellRenderer1(index, url, data, dataJson)
        })

        this.$root.$on('getVerifyDataEvent()', (index, url, data, dataJson) => {
            this.getarticleVerifyRenderer1(index, url, data, dataJson)
        })

        // this.props.
    },
    methods: {
        getDetails() {
            this.total = 'Loading...'
            axios.post('/getArticledata', this.data)
                .then(res => {
                    console.log(res.data.articleData)
                    this.totalRows = res.data.articleData.length
                    this.getTableData = this.getTableDataSearch = res.data.articleData
                })
        },
        onFileChange(event) {
            if (['image/png', 'image/jpeg', 'image/jpg', 'image/webp', 'image/svg'].includes(event.target.files[0]['type'])) {
                this.data.fileUpload = event.target.files[0]
            } else {
                this.sweetAlertmethod('warning', 'warning', 'InValid Image Format')
            }

        },
        validationForm() {
            this.$refs.simpleRules.validate()
                .then(async success => {
                    if (success) {
                        var localUserId = JSON.parse(localStorage.getItem('userData')).id

                        var form = new FormData()
                        form.append('articleTitle', this.data.articleTitle)
                        form.append('articleMessage', this.data.articleMessage)
                        form.append('fileUpload', this.data.fileUpload)
                        form.append('editId', this.data.editId)
                        form.append('EditfileUpload', this.data.EditfileUpload)
                        form.append('uid', localUserId)
                        const config = {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        }
                        // eslint-disable-next-line
                        const callAxios = await this.callAxios('/saveArticle', form, 'post')
                        console.log('bala : ' + callAxios)
                        // alert('form submitted!');
                        if (callAxios.status === 200) {
                            if (callAxios.data.adStatus == true) {
                                this.getTableData = ''
                                this.getDetails()
                                this.sweetAlertmethod('success', 'Success', callAxios.data.adMessage)
                                this.data = {
                                    articleTitle: '',
                                    articleMessage: '',
                                    fileUpload: null,
                                    editId: '',
                                    EditfileUpload: '',
                                }

                            } else {
                                this.sweetAlertmethod('error', 'error', callAxios.data.adMessage, 'btn-primary')
                            }
                        } else {
                            this.sweetAlertmethod('error', 'Failed', '', 'btn-danger')
                        }
                    } else {
                        this.sweetAlertmethod('error', 'Failed', '', 'btn-danger')
                    }
                })
        },

        VerifyvalidationForm() {
            this.$refs.verifyUpdateRules.validate()
                .then(async success => {
                    if (success) {
                        this.data.userid = JSON.parse(localStorage.getItem('userData')).id
                        const callAxios_update = await this.callAxios('/articleVerify', this.data, 'post')
                        console.log('bala : ' + callAxios_update)
                        // console.log("ys "+this.data.editId);
                        // console.log("ys "+this.data.isVerify);
                        if (callAxios_update.data.adStatus == true) {
                            this.getTableData = ''
                            this.getDetails()
                            this.sweetAlertmethod('success', 'Success', callAxios_update.data.adMessage)
                            this.data = {
                                editId: '',
                                isVerify: '',
                            }
                            this.$bvModal.hide('modal-login')
                        } else {
                            this.sweetAlertmethod('error', 'error', callAxios_update.data.adMessage, 'btn-primary')
                        }
                    }
                })
        },

        async btnCellRenderer1(index, url, data, dataJson) {
            const callAxios = await this.callAxios(url, data, 'post')
            // console.log("bala : "+callAxios.editData)
            if (callAxios.status === 200) {
                if (url == '/getArticleEditdata') {
                    // alert("iif");
                    this.editData = callAxios.data.editData
                    this.data.editId = this.editData[0].id
                    this.data.articleTitle = this.editData[0].title
                    this.data.articleMessage = this.editData[0].content
                    // this.data.fileUpload = this.editData[0].imageUrl
                    this.data.EditfileUpload = this.editData[0].imageUrl
                    // console.log(this.editData[0].title )
                }
            }
        },

        async getarticleVerifyRenderer1(index, url, data, dataJson) {
            const callAxios = await this.callAxios(url, data, 'post')
            // console.log("bala : "+callAxios.editData)
            if (callAxios.status === 200) {
                if (url == '/getarticleVerifyData') {
                    // alert("iif");
                    this.$bvModal.show('modal-login')
                    this.verifyData = callAxios.data.verifyData
                    this.data.editId = this.verifyData[0].id
                    this.data.isVerify = this.verifyData[0].isVerify
                    this.data.Vcontent = this.verifyData[0].content
                    // alert("bala "+this.data.isVerify);
                    console.log(this.verifyData[0].isVerify)
                }
            }
        },

        async articleVerifyRenderer1(index, url, data, dataJson) {
            const callAxios = await this.callAxios(url, data, 'post')
            console.log('bala : ' + callAxios.verifyData)
            if (callAxios.status === 200) {
                if (url == '/articleVerify') {
                    // alert("ok");
                    this.$bvModal.show('modal-login')
                }
            }
            // if (callAxios.status === 200) {
            //     if (url == '/articleVerify') {
            //         // alert("iif");
            //         this.editData = callAxios.data.editData
            //         this.data.editId = this.editData[0].id
            //         this.data.articleTitle = this.editData[0].title
            //         this.data.articleMessage = this.editData[0].content
            //         // this.data.fileUpload = this.editData[0].imageUrl
            //         this.data.EditfileUpload = this.editData[0].imageUrl
            //         // console.log(this.editData[0].title )
            //     }
            // }
        },

        agCommonSearchBoxMethod() {
            // alert("ok");
            // console.log(this.defaultColDef.setQuickFilter(this.agCommonSearchBox));
            if (this.agCommonSearchBox.trim()) {
                let getSearchText = this.agCommonSearchBox.toLowerCase()
                this.getTableData = this.getTableDataSearch.filter((item) => {
                    return getSearchText.toLowerCase()
                        .split(' ')
                        .every(v => ((item.title === null) ? '' : item.title).toString()
                            .toLowerCase()
                            .includes(v))

                })
            } else {
                this.getTableData = this.getTableDataSearch
            }
        },
    },
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
@import '~@resources/scss/vue/libs/vue-good-table.scss';
</style>

<style>
::selection {
    color: #fff;
    background: #28c76f;
}
</style>
