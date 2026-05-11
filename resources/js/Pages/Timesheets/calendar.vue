<script setup>
import { computed, ref } from "vue";
import { Head, usePage, router } from "@inertiajs/vue3";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Dialog from "primevue/dialog";
import Button from 'primevue/button';
import TimesCard from "./TimesCard.vue";
import ConfirmDialog from "./ConfirmDialog.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { useToast } from "primevue/usetoast"; 
import Toast from "primevue/toast";

const toast = useToast();
const props = defineProps({
  calendar: Array,
});

// --- SÉLECTION MULTIPLE & PAGINATION ---
const selectedEmployees = ref([]);
const rows = ref(5); 

// --- GESTION DES RÔLES ---
const user = computed(() => usePage().props.auth.user);
const role = computed(() => user.value?.role?.name?.toLowerCase() || 'tc');

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

// --- UTILITAIRES D'AFFICHAGE ---
const formatHeader = (d) => new Intl.DateTimeFormat("fr-FR", { weekday: "short", day: "numeric" }).format(new Date(d));
// verifie si l'employer a une saisie des heure pour l'afficher dans les input
const getEntry = (entries, date) => {
  const found = entries?.find(e => e.date.startsWith(date));
  return found ? { ...found, is_empty: false } : { is_empty: true };
};

// --- LOGIQUE DES COULEURS ---
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

// --- GESTION DES MODALS ---
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
    selectedForSubmit.value = { 
        id: timesheet.id, 
        name: `${timesheet.employee.first_name} ${timesheet.employee.last_name}` 
    };
    showConfirmDialog.value = true;
};
// selection multiple
const openBulkEdit = () => {
    if (selectedEmployees.value.length === 0) return;
       // Vérification : est-ce qu'un des employés sélectionnés est déjà "soumis" ?
    const hasSubmitted = selectedEmployees.value.some(emp => emp.status === 'soumis');

    if (hasSubmitted) {
        toast.add({ 
            severity: 'error', 
            summary: 'Action impossible', 
            detail: 'Impossible de renseigner de nouvelles heures car un ou plusieurs employés ont un statut soumis.', 
            life: 5000 
        });
        return; // On arrête tout, le modal ne s'ouvrira pas
    }
    selectedData.value = {
        isBulk: true,
        timesheet_ids: selectedEmployees.value.map(item => item.id),
        employee_name: `${selectedEmployees.value.length} employés sélectionnés`,
        all_dates: periodDates.value,
        role: role.value,
        status: 'brouillon',
        timesheet_id: selectedEmployees.value[0].id 
    };
    displayModal.value = true;
};

// --- CALCUL DES TOTALS ---
const getTotalsData = (timesheet) => {
    if (!timesheet?.entries?.length) return { worked: 0, planned: 0 };

    return timesheet.entries.reduce((acc, entry) => {
        // parseFloat sécurisé avec repli à 0
        const worked = parseFloat(entry.total_hours) || 0;
        const planned = parseFloat(entry.planned_hours) || 0;

        return {
            worked: acc.worked + worked,
            planned: acc.planned + planned
        };
    }, { worked: 0, planned: 0 });
};

</script>
<template>
  <Head title="Calendrier de Pointage" />
  <Toast />
  <AppLayout>
    <div class="p-6 bg-[#fcfdfe] min-h-screen"> <div class="flex justify-between items-center mb-6">
        <div>
          <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tighter">Gestion des Flux</h1>
          <p class="text-slate-400 text-sm">Suivi hebdomadaire des présences</p>
        </div>
        <div class="flex gap-3">
          <Button v-if="selectedEmployees.length > 0" label="Saisie Groupée" icon="pi pi-users" class="p-button-help p-button-sm shadow-sm" @click="openBulkEdit" />
          <Button label="Nouveau Téléconseiller" icon="pi pi-user-plus" class="p-button-primary p-button-sm shadow-sm" @click="router.visit('/employees/create')" />
        </div>
      </div>

      <DataTable 
        :value="calendar" 
        v-model:selection="selectedEmployees" 
        :paginator="true" 
        :rows="rows" 
        dataKey="id" 
        scrollable 
        class="p-datatable-sm custom-table border-0 rounded-xl overflow-hidden shadow-xl shadow-slate-200/50"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
        currentPageReportTemplate="{first}-{last} sur {totalRecords}"
      >
<Column 
  selectionMode="multiple" 
  headerStyle="width: 3rem; background: #f8fafc" 
  frozen 
  :disabledSelection="data => data.status === 'soumis'"
></Column>
        
        <Column frozen header="Employés" style="min-width: 250px">
          <template #body="{ data }">
            <div class="flex flex-col py-2 px-1">
              <span class="font-bold text-slate-700 text-sm">{{ data.employee.first_name }} {{ data.employee.last_name }}</span>
              <div class="flex gap-2 mt-1">
                <small class="text-[10px] px-1.5 py-0.5 bg-slate-100 text-slate-500 rounded font-medium">ID: {{ data.employee.matricule }}</small>
                <small class="text-[10px] text-slate-400 self-center">Par : {{ data.validator?.first_name || 'N/A' }}</small>
              </div>
            </div>
          </template>
        </Column>

        <Column v-for="date in periodDates" :key="date" :header="formatHeader(date)" class="text-center">
          <template #body="{ data }">
            <div @click="openTimeCard(data, date)" 
                 :class="getCellClass(data, date)" 
                 class="m-1 p-2 border-2 rounded-lg cursor-pointer hover:scale-[1.02] active:scale-95 transition-all flex flex-col items-center justify-center min-h-[65px] group">
              <span class="text-[8px] font-bold uppercase tracking-widest opacity-60 group-hover:opacity-100">{{ data.status }}</span>
              <div v-if="!getEntry(data.entries, date).is_empty" class="text-sm font-black mt-0.5">
                {{ getEntry(data.entries, date).total_hours }}<span class="text-[10px] ml-0.5">h</span>
              </div>
              <div v-else class="text-[9px] font-bold opacity-40 italic uppercase tracking-tighter">Absent</div>
            </div>
          </template>
        </Column>

        <Column header="Total (R/P)" class="text-center" style="min-width: 130px">
          <template #body="{ data }">
            <div class="total-container flex items-center justify-center gap-1" :class="getTotalsData(data).worked >= getTotalsData(data).planned ? 'text-emerald-600' : 'text-rose-500'">
              <span class="real-hours text-xl font-black italic"> {{ getTotalsData(data).worked.toFixed(1) }} </span>
              <span class="separator">/</span>
              <span class="planned-hours text-[11px] font-bold self-end mb-1 text-slate-400"> {{ getTotalsData(data).planned.toFixed(1) }}h </span>
            </div>
          </template>
        </Column>

        <Column header="Action" class="text-center" style="min-width: 80px">
          <template #body="{ data }">
            <button v-if="(role === 'cp' || role === 'admin') && data.status !== 'soumis'" @click="openConfirm(data)" class="p-2 text-blue-500 hover:bg-blue-50 rounded-xl transition-all">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
              </svg>
            </button>
            <div v-if="data.status === 'soumis'" class="text-emerald-400 bg-emerald-50 w-8 h-8 flex items-center justify-center rounded-full mx-auto">
              <i class="pi pi-lock text-sm"></i>
            </div>
          </template>
        </Column>
      </DataTable>
    </div>

    <Dialog v-model:visible="displayModal" :header="'Pointage'" :modal="true" :style="{ width: '400px' }">
      <TimesCard v-if="displayModal" :data="selectedData" @close="displayModal = false" />
    </Dialog>

    <ConfirmDialog v-model:visible="showConfirmDialog" :timesheetId="selectedForSubmit?.id" :employeeName="selectedForSubmit?.name" />
  </AppLayout>
</template>

<style scoped>
/* Suppression du fond noir et uniformisation */
.custom-table :deep(.p-datatable-thead > tr > th) {
  background-color: #f8fafc !important;
  color: #64748b !important;
  font-size: 10px !important;
  border-bottom: 2px solid #f1f5f9 !important;
  padding: 16px 8px !important;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.custom-table :deep(.p-datatable-tbody > tr) {
  background-color: #ffffff !important;
  transition: background-color 0.2s;
}

.custom-table :deep(.p-datatable-tbody > tr:hover) {
  background-color: #fcfdfe !important;
}

.custom-table :deep(.p-datatable-tbody > tr > td) {
  border-bottom: 1px solid #f1f5f9 !important;
  padding: 8px !important;
}

.separator {
  font-size: 24px;
  font-weight: 200;
  color: #cbd5e1;
  transform: rotate(15deg); /* Ton style conservé */
  display: inline-block;
  margin: 0 2px;
}

.real-hours { letter-spacing: -1.5px; }

:deep(.p-paginator) { 
  background: #ffffff !important;
  border-top: 1px solid #f1f5f9 !important;
  padding: 0.75rem !important;
  color: #64748b !important;
}

:deep(.p-column-header-content) {
  justify-content: center;
}
</style>