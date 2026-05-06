<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head , Link} from '@inertiajs/vue3';
import Button from "primevue/button";
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';
import { onMounted } from 'vue';
const toast = useToast();
onMounted(()=>{
    toast.add({ severity: 'success', summary: 'Success Message', detail: 'Message Content', life: 3000 });
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <Toast/>
            <Button label="Value"/>
           <Link href="/calendar">Calendar</Link>
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        You're logged in!
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
