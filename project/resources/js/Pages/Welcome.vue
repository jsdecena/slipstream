<script setup>
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

// Mock customers data
const customers = ref([
    { id: 1, name: 'John Doe', email: 'john.doe@example.com', phone: '+1 234-567-8900', status: 'Active' },
    { id: 2, name: 'Jane Smith', email: 'jane.smith@example.com', phone: '+1 234-567-8901', status: 'Active' },
    { id: 3, name: 'Bob Johnson', email: 'bob.johnson@example.com', phone: '+1 234-567-8902', status: 'Inactive' },
    { id: 4, name: 'Alice Williams', email: 'alice.williams@example.com', phone: '+1 234-567-8903', status: 'Active' },
    { id: 5, name: 'Charlie Brown', email: 'charlie.brown@example.com', phone: '+1 234-567-8904', status: 'Active' },
]);
</script>

<template>
    <Head title="Welcome" />
    
    <div class="min-h-screen bg-gray-100">
        <!-- 2 Column Layout -->
        <div class="flex">
            <!-- Sidebar -->
            <aside class="w-64 bg-white shadow-lg min-h-screen">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Navigation</h2>
                    <nav class="space-y-2">
                        <a href="#" class="block px-4 py-2 text-gray-700 bg-blue-50 text-blue-700 rounded-lg font-medium">
                            Customers
                        </a>
                    </nav>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 p-8">
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-gray-800">Customers</h1>
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            Create Customer
                        </button>
                    </div>

                    <!-- Customers Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Phone
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="customer in customers" :key="customer.id" class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ customer.id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ customer.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ customer.email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ customer.phone }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span 
                                            :class="{
                                                'bg-green-100 text-green-800': customer.status === 'Active',
                                                'bg-red-100 text-red-800': customer.status === 'Inactive'
                                            }"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        >
                                            {{ customer.status }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
