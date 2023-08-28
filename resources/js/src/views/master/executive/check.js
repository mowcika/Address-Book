import ViewPrivateJobs from '@/views/elements/ViewPrivateJobs'
import { BButton, BFormCheckbox, BModal, VBModal } from 'bootstrap-vue'

export default {

    template: `
        <div>
        {{ cratedName }}
        </div>`,
    components: {
        ViewPrivateJobs,
        BButton,
        BModal,
        VBModal,
        BFormCheckbox,
    },
    data() {
        return {
            linkURL: null,
            idVariable: null,
            cratedName: '',
        }
    },

    created() {

        let userArraylist = this.params.api.mastersarray.expiryfollowupstatusArray
        let inbyid = this.params.data.last_followup_status
        if (inbyid) {
            this.cratedName = userArraylist.find(food => food.id === inbyid).source_name
        } else {
            this.cratedName = '-'
        }
        this.sortingOrder = ['desc', 'asc', null]
        this.defaultColDef = {
            sortable: true,
            filter: true,
            flex: 0,
            resizable: true,
        }
    },

}
