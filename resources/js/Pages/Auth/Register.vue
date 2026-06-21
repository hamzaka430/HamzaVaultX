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
            <h2 class="text-xl sm:text-2xl font-bold font-sans text-ink tracking-tight">
                Create your account
            </h2>
            <p class="mt-1 font-sans text-body-md text-ink-mute">
                Get started with your free account
            </p>
        </div>

        <!-- Registration Form -->
        <form @submit.prevent="submit" class="space-y-4">
            <!-- Name Field -->
            <div>
                <InputLabel for="name" value="Name" class="font-sans text-body-md font-semibold text-ink" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1.5 block w-full px-3 py-2.5"
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
                <InputLabel for="email" value="Email" class="font-sans text-body-md font-semibold text-ink" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1.5 block w-full px-3 py-2.5"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="abc@example.com"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Password Field -->
            <div>
                <InputLabel for="password" value="Password" class="font-sans text-body-md font-semibold text-ink" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1.5 block w-full px-3 py-2.5"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                    placeholder="••••••••"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Confirm Password Field -->
            <div>
                <InputLabel for="password_confirmation" value="Confirm Password" class="font-sans text-body-md font-semibold text-ink" />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1.5 block w-full px-3 py-2.5"
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
                    class="w-full justify-center py-2.5"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Creating account...' : 'Create account' }}
                </PrimaryButton>
            </div>

            <!-- Login Link -->
            <div class="text-center pt-1">
                <p class="font-sans text-body-md text-ink-mute">
                    Already have an account?
                    <Link
                        :href="route('login')"
                        class="font-medium text-primary hover:text-primary-deep transition-colors"
                    >
                        Sign in
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
