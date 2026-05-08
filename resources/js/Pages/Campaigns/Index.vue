<script setup>
import { ref, watch } from 'vue';
import { router, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import SelectButton from 'primevue/selectbutton';
import Dialog from 'primevue/dialog';
import Textarea from 'primevue/textarea';
import Menu from 'primevue/menu';
import { useToast } from 'primevue/usetoast';

// Props
const props = defineProps({
    campaigns: Object, // Paginated object
    filters: Object,
});

const toast = useToast();

// Local states
const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || 'Tous');
const statusOptions = ['Tous', 'Active', 'Inactive', 'Terminee'];

const campaignDialog = ref(false);
const deleteDialog = ref(false);
const selectedCampaign = ref(null);
const form = ref({
    name: '',
    description: '',
    start_date: '',
    end_date: '',
});

const menu = ref();
const menuItems = ref([]);

// Watchers for filtering
watch([search, statusFilter], () => {
    router.get(route('campaigns.index'), {
        search: search.value,
        status: statusFilter.value
    }, {
        preserveState: true,
        replace: true
    });
});

/**
 * Open context menu
 */
const toggleMenu = (event, data) => {
    selectedCampaign.value = data;
    menuItems.value = [
        { label: 'Voir le détail', icon: 'pi pi-eye', command: () => router.get(route('campaigns.show', data.id)) },
        { label: 'Modifier', icon: 'pi pi-pencil', disabled: data.status === 'terminee', command: () => editCampaign(data) },
        { separator: true },
        { 
            label: data.status === 'active' ? 'Désactiver' : 'Activer', 
            icon: data.status === 'active' ? 'pi pi-power-off' : 'pi pi-check-circle',
            disabled: data.status === 'terminee',
            command: () => toggleStatus(data)
        },
        { 
            label: 'Clôturer', 
            icon: 'pi pi-times-circle', 
            disabled: data.status === 'terminee',
            command: () => completeCampaign(data) 
        },
        { 
            label: 'Supprimer', 
            icon: 'pi pi-trash', 
            class: 'text-red-500', 
            disabled: data.status === 'active',
            command: () => confirmDelete(data) 
        }
    ];
    menu.value.toggle(event);
};

const openNew = () => {
    form.value = { name: '', description: '', start_date: '', end_date: '' };
    campaignDialog.value = true;
};

const editCampaign = (data) => {
    form.value = { ...data };
    // Format dates for input
    if (data.start_date) form.value.start_date = formatDateForInput(data.start_date);
    if (data.end_date) form.value.end_date = formatDateForInput(data.end_date);
    campaignDialog.value = true;
};

const formatDateForInput = (dateStr) => {
    const parts = dateStr.split('/');
    if (parts.length === 3) return `${parts[2]}-${parts[1]}-${parts[0]}`;
    return dateStr;
};

const saveCampaign = () => {
    if (form.value.id) {
        router.put(route('campaigns.update', form.value.id), form.value, {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Succès', detail: 'Campagne mise à jour', life: 3000 });
                campaignDialog.value = false;
            }
        });
    } else {
        router.post(route('campaigns.store'), form.value, {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Succès', detail: 'Campagne créée', life: 3000 });
                campaignDialog.value = false;
            }
        });
    }
};

const toggleStatus = (data) => {
    const action = data.status === 'active' ? 'deactivate' : 'activate';
    router.patch(route(`campaigns.${action}`, data.id), {}, {
        onSuccess: () => toast.add({ severity: 'success', summary: 'Succès', detail: 'Statut mis à jour', life: 3000 })
    });
};

const completeCampaign = (data) => {
    router.patch(route('campaigns.complete', data.id), {}, {
        onSuccess: () => toast.add({ severity: 'success', summary: 'Succès', detail: 'Campagne clôturée', life: 3000 })
    });
};

const confirmDelete = (data) => {
    selectedCampaign.value = data;
    deleteDialog.value = true;
};

const deleteCampaign = () => {
    router.delete(route('campaigns.destroy', selectedCampaign.value.id), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Succès', detail: 'Campagne supprimée', life: 3000 });
            deleteDialog.value = false;
        }
    });
};

const getStatusSeverity = (status) => {
    switch (status) {
        case 'active': return 'success';
        case 'inactive': return 'warn';
        case 'terminee': return 'secondary';
        default: return 'info';
    }
};

const onPage = (event) => {
    router.get(route('campaigns.index'), {
        page: event.page + 1,
        search: search.value,
        status: statusFilter.value
    }, { preserveState: true });
};
</script>

<template>
    <AppLayout>
        <div class="p-6 bg-slate-50 min-h-screen">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Campagnes</h1>
                    <p class="text-slate-500">{{ props.campaigns.total }} campagnes enregistrées</p>
                </div>
                <Button label="Nouvelle campagne" icon="pi pi-plus" class="rounded-xl px-4 py-2 bg-blue-600 border-none shadow-lg shadow-blue-200" @click="openNew" />
            </div>

            <!-- Filters -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 mb-6 flex flex-wrap gap-4 items-center">
                <div class="p-input-icon-left flex-1 min-w-[300px]">
                    <i class="pi pi-search ml-3 text-slate-400" />
                    <InputText v-model="search" placeholder="Rechercher une campagne..." class="w-full pl-10 rounded-xl border-slate-200" />
                </div>
                <SelectButton v-model="statusFilter" :options="statusOptions" class="custom-select-button" />
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <DataTable :value="props.campaigns.data" class="p-datatable-custom" responsiveLayout="stack">
                    <Column field="name" header="Nom" sortable>
                        <template #body="{ data }">
                            <Link :href="route('campaigns.show', data.id)" class="font-bold text-slate-900 hover:text-blue-600 transition-colors">
                                {{ data.name }}
                            </Link>
                        </template>
                    </Column>
                    
                    <Column field="description" header="Description">
                        <template #body="{ data }">
                            <span class="text-slate-500 truncate block max-w-xs">{{ data.description }}</span>
                        </template>
                    </Column>

                    <Column field="start_date" header="Début" sortable />
                    <Column field="end_date" header="Fin" sortable />

                    <Column header="Statut">
                        <template #body="{ data }">
                            <Tag :value="data.status" :severity="getStatusSeverity(data.status)" rounded class="px-3" />
                        </template>
                    </Column>

                    <Column header="Affectations">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2 text-slate-600">
                                <i class="pi pi-users text-sm" />
                                <span>{{ data.assignments_count || 0 }}</span>
                            </div>
                        </template>
                    </Column>

                    <Column header="Actions" class="w-20">
                        <template #body="{ data }">
                            <Button icon="pi pi-ellipsis-v" severity="secondary" text rounded @click="toggleMenu($event, data)" />
                        </template>
                    </Column>
                    
                    <template #empty>
                        <div class="text-center py-12">
                            <i class="pi pi-folder-open text-4xl text-slate-200 mb-3" />
                            <p class="text-slate-400">Aucune campagne trouvée.</p>
                        </div>
                    </template>
                </DataTable>
                
                <!-- Pagination -->
                <div class="p-4 border-t border-slate-100">
                    <DataTable :value="[]" :lazy="true" :paginator="true" :rows="10" :totalRecords="props.campaigns.total" 
                               :first="(props.campaigns.current_page - 1) * 10" @page="onPage" />
                </div>
            </div>

            <Menu ref="menu" :model="menuItems" :popup="true" />

            <!-- MODAL CRÉATION/EDITION -->
            <Dialog v-model:visible="campaignDialog" :header="form.id ? 'Modifier la campagne' : 'Nouvelle campagne'" modal class="p-fluid rounded-2xl" :style="{width: '500px'}">
                <div class="flex flex-col gap-4 mt-2">
                    <div>
                        <label class="font-semibold block mb-1">Nom de la campagne *</label>
                        <InputText v-model="form.name" placeholder="Ex: Satisfaction Client Q1" />
                    </div>
                    <div>
                        <label class="font-semibold block mb-1">Description</label>
                        <Textarea v-model="form.description" rows="3" placeholder="Objectifs et détails..." />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="font-semibold block mb-1">Date début *</label>
                            <InputText type="date" v-model="form.start_date" />
                        </div>
                        <div>
                            <label class="font-semibold block mb-1">Date fin</label>
                            <InputText type="date" v-model="form.end_date" />
                        </div>
                    </div>
                    <!-- Choix du statut dans le formulaire (Image 3 révisée) -->
                    <div>
                        <label class="font-semibold block mb-2">Statut de la campagne</label>
                        <div class="flex gap-2">
                            <Button label="Inactive" :outlined="form.status !== 'inactive' && form.status !== undefined" rounded @click="form.status = 'inactive'" severity="warn" size="small" />
                            <Button label="Active" :outlined="form.status !== 'active'" rounded @click="form.status = 'active'" severity="success" size="small" />
                            <Button label="Terminée" :outlined="form.status !== 'terminee'" rounded @click="form.status = 'terminee'" severity="secondary" size="small" />
                        </div>
                        <small class="text-slate-400 mt-1 block">Sélectionnez le statut initial de la campagne.</small>
                    </div>
                </div>
                <template #footer>
                    <Button label="Annuler" severity="secondary" text @click="campaignDialog = false" />
                    <Button label="Enregistrer" icon="pi pi-check" @click="saveCampaign" class="bg-blue-600 border-none rounded-xl" />
                </template>
            </Dialog>

            <Dialog v-model:visible="deleteDialog" header="Confirmation" modal :style="{width: '400px'}">
                <div class="flex items-center gap-3">
                    <i class="pi pi-exclamation-triangle text-red-500 text-3xl" />
                    <span>Voulez-vous supprimer <b>{{ selectedCampaign?.name }}</b> ? Cette action est irréversible.</span>
                </div>
                <template #footer>
                    <Button label="Non" severity="secondary" text @click="deleteDialog = false" />
                    <Button label="Oui, supprimer" severity="danger" @click="deleteCampaign" class="bg-red-600 border-none rounded-xl" />
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
