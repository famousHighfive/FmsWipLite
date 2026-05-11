<script setup>
// Importation des composants PrimeVue
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import Dialog from 'primevue/dialog';
import Textarea from 'primevue/textarea';
import Menu from 'primevue/menu';
import SelectButton from 'primevue/selectbutton';
import ToggleSwitch from 'primevue/toggleswitch';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import Select from 'primevue/select';
import Message from 'primevue/message';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

// Props reçues du contrôleur Laravel
const props = defineProps({
    campaigns: Array, // Liste des campagnes avec assignment_count
});

// --- FILTRAGE AUTOMATIQUE PRIME VUE ---
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const toast = useToast();
const confirm = useConfirm();
const dt = ref();

// États pour les modaux
const campaignDialog = ref(false);
const deleteDialog = ref(false);
const selectedCampaign = ref(null);
const campaign = ref({ status: 'inactive' });
const submitted = ref(false);

// Menu contextuel
const menu = ref();
const menuItems = ref([]);

// Filtres
const statusFilter = ref('Tous');
const statusOptions = ref(['Tous', 'Active', 'Inactive', 'Terminée']);
const showClosed = ref(false);

/**
 * Filtrage des campagnes selon la recherche, le statut et l'option d'affichage des clôturées
 */
const filteredCampaigns = computed(() => {
    return props.campaigns.filter(c => {
        const matchesStatus = statusFilter.value === 'Tous' || c.status.toLowerCase() === statusFilter.value.toLowerCase();
        return matchesStatus;
    });
});

/**
 * Gestion du menu contextuel par ligne
 */
const toggleMenu = (event, data) => {
    event.stopPropagation(); // Empêche le clic sur la ligne
    selectedCampaign.value = data;
    
    const items = [
        { label: 'Voir le détail', icon: 'pi pi-eye', command: () => router.get(`/campaigns/${data.id}`) },
        { label: 'Modifier', icon: 'pi pi-pencil', command: () => editCampaign(data) }
    ];

    // On n'ajoute le bouton de changement de statut que si la campagne n'est pas terminée
    if (data.status !== 'terminee') {
        const nextStatus = data.status === 'active' ? 'inactive' : 'active';
        const statusLabel = data.status === 'active' ? 'Désactiver' : 'Activer';
        items.push({ 
            label: statusLabel, 
            icon: data.status === 'active' ? 'pi pi-power-off' : 'pi pi-play', 
            command: () => toggleStatus(data, nextStatus) 
        });
    }

    // Bouton de clôture (si non terminée)
    items.push({ separator: true });
    if (data.status !== 'terminee') {
        items.push({ 
            label: 'Clôturer', 
            icon: 'pi pi-times-circle', 
            class: 'text-red-500', 
            command: () => confirmClose(data) 
        });
    }

    menuItems.value = items;
    menu.value.toggle(event);
};

/**
 * Basculer le statut d'une campagne
 */
const toggleStatus = (data, nextStatus) => {
    router.patch(`/campaigns/${data.id}/status`, { status: nextStatus }, {
        onSuccess: () => {
            toast.add({ 
                severity: 'success', 
                summary: 'Statut mis à jour', 
                detail: `La campagne est maintenant ${nextStatus}`, 
                life: 3000 
            });
        }
    });
};

/**
 * Navigation vers la page de détail au clic sur une ligne
 */
const onRowClick = (event) => {
    // Si on a cliqué sur un bouton ou un menu, on ne navigue pas
    if (event.originalEvent.target.closest('button') || event.originalEvent.target.closest('.p-menu')) {
        return;
    }
    router.get(`/campaigns/${event.data.id}`);
};

// Fonctions CRUD 
const openNew = () => {
    campaign.value = { status: 'inactive' };
    submitted.value = false;
    campaignDialog.value = true;
};

const editCampaign = (data) => {
    // Formatage des dates pour l'input date
    const formatted = { ...data };
    if (data.start_date) {
        const p = data.start_date.split('/');
        if (p.length === 3) formatted.start_date = `${p[2]}-${p[1]}-${p[0]}`;
    }
    if (data.end_date) {
        const p = data.end_date.split('/');
        if (p.length === 3) formatted.end_date = `${p[2]}-${p[1]}-${p[0]}`;
    }
    campaign.value = formatted;
    campaignDialog.value = true;
};

const saveCampaign = () => {
    submitted.value = true;
    if (!campaign.value.name) return;

    if (campaign.value.id) {
        router.put(`/campaigns/${campaign.value.id}`, campaign.value, {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Succès', detail: 'Campagne mise à jour', life: 3000 });
                campaignDialog.value = false;
            }
        });
    } else {
        router.post('/campaigns', campaign.value, {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Succès', detail: 'Campagne créée', life: 3000 });
                campaignDialog.value = false;
            }
        });
    }
};

const confirmDelete = (data) => {
    confirm.require({
        message: `Voulez-vous vraiment clôturer la campagne "${data.name}" ?`,
        header: 'Confirmation de clôture',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Annuler',
            severity: 'secondary',
            variant: 'text'
        },
        acceptProps: {
            label: 'Confirmer la clôture',
            severity: 'danger'
        },
        accept: () => {
            router.delete(`/campaigns/${data.id}`, {
                onSuccess: () => {
                    toast.add({ 
                        severity: 'success', 
                        summary: 'Succès', 
                        detail: 'Campagne clôturée avec succès', 
                        life: 3000 
                    });
                }
            });
        }
    });
};

const getStatusSeverity = (status) => {
    switch (status.toLowerCase()) {
        case 'active': return 'success';
        case 'inactive': return 'warn';
        case 'terminee': return 'secondary';
        default: return 'info';
    }
};
</script>

<template>
    <AppLayout>
        <div class="space-y-6">
            <!-- HEADER -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-black text-slate-800 tracking-tight">Gestion des Campagnes</h1>
                    <p class="text-slate-500 font-medium">{{ props.campaigns.length }} campagnes configurées</p>
                </div>
                <Button label="Nouvelle campagne" icon="pi pi-plus" class="!bg-gradient-to-r !from-blue-600 !to-indigo-600 !border-0 !rounded-2xl !px-6 !py-3 !font-black !text-xs !uppercase !tracking-widest !text-white shadow-xl shadow-blue-500/20 hover:-translate-y-0.5 transition-all" @click="openNew" />
            </div>

            <!-- FILTRES -->
            <div class="flex flex-wrap gap-4 items-center">
                <IconField class="flex-1 min-w-[300px]">
                    <InputIcon>
                        <i class="pi pi-search" />
                    </InputIcon>
                    <InputText v-model="filters['global'].value" placeholder="Recherche automatique..." class="w-full !rounded-2xl !bg-white/70 !backdrop-blur-md !border-white/50 !text-sm focus:!ring-2 focus:!ring-blue-500/20" />
                </IconField>
                <SelectButton v-model="statusFilter" :options="statusOptions" class="!rounded-2xl overflow-hidden !border-white/50 !bg-white/50 !backdrop-blur-md" />
            </div>

            <div class="card !border-0 !shadow-2xl !shadow-slate-200/50 !rounded-[2.5rem] overflow-hidden bg-white/70 backdrop-blur-md border border-white/50">
                <DataTable 
                    :value="filteredCampaigns" 
                    v-model:filters="filters"
                    :globalFilterFields="['name', 'description', 'status']"
                    @row-click="onRowClick" 
                    class="p-datatable-sm cursor-pointer"
                    paginator :rows="10"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    currentPageReportTemplate="Affichage de {first} à {last} sur {totalRecords}">
                    <template #header>
                        <div class="flex justify-between items-center py-4 px-6 bg-white/30">
                            <span class="text-slate-800 font-black uppercase tracking-widest text-xs">Liste des campagnes</span>
                        </div>
                    </template>
                    <Column field="name" header="Nom" sortable>
                        <template #body="slotProps">
                            <span class="font-bold text-slate-900 hover:text-blue-600 transition-colors">
                                {{ slotProps.data.name }}
                            </span>
                        </template>
                    </Column>
                    
                    <Column field="description" header="Description">
                        <template #body="slotProps">
                            <span class="text-slate-500 truncate block max-w-xs">{{ slotProps.data.description }}</span>
                        </template>
                    </Column>

                    <Column field="start_date" header="Début" sortable />
                    <Column field="end_date" header="Fin" sortable />

                    <Column header="Statut">
                        <template #body="slotProps">
                            <Tag :value="slotProps.data.status" :severity="getStatusSeverity(slotProps.data.status)" rounded />
                        </template>
                    </Column>

                    <Column header="Affectations">
                        <template #body="slotProps">
                            <div class="flex items-center gap-2 text-slate-600">
                                <i class="pi pi-users text-sm" />
                                <span>{{ slotProps.data.assignments_count || 0 }}</span>
                            </div>
                        </template>
                    </Column>

                    <Column header="Actions" class="w-20">
                        <template #body="slotProps">
                            <Button icon="pi pi-ellipsis-v" severity="secondary" text rounded @click="toggleMenu($event, slotProps.data)" />
                        </template>
                    </Column>
                </DataTable>
                <Menu ref="menu" :model="menuItems" :popup="true" />
            </div>

            <!-- MODAL CRÉATION/EDITION -->
            <Dialog v-model:visible="campaignDialog" :header="campaign.id ? 'Modifier la campagne' : 'Nouvelle campagne'" modal class="!rounded-[2.5rem] !bg-white/90 !backdrop-blur-xl !border-white/50 !shadow-2xl" :style="{width: '550px'}">
                <div class="flex flex-col gap-6 pt-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom de la campagne *</label>
                        <InputText v-model="campaign.name" placeholder="Ex: Campagne Highfive" class="!rounded-xl !bg-white/50 !border-slate-100 !text-sm focus:!ring-2 focus:!ring-blue-500/20" :class="{'p-invalid': submitted && !campaign.name}" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Description</label>
                        <Textarea v-model="campaign.description" rows="3" placeholder="Objectifs et détails..." class="!rounded-xl !bg-white/50 !border-slate-100 !text-sm focus:!ring-2 focus:!ring-blue-500/20" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Date début *</label>
                            <InputText type="date" v-model="campaign.start_date" class="!rounded-xl !bg-white/50 !border-slate-100 !text-sm" />
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Date fin</label>
                            <InputText type="date" v-model="campaign.end_date" class="!rounded-xl !bg-white/50 !border-slate-100 !text-sm" />
                        </div>
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Statut de la campagne</label>
                        <div class="flex gap-3">
                            <Button label="Active" :outlined="campaign.status !== 'active'" class="flex-1 !rounded-xl !py-2.5 !font-black !text-[10px] !uppercase !tracking-widest" @click="campaign.status = 'active'" severity="success" />
                            <Button label="Inactive" :outlined="campaign.status !== 'inactive'" class="flex-1 !rounded-xl !py-2.5 !font-black !text-[10px] !uppercase !tracking-widest" @click="campaign.status = 'inactive'" severity="warn" />
                            <Button label="Terminée" :outlined="campaign.status !== 'terminee'" class="flex-1 !rounded-xl !py-2.5 !font-black !text-[10px] !uppercase !tracking-widest" @click="campaign.status = 'terminee'" severity="secondary" />
                        </div>
                    </div>
                </div>
                <template #footer>
                    <div class="flex gap-3 mt-4">
                        <Button label="Annuler" class="flex-1 !bg-white !text-slate-500 !border-slate-100 !rounded-xl !py-3 !font-bold" @click="campaignDialog = false" />
                        <Button label="Enregistrer" icon="pi pi-check" class="flex-1 !bg-gradient-to-r !from-blue-600 !to-indigo-600 !border-0 !rounded-xl !py-3 !font-bold !text-white shadow-lg shadow-blue-500/20" @click="saveCampaign" />
                    </div>
                </template>
            </Dialog>
        </div>
    </AppLayout>
</template>

<style scoped>
:deep(.p-selectbutton) {
    background: #f1f5f9;
    padding: 4px;
    border-radius: 12px;
}
:deep(.p-selectbutton .p-button) {
    border: none;
    background: transparent;
    color: #64748b;
    border-radius: 8px;
    transition: all 0.2s;
    font-weight: 500;
}
:deep(.p-selectbutton .p-button.p-highlight) {
    background: white;
    color: #2563eb;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
:deep(.p-datatable-custom .p-datatable-thead > tr > th) {
    background: #f8fafc;
    color: #64748b;
    font-weight: 600;
    font-size: 0.875rem;
    padding: 1rem;
}
:deep(.p-datatable-custom .p-datatable-tbody > tr > td) {
    padding: 1rem;
    border-bottom: 1px solid #f1f5f9;
}
</style>
