<script setup>
// Import des composants primevue
import Toolbar from 'primevue/toolbar';
import Button from 'primevue/button';
import FileUpload from 'primevue/fileupload';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import Dialog from 'primevue/dialog';
import Textarea from 'primevue/textarea';
import Dropdown from 'primevue/dropdown';
import Menu from 'primevue/menu';


// Import router inertia pour envoyer les requêtes au backend
import { router } from '@inertiajs/vue3'

// Import des fonctions Vue
import { ref } from 'vue'

// PrimeVue filtre
import { FilterMatchMode } from '@primevue/core/api'

// Toast notification
import { useToast } from 'primevue/usetoast'
import AppLayout from '@/Layouts/AppLayout.vue';

// import { Button, Column, DataTable, Dialog, Dropdown, FileUpload, InputText, Tag, Textarea, Toolbar } from 'primevue'

// Props venant du controller Laravel (Inertia)
const props = defineProps({
    campaigns: Array, // liste des campagnes
})

// Liste affichée dans le tableau
const campaigns = ref(props.campaigns)

// Objet campagne pour le formulaire (create/update)
const campaign = ref({})

// Toast pour feedback utilisateur
const toast = useToast()

// Référence du datatable
const dt = ref()

// Dialogs (modals)
const campaignDialog = ref(false)
const deleteDialog = ref(false)

// Campagne sélectionnée pour suppression
const selectedCampaign = ref(null)

// Menu actions
const menu = ref();
const menuItems = ref([]);

// Filtres du tableau
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
})

// Validation form
const submitted = ref(false)

// Liste des statuts
const statuses = [
    { label: 'Active', value: 'active' },
    { label: 'Inactive', value: 'inactive' },
    { label: 'Terminée', value: 'terminee' }
]

/**
 * Logique pour afficher le menu contextuel sur une ligne
 * @param event - L'événement click
 * @param data - La campagne de la ligne
 */
const toggleMenu = (event, data) => {
    
    selectedCampaign.value = data;
    menuItems.value = [
        { label: 'Voir le détail', icon: 'pi pi-eye' },
        { label: 'Modifier', icon: 'pi pi-pencil', command: () => editCampaign(data) },
        { label: 'Changer statut', icon: 'pi pi-sync' },
        { separator: true },
        { label: 'Supprimer', icon: 'pi pi-trash', class: 'text-red-500', command: () => confirmDelete(data) }
    ];
    menu.value.toggle(event);
};

/**
 * Ouvrir le modal pour créer une campagne
 */
const openNew = () => {
    campaign.value = {} // reset
    submitted.value = false
    campaignDialog.value = true
}

/**
 * Fermer le modal
 */
const hideDialog = () => {
    campaignDialog.value = false
}

/**
 * Sauvegarder (create ou update)
 */
const saveCampaign = () => {

    submitted.value = true

    // Vérification simple
    if (!campaign.value.name) return

    // UPDATE
    if (campaign.value.id) {
        router.put(`/campaigns/${campaign.value.id}`, campaign.value, {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Succès', detail: 'Campagne mise à jour', life: 3000 })
                campaignDialog.value = false
            }
        })
    }
    // CREATE
    else {
        router.post('/campaigns', campaign.value, {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Succès', detail: 'Campagne créée', life: 3000 })
                campaignDialog.value = false
            }
        })
    }
}

/**
 * Modifier une campagne
 */
const editCampaign = (data) => {
    campaign.value = { ...data } // copie
    campaignDialog.value = true
}

/**
 * Confirmer suppression
 */
const confirmDelete = (data) => {
    selectedCampaign.value = data
    deleteDialog.value = true
}

/**
 * Supprimer campagne
 */
const deleteCampaign = () => {
    router.delete(`/campaigns/${selectedCampaign.value.id}`, {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Succès', detail: 'Supprimé', life: 3000 })
            deleteDialog.value = false
        }
    })
}

/**
 * Couleur du statut
 */
const getStatusSeverity = (status) => {
    switch (status) {
        case 'active': return 'success'
        case 'inactive': return 'warn'
        case 'terminee': return 'secondary'
    }
}

</script>

<template>
    <AppLayout>
        <div class="p-4">
            <div class="card bg-white border-round shadow-1">
                <!-- Toolbar ajustée avec la couleur bleue du bouton (bg-blue-600 ou via style) -->
                <Toolbar class="mb-6 border-none bg-transparent">
                    <template #start>
                        <!-- Le bouton bleu comme sur ton image -->
                        <Button label="Nouvelle campagne" icon="pi pi-plus" class="p-button-primary" 
                                style="background-color: #2563eb; border: none" @click="openNew" />
                    </template>

                    <template #end>
                        <FileUpload mode="basic" accept="image/*" :maxFileSize="1000000" label="Import" customUpload
                            chooseLabel="Import" class="mr-2" auto :chooseButtonProps="{ severity: 'secondary' }" />
                        <Button label="Export" icon="pi pi-upload" severity="secondary" variant="text" />
                    </template>
                </Toolbar>

                <DataTable :value="campaigns" :filters="filters" paginator :rows="10" 
                           class="p-datatable-sm" responsiveLayout="stack">
                    
                    <!-- HEADER DU TABLEAU -->
                    <template #header>
                        <div class="flex flex-wrap gap-2 justify-between items-center">
                            <h2 class="m-0 text-xl font-semibold">Campagnes</h2>
                            <div class="flex items-center gap-2">
                                <span class="p-input-icon-left">
                                    <InputText v-model="filters.global.value" placeholder="Rechercher une campagne..." class="w-full md:w-20rem" />
                                </span>
                            </div>
                        </div>
                    </template>

                    <!-- COLONNES -->
                    <Column field="name" header="Nom" sortable class="font-bold" />
                    <Column field="description" header="Description" />
                    <Column field="start_date" header="Début" sortable />
                    <Column field="end_date" header="Fin" sortable />

                    <Column header="Statut">
                        <template #body="slotProps">
                            <!-- Tag avec pastille ronde comme sur ton image -->
                            <Tag :value="slotProps.data.status" :severity="getStatusSeverity(slotProps.data.status)" rounded />
                        </template>
                    </Column>

                    <!-- COLONNE ACTIONS AVEC MENU PRIME VUE -->
                    <Column header="Actions" headerStyle="width: 5rem; text-align: center" bodyStyle="text-align: center; overflow: visible">
                        <template #body="slotProps">
                            <Button type="button" icon="pi pi-ellipsis-v" @click="toggleMenu($event, slotProps.data)" 
                                    aria-haspopup="true" aria-controls="overlay_menu" severity="secondary" variant="text" rounded />
                        </template>
                    </Column>
                </DataTable>
                
                <!-- Composant Menu flottant pour les actions -->
                <Menu ref="menu" id="overlay_menu" :model="menuItems" :popup="true" />
            </div>

            <!-- MODAL DE CRÉATION/MODIFICATION -->
<Dialog 
    v-model:visible="campaignDialog" 
    :style="{ width: '520px' }" 
    modal 
    :closable="true"
    class="p-fluid rounded-xl"
>
    <!-- ✨ HEADER PERSONNALISÉ -->
    <template #header>
        <div class="flex justify-between items-center w-full">
            <span class="text-lg font-semibold">Nouvelle campagne</span>
        </div>
    </template>

    <div class="flex flex-col gap-5 mt-2">

        <!-- ✨ NOM -->
        <div>
            <label class="text-sm font-medium">
                Nom <span class="text-red-500">*</span>
            </label>
            <InputText 
                v-model="campaign.name" 
                placeholder="Nom de la campagne"
                class="mt-1"
                :class="{ 'p-invalid': submitted && !campaign.name }"
            />
            <small v-if="submitted && !campaign.name" class="p-error">
                Le nom est obligatoire
            </small>
        </div>

        <!-- ✨ DESCRIPTION -->
        <div>
            <label class="text-sm font-medium">Description</label>
            <Textarea 
                v-model="campaign.description" 
                rows="3"
                placeholder="Description optionnelle"
                class="mt-1"
            />
        </div>

        <!-- ✨ DATES (alignées comme sur l’image) -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium">
                    Date début <span class="text-red-500">*</span>
                </label>
                <InputText type="date" v-model="campaign.start_date" class="mt-1" />
            </div>

            <div>
                <label class="text-sm font-medium">
                    Date fin <span class="text-red-500">*</span>
                </label>
                <InputText type="date" v-model="campaign.end_date" class="mt-1" />
            </div>
        </div>

        <!-- ✨ STATUT EN BOUTONS (comme l’image) -->
        <div>
            <label class="text-sm font-medium block mb-2">Statut</label>

            <div class="flex gap-2">
                <Button 
                    label="Active"
                    :outlined="campaign.status !== 'active'"
                    :class="[
                        'rounded-full px-4',
                        campaign.status === 'active' ? 'bg-green-100 text-green-700 border-green-300' : ''
                    ]"
                    @click="campaign.status = 'active'"
                />

                <Button 
                    label="Inactive"
                    :outlined="campaign.status !== 'inactive'"
                    class="rounded-full px-4"
                    @click="campaign.status = 'inactive'"
                />

                <Button 
                    label="Terminée"
                    :outlined="campaign.status !== 'terminee'"
                    class="rounded-full px-4"
                    @click="campaign.status = 'terminee'"
                />
            </div>
        </div>

        <!-- ✨ APERÇU STATUT -->
        <div class="bg-gray-50 border-round p-3 flex items-center gap-2">
            <span class="text-sm text-gray-500">Aperçu :</span>

            <Tag 
                :value="campaign.status || 'active'" 
                :severity="getStatusSeverity(campaign.status || 'active')" 
                rounded 
            />
        </div>
    </div>

    <!-- ✨ FOOTER -->
    <template #footer>
        <div class="flex justify-end gap-2 w-full">
            <Button 
                label="Annuler" 
                severity="secondary" 
                text 
                class="rounded-full"
                @click="hideDialog" 
            />

            <Button 
                label="Enregistrer" 
                icon="pi pi-check"
                class="rounded-full px-4"
                style="background-color:#2563eb; border:none"
                @click="saveCampaign" 
            />
        </div>
    </template>
</Dialog>

            <!-- MODAL SUPPRESSION -->
            <Dialog v-model:visible="deleteDialog" :style="{ width: '450px' }" header="Confirmation" modal>
                <div class="flex items-center gap-4">
                    <i class="pi pi-exclamation-triangle text-red-500" style="font-size: 2rem" />
                    <span v-if="selectedCampaign">Voulez-vous vraiment supprimer <b>{{ selectedCampaign.name }}</b> ?</span>
                </div>
                <template #footer>
                    <Button label="Non" icon="pi pi-times" severity="secondary" variant="text" @click="deleteDialog = false" />
                    <Button label="Oui, supprimer" icon="pi pi-trash" severity="danger" @click="deleteCampaign" />
                </template>
            </Dialog>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Ajout d'un style pour correspondre au design épuré */
:deep(.p-datatable-header) {
    background: transparent;
    border: none;
    padding: 1.5rem 1rem;
}

:deep(.p-tag) {
    text-transform: capitalize;
}
</style>