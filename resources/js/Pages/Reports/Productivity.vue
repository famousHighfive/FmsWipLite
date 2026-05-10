<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { TrendingUp, User, Clock } from 'lucide-vue-next';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import { ref } from 'vue';
import { FilterMatchMode } from '@primeuix/api';

defineProps({
    productivityStats: Array
});

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
</script>

<template>
    <Head title="Productivité" />

    <AppLayout>
        <div class="space-y-8">
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Analyse de Productivité</h2>
                <p class="text-slate-500 font-medium">Comparaison entre les heures prévues et les heures réellement travaillées.</p>
            </div>

            <!-- Table Section -->
            <div class="bg-white/50 backdrop-blur-sm p-8 rounded-3xl border border-white shadow-sm">
                <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                    <TrendingUp class="w-5 h-5 text-blue-600" />
                    Classement par ratio de production
                </h3>
                
                <DataTable 
                    :value="productivityStats" 
                    v-model:filters="filters"
                    :globalFilterFields="['name']"
                    class="p-datatable-sm" 
                    paginator :rows="15"
                >
                    <template #header>
                        <div class="flex justify-end mb-4">
                            <IconField iconPosition="left">
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText v-model="filters['global'].value" placeholder="Chercher un collaborateur..." class="!rounded-xl !bg-slate-50/50" />
                            </IconField>
                        </div>
                    </template>
                    <Column field="name" header="Collaborateur" sortable>
                        <template #body="{ data }">
                            <span class="font-bold text-slate-700">{{ data.name }}</span>
                        </template>
                    </Column>
                    <Column field="planned" header="Heures Prévues" sortable>
                        <template #body="{ data }">
                            <span class="text-slate-500">{{ data.planned }}h</span>
                        </template>
                    </Column>
                    <Column field="worked" header="Heures Réalisées" sortable>
                        <template #body="{ data }">
                            <span class="font-bold text-blue-600">{{ data.worked }}h</span>
                        </template>
                    </Column>
                    <Column field="ratio" header="Ratio de Productivité" sortable>
                        <template #body="{ data }">
                            <div class="flex items-center gap-4">
                                <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div :class="['h-full', data.ratio >= 100 ? 'bg-green-500' : 'bg-amber-500']" :style="{ width: Math.min(data.ratio, 100) + '%' }"></div>
                                </div>
                                <span :class="['text-xs font-black w-12', data.ratio >= 100 ? 'text-green-600' : 'text-amber-600']">
                                    {{ Math.round(data.ratio) }}%
                                </span>
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>
