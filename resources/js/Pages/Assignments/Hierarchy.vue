<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
    Users, 
    UserCheck, 
    UserPlus, 
    ChevronRight, 
    ChevronDown, 
    Megaphone,
    Search,
    Filter,
    ArrowRight
} from 'lucide-vue-next';

const props = defineProps({
    hierarchy: Array
});

const expandedCampaigns = ref({});
const expandedCPs = ref({});
const expandedSUPs = ref({});

const toggleCampaign = (id) => {
    expandedCampaigns.value[id] = !expandedCampaigns.value[id];
};

const toggleCP = (id) => {
    expandedCPs.value[id] = !expandedCPs.value[id];
};

const toggleSUP = (id) => {
    expandedSUPs.value[id] = !expandedSUPs.value[id];
};
</script>

<template>
    <Head title="Vue Hiérarchique" />

    <AppLayout>
        <template #header>
            <div>
                <h2 class="text-3xl font-black text-slate-800">Vue Hiérarchique</h2>
                <p class="text-slate-500 font-medium mt-1">Organisation des équipes par campagne</p>
            </div>
        </template>

        <div class="py-8 space-y-8">
            <!-- Search & Filters -->
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col md:flex-row gap-4 items-center justify-between">
                <div class="relative w-full md:w-96">
                    <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                    <input 
                        type="text" 
                        placeholder="Rechercher un agent, un superviseur..." 
                        class="w-full pl-12 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all"
                    >
                </div>
                <div class="flex gap-2">
                    <button class="flex items-center gap-2 px-5 py-3 bg-slate-50 text-slate-600 rounded-2xl text-sm font-bold hover:bg-slate-100 transition-colors">
                        <Filter class="w-4 h-4" />
                        Filtres
                    </button>
                    <Link :href="route('assignments.cp')" class="flex items-center gap-2 px-5 py-3 bg-indigo-600 text-white rounded-2xl text-sm font-bold hover:bg-indigo-700 transition-all shadow-md">
                        <UserPlus class="w-4 h-4" />
                        Nouvelle Affectation
                    </Link>
                </div>
            </div>

            <!-- Hierarchy Tree -->
            <div class="space-y-6">
                <div v-for="campaign in hierarchy" :key="campaign.id" class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                    <!-- Campaign Level -->
                    <div 
                        @click="toggleCampaign(campaign.id)"
                        class="p-6 flex items-center justify-between cursor-pointer hover:bg-slate-50/50 transition-colors"
                    >
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-indigo-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200">
                                <Megaphone class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-slate-800">{{ campaign.name }}</h3>
                                <p class="text-xs font-black text-indigo-400 uppercase tracking-widest">{{ campaign.cps.length }} Chefs de Plateau</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-widest">{{ campaign.status }}</span>
                            <div class="p-2 bg-slate-100 rounded-xl text-slate-400">
                                <ChevronDown v-if="expandedCampaigns[campaign.id]" class="w-5 h-5" />
                                <ChevronRight v-else class="w-5 h-5" />
                            </div>
                        </div>
                    </div>

                    <!-- CP Level -->
                    <div v-if="expandedCampaigns[campaign.id]" class="border-t border-slate-50 bg-slate-50/30 p-6 space-y-4">
                        <div v-if="campaign.cps.length === 0" class="py-8 text-center text-slate-400 italic text-sm">
                            Aucun Chef de Plateau affecté à cette campagne.
                        </div>
                        
                        <div v-for="cp in campaign.cps" :key="cp.id" class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                            <div 
                                @click.stop="toggleCP(cp.id)"
                                class="p-5 flex items-center justify-between cursor-pointer hover:bg-slate-50/80 transition-colors"
                            >
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center font-black">
                                        {{ cp.employee.first_name.charAt(0) }}{{ cp.employee.last_name.charAt(0) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800">{{ cp.employee.first_name }} {{ cp.employee.last_name }}</p>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Chef de Plateau • {{ cp.supervisors.length }} Superviseurs</p>
                                    </div>
                                </div>
                                <div class="p-1.5 bg-slate-50 rounded-lg text-slate-400">
                                    <ChevronDown v-if="expandedCPs[cp.id]" class="w-4 h-4" />
                                    <ChevronRight v-else class="w-4 h-4" />
                                </div>
                            </div>

                            <!-- SUP Level -->
                            <div v-if="expandedCPs[cp.id]" class="border-t border-slate-50 p-5 pl-12 space-y-3 bg-slate-50/20">
                                <div v-if="cp.supervisors.length === 0" class="py-4 text-center text-slate-400 italic text-xs">
                                    Aucun superviseur sous la responsabilité de ce CP.
                                </div>

                                <div v-for="sup in cp.supervisors" :key="sup.id" class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                                    <div 
                                        @click.stop="toggleSUP(sup.id)"
                                        class="p-4 flex items-center justify-between cursor-pointer hover:bg-slate-50/80 transition-colors"
                                    >
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center font-black text-xs">
                                                {{ sup.employee.first_name.charAt(0) }}{{ sup.employee.last_name.charAt(0) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-700 text-sm">{{ sup.employee.first_name }} {{ sup.employee.last_name }}</p>
                                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Superviseur • {{ sup.teleconseillers.length }} Agents</p>
                                            </div>
                                        </div>
                                        <div class="p-1 bg-slate-50 rounded-md text-slate-400">
                                            <ChevronDown v-if="expandedSUPs[sup.id]" class="w-3.5 h-3.5" />
                                            <ChevronRight v-else class="w-3.5 h-3.5" />
                                        </div>
                                    </div>

                                    <!-- TC Level -->
                                    <div v-if="expandedSUPs[sup.id]" class="border-t border-slate-50 p-4 pl-12 bg-slate-50/40 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        <div v-if="sup.teleconseillers.length === 0" class="col-span-full py-4 text-center text-slate-400 italic text-xs">
                                            Aucun téléconseiller dans cette équipe.
                                        </div>

                                        <div v-for="tc in sup.teleconseillers" :key="tc.id" class="bg-white p-3 rounded-xl border border-slate-100 flex items-center justify-between group hover:border-indigo-200 transition-all">
                                            <div class="flex items-center gap-3">
                                                <div class="w-7 h-7 bg-slate-50 text-slate-400 rounded-md flex items-center justify-center font-black text-[10px] group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors">
                                                    {{ tc.employee.first_name.charAt(0) }}{{ tc.employee.last_name.charAt(0) }}
                                                </div>
                                                <p class="font-bold text-slate-600 text-xs">{{ tc.employee.first_name }} {{ tc.employee.last_name }}</p>
                                            </div>
                                            <Link :href="route('employees.show', tc.employee.id)" class="p-1 text-slate-300 hover:text-indigo-600">
                                                <ArrowRight class="w-3 h-3" />
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="hierarchy.length === 0" class="py-20 text-center bg-white rounded-[2.5rem] border border-dashed border-slate-200">
                    <Megaphone class="w-16 h-16 text-slate-100 mx-auto mb-4" />
                    <p class="text-slate-400 font-medium">Aucune campagne active ou affectation trouvée.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
