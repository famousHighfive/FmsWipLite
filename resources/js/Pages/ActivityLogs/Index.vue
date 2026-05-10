<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import Tag from 'primevue/tag';
import { ref } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';

defineProps({
    logs: Array // Reçu comme Array maintenant
});

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
</script>

<template>
    <Head title="Journal d'activité" />

    <AppLayout>

        <template #header>
            <div>
                <h2 class="text-2xl font-black text-slate-800">
                    Journal d'activité
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Historique des actions effectuées dans le système
                </p>
            </div>
        </template>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <DataTable 
                :value="logs" 
                v-model:filters="filters"
                :globalFilterFields="['user.name', 'action', 'model_type', 'description', 'ip_address']"
                paginator :rows="10" 
                class="p-datatable-sm"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                currentPageReportTemplate="Affichage de {first} à {last} sur {totalRecords} activités"
                stripedRows
            >
                <template #header>
                    <div class="flex justify-between items-center py-2 px-4">
                        <h3 class="text-lg font-black text-slate-800 m-0">
                            Activités récentes
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
                        <span class="font-bold text-slate-700">{{ data.user?.name || 'Système' }}</span>
                    </template>
                </Column>

                <Column field="action" header="Action" sortable>
                    <template #body="{ data }">
                        <Tag :value="data.action" severity="info" class="!text-[10px] font-black uppercase" />
                    </template>
                </Column>

                <Column field="model_type" header="Modèle" sortable class="text-slate-500" />
                
                <Column field="description" header="Description" sortable class="text-slate-600" />
                
                <Column field="ip_address" header="IP" sortable class="text-slate-500" />
                
                <Column field="created_at" header="Date" sortable>
                    <template #body="{ data }">
                        {{ new Date(data.created_at).toLocaleString('fr-FR') }}
                    </template>
                </Column>
            </DataTable>
        </div>

    </AppLayout>
</template>

<style scoped>
:deep(.p-datatable-thead > tr > th) {
    @apply !bg-slate-50 !text-slate-500 !text-xs !font-black !uppercase !py-4;
}
</style>
