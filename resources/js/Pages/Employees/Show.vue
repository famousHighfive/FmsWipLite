<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { router } from "@inertiajs/vue3";
import Tag from "primevue/tag";
import Button from "primevue/button";
import { User, Briefcase, Calendar, Mail, Phone, MapPin, History, ArrowLeft } from "lucide-vue-next";

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
// NAVIGATION
// ---------------------------------------------------------
const goBack = () => router.visit(route("employees.index"));
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
                        @click="goBack"
                        class="!bg-white !shadow-sm"
                    />
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 tracking-tight">
                            {{ employee.first_name }} {{ employee.last_name }}
                        </h2>
                        <p class="text-[10px] font-black text-blue-500 uppercase tracking-widest">{{ employee.matricule }}</p>
                    </div>
                </div>

                <!-- Droite — Statut + Historique -->
                <div class="flex items-center gap-3">
                    <Tag
                        :value="employee.status"
                        :severity="getStatusSeverity(employee.status)"
                        class="!rounded-lg !px-3 !py-1 text-xs font-bold uppercase"
                    />
                    <!-- <Button
                        icon="pi pi-history"
                        label="Voir l'historique"
                        variant="outlined"
                        severity="info"
                        @click="router.visit(route('employees.history', employee.id),)"
                    /> -->
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- ======================================= -->
                <!-- COLONNE GAUCHE — Infos personnelles     -->
                <!-- ======================================= -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Infos personnelles -->
                    <div class="bg-white/50 backdrop-blur-sm p-8 rounded-3xl border border-white shadow-sm">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                                <User class="w-5 h-5" />
                            </div>
                            <h3 class="text-lg font-black text-slate-800 tracking-tight">Informations personnelles</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-1">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                    <User class="w-3 h-3" /> Prénom
                                </span>
                                <p class="font-bold text-slate-700">{{ employee.first_name }}</p>
                            </div>
                            <div class="space-y-1">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                    <User class="w-3 h-3" /> Nom
                                </span>
                                <p class="font-bold text-slate-700">{{ employee.last_name }}</p>
                            </div>
                            <div class="space-y-1">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                    <Calendar class="w-3 h-3" /> Date de naissance
                                </span>
                                <p class="font-bold text-slate-700">
                                    {{ employee.birth_date ? new Date(employee.birth_date).toLocaleDateString("fr-FR") : "—" }}
                                </p>
                            </div>
                            <div class="space-y-1">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                    <Mail class="w-3 h-3" /> Email
                                </span>
                                <p class="font-bold text-slate-700">{{ employee.email }}</p>
                            </div>
                            <div class="space-y-1">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                    <Phone class="w-3 h-3" /> Téléphone
                                </span>
                                <p class="font-bold text-slate-700">{{ employee.phone ?? "—" }}</p>
                            </div>
                            <div class="space-y-1">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                    <MapPin class="w-3 h-3" /> Adresse
                                </span>
                                <p class="font-bold text-slate-700">{{ employee.address ?? "—" }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Infos professionnelles -->
                    <div class="bg-white/50 backdrop-blur-sm p-8 rounded-3xl border border-white shadow-sm">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                                <Briefcase class="w-5 h-5" />
                            </div>
                            <h3 class="text-lg font-black text-slate-800 tracking-tight">Informations professionnelles</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-1">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Poste</span>
                                <p class="font-bold text-slate-700">{{ employee.position?.name ?? "—" }}</p>
                            </div>
                            <div class="space-y-1">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Salaire de base</span>
                                <p class="font-bold text-slate-700 text-lg text-indigo-600">{{ employee.salary_base }} XOF</p>
                            </div>
                            <div class="space-y-1">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Date d'entrée</span>
                                <p class="font-bold text-slate-700">
                                    {{ new Date(employee.created_at).toLocaleDateString("fr-FR") }}
                                </p>
                            </div>
                            <div class="space-y-1">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Compte utilisateur</span>
                                <p class="font-bold text-slate-700">{{ employee.user?.name ?? "Aucun compte lié" }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ======================================= -->
                <!-- COLONNE DROITE — Actions rapides        -->
                <!-- ======================================= -->
                <div class="space-y-6">
                    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-8 rounded-3xl text-white shadow-xl shadow-blue-500/20">
                        <h4 class="text-lg font-black mb-2 tracking-tight">Actions rapides</h4>
                        <p class="text-blue-100 text-sm mb-6 font-medium">Gérez le dossier de cet employé en un clic.</p>
                        
                        <div class="space-y-3">
                            <Button label="Modifier le profil" icon="pi pi-pencil" class="!w-full !bg-white/10 !border-white/20 !text-white !rounded-xl font-bold hover:!bg-white/20" @click="router.visit(route('employees.edit', employee.id))" />
                            <Button label="Gérer les accès" icon="pi pi-lock" class="!w-full !bg-white/10 !border-white/20 !text-white !rounded-xl font-bold hover:!bg-white/20" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
