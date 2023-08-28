<template>
    <common-draft title="Notification" table="save_notification" page="notification" :filterTable=this.data.filterTable>
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
