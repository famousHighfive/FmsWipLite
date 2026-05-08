<script setup>
import { ref } from "vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Button from "primevue/button";
import Tag from "primevue/tag";
import Dialog from "primevue/dialog";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Select from "primevue/select";
import DatePicker from "primevue/datepicker";
import { UserPlus, Clock, Eye, Calendar, CheckCircle, AlertCircle, PauseCircle, XCircle, Megaphone } from "lucide-vue-next";

const props = defineProps({
    supervisorAssignments: Array,
    campaigns: Array,
    chefsDePlateau: Array,
    supPositionId: Number,
});

const selectedSupervisor = ref(null);
const showTeleconseillersModal = ref(false);
const showCampaignModal = ref(false);

const campaignForm = useForm({
    employee_id: null,
    campaign_id: null,
    manager_id: null,
    position_id: props.supPositionId,
    start_date: new Date(),
});

const openCampaignModal = (supervisor) => {
    campaignForm.employee_id = supervisor.id;
    selectedSupervisor.value = supervisor;
    showCampaignModal.value = true;
};

const submitCampaignAssignment = () => {
    campaignForm.post(route('assignments.store'), {
        onSuccess: () => {
            showCampaignModal.value = false;
            campaignForm.reset();
        }
    });
};

const getStatusLabel = (status) => {
    switch (status) {
        case 'validé': return 'success';
        case 'en attente': return 'warning';
        case 'suspendu': return 'danger';
        case 'terminé': return 'info';
        default: return 'secondary';
    }
};

const viewTeleconseillers = (supervisorData) => {
    selectedSupervisor.value = supervisorData;
    showTeleconseillersModal.value = true;
};

const validateAssignment = (id) => {
    router.post(route('planning.assignments.validate', id));
};

const suspendAssignment = (id) => {
    router.post(route('planning.assignments.suspend', id));
};

const terminateAssignment = (id) => {
    router.post(route('planning.assignments.terminate', id));
};
</script>

<template>
    <Head title="Affectations des Plannings" />

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-black text-slate-800">Affectations</h2>
                    <p class="text-slate-500 text-sm">Gérez les plannings des superviseurs et leurs équipes</p>
                </div>

                <Link :href="route('planning.assignments.create')">
                    <Button class="!bg-blue-600 !border-none !rounded-xl !px-6 flex items-center gap-2">
                        <UserPlus class="w-4 h-4 text-white" />
                        <span class="font-bold text-white">Nouvelle Affectation</span>
                    </Button>
                </Link>
            </div>
        </template>

        <div class="py-8 space-y-4">
            <div v-for="item in supervisorAssignments" :key="item.supervisor.id" class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 font-bold text-sm">
                            {{ item.supervisor.name.substring(0, 2).toUpperCase() }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-slate-800">{{ item.supervisor.name }}</h3>
                                <div v-if="item.supervisor.has_campaign" class="flex items-center gap-1.5 bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full">
                                    <Megaphone class="w-3 h-3" />
                                    <span class="text-[10px] font-black">{{ item.supervisor.campaign_name }}</span>
                                </div>
                                <div v-if="item.assignments.length > 0" class="flex items-center gap-1.5 bg-green-50 text-green-600 px-2 py-0.5 rounded-full">
                                    <CheckCircle class="w-3 h-3" />
                                    <span class="text-[10px] font-black">Planning assigné</span>
                                </div>
                                <div v-else class="flex items-center gap-1.5 bg-amber-50 text-amber-600 px-2 py-0.5 rounded-full">
                                    <AlertCircle class="w-3 h-3" />
                                    <span class="text-[10px] font-black">Aucun planning</span>
                                </div>
                            </div>
                            <p class="text-xs text-slate-400 uppercase font-black">Superviseur</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <Button v-if="!item.supervisor.has_campaign" @click="openCampaignModal(item.supervisor)" class="!bg-indigo-600 !text-white !border-none !rounded-xl !px-4">
                            <Megaphone class="w-4 h-4 mr-2" />
                            Affecter Campagne
                        </Button>
                        <Button @click="viewTeleconseillers(item)" class="!bg-slate-100 !text-slate-700 !border-none !rounded-xl !px-4">
                            <Eye class="w-4 h-4 mr-2" />
                            Voir l'équipe
                        </Button>
                    </div>
                </div>

                <div v-if="item.assignments.length > 0" class="space-y-3">
                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest">Plannings du superviseur</h4>
                    <div v-for="assignment in item.assignments" :key="assignment.id" class="flex items-center justify-between bg-slate-50 rounded-xl p-4">
                        <div class="flex items-center gap-4">
                            <Clock class="w-5 h-5 text-blue-500" />
                            <div>
                                <p class="font-bold text-slate-700">{{ assignment.model.name }}</p>
                                <p class="text-xs text-slate-500 flex items-center gap-1">
                                    <Calendar class="w-3 h-3" />
                                    Du {{ assignment.start_date }} au {{ assignment.end_date }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <Tag :severity="getStatusLabel(assignment.status)" :value="assignment.status.toUpperCase()" class="!text-[10px] !font-black !px-3" />
                            <div class="flex items-center gap-1.5">
                                <Button v-if="assignment.status === 'en attente'" @click="validateAssignment(assignment.id)" class="!bg-green-600 !border-none !text-white !px-2.5 !py-1.5 !text-[10px] !font-bold !rounded-lg flex items-center gap-1">
                                    <CheckCircle class="w-3 h-3" />
                                    Valider
                                </Button>
                                <Button v-if="['validé', 'en attente'].includes(assignment.status)" @click="suspendAssignment(assignment.id)" class="!bg-amber-600 !border-none !text-white !px-2.5 !py-1.5 !text-[10px] !font-bold !rounded-lg flex items-center gap-1">
                                    <PauseCircle class="w-3 h-3" />
                                    Suspendre
                                </Button>
                                <Button v-if="['validé', 'suspendu', 'en attente'].includes(assignment.status)" @click="terminateAssignment(assignment.id)" class="!bg-red-600 !border-none !text-white !px-2.5 !py-1.5 !text-[10px] !font-bold !rounded-lg flex items-center gap-1">
                                    <XCircle class="w-3 h-3" />
                                    Terminer
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-8 text-slate-400">
                    <AlertCircle class="w-8 h-8 mx-auto mb-2 text-slate-200" />
                    Aucun planning affecté
                </div>
            </div>
        </div>

        <Dialog v-model:visible="showTeleconseillersModal" header="Téléconseillers de l'équipe" :style="{ width: '60rem' }">
            <div v-if="selectedSupervisor" class="space-y-4">
                <h3 class="text-lg font-bold text-slate-800">Équipe de {{ selectedSupervisor.supervisor.name }}</h3>
                <DataTable :value="selectedSupervisor.teleconseillers" class="p-datatable-sm">
                    <Column field="employee.name" header="Téléconseiller">
                        <template #body="{ data }">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-green-600 font-bold text-xs">
                                    {{ data.employee.name.substring(0, 2).toUpperCase() }}
                                </div>
                                <div>
                                    <span class="font-bold text-slate-700">{{ data.employee.name }}</span>
                                    <span class="text-[10px] text-slate-400 uppercase font-black ml-2">{{ data.employee.role }}</span>
                                </div>
                            </div>
                        </template>
                    </Column>
                    <Column field="model.name" header="Planning">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2 text-blue-600 font-semibold">
                                <Clock class="w-4 h-4" />
                                {{ data.model.name }}
                            </div>
                        </template>
                    </Column>
                    <Column header="Période">
                        <template #body="{ data }">
                            <span class="text-xs text-slate-500 flex items-center gap-1">
                                <Calendar class="w-3 h-3" />
                                Du {{ data.start_date }} au {{ data.end_date }}
                            </span>
                        </template>
                    </Column>
                    <Column field="status" header="Statut">
                        <template #body="{ data }">
                            <Tag :severity="getStatusLabel(data.status)" :value="data.status.toUpperCase()" class="!text-[10px] !font-black !px-3" />
                        </template>
                    </Column>
                    <Column header="Actions" headerStyle="width: 15rem">
                        <template #body="{ data }">
                            <div class="flex items-center gap-1.5">
                                <Button v-if="data.status === 'en attente'" @click="validateAssignment(data.id)" class="!bg-green-600 !border-none !text-white !px-2.5 !py-1.5 !text-[10px] !font-bold !rounded-lg flex items-center gap-1">
                                    <CheckCircle class="w-3 h-3" />
                                    Valider
                                </Button>
                                <Button v-if="['validé', 'en attente'].includes(data.status)" @click="suspendAssignment(data.id)" class="!bg-amber-600 !border-none !text-white !px-2.5 !py-1.5 !text-[10px] !font-bold !rounded-lg flex items-center gap-1">
                                    <PauseCircle class="w-3 h-3" />
                                    Suspendre
                                </Button>
                                <Button v-if="['validé', 'suspendu', 'en attente'].includes(data.status)" @click="terminateAssignment(data.id)" class="!bg-red-600 !border-none !text-white !px-2.5 !py-1.5 !text-[10px] !font-bold !rounded-lg flex items-center gap-1">
                                    <XCircle class="w-3 h-3" />
                                    Terminer
                                </Button>
                            </div>
                        </template>
                    </Column>
                </DataTable>
                <div v-if="selectedSupervisor.teleconseillers.length === 0" class="text-center py-12 text-slate-400">
                    <AlertCircle class="w-12 h-12 mx-auto mb-4 text-slate-200" />
                    Aucun téléconseiller dans cette équipe
                </div>
            </div>
        </Dialog>

        <!-- MODAL D'AFFECTATION CAMPAGNE -->
        <Dialog v-model:visible="showCampaignModal" header="Affecter à une campagne" :style="{ width: '30rem' }" modal>
            <form @submit.prevent="submitCampaignAssignment" class="space-y-6 pt-4">
                <div v-if="selectedSupervisor" class="bg-blue-50 p-4 rounded-xl flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                        {{ selectedSupervisor.name.substring(0, 2).toUpperCase() }}
                    </div>
                    <div>
                        <p class="text-xs font-black text-blue-600 uppercase tracking-widest">Superviseur</p>
                        <p class="font-bold text-slate-800">{{ selectedSupervisor.name }}</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Campagne</label>
                    <Select v-model="campaignForm.campaign_id" :options="campaigns" optionLabel="name" optionValue="id" placeholder="Choisir une campagne" class="w-full !rounded-xl !bg-slate-50 !border-slate-100" />
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Chef de Plateau (Responsable)</label>
                    <Select v-model="campaignForm.manager_id" :options="chefsDePlateau" optionLabel="user.name" optionValue="id" placeholder="Choisir un CP" class="w-full !rounded-xl !bg-slate-50 !border-slate-100" />
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Date de début</label>
                    <DatePicker v-model="campaignForm.start_date" class="w-full" inputClass="!rounded-xl !bg-slate-50 !border-slate-100" />
                </div>

                <div class="flex gap-3 pt-2">
                    <Button label="Annuler" text @click="showCampaignModal = false" class="flex-1 !py-3 !rounded-xl" />
                    <Button type="submit" label="Confirmer l'affectation" :loading="campaignForm.processing" class="flex-1 !bg-blue-600 !border-none !py-3 !rounded-xl !font-bold" />
                </div>
            </form>
        </Dialog>
    </AppLayout>
</template>
