<script setup>
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Badge from 'primevue/badge';
import { Plus, Edit2, Calendar, User, ArrowRight, X, Clock } from 'lucide-vue-next';

const props = defineProps({
    planningModels: Array,
    activeAssignments: Array
});

const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
    name: '',
    monday_hours: 0, tuesday_hours: 0, wednesday_hours: 0, thursday_hours: 0,
    friday_hours: 0, saturday_hours: 0, sunday_hours: 0, total_hours: 0,
});

const autoTotal = computed(() => {
    const total = Number(form.monday_hours || 0) + Number(form.tuesday_hours || 0) +
                  Number(form.wednesday_hours || 0) + Number(form.thursday_hours || 0) +
                  Number(form.friday_hours || 0) + Number(form.saturday_hours || 0) +
                  Number(form.sunday_hours || 0);
    form.total_hours = total;
    return total;
});

const submit = () => {
    const action = isEditing.value
        ? () => form.put(route('planning.models.update', editingId.value))
        : () => form.post(route('planning.models.store'));

    action({
        onSuccess: () => {
            showModal.value = false; // Fermeture immédiate
            form.reset();
        }
    });
};

const openEditModal = (model) => {
    isEditing.value = true;
    editingId.value = model.id;
    form.name = model.name;
    weekDays.forEach(d => form[d.key] = Number(model[d.key]));
    showModal.value = true;
};

const weekDays = [
    { label: 'Lun', key: 'monday_hours' }, { label: 'Mar', key: 'tuesday_hours' },
    { label: 'Mer', key: 'wednesday_hours' }, { label: 'Jeu', key: 'thursday_hours' },
    { label: 'Ven', key: 'friday_hours' }, { label: 'Sam', key: 'saturday_hours' },
    { label: 'Dim', key: 'sunday_hours' },
];
</script>

<template>
    <Head title="Modèles de Planning" />
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center w-full">
                <div>
                    <h2 class="text-xl font-black text-slate-800">Modèles de Planning</h2>
                    <p class="text-blue-600 text-[10px] font-bold uppercase tracking-widest">Configuration des horaires types</p>
                </div>
                <Button @click="isEditing = false; form.reset(); showModal = true"
                    class="!bg-blue-600 !border-none !rounded-2xl !px-6 !py-3 !shadow-xl !shadow-blue-200">
                    <Plus class="w-5 h-5 text-white mr-2" />
                    <span class="text-white font-black text-sm">Nouveau modèle</span>
                </Button>
            </div>
        </template>

        <div class="grid grid-cols-12 gap-8">
            <!-- Liste -->
            <div class="col-span-5 space-y-4">
                <div v-for="model in planningModels" :key="model.id" class="bg-white border border-slate-200 p-6 rounded-[2rem] shadow-sm">
                    <div class="flex justify-between items-start mb-6">
                        <h4 class="font-black text-slate-800 tracking-tight text-lg">{{ model.name }}</h4>
                        <Badge :value="model.total_hours + 'h'" class="!bg-blue-50 !text-blue-600 !font-black !px-4" />
                    </div>
                    <div class="flex gap-1.5 mb-6">
                        <div v-for="day in weekDays" :key="day.key" class="flex-1 text-center p-2 rounded-xl border border-slate-50 bg-slate-50/50">
                            <p class="text-[9px] font-black text-slate-400 uppercase mb-1">{{ day.label }}</p>
                            <p class="text-xs font-black text-slate-700">{{ Number(model[day.key]) }}</p>
                        </div>
                    </div>
                    <div class="flex justify-between items-center pt-4 border-t border-slate-50">
                        <div class="flex items-center gap-2 text-slate-400 font-bold text-xs uppercase">
                            <User class="w-4 h-4 text-blue-500" /> {{ model.planning_assignments_count }} Agents
                        </div>
                        <Button @click="openEditModal(model)" class="!bg-slate-900 !p-3 !rounded-xl"><Edit2 class="w-4 h-4 text-white" /></Button>
                    </div>
                </div>
            </div>

            <!-- Affectations Actives -->
            <div class="col-span-7 bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-sm h-fit">
                <h3 class="text-sm font-black text-slate-800 mb-6 uppercase tracking-widest flex items-center gap-3">
                    <span class="w-2 h-6 bg-blue-600 rounded-full"></span> Affectations Actives
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div v-for="assign in activeAssignments" :key="assign.id" class="p-5 border border-slate-100 rounded-2xl flex items-center gap-4 bg-slate-50/30">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm text-blue-600"><Calendar class="w-5 h-5" /></div>
                        <div>
                            <p class="font-black text-slate-800 text-sm truncate">{{ assign.user_name }}</p>
                            <p class="text-[10px] font-bold text-blue-600 uppercase">{{ assign.model_name }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODALE RESSERRÉE ET DENSITE AUGMENTÉE -->
        <Dialog v-model:visible="showModal" modal header=" " :style="{ width: '38rem' }"
            :pt="{
                root: { class: '!rounded-[3rem] !bg-white !border-none !shadow-2xl' },
                content: { class: '!p-12' }
            }">

            <div class="mb-10 flex justify-between items-center">
                <div class="flex items-center gap-5">
                    <div class="p-4 bg-blue-600 rounded-2xl shadow-xl shadow-blue-100">
                        <Clock class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-slate-800 tracking-tight">{{ isEditing ? 'Édition modèle' : 'Nouveau modèle' }}</h3>
                        <p class="text-[11px] font-bold text-blue-500 uppercase tracking-widest">Configuration hebdomadaire</p>
                    </div>
                </div>
                <Button @click="showModal = false" text class="!p-2 hover:!bg-red-50 !rounded-full group transition-all">
                    <X class="w-6 h-6 text-slate-300 group-hover:text-red-500" />
                </Button>
            </div>

            <form @submit.prevent="submit" class="space-y-10">
                <div class="space-y-3">
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom du modèle</label>
                    <InputText v-model="form.name" class="!w-full !rounded-2xl !py-5 !px-6 !bg-slate-50 !border-none !text-base !font-black focus:!ring-2 !ring-blue-500" placeholder="Ex: Temps Plein 35h" />
                </div>

                <div class="bg-slate-50 p-8 rounded-[2rem]">
                    <div class="grid grid-cols-7 gap-3">
                        <div v-for="day in weekDays" :key="day.key" class="flex flex-col gap-3">
                            <span class="text-[10px] font-black text-center text-blue-600 uppercase tracking-tighter">{{ day.label }}</span>
                            <InputNumber v-model="form[day.key]" :min="0" :max="24"
                                inputClass="!w-full !text-center !py-4 !rounded-xl !bg-white !border-none !shadow-sm !text-sm !font-black text-slate-800" />
                        </div>
                    </div>
                </div>

                <div class="bg-slate-900 p-10 rounded-[2.5rem] flex justify-between items-center text-white">
                    <div class="flex items-center gap-5">
                        <div class="p-4 bg-white/10 rounded-2xl border border-white/10"><Calendar class="w-7 h-7" /></div>
                        <span class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Total semaine</span>
                    </div>
                    <div class="text-5xl font-black tabular-nums">{{ autoTotal }}<span class="text-xl ml-2 text-blue-500 italic">h</span></div>
                </div>

                <div class="flex gap-4">
                    <Button label="Annuler" @click="showModal = false" text class="flex-1 !py-5 !rounded-2xl !text-sm !font-bold !text-slate-400" />
                    <Button type="submit" :loading="form.processing" class="flex-1 !bg-blue-600 !border-none !py-5 !rounded-2xl !text-sm !font-black !text-white !shadow-2xl !shadow-blue-200">
                        {{ isEditing ? 'Mettre à jour' : 'Enregistrer le modèle' }}
                    </Button>
                </div>
            </form>
        </Dialog>
    </AppLayout>
</template>
