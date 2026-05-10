<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'

import AppLayout from '@/Layouts/AppLayout.vue'

/**
 * =========================================================
 * PRIMEVUE
 * =========================================================
 */
import Card from 'primevue/card'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import Select from 'primevue/select'
import InputText from 'primevue/inputtext'
import Tag from 'primevue/tag'
import Avatar from 'primevue/avatar'
import Accordion from 'primevue/accordion'
import AccordionPanel from 'primevue/accordionpanel'
import AccordionHeader from 'primevue/accordionheader'
import AccordionContent from 'primevue/accordioncontent'
import Toast from 'primevue/toast'
import Divider from 'primevue/divider'

import { useToast } from 'primevue/usetoast'

/**
 * =========================================================
 * PROPS
 * =========================================================
 */
const props = defineProps({

    /**
     * TC non affectés
     */
    unassignedTCs: Array,

    /**
     * TC affectés
     */
    assignedTCs: Array,

    /**
     * SUP actifs
     */
    supAssignments: Array
})

/**
 * =========================================================
 * TOAST
 * =========================================================
 */
const toast = useToast()

/**
 * =========================================================
 * SEARCH
 * =========================================================
 */
const search = ref('')

/**
 * =========================================================
 * SELECT SUP PAR TC
 * =========================================================
 *
 * Object :
 * {
 *   employee_id: selectedSUP
 * }
 */
const selectedSUPs = ref({})

/**
 * =========================================================
 * FILTRAGE TC
 * =========================================================
 */
const filteredTCs = computed(() => {

    return props.unassignedTCs.filter(tc => {

        const fullname = `${tc.first_name} ${tc.last_name}`

        return fullname
            .toLowerCase()
            .includes(search.value.toLowerCase())
    })
})

/**
 * =========================================================
 * AFFECTER TC
 * =========================================================
 */
const assignTC = (tc) => {

    /**
     * SUP choisi
     */
    const selectedSUP = selectedSUPs.value[tc.id]

    /**
     * Vérification
     */
    if (!selectedSUP) {

        toast.add({
            severity: 'warn',
            summary: 'Attention',
            detail: 'Choisissez un superviseur',
            life: 3000
        })

        return
    }

    /**
     * Requête
     */
    router.post('/assign/tc', {

        employee_id: tc.id,

        sup_assignment_id: selectedSUP.id

    }, {

        onSuccess: () => {

            toast.add({
                severity: 'success',
                summary: 'Succès',
                detail: 'TC affecté avec succès',
                life: 3000
            })
        }
    })
}

/**
 * =========================================================
 * GROUPEMENT PAR SUP
 * =========================================================
 */
const hierarchy = computed(() => {

    return props.supAssignments.map(sup => {

        /**
         * TC liés au SUP
         */
        const tcs = props.assignedTCs.filter(tc => {

            return tc.manager_id === sup.employee_id
        })

        return {
            sup,
            tcs
        }
    })
})
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
                    Gestion des Téléconseillers
                </h1>

                <p class="text-slate-500 mt-2">
                    Affectation des TC aux superviseurs
                </p>

            </div>

            <div class="flex items-center gap-3">

                <Tag
                    :value="`${unassignedTCs.length} disponibles`"
                    severity="warn"
                    rounded
                />

                <Tag
                    :value="`${assignedTCs.length} affectés`"
                    severity="success"
                    rounded
                />

                <Tag
                    :value="`${supAssignments.length} superviseurs actifs`"
                    severity="info"
                    rounded
                />

            </div>

        </div>

    </div>





    <!-- ================================================= -->
    <!-- CONTENT -->
    <!-- ================================================= -->

    <div class="p-8">

        <div class="grid grid-cols-1 2xl:grid-cols-5 gap-8">

            <!-- ========================================= -->
            <!-- TABLE TC DISPONIBLES -->
            <!-- ========================================= -->

            <div class="2xl:col-span-3">

                <Card class="rounded-3xl border-0 shadow-sm">

                    <template #content>

                        <!-- TOP -->
                        <div class="flex items-center justify-between mb-6">

                            <div>

                                <h2 class="text-2xl font-bold text-slate-900">
                                    TC Disponibles
                                </h2>

                                <p class="text-slate-500 mt-1">
                                    Ressources prêtes à être affectées
                                </p>

                            </div>

                            <div
                                class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center"
                            >
                                <i class="pi pi-users text-blue-600 text-xl"></i>
                            </div>

                        </div>





                        <!-- SEARCH -->
                        <div class="mb-6">

                            <span class="p-input-icon-left w-full">

                                <i class="pi pi-search" />

                                <InputText
                                    v-model="search"
                                    placeholder="Rechercher un TC..."
                                    class="w-full rounded-2xl"
                                />

                            </span>

                        </div>





                        <!-- TABLE -->
                        <DataTable
                            :value="filteredTCs"
                            paginator
                            :rows="8"
                            stripedRows
                            responsiveLayout="scroll"
                            class="rounded-2xl overflow-hidden"
                        >

                            <!-- AVATAR -->
                            <Column header="TC">

                                <template #body="{ data }">

                                    <div class="flex items-center gap-3">

                                        <Avatar
                                            :label="`${data.first_name[0]}${data.last_name[0]}`"
                                            shape="circle"
                                            class="bg-blue-600 text-white"
                                        />

                                        <div>

                                            <div class="font-semibold text-slate-900">
                                                {{ data.first_name }}
                                                {{ data.last_name }}
                                            </div>

                                            <div class="text-sm text-slate-500">
                                                {{ data.email }}
                                            </div>

                                        </div>

                                    </div>

                                </template>

                            </Column>





                            <!-- SUP -->
                            <Column header="Superviseur">

                                <template #body="{ data }">

                                    <Select
                                        v-model="selectedSUPs[data.id]"
                                        :options="supAssignments"
                                        optionLabel="employee.first_name"
                                        placeholder="Choisir un SUP"
                                        class="w-full"
                                    >

                                        <template #option="slotProps">

                                            <div class="flex flex-col">

                                                <span class="font-medium">
                                                    {{ slotProps.option.employee.first_name }}
                                                    {{ slotProps.option.employee.last_name }}
                                                </span>

                                                <span class="text-xs text-slate-500">
                                                    {{ slotProps.option.campaign.name }}
                                                </span>

                                            </div>

                                        </template>

                                    </Select>

                                </template>

                            </Column>





                            <!-- ACTION -->
                            <Column header="">

                                <template #body="{ data }">

                                    <Button
                                        label="Affecter"
                                        icon="pi pi-check"
                                        class="!bg-blue-600 border-none rounded-xl"
                                        @click="assignTC(data)"
                                    />

                                </template>

                            </Column>

                        </DataTable>

                    </template>

                </Card>

            </div>









            <!-- ========================================= -->
            <!-- HIERARCHIE -->
            <!-- ========================================= -->

            <div class="2xl:col-span-2">

                <Card class="rounded-3xl border-0 shadow-sm">

                    <template #content>

                        <!-- TOP -->
                        <div class="flex items-center justify-between mb-6">

                            <div>

                                <h2 class="text-2xl font-bold text-slate-900">
                                    Hiérarchie Active
                                </h2>

                                <p class="text-slate-500 mt-1">
                                    SUP → TC
                                </p>

                            </div>

                            <div
                                class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center"
                            >
                                <i class="pi pi-sitemap text-emerald-600 text-xl"></i>
                            </div>

                        </div>





                        <!-- ACCORDION -->
                        <Accordion>

                            <AccordionPanel
                                v-for="item in hierarchy"
                                :key="item.sup.id"
                                :value="item.sup.id"
                            >

                                <!-- HEADER -->
                                <AccordionHeader>

                                    <div class="flex items-center gap-4 w-full">

                                        <Avatar
                                            :label="`${item.sup.employee.first_name[0]}${item.sup.employee.last_name[0]}`"
                                            shape="circle"
                                            class="bg-emerald-600 text-white"
                                        />

                                        <div class="flex-1">

                                            <div class="font-semibold text-slate-900">
                                                {{ item.sup.employee.first_name }}
                                                {{ item.sup.employee.last_name }}
                                            </div>

                                            <div class="text-sm text-slate-500">
                                                {{ item.sup.campaign.name }}
                                            </div>

                                        </div>

                                        <Tag
                                            :value="`${item.tcs.length} TC`"
                                            severity="info"
                                            rounded
                                        />

                                    </div>

                                </AccordionHeader>





                                <!-- CONTENT -->
                                <AccordionContent>

                                    <div
                                        v-if="item.tcs.length"
                                        class="space-y-3"
                                    >

                                        <div
                                            v-for="tc in item.tcs"
                                            :key="tc.id"
                                            class="bg-slate-50 border border-slate-200 rounded-2xl p-4"
                                        >

                                            <div class="flex items-center justify-between">

                                                <div class="flex items-center gap-3">

                                                    <Avatar
                                                        :label="`${tc.employee.first_name[0]}${tc.employee.last_name[0]}`"
                                                        shape="circle"
                                                        class="bg-blue-600 text-white"
                                                    />

                                                    <div>

                                                        <div class="font-semibold">
                                                            {{ tc.employee.first_name }}
                                                            {{ tc.employee.last_name }}
                                                        </div>

                                                        <div class="text-sm text-slate-500">
                                                            {{ tc.employee.email }}
                                                        </div>

                                                    </div>

                                                </div>

                                                <Tag
                                                    value="Actif"
                                                    severity="success"
                                                    rounded
                                                />

                                            </div>

                                        </div>

                                    </div>





                                    <!-- EMPTY -->
                                    <div
                                        v-else
                                        class="text-center py-8 text-slate-500"
                                    >

                                        Aucun TC affecté

                                    </div>

                                </AccordionContent>

                            </AccordionPanel>

                        </Accordion>

                    </template>

                </Card>

            </div>

        </div>

    </div>

</div>

</AppLayout>
</template>