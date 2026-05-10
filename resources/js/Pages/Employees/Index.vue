<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, watch } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";

import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Button from "primevue/button";
import Toolbar from "primevue/toolbar";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import InputText from "primevue/inputtext";
import Tag from "primevue/tag";
import Dialog from "primevue/dialog";
import Select from "primevue/select";
import DatePicker from "primevue/datepicker";
import InputNumber from "primevue/inputnumber";
import Message from "primevue/message";
import Toast from "primevue/toast";
import ConfirmDialog from "primevue/confirmdialog";
import { FilterMatchMode } from '@primevue/core/api';

// ---------------------------------------------------------
// PROPS
// ---------------------------------------------------------
const props = defineProps({
    employees: Array, // Reçu comme Array maintenant
    positions: Array,
    filters: Object,
});

// ---------------------------------------------------------
// INSTANCES
// ---------------------------------------------------------
const toast = useToast();
const confirm = useConfirm();
const dt = ref();

// ---------------------------------------------------------
// FILTRAGE AUTOMATIQUE PRIME VUE (Client-side)
// ---------------------------------------------------------
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

// ---------------------------------------------------------
// DIALOG — état
// ---------------------------------------------------------
const dialogVisible = ref(false);
const isEditing = ref(false);
const submitted = ref(false);

// ---------------------------------------------------------
// FORMULAIRE
// ---------------------------------------------------------
const form = useForm({
    id: null,
    matricule: "", // lecture seule en mode édition
    first_name: "",
    last_name: "",
    birth_date: null,
    phone: "",
    email: "",
    address: "",
    position_id: null,
    salary_base: null,
    status: "actif",
    user_id: null, // optionnel — lien vers un compte utilisateur
});

const statuses = [
    { label: "Actif", value: "actif" },
    { label: "Inactif", value: "inactif" },
    { label: "Suspendu", value: "suspendu" },
];

// ---------------------------------------------------------
// OUVRIR LE DIALOG — création
// ---------------------------------------------------------
const openCreate = () => {
    isEditing.value = false;
    submitted.value = false;
    form.reset();
    form.clearErrors();
    dialogVisible.value = true;
};

// ---------------------------------------------------------
// OUVRIR LE DIALOG — modification
// ---------------------------------------------------------
const openEdit = (employee) => {
    isEditing.value = true;
    submitted.value = false;
    form.clearErrors();

    form.id = employee.id;
    form.matricule = employee.matricule; // affiché en lecture seule
    form.first_name = employee.first_name;
    form.last_name = employee.last_name;
    form.birth_date = employee.birth_date
        ? new Date(employee.birth_date)
        : null;
    form.phone = employee.phone ?? "";
    form.email = employee.email;
    form.address = employee.address ?? "";
    form.position_id = employee.position_id;
    form.salary_base = parseFloat(employee.salary_base);
    form.status = employee.status;
    form.user_id = employee.user_id;

    dialogVisible.value = true;
};

// ---------------------------------------------------------
// FERMER LE DIALOG
// ---------------------------------------------------------
const closeDialog = () => {
    dialogVisible.value = false;
    submitted.value = false;
    form.reset();
    form.clearErrors();
};

// ---------------------------------------------------------
// SOUMETTRE LE FORMULAIRE
// ---------------------------------------------------------
const submitForm = () => {
    submitted.value = true;

    const data = {
        ...form.data(),
        birth_date: form.birth_date
            ? new Date(form.birth_date).toISOString().split("T")[0]
            : null,
    };

    if (isEditing.value) {
        form.transform(() => data).put(route("employees.update", form.id), {
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: "success",
                    summary: "Mis à jour",
                    detail: `${form.first_name} ${form.last_name} a été mis à jour.`,
                    life: 3000,
                });
                closeDialog();
            },
        });
    } else {
        form.transform(() => data).post(route("employees.store"), {
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: "success",
                    summary: "Créé",
                    detail: `L'employé a été créé avec succès.`,
                    life: 3000,
                });
                closeDialog();
            },
        });
    }
};

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
// EXPORT CSV
// ---------------------------------------------------------
const exportCSV = () => dt.value.exportCSV();

// ---------------------------------------------------------
// SUPPRESSION
// ---------------------------------------------------------
const confirmDelete = (employee) => {
    confirm.require({
        message: `Voulez-vous désactiver ${employee.first_name} ${employee.last_name} ?`,
        header: "Confirmation",
        icon: "pi pi-exclamation-triangle",
        rejectProps: {
            label: "Annuler",
            severity: "secondary",
            variant: "text",
        },
        acceptProps: { label: "Désactiver", severity: "danger" },
        accept: () => {
            router.delete(route("employees.destroy", employee.id), {
                preserveScroll: true,
                onSuccess: () =>
                    toast.add({
                        severity: "success",
                        summary: "Archivé",
                        detail: `${employee.first_name} ${employee.last_name} a été archivé.`,
                        life: 3000,
                    }),
            });
        },
    });
};
</script>

<template>
    <AppLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight">Gestion des Employés</h2>
                    <p class="text-slate-500 font-medium">Administrez vos collaborateurs et leurs informations.</p>
                </div>
                <div class="flex gap-3">
                    <Button
                        label="Exporter"
                        icon="pi pi-upload"
                        class="!bg-white !text-blue-600 !border-blue-100 !rounded-2xl !px-6 !py-3 !font-black !text-xs !uppercase !tracking-widest shadow-sm hover:!bg-blue-50 transition-all"
                        @click="exportCSV"
                    />
                    <Button
                        label="Nouvel employé"
                        icon="pi pi-plus"
                        class="!bg-gradient-to-r !from-blue-600 !to-indigo-600 !border-0 !rounded-2xl !px-6 !py-3 !font-black !text-xs !uppercase !tracking-widest !text-white shadow-xl shadow-blue-500/20 hover:-translate-y-0.5 transition-all"
                        @click="openCreate"
                    />
                </div>
            </div>

            <div class="card !border-0 !shadow-2xl !shadow-slate-200/50 !rounded-[2.5rem] overflow-hidden bg-white/70 backdrop-blur-md border border-white/50">
                <!-- DATATABLE -->
                <DataTable
                    ref="dt"
                    :value="employees"
                    v-model:filters="filters"
                    :globalFilterFields="['matricule', 'first_name', 'last_name', 'email', 'phone', 'position.name', 'status']"
                    :paginator="true"
                    :rows="10"
                    :rowsPerPageOptions="[10, 25, 50]"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    currentPageReportTemplate="Affichage de {first} à {last} sur {totalRecords} employés"
                    stripedRows
                    class="p-datatable-sm"
                >
                    <template #header>
                        <div class="flex justify-between items-center py-4 px-6 bg-white/30">
                            <span class="text-slate-800 font-black uppercase tracking-widest text-xs">Liste des employés</span>
                            <IconField iconPosition="left">
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText
                                    v-model="filters['global'].value"
                                    placeholder="Recherche automatique..."
                                    class="!rounded-2xl !bg-white/80 !border-slate-100 !text-sm focus:!ring-2 focus:!ring-blue-500/20"
                                />
                            </IconField>
                        </div>
                    </template>

                <Column
                    field="matricule"
                    header="Matricule"
                    sortable
                    style="min-width: 10rem"
                />
                <Column header="Nom complet" sortable style="min-width: 14rem">
                    <template #body="{ data }">
                        {{ data.first_name }} {{ data.last_name }}
                    </template>
                </Column>
                <Column header="Poste" style="min-width: 10rem">
                    <template #body="{ data }">
                        {{ data.position?.name ?? "—" }}
                    </template>
                </Column>
                <Column
                    field="email"
                    header="Email"
                    sortable
                    style="min-width: 14rem"
                />
                <Column
                    field="phone"
                    header="Téléphone"
                    style="min-width: 12rem"
                />
                <Column
                    field="status"
                    header="Statut"
                    sortable
                    style="min-width: 8rem"
                >
                    <template #body="{ data }">
                        <Tag
                            :value="data.status"
                            :severity="getStatusSeverity(data.status)"
                        />
                    </template>
                </Column>
                <Column
                    header="Actions"
                    :exportable="false"
                    style="min-width: 8rem"
                >
                    <template #body="{ data }">
                        <Button
                            icon="pi pi-eye"
                            variant="outlined"
                            rounded
                            class="mr-2"
                            @click="router.visit(route('employees.show', data.id))"
                        />
                        <Button
                            icon="pi pi-pencil"
                            variant="outlined"
                            rounded
                            class="mr-2"
                            @click="openEdit(data)"
                        />
                        <Button
                            v-if="data.status !== 'inactif'"
                            icon="pi pi-ban"
                            variant="outlined"
                            rounded
                            severity="danger"
                            @click="confirmDelete(data)"
                        />
                    </template>
                </Column>
            </DataTable>
        </div>

        <!-- ================================================ -->
        <!-- DIALOG — Formulaire Créer / Modifier             -->
        <!-- ================================================ -->
        <Dialog
            v-model:visible="dialogVisible"
            :header="isEditing ? 'Modifier l\'employé' : 'Nouvel employé'"
            :style="{ width: '650px' }"
            :modal="true"
            :closable="true"
            @hide="closeDialog"
            class="!rounded-[2.5rem] !bg-white/90 !backdrop-blur-xl !border-white/50 !shadow-2xl"
        >
            <div class="flex flex-col gap-4 pt-2">
                <!-- Erreur globale -->
                <Message v-if="form.hasErrors" severity="error" class="!rounded-2xl">
                    Veuillez corriger les erreurs ci-dessous.
                </Message>

                <!-- Matricule — lecture seule en mode édition -->
                <div v-if="isEditing" class="flex flex-col gap-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1"
                        >Matricule</label
                    >
                    <InputText
                        :value="form.matricule"
                        disabled
                        class="!bg-slate-100/50 !rounded-xl !border-slate-100 !text-sm cursor-not-allowed"
                        fluid
                    />
                    <small class="text-[10px] text-slate-400 font-medium ml-1"
                        >Le matricule est généré automatiquement.</small
                    >
                </div>

                <!-- Prénom / Nom -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1"
                            >Prénom <span class="text-red-500">*</span></label
                        >
                        <InputText
                            v-model="form.first_name"
                            :invalid="!!form.errors.first_name"
                            placeholder="Prénom"
                            class="!rounded-xl !bg-white/50 !border-slate-100 !text-sm focus:!ring-2 focus:!ring-blue-500/20"
                            fluid
                        />
                        <small
                            v-if="form.errors.first_name"
                            class="text-red-500 text-[10px] font-bold ml-1"
                            >{{ form.errors.first_name }}</small
                        >
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1"
                            >Nom <span class="text-red-500">*</span></label
                        >
                        <InputText
                            v-model="form.last_name"
                            :invalid="!!form.errors.last_name"
                            placeholder="Nom"
                            class="!rounded-xl !bg-white/50 !border-slate-100 !text-sm focus:!ring-2 focus:!ring-blue-500/20"
                            fluid
                        />
                        <small
                            v-if="form.errors.last_name"
                            class="text-red-500 text-[10px] font-bold ml-1"
                            >{{ form.errors.last_name }}</small
                        >
                    </div>
                </div>

                <!-- Email / Téléphone -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1"
                            >Email <span class="text-red-500">*</span></label
                        >
                        <InputText
                            v-model="form.email"
                            :invalid="!!form.errors.email"
                            placeholder="email@exemple.com"
                            class="!rounded-xl !bg-white/50 !border-slate-100 !text-sm focus:!ring-2 focus:!ring-blue-500/20"
                            fluid
                        />
                        <small v-if="form.errors.email" class="text-red-500 text-[10px] font-bold ml-1">{{
                            form.errors.email
                        }}</small>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Téléphone</label>
                        <InputText
                            v-model="form.phone"
                            placeholder="+229 00 00 00 00"
                            class="!rounded-xl !bg-white/50 !border-slate-100 !text-sm focus:!ring-2 focus:!ring-blue-500/20"
                            fluid
                        />
                    </div>
                </div>

                <!-- Date de naissance -->
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1"
                        >Date de naissance
                        <span class="text-red-500">*</span></label
                    >
                    <DatePicker
                        v-model="form.birth_date"
                        :invalid="!!form.errors.birth_date"
                        dateFormat="dd/mm/yy"
                        :maxDate="new Date()"
                        showIcon
                        inputClass="!rounded-xl !bg-white/50 !border-slate-100 !text-sm focus:!ring-2 focus:!ring-blue-500/20"
                        fluid
                    />
                    <small v-if="form.errors.birth_date" class="text-red-500 text-[10px] font-bold ml-1">{{
                        form.errors.birth_date
                    }}</small>
                </div>

                <!-- Adresse -->
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Adresse</label>
                    <InputText
                        v-model="form.address"
                        placeholder="Adresse complète"
                        fluid
                        class="!rounded-xl !bg-white/50 !border-slate-100 !text-sm focus:!ring-2 focus:!ring-blue-500/20"
                    />
                </div>

                <!-- Poste / Statut -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1"
                            >Poste <span class="text-red-500">*</span></label
                        >
                        <Select
                            v-model="form.position_id"
                            :options="positions"
                            optionLabel="name"
                            optionValue="id"
                            placeholder="Sélectionner un poste"
                            :invalid="!!form.errors.position_id"
                            class="!rounded-xl !bg-white/50 !border-slate-100 !text-sm focus:!ring-2 focus:!ring-blue-500/20"
                            fluid
                        />
                        <small
                            v-if="form.errors.position_id"
                            class="text-red-500 text-[10px] font-bold ml-1"
                            >{{ form.errors.position_id }}</small
                        >
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1"
                            >Statut <span class="text-red-500">*</span></label
                        >
                        <Select
                            v-model="form.status"
                            :options="statuses"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Sélectionner un statut"
                            :invalid="!!form.errors.status"
                            class="!rounded-xl !bg-white/50 !border-slate-100 !text-sm focus:!ring-2 focus:!ring-blue-500/20"
                            fluid
                        />
                        <small v-if="form.errors.status" class="text-red-500 text-[10px] font-bold ml-1">{{
                            form.errors.status
                        }}</small>
                    </div>
                </div>

                <!-- Salaire de base -->
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1"
                        >Salaire de base
                        <span class="text-red-500">*</span></label
                    >
                    <InputNumber
                        v-model="form.salary_base"
                        :invalid="!!form.errors.salary_base"
                        :min="0"
                        class="!rounded-xl !bg-white/50 !border-slate-100 !text-sm focus:!ring-2 focus:!ring-blue-500/20"
                        inputClass="!rounded-xl !bg-white/50 !border-slate-100 !text-sm focus:!ring-2 focus:!ring-blue-500/20"
                        fluid
                    />
                    <small
                        v-if="form.errors.salary_base"
                        class="text-red-500 text-[10px] font-bold ml-1"
                        >{{ form.errors.salary_base }}</small
                    >
                </div>
            </div>

            <!-- FOOTER -->
            <template #footer>
                <div class="flex gap-3 mt-4">
                    <Button
                        label="Annuler"
                        icon="pi pi-times"
                        class="flex-1 !bg-white !text-slate-500 !border-slate-100 !rounded-xl !py-3 !font-bold"
                        @click="closeDialog"
                    />
                    <Button
                        :label="isEditing ? 'Mettre à jour' : 'Créer l\'employé'"
                        icon="pi pi-check"
                        class="flex-1 !bg-gradient-to-r !from-blue-600 !to-indigo-600 !border-0 !rounded-xl !py-3 !font-bold !text-white shadow-lg shadow-blue-500/20"
                        :loading="form.processing"
                        @click="submitForm"
                    />
                </div>
            </template>
        </Dialog>
    </AppLayout>
</template>
