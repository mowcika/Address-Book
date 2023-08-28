<template>
    <common-draft title="View Send Notification" table="save_notification" page="View Send Notification"
                  :filterTable=this.data.filterTable
    >
    </common-draft>
</template>

<script>
import CommonDraft from '@/views/listing/CommonDraft.vue'

export default {
    data() {
        return {
            data: {},

        }
    },
    components: {
        CommonDraft,
    },
}
</script>
