<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Tag from "primevue/tag";
import Button from "primevue/button";
import { Clock, CheckCircle, AlertCircle, History } from "lucide-vue-next";

const props = defineProps({
    timesheets: Array,
});

const getStatusSeverity = (status) => {
    switch (status) {
        case "valide":
            return "success";
        case "soumis":
            return "info";
        case "brouillon":
            return "secondary";
        default:
            return "warn";
    }
};

const getStatusLabel = (status) => {
    switch (status) {
        case "valide":
            return "Validée";
        case "soumis":
            return "En attente";
        case "brouillon":
            return "Brouillon";
        default:
            return status;
    }
};
</script>

<template>
    <AppLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight">Gestion des Heures</h2>
                    <p class="text-slate-500 font-medium">Consultez et gérez les feuilles de temps.</p>
                </div>
                <div class="flex gap-3">
                    <Link :href="route('timesheets.entry')">
                        <Button label="Saisie des heures" icon="pi pi-plus" class="!bg-gradient-to-r !from-blue-600 !to-indigo-600 !border-0 !rounded-xl !shadow-lg !shadow-blue-500/20" />
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                        <Clock class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total feuilles</p>
                        <p class="text-2xl font-black text-slate-800">{{ timesheets.length }}</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-600">
                        <CheckCircle class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Validées</p>
                        <p class="text-2xl font-black text-slate-800">{{ timesheets.filter(t => t.status === 'valide').length }}</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                        <AlertCircle class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">En attente</p>
                        <p class="text-2xl font-black text-slate-800">{{ timesheets.filter(t => t.status === 'soumis').length }}</p>
                    </div>
                </div>
            </div>

            <div class="card !border-0 !shadow-sm !rounded-2xl overflow-hidden bg-white/50 backdrop-blur-sm">
                <DataTable :value="timesheets" paginator :rows="10" class="p-datatable-sm"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    currentPageReportTemplate="{first} à {last} sur {totalRecords}">
                    <template #header>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-slate-600 font-bold">Historique des feuilles de temps</span>
                        </div>
                    </template>

                    <Column header="Employé" sortable field="employee.user.name">
                        <template #body="{ data }">
                            <div class="font-bold text-slate-800">{{ data.employee?.user?.name || 'N/A' }}</div>
                            <div class="text-[10px] text-slate-400 font-black uppercase tracking-tighter">{{ data.employee?.matricule }}</div>
                        </template>
                    </Column>

                    <Column header="Période" sortable field="period_start">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2 text-sm font-bold text-slate-600">
                                <span>{{ new Date(data.period_start).toLocaleDateString() }}</span>
                                <span class="text-slate-300">→</span>
                                <span>{{ new Date(data.period_end).toLocaleDateString() }}</span>
                            </div>
                        </template>
                    </Column>

                    <Column header="Statut" sortable field="status">
                        <template #body="{ data }">
                            <Tag :value="getStatusLabel(data.status)" :severity="getStatusSeverity(data.status)" class="!rounded-lg !px-3" />
                        </template>
                    </Column>

                    <Column header="Validé par" field="validator.user.name">
                        <template #body="{ data }">
                            <span v-if="data.validator" class="text-sm font-medium text-slate-600">
                                {{ data.validator.user?.name }}
                            </span>
                            <span v-else class="text-slate-300 italic text-xs">En attente</span>
                        </template>
                    </Column>

                    <Column header="Actions">
                        <template #body="{ data }">
                            <Button icon="pi pi-eye" variant="text" rounded severity="secondary" @click="router.visit(route('timesheets.show', data.id))" />
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>
