<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <!-- Page Title -->
        <div class="text-center mb-6">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                Create your account
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Get started with your free account
            </p>
        </div>

        <!-- Registration Form -->
        <form @submit.prevent="submit" class="space-y-4">
            <!-- Name Field -->
            <div>
                <InputLabel for="name" value="Name" class="text-sm font-semibold text-gray-700" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1.5 block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Hamza Zaka"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <!-- Email Field -->
            <div>
                <InputLabel for="email" value="Email" class="text-sm font-semibold text-gray-700" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1.5 block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="abc@example.com"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Password Field -->
            <div>
                <InputLabel for="password" value="Password" class="text-sm font-semibold text-gray-700" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1.5 block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                    placeholder="••••••••"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Confirm Password Field -->
            <div>
                <InputLabel for="password_confirmation" value="Confirm Password" class="text-sm font-semibold text-gray-700" />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1.5 block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="••••••••"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <!-- Submit Button -->
            <div class="pt-1">
                <PrimaryButton
                    class="w-full justify-center px-4 py-2.5 text-sm font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-700 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Creating account...' : 'Create account' }}
                </PrimaryButton>
            </div>

            <!-- Login Link -->
            <div class="text-center pt-1">
                <p class="text-sm text-gray-600">
                    Already have an account?
                    <Link
                        :href="route('login')"
                        class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors"
                    >
                        Sign in
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
