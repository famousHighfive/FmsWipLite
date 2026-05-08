<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import Timeline from 'primevue/timeline';

const props = defineProps({
    history: Object // Paginated
});

const getActionSeverity = (type) => {
    switch (type) {
        case 'assign': return 'success';
        case 'release': return 'danger';
        case 'transfer': return 'info';
        default: return 'secondary';
    }
};

const getActionLabel = (type) => {
    switch (type) {
        case 'assign': return 'Affectation';
        case 'release': return 'Libération';
        case 'transfer': return 'Transfert / Réaffectation';
        default: return type;
    }
};
</script>

<template>
    <AppLayout>
        <div class="p-6 bg-slate-50 min-h-screen">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Historique des Affectations</h1>
                    <p class="text-slate-500">Traces complètes de tous les mouvements RH</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Table -->
                <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <DataTable :value="props.history.data" class="p-datatable-custom" responsiveLayout="stack">
                        <Column field="created_at" header="Date" sortable />
                        
                        <Column header="Employé">
                            <template #body="{ data }">
                                <div class="font-bold text-slate-900">
                                    {{ data.employee.first_name }} {{ data.employee.last_name }}
                                </div>
                            </template>
                        </Column>

                        <Column header="Action">
                            <template #body="{ data }">
                                <Tag :value="getActionLabel(data.action_type)" :severity="getActionSeverity(data.action_type)" rounded />
                            </template>
                        </Column>

                        <Column header="Campagne">
                            <template #body="{ data }">
                                <div v-if="data.new_campaign" class="text-sm">
                                    <i class="pi pi-arrow-right text-green-500 mr-1" />
                                    {{ data.new_campaign.name }}
                                </div>
                                <div v-if="data.old_campaign" class="text-sm text-slate-400">
                                    <i class="pi pi-history mr-1" />
                                    {{ data.old_campaign.name }}
                                </div>
                            </template>
                        </Column>

                        <Column header="Par">
                            <template #body="{ data }">
                                <div class="text-sm text-slate-600">{{ data.author.name }}</div>
                            </template>
                        </Column>
                    </DataTable>
                </div>

                <!-- Recent Timeline Sidebar -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-6">Derniers mouvements</h3>
                    <Timeline :value="props.history.data.slice(0, 5)" class="customized-timeline">
                        <template #marker="slotProps">
                            <span class="flex w-8 h-8 items-center justify-center text-white rounded-full z-10 shadow-sm" 
                                  :class="getActionSeverity(slotProps.item.action_type) === 'success' ? 'bg-green-500' : (getActionSeverity(slotProps.item.action_type) === 'danger' ? 'bg-red-500' : 'bg-blue-500')">
                                <i :class="slotProps.item.action_type === 'assign' ? 'pi pi-user-plus' : (slotProps.item.action_type === 'release' ? 'pi pi-user-minus' : 'pi pi-sync')"></i>
                            </span>
                        </template>
                        <template #content="slotProps">
                            <div class="mb-4 ml-2">
                                <div class="font-bold text-slate-900">{{ slotProps.item.employee.first_name }}</div>
                                <div class="text-sm text-slate-500">{{ getActionLabel(slotProps.item.action_type) }}</div>
                                <div class="text-xs text-slate-400 mt-1">{{ slotProps.item.created_at }}</div>
                            </div>
                        </template>
                    </Timeline>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.customized-timeline :deep(.p-timeline-event-opposite) {
    display: none;
}
</style>
