<script setup>
import { computed } from "vue";
import { useForm,  router } from "@inertiajs/vue3";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import Calendar from "primevue/calendar";
import Dropdown from "primevue/dropdown";
const props = defineProps({
    data: Object, // Contient employee_id, employee_ids (bulk), employee_name, date, entry, status, role, isBulk
});

const emit = defineEmits(["close"]);

// --- LOGIQUE DE VERROUILLAGE SELON TA MATRICE ---
const isLocked = computed(() => {
    // 1. Le Téléconseiller (TC) est TOUJOURS en lecture seule
    if (props.data.role === "TC") return true;
    // 2. Si le statut est 'soumis', personne ne modifie (même le SUP ou CP)
    if (props.data.status === "soumis") return true;
    // 3. Sinon (Brouillon/Valide), le SUP et le CP peuvent modifier
    return false;
});
// Génération des options pour le choix de la date (Mode Multi)
const dateOptions = computed(() => {
    if (!props.data.isBulk || !props.data.all_dates) return [];
    return props.data.all_dates.map((d) => ({
        label: new Intl.DateTimeFormat("fr-FR", {
            weekday: "long",
            day: "numeric",
            month: "long",
        }).format(new Date(d)),
        value: d,
    }));
});
// --- INITIALISATION DU FORMULAIRE ---
const form = useForm({
    // Cas simple
    timesheet_id: props.data.entry?.timesheet_id || props.data.timesheet_id,
    // Cas groupé
    timesheet_ids: props.data.isBulk ? props.data.timesheet_ids : null,

    date: props.data.date,
    check_in: props.data.entry?.check_in
        ? new Date(`2000-01-01 ${props.data.entry.check_in}`)
        : null,
    check_out: props.data.entry?.check_out
        ? new Date(`2000-01-01 ${props.data.entry.check_out}`)
        : null,
    break_duration: props.data.entry?.break_duration || 0,
    comment: props.data.entry?.comment || "",
});

// Transformation des objets Date en format HH:mm pour Laravel
const formatToHHmm = (date) => {
    if (!date) return null;
    const d = new Date(date);
    return (
        d.getHours().toString().padStart(2, "0") +
        ":" +
        d.getMinutes().toString().padStart(2, "0")
    );
};

const submit = () => {
    if (isLocked.value) return;

    form.transform((data) => ({
        ...data,
        timesheet_ids: props.data.isBulk
            ? props.data.timesheet_ids
            : [props.data.timesheet_id],
        check_in: formatToHHmm(data.check_in),
        check_out: formatToHHmm(data.check_out),
    })).post("/timesheet-entries", {
        preserveScroll: true,

        onSuccess: () => {
            router.reload({ only: ["calendar"] });
            emit("close");
        },
    });
};
</script>

<template>
    <div class="p-fluid grid gap-4">
        <!-- Info contextuelle -->
        <div
            class="mb-1 p-3 rounded bg-blue-50 border border-blue-100 shadow-sm"
        >
            <div class="font-black text-blue-900 text-sm uppercase">
                {{ data.employee_name }}
            </div>
            <div
                class="text-[10px] text-blue-600 font-bold uppercase"
                v-if="!data.isBulk"
            >
                Date : {{ data.date }}
            </div>
            <div class="text-[10px] text-orange-600 font-bold uppercase" v-else>
                Saisie Multiple
            </div>
        </div>

        <!-- SÉLECTEUR DE DATE (VISIBLE SEULEMENT EN MODE MULTIPLE) -->
        <div v-if="data.isBulk" class="field">
            <label
                class="font-black text-[10px] uppercase text-gray-500 mb-1 block"
                >1. Choisir le jour à remplir</label
            >
            <Dropdown
                v-model="form.date"
                :options="dateOptions"
                optionLabel="label"
                optionValue="value"
                placeholder="Sélectionner un jour de la semaine"
                class="w-full font-bold border-blue-300"
            />
        </div>

        <div class="field">
            <label
                class="font-black text-[10px] uppercase text-gray-500 mb-1 block"
                >{{ data.isBulk ? "2. Heure Arrivée" : "Heure Arrivée" }}</label
            >
            <Calendar
                v-model="form.check_in"
                timeOnly
                hourFormat="24"
                :disabled="isLocked"
                placeholder="--:--"
            />
        </div>

        <div class="field">
            <label
                class="font-black text-[10px] uppercase text-gray-500 mb-1 block"
                >Heure Départ</label
            >
            <Calendar
                v-model="form.check_out"
                timeOnly
                hourFormat="24"
                :disabled="isLocked"
                placeholder="--:--"
            />
        </div>

        <div class="field">
            <label
                class="font-black text-[10px] uppercase text-gray-500 mb-1 block"
                >Pause (min)</label
            >
            <InputText
                v-model="form.break_duration"
                type="number"
                :disabled="isLocked"
            />
        </div>

        <div class="flex justify-end gap-2 mt-4 pt-3 border-t">
            <Button
                label="Annuler"
                class="p-button-text p-button-secondary text-xs"
                @click="$emit('close')"
            />
            <Button
                v-if="!isLocked"
                :label="data.isBulk ? 'Appliquer au groupe' : 'Enregistrer'"
                icon="pi pi-check"
                class="p-button-sm font-bold"
                :loading="form.processing"
                @click="submit"
            />
        </div>
    </div>
</template>

<style scoped>
/* Pour uniformiser avec le reste du calendrier */
:deep(.p-inputtext) {
    font-size: 0.85rem;
    font-weight: 600;
}
:deep(.p-disabled) {
    background-color: #fcfcfc !important;
    opacity: 0.7;
}
</style>
