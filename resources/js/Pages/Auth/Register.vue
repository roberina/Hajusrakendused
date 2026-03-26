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

        <div class="auth-wrapper">
            <div class="auth-card">
                <div class="auth-header">
                    <div class="auth-logo">
                        <svg viewBox="0 0 16 16" fill="none"><rect x="2" y="2" width="5" height="5" fill="currentColor"/><rect x="9" y="2" width="5" height="5" fill="currentColor"/><rect x="2" y="9" width="5" height="5" fill="currentColor"/><rect x="9" y="9" width="5" height="5" fill="currentColor" opacity=".35"/></svg>
                    </div>
                    <h1 class="auth-title">Create account</h1>
                    <p class="auth-subtitle">Join us today — it's free</p>
                </div>

                <form @submit.prevent="submit" class="auth-form">
                    <div class="field-group">
                        <div class="field">
                            <label for="name" class="field-label">Full name</label>
                            <input id="name" type="text" class="field-input" v-model="form.name" required autofocus autocomplete="name" placeholder="Your name" :class="{ 'field-input--error': form.errors.name }" />
                            <p v-if="form.errors.name" class="field-error">{{ form.errors.name }}</p>
                        </div>
                        <div class="field">
                            <label for="email" class="field-label">Email address</label>
                            <input id="email" type="email" class="field-input" v-model="form.email" required autocomplete="username" placeholder="you@example.com" :class="{ 'field-input--error': form.errors.email }" />
                            <p v-if="form.errors.email" class="field-error">{{ form.errors.email }}</p>
                        </div>
                        <div class="field">
                            <label for="password" class="field-label">Password</label>
                            <input id="password" type="password" class="field-input" v-model="form.password" required autocomplete="new-password" placeholder="Min. 8 characters" :class="{ 'field-input--error': form.errors.password }" />
                            <p v-if="form.errors.password" class="field-error">{{ form.errors.password }}</p>
                        </div>
                        <div class="field">
                            <label for="password_confirmation" class="field-label">Confirm password</label>
                            <input id="password_confirmation" type="password" class="field-input" v-model="form.password_confirmation" required autocomplete="new-password" placeholder="Repeat your password" :class="{ 'field-input--error': form.errors.password_confirmation }" />
                            <p v-if="form.errors.password_confirmation" class="field-error">{{ form.errors.password_confirmation }}</p>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn" :class="{ 'submit-btn--loading': form.processing }" :disabled="form.processing">
                        <span v-if="!form.processing">Create account</span>
                        <span v-else class="loading-dots"><span></span><span></span><span></span></span>
                    </button>

                    <p class="auth-footer-text">
                        Already have an account?
                        <Link :href="route('login')" class="auth-link">Sign in</Link>
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

.auth-form { display: flex; flex-direction: column; }
.field-group { display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem; }
.field { display: flex; flex-direction: column; gap: 0.4rem; }

.field-label {
    font-size: 0.75rem;
    font-weight: 500;
    color: #666;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}

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