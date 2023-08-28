<template>
    <common-draft title="Private Jobs" table="jobs" page="search" :filterTable=this.data.filterTable>
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
