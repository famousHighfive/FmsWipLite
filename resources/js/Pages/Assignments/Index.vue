<script setup>
// Importation des composants PrimeVue nécessaires pour l'interface
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import Dropdown from 'primevue/dropdown';
import Tag from 'primevue/tag';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import { ref, computed } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

// Récupération des données envoyées par le contrôleur Laravel
const props = defineProps({
    assignments: Array, // Liste des affectations actives
    employees: Array,   // Liste des employés disponibles
    campaigns: Array,   // Liste des campagnes actives
    positions: Array    // Liste des postes (CP, SUP, TC)
});
console.log(props.employees);
// Variables d'état pour la gestion des modaux (dialogues)
const toast = useToast();
const confirm = useConfirm();
const assignmentDialog = ref(false); // Modal de nouvelle affectation
const releaseDialog = ref(false);    // Modal de libération/transfert
const submitted = ref(false);

// --- FILTRAGE PRIME VUE AUTOMATIQUE ---
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

// Objet pour stocker les données du formulaire de nouvelle affectation
const newAssignment = ref({
    employee_id: null,
    campaign_id: null,
    manager_id: null,
    position_id: null,
    start_date: new Date().toISOString().substr(0, 10) // Date du jour par défaut
});

// Objet pour stocker les données de libération d'une ressource
const selectedAssignment = ref(null);
const releaseData = ref({
    mode: 'solo', // Valeurs possibles : 'solo', 'cascade', 'transfer'
    new_manager_id: null,
    reason: ''
});

// --- LOGIQUE DE FILTRAGE ET RECHERCHE ---

// Liste des managers potentiels (Employés qui ont un poste de CP ou SUP)
const potentialManagers = computed(() => {
    return props.employees.filter(emp => {
        // On cherche le code du poste de l'employé pour savoir s'il peut être manager
        const pos = props.positions.find(p => p.id === emp.position_id);
        return pos && (pos.code === 'CP' || pos.code === 'SUP');
    });
});

// --- ACTIONS ---

/**
 * Ouvre le modal pour créer une nouvelle affectation
 */
const openNew = () => {
    newAssignment.value = {
        employee_id: null,
        campaign_id: null,
        manager_id: null,
        position_id: null,
        start_date: new Date().toISOString().substr(0, 10)
    };
    submitted.value = false;
    assignmentDialog.value = true;
};

/**
 * Enregistre une nouvelle affectation via Inertia
 */
const saveAssignment = () => {
    submitted.value = true;
    
    // Vérification basique des champs obligatoires
    if (!newAssignment.value.employee_id || !newAssignment.value.campaign_id || !newAssignment.value.position_id) {
        return;
    }

    router.post('/assignments', newAssignment.value, {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Succès', detail: 'Affectation réussie', life: 3000 });
            assignmentDialog.value = false;
        }
    });
};

/**
 * Ouvre le modal de libération pour une affectation spécifique
 */
const confirmRelease = (data) => {
    confirm.require({
        message: `Voulez-vous vraiment libérer ${data.employee.first_name} ${data.employee.last_name} de la campagne ${data.campaign.name} ?`,
        header: 'Confirmation de libération',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Annuler',
            severity: 'secondary',
            variant: 'text'
        },
        acceptProps: {
            label: 'Procéder',
            severity: 'danger'
        },
        accept: () => {
            selectedAssignment.value = data;
            releaseData.value = { mode: 'solo', new_manager_id: null, reason: '' };
            releaseDialog.value = true;
        }
    });
};

/**
 * Exécute la libération (solo, cascade ou transfert) via Inertia
 */
const executeRelease = () => {
    if (releaseData.value.mode === 'transfer' && !releaseData.value.new_manager_id) {
        toast.add({ severity: 'error', summary: 'Erreur', detail: 'Veuillez choisir un nouveau manager pour le transfert', life: 3000 });
        return;
    }

    router.post(`/assignments/${selectedAssignment.value.id}/release`, releaseData.value, {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Succès', detail: 'Action effectuée avec succès', life: 3000 });
            releaseDialog.value = false;
        }
    });
};

/**
 * Détermine la couleur du tag en fonction du poste
 */
const getPositionSeverity = (code) => {
    switch (code) {
        case 'CP': return 'info';
        case 'SUP': return 'warn';
        case 'TC': return 'success';
        default: return 'secondary';
    }
};

</script>

<template>
    <AppLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight">Gestion des Affectations</h2>
                    <p class="text-slate-500 font-medium">Gérez l'organisation hiérarchique de vos équipes par campagne.</p>
                </div>
                <Button label="Nouvelle Affectation" icon="pi pi-plus" class="!bg-gradient-to-r !from-blue-600 !to-indigo-600 !border-0 !rounded-2xl !px-6 !py-3 !font-black !text-xs !uppercase !tracking-widest !text-white shadow-xl shadow-blue-500/20 hover:-translate-y-0.5 transition-all" @click="openNew" />
            </div>

            <div class="card !border-0 !shadow-2xl !shadow-slate-200/50 !rounded-[2.5rem] overflow-hidden bg-white/70 backdrop-blur-md border border-white/50">
                <!-- Tableau des affectations actives -->
                <DataTable 
                    :value="props.assignments" 
                    v-model:filters="filters"
                    :globalFilterFields="['employee.first_name', 'employee.last_name', 'employee.matricule', 'campaign.name', 'position.name', 'manager.first_name', 'manager.last_name']"
                    paginator :rows="10" 
                    class="p-datatable-sm"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    currentPageReportTemplate="Affichage de {first} à {last} sur {totalRecords}">
                    <template #header>
                        <div class="flex justify-between items-center py-4 px-6 bg-white/30">
                            <span class="text-slate-800 font-black uppercase tracking-widest text-xs">Ressources affectées</span>
                            <IconField iconPosition="left">
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText v-model="filters['global'].value" placeholder="Recherche automatique..." class="!rounded-2xl !bg-white/80 !border-slate-100 !text-sm focus:!ring-2 focus:!ring-blue-500/20" />
                            </IconField>
                        </div>
                    </template>

                    <Column header="Employé">
                        <template #body="slotProps">
                            <div class="font-bold">{{ slotProps.data.employee.first_name }} {{ slotProps.data.employee.last_name }}</div>
                            <small class="text-gray-500">{{ slotProps.data.employee.matricule }}</small>
                        </template>
                    </Column>

                    <Column header="Poste">
                        <template #body="slotProps">
                            <Tag :value="slotProps.data.position.name" :severity="getPositionSeverity(slotProps.data.position.code)" />
                        </template>
                    </Column>

                    <Column field="campaign.name" header="Campagne" sortable />

                    <Column header="Manager Direct">
                        <template #body="slotProps">
                            <span v-if="slotProps.data.manager">
                                {{ slotProps.data.manager.first_name }} {{ slotProps.data.manager.last_name }}
                            </span>
                            <span v-else class="text-gray-400 italic">Aucun</span>
                        </template>
                    </Column>

                    <Column field="start_date" header="Depuis le" sortable />

                    <Column header="Actions" bodyStyle="text-align: center">
                        <template #body="slotProps">
                            <Button icon="pi pi-sign-out" severity="danger" text rounded v-tooltip.top="'Libérer / Transférer'" @click="confirmRelease(slotProps.data)" />
                        </template>
                    </Column>
                </DataTable>
            </div>

            <!-- MODAL DE NOUVELLE AFFECTATION -->
            <Dialog v-model:visible="assignmentDialog" header="Nouvelle Affectation" :style="{ width: '550px' }" modal class="!rounded-[2.5rem] !bg-white/90 !backdrop-blur-xl !border-white/50 !shadow-2xl">
                <div class="flex flex-col gap-6 pt-4">
                    <!-- Choix de l'employé -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Employé</label>
                        <Dropdown v-model="newAssignment.employee_id" :options="props.employees" optionLabel="email" optionValue="id" filter placeholder="Choisir un employé" class="!rounded-xl !bg-white/50 !border-slate-100 !text-sm" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Choix de la campagne -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Campagne</label>
                            <Dropdown v-model="newAssignment.campaign_id" :options="props.campaigns" optionLabel="name" optionValue="id" placeholder="Choisir une campagne" class="!rounded-xl !bg-white/50 !border-slate-100 !text-sm" />
                        </div>

                        <!-- Choix du poste dans la campagne -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Poste occupé</label>
                            <Dropdown v-model="newAssignment.position_id" :options="props.positions" optionLabel="name" optionValue="id" placeholder="Définir le rôle" class="!rounded-xl !bg-white/50 !border-slate-100 !text-sm" />
                        </div>
                    </div>

                    <!-- Choix du manager (facultatif) -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Manager Direct (Optionnel)</label>
                        <Dropdown v-model="newAssignment.manager_id" :options="potentialManagers" optionLabel="email" optionValue="id" filter showClear placeholder="Aucun manager" class="!rounded-xl !bg-white/50 !border-slate-100 !text-sm" />
                    </div>

                    <!-- Date de début -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Date de début</label>
                        <InputText type="date" v-model="newAssignment.start_date" class="!rounded-xl !bg-white/50 !border-slate-100 !text-sm" />
                    </div>
                </div>

                <template #footer>
                    <div class="flex gap-3 mt-4 w-full">
                        <Button label="Annuler" class="flex-1 !bg-white !text-slate-500 !border-slate-100 !rounded-xl !py-3 !font-bold" @click="assignmentDialog = false" />
                        <Button label="Confirmer l'affectation" icon="pi pi-check" class="flex-1 !bg-gradient-to-r !from-blue-600 !to-indigo-600 !border-0 !rounded-xl !py-3 !font-bold !text-white shadow-lg shadow-blue-500/20" @click="saveAssignment" />
                    </div>
                </template>
            </Dialog>

            <!-- MODAL DE LIBÉRATION / TRANSFERT (Le cœur de la logique demandée) -->
            <Dialog v-model:visible="releaseDialog" header="Libérer une ressource" :style="{ width: '550px' }" modal class="!rounded-[2.5rem] !bg-white/90 !backdrop-blur-xl !border-white/50 !shadow-2xl">
                <div v-if="selectedAssignment" class="flex flex-col gap-6 pt-4">
                    <div class="bg-blue-50/50 p-4 rounded-2xl text-blue-800 text-sm border border-blue-100 font-medium">
                        Vous allez libérer <b class="text-blue-600">{{ selectedAssignment.employee.first_name }} {{ selectedAssignment.employee.last_name }}</b> de la campagne <b class="text-blue-600">{{ selectedAssignment.campaign.name }}</b>.
                    </div>

                    <!-- Options de libération -->
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Comment gérer ses subordonnés ?</label>
                        
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center gap-4 border border-slate-100 p-4 rounded-2xl cursor-pointer hover:bg-white/50 transition-all" @click="releaseData.mode = 'solo'" :class="{'!border-blue-500 !bg-blue-50/50': releaseData.mode === 'solo'}">
                                <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm">
                                    <i class="pi pi-user text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="font-black text-slate-800 text-sm">Libération seule</div>
                                    <div class="text-[10px] text-slate-500 font-medium uppercase tracking-tight">Ses subordonnés restent sans manager direct.</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 border border-slate-100 p-4 rounded-2xl cursor-pointer hover:bg-white/50 transition-all" @click="releaseData.mode = 'cascade'" :class="{'!border-rose-500 !bg-rose-50/50': releaseData.mode === 'cascade'}">
                                <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm">
                                    <i class="pi pi-users text-rose-600"></i>
                                </div>
                                <div>
                                    <div class="font-black text-rose-700 text-sm">Libération en cascade</div>
                                    <div class="text-[10px] text-rose-500 font-medium uppercase tracking-tight">Toute sa chaîne (SUP et TC) est libérée.</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 border border-slate-100 p-4 rounded-2xl cursor-pointer hover:bg-white/50 transition-all" @click="releaseData.mode = 'transfer'" :class="{'!border-emerald-500 !bg-emerald-50/50': releaseData.mode === 'transfer'}">
                                <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm">
                                    <i class="pi pi-sync text-emerald-600"></i>
                                </div>
                                <div>
                                    <div class="font-black text-emerald-700 text-sm">Transfert de chaîne</div>
                                    <div class="text-[10px] text-emerald-500 font-medium uppercase tracking-tight">Toute sa chaîne est rattachée à un remplaçant.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Choix du nouveau manager si transfert -->
                    <div v-if="releaseData.mode === 'transfer'" class="space-y-2">
                        <label class="text-[10px] font-black text-emerald-600 uppercase tracking-widest ml-1">Choisir le nouveau responsable</label>
                        <Dropdown v-model="releaseData.new_manager_id" :options="potentialManagers.filter(m => m.id !== selectedAssignment.employee_id)" optionLabel="email" optionValue="id" filter placeholder="Sélectionner le remplaçant" class="!rounded-xl !bg-white/50 !border-slate-100 !text-sm" />
                    </div>

                    <!-- Raison du départ -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Motif / Commentaire</label>
                        <InputText v-model="releaseData.reason" placeholder="Ex: Fin de contrat, mutation..." class="!rounded-xl !bg-white/50 !border-slate-100 !text-sm" />
                    </div>
                </div>

                <template #footer>
                    <div class="flex gap-3 mt-4 w-full">
                        <Button label="Annuler" class="flex-1 !bg-white !text-slate-500 !border-slate-100 !rounded-xl !py-3 !font-bold" @click="releaseDialog = false" />
                        <Button label="Valider l'action" icon="pi pi-check" class="flex-1 !border-0 !rounded-xl !py-3 !font-bold !text-white shadow-lg" :class="releaseData.mode === 'cascade' ? '!bg-rose-600 shadow-rose-500/20' : '!bg-blue-600 shadow-blue-500/20'" @click="executeRelease" />
                    </div>
                </template>
            </Dialog>
        </div>
    </AppLayout>
</template>
