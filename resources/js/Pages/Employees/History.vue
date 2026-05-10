<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { router } from "@inertiajs/vue3";
import Tag from "primevue/tag";
import Button from "primevue/button";
import Column from "primevue/column";
import DataTable from "primevue/datatable";

// ---------------------------------------------------------
// PROPS
// ---------------------------------------------------------
const props = defineProps({
    histories: Object, // paginé — toutes les modifications
});

// ---------------------------------------------------------
// STATUT — badge coloré
// ---------------------------------------------------------
const getStatusSeverity = (status) => {
    switch (status) {
        case "actif":    return "success";
        case "suspendu": return "warn";
        case "inactif":  return "danger";
        default:         return null;
    }
};

// ---------------------------------------------------------
// PAGINATION CÔTÉ SERVEUR
// ---------------------------------------------------------
const onPageChange = (event) => {
    router.get(route('employees.history'), {
        page: event.page + 1,
    }, { preserveState: true });
};

// ---------------------------------------------------------
// NAVIGATION — vers le profil de l'employé
// ---------------------------------------------------------
const goToEmployee = (employeeId) => {
    router.visit(route('employees.show', employeeId));
};
</script>

<template>
    <AppLayout>
        <div class="flex flex-col gap-6">

            <!-- HEADER -->
            <div class="flex items-center gap-3">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">
                        Historique des modifications
                    </h2>
                    <span class="text-slate-500 text-sm">
                        Toutes les modifications de postes et statuts des employés
                    </span>
                </div>
            </div>

            <!-- DATATABLE -->
            <div class="card">
                <DataTable
                    :value="histories.data"
                    :lazy="true"
                    :paginator="true"
                    :rows="histories.per_page"
                    :totalRecords="histories.total"
                    :rowsPerPageOptions="[15, 25, 50]"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    currentPageReportTemplate="Affichage de {first} à {last} sur {totalRecords} modifications"
                    @page="onPageChange"
                >
                    <template #header>
                        <div class="flex items-center justify-between">
                            <h4 class="m-0">Journal global des modifications</h4>
                            <span class="text-sm text-slate-400">
                                {{ histories.total }} modification(s) enregistrée(s)
                            </span>
                        </div>
                    </template>

                    <!-- Date -->
                    <Column header="Date" style="min-width: 12rem">
                        <template #body="{ data }">
                            <span class="text-slate-600 text-sm">
                                {{ new Date(data.created_at).toLocaleDateString('fr-FR') }}
                                {{ new Date(data.created_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }) }}
                            </span>
                        </template>
                    </Column>

                    <!-- Employé concerné -->
                    <Column header="Employé" style="min-width: 14rem">
                        <template #body="{ data }">
                            <div
                                class="flex flex-col cursor-pointer hover:text-blue-600 transition-colors"
                                @click="goToEmployee(data.employee_id)"
                            >
                                <span class="font-medium text-slate-800 text-sm">
                                    {{ data.employee?.first_name }} {{ data.employee?.last_name }}
                                </span>
                                <span class="font-mono text-xs text-blue-600">
                                    {{ data.employee?.matricule }}
                                </span>
                            </div>
                        </template>
                    </Column>

                    <!-- Ancien poste → Nouveau poste -->
                    <Column header="Poste" style="min-width: 16rem">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2 text-sm">
                                <span class="text-slate-400 line-through">
                                    {{ data.old_position?.name ?? '—' }}
                                </span>
                                <template v-if="data.old_position_id !== data.new_position_id">
                                    <i class="pi pi-arrow-right text-xs text-slate-400" />
                                    <span class="font-medium text-slate-800">
                                        {{ data.new_position?.name ?? '—' }}
                                    </span>
                                </template>
                                <span v-else class="text-slate-400 text-xs italic">Inchangé</span>
                            </div>
                        </template>
                    </Column>

                    <!-- Ancien statut → Nouveau statut -->
                    <Column header="Statut" style="min-width: 14rem">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <Tag
                                    v-if="data.old_status"
                                    :value="data.old_status"
                                    :severity="getStatusSeverity(data.old_status)"
                                    class="opacity-50"
                                />
                                <template v-if="data.old_status !== data.new_status">
                                    <i class="pi pi-arrow-right text-xs text-slate-400" />
                                    <Tag
                                        :value="data.new_status"
                                        :severity="getStatusSeverity(data.new_status)"
                                    />
                                </template>
                                <span v-else class="text-slate-400 text-xs italic">Inchangé</span>
                            </div>
                        </template>
                    </Column>

                    <!-- Raison -->
                    <Column header="Raison" style="min-width: 12rem">
                        <template #body="{ data }">
                            <span class="text-slate-600 text-sm">
                                {{ data.reason ?? '—' }}
                            </span>
                        </template>
                    </Column>

                    <!-- Modifié par -->
                    <Column header="Modifié par" style="min-width: 10rem">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ data.changed_by?.name?.charAt(0)?.toUpperCase() ?? 'S' }}
                                </div>
                                <span class="text-sm text-slate-700">
                                    {{ data.changed_by?.name ?? 'Système' }}
                                </span>
                            </div>
                        </template>
                    </Column>

                </DataTable>

                <!-- Aucun historique -->
                <div v-if="histories.total === 0" class="flex flex-col items-center gap-3 py-16 text-slate-400">
                    <i class="pi pi-inbox text-5xl" />
                    <span class="text-lg">Aucune modification enregistrée</span>
                    <span class="text-sm">Les changements de poste et de statut apparaîtront ici.</span>
                </div>

            </div>
        </div>
    </AppLayout>
</template>