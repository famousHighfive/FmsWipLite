<script setup>
import { computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import Calendar from "primevue/calendar";

const props = defineProps({
    data: Object, // Contient timesheet_id, employee_name, date, entry, status, role
});

const emit = defineEmits(["close"]);

// --- LOGIQUE DE VERROUILLAGE SELON TA MATRICE ---
const isLocked = computed(() => {
    // 1. Le Téléconseiller (TC) est TOUJOURS en lecture seule
    if (props.data.role === 'TC') return true;
    
    // 2. Si le statut est 'soumis', personne ne modifie (même le SUP ou CP)
    if (props.data.status === 'soumis') return true;
    
    // 3. Sinon (Brouillon/Valide), le SUP et le CP peuvent modifier
    return false;
});

const form = useForm({
    timesheet_id: props.data.entry?.timesheet_id || props.data.timesheet_id,
    employee_id: props.data.employee_id,
    date: props.data.date,
    check_in: props.data.entry?.check_in ? new Date(`2000-01-01 ${props.data.entry.check_in}`) : null,
    check_out: props.data.entry?.check_out ? new Date(`2000-01-01 ${props.data.entry.check_out}`) : null,
    break_duration: props.data.entry?.break_duration || 0,
    comment: props.data.entry?.comment || "",
});

const formatToHHmm = (date) => {
    if (!date) return null;
    const d = new Date(date);
    return d.getHours().toString().padStart(2, "0") + ":" + d.getMinutes().toString().padStart(2, "0");
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
        <div class="mb-1 p-3 rounded bg-gray-50 border flex justify-between items-center">
            <div>
                <div class="font-bold text-gray-800">{{ data.employee_name }}</div>
                <div class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">{{ data.date }}</div>
            </div>
            <div :class="isLocked ? 'text-red-500 bg-red-50' : 'text-blue-500 bg-blue-50'" class="px-2 py-1 rounded text-[10px] font-black border uppercase">
                {{ isLocked ? 'Lecture Seule' : 'Mode Édition' }}
            </div>
        </div>

        <div class="field">
            <label class="font-bold text-xs uppercase text-gray-600">Arrivée</label>
            <Calendar v-model="form.check_in" timeOnly hourFormat="24" :disabled="isLocked" placeholder="--:--" />
        </div>

        <div class="field">
            <label class="font-bold text-xs uppercase text-gray-600">Départ</label>
            <Calendar v-model="form.check_out" timeOnly hourFormat="24" :disabled="isLocked" placeholder="--:--" />
        </div>

        <div class="field">
            <label class="font-bold text-xs uppercase text-gray-600">Pause (min)</label>
            <InputText v-model="form.break_duration" type="number" :disabled="isLocked" />
        </div>

        <div class="field">
            <label class="font-bold text-xs uppercase text-gray-600">Commentaire</label>
            <InputText v-model="form.comment" placeholder="Motif si écart..." :disabled="isLocked" />
        </div>

        <div class="flex justify-end gap-2 mt-4">
            <Button label="Fermer" class="p-button-text p-button-secondary text-sm" @click="$emit('close')" />
            <Button v-if="!isLocked" label="Enregistrer" icon="pi pi-check" class="p-button-sm" :loading="form.processing" @click="submit" />
        </div>
    </div>
</template>
