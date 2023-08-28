import BCardCode from '@core/components/b-card-code'
import { ValidationObserver, ValidationProvider } from 'vee-validate'
import { BButton, BCol, BForm, BFormFile, BFormGroup, BFormInput, BFormSelect, BRow } from 'bootstrap-vue'
import vSelect from 'vue-select'
import { email, length, min, required, url } from '@validations'

export default {
    components: {
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
        vSelect,
    },
    data() {
        return {
            data: {
                firstname: '',
                email: '',
                phonenumber: '',
                designation: '',
                person_proof_type: '',
                person_proof_text: '',
                person_proof: '',

            },
            number: '',
            URL: '',
            required,
            email,
            url,
            length,
            PersonProof: [],
            employer_reg_data: {
                id: '',
                companyname: '',
                tcompanyname: '',
                id_proof: '',
                companyProof: '',
                company_type: '',
                id_proof_type: '',
                address: '',
                pincode: '',
                gst: '',
                image: '',
                website: '',
                industry_type: '',
                pan_card: '',
                company_proof_type: '',
                companyNature: '',
                state: {
                    value: 1,
                    text: 'TAMIL NADU'
                },
                district: '',
                city: '',
                type: '1',
            },
            district_id: {
                disid: '',
            },

            industry_type: [],
            companyProof: [],
            addressProof: [],
            company_type: [],
            distSelect: [],
            stateSelect: [],
            citySelect: [],

        }
    },
    created() {
        this.total = 'Loading...'
        // axios.post('/person-proof')
        //     .then(res => {
        //         // console.log(res.data)
        //         // console.log(res.data.personProof)
        //         this.PersonProof = res.data.personProof;
        //         // this.total = res.data.length;
        //         // this.rows = res.data
        //     });

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
                this.distSelect = res.data.dist
                this.stateSelect = res.data.state
            })
    },
    methods: {
        validationForm() {
            this.$refs.simpleRules.validate()
                .then(success => {
                    if (success) {
                        // eslint-disable-next-line
                        alert('form submitted!')
                    }
                })
        },
        check(value) {
            if (value) {
                this.district_id.disid = value
                this.employer_reg_data.city = ''
                this.citySelect = ''
                let axiosSource = axios.CancelToken.source()
                this.request = { cancel: axiosSource.cancel }

                axios
                    .post('/getCity', {
                        cancelToken: axiosSource.token,
                        value,
                    })
                    .then((res) => {
                        this.citySelect = res.data.areas
                    })
            }

        }

    },

}
