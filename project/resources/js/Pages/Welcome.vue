<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

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

const customers = ref([]);
const isLoading = ref(false);

// Fetch customers from API
const fetchCustomers = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get('/customers');
        customers.value = response.data.data || response.data;
    } catch (error) {
        console.error('Error fetching customers:', error);
        alert('An error occurred while fetching customers. Please try again.');
    } finally {
        isLoading.value = false;
    }
};

// Fetch customers when component mounts
onMounted(() => {
    fetchCustomers();
});

// Modal state
const showModal = ref(false);
const editingCustomerId = ref(null);

// Form data
const form = ref({
    name: '',
    reference: '',
    category: 'Bronze',
    start_date: '',
    description: '',
});

const errors = ref({});
const isSubmitting = ref(false);

// Contacts data
const contacts = ref([]);

const openModal = () => {
    editingCustomerId.value = null;
    showModal.value = true;
    // Reset form when opening modal
    form.value = {
        name: '',
        reference: '',
        category: 'Bronze',
        start_date: '',
        description: '',
    };
    errors.value = {};
    contacts.value = [];
};

const editCustomer = async (customerId) => {
    try {
        const response = await axios.get(`/customers/${customerId}`);
        const customer = response.data;
        
        // Format date for date input (YYYY-MM-DD)
        let formattedDate = '';
        if (customer.start_date) {
            const date = new Date(customer.start_date);
            formattedDate = date.toISOString().split('T')[0];
        }
        
        // Populate form with customer data
        form.value = {
            name: customer.name || '',
            reference: customer.reference || '',
            category: customer.category || 'Bronze',
            start_date: formattedDate,
            description: customer.description || '',
        };
        
        editingCustomerId.value = customerId;
        showModal.value = true;
        errors.value = {};
        // TODO: Load contacts when available
        contacts.value = [];
    } catch (error) {
        console.error('Error fetching customer:', error);
        alert('An error occurred while fetching customer data. Please try again.');
    }
};

const closeModal = () => {
    showModal.value = false;
    editingCustomerId.value = null;
    // Reset form when closing modal
    form.value = {
        name: '',
        reference: '',
        category: 'Bronze',
        start_date: '',
        description: '',
    };
    errors.value = {};
    contacts.value = [];
};

const submitForm = async () => {
    isSubmitting.value = true;
    errors.value = {};

    try {
        if (editingCustomerId.value) {
            // Update existing customer
            await axios.put(`/customers/${editingCustomerId.value}`, form.value);
        } else {
            // Create new customer
            await axios.post('/customers', form.value);
        }
        
        // Refresh the customers list
        await fetchCustomers();
        
        // Close modal and reset form
        closeModal();
    } catch (error) {
        if (error.response && error.response.status === 422) {
            const validationErrors = error.response.data.errors || {};
            errors.value = {};
            for (const [key, value] of Object.entries(validationErrors)) {
                errors.value[key] = Array.isArray(value) ? value[0] : value;
            }
        } else {
            // Handle other errors
            console.error('Error submitting form:', error);
            const action = editingCustomerId.value ? 'updating' : 'creating';
            alert(`An error occurred while ${action} the customer. Please try again.`);
        }
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<template>
    <Head title="Customer List" />
    
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
                        <PrimaryButton @click="openModal">
                            Create Customer
                        </PrimaryButton>
                    </div>

                    <!-- Loading State -->
                    <div v-if="isLoading" class="flex flex-col items-center justify-center py-16 px-6">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
                        <h3 class="text-xl font-semibold text-gray-700">Loading Your Customers</h3>
                    </div>

                    <!-- Empty State -->
                    <div v-else-if="customers.length === 0" class="flex flex-col items-center justify-center py-16 px-6">
                        <i class="fas fa-users text-gray-400 text-6xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No customers found.</h3>
                        <PrimaryButton @click="openModal">
                            Create Your First Customer
                        </PrimaryButton>
                    </div>

                    <!-- Customers Table -->
                    <div v-else class="overflow-x-auto">
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
                                        Reference
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Category
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No. of Contacts
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
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
                                        {{ customer.reference }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span 
                                            :class="{
                                                'bg-yellow-100 text-yellow-800': customer.category === 'Bronze',
                                                'bg-gray-100 text-gray-800': customer.category === 'Silver',
                                                'bg-yellow-200 text-yellow-900': customer.category === 'Gold',
                                                'bg-blue-100 text-blue-800': customer.category === 'Platinum'
                                            }"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        >
                                            {{ customer.category }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        0
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button
                                                type="button"
                                                @click="editCustomer(customer.id)"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>

        <!-- Modal -->
        <div 
            v-if="showModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto py-8"
            @click.self="closeModal"
        >
            <div class="bg-white rounded-lg shadow-xl max-w-5xl w-full mx-4 my-8">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">
                        {{ editingCustomerId ? 'Edit Customer' : 'Create Customer' }} - Detail
                    </h2>
                    <div class="flex items-center space-x-4">
                        <button 
                            @click="closeModal"
                            class="text-gray-600 hover:text-gray-800 transition"
                        >
                            Back
                        </button>
                        <PrimaryButton type="submit" form="customer-form" :disabled="isSubmitting">
                            {{ isSubmitting ? 'Saving...' : 'Save' }}
                        </PrimaryButton>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <form id="customer-form" @submit.prevent="submitForm" class="space-y-6">
                        <!-- General and Details Sections Side by Side -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- General Section -->
                            <div class="border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">General</h3>
                                <div class="space-y-4">
                                    <!-- Name Field -->
                                    <div>
                                        <InputLabel for="name" value="Name" />
                                        <TextInput
                                            id="name"
                                            v-model="form.name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            required
                                        />
                                        <InputError :message="errors.name" class="mt-2" />
                                    </div>

                                    <!-- Reference Field -->
                                    <div>
                                        <InputLabel for="reference" value="Reference" />
                                        <TextInput
                                            id="reference"
                                            v-model="form.reference"
                                            type="text"
                                            class="mt-1 block w-full"
                                            required
                                        />
                                        <InputError :message="errors.reference" class="mt-2" />
                                    </div>

                                    <!-- Category Field -->
                                    <div>
                                        <InputLabel for="category" value="Category" />
                                        <select
                                            id="category"
                                            v-model="form.category"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                        >
                                            <option value="" disabled>Select Category</option>
                                            <option value="Bronze">Bronze</option>
                                            <option value="Silver">Silver</option>
                                            <option value="Gold">Gold</option>
                                            <option value="Platinum">Platinum</option>
                                        </select>
                                        <InputError :message="errors.category" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Details Section -->
                            <div class="border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Details</h3>
                                <div class="space-y-4">
                                    <!-- Start Date Field -->
                                    <div>
                                        <InputLabel for="start_date" value="Start Date" />
                                        <TextInput
                                            id="start_date"
                                            v-model="form.start_date"
                                            type="date"
                                            class="mt-1 block w-full"
                                            required
                                        />
                                        <InputError :message="errors.start_date" class="mt-2" />
                                    </div>

                                    <!-- Description Field -->
                                    <div>
                                        <InputLabel for="description" value="Description" />
                                        <textarea
                                            id="description"
                                            v-model="form.description"
                                            rows="4"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                        ></textarea>
                                        <InputError :message="errors.description" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contacts Section -->
                        <div class="border border-gray-200 rounded-lg p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">Contacts</h3>
                                <PrimaryButton type="button">
                                    Create
                                </PrimaryButton>
                            </div>
                            
                            <!-- Contacts Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                First Name
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Last Name
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-if="contacts.length === 0" class="bg-gray-50">
                                            <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">
                                                No contacts found.
                                            </td>
                                        </tr>
                                        <tr v-for="contact in contacts" :key="contact.id" class="hover:bg-gray-50 transition">
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ contact.first_name }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ contact.last_name }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-3">
                                                    <button
                                                        type="button"
                                                        class="text-blue-600 hover:text-blue-800"
                                                    >
                                                        Edit
                                                    </button>
                                                    <span class="text-gray-300">|</span>
                                                    <button
                                                        type="button"
                                                        class="text-red-600 hover:text-red-800"
                                                    >
                                                        Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
