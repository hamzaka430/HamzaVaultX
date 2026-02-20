<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <!-- Page Title -->
        <div class="text-center mb-6">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                Welcome back
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Sign in to your account to continue
            </p>
        </div>

        <!-- Status Message -->
        <div v-if="status" class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200">
            <p class="text-sm font-medium text-green-800">{{ status }}</p>
        </div>

        <!-- Login Form -->
        <form @submit.prevent="submit" class="space-y-4">
            <!-- Email Field -->
            <div>
                <InputLabel for="email" value="Email" class="text-sm font-semibold text-gray-700" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1.5 block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="you@example.com"
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
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <label class="flex items-center cursor-pointer">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ml-2 text-sm text-gray-700">Remember me</span>
                </label>

                <Link
                    :href="route('password.request')"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors"
                >
                    Forgot password?
                </Link>
            </div>

            <!-- Submit Button -->
            <div>
                <PrimaryButton
                    class="w-full justify-center px-4 py-2.5 text-sm font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-700 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Signing in...' : 'Sign in' }}
                </PrimaryButton>
            </div>
        </form>

        <!-- Register Link -->
        <template #registerLink>
            <p class="text-sm text-gray-600">
                Don't have an account?
                <Link
                    :href="route('register')"
                    class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors"
                >
                    Sign up
                </Link>
            </p>
        </template>
    </GuestLayout>
</template>
