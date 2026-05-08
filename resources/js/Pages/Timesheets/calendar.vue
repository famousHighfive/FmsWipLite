<script setup>
import { computed, ref } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Dialog from "primevue/dialog";
import TimesCard from "./TimesCard.vue";
import ConfirmDialog from "./ConfirmDialog.vue";

const props = defineProps({
  calendar: Array,
});

// --- RÉCUPÉRATION DU RÔLE ---
const user = computed(() => usePage().props.auth.user);
const role = computed(() => user.value?.role?.name || 'TC');

// --- CALCUL DYNAMIQUE DE LA PÉRIODE ---
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

// --- LOGIQUE DES COULEURS DES CARTES ---
const getCellClass = (timesheet, date) => {
    const entry = getEntry(timesheet.entries, date);
    if (entry.is_empty) return 'bg-red-50 border-red-200 text-red-700';

    if (parseFloat(entry.total_hours) < parseFloat(entry.planned_hours)) {
        return 'bg-orange-50 border-orange-200 text-orange-700';
    }

    if (timesheet.status === 'soumis') return 'bg-emerald-100 border-emerald-300 text-emerald-800 font-bold';
    return 'bg-green-50 border-green-200 text-green-800';
};

const getStatusBadgeClass = (status) => {
    if (status === 'soumis') return 'bg-emerald-600 text-white';
    if (status === 'valide') return 'bg-green-500 text-white';
    return 'bg-gray-400 text-white';
};

// --- GESTION DES ACTIONS ---
const displayModal = ref(false);
const showConfirmDialog = ref(false);
const selectedData = ref(null);
const selectedForSubmit = ref(null);

const openTimeCard = (timesheet, date) => {
  selectedData.value = {
    timesheet_id: timesheet.id,
    status: timesheet.status,
    role: role.value,
    employee_name: `${timesheet.employee.first_name} ${timesheet.employee.last_name}`,
    date: date,
    entry: timesheet.entries.find(e => e.date.startsWith(date)) || null
  };
  displayModal.value = true;
};

const openConfirm = (timesheet) => {
    selectedForSubmit.value = { id: timesheet.id, name: `${timesheet.employee.first_name} ${timesheet.employee.last_name}` };
    showConfirmDialog.value = true;
};

// --- CALCUL DES TOTALS ---
const getTotalsData = (timesheet) => {
  if (!timesheet.entries) return { worked: 0, planned: 0 };
  const uniqueEntries = {};
  timesheet.entries.forEach(entry => {
    const dateKey = entry.date.split('T')[0];
    uniqueEntries[dateKey] = {
      worked: parseFloat(entry.total_hours || 0),
      planned: parseFloat(entry.planned_hours || 0)
    };
  });
  return Object.values(uniqueEntries).reduce((acc, curr) => {
    acc.worked += curr.worked;
    acc.planned += curr.planned;
    return acc;
  }, { worked: 0, planned: 0 });
};
</script>

<template>
  <Head title="Calendrier de Pointage" />
  <div class="p-6 bg-white min-h-screen">
    <DataTable :value="calendar" scrollable class="p-datatable-sm custom-table border rounded-xl overflow-hidden shadow-sm">
      
      <!-- Colonne : Employés -->
      <Column frozen header="Employés" style="min-width: 260px" class="bg-gray-50/50">
        <template #body="{ data }">
          <div class="flex flex-col py-2">
            <span class="font-bold text-gray-800 text-sm">{{ data.employee.first_name }} {{ data.employee.last_name }}</span>
            <small class="text-[10px] text-gray-500 mt-1 flex items-center gap-1">
                <i class="pi pi-user-check text-[9px]"></i>
                Validé par : 
                <span class="font-semibold text-gray-700">
                    {{ data.validator ? `${data.validator.first_name} ${data.validator.last_name}` : 'En attente' }}
                </span>
            </small>
            <span :class="getStatusBadgeClass(data.status)" class="text-[9px] w-fit px-1.5 py-0.5 rounded mt-1.5 font-black uppercase tracking-widest border">
                {{ data.status }}
            </span>
          </div>
        </template>
      </Column>

      <!-- Colonnes Dynamiques : Jours -->
      <Column v-for="date in periodDates" :key="date" :header="formatHeader(date)" class="text-center">
        <template #body="{ data }">
          <div @click="openTimeCard(data, date)" :class="getCellClass(data, date)" class="m-1 p-2 border rounded shadow-sm cursor-pointer hover:brightness-95 transition-all flex flex-col items-center justify-center min-h-[60px]">
            <span class="text-[8px] font-black uppercase tracking-tighter opacity-80">{{ data.status }}</span>
            <div v-if="!getEntry(data.entries, date).is_empty" class="text-xs font-black mt-1">
              {{ getEntry(data.entries, date).total_hours }}h
            </div>
            <div v-else class="text-[10px] font-bold opacity-60 italic uppercase tracking-tighter">Absent</div>
          </div>
        </template>
      </Column>

      <!-- Colonne : Total avec Style Custom -->
      <Column header="Total (R/P)" class="text-center" style="min-width: 120px">
        <template #body="{ data }">
          <div class="total-container flex items-center justify-center gap-1" 
               :class="getTotalsData(data).worked >= getTotalsData(data).planned ? 'text-green-600' : 'text-red-600'">
            
            <span class="real-hours text-lg font-black italic">
                {{ getTotalsData(data).worked.toFixed(1) }}
            </span>
            
            <span class="separator">/</span>
            
            <span class="planned-hours text-[10px] font-bold self-end mb-1 text-gray-500">
                {{ getTotalsData(data).planned.toFixed(1) }}h
            </span>
          </div>
        </template>
      </Column>

      <!-- Colonne Action -->
      <Column header="Action" class="text-center" style="min-width: 70px">
        <template #body="{ data }">
          <div class="flex justify-center">
            <button v-if="role === 'CP' && data.status !== 'soumis'" @click="openConfirm(data)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-full transition-all hover:scale-110">
                <svg xmlns="http://w3.org" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
            </button>
            <div v-if="data.status === 'soumis'" class="text-emerald-500 p-2">
                <svg xmlns="http://w3.org" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
          </div>
        </template>
      </Column>
    </DataTable>
  </div>

  <Dialog v-model:visible="displayModal" :header="'Détails : ' + selectedData?.employee_name" :modal="true" :style="{ width: '400px' }">
    <TimesCard v-if="displayModal" :data="selectedData" @close="displayModal = false" />
  </Dialog>

  <ConfirmDialog v-model:visible="showConfirmDialog" :timesheetId="selectedForSubmit?.id" :employeeName="selectedForSubmit?.name" />
</template>

<style scoped>
/* Style de la table */
.custom-table :deep(.p-datatable-thead > tr > th) {
    background-color: #f8fafc !important;
    color: #475569 !important;
    font-size: 11px !important;
    border: 1px solid #e2e8f0 !important;
    padding: 12px 6px !important;
    text-transform: uppercase;
}

/* Style de la barre inclinée */
.separator {
    font-size: 22px;
    font-weight: 300;
    color: #cbd5e1;
    transform: rotate(15deg);
    display: inline-block;
    margin: 0 2px;
}

.real-hours {
    letter-spacing: -1px;
}
</style>
