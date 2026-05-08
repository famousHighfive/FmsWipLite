<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { router } from "@inertiajs/vue3";
import Button from "primevue/button";
import Tag from "primevue/tag";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import { Clock, User, Calendar, CheckCircle, ArrowLeft, FileText } from "lucide-vue-next";

const props = defineProps({
    timesheet: Object,
});

const getStatusSeverity = (status) => {
    switch (status) {
        case "valide":
            return "success";
        case "soumis":
            return "info";
        case "brouillon":
            return "secondary";
        default:
            return "warn";
    }
};

const getStatusLabel = (status) => {
    switch (status) {
        case "valide":
            return "Validée";
        case "soumis":
            return "En attente";
        case "brouillon":
            return "Brouillon";
        default:
            return status;
    }
};

const formatTime = (time) => {
    if (!time) return "—";
    return time.substring(0, 5);
};
</script>

<template>
    <AppLayout>
        <div class="space-y-6">
            <!-- HEADER -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Button
                        icon="pi pi-arrow-left"
                        rounded
                        variant="text"
                        severity="secondary"
                        @click="router.visit(route('timesheets.index'))"
                        class="!bg-white !shadow-sm"
                    />
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 tracking-tight">
                            Détail de la feuille de temps
                        </h2>
                        <p class="text-[10px] font-black text-blue-500 uppercase tracking-widest">
                            Période du {{ new Date(timesheet.period_start).toLocaleDateString() }} au {{ new Date(timesheet.period_end).toLocaleDateString() }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <Tag
                        :value="getStatusLabel(timesheet.status)"
                        :severity="getStatusSeverity(timesheet.status)"
                        class="!rounded-lg !px-3 !py-1 text-xs font-bold uppercase"
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- INFOS RÉCAPITULATIVES -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white/50 backdrop-blur-sm p-8 rounded-3xl border border-white shadow-sm">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                                <User class="w-5 h-5" />
                            </div>
                            <h3 class="text-lg font-black text-slate-800 tracking-tight">Collaborateur</h3>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nom complet</span>
                                <p class="font-bold text-slate-700">{{ timesheet.employee?.user?.name }}</p>
                            </div>
                            <div>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Matricule</span>
                                <p class="font-bold text-slate-700">{{ timesheet.employee?.matricule }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="timesheet.status === 'valide'" class="bg-gradient-to-br from-green-500 to-emerald-600 p-8 rounded-3xl text-white shadow-xl shadow-green-500/20">
                        <div class="flex items-center gap-3 mb-4">
                            <CheckCircle class="w-6 h-6" />
                            <h4 class="text-lg font-black tracking-tight">Validation</h4>
                        </div>
                        <div class="space-y-3">
                            <p class="text-green-50 text-sm font-medium">Validée par : <span class="font-bold text-white">{{ timesheet.validator?.user?.name }}</span></p>
                            <p class="text-green-50 text-sm font-medium">Le : <span class="font-bold text-white">{{ new Date(timesheet.validated_at).toLocaleString() }}</span></p>
                        </div>
                    </div>
                </div>

                <!-- TABLEAU DES ENTRÉES -->
                <div class="lg:col-span-2">
                    <div class="card !border-0 !shadow-sm !rounded-2xl overflow-hidden bg-white/50 backdrop-blur-sm">
                        <DataTable :value="timesheet.entries" class="p-datatable-sm">
                            <template #header>
                                <div class="flex items-center gap-2 py-2 px-4">
                                    <Clock class="w-4 h-4 text-blue-600" />
                                    <span class="text-slate-600 font-bold">Détail quotidien</span>
                                </div>
                            </template>

                            <Column header="Date" sortable field="date">
                                <template #body="{ data }">
                                    <div class="font-bold text-slate-700">
                                        {{ new Date(data.date).toLocaleDateString('fr-FR', { weekday: 'short', day: 'numeric', month: 'short' }) }}
                                    </div>
                                </template>
                            </Column>

                            <Column header="Entrée" field="check_in">
                                <template #body="{ data }">
                                    <span class="text-sm font-medium text-slate-600">{{ formatTime(data.check_in) }}</span>
                                </template>
                            </Column>

                            <Column header="Sortie" field="check_out">
                                <template #body="{ data }">
                                    <span class="text-sm font-medium text-slate-600">{{ formatTime(data.check_out) }}</span>
                                </template>
                            </Column>

                            <Column header="Pause" field="break_duration">
                                <template #body="{ data }">
                                    <span class="text-sm text-slate-500">{{ data.break_duration }} min</span>
                                </template>
                            </Column>

                            <Column header="Total" field="total_hours">
                                <template #body="{ data }">
                                    <span class="font-black text-blue-600">{{ data.total_hours }}h</span>
                                </template>
                            </Column>

                            <Column header="Statut">
                                <template #body="{ data }">
                                    <Tag v-if="data.absence_type" :value="data.absence_type" severity="warn" class="!text-[10px]" />
                                    <Tag v-else value="Présent" severity="success" class="!text-[10px]" />
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
