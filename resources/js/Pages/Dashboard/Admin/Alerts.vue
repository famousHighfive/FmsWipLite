<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { AlertCircle, Bell, Clock, User, ShieldAlert } from 'lucide-vue-next';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import { ref } from 'vue';
import { FilterMatchMode } from '@primeuix/api';

defineProps({
    alerts: Array // Reçu comme Array maintenant
});

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const getActionClass = (action) => {
    switch (action) {
        case 'delete': return 'bg-rose-100 text-rose-700 border-rose-200';
        case 'create': return 'bg-emerald-100 text-emerald-700 border-emerald-200';
        case 'update': return 'bg-blue-100 text-blue-700 border-blue-200';
        case 'login': return 'bg-amber-100 text-amber-700 border-amber-200';
        default: return 'bg-slate-100 text-slate-700 border-slate-200';
    }
};
</script>

<template>
    <Head title="Alertes & Notifications" />

    <AppLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                        <Bell class="w-8 h-8 text-indigo-600" />
                        Alertes & Notifications
                    </h2>
                    <p class="text-slate-500 font-medium mt-1">
                        Surveillance en temps réel des activités critiques du système
                    </p>
                </div>
            </div>
        </template>

        <div class="py-8 space-y-8">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                <DataTable 
                    :value="alerts" 
                    v-model:filters="filters"
                    :globalFilterFields="['user.name', 'action', 'description', 'ip_address']"
                    paginator :rows="10" 
                    class="p-datatable-sm"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    currentPageReportTemplate="Affichage de {first} à {last} sur {totalRecords} alertes"
                    stripedRows
                >
                    <template #header>
                        <div class="px-8 py-6 flex items-center justify-between">
                            <h3 class="text-lg font-black text-slate-800 flex items-center gap-2 m-0">
                                <ShieldAlert class="w-5 h-5 text-indigo-600" />
                                Journal des Alertes
                            </h3>
                            <IconField iconPosition="left">
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText v-model="filters['global'].value" placeholder="Recherche automatique..." class="!rounded-xl !bg-slate-50/50" />
                            </IconField>
                        </div>
                    </template>

                    <Column field="user.name" header="Utilisateur" sortable>
                        <template #body="{ data }">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-black text-sm">
                                    {{ data.user?.name?.charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">{{ data.user?.name }}</p>
                                    <p class="text-xs text-slate-400">{{ data.ip_address }}</p>
                                </div>
                            </div>
                        </template>
                    </Column>

                    <Column field="action" header="Type" sortable>
                        <template #body="{ data }">
                            <span :class="['px-3 py-1 rounded-lg text-[10px] font-black uppercase border', getActionClass(data.action)]">
                                {{ data.action }}
                            </span>
                        </template>
                    </Column>

                    <Column field="description" header="Description" sortable>
                        <template #body="{ data }">
                            <p class="text-slate-600 font-medium leading-relaxed max-w-md">
                                {{ data.description }}
                            </p>
                            <p class="text-[10px] text-slate-400 mt-1 uppercase font-bold tracking-tighter">
                                {{ data.model_type }} #{{ data.model_id }}
                            </p>
                        </template>
                    </Column>

                    <Column field="created_at" header="Date & Heure" sortable>
                        <template #body="{ data }">
                            <div class="flex items-center gap-2 text-slate-500 font-bold text-sm">
                                <Clock class="w-4 h-4 text-slate-300" />
                                {{ new Date(data.created_at).toLocaleString('fr-FR') }}
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Optionnel : cacher la barre de défilement pour un look plus propre */
::-webkit-scrollbar {
    height: 4px;
}
::-webkit-scrollbar-track {
    background: transparent;
}
::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
</style>
