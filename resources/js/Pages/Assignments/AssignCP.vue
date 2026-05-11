<script setup>
import { ref } from 'vue'

import { router } from '@inertiajs/vue3'

import AppLayout from '@/Layouts/AppLayout.vue'

import Dialog from 'primevue/dialog'
import Card from 'primevue/card'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Divider from 'primevue/divider'
import Tag from 'primevue/tag'
import Select from 'primevue/select'
import Toast from 'primevue/toast'
import Menu from 'primevue/menu'
import Avatar from 'primevue/avatar'

import { useToast } from 'primevue/usetoast'

/**
 * =========================================================
 * DONNÉES
 * =========================================================
 */
const props = defineProps({

    /**
     * CP non affectés
     */
    unassignedCPs: Array,

    /**
     * CP affectés
     */
    assignedCPs: Array,

    /**
     * Campagnes disponibles
     */
    campaigns: Array,
    
    /**
     * Campagnes disponibles
     */
    availableCampaigns: Array
})

/**
 * =========================================================
 * TOAST
 * =========================================================
 */
const toast = useToast()

/**
 * =========================================================
 * RECHERCHES
 * =========================================================
 */
const leftSearch = ref('')
const rightSearch = ref('')

/**
 * =========================================================
 * SÉLECTIONS
 * =========================================================
 */
const selectedCampaign = ref(null)
const selectedAssignment = ref(null)
/**
 * Dialog nouvelle campagne
 */
const assignCampaignDialog = ref(false)

/**
 * Campagne sélectionnée
 */
const reassignmentCampaign = ref(null)

/**
 * =========================================================
 * DIALOG LIBÉRATION
 * =========================================================
 */
const releaseDialog = ref(false)

/**
 * =========================================================
 * OUVRIR DIALOG LIBÉRATION
 * =========================================================
 */
const openReleaseDialog = () => {

    releaseDialog.value = true
}

/**
 * =========================================================
 * LIBÉRER UN CP
 * =========================================================
 */
const releaseCP = () => {

    router.post(

        `/assign/${selectedAssignment.value.id}/release-cp`,

        {},

        {
            onSuccess: () => {

                toast.add({
                    severity: 'success',
                    summary: 'Succès',
                    detail: 'CP libéré avec succès',
                    life: 3000
                })

                /**
                 * Fermer dialog
                 */
                releaseDialog.value = false
            }
        }
    )
}


/**
 * =========================================================
 * MENU CONTEXTUEL
 * =========================================================
 */
const menu = ref()

/**
 * Items du menu
 */
const menuItems = ref([])

/**
 * =========================================================
 * AFFECTER UN CP
 * =========================================================
 */
const assignCP = (cp) => {

    /**
     * Vérifie campagne
     */
    if (!selectedCampaign.value) {

        toast.add({
            severity: 'warn',
            summary: 'Attention',
            detail: 'Sélectionnez une campagne',
            life: 3000
        })

        return
    }

    /**
     * Requête Laravel
     */
    router.post('/assign/cp', {

        /**
         * Employé
         */
        employee_id: cp.id,

        /**
         * Campagne
         */
        campaign_id: selectedCampaign.value.id

    }, {

        /**
         * Succès
         */
        onSuccess: () => {

            toast.add({
                severity: 'success',
                summary: 'Succès',
                detail: 'CP affecté avec succès',
                life: 3000
            })

            /**
             * Reset
             */
            selectedCampaign.value = null
        }
    })
}

/**
 * =========================================================
 * MENU ACTIONS
 * =========================================================
 */
const toggleMenu = (event, assignment) => {

    /**
     * Affectation sélectionnée
     */
    selectedAssignment.value = assignment

    /**
     * Items menu
     */
   menuItems.value = [

    /**
     * Affecter à une autre campagne
     */
    {
            label: 'Affecter à une campagne',
            icon: 'pi pi-plus',
            command: () => {

                /**
                 * Reset
                 */
                reassignmentCampaign.value = null

                /**
                 * Ouvre dialog
                 */
                assignCampaignDialog.value = true
            }
        },


    /**
     * Voir les superviseurs
     */
    {
        label: 'Voir les superviseurs',
        icon: 'pi pi-sitemap',
        command: () => {
            openHierarchyDialog()
        }
    },

    /**
     * Réaffecter la hiérarchie
     */
    {
        label: 'Transférer la hiérarchie',
        icon: 'pi pi-refresh',
        command: () => {
            openTransferDialog()
        }
    },

    /**
     * Libérer le CP
     */
    {
        label: 'Libérer',
        icon: 'pi pi-sign-out',
        class: 'text-red-500',
        command: () => {
            openReleaseDialog()
        }
    }
]

    /**
     * Ouvre menu
     */
    menu.value.toggle(event)
}

/**
 * =========================================================
 * AJOUTER UNE CAMPAGNE À UN CP
 * =========================================================
 */
const assignNewCampaign = () => {

    /**
     * Vérification
     */
    if (!reassignmentCampaign.value) {

        toast.add({
            severity: 'warn',
            summary: 'Attention',
            detail: 'Sélectionnez une campagne',
            life: 3000
        })

        return
    }

    /**
     * Requête
     */
    router.post(

        `/assign/${selectedAssignment.value.id}/campaign`,

        {
            campaign_id: reassignmentCampaign.value.id
        },

        {
            onSuccess: () => {

                toast.add({
                    severity: 'success',
                    summary: 'Succès',
                    detail: 'Nouvelle campagne affectée',
                    life: 3000
                })

                assignCampaignDialog.value = false
            }
        }
    )
}






</script>

<template>
    <AppLayout>

        <Toast />

        <div class="min-h-screen bg-slate-100">

            <!-- ================================================= -->
            <!-- HEADER -->
            <!-- ================================================= -->

            <div class="bg-white border-b border-slate-200 px-8 py-6">

                <div class="flex items-center justify-between">

                    <div>

                        <h1 class="text-3xl font-bold text-slate-900">
                            Gestion des Chefs de Plateau
                        </h1>

                        <p class="text-slate-500 mt-2">
                            Affectation, suivi et gestion des campagnes
                        </p>

                    </div>

                    <div class="hidden md:flex items-center gap-3">

                        <Tag
                            :value="`${assignedCPs.length} Affectations actives`"
                            severity="success"
                            rounded
                        />

                        <Tag
                            :value="`${unassignedCPs.length} Disponibles`"
                            severity="warn"
                            rounded
                        />

                    </div>

                </div>

            </div>





            <!-- ================================================= -->
            <!-- CONTENT -->
            <!-- ================================================= -->

            <div class="p-8">

                <div class="grid grid-cols-1 2xl:grid-cols-2 gap-8">

                    <!-- ========================================= -->
                    <!-- CP DISPONIBLES -->
                    <!-- ========================================= -->

                    <Card class="shadow-sm border-0 rounded-3xl overflow-hidden">

                        <template #content>

                            <!-- TOP -->
                            <div class="flex items-center justify-between mb-6">

                                <div>

                                    <h2 class="text-2xl font-bold text-slate-900">
                                        CP Disponibles
                                    </h2>

                                    <p class="text-slate-500 mt-1">
                                        Ressources sans campagne active
                                    </p>

                                </div>

                                <div
                                    class="w-12 h-12 rounded-2xl bg-orange-100 flex items-center justify-center"
                                >
                                    <i class="pi pi-users text-orange-500 text-xl"></i>
                                </div>

                            </div>





                            <!-- SEARCH -->
                            <div class="mb-6">

                                <span class="p-input-icon-left w-full">

                                    <i class="pi pi-search" />

                                    <InputText
                                        v-model="leftSearch"
                                        placeholder="Rechercher un CP..."
                                        class="w-full rounded-2xl"
                                    />

                                </span>

                            </div>





                            <!-- LIST -->
                            <div class="space-y-4 max-h-[700px] overflow-y-auto pr-2">

                                <div
                                    v-for="cp in unassignedCPs.filter(c =>
                                        c.first_name.toLowerCase().includes(leftSearch.toLowerCase()) ||
                                        c.last_name.toLowerCase().includes(leftSearch.toLowerCase())
                                    )"
                                    :key="cp.id"
                                    class="bg-slate-50 border border-slate-200 rounded-3xl p-5 hover:border-blue-300 hover:shadow-md transition-all"
                                >

                                    <!-- TOP -->
                                    <div class="flex items-start justify-between">

                                        <div class="flex items-center gap-4">

                                            <!-- AVATAR -->
                                            <Avatar
                                                :label="`${cp.first_name[0]}${cp.last_name[0]}`"
                                                shape="circle"
                                                size="large"
                                                class="bg-blue-600 text-white"
                                            />

                                            <!-- INFOS -->
                                            <div>

                                                <h3 class="font-bold text-slate-900 text-lg">
                                                    {{ cp.first_name }}
                                                    {{ cp.last_name }}
                                                </h3>

                                                <p class="text-slate-500 text-sm mt-1">
                                                    {{ cp.email }}
                                                </p>

                                            </div>

                                        </div>

                                        <!-- STATUS -->
                                        <Tag
                                            value="Disponible"
                                            severity="warn"
                                            rounded
                                        />

                                    </div>





                                    <Divider />





                                    <!-- ACTIONS -->
                                    <div class="space-y-3">

                                        <Select
                                            v-model="selectedCampaign"
                                            :options="campaigns"
                                            optionLabel="name"
                                            placeholder="Choisir une campagne"
                                            class="w-full"
                                        />

                                        <Button
                                            label="Affecter à la campagne"
                                            icon="pi pi-check"
                                            class="w-full !bg-blue-600 border-none rounded-xl"
                                            @click="assignCP(cp)"
                                        />

                                    </div>

                                </div>

                            </div>

                        </template>

                    </Card>









                    <!-- ========================================= -->
                    <!-- CP AFFECTÉS -->
                    <!-- ========================================= -->

                    <Card class="shadow-sm border-0 rounded-3xl overflow-hidden">

                        <template #content>

                            <!-- TOP -->
                            <div class="flex items-center justify-between mb-6">

                                <div>

                                    <h2 class="text-2xl font-bold text-slate-900">
                                        CP Affectés
                                    </h2>

                                    <p class="text-slate-500 mt-1">
                                        Affectations actives par campagne
                                    </p>

                                </div>

                                <div
                                    class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center"
                                >
                                    <i class="pi pi-briefcase text-emerald-500 text-xl"></i>
                                </div>

                            </div>





                            <!-- SEARCH -->
                            <div class="mb-6">

                                <span class="p-input-icon-left w-full">

                                    <i class="pi pi-search" />

                                    <InputText
                                        v-model="rightSearch"
                                        placeholder="Rechercher un CP..."
                                        class="w-full rounded-2xl"
                                    />

                                </span>

                            </div>





                            <!-- LIST -->
                            <div class="space-y-4 max-h-[700px] overflow-y-auto pr-2">

                                <div
                                    v-for="assignment in assignedCPs.filter(a =>
                                        a.employee.first_name.toLowerCase().includes(rightSearch.toLowerCase()) ||
                                        a.employee.last_name.toLowerCase().includes(rightSearch.toLowerCase())
                                    )"
                                    :key="assignment.id"
                                    class="bg-white border border-slate-200 rounded-3xl p-5 hover:shadow-md hover:border-emerald-300 transition-all"
                                >

                                    <!-- TOP -->
                                    <div class="flex items-start justify-between">

                                        <div class="flex items-center gap-4">

                                            <!-- AVATAR -->
                                            <Avatar
                                                :label="`${assignment.employee.first_name[0]}${assignment.employee.last_name[0]}`"
                                                shape="circle"
                                                size="large"
                                                class="bg-emerald-600 text-white"
                                            />

                                            <!-- INFOS -->
                                            <div>

                                                <h3 class="font-bold text-slate-900 text-lg">
                                                    {{ assignment.employee.first_name }}
                                                    {{ assignment.employee.last_name }}
                                                </h3>

                                                <p class="text-slate-500 text-sm mt-1">
                                                    {{ assignment.employee.email }}
                                                </p>

                                            </div>

                                        </div>





                                        <!-- RIGHT -->
                                        <div class="flex items-center gap-2">

                                            <Tag
                                                value="Actif"
                                                severity="success"
                                                rounded
                                            />

                                            <!-- MENU BTN -->
                                            <Button
                                                icon="pi pi-ellipsis-v"
                                                severity="secondary"
                                                text
                                                rounded
                                                @click="toggleMenu($event, assignment)"
                                            />

                                        </div>

                                    </div>





                                    <Divider />





                                    <!-- CAMPAGNE -->
                                    <div
                                        class="bg-slate-50 rounded-2xl p-4 flex items-center justify-between"
                                    >

                                        <div>

                                            <p class="text-sm text-slate-500">
                                                Campagne assignée
                                            </p>

                                            <h4 class="font-bold text-slate-900 mt-1">
                                                {{ assignment.campaign.name }}
                                            </h4>

                                        </div>

                                        <Tag
                                            :value="assignment.campaign.status"
                                            severity="success"
                                            rounded
                                        />

                                    </div>

                                </div>

                            </div>

                        </template>

                    </Card>

                </div>

            </div>





            <!-- ================================================= -->
            <!-- MENU -->
            <!-- ================================================= -->

            <Menu
                ref="menu"
                :model="menuItems"
                popup
            />

            <Dialog
    v-model:visible="assignCampaignDialog"
    modal
    header="Affecter une nouvelle campagne"
    :style="{ width: '450px' }"
>

    <div class="space-y-4">

        <div>

            <label class="block text-sm font-medium mb-2">
                Campagne
            </label>

            <Select
                v-model="reassignmentCampaign"
                :options="availableCampaigns"
                optionLabel="name"
                placeholder="Choisir une campagne"
                class="w-full"
            />

        </div>

    </div>

    <template #footer>

        <Button
            label="Annuler"
            severity="secondary"
            text
            @click="assignCampaignDialog = false"
        />

        <Button
            label="Affecter"
            icon="pi pi-check"
            class="!bg-blue-600 border-none"
            @click="assignNewCampaign"
        />

    </template>

</Dialog>

<!-- ================================================= -->
<!-- DIALOG LIBÉRATION -->
<!-- ================================================= -->

<Dialog
    v-model:visible="releaseDialog"
    modal
    header="Libérer le Chef de Plateau"
    :style="{ width: '450px' }"
>

    <div class="space-y-4">

        <div
            class="bg-red-50 border border-red-200 rounded-2xl p-4"
        >

            <div class="flex items-start gap-3">

                <i class="pi pi-exclamation-triangle text-red-500 text-xl mt-1"></i>

                <div>

                    <h3 class="font-semibold text-red-700">
                        Confirmation requise
                    </h3>

                    <p class="text-sm text-red-600 mt-2 leading-relaxed">

                        Vous êtes sur le point de libérer :

                        <span class="font-semibold">
                            {{ selectedAssignment?.employee.first_name }}
                            {{ selectedAssignment?.employee.last_name }}
                        </span>

                        de la campagne :

                        <span class="font-semibold">
                            {{ selectedAssignment?.campaign.name }}
                        </span>

                    </p>

                </div>

            </div>

        </div>

    </div>

    <template #footer>

        <Button
            label="Annuler"
            severity="secondary"
            text
            @click="releaseDialog = false"
        />

        <Button
            label="Libérer"
            icon="pi pi-sign-out"
            severity="danger"
            @click="releaseCP"
        />

    </template>

</Dialog>
        </div>

    </AppLayout>
</template>