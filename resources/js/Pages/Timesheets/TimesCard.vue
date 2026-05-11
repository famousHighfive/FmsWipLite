<script setup>
import { computed } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import Calendar from "primevue/calendar";
import Dropdown from "primevue/dropdown";

const props = defineProps({
    data: Object, // Reçoit les infos de l'employé, les dates et le mode (simple ou bulk)
});

const emit = defineEmits(["close"]);

/**
 * LOGIQUE DE VERROUILLAGE
 * Détermine si le formulaire doit être désactivé selon le rôle et le statut.
 */
const isLocked = computed(() => {
    // Bloque si c'est un Téléconseiller ou si la feuille est déjà soumise
    if (props.data.role === "tc") return true; 
    if (props.data.status === "soumis") return true;
    return false;
});

/**
 * GÉNÉRATION DES OPTIONS DE DATE
 * Prépare la liste déroulante pour le mode de saisie groupée.
 */
const dateOptions = computed(() => {
    if (!props.data.isBulk || !props.data.all_dates) return [];
    return props.data.all_dates.map((d) => ({
        // Formate la date en texte lisible (ex: "lundi 20 mai")
        label: new Intl.DateTimeFormat("fr-FR", { weekday: "long", day: "numeric", month: "long" }).format(new Date(d)),
        value: d,
    }));
});

/**
 * INITIALISATION DU FORMULAIRE
 * Prépare l'objet de données réactif pour Inertia.
 */
const form = useForm({
    timesheet_id: props.data.entry?.timesheet_id || props.data.timesheet_id,
    timesheet_ids: props.data.isBulk ? props.data.timesheet_ids : null, // Liste d'IDs pour le mode groupé
    date: props.data.date,
    // Convertit les strings HH:mm en objets Date pour le composant Calendar de PrimeVue
    check_in: props.data.entry?.check_in ? new Date(`2000-01-01 ${props.data.entry.check_in}`) : null,
    check_out: props.data.entry?.check_out ? new Date(`2000-01-01 ${props.data.entry.check_out}`) : null,
    break_duration: props.data.entry?.break_duration || 0,
    comment: props.data.entry?.comment || "",
});

/**
 * FORMATAGE DE L'HEURE
 * Convertit un objet Date en chaîne "HH:mm" compatible avec la base de données.
 */
const formatToHHmm = (date) => {
    if (!date) return null;
    const d = new Date(date);
    return (d.getHours().toString().padStart(2, "0") + ":" + d.getMinutes().toString().padStart(2, "0"));
};

/**
 * SOUMISSION DU FORMULAIRE
 * Transforme les données et les envoie au serveur via une requête POST.
 */
const submit = () => {
    if (isLocked.value) return; // Sécurité anti-clic si verrouillé

    form.transform((data) => ({
        ...data,
        // S'assure que le backend reçoit toujours un tableau d'IDs (même pour un seul employé)
        timesheet_ids: props.data.isBulk ? props.data.timesheet_ids : [props.data.timesheet_id],
        check_in: formatToHHmm(data.check_in),
        check_out: formatToHHmm(data.check_out),
    })).post("/timesheet-entries", {
        preserveScroll: true,
        onSuccess: () => {
            // Rafraîchit les données du calendrier et ferme le modal
            router.reload({ only: ["calendar"] });
            emit("close");
        },
    });
};
</script>

<template>
    <div class="p-fluid grid gap-4">
        <!-- Bandeau d'information contextuel -->
        <div class="mb-1 p-3 rounded bg-blue-50 border border-blue-100 shadow-sm">
            <div class="font-black text-blue-900 text-sm uppercase"> {{ data.employee_name }} </div>
            <div class="text-[10px] text-blue-600 font-bold uppercase" v-if="!data.isBulk"> Date : {{ data.date }} </div>
            <div class="text-[10px] text-orange-600 font-bold uppercase" v-else> Saisie Multiple </div>
        </div>

        <!-- SÉLECTEUR DE DATE (Mode Multiple uniquement) -->
        <div v-if="data.isBulk" class="field">
            <label class="font-black text-[10px] uppercase text-gray-500 mb-1 block">1. Choisir le jour à remplir</label>
            <Dropdown v-model="form.date" :options="dateOptions" optionLabel="label" optionValue="value" placeholder="Sélectionner un jour" class="w-full font-bold border-blue-300" />
        </div>

        <!-- SAISIE DES HORAIRES -->
        <div class="field">
            <label class="font-black text-[10px] uppercase text-gray-500 mb-1 block">{{ data.isBulk ? "2. Heure Arrivée" : "Heure Arrivée" }}</label>
            <Calendar v-model="form.check_in" timeOnly hourFormat="24" :disabled="isLocked" placeholder="--:--" />
        </div>

        <div class="field">
            <label class="font-black text-[10px] uppercase text-gray-500 mb-1 block">Heure Départ</label>
            <Calendar v-model="form.check_out" timeOnly hourFormat="24" :disabled="isLocked" placeholder="--:--" />
        </div>

        <!-- SAISIE DE LA PAUSE -->
        <div class="field">
            <label class="font-black text-[10px] uppercase text-gray-500 mb-1 block">Pause (min)</label>
            <InputText v-model="form.break_duration" type="number" :disabled="isLocked" />
        </div>

        <!-- ACTIONS -->
        <div class="flex justify-end gap-2 mt-4 pt-3 border-t">
            <Button label="Annuler" class="p-button-text p-button-secondary text-xs" @click="$emit('close')" />
            <Button v-if="!isLocked" :label="data.isBulk ? 'Appliquer au groupe' : 'Enregistrer'" icon="pi pi-check" class="p-button-sm font-bold" :loading="form.processing" @click="submit" />
        </div>
    </div>
</template>
