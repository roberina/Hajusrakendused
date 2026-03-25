<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    shark: { type: Object, default: null },
});

const isEdit = !!props.shark;

const form = useForm({
    title:        props.shark?.title        ?? '',
    image:        props.shark?.image        ?? '',
    description:  props.shark?.description  ?? '',
    max_length:   props.shark?.max_length   ?? '',
    habitat:      props.shark?.habitat      ?? '',
    danger_level: props.shark?.danger_level ?? 'madal',
});

function submit() {
    if (isEdit) {
        form.put(route('sharks.update', props.shark.id));
    } else {
        form.post(route('sharks.store'));
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Muuda haid' : 'Lisa uus hai'" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('sharks.index')" class="back-btn">← Tagasi</Link>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ isEdit ? '✏️ Muuda haid' : '🦈 Lisa uus hai' }}
                </h2>
            </div>
        </template>

        <div class="form-wrap">
            <div class="form-container">
                <div class="form-card">
                    <form @submit.prevent="submit" class="form-body">

                        <div class="field">
                            <label class="field-label">Hainimi *</label>
                            <input v-model="form.title" type="text" class="field-inp" placeholder="nt Valge Hai" :class="{ 'field-err': form.errors.title }" />
                            <div v-if="form.errors.title" class="err-msg">{{ form.errors.title }}</div>
                        </div>

                        <div class="field">
                            <label class="field-label">Pildi URL</label>
                            <input v-model="form.image" type="url" class="field-inp" placeholder="https://..." :class="{ 'field-err': form.errors.image }" />
                            <div v-if="form.errors.image" class="err-msg">{{ form.errors.image }}</div>
                        </div>

                        <div class="field">
                            <label class="field-label">Kirjeldus *</label>
                            <textarea v-model="form.description" class="field-inp field-ta" placeholder="Kirjelda seda haid..." rows="5" :class="{ 'field-err': form.errors.description }"></textarea>
                            <div v-if="form.errors.description" class="err-msg">{{ form.errors.description }}</div>
                        </div>

                        <div class="field-row">
                            <div class="field">
                                <label class="field-label">Maksimaalne pikkus (meetrites) *</label>
                                <input v-model="form.max_length" type="number" step="0.1" min="0.1" max="30" class="field-inp" placeholder="nt 6.5" :class="{ 'field-err': form.errors.max_length }" />
                                <div v-if="form.errors.max_length" class="err-msg">{{ form.errors.max_length }}</div>
                            </div>
                            <div class="field">
                                <label class="field-label">Ohtlikkus *</label>
                                <select v-model="form.danger_level" class="field-inp" :class="{ 'field-err': form.errors.danger_level }">
                                    <option value="madal">🟢 Madal</option>
                                    <option value="keskmine">🟡 Keskmine</option>
                                    <option value="kõrge">🔴 Kõrge</option>
                                </select>
                                <div v-if="form.errors.danger_level" class="err-msg">{{ form.errors.danger_level }}</div>
                            </div>
                        </div>

                        <div class="field">
                            <label class="field-label">Elupaik *</label>
                            <input v-model="form.habitat" type="text" class="field-inp" placeholder="nt Troopiline ookean, Korallriff..." :class="{ 'field-err': form.errors.habitat }" />
                            <div v-if="form.errors.habitat" class="err-msg">{{ form.errors.habitat }}</div>
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="submit-btn" :disabled="form.processing">
                                {{ form.processing ? 'Salvestab...' : (isEdit ? '✅ Salvesta muudatused' : '🦈 Lisa hai') }}
                            </button>
                            <Link :href="route('sharks.index')" class="cancel-btn">Tühista</Link>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.form-wrap { background: #f0f9ff; min-height: 100vh; padding: 32px 16px; font-family: 'Inter', sans-serif; }
.form-container { max-width: 680px; margin: 0 auto; }
.back-btn { font-size: 13px; color: #64748b; text-decoration: none; font-weight: 600; transition: color 0.15s; }
.back-btn:hover { color: #0284c7; }

.form-card { background: white; border-radius: 16px; padding: 36px; border: 1px solid #e0f2fe; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
.form-body { display: flex; flex-direction: column; gap: 20px; }

.field { display: flex; flex-direction: column; gap: 6px; flex: 1; }
.field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.field-label { font-size: 12px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; }
.field-inp { border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 11px 14px; font-size: 14px; font-family: 'Inter', sans-serif; color: #0f172a; outline: none; transition: border 0.15s; box-sizing: border-box; width: 100%; }
.field-inp:focus { border-color: #0284c7; }
.field-err { border-color: #fca5a5 !important; }
.field-ta { resize: vertical; line-height: 1.6; }
.err-msg { color: #dc2626; font-size: 12px; }

.form-footer { display: flex; gap: 12px; align-items: center; padding-top: 8px; }
.submit-btn { background: #0284c7; color: white; border: none; border-radius: 10px; padding: 11px 24px; font-size: 14px; font-weight: 600; cursor: pointer; transition: background 0.15s; }
.submit-btn:hover:not(:disabled) { background: #0369a1; }
.submit-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.cancel-btn { text-decoration: none; color: #64748b; font-size: 14px; font-weight: 500; padding: 11px 20px; border-radius: 10px; border: 1px solid #e2e8f0; transition: all 0.15s; }
.cancel-btn:hover { background: #f8fafc; }
</style>