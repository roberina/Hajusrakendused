<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="auth-wrapper">
            <div class="auth-card">
                <div class="auth-header">
                    <div class="auth-logo">
                        <svg viewBox="0 0 16 16" fill="none"><rect x="2" y="2" width="5" height="5" fill="currentColor"/><rect x="9" y="2" width="5" height="5" fill="currentColor"/><rect x="2" y="9" width="5" height="5" fill="currentColor"/><rect x="9" y="9" width="5" height="5" fill="currentColor" opacity=".35"/></svg>
                    </div>
                    <h1 class="auth-title">Welcome back</h1>
                    <p class="auth-subtitle">Sign in to your account</p>
                </div>

                <div v-if="status" class="status-banner">{{ status }}</div>

                <form @submit.prevent="submit" class="auth-form">
                    <div class="field-group">
                        <div class="field">
                            <div class="field-label-row">
                                <label for="email" class="field-label">Email</label>
                            </div>
                            <input id="email" type="email" class="field-input" v-model="form.email" required autofocus autocomplete="username" placeholder="you@example.com" :class="{ 'field-input--error': form.errors.email }" />
                            <p v-if="form.errors.email" class="field-error">{{ form.errors.email }}</p>
                        </div>
                        <div class="field">
                            <div class="field-label-row">
                                <label for="password" class="field-label">Password</label>
                                <Link v-if="canResetPassword" :href="route('password.request')" class="forgot-link">Forgot?</Link>
                            </div>
                            <input id="password" type="password" class="field-input" v-model="form.password" required autocomplete="current-password" placeholder="Your password" :class="{ 'field-input--error': form.errors.password }" />
                            <p v-if="form.errors.password" class="field-error">{{ form.errors.password }}</p>
                        </div>
                    </div>

                    <label class="remember-label">
                        <div class="custom-checkbox" :class="{ 'custom-checkbox--checked': form.remember }">
                            <input type="checkbox" name="remember" v-model="form.remember" class="sr-only" />
                            <svg v-if="form.remember" viewBox="0 0 10 10" fill="none"><path d="M2 5l2.5 2.5L8 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <span class="remember-text">Remember me for 30 days</span>
                    </label>

                    <button type="submit" class="submit-btn" :class="{ 'submit-btn--loading': form.processing }" :disabled="form.processing">
                        <span v-if="!form.processing">Sign in</span>
                        <span v-else class="loading-dots"><span></span><span></span><span></span></span>
                    </button>

                    <p class="auth-footer-text">
                        Don't have an account?
                        <Link :href="route('register')" class="auth-link">Create one</Link>
                    </p>
                </form>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Sans:wght@300;400;500&display=swap');

.auth-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f5f5f3;
    padding: 2rem;
    font-family: 'DM Sans', sans-serif;
}

.auth-card {
    width: 100%;
    max-width: 400px;
    background: #ffffff;
    border: 1.5px solid #e5e5e5;
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: 0 2px 24px rgba(0,0,0,0.06);
    animation: slideUp 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}

.auth-header { text-align: center; margin-bottom: 2rem; }

.auth-logo {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    background: #0a0a0a;
    border-radius: 12px;
    margin-bottom: 1.25rem;
    color: #ffffff;
}

.auth-logo svg { width: 18px; height: 18px; }

.auth-title {
    font-family: 'Instrument Serif', serif;
    font-size: 1.7rem;
    font-weight: 400;
    color: #0a0a0a;
    margin: 0 0 0.35rem;
    letter-spacing: -0.02em;
}

.auth-subtitle { font-size: 0.875rem; color: #999; margin: 0; font-weight: 300; }

.status-banner {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 8px;
    padding: 0.65rem 0.9rem;
    font-size: 0.85rem;
    color: #15803d;
    margin-bottom: 1.25rem;
}

.auth-form { display: flex; flex-direction: column; }
.field-group { display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1rem; }
.field { display: flex; flex-direction: column; gap: 0.4rem; }
.field-label-row { display: flex; align-items: center; justify-content: space-between; }

.field-label {
    font-size: 0.75rem;
    font-weight: 500;
    color: #666;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}

.forgot-link { font-size: 0.78rem; color: #999; text-decoration: none; transition: color 0.15s; }
.forgot-link:hover { color: #0a0a0a; }

.field-input {
    width: 100%;
    padding: 0.7rem 0.9rem;
    background: #fafafa;
    border: 1.5px solid #e5e5e5;
    border-radius: 10px;
    color: #0a0a0a;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.9rem;
    font-weight: 300;
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
    box-sizing: border-box;
}

.field-input::placeholder { color: #c0c0c0; }
.field-input:focus { border-color: #0a0a0a; background: #fff; box-shadow: 0 0 0 3px rgba(0,0,0,0.06); }
.field-input--error { border-color: #ef4444; }
.field-input--error:focus { box-shadow: 0 0 0 3px rgba(239,68,68,0.1); }
.field-error { font-size: 0.78rem; color: #ef4444; margin: 0; }

.remember-label { display: flex; align-items: center; gap: 0.6rem; cursor: pointer; margin-bottom: 1.25rem; margin-top: 0.25rem; }

.custom-checkbox {
    width: 18px; height: 18px;
    border-radius: 5px;
    border: 1.5px solid #d5d5d5;
    background: #fafafa;
    flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    transition: background 0.15s, border-color 0.15s;
}
.custom-checkbox--checked { background: #0a0a0a; border-color: #0a0a0a; color: #fff; }
.custom-checkbox svg { width: 10px; height: 10px; }
.sr-only { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0,0,0,0); }
.remember-text { font-size: 0.85rem; color: #999; font-weight: 300; user-select: none; }

.submit-btn {
    width: 100%;
    padding: 0.8rem;
    background: #0a0a0a;
    color: #ffffff;
    border: none;
    border-radius: 10px;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s, transform 0.15s;
    margin-bottom: 1.25rem;
    letter-spacing: 0.01em;
}

.submit-btn:hover:not(:disabled) { background: #222; transform: translateY(-1px); }
.submit-btn:active:not(:disabled) { transform: translateY(0); }
.submit-btn--loading { opacity: 0.45; cursor: not-allowed; }

.loading-dots { display: inline-flex; gap: 4px; align-items: center; height: 16px; }
.loading-dots span { width: 5px; height: 5px; background: #fff; border-radius: 50%; animation: dot-pulse 1.2s ease-in-out infinite; }
.loading-dots span:nth-child(2) { animation-delay: 0.2s; }
.loading-dots span:nth-child(3) { animation-delay: 0.4s; }

@keyframes dot-pulse {
    0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
    40% { transform: scale(1); opacity: 1; }
}

.auth-footer-text { text-align: center; font-size: 0.85rem; color: #aaa; margin: 0; }
.auth-link { color: #0a0a0a; text-decoration: none; font-weight: 500; }
.auth-link:hover { text-decoration: underline; }
</style>