<script setup>
import {Head, router, Link} from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
defineProps({
    calendar: Array,
    auth: Object
})
</script>


<template>
<Head title ="Timesheets-General" />

<div>Mystac</div>
<div>
<DataTable :value="calendar" tableStyle="min-width: 50rem" :row="10">
    <Column field="period_start" header="Strating"></Column>
    <Column field="period_end" header="Ending"></Column>

    <Column field="employee.first_name" header="First Name"></Column>
    <Column field="employee.last_name" header="Last Name"></Column>
</DataTable>
</div>
</template>


<style scoped>


</style>