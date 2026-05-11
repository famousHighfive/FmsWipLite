<!-- resources/js/Pages/Campaigns/InactiveCampaigns.vue -->

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

import AppLayout from '@/Layouts/AppLayout.vue';

import Button from 'primevue/button';
import Card from 'primevue/card';
import Tag from 'primevue/tag';
import Menu from 'primevue/menu';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Toast from 'primevue/toast';

import { useToast } from 'primevue/usetoast';

const props = defineProps({
    campaigns: Array
});

const toast = useToast();

const searchQuery = ref('');

const selectedCampaign = ref(null);
const deleteDialog = ref(false);

const menu = ref();
const menuItems = ref([]);

const filteredCampaigns = computed(() => {
    return props.campaigns.filter(campaign => {
        const search = searchQuery.value.toLowerCase();

        return (
            campaign.name.toLowerCase().includes(search) ||
            campaign.description?.toLowerCase().includes(search)
        );
    });
});

const toggleMenu = (event, campaign) => {
    event.stopPropagation();

    selectedCampaign.value = campaign;

    menuItems.value = [
        {
            label: 'Supprimer',
            icon: 'pi pi-trash',
            class: 'text-red-500',
            command: () => openDeleteDialog(campaign)
        }
    ];

    menu.value.toggle(event);
};

const openDeleteDialog = (campaign) => {
    selectedCampaign.value = campaign;
    deleteDialog.value = true;
};

const deleteCampaign = () => {
    router.delete(`/campaigns/${selectedCampaign.value.id}`, {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Succès',
                detail: 'Campagne supprimée avec succès',
                life: 3000
            });

            deleteDialog.value = false;
        }
    });
};

const goToCampaign = (campaign) => {
    router.get(`/campaigns/${campaign.id}`);
};

const getSeverity = (status) => {
    switch (status) {
        case 'inactive':
            return 'warn';

        case 'terminee':
            return 'secondary';

        default:
            return 'contrast';
    }
};
</script>

<template>
    <AppLayout>
        <Toast />

        <div class="p-6 bg-slate-50 min-h-screen">

            <!-- HEADER -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

                <div>
                    <h1 class="text-3xl font-bold text-slate-900">
                        Campagnes inactives
                    </h1>

                    <p class="text-slate-500 mt-1">
                        {{ campaigns.length }} campagnes inactives 
                    </p>
                </div>

                <div class="w-full md:w-[350px]">
                    <InputText
                        v-model="searchQuery"
                        placeholder="Rechercher une campagne..."
                        class="w-full rounded-xl"
                    />
                </div>

            </div>

            <!-- EMPTY -->
            <div
                v-if="filteredCampaigns.length === 0"
                class="bg-white border border-slate-100 rounded-2xl p-10 text-center shadow-sm"
            >
                <i class="pi pi-folder text-5xl text-slate-300 mb-4"></i>

                <h2 class="text-xl font-semibold text-slate-700 mb-2">
                    Aucune campagne inactive
                </h2>

                <p class="text-slate-500">
                    Les campagnes inactives ou terminées apparaîtront ici.
                </p>
            </div>

            <!-- GRID -->
            <div
                v-else
                class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6"
            >

                <Card
                    v-for="campaign in filteredCampaigns"
                    :key="campaign.id"
                    class="rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer overflow-hidden"
                    @click="goToCampaign(campaign)"
                >

                    <template #content>

                        <!-- HEADER -->
                        <div class="flex justify-between items-start mb-5">

                            <div>
                                <h2 class="text-lg font-bold text-slate-900 line-clamp-1">
                                    {{ campaign.name }}
                                </h2>

                                <Tag
                                    :value="campaign.status.toUpperCase()"
                                    :severity="getSeverity(campaign.status)"
                                    rounded
                                    class="mt-3"
                                />
                            </div>

                            <Button
                                icon="pi pi-ellipsis-v"
                                severity="secondary"
                                text
                                rounded
                                @click="toggleMenu($event, campaign)"
                            />

                        </div>

                        <!-- DESCRIPTION -->
                        <p class="text-slate-500 text-sm leading-relaxed line-clamp-3 min-h-[65px]">
                            {{ campaign.description || 'Aucune description disponible.' }}
                        </p>

                        <!-- DATES -->
                        <div class="mt-5 space-y-2">

                            <div class="flex items-center gap-2 text-sm text-slate-600">
                                <i class="pi pi-calendar"></i>

                                <span>
                                    {{ campaign.start_date }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2 text-sm text-slate-600">
                                <i class="pi pi-calendar-clock"></i>

                                <span>
                                    {{ campaign.end_date || 'Non définie' }}
                                </span>
                            </div>

                        </div>

                        <!-- FOOTER -->
                        <div class="mt-5 flex items-center justify-between">

                            <div class="flex items-center gap-2 text-slate-600">
                                <i class="pi pi-users"></i>

                                <span class="font-medium">
                                    {{ campaign.assignments_count || 0 }}
                                </span>
                            </div>

                            <Button
                                label="Voir détail"
                                icon="pi pi-arrow-right"
                                size="small"
                                outlined
                            />

                        </div>

                    </template>

                </Card>

            </div>

            <!-- MENU -->
            <Menu
                ref="menu"
                :model="menuItems"
                popup
            />

            <!-- DELETE DIALOG -->
            <Dialog
                v-model:visible="deleteDialog"
                modal
                header="Suppression"
                :style="{ width: '400px' }"
            >

                <div class="flex gap-4">

                    <i class="pi pi-exclamation-triangle text-red-500 text-3xl"></i>

                    <div>
                        <p class="font-medium text-slate-800">
                            Supprimer cette campagne ?
                        </p>

                        <p class="text-slate-500 text-sm mt-2">
                            Cette action est irréversible.
                        </p>
                    </div>

                </div>

                <template #footer>

                    <Button
                        label="Annuler"
                        severity="secondary"
                        text
                        @click="deleteDialog = false"
                    />

                    <Button
                        label="Supprimer"
                        severity="danger"
                        icon="pi pi-trash"
                        @click="deleteCampaign"
                    />

                </template>

            </Dialog>

        </div>
    </AppLayout>
</template>