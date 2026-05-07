<script setup>
import { computed, ref } from "vue";
import { Head, router } from "@inertiajs/vue3";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Dialog from "primevue/dialog";
import TimesCard from "./TimesCard.vue";

const props = defineProps({
  calendar: Array,
});

// --- CALCUL DE LA PÉRIODE ---
const periodDates = computed(() => {
  if (!props.calendar?.length) return [];
  const start = new Date(props.calendar[0].period_start);
  const end = new Date(props.calendar[0].period_end);
  const dates = [];
  let current = new Date(start);
  while (current <= end) {
    dates.push(new Date(current).toISOString().split("T")[0]);
    current.setDate(current.getDate() + 1);
  }
  return dates;
});

// --- UTILITAIRES ---
const formatHeader = (d) => new Intl.DateTimeFormat("fr-FR", { weekday: "short", day: "numeric" }).format(new Date(d));

const getEntry = (entries, date) => {
  const found = entries?.find(e => e.date.startsWith(date));
  return found ? { ...found, is_empty: false } : { is_empty: true };
};

// --- LOGIQUE DES COULEURS (Version Douce) ---
const getCellClass = (timesheet, date) => {
    const entry = getEntry(timesheet.entries, date);
    
    // 1. Absence Totale -> Rouge doux
    if (entry.is_empty) return 'bg-red-50 border-red-200 text-red-700';

    // 2. Heures incomplètes (Réel < Prévu) -> Orange doux
    if (parseFloat(entry.total_hours) < parseFloat(entry.planned_hours)) {
        return 'bg-orange-50 border-orange-200 text-orange-700';
    }

    // 3. Statut Global
    if (timesheet.status === 'soumis') return 'bg-emerald-100 border-emerald-300 text-emerald-800';
    return 'bg-green-50 border-green-200 text-green-800'; // Par défaut Saisi/Valide
};

const getStatusBadgeClass = (status) => {
    if (status === 'soumis') return 'bg-emerald-600 text-white';
    if (status === 'valide') return 'bg-green-500 text-white';
    return 'bg-gray-400 text-white';
};

// --- ACTIONS ---
const displayModal = ref(false);
const selectedData = ref(null);

const openTimeCard = (timesheet, date) => {
  selectedData.value = {
    timesheet_id: timesheet.id,
    status: timesheet.status,
    employee_name: `${timesheet.employee.first_name} ${timesheet.employee.last_name}`,
    date: date,
    entry: timesheet.entries.find(e => e.date.startsWith(date)) || null
  };
  displayModal.value = true;
};

const submitTimesheet = (id) => {
    if (confirm("Soumettre définitivement cette semaine ?")) {
        router.post(`/timesheets/${id}/submit`);
    }
};

const calculateTotals = (timesheet) => {
  const worked = timesheet.entries.reduce((acc, e) => acc + parseFloat(e.total_hours || 0), 0);
  const planned = timesheet.entries.reduce((acc, e) => acc + parseFloat(e.planned_hours || 0), 0);
  return `${worked.toFixed(1)}h / ${planned.toFixed(1)}h`;
};
</script>

<template>
  <Head title="Timesheet Calendar" />
  
  <div class="p-6 bg-white min-h-screen">
    <DataTable :value="calendar" scrollable class="p-datatable-sm custom-table">
      
      <!-- Colonne : Employés -->
      <Column frozen header="Employés" style="min-width: 220px">
        <template #body="{ data }">
          <div class="flex flex-col">
            <span class="font-semibold text-gray-800 text-sm">{{ data.employee.first_name }} {{ data.employee.last_name }}</span>
            <span :class="getStatusBadgeClass(data.status)" class="text-[9px] w-fit px-1.5 py-0.5 rounded mt-1 font-bold uppercase">
                {{ data.status }}
            </span>
          </div>
        </template>
      </Column>

      <!-- Colonnes : Jours -->
      <Column v-for="date in periodDates" :key="date" :header="formatHeader(date)" class="text-center">
        <template #body="{ data }">
          <div @click="openTimeCard(data, date)" 
               :class="getCellClass(data, date)"
               class="m-1 p-2 border rounded shadow-sm cursor-pointer hover:brightness-95 transition-all flex flex-col items-center justify-center min-h-[55px]">
            
            <span class="text-[9px] font-bold uppercase">{{ data.status }}</span>
            
            <div v-if="!getEntry(data.entries, date).is_empty" class="text-xs font-black mt-1">
                {{ getEntry(data.entries, date).total_hours }}h
            </div>
            <div v-else class="text-[9px] font-bold opacity-60">ABSENT</div>
          </div>
        </template>
      </Column>

      <!-- Colonne : Total -->
      <Column header="Total" class="text-center" style="min-width: 100px">
        <template #body="{ data }">
          <span class="text-xs font-bold text-gray-700">{{ calculateTotals(data) }}</span>
        </template>
      </Column>

      <!-- Action -->
      <Column header="Action" class="text-center" style="min-width: 60px">
        <template #body="{ data }">
          <button @click="submitTimesheet(data.id)" 
                  :disabled="data.status === 'soumis'"
                  class="p-1.5 rounded-full transition-colors"
                  :class="data.status === 'soumis' ? 'text-emerald-500' : 'text-gray-400 hover:text-blue-600 hover:bg-blue-50'">
            <svg xmlns="http://w3.org" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path v-if="data.status !== 'soumis'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
          </button>
        </template>
      </Column>

    </DataTable>
  </div>

  <Dialog v-model:visible="displayModal" :header="'Pointage : ' + selectedData?.employee_name" :modal="true" :style="{ width: '400px' }">
    <TimesCard v-if="displayModal" :data="selectedData" @close="displayModal = false" />
  </Dialog>
</template>

<style scoped>
/* Supprime le style agressif de PrimeVue */
.custom-table :deep(.p-datatable-thead > tr > th) {
    background-color: #f8fafc !important;
    color: #475569 !important;
    font-size: 11px !important;
    border: 1px solid #e2e8f0 !important;
    padding: 10px 5px !important;
}
.custom-table :deep(.p-datatable-tbody > tr > td) {
    border: 1px solid #f1f5f9 !important;
}
</style>
