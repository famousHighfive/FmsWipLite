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
    employee:  Object,
    histories: Object, // paginé
});

// ---------------------------------------------------------
// LABELS LISIBLES pour les champs
// ---------------------------------------------------------
const fieldLabels = {
    first_name:  'Prénom',
    last_name:   'Nom',
    email:       'Email',
    phone:       'Téléphone',
    address:     'Adresse',
    birth_date:  'Date de naissance',
    position_id: 'Poste',
    salary_base: 'Salaire de base',
    status:      'Statut',
    user_id:     'Compte utilisateur',
};

const getFieldLabel = (field) => fieldLabels[field] ?? field;

// ---------------------------------------------------------
// STATUT — badge coloré
// ---------------------------------------------------------
const getStatusSeverity = (status) => {
    switch (status) {
        case "actif":    return "info";
        case "suspendu": return "warn";
        case "inactif":  return "danger";
        default:         return null;
    }
};

// ---------------------------------------------------------
// PAGINATION CÔTÉ SERVEUR
// ---------------------------------------------------------
const onPageChange = (event) => {
    router.get(route('employees.history', props.employee.id), {
        page: event.page + 1,
    }, { preserveState: true });
};

// ---------------------------------------------------------
// NAVIGATION
// ---------------------------------------------------------
const goBack = () => router.visit(route('employees.show', props.employee.id));
</script>

<template>
    <AppLayout>
        <div class="flex flex-col gap-6">

            <!-- HEADER -->
            <div class="flex items-center gap-3">
                <Button
                    icon="pi pi-arrow-left"
                    variant="text"
                    severity="secondary"
                    @click="goBack"
                />
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">
                        Historique des modifications
                    </h2>
                    <div class="flex items-center gap-2 text-slate-500 text-sm">
                        <span>{{ employee.first_name }} {{ employee.last_name }}</span>
                        <span>·</span>
                        <span class="font-mono text-blue-600">{{ employee.matricule }}</span>
                        <span>·</span>
                        <Tag
                            :value="employee.status"
                            :severity="getStatusSeverity(employee.status)"
                        />
                    </div>
                </div>
            </div>

            <!-- DATATABLE HISTORIQUE -->
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
                            <h4 class="m-0">Journal des modifications</h4>
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

                    <!-- Champ modifié -->
                    <Column header="Champ modifié" style="min-width: 10rem">
                        <template #body="{ data }">
                            <span class="font-semibold text-blue-700 text-sm uppercase tracking-wide">
                                {{ getFieldLabel(data.field) }}
                            </span>
                        </template>
                    </Column>

                    <!-- Ancienne valeur -->
                    <Column header="Ancienne valeur" style="min-width: 12rem">
                        <template #body="{ data }">
                            <span class="text-slate-400 line-through text-sm">
                                {{ data.old_value ?? '—' }}
                            </span>
                        </template>
                    </Column>

                    <!-- Nouvelle valeur -->
                    <Column header="Nouvelle valeur" style="min-width: 12rem">
                        <template #body="{ data }">
                            <Tag
                                v-if="data.field === 'status'"
                                :value="data.new_value"
                                :severity="getStatusSeverity(data.new_value)"
                            />
                            <span v-else class="font-medium text-slate-800 text-sm">
                                {{ data.new_value ?? '—' }}
                            </span>
                        </template>
                    </Column>

                    <!-- Modifié par -->
                    <Column header="Modifié par" style="min-width: 10rem">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 flex items-center justify-center text-white text-xs font-bold">
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
                    <span class="text-sm">Les modifications de cet employé apparaîtront ici.</span>
                </div>
            </div>

        </div>
    </AppLayout>
</template>