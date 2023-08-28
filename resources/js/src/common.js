import ToastificationContent from '@core/components/toastification/ToastificationContent'
import navMenuItem from '@/navigation/vertical'
import axios from 'axios'

export default {
    data() {
        return {
            navMenuItem,
            menuData: [],
            dynamicMenu: [],
        }
    },
    methods: {
        begin_loading() {
        },
        end_loading() {

        },
        getDetails() {
        },
        sweetAlertmethod(icon, title, desc, varient) {
            this.$swal({
                title: title,
                html: desc,
                icon: icon,
                customClass: {
                    confirmButton: 'btn ' + varient,
                },
                buttonsStyling: false,
            })
        },
        sweetAlertConfirm1(url, data, requestMethod, grid = 'ag-grid', text = 'Are You Sure Verify !', call_fun = 'getDetails') {
            this.$swal({
                title: 'Are you sure?',
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Verify it!',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ml-1',
                },
                buttonsStyling: false,
            })
                .then(async result => {
                    console.log(result.value)
                    if (result.value) {
                        console.log('rid id : ' + data.id)
                        console.log('uid : ' + data.uid)
                        const callAxios = await this.callAxios(url, data, requestMethod)
                        if (callAxios.status === 200) {
                            console.log(callAxios.data)
                            this.sweetAlertmethod('success', callAxios.data.message, '', 'btn-success')
                            this.getDetails()
                            // this[call_fun]();
                        } else {
                            this.sweetAlertmethod('warning', callAxios.data.message, '', 'btn-warning')
                        }
                    }
                })
        },
        sweetAlertDeleteMethod(url, data, requestMethod, grid = 'good-table') {
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
                .then(async result => {
                    if (result.value) {
                        console.log('delete id : ' + data.id)
                        const callAxios = await this.callAxios(url, data, requestMethod)
                        if (callAxios.status === 200) {
                            this.sweetAlertmethod('success', 'Deleted!', '', 'btn-success')
                            this.total = callAxios.data.length
                            if (callAxios.data) {
                                console.log(callAxios.data.data)
                                console.log(callAxios.data)
                                this.rows = this.rowData = callAxios.data
                                this.data = {
                                    id: ''
                                }
                            } else {
                                if (grid == 'good-table') {
                                    var deleted_index = this.rows.findIndex(item => item.id === data.id)
                                    this.rows.splice(deleted_index, 1)
                                } else if (grid == 'ag-grid') {
                                    // console.log(data)
                                    var deleted_index = this.rowData.findIndex(item => item.id === data.id)
                                    this.rowData.splice(deleted_index, 1)
                                }
                                // console.log(this.rows)
                            }
                        } else {
                            this.sweetAlertmethod('warning', 'Warning!', callAxios.response.data.message, 'btn-success')
                            console.log('1')
                        }
                    } else {
                        console.log('2')
                    }
                })
        },
        sweetAlertcommon(url, data, requestMethod, btnName, rentxt) {
            this.$swal({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: btnName,
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ml-1',
                },
                buttonsStyling: false,
            })
                .then(async result => {
                    if (result.value) {
                        const callAxios = await this.callAxios(url, data, requestMethod)
                        if (callAxios.status === 200) {
                            this.sweetAlertmethod('success', rentxt, '', 'btn-success')
                            this.total = callAxios.data.length
                            this.rows = callAxios.data
                            this.data = {
                                id: ''
                            }
                        } else {
                            this.sweetAlertmethod('warning', 'Warning!', callAxios.response.data.message, 'btn-success')
                            console.log('1')
                        }
                    } else {
                        console.log('2')
                    }
                })
        },
        sweetAlertcommon1(url, data, requestMethod, btnName, rentxt) {
            this.$swal({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: btnName,
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ml-1',
                },
                buttonsStyling: false,
            })
                .then(async result => {
                    if (result.value) {
                        const callAxios = await this.callAxios(url, data, requestMethod)
                        if (callAxios.status === 200) {
                            this.sweetAlertmethod('success', rentxt, '', 'btn-success')
                            // this.getPrivateJob();
                            var deleted_index = this.rowData.findIndex(item => item.id === data.id)
                            this.rowData.splice(deleted_index, 1)

                        } else {
                            this.sweetAlertmethod('warning', 'Warning!', 'Something Went Wrong', 'btn-success')
                            console.log('1')
                        }
                    } else {
                        console.log('2')
                    }
                })
        },
        sweetAlertVerifycommon(url, data, requestMethod, btnName, rentxt) {
            this.$swal({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: btnName,
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ml-1',
                },
                buttonsStyling: false,
            })
                .then(async result => {
                    if (result.value) {
                        const callAxios = await this.callAxios(url, data, requestMethod)
                        if (callAxios.status === 200) {
                            this.sweetAlertmethod('success', rentxt, '', 'btn-success')
                            this.hdata.is_com_add_proof = callAxios.data[0].is_com_add_proof
                            this.hdata.is_person_proof = callAxios.data[0].is_person_proof
                            this.hdata.is_com_id_proof = callAxios.data[0].is_com_id_proof

                        } else {
                            this.sweetAlertmethod('warning', 'Warning!', callAxios.response.data.message, 'btn-success')
                            console.log('1')
                        }
                    } else {
                        console.log('2')
                    }
                })
        },
        sweetAlertActivateMethod(url, data, requestMethod, textShow, sucessText) {
            this.$swal({
                title: 'Are you sure?',
                text: textShow,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Confirm!',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ml-1',
                },
                buttonsStyling: false,
            })
                .then(async result => {
                    if (result.value) {
                        console.log(`delete id : ${data.id}`)
                        const callAxios = await this.callAxios(url, data, requestMethod)
                        if (callAxios.status === 200) {
                            this.sweetAlertmethod('success', sucessText, '', 'btn-success')
                            this.total = callAxios.data.length
                            if (callAxios.data) {
                                this.rows = callAxios.data
                                // this.data = {
                                //   id: '',
                                // }
                                this.data.id = ''
                            } else {
                                const deleted_index = this.rows.findIndex(item => item.id === data.id)
                                this.rows.splice(deleted_index, 1)
                                // console.log(this.rows)
                            }
                        } else {
                            this.sweetAlertmethod('warning', 'Warning!', callAxios.response.data.message, 'btn-success')
                            console.log('1')
                        }
                    } else {
                        console.log('2')
                    }
                })
        },
        s(icon, desc, title) {
            this.$swal({
                title: title,
                text: desc,
                icon: icon,
                customClass: {
                    confirmButton: 'btn btn-success',
                },
            })
        },
        toast(title, text, variant) {
            this.$toast({
                component: ToastificationContent,
                props: {
                    title: title,
                    icon: 'BellIcon',
                    text: text,
                    variant,
                },
            })
        },
        callAxios(url, data, requestMethod) {
            this.begin_loading()
            const response1 = axios({
                method: requestMethod,
                url: url,
                data: data,
            })
                .then(function (response) {
                    // console.log("response" + response);
                    return (response)
                    // console.log(response.data);
                    // console.log(response.status);
                    // console.log(response.statusText);
                    // console.log(response.headers);
                    // console.log(response.config);
                })
                .catch(function (error) {
                    console.log('error' + error)
                    return error
                })
            return response1
            this.end_loading()
        },
        callAxiosWithConfig(url, data, requestMethod, config) {
            this.begin_loading()
            const response1 = axios({
                method: requestMethod,
                url: url,
                data: data,

                headers: config,
            })
                .then(function (response) {
                    // console.log("response" + response);
                    return (response)
                    // console.log(response.data);
                    // console.log(response.status);
                    // console.log(response.statusText);
                    // console.log(response.headers);
                    // console.log(response.config);
                })
                .catch(function (error) {
                    // console.log("error" + error);
                    return error
                })
            return response1
            this.end_loading()
        },
        setWithExpiry(key, value, ttlInSeconds) {
            const now = new Date()

            // `item` is an object which contains the original value
            // as well as the time when it's supposed to expire
            const item = {
                value: value,
                expiry: now.getTime() + (ttlInSeconds * 1000),
            }
            localStorage.setItem(key, JSON.stringify(item))
        },

        getWithExpiry(key) {
            const itemStr = localStorage.getItem(key)

            // if the item doesn't exist, return null
            if (!itemStr) {
                return null
            }

            const item = JSON.parse(itemStr)
            const now = new Date()
            // console.log(now.getTime() + "---" + item.expiry)
            // compare the expiry time of the item with the current time
            if (now.getTime() > item.expiry) {
                // If the item is expired, delete the item from storage
                // and return null
                localStorage.removeItem(key)
                return null
            }
            return item.value
        },
        getMenuBarToShow() {
            const localUserId = JSON.parse(localStorage.getItem('userData')).id
            axios.post('/getMenuBarToShow', { user_id: localUserId })
                .then(res => {
                    this.dynamicMenu = res.data
                    this.menuData = [...this.dynamicMenu, ...this.navMenuItem]
                })
        },
    }
}
