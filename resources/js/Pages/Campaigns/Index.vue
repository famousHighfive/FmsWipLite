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

// Import router inertia pour envoyer les requêtes au backend
import { router } from '@inertiajs/vue3'

// Import des fonctions Vue
import { ref } from 'vue'

// PrimeVue filtre
import { FilterMatchMode } from '@primevue/core/api'

// Toast notification
import { useToast } from 'primevue/usetoast'
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
    <div>
        <div class="card">
            <Toolbar class="mb-6">
                <template #start>
                    <Button label="New" icon="pi pi-plus" class="mr-2" @click="openNew" />
                    <Button label="Delete" icon="pi pi-trash" severity="danger" variant="outlined"
                        @click="confirmDeleteSelected" :disabled="!selectedProducts || !selectedProducts.length" />
                </template>

                <template #end>
                    <FileUpload mode="basic" accept="image/*" :maxFileSize="1000000" label="Import" customUpload
                        chooseLabel="Import" class="mr-2" auto :chooseButtonProps="{ severity: 'secondary' }" />
                    <Button label="Export" icon="pi pi-upload" severity="secondary" @click="exportCSV($event)" />
                </template>
            </Toolbar>

<DataTable 
    :value="campaigns"
    :filters="filters"
    paginator
    :rows="10"
>

    <!-- HEADER -->
    <template #header>
        <div class="flex justify-between">
            <h3>Campagnes</h3>

            <div class="flex gap-2">
                <InputText v-model="filters.global.value" placeholder="Rechercher..." />
                <Button label="Nouvelle" icon="pi pi-plus" @click="openNew" />
            </div>
        </div>
    </template>

    <!-- COLONNES -->
    <Column field="name" header="Nom" sortable />

    <Column field="description" header="Description" />

    <Column field="start_date" header="Début" />

    <Column field="end_date" header="Fin" />

    <Column header="Statut">
        <template #body="slotProps">
            <Tag 
                :value="slotProps.data.status"
                :severity="getStatusSeverity(slotProps.data.status)"
            />
        </template>
    </Column>

    <Column header="Actions">
        <template #body="slotProps">
            <Button icon="pi pi-pencil" @click="editCampaign(slotProps.data)" />
            <Button icon="pi pi-trash" severity="danger" @click="confirmDelete(slotProps.data)" />
        </template>
    </Column>

</DataTable>
        </div>
        
<Dialog v-model:visible="campaignDialog" header="Campagne" modal>

    <div class="flex flex-col gap-4">

        <!-- NOM -->
        <InputText 
            v-model="campaign.name" 
            placeholder="Nom"
        />

        <!-- DESCRIPTION -->
        <Textarea 
            v-model="campaign.description"
            placeholder="Description"
        />

        <!-- DATES -->
        <InputText type="date" v-model="campaign.start_date" />
        <InputText type="date" v-model="campaign.end_date" />

        <!-- STATUS -->
        <Dropdown 
            v-model="campaign.status"
            :options="statuses"
            optionLabel="label"
            optionValue="value"
            placeholder="Statut"
        />

    </div>

    <template #footer>
        <Button label="Annuler" @click="hideDialog" />
        <Button label="Enregistrer" @click="saveCampaign" />
    </template>

</Dialog>

<Dialog v-model:visible="deleteDialog" header="Confirmation" modal>

    <p>Supprimer cette campagne ?</p>

    <template #footer>
        <Button label="Non" @click="deleteDialog=false" />
        <Button label="Oui" severity="danger" @click="deleteCampaign" />
    </template>

</Dialog>

        <!-- <Dialog v-model:visible="deleteProductsDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span v-if="product">Are you sure you want to delete the selected products?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteProductsDialog = false" severity="secondary"
                    variant="text" />
                <Button label="Yes" icon="pi pi-check" text @click="deleteSelectedProducts" severity="danger" />
            </template>
        </Dialog> -->
    </div>
</template>
