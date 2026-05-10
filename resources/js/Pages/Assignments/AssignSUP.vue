<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

import AppLayout from '@/Layouts/AppLayout.vue'

import Card from 'primevue/card'
import Button from 'primevue/button'
import Select from 'primevue/select'
import Avatar from 'primevue/avatar'
import Tag from 'primevue/tag'
import Toast from 'primevue/toast'
import Toolbar from 'primevue/toolbar'
import DataView from 'primevue/dataview'
import Divider from 'primevue/divider'

import { useToast } from 'primevue/usetoast'

/**
 * ================================
 * PROPS
 * ================================
 */
const props = defineProps({
    unassignedSUPs: Array,
    assignedSUPs: Array,
    cpAssignments: Array
})

console.log(props.cpAssignments);


const toast = useToast()

/**
 * ================================
 * STATE GLOBAL CP (UX améliorée)
 * ================================
 */
const selectedCP = ref(null)

/**
 * ================================
 * FILTRES
 * ================================
 */
const globalFilter = ref('')

/**
 * ================================
 * SUP FILTERED
 * ================================
 */
const filteredSUPs = computed(() => {
    if (!globalFilter.value) return props.unassignedSUPs

    return props.unassignedSUPs.filter(s =>
        s.first_name.toLowerCase().includes(globalFilter.value.toLowerCase()) ||
        s.last_name.toLowerCase().includes(globalFilter.value.toLowerCase())
    )
})

/**
 * ================================
 * ASSIGN SUP
 * ================================
 */
const assignSUP = (sup) => {

    if (!selectedCP.value) {
        toast.add({
            severity: 'warn',
            summary: 'CP requis',
            detail: 'Sélectionne un Chef de Plateau',
            life: 3000
        })
        return
    }

    router.post('/assign/sup', {
        employee_id: sup.id,
        cp_assignment_id: selectedCP.value.id
    }, {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Succès',
                detail: 'SUP affecté',
                life: 3000
            })
        }
    })
}
</script>

<template>
<AppLayout>
<Toast />

<div class="p-6 space-y-6 bg-slate-50 min-h-screen">

<!-- ============================= -->
<!-- HEADER TOOLBAR -->
<!-- ============================= -->
<Toolbar class="rounded-2xl shadow-sm">

    <template #start>
        <div>
            <h1 class="text-xl font-bold">Gestion des SUP</h1>
            <p class="text-sm text-gray-500">
                Affectation hiérarchique SUP → CP → Campagne
            </p>
        </div>
    </template>

   <template #center>
    <Select
        v-model="selectedCP"
        :options="cpAssignments"
        placeholder="Sélectionner un CP"
        class="w-72"
    >
        <!-- Affichage de l'élément une fois sélectionné -->
        <template #value="slotProps">
            <div v-if="slotProps.value" class="flex items-center">
                <span>
                    {{ slotProps.value.employee.first_name }} 
                    {{ slotProps.value.employee.last_name }} 
                    ({{ slotProps.value.campaign.name }})
                </span>
            </div>
            <span v-else>
                {{ slotProps.placeholder }}
            </span>
        </template>

        <!-- Affichage des options dans la liste déroulante -->
        <template #option="slotProps">
            <div class="flex flex-col">
                <span class="font-bold">
                    {{ slotProps.option.employee.first_name }} {{ slotProps.option.employee.last_name }}
                </span>
                <small class="text-gray-500">
                    Campagne : {{ slotProps.option.campaign.name }}
                </small>
            </div>
        </template>
    </Select>
</template>


    <template #end>
        <input
            v-model="globalFilter"
            placeholder="Rechercher SUP..."
            class="p-inputtext p-component w-64"
        />
    </template>

</Toolbar>

<!-- ============================= -->
<!-- GRID -->
<!-- ============================= -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

<!-- ============================= -->
<!-- SUP DISPONIBLES -->
<!-- ============================= -->
<Card class="shadow-sm rounded-2xl">

    <template #title>
        <div class="flex items-center justify-between">
            <span>SUP Disponibles</span>
            <Tag :value="unassignedSUPs.length" severity="info" />
        </div>
    </template>

    <template #content>

        <DataView :value="filteredSUPs">

            <template #list="slotProps">

                <div class="space-y-3">

                    <div
                        v-for="sup in slotProps.items"
                        :key="sup.id"
                        class="flex items-center justify-between p-4 border rounded-xl bg-white hover:shadow-md transition"
                    >

                        <!-- LEFT -->
                        <div class="flex items-center gap-3">

                            <Avatar
                                :label="sup.first_name[0] + sup.last_name[0]"
                                class="bg-blue-600 text-white"
                                shape="circle"
                            />

                            <div>
                                <div class="font-semibold">
                                    {{ sup.first_name }} {{ sup.last_name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ sup.email }}
                                </div>
                            </div>

                        </div>

                        <!-- RIGHT -->
                        <Button
                            label="Affecter"
                            icon="pi pi-check"
                            class="p-button-sm"
                            @click="assignSUP(sup)"
                        />

                    </div>

                </div>

            </template>

        </DataView>

    </template>

</Card>

<!-- ============================= -->
<!-- SUP AFFECTÉS -->
<!-- ============================= -->
<Card class="shadow-sm rounded-2xl">

    <template #title>
        <div class="flex items-center justify-between">
            <span>SUP Affectés</span>
            <Tag :value="assignedSUPs.length" severity="success" />
        </div>
    </template>

    <template #content>

        <DataView :value="assignedSUPs">

            <template #list="slotProps">

                <div class="space-y-3">

                    <div
                        v-for="a in slotProps.items"
                        :key="a.id"
                        class="p-4 border rounded-xl bg-white hover:shadow-md transition"
                    >

                        <div class="flex justify-between items-center">

                            <div>
                                <div class="font-semibold">
                                    {{ a.employee.first_name }} {{ a.employee.last_name }}
                                </div>

                                <div class="text-sm text-gray-500">
                                    CP: {{ a.manager?.first_name }} {{ a.manager?.last_name }}
                                </div>

                                <div class="text-sm text-gray-500">
                                    Campagne: {{ a.campaign?.name }}
                                </div>
                            </div>

                            <Tag value="Actif" severity="success" />

                        </div>

                    </div>

                </div>

            </template>

        </DataView>

    </template>

</Card>

</div>

</div>

</AppLayout>
</template>