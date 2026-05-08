<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
campaigns: Array,
employees: Array
});
</script>

<template>
<AppLayout>

<template #header>
<h2 class="text-3xl font-black text-slate-800">
Statistiques Générales
</h2>
</template>

<div class="bg-white rounded-3xl p-6 shadow-sm">

<table class="w-full">

<thead>
<tr>
<th>Campagne</th>
<th>Affectations</th>
</tr>
</thead>

<tbody>
<tr v-for="campaign in campaigns" :key="campaign.id">
<td>{{ campaign.name }}</td>
<td>{{ campaign.assignments_count }}</td>
</tr>
</tbody>

</table>

</div>

</AppLayout>
</template>
