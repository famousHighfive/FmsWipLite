<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, computed } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import DatePicker from "primevue/datepicker";
import { Clock, Save, ArrowLeft, User } from "lucide-vue-next";

const props = defineProps({
    subordinates: Array,
    plannings: Array,
    startDate: String,
    endDate: String,
});

const currentDate = ref(props.startDate);

const changeWeek = (offset) => {
    const date = new Date(currentDate.value);
    date.setDate(date.getDate() + (offset * 7));
    router.get(route('timesheets.entry'), { date: date.toISOString().split('T')[0] }, {
        preserveState: true,
        onSuccess: () => {
            currentDate.value = props.startDate;
            if (form.employee_id) initEntries(form.employee_id);
        }
    });
};

const selectedEmployee = ref(null);
const weekEntries = ref([]);

const form = useForm({
    employee_id: null,
    period_start: props.startDate,
    period_end: props.endDate,
    entries: [],
});

// Générer les jours de la semaine
const days = computed(() => {
    const start = new Date(props.startDate);
    const result = [];
    for (let i = 0; i < 7; i++) {
        const d = new Date(start);
        d.setDate(start.getDate() + i);
        result.push({
            date: d.toISOString().split('T')[0],
            label: d.toLocaleDateString('fr-FR', { weekday: 'short', day: 'numeric', month: 'short' })
        });
    }
    return result;
});

const initEntries = (empId) => {
    selectedEmployee.value = props.subordinates.find(e => e.id === empId);
    form.employee_id = empId;
    form.entries = days.value.map(day => {
        // Chercher le planning pour ce jour
        const planning = props.plannings.find(p => p.employee_id === empId);
        const dayName = new Date(day.date).toLocaleDateString('en-US', { weekday: 'long' }).toLowerCase();
        const plannedHours = planning ? planning.planningModel[`${dayName}_hours`] : 0;

        return {
            date: day.date,
            check_in: '08:00',
            check_out: '17:00',
            break_duration: 60,
            planned_hours: plannedHours,
            absence_type: null,
            comment: ''
        };
    });
};

const submit = () => {
    form.post(route('timesheets.store'), {
        onSuccess: () => {
            router.visit(route('timesheets.index'));
        }
    });
};
</script>

<template>
    <AppLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Button icon="pi pi-arrow-left" severity="secondary" rounded variant="text" @click="router.visit(route('timesheets.index'))" />
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Saisie des Heures</h2>
                        <div class="flex items-center gap-2 mt-1">
                            <Button icon="pi pi-chevron-left" variant="text" severity="secondary" rounded size="small" @click="changeWeek(-1)" />
                            <p class="text-slate-500 font-medium text-sm">Semaine du {{ new Date(startDate).toLocaleDateString() }} au {{ new Date(endDate).toLocaleDateString() }}</p>
                            <Button icon="pi pi-chevron-right" variant="text" severity="secondary" rounded size="small" @click="changeWeek(1)" />
                        </div>
                    </div>
                </div>
                <Button v-if="form.employee_id" label="Enregistrer la semaine" icon="pi pi-check" class="!bg-gradient-to-r !from-blue-600 !to-indigo-600 !border-0 !rounded-xl !shadow-lg !shadow-blue-500/20" @click="submit" />
            </div>

            <div class="grid grid-cols-12 gap-8">
                <div class="col-span-12 lg:col-span-4 space-y-4">
                    <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest px-2">Sélectionner un collaborateur</h3>
                    <div class="space-y-2">
                        <button 
                            v-for="emp in subordinates" 
                            :key="emp.id"
                            @click="initEntries(emp.id)"
                            :class="[
                                'w-full flex items-center gap-4 p-4 rounded-2xl border transition-all text-left',
                                form.employee_id === emp.id 
                                    ? 'bg-white border-blue-200 shadow-md shadow-blue-500/5' 
                                    : 'bg-white/50 border-slate-100 hover:border-slate-200'
                            ]"
                        >
                            <div :class="['w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm', form.employee_id === emp.id ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-500']">
                                {{ emp.user?.name?.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">{{ emp.user?.name }}</p>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">{{ emp.matricule }}</p>
                            </div>
                        </button>
                    </div>
                </div>

                <div class="col-span-12 lg:col-span-8">
                    <div v-if="!form.employee_id" class="h-64 flex flex-col items-center justify-center text-slate-300 bg-white/30 border-2 border-dashed border-slate-100 rounded-3xl">
                        <User class="w-12 h-12 mb-2 opacity-20" />
                        <p class="font-bold">Choisissez un employé pour commencer la saisie</p>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="(entry, index) in form.entries" :key="index" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 grid grid-cols-12 gap-6 items-center">
                            <div class="col-span-12 md:col-span-3">
                                <p class="text-xs font-black text-blue-500 uppercase tracking-widest mb-1">{{ days[index].label }}</p>
                                <p class="font-bold text-slate-800">{{ entry.date }}</p>
                            </div>

                            <div class="col-span-6 md:col-span-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Arrivée</label>
                                <InputText v-model="entry.check_in" type="time" class="w-full !rounded-xl !bg-slate-50/50 !border-slate-100" />
                            </div>

                            <div class="col-span-6 md:col-span-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Départ</label>
                                <InputText v-model="entry.check_out" type="time" class="w-full !rounded-xl !bg-slate-50/50 !border-slate-100" />
                            </div>

                            <div class="col-span-6 md:col-span-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Pause (min)</label>
                                <InputText v-model="entry.break_duration" type="number" class="w-full !rounded-xl !bg-slate-50/50 !border-slate-100" />
                            </div>

                            <div class="col-span-6 md:col-span-3">
                                <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Absence</label>
                                <Select v-model="entry.absence_type" :options="['Congé', 'Maladie', 'Retard', 'Autre']" placeholder="Statut" class="w-full !rounded-xl !bg-slate-50/50 !border-slate-100" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
