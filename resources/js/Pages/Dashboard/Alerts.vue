<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
logs: Array
});
</script>

<template>
<AppLayout>

<template #header>
<h2 class="text-3xl font-black text-slate-800">
Alertes & Notifications
</h2>
</template>

<div class="space-y-4">

<div
v-for="log in logs"
:key="log.id"
class="bg-white rounded-3xl p-5 shadow-sm"
>
<h3 class="font-black">
{{ log.action }}
</h3>

<p class="text-slate-500">
{{ log.description }}
</p>

</div>

</div>

</AppLayout>
</template>
