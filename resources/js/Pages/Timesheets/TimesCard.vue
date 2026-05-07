<script setup>
import { computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import Calendar from "primevue/calendar";

const props = defineProps({
    data: Object, // Reçoit timesheet_id, employee_name, date, entry, status
});

const emit = defineEmits(["close"]);

// --- LOGIQUE DE VERROUILLAGE ---
// Si le statut global de la timesheet est 'soumis', on bloque tout
const isLocked = computed(() => props.data.status === 'soumis');

const form = useForm({
    timesheet_id: props.data.entry?.timesheet_id || props.data.timesheet_id,
    employee_id: props.data.employee_id,
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

// Utilitaire pour transformer l'objet Date en string "HH:mm" pour MySQL
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
        check_in: formatToHHmm(data.check_in),
        check_out: formatToHHmm(data.check_out),
    })).post("/timesheet-entries", {
        onSuccess: () => emit("close"),
    });
};
</script>

<template>
    <div class="p-fluid grid gap-4">
        <!-- En-tête avec rappel du statut -->
        <div class="mb-2 p-3 rounded-lg bg-gray-50 border flex justify-between items-center">
            <div>
                <div class="font-bold text-lg text-gray-800">
                    {{ data.employee_name }}
                </div>
                <div class="text-xs text-gray-500 font-medium">
                    Journée du : {{ data.date }}
                </div>
            </div>
            <div v-if="isLocked" class="flex items-center text-emerald-600 font-bold text-xs bg-emerald-50 px-2 py-1 rounded border border-emerald-200">
                <i class="pi pi-lock mr-1"></i> SOUMIS
            </div>
        </div>

        <!-- Alerte si verrouillé -->
        <div v-if="isLocked" class="p-3 bg-amber-50 border border-amber-200 rounded text-amber-700 text-xs flex items-start gap-2">
            <i class="pi pi-exclamation-triangle mt-0.5"></i>
            <span>Cette feuille de temps a été soumise. Les modifications sont désactivées.</span>
        </div>

        <div class="field">
            <label for="check_in" class="font-semibold text-sm">Heure d'arrivée</label>
            <Calendar
                id="check_in"
                v-model="form.check_in"
                timeOnly
                hourFormat="24"
                placeholder="--:--"
                :disabled="isLocked"
            />
        </div>

        <div class="field">
            <label for="check_out" class="font-semibold text-sm">Heure de départ</label>
            <Calendar
                id="check_out"
                v-model="form.check_out"
                timeOnly
                hourFormat="24"
                placeholder="--:--"
                :disabled="isLocked"
            />
        </div>

        <div class="field">
            <label for="break" class="font-semibold text-sm">Durée de pause (min)</label>
            <InputText 
                id="break" 
                v-model="form.break_duration" 
                type="number" 
                :disabled="isLocked" 
            />
        </div>

        <div class="field">
            <label for="comment" class="font-semibold text-sm">Commentaire / Justification</label>
            <InputText
                id="comment"
                v-model="form.comment"
                placeholder="Ex: Retard transport, RDV médical..."
                :disabled="isLocked"
            />
        </div>

        <!-- Pied de formulaire dynamique -->
        <div class="flex justify-end gap-2 mt-6">
            <Button
                label="Fermer"
                icon="pi pi-times"
                class="p-button-text p-button-secondary"
                @click="$emit('close')"
            />
            <Button
                v-if="!isLocked"
                label="Enregistrer les heures"
                icon="pi pi-save"
                class="p-button-primary"
                :loading="form.processing"
                @click="submit"
            />
            <Button
                v-else
                label="Modification impossible"
                icon="pi pi-lock"
                class="p-button-secondary"
                disabled
            />
        </div>
    </div>
</template>

<style scoped>
/* Style pour marquer visuellement les champs désactivés */
:deep(.p-disabled) {
    opacity: 0.8;
    background-color: #f9fafb !important;
}
</style>
