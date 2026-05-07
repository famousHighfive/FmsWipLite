<script setup>
import { computed } from "vue";
import { Head } from "@inertiajs/vue3";
import DataTable from "primevue/datatable";
import Column from "primevue/column";

// 1. Récupération des données du controller
const props = defineProps({
    calendar: Array,
});

/**
 * 2. LOGIQUE DYNAMIQUE DES DATES
 * Transforme period_start et period_end en un tableau de jours individuels
 */
const periodDates = computed(() => {
    if (!props.calendar || props.calendar.length === 0) return [];

    // On prend la période de la première fiche (commune à la vue)
    const start = new Date(props.calendar[0].period_start);
    const end = new Date(props.calendar[0].period_end);
    const dates = [];

    let current = new Date(start);

    // Boucle tant qu'on n'a pas dépassé la date de fin
    while (current <= end) {
        // On stocke la date au format ISO (YYYY-MM-DD) pour faciliter la recherche
        dates.push(new Date(current).toISOString().split("T")[0]);
        // On ajoute +1 jour au curseur
        current.setDate(current.getDate() + 1);
    }
    return dates;
});

/**
 * 3. FORMATTAGE DES EN-TÊTES
 * Transforme "2026-05-04" en "lun. 4"
 */
const formatDateHeader = (dateStr) => {
    return new Intl.DateTimeFormat("fr-FR", {
        weekday: "short",
        day: "numeric",
    }).format(new Date(dateStr));
};

/**
 * 4. RECHERCHE D'ENTRÉE
 * Pour chaque cellule, on cherche si l'employé a une donnée pour cette date
 */
const getEntryByDate = (entries, targetDate) => {
    return entries.find((e) => e.date === targetDate) || { is_empty: true };
};
</script>

<template>
    <Head title="Gestion Timesheets" />
    <h2>Genesis</h2>
    <DataTable
        :value="calendar"
        scrollable
        class="p-datatable-gridlines text-sm"
    >
        <Column frozen header="Employés" style="min-width: 200px">
            <template #body="{ data }">
                <div class="font-bold">
                    {{ data.employee.first_name }} {{ data.employee.last_name }}
                </div>
                <small class="text-gray-500">{{
                    data.validator?.name || "Pas de validateur"
                }}</small>
            </template>
        </Column>

        <Column
            v-for="date in periodDates"
            :key="date"
            :header="formatDateHeader(date)"
            class="text-center"
        >
            <template #body="{ data }">
                <section class="cell-content">
                    <div
                        v-if="!getEntryByDate(data.entries, date).is_empty"
                        class="p-2 bg-blue-50 border rounded text-blue-800"
                    >
                        <strong>{{
                            getEntryByDate(data.entries, date).period_start
                        }}</strong
                        ><br />
                        <strong>{{
                            getEntryByDate(data.entries, date).period_end
                        }}</strong>
                    </div>
                    <div
                        v-else
                        class="p-4 bg-gray-50 opacity-50 border-dashed border rounded"
                    >
                        <span class="text-xs text-gray-400 italic">Absent</span>
                    </div>
                </section>
            </template>
        </Column>

        <Column header="Total" class="text-right font-bold">
            <template #body>--h</template>
        </Column>
    </DataTable>
</template>

<style scoped>
/* Style rapide pour la propreté visuelle */
:deep(.p-column-header-content) {
    justify-content: center;
    text-transform: capitalize;
}
.employee-info {
    line-height: 1.2;
}
</style>
