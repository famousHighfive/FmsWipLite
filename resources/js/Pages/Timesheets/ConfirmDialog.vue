<script setup>
import { router, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import Dialog from "primevue/dialog";
import Button from "primevue/button";

const props = defineProps({
    visible: Boolean,           // Contrôle l'affichage du modal depuis le parent
    timesheetId: [Number, String], // ID de la feuille de temps à clôturer
    employeeName: String        // Nom de l'employé pour personnaliser le message
});

const emit = defineEmits(["update:visible"]);

/**
 * RÉCUPÉRATION DU RÔLE UTILISATEUR
 * Identifie si l'utilisateur est un Admin ou un CP pour adapter le texte de l'interface.
 */
const userRole = computed(() => usePage().props.auth.user.role.name.toLowerCase());

/**
 * FERMETURE DU MODAL
 * Envoie un signal au parent pour passer la visibilité à 'false'.
 */
const close = () => {
    emit("update:visible", false);
};

/**
 * CONFIRMATION ET SOUMISSION FINALE
 * Déclenche l'appel API pour verrouiller la feuille de temps de manière irréversible.
 */
const handleConfirm = () => {
    // Envoi d'une requête POST vers la route de soumission Laravel
    router.post(`/timesheets/${props.timesheetId}/submit`, {}, {
        preserveScroll: true, // Évite que la page ne remonte en haut au rechargement
        onSuccess: () => {
            // Si le serveur répond positivement, on ferme la fenêtre
            console.log("Succès : Semaine clôturée !");
            close();
        },
        onError: (err) => {
            // En cas de problème (ex: déjà soumis), on log l'erreur
            console.error("Erreur serveur lors de la clôture :", err);
        }
    });
};
</script>

<template>
    <Dialog 
        :visible="visible" 
        @update:visible="close" 
        :header="userRole === 'admin' ? 'Validation Administrateur' : 'Validation Hiérarchique'" 
        :modal="true" 
        :style="{ width: '400px' }" 
        :closable="false" 
        class="custom-confirm-dialog"
    >
        <div class="flex flex-col items-center p-2 text-center">
            <!-- Icône visuelle (Bouclier de validation) -->
            <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mb-4 border border-emerald-100 shadow-inner">
                <svg xmlns="http://w3.org" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>

            <!-- Titre et Message d'avertissement -->
            <h3 class="text-lg font-black text-gray-900 mb-1 uppercase tracking-tight">Clôturer la semaine ?</h3>
            
            <p class="text-sm text-gray-500 mb-6 px-4 leading-relaxed">
                En tant que 
                <span class="font-bold text-emerald-700 underline decoration-2 italic">
                    {{ userRole === 'admin' ? 'Administrateur' : 'Chef de Plateau' }}
                </span>, 
                vous allez soumettre les heures de <span class="font-bold text-gray-800">{{ employeeName }}</span>.
                <br>
                <!-- Alerte de sécurité sur l'irréversibilité -->
                <span class="text-red-500 font-bold italic text-[11px] mt-2 block bg-red-50 p-1 rounded">
                    <i class="pi pi-exclamation-circle mr-1"></i> 
                    ACTION IRRÉVERSIBLE : Verrouillage total de la saisie.
                </span>
            </p>

            <!-- Boutons d'action -->
            <div class="flex gap-3 w-full">
                <Button label="Annuler" class="p-button-text p-button-secondary flex-1 font-bold" @click="close" />
                <Button label="Confirmer" class="p-button-success flex-1 font-black shadow-lg uppercase text-xs" @click="handleConfirm" />
            </div>
        </div>
    </Dialog>
</template>
