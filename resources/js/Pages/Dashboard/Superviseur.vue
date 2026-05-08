<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    Users, 
    CheckSquare, 
    Clock, 
    Award,
    Mail,
    Phone,
    ArrowRight
} from 'lucide-vue-next';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({})
    },
    myTeam: {
        type: Array,
        default: () => []
    }
});
</script>

<template>
    <Head title="Dashboard - Superviseur" />

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-black text-slate-800">Espace Superviseur</h2>
                    <p class="text-slate-500 font-medium">Gestion de votre équipe et validation des temps</p>
                </div>
                <div class="p-2 bg-amber-50 rounded-2xl">
                    <Award class="w-8 h-8 text-amber-600" />
                </div>
            </div>
        </template>

        <div class="py-8 space-y-8">
            <!-- Stats Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl">
                            <Users class="w-6 h-6" />
                        </div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Mon Équipe</p>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800">{{ stats.myAgents }} agents</h3>
                </div>

                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl">
                            <CheckSquare class="w-6 h-6" />
                        </div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Validations effectuées</p>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800">{{ stats.validatedTimesheets }}</h3>
                </div>

                <div class="bg-indigo-600 p-6 rounded-[2rem] shadow-lg text-white">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 bg-white/20 rounded-2xl">
                            <Clock class="w-6 h-6" />
                        </div>
                        <p class="text-xs font-black text-indigo-100 uppercase tracking-widest">En attente de validation</p>
                    </div>
                    <div class="flex justify-between items-baseline">
                        <h3 class="text-3xl font-black">{{ stats.pendingMyValidation }}</h3>
                        <Link :href="route('planning.validate')" class="text-xs font-bold text-white underline hover:text-indigo-100">Traiter maintenant</Link>
                    </div>
                </div>
            </div>

            <!-- Team Members -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-xl font-black text-slate-800">Membres de l'Équipe</h3>
                    <div class="flex gap-2">
                        <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-black uppercase">Actifs ({{ myTeam.length }})</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="agent in myTeam" :key="agent.id" class="p-6 bg-slate-50 rounded-3xl border border-transparent hover:border-slate-200 transition-all group">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center font-black text-slate-600 border border-slate-100 shadow-sm group-hover:scale-110 transition-transform">
                                {{ agent.first_name.charAt(0) }}{{ agent.last_name.charAt(0) }}
                            </div>
                            <div class="px-2 py-1 bg-green-100 text-green-700 rounded-lg text-[10px] font-black uppercase tracking-widest">
                                {{ agent.status }}
                            </div>
                        </div>
                        
                        <h4 class="text-lg font-black text-slate-800">{{ agent.first_name }} {{ agent.last_name }}</h4>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-4">{{ agent.position?.name || 'Agent' }}</p>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex items-center gap-3 text-slate-500">
                                <Mail class="w-4 h-4" />
                                <span class="text-xs font-medium">{{ agent.email }}</span>
                            </div>
                            <div class="flex items-center gap-3 text-slate-500">
                                <Phone class="w-4 h-4" />
                                <span class="text-xs font-medium">{{ agent.phone }}</span>
                            </div>
                        </div>

                        <Link :href="route('employees.show', agent.id)" class="flex items-center justify-center gap-2 w-full py-3 bg-white border border-slate-200 text-slate-700 rounded-xl text-xs font-black hover:bg-slate-100 transition-colors">
                            Voir Profile
                            <ArrowRight class="w-3 h-3" />
                        </Link>
                    </div>

                    <div v-if="myTeam.length === 0" class="col-span-full py-20 text-center">
                        <Users class="w-16 h-16 text-slate-100 mx-auto mb-4" />
                        <p class="text-slate-400 font-medium">Aucun agent affecté sous votre supervision pour le moment.</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
