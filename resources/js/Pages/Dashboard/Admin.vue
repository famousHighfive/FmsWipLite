<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
defineProps({ stats: Object, campaignStats: Array, planningGaps: Object });
</script>

<template>
<AppLayout>

<template #header>
<h2 class="text-3xl font-black text-slate-800">
Dashboard Admin
</h2>
</template>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6">

<div class="bg-white p-6 rounded-3xl shadow-sm">
<p>Employés</p>
<h1 class="text-4xl font-black">{{ stats.employees }}</h1>
</div>

<div class="bg-white p-6 rounded-3xl shadow-sm">
<p>Campagnes</p>
<h1 class="text-4xl font-black">{{ stats.campaigns }}</h1>
</div>

<div class="bg-white p-6 rounded-3xl shadow-sm">
<p>Affectations</p>
<h1 class="text-4xl font-black">{{ stats.assignments }}</h1>
</div>

<div class="bg-white p-6 rounded-3xl shadow-sm">
<p>Heures</p>
<h1 class="text-4xl font-black">{{ stats.workedHours }}</h1>
</div>

</div>

</AppLayout>
</template>
