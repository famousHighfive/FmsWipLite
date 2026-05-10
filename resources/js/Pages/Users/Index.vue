<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { UserPlus, Edit, Trash2, Search } from 'lucide-vue-next';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import Tag from 'primevue/tag';
import { ref } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';

const props = defineProps({
  users: Array, // Reçu comme Array maintenant
});

const toast = useToast();
const confirm = useConfirm();

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const handleDelete = (user) => {
  confirm.require({
    message: `Voulez-vous vraiment supprimer l'utilisateur "${user.name}" ?`,
    header: 'Confirmation de suppression',
    icon: 'pi pi-exclamation-triangle',
    acceptProps: { label: 'Supprimer', severity: 'danger' },
    rejectProps: { label: 'Annuler', severity: 'secondary', variant: 'text' },
    accept: () => {
      router.delete(route('users.destroy', user.id), {
        onSuccess: () => {
          toast.add({ severity: 'success', summary: 'Supprimé', detail: 'Utilisateur supprimé avec succès', life: 3000 });
        }
      });
    }
  });
};

const getRoleSeverity = (role) => {
    switch (role) {
        case 'admin': return 'secondary';
        case 'cp': return 'info';
        case 'sup': return 'success';
        case 'tc': return 'warn';
        default: return 'contrast';
    }
};
</script>

<template>
  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <div>
          <h2 class="text-3xl font-bold text-slate-800">Gestion des Utilisateurs</h2>
          <p class="text-slate-500 mt-1">Gérez les comptes utilisateurs du système</p>
        </div>
        <Link
          :href="route('users.create')"
          class="flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-blue-500/30 transition-all"
        >
          <UserPlus class="w-5 h-5" />
          <span>Nouvel Utilisateur</span>
        </Link>
      </div>
    </template>

    <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-lg border border-blue-100/50 overflow-hidden mt-6">
      <DataTable 
        :value="users" 
        v-model:filters="filters"
        :globalFilterFields="['id', 'name', 'email', 'role.name']"
        paginator :rows="10" 
        class="p-datatable-sm"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Affichage de {first} à {last} sur {totalRecords} utilisateurs"
        stripedRows
      >
        <template #header>
            <div class="flex justify-between items-center py-2 px-4">
                <span class="text-slate-600 font-bold">Comptes utilisateurs</span>
                <IconField iconPosition="left">
                    <InputIcon>
                        <i class="pi pi-search" />
                    </InputIcon>
                    <InputText v-model="filters['global'].value" placeholder="Recherche automatique..." class="!rounded-xl !bg-slate-50/50" />
                </IconField>
            </div>
        </template>

        <Column field="id" header="ID" sortable>
            <template #body="{ data }">
                <span class="font-mono font-bold text-blue-600 bg-blue-100 px-2 py-1 rounded text-xs">
                    #{{ data.id }}
                </span>
            </template>
        </Column>

        <Column field="name" header="Nom" sortable class="font-semibold text-slate-800" />
        <Column field="email" header="Email" sortable class="text-slate-600" />
        
        <Column field="role.name" header="Rôle" sortable>
            <template #body="{ data }">
                <Tag :value="data.role?.name" :severity="getRoleSeverity(data.role?.name)" class="!text-[10px] !font-bold uppercase" />
            </template>
        </Column>

        <Column field="created_at" header="Créé le" sortable>
            <template #body="{ data }">
                {{ new Date(data.created_at).toLocaleDateString('fr-FR') }}
            </template>
        </Column>

        <Column header="Actions">
            <template #body="{ data }">
                <div class="flex items-center gap-2">
                    <Link
                      :href="route('users.edit', data.id)"
                      class="p-2 hover:bg-blue-50 rounded-xl text-slate-500 hover:text-blue-600 transition-all"
                    >
                      <Edit class="w-4 h-4" />
                    </Link>
                    <button
                      @click="handleDelete(data)"
                      class="p-2 hover:bg-red-50 rounded-xl text-slate-500 hover:text-red-600 transition-all"
                    >
                      <Trash2 class="w-4 h-4" />
                    </button>
                </div>
            </template>
        </Column>
      </DataTable>
    </div>
  </AppLayout>
</template>

<style scoped>
:deep(.p-datatable-thead > tr > th) {
    @apply !bg-blue-50/50 !text-slate-500 !text-xs !font-bold !uppercase !tracking-wider !py-4;
}
:deep(.p-datatable-tbody > tr) {
    @apply !transition-colors;
}
:deep(.p-datatable-tbody > tr:hover) {
    @apply !bg-blue-50/30;
}
</style>
