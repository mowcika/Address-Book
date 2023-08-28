<template>
    <div class="auth-wrapper auth-v2">
        <b-row class="auth-inner m-0">

            <!-- Brand logo-->
            <b-link class="brand-logo">
                <vuexy-logo/>
                <h2 class="brand-text text-primary ml-1">
                    Calendar Pro
                </h2>
            </b-link>
            <!-- /Brand logo-->

            <!-- Left Text-->
            <b-col
                lg="8"
                class="d-none d-lg-flex align-items-center p-5"
            >
                <div class="w-100 d-lg-flex align-items-center justify-content-center px-5">
                    <b-img
                        fluid
                        :src="imgUrl"
                        alt="Login V2"
                    />
                </div>
            </b-col>
            <!-- /Left Text-->

            <!-- Login-->
            <b-col
                lg="4"
                class="d-flex align-items-center auth-bg px-2 p-lg-5"
            >
                <b-col
                    sm="8"
                    md="6"
                    lg="12"
                    class="px-xl-2 mx-auto"
                >
                    <div id="credential_part" v-show="!toggle">
                        <!-- form -->
                        <b-card-title
                            class="mb-1 font-weight-bold"
                            title-tag="h2"
                        >
                            Welcome to Calendar Pro! 👋
                        </b-card-title>
                        <b-card-text class="mb-2">
                            Please sign-in to your account and start the adventure
                        </b-card-text>

                        <validation-observer
                            ref="loginForm"
                            #default="{invalid}"
                        >
                            <b-form
                                class="auth-login-form mt-2"
                                @submit.prevent="login"
                            >
                                <!-- User name -->
                                <b-form-group
                                    label="User name"
                                    label-for="user-name"
                                >
                                    <validation-provider
                                        #default="{ errors }"
                                        name="Username"
                                        rules="required"
                                    >
                                        <b-form-input
                                            autocomplete="new-uname"
                                            id="user-name"
                                            v-model="data.uname"
                                            :state="errors.length > 0 ? false:null"
                                            name="user-name"
                                            placeholder="User Name"
                                        />
                                        <small class="text-danger">{{ errors[0] }}</small>
                                    </validation-provider>
                                </b-form-group>

                                <!-- forgot password -->
                                <b-form-group>
                                    <div class="d-flex justify-content-between">
                                        <label for="login-password">Password</label>
                                    </div>
                                    <validation-provider
                                        #default="{ errors }"
                                        name="Password"
                                        vid="password"
                                        rules="required"
                                    >
                                        <b-input-group
                                            class="input-group-merge"
                                            :class="errors.length > 0 ? 'is-invalid':null"
                                        >
                                            <b-form-input
                                                autocomplete="new-password"
                                                id="login-password"
                                                v-model="data.pass"
                                                :state="errors.length > 0 ? false:null"
                                                class="form-control-merge"
                                                :type="passwordFieldType"
                                                name="login-password"
                                                placeholder="Password"
                                            />
                                            <b-input-group-append is-text>
                                                <feather-icon
                                                    class="cursor-pointer"
                                                    :icon="passwordToggleIcon"
                                                    @click="togglePasswordVisibility"
                                                />
                                            </b-input-group-append>
                                        </b-input-group>
                                        <small class="text-danger">{{ errors[0] }}</small>
                                    </validation-provider>
                                </b-form-group>

                                <!-- submit buttons -->
                                <b-button
                                    type="submit"
                                    variant="primary"
                                    block
                                    :disabled="invalid"
                                >
                                    Sign in
                                </b-button>
                            </b-form>
                        </validation-observer>

                    </div>
                    <div id="authentication_part" v-show="toggle">
                        <b-card-title v-if="data.name"
                                      class="mb-1 font-weight-bold text-center"
                                      title-tag="h2"
                        >
                            Hi! {{ data.name }} 👋
                        </b-card-title>
                        <div class="story mt-5 text-center">
                            <h1 class="text-primary">
                                Login Code
                            </h1>
                            <label><b>Verification</b></label>
                            <SimpleOtpInput
                                v-model="data.mfacode"
                                class="otp-with-effect"
                                inputClasses="input-with-effect"
                                :pasteDelayMs="192"
                                @complete="handleComplete"
                            >
                                <template v-slot:extra>
          <span class="focus-border">
            <i></i>
          </span>
                                </template>
                            </SimpleOtpInput>
                            <!--                            <p><b>{{ data.code }}</b></p>-->
                        </div>
                        <div class="mt-5 text-center">
                            <b-link href="https://nithrajobs.com/jobs_authenticator.apk">
                                <img src="https://nithrajobs.com/nithra authenticator app.svg">
                                <p><b>Download</b></p>
                            </b-link>
                        </div>
                    </div>


                </b-col>
            </b-col>
            <!-- /Login-->
        </b-row>
    </div>
</template>

<script>
/* eslint-disable global-require */
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import VuexyLogo from '@core/layouts/components/Logo.vue'
import {
    BRow,
    BCol,
    BLink,
    BFormGroup,
    BFormInput,
    BInputGroupAppend,
    BInputGroup,
    BFormCheckbox,
    BCardText,
    BCardTitle,
    BImg,
    BForm,
    BButton,
} from 'bootstrap-vue'
import useJwt from '@/auth/jwt/useJwt'
import { required, alpha } from '@validations'
import { togglePasswordVisibility } from '@core/mixins/ui/forms'
import store from '@/store/index'
import { getHomeRouteForLoggedInUser } from '@/auth/utils'

import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import SimpleOtpInput from 'vue-simple-otp-input'

export default {
    components: {
        BRow,
        BCol,
        BLink,
        BFormGroup,
        BFormInput,
        BInputGroupAppend,
        BInputGroup,
        BFormCheckbox,
        BCardText,
        BCardTitle,
        BImg,
        BForm,
        BButton,
        VuexyLogo,
        ValidationProvider,
        ValidationObserver,
        SimpleOtpInput,
    },
    mixins: [togglePasswordVisibility],
    data() {
        return {
            data: {
                uname: '',
                pass: '',
            },
            sideImg: require('@/assets/images/pages/login-v2.svg'),
            toggle: false,

            // validation rules
            required,
            alpha,
        }
    },
    computed: {
        passwordToggleIcon() {
            return this.passwordFieldType === 'password' ? 'EyeIcon' : 'EyeOffIcon'
        },
        imgUrl() {
            if (store.state.appConfig.layout.skin === 'dark') {
                // eslint-disable-next-line vue/no-side-effects-in-computed-properties
                this.sideImg = require('@/assets/images/pages/login-v2-dark.svg')
                return this.sideImg
            }
            return this.sideImg
        },
    },
    methods: {
        login() {
            this.$refs.loginForm.validate()
                .then(async success => {
                    const callAxios = await this.callAxios('/loginFirst', this.data, 'post')
                    console.log(callAxios)
                    var return_data = callAxios.data[0]
                    if (callAxios.status === 200) {
                        if (typeof (return_data) === 'undefined') {
                            this.sweetAlertmethod('error', 'Error', 'Invalid User Name / Password', 'btn-primary')
                        } else {
                            this.data = return_data
                            this.toggle = true
                        }
                    } else {
                        // console.log(callAxios.response.data.message);
                        // this.toast("Error", callAxios.response.data.message, 'danger');
                        this.sweetAlertmethod('error', 'Error', callAxios.response.data.message, 'btn-primary')
                    }
                    return
                })
        },
        async handleComplete() {
            const callAxios = await this.callAxios('/loginSecond', this.data, 'post')
            console.log(callAxios)
            var return_data = callAxios.data[0]
            if (callAxios.status === 200) {
                if (typeof (return_data) === 'undefined') {
                    this.sweetAlertmethod('error', 'Error', 'Invalid Login Code', 'btn-primary')
                } else {
                    useJwt
                    useJwt
                        .handleComplete({
                            return_data,
                        })
                        .then(response => {
                            const { userData } = response.data
                            useJwt.setToken(response.data.accessToken)
                            useJwt.setRefreshToken(response.data.refreshToken)
                            localStorage.setItem('userData', JSON.stringify(userData))
                            this.$ability.update(userData.ability)

                            // ? This is just for demo purpose as well.
                            // ? Because we are showing eCommerce app's cart items count in navbar
                            this.$store.commit('app-ecommerce/UPDATE_CART_ITEMS_COUNT', userData.extras.eCommerceCartItemsCount)

                            // ? This is just for demo purpose. Don't think CASL is role based in this case, we used role in if condition just for ease
                            this.$router.replace(getHomeRouteForLoggedInUser(userData.role))
                                .then(() => {
                                    this.$toast({
                                        component: ToastificationContent,
                                        position: 'top-right',
                                        props: {
                                            title: `Welcome ${userData.name || userData.uname}`,
                                            icon: 'CoffeeIcon',
                                            variant: 'success',
                                            text: `You have successfully logged in as ${userData.role}. Now you can start to explore!`,
                                        },
                                    })
                                })
                        })
                        .catch(error => {
                            this.sweetAlertmethod('error', 'Error', error, 'btn-primary')
                        })
                    // this.data = return_data;
                    // this.toggle = true;
                }
            } else {
                // console.log(callAxios.response.data.message);
                // this.toast("Error", callAxios.response.data.message, 'danger');
                this.sweetAlertmethod('error', 'Error', callAxios.response.data.message, 'btn-primary')
            }
        }
    },
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/pages/page-auth.scss';
</style>
<style>
.simple-otp-input {
    display: flex;
    align-items: center;
    justify-content: center;
}

.otp-single-input {
    /*font-size: 20px;*/
    font-weight: bolder;
    padding: 3px;
    width: 4em;
    height: 4em;
    text-align: center;
}

.single-input-container {
    position: relative;
    margin: 2px;
}
</style>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
@import '~@resources/scss/vue/libs/vue-good-table.scss';
</style>
