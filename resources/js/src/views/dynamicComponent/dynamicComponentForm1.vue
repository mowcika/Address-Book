<template>
  <b-card-code title="Repeating Forms">
    <div>
      <b-row>
        <b-col md="8">
          <validation-observer ref="simpleRules">
            <b-form
              ref="form"
              style="height: 100%"
              class="repeater-form"
            >
              <b-row>
                <b-col md="4">
                  <b-form-group>
                    <label class="required">Select Language</label>
                    <validation-provider
                      #default="{ errors }"
                      name="Select Language"
                      rules="required"
                    >
                      <v-select
                        v-model="data.lang"
                        :options="langArray"
                        :reduce="item=>item.id"
                        label="name"
                      />

                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-form-group>
                </b-col>
                <b-col md="4">
                  <b-form-group>
                    <label class="required">Select Main Category</label>
                    <validation-provider
                      #default="{ errors }"
                      name="Select Main Category"
                      rules="required"
                    >
                      <v-select
                        v-model="data.mainCat"
                        :options="mainCatArray"
                        :reduce="item=>item.id"
                        label="name"
                        @input="mainCatchange(index)"
                      />

                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-form-group>
                </b-col>
                <b-col md="4">
                  <b-form-group>
                    <label class="required">Select Sub Category</label>
                    <validation-provider
                      #default="{ errors }"
                      name="Select Sub Category"
                      rules="required"
                    >
                      <v-select
                        v-model="data.subCat"
                        :options="subCatArray"
                        :reduce="item=>item.id"
                        label="name"
                      />

                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-form-group>
                </b-col>
              </b-row>
              <!-- Row Loop -->
              <b-row
                v-for="(item, index) in items"
                :id="item.id"
                :key="item.id"
                ref="row"
              >
                <b-col md="12">
                  <!-- Remove Button -->
                  <b-button
                    v-ripple.400="'rgba(234, 84, 85, 0.15)'"
                    variant="outline-danger"
                    style="float: right"
                    @click="removeItem(index)"
                  >
                    <feather-icon
                      icon="XIcon"
                      class="mr-25"
                    />
                    <span />
                  </b-button>

                  <b-button
                    v-if="!data.id"
                    variant="outline-success"
                    style="float:right"
                    @click="preview(index)"
                  >
                    <feather-icon
                      icon="EyeIcon"
                      class="mr-25"
                    />

                  </b-button>

                </b-col>

                <!--Select Widget Type -->
                <b-col md="4">
                  <b-form-group>
                    <label class="required">Select Widget Type</label>
                    <validation-provider
                      #default="{ errors }"
                      name="Select Widget Type"
                      rules="required"
                    >
                      <v-select
                        v-model="data.weight[index]"
                        :options="weightArray"
                        :reduce="item=>item.id"
                        label="name"
                        @input="weightchange(index,data.weight[index])"
                      />

                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-form-group>
                </b-col>

                <!--Component type -->
                <b-col md="4">
                  <b-form-group>
                    <label class="required">Component Type</label>
                    <validation-provider
                      #default="{ errors }"
                      name="Select Component Type"
                      rules="required"
                    >
                      <v-select
                        v-model="data.compType[index]"
                        :options="labelTypeArray"
                        :reduce="item=>item.id"
                        label="name"
                        @input="componentchange(index,data.compType[index])"
                      />
                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-form-group>
                </b-col>
                <!--                    Top Title Text Color Code -->
                <b-col
                  v-if="showOptionArea[index]==0"
                  md="4"
                >
                  <b-form-group>
                    <label>Top Title Text Color Code</label>
                    <validation-provider
                      #default="{ errors }"
                      name="Top Title Text Color Code"
                      rules=""
                    >
                      <b-form-input
                        v-model="data.title_text_color[index]"
                        :state="errors.length > 0 ? false:null"
                        placeholder="Top Title Text Color Code"
                        type="text"
                      />
                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-form-group>
                </b-col>

                <!--Top Title Text Alignment -->
                <b-col
                  v-if="showOptionArea[index]==0"
                  md="4"
                >
                  <b-form-group>
                    <label> Select Top Title Text
                      Alignment</label>
                    <validation-provider
                      #default="{ errors }"
                      name="Select Top Title Text Alignment"
                    >
                      <v-select
                        :key="index"
                        v-model="data.title_aligenment[index]"
                        :options="alignmentMasterArray"
                        :reduce="item=>item.id"
                        label="name"
                      />

                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-form-group>
                </b-col>

                <!--Select Top Title Text Font Size -->
                <b-col
                  v-if="showOptionArea[index]==0"
                  md="4"
                >
                  <b-form-group>
                    <label>Select Top Title Text Font Size</label>
                    <validation-provider
                      #default="{ errors }"
                      name="Select Top Title Text Font Size"
                    >
                      <v-select
                        v-model="data.title_text_size[index]"
                        :options="fontSizeMasterArray"
                        :reduce="item=>item.id"
                        label="name"
                      />

                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-form-group>
                </b-col>

                <!--                    top Title -->
                <b-col
                  v-if="showOptionArea[index]==0"
                  md="4"
                >
                  <b-form-group>
                    <label>Top Title</label>
                    <validation-provider
                      #default="{ errors }"
                      name="Top Title"
                      rules=""
                    >
                      <b-form-input
                        v-model="data.top_title[index]"
                        :state="errors.length > 0 ? false:null"
                        placeholder="Top Title"
                        type="text"
                      />
                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-form-group>
                </b-col>

                <!--                    description Color Code -->
                <b-col
                  v-if="showOptionArea[index]==0"
                  md="4"
                >
                  <b-form-group>
                    <label>Description Color Code</label>
                    <validation-provider
                      #default="{ errors }"
                      name="Description Color Code"
                      rules=""
                    >
                      <b-form-input
                        v-model="data.des_text_color[index]"
                        :state="errors.length > 0 ? false:null"
                        placeholder="Description Color Code"
                        type="text"
                      />
                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-form-group>
                </b-col>

                <!--description Text Alignment -->
                <b-col
                  v-if="showOptionArea[index]==0"
                  md="4"
                >
                  <b-form-group>
                    <label> Select Description Alignment</label>
                    <validation-provider
                      #default="{ errors }"
                      name="Select Description Alignment"
                    >
                      <v-select
                        v-model="data.des_aligenment[index]"
                        :options="alignmentMasterArray"
                        :reduce="item=>item.id"
                        label="name"
                      />

                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-form-group>
                </b-col>

                <!--Select description Text Font Size -->
                <b-col
                  v-if="showOptionArea[index]==0"
                  md="4"
                >
                  <b-form-group>
                    <label>Select description Text Font Size</label>
                    <validation-provider
                      #default="{ errors }"
                      name="Select description Text Font Size"
                    >
                      <v-select
                        v-model="data.des_text_size[index]"
                        :options="fontSizeMasterArray"
                        :reduce="item=>item.id"
                        label="name"
                      />

                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-form-group>
                </b-col>

                <!--description -->
                <b-col
                  v-if="showOptionArea[index]==0"
                  md="4"
                >
                  <b-form-group>
                    <label>Description</label>
                    <validation-provider
                      #default="{ errors }"
                      name="Top Title"
                      rules=""
                    >
                      <b-form-textarea
                        id="textarea-default"
                        v-model="data.description[index]"
                        placeholder="Description"
                        rows="3"
                      />
                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-form-group>
                </b-col>
                <!--backgroud Image -->
                <b-col md="4">
                  <b-form-group>
                    <label> {{
                      `${isCompoWidght[index] == 0 ? '' : isCompoWidght[index] == 1 ? 'Image' :
                        'Video'}`
                    }} URL</label>
                    <validation-provider
                      #default="{ errors }"
                      name="Image URL"
                      rules=""
                    >
                      <b-form-input
                        v-model="data.compontUrl[index]"
                        :state="errors.length > 0 ? false:null"
                        placeholder="Image URL"
                        type="url"
                      />
                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-form-group>
                </b-col>
                <!--Select  Open Type -->
                <b-col
                  v-if="showOptionArea[index]==0"
                  md="3"
                >
                  <b-form-group>
                    <label>Select Open Type</label>
                    <validation-provider
                      #default="{ errors }"
                      name="Select Open Type"
                      rules=""
                    >
                      <v-select
                        v-model="data.open_type[index]"
                        :options="openTypeArray"
                        :reduce="item=>item.id"
                        label="name"
                        @input="openTypechange(index)"
                      />

                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-form-group>
                </b-col>
                <!--                    share-->
                <b-col
                  v-if="showOptionArea[index]==0"
                  md="3"
                >
                  <label class="required">Share</label>
                  <validation-provider
                    #default="{ errors }"
                    name="Share"
                    rules="required"
                  >

                    <b-form-group class="text-dark">
                      <b-form-radio-group v-model="data.share[index]">
                        <b-form-radio value="1">
                          <small>YES</small>
                        </b-form-radio>
                        <b-form-radio value="2">
                          <small>NO</small>
                        </b-form-radio>
                      </b-form-radio-group>
                    </b-form-group>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-col>
                <!--                    Bookmark-->
                <b-col
                  v-if="showOptionArea[index]==0"
                  md="3"
                >
                  <label class="required">Bookmark</label>
                  <validation-provider
                    #default="{ errors }"
                    name="Required"
                    rules="required"
                  >

                    <b-form-group class="text-dark">
                      <b-form-radio-group v-model="data.required[index]">
                        <b-form-radio value="1">
                          <small>YES</small>
                        </b-form-radio>
                        <b-form-radio value="2">
                          <small>NO</small>
                        </b-form-radio>
                      </b-form-radio-group>
                    </b-form-group>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-col>
                <!--                    what's app share-->
                <b-col
                  v-if="showOptionArea[index]==0"
                  md="3"
                >
                  <label class="required">What's App Share</label>
                  <validation-provider
                    #default="{ errors }"
                    name="What's App Share"
                    rules="required"
                  >

                    <b-form-group class="text-dark">
                      <b-form-radio-group v-model="data.wshare[index]">
                        <b-form-radio value="1">
                          <small>YES</small>
                        </b-form-radio>
                        <b-form-radio value="2">
                          <small>NO</small>
                        </b-form-radio>
                      </b-form-radio-group>
                    </b-form-group>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-col>

                <b-col cols="12">
                  <hr>
                </b-col>
              </b-row>
              <div class="float-right">
                <b-col>
                  <b-button
                    v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                    variant="outline-danger"
                    @click=""
                  >Close
                  </b-button>

                  <b-button
                    v-if="!data.id"
                    type="submit"
                    variant="primary"
                    @click.prevent="validationForm"
                  >
                    Submit
                  </b-button>
                  <b-button
                    v-else
                    type="submit"
                    variant="primary"
                    @click.prevent="validationForm"
                  >
                    Update
                  </b-button>
                </b-col>
              </div>
            </b-form>
          </validation-observer>
        </b-col>
        <b-col md="4">
          <div>
            <b-col
              md="12"
              lg="12"
            >
              <center>
                <span><b>Preview</b></span><br><br>
              </center>
            </b-col>
            <b-col
              md="12"
              lg="12"
            >
              <b-card
                v-if="cardHide1"
                :title="title"
              >
                <b-card-text>
                  <p :style="{ color: rgbColor, 'text-align': get_align_tit }">
                    {{ gettit }}
                  </p>
                  <br>
                  <p :style="{ color: rgbColor_des,'text-align':get_align_des }">
                    {{ getcontent }}
                  </p>
                </b-card-text>

              </b-card>
            </b-col>
            <b-col
              md="12"
              lg="12"
            >
              <b-card
                v-if="cardHide2"
                :img-src="require('@/assets/images/slider/04.jpg')"
                img-alt="Card image cap"
                img-top
                :title="title"
              >
                <b-card-text>
                  <p :style="{ color: rgbColor, 'text-align': get_align_tit }">
                    {{ gettit }}
                  </p>
                  <br>
                  <p :style="{ color: rgbColor_des,'text-align':get_align_des }">
                    {{ getcontent }}
                  </p>
                </b-card-text>
              </b-card>
            </b-col>
          </div>
        </b-col>
      </b-row>

    </div>

    <b-button
      v-ripple.400="'rgba(255, 255, 255, 0.15)'"
      variant="primary"
      @click="repeateAgain"
    >
      <feather-icon
        icon="PlusIcon"
        class="mr-25"
      />
      <span>Add New</span>
    </b-button>
    <template #code>
      {{ codeBasic }}
    </template>
  </b-card-code>

</template>

<script>
import BCardCode from '@core/components/b-card-code'
import {
  BButton, BCard, BCardText,
  BCol,
  BForm,
  BFormCheckbox,
  BFormFile,
  BFormGroup,
  BFormInput,
  BFormRadio,
  BFormRadioGroup,
  BFormSelect,
  BFormTextarea,
  BRow,
  BSpinner,
} from 'bootstrap-vue'
import { heightTransition } from '@core/mixins/ui/transition'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import { max, min, required } from '@validations'
import { ValidationObserver, ValidationProvider } from 'vee-validate'
import flatPickr from 'vue-flatpickr-component'
import { codeBasic } from './code'

export default {
  components: {
    BCardText,
    BCard,

    BFormRadioGroup,
    BFormRadio,
    BSpinner,
    BCardCode,
    ValidationProvider,
    ValidationObserver,
    BFormInput,
    BFormSelect,
    BFormGroup,
    BForm,
    BFormFile,
    BRow,
    BCol,
    BButton,
    BFormTextarea,
    vSelect,
    flatPickr,
    BFormCheckbox,
  },
  directives: {
    Ripple,
  },
  mixins: [heightTransition],
  data() {
    return {
      color: '#000000',
      data: {
        id: '',
        lang: '',
        mainCat: '',
        subCat: '',
        compType: [],
        weight: [],
        title_text_color: [],
        title_aligenment: [],
        title_text_size: [],
        top_title: [],
        des_text_color: [],
        des_aligenment: [],
        des_text_size: [],
        description: [],
        compontUrl: [],
        open_type: [],
        wshare: [],
        share: [],
        required: [],
        cby: JSON.parse(localStorage.getItem('userData')).id,

      },
      title: '',
      getcontent: '',
      gettit: '',
      showcode: '',
      rgbColor: '',
      rgbColor_des: '',
      get_align_tit: '',
      get_align_des: '',
      initial_loading: true,
      showBackgroundImg: [],
      showOpenType: [],
      showOpenTypeAction: [],
      showOptionArray: [],
      showOptionArea: [],
      multiLineRequired: [],
      editTextRequired: [],
      isBtnWidght: [],
      isCompoWidght: [],
      variants: ['light', 'primary', 'secondary', 'danger', 'warning', 'success', 'info', 'dark'],
      selectButtonAction: [{
        id: 'submit',
        name: 'Submit',
      }, {
        id: 'cancel',
        name: 'Cancel',
      }, {
        id: 'reset',
        name: 'Reset',
      }],
      weightArray: [],
      backGroudTypeArray: [],
      labelTypeArray: [],
      styleMasterArray: [],
      alignmentMasterArray: [],
      fontSizeMasterArray: [],
      openTypeArray: [],
      inputTypeArray: [],
      langArray: [],
      mainCatArray: [],
      subCatArray: [],
      tempTableNameArray: [],
      tableNameArray: [],
      cardHide1: 0,
      cardHide2: 0,
      maxChar500: 500,
      required,
      weightchangeVal: '',
      min,
      max,
      userData: JSON.parse(localStorage.getItem('userData')),
      items: [{
        id: 1,
        selected: 'male',
        selected1: 'designer',
        prevHeight: 0,
      }],
      nextTodoId: 2,
      codeBasic,
    }
  },
  mounted() {
    this.initTrHeight()
  },

  async created() {
    window.addEventListener('resize', this.initTrHeight)
    const callAxios = await this.callAxios('/getDynamicMaster', {}, 'post')
    // console.log(callAxios.data);
    this.weightArray = callAxios.data.widget
    this.backGroudTypeArray = callAxios.data.backgroud_type
    this.styleMasterArray = callAxios.data.style_master
    this.alignmentMasterArray = callAxios.data.alignment_master
    this.fontSizeMasterArray = callAxios.data.font_size_master
    this.tempTableNameArray = callAxios.data.all_table
    this.inputTypeArray = callAxios.data.input_type
    this.openTypeArray = callAxios.data.open_type
    this.labelTypeArray = callAxios.data.label_type
    this.langArray = callAxios.data.lang
    this.mainCatArray = callAxios.data.mainCat

    if (this.tempTableNameArray.length > 0) {
      this.tempTableNameArray.forEach((value, index) => {
        // console.log(value.Tables_in_gcmpro);
        // console.log(index);
        this.tableNameArray.push({
          id: value.Tables_in_gcmpro,
          name: value.Tables_in_gcmpro,
        })
      })
    }
    console.log(this.tableNameArray)
    if (this.$route.query.id) {
      this.data.id = this.$route.query.id
      const callAxiosEdit = await this.callAxios('/getSingleNotification', this.data, 'post')
      // console.log(callAxiosEdit.data[0])
      // console.log(callAxiosEdit.data.status)
      if (callAxiosEdit.status == 200) {
        // alert(callAxiosEdit.data[0].id)
        this.data.id = callAxiosEdit.data[0].id
        this.data.appName = callAxiosEdit.data[0].app_name
        this.data.notiType = callAxiosEdit.data[0].notiType
        this.data.msgType = callAxiosEdit.data[0].msgType
        this.data.title = callAxiosEdit.data[0].title
        this.data.img = callAxiosEdit.data[0].img
        this.data.msg = callAxiosEdit.data[0].msg
        this.data.pname = callAxiosEdit.data[0].package_name
        this.data.className = callAxiosEdit.data[0].className
        this.data.sendType = callAxiosEdit.data[0].sendType
        this.data.sendTypeValue = callAxiosEdit.data[0].sendTypeValue
        this.data.typeCondition = callAxiosEdit.data[0].typeCondition
        this.data.vcode = callAxiosEdit.data[0].vcode
        this.data.weburl = callAxiosEdit.data[0].weburl
        this.data.sendDate = callAxiosEdit.data[0].sendDate
        // this.notiTypeChange()
        // this.clickRadio()
      }
    }
    this.initial_loading = false
    // console.log( JSON.parse(localStorage.getItem('userData')).id)

    window.addEventListener('resize', this.initTrHeight)
  },
  destroyed() {
    window.removeEventListener('resize', this.initTrHeight)
  },
  methods: {

    inputTypeChange(index) {
      console.log(this.data.input_type[index])
      if (this.data.input_type[index] == 6) {
        this.$set(this.multiLineRequired, index, 1)
        // this.multiLineRequired[]==1
        console.log(this.multiLineRequired)
      } else {
        this.$set(this.multiLineRequired, index, 0)
        // this.multiLineRequired[index].push('0');
        console.log(this.multiLineRequired)
      }
      console.log(this.multiLineRequired[index])
    },
    openTypechange(index) {
      if (this.data.open_type[index]) {
        this.$set(this.showOpenTypeAction, index, 1)
        // this.showOpenTypeAction[index]=1
      } else {
        this.$set(this.showOpenTypeAction, index, 0)
        // this.showOpenTypeAction[index]=0
      }
    },
    weightchange(index, getid) {
      // alert(this.data.weight[index])
      this.weightchangeVal = getid
      if (this.data.weight[index] == 4) {
        this.$set(this.showOptionArea, index, 1)
      } else {
        this.$set(this.showOptionArea, index, 0)
      }
    },
    preview(index) {
      const getval_template = this.data.weight[index]
      const getval_compentent = this.data.compType[index]
      const getval_tit_alignment = this.data.title_aligenment[index]
      const getval_tit_size = this.data.title_text_size[index]
      const getval_top_title = this.data.top_title[index]
      const title_text_color = this.data.title_text_color[index]
      const getval_des_text_color = this.data.des_text_color[index]
      const getval_des_aligenment = this.data.des_aligenment[index]
      const getval_des_text_size = this.data.des_text_size[index]
      const getval_description = this.data.description[index]
      const getval_videourl = this.data.compontUrl[index]
      const getval_open_type = this.data.open_type[index]
      this.cardHide1 = 0
      this.cardHide2 = 0
      this.gettit = getval_top_title
      this.getcontent = getval_description
      this.rgbColor = title_text_color
      this.rgbColor_des = getval_des_text_color
      const alignmentName = this.alignmentMasterArray.filter(item => item.id == getval_tit_alignment)
       this.get_align_tit = alignmentName[0].name
      // const alignmentName_des = this.alignmentMasterArray.filter(item => item.id == getval_des_aligenment)
      // this.get_align_des = alignmentName_des[0].name

      if ((getval_template == '1') && (getval_compentent == 'type1')) {
        this.showcode = this.data.title_text_color
        this.cardHide1 = 1
      } else {
        this.cardHide2 = 1
      }
    },
    componentchange(index, getcompenent) {
      this.componentchangeval = getcompenent
      // alert(this.data.compType[index])
      if (this.data.compType[index] == 'type1') {
        this.$set(this.isCompoWidght, index, 0)
        // this.showOptionArea[index]=1
      } else if (this.data.compType[index] == 'type2') {
        this.$set(this.isCompoWidght, index, 1)
      } else if (this.data.compType[index] == 'type3') {
        this.$set(this.isCompoWidght, index, 3)
      } else {
        this.$set(this.isCompoWidght, index, 2)
        // this.showOptionArea[index]=0
      }

      // alert(this.showOptionArea[index])
      // alert(this.editTextRequired[index])
      // alert(this.isBtnWidght[index])
    },
    clickRadio(index) {
      if (this.data.lable_Click[index] == 1) {
        this.$set(this.showOpenType, index, 1)
        // this.showOpenType[index]=1
      } else {
        this.$set(this.showOpenType, index, 0)
        // this.showOpenType[index]=0
      }
    },
    checkPandiya(id) {
      console.log('pandiya')
    },
    backGroudType(index) {
      if (this.data.lable_backgroud_type[index] == '3') {
        this.$set(this.showBackgroundImg, index, 1)
        // this.showBackgroundImg[index] = 1
      } else {
        this.$set(this.showBackgroundImg, index, 0)
        // this.showBackgroundImg[index] = 0
      }
    },

    async validationForm() {
      this.$refs.simpleRules.validate()
        .then(async success => {
          if (success) {
            this.initial_loading = true

            // eslint-disable-next-line
                        // alert('form submitted!')
            // this.data.draft_id = 0;
            // this.data.table = 'jobs';
            // this.data.inby = this.userData.id;
            console.log(this.data)
            const config = {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
            const callAxios = await this.callAxiosWithConfig('/saveDynamicComponent', this.data, 'post', config)

            console.log(callAxios)

            if (callAxios.status === 200) {
              if (this.data.id) {
                this.sweetAlertmethod('success', 'The notification was Updated successfully', '', 'btn-primary')
                // this.$router.push('/save/notification')
              } else {
                this.sweetAlertmethod('success', 'The Dynamic Dialogue was added successfully', '', 'btn-primary')
              }

              this.data.id = ''
              this.data.lang = ''
              this.data.mainCat = ''
              this.data.subCat = ''
              this.data.compType = []
              this.data.weight = []
              this.data.title_text_color = []
              this.data.title_aligenment = []
              this.data.title_text_size = []
              this.data.top_title = []
              this.data.des_text_color = []
              this.data.des_aligenment = []
              this.data.des_text_size = []
              this.data.description = []
              this.data.compontUrl = []
              this.data.open_type = []
              this.data.wshare = []
              this.data.share = []
              this.data.required = []
              this.initial_loading = false
            } else {
              this.sweetAlertmethod('error', 'Alert', callAxios.response.data.message, 'btn-primary')
            }
          }
        })
    },
    async savedraft() {
      this.data.id = 0
      this.data.table = 'draft_jobs'
      this.data.inby = this.userData.id
      console.log(this.data)
      const config = {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      }
      const callAxios = await this.callAxiosWithConfig('/savegovtjobs', this.data, 'post', config)
      if (callAxios.status === 200) {
        this.sweetAlertmethod('success', 'Success', '', 'btn-primary')
        this.$router.push('/listing/draft')
      } else {
        this.sweetAlertmethod('error', 'Alert', callAxios.response.data.message, 'btn-primary')
      }
    },
    async mainCatchange() {
      this.data.subCat = ''
      const data_send = await this.callAxios('/subCatFilter', { cat_id: this.data.mainCat }, 'post')
      this.subCatArray = data_send.data
    },
    repeateAgain() {
      this.items.push({
        id: this.nextTodoId += this.nextTodoId,
      })

      this.$nextTick(() => {
        this.trAddHeight(this.$refs.row[0].offsetHeight)
      })
    },
    removeItem(index) {
      this.items.splice(index, 1)
      this.trTrimHeight(this.$refs.row[0].offsetHeight)
    },
    initTrHeight() {
      this.trSetHeight(null)
      this.$nextTick(() => {
        this.trSetHeight(this.$refs.form.scrollHeight)
      })
    },
  },
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>
<style lang="scss" scoped>

.repeater-form {
    overflow: hidden;
    //transition: .3555s height;
}
</style>
