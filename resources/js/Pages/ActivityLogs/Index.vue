<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    logs: Object
});
</script>

<template>
    <Head title="Journal d'activité" />

    <AppLayout>

        <template #header>
            <div>
                <h2 class="text-2xl font-black text-slate-800">
                    Journal d'activité
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Historique des actions effectuées dans le système
                </p>
            </div>
        </template>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="text-lg font-black text-slate-800">
                    Activités récentes
                </h3>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-slate-50">
                        <tr>

                            <th class="px-6 py-4 text-left text-xs font-black text-slate-500 uppercase">
                                Utilisateur
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-black text-slate-500 uppercase">
                                Action
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-black text-slate-500 uppercase">
                                Modèle
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-black text-slate-500 uppercase">
                                Description
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-black text-slate-500 uppercase">
                                IP
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-black text-slate-500 uppercase">
                                Date
                            </th>

                        </tr>
                    </thead>

                    <tbody>

                        <tr
                            v-for="log in logs.data"
                            :key="log.id"
                            class="border-b border-slate-100 hover:bg-slate-50 transition"
                        >

                            <td class="px-6 py-5 font-bold text-slate-700">
                                {{ log.user?.name }}
                            </td>

                            <td class="px-6 py-5">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-black uppercase bg-blue-100 text-blue-700"
                                >
                                    {{ log.action }}
                                </span>
                            </td>

                            <td class="px-6 py-5 text-slate-500">
                                {{ log.model_type }}
                            </td>

                            <td class="px-6 py-5 text-slate-600">
                                {{ log.description }}
                            </td>

                            <td class="px-6 py-5 text-slate-500">
                                {{ log.ip_address }}
                            </td>

                            <td class="px-6 py-5 text-slate-500">
                                {{ log.created_at }}
                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </AppLayout>
</template>
