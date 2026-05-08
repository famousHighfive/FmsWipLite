<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { router } from "@inertiajs/vue3";
import Tag from "primevue/tag";
import Button from "primevue/button";
import Card from "primevue/card";
import Divider from "primevue/divider";

// ---------------------------------------------------------
// PROPS
// ---------------------------------------------------------
const props = defineProps({
    employee: Object,
});

// ---------------------------------------------------------
// STATUT — badge coloré
// ---------------------------------------------------------
const getStatusSeverity = (status) => {
    switch (status) {
        case "actif":
            return "success";
        case "suspendu":
            return "warn";
        case "inactif":
            return "danger";
        default:
            return null;
    }
};

// ---------------------------------------------------------
// LABELS LISIBLES pour les champs historique
// ---------------------------------------------------------
const fieldLabels = {
    first_name: "Prénom",
    last_name: "Nom",
    email: "Email",
    phone: "Téléphone",
    address: "Adresse",
    birth_date: "Date de naissance",
    position_id: "Poste",
    salary_base: "Salaire de base",
    status: "Statut",
    user_id: "Compte utilisateur",
};

const getFieldLabel = (field) => fieldLabels[field] ?? field;

// ---------------------------------------------------------
// NAVIGATION
// ---------------------------------------------------------
const goBack = () => router.visit(route("employees.index"));
</script>

<template>
    <AppLayout>
        <div class="flex flex-col gap-6">
            <!-- HEADER -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <Button
                        icon="pi pi-arrow-left"
                        variant="text"
                        severity="secondary"
                        @click="goBack"
                    />
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800">
                            {{ employee.first_name }} {{ employee.last_name }}
                        </h2>
                        <span class="text-slate-500 text-sm">{{
                            employee.matricule
                        }}</span>
                    </div>
                </div>

                <!-- Droite — Statut + Historique -->
                <div class="flex items-center gap-3">
                    <Tag
                        :value="employee.status"
                        :severity="getStatusSeverity(employee.status)"
                        class="text-sm"
                    />
                    <Button
                        icon="pi pi-history"
                        label="Voir l'historique"
                        variant="outlined"
                        severity="info"
                        @click="router.visit(route('employees.history', employee.id),)"
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- ======================================= -->
                <!-- COLONNE GAUCHE — Infos personnelles     -->
                <!-- ======================================= -->
                <div class="lg:col-span-2 flex flex-col gap-6">
                    <!-- Infos personnelles -->
                    <Card>
                        <template #title>
                            <div class="flex items-center gap-2 text-blue-700">
                                <i class="pi pi-user" />
                                <span>Informations personnelles</span>
                            </div>
                        </template>
                        <template #content>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex flex-col gap-1">
                                    <span
                                        class="text-xs text-slate-400 uppercase tracking-wide"
                                        >Prénom</span
                                    >
                                    <span class="font-medium text-slate-800">{{
                                        employee.first_name
                                    }}</span>
                                </div>

                                <div class="flex flex-col gap-1">
                                    <span
                                        class="text-xs text-slate-400 uppercase tracking-wide"
                                        >Nom</span
                                    >
                                    <span class="font-medium text-slate-800">{{
                                        employee.last_name
                                    }}</span>
                                </div>

                                <div class="flex flex-col gap-1">
                                    <span
                                        class="text-xs text-slate-400 uppercase tracking-wide"
                                        >Date de naissance</span
                                    >
                                    <span class="font-medium text-slate-800">
                                        {{
                                            employee.birth_date
                                                ? new Date(
                                                      employee.birth_date,
                                                  ).toLocaleDateString("fr-FR")
                                                : "—"
                                        }}
                                    </span>
                                </div>

                                <div class="flex flex-col gap-1">
                                    <span
                                        class="text-xs text-slate-400 uppercase tracking-wide"
                                        >Email</span
                                    >
                                    <span class="font-medium text-slate-800">{{
                                        employee.email
                                    }}</span>
                                </div>

                                <div class="flex flex-col gap-1">
                                    <span
                                        class="text-xs text-slate-400 uppercase tracking-wide"
                                        >Téléphone</span
                                    >
                                    <span class="font-medium text-slate-800">{{
                                        employee.phone ?? "—"
                                    }}</span>
                                </div>

                                <div class="flex flex-col gap-1">
                                    <span
                                        class="text-xs text-slate-400 uppercase tracking-wide"
                                        >Adresse</span
                                    >
                                    <span class="font-medium text-slate-800">{{
                                        employee.address ?? "—"
                                    }}</span>
                                </div>
                            </div>
                        </template>
                    </Card>

                    <!-- Infos professionnelles -->
                    <Card>
                        <template #title>
                            <div class="flex items-center gap-2 text-blue-700">
                                <i class="pi pi-briefcase" />
                                <span>Informations professionnelles</span>
                            </div>
                        </template>
                        <template #content>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex flex-col gap-1">
                                    <span
                                        class="text-xs text-slate-400 uppercase tracking-wide"
                                        >Matricule</span
                                    >
                                    <span
                                        class="font-mono font-bold text-blue-700"
                                        >{{ employee.matricule }}</span
                                    >
                                </div>

                                <div class="flex flex-col gap-1">
                                    <span
                                        class="text-xs text-slate-400 uppercase tracking-wide"
                                        >Poste</span
                                    >
                                    <span class="font-medium text-slate-800">{{
                                        employee.position?.name ?? "—"
                                    }}</span>
                                </div>

                                <div class="flex flex-col gap-1">
                                    <span
                                        class="text-xs text-slate-400 uppercase tracking-wide"
                                        >Salaire de base</span
                                    >
                                    <span class="font-medium text-slate-800">
                                        {{
                                            Number(
                                                employee.salary_base,
                                            ).toLocaleString("fr-FR")
                                        }}
                                        XOF
                                    </span>
                                </div>

                                <div class="flex flex-col gap-1">
                                    <span
                                        class="text-xs text-slate-400 uppercase tracking-wide"
                                        >Statut</span
                                    >
                                    <Tag
                                        :value="employee.status"
                                        :severity="
                                            getStatusSeverity(employee.status)
                                        "
                                    />
                                </div>

                                <div class="flex flex-col gap-1">
                                    <span
                                        class="text-xs text-slate-400 uppercase tracking-wide"
                                        >Date d'entrée</span
                                    >
                                    <span class="font-medium text-slate-800">
                                        {{
                                            new Date(
                                                employee.created_at,
                                            ).toLocaleDateString("fr-FR")
                                        }}
                                    </span>
                                </div>

                                <div class="flex flex-col gap-1">
                                    <span
                                        class="text-xs text-slate-400 uppercase tracking-wide"
                                        >Compte utilisateur</span
                                    >
                                    <span class="font-medium text-slate-800">
                                        {{
                                            employee.user?.name ??
                                            "Aucun compte lié"
                                        }}
                                    </span>
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
