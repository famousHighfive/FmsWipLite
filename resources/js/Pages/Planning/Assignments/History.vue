<script setup>
import { Head } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Tag from "primevue/tag";
import { Clock, User, Calendar, History } from "lucide-vue-next";

const props = defineProps({
    history: Array,
});

const getStatusLabel = (status) => {
    switch (status) {
        case 'validé': return 'success';
        case 'en attente': return 'warning';
        case 'suspendu': return 'danger';
        case 'terminé': return 'info';
        default: return 'secondary';
    }
};
</script>

<template>
    <Head title="Historique des Plannings" />

    <AppLayout>
        <div class="mb-8 flex justify-between items-center bg-white/50 backdrop-blur-sm p-6 rounded-[2rem] border border-white shadow-sm">
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Historique</h2>
                <p class="text-blue-500/70 text-xs font-bold uppercase tracking-widest mt-1">Suivez l'historique des modifications des plannings</p>
            </div>
        </div>

        <div class="py-8">
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6">

                <DataTable
                    :value="history"
                    paginator :rows="10"
                    stripedRows
                    responsiveLayout="stack"
                    class="p-datatable-sm custom-table"
                >
                    <Column field="planning_assignment.employee_name" header="Collaborateur">
                        <template #body="{ data }">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center text-purple-600 font-bold text-xs">
                                    {{ data.planning_assignment.employee_name.substring(0, 2).toUpperCase() }}
                                </div>
                                <span class="font-bold text-slate-700 text-sm">{{ data.planning_assignment.employee_name }}</span>
                            </div>
                        </template>
                    </Column>

                    <Column header="Ancien statut">
                        <template #body="{ data }">
                            <Tag v-if="data.old_status" :severity="getStatusLabel(data.old_status)" :value="data.old_status.toUpperCase()" class="!text-[9px] !font-black !px-3" />
                            <span v-else class="text-slate-400 text-xs">—</span>
                        </template>
                    </Column>

                    <Column header="Nouveau statut">
                        <template #body="{ data }">
                            <Tag :severity="getStatusLabel(data.new_status)" :value="data.new_status.toUpperCase()" class="!text-[9px] !font-black !px-3" />
                        </template>
                    </Column>

                    <Column field="changed_by" header="Modifié par">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2 text-slate-600">
                                <User class="w-3.5 h-3.5" />
                                <span class="text-sm font-medium">{{ data.changed_by }}</span>
                            </div>
                        </template>
                    </Column>

                    <Column field="reason" header="Raison">
                        <template #body="{ data }">
                            <span class="text-xs text-slate-500 italic">{{ data.reason }}</span>
                        </template>
                    </Column>

                    <Column field="created_at" header="Date">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                <Calendar class="w-3.5 h-3.5" />
                                {{ data.created_at }}
                            </div>
                        </template>
                    </Column>

                    <template #empty>
                        <div class="py-20 text-center flex flex-col items-center">
                            <History class="w-12 h-12 text-slate-200 mb-4" />
                            <p class="text-slate-400 font-medium">Aucun historique disponible.</p>
                        </div>
                    </template>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.custom-table :deep(.p-datatable-thead > tr > th) {
    @apply !bg-transparent !text-slate-400 !text-[11px] !font-black !uppercase !tracking-widest !border-b !border-slate-50;
}
.custom-table :deep(.p-datatable-tbody > tr) {
    @apply !transition-colors;
}
.custom-table :deep(.p-datatable-tbody > tr:hover) {
    @apply !bg-purple-50/30;
}
</style>
