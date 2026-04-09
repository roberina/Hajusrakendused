<script setup>
import { ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    apiKeys: { type: Array, default: () => [] },
});

const page = usePage();
const newKey = ref(page.props.flash?.new_key ?? null);
const copied = ref(false);

const form = useForm({ name: '' });

function generate() {
    form.post(route('api-keys.store'), {
        onSuccess: () => {
            form.reset();
            newKey.value = page.props.flash?.new_key;
        }
    });
}

function copyKey(key) {
    navigator.clipboard.writeText(key);
    copied.value = true;
    setTimeout(() => copied.value = false, 2000);
}

function deleteKey(id) {
    if (!confirm('Kustuta API võti?')) return;
    useForm({}).delete(route('api-keys.destroy', id));
}

function formatDate(dt) {
    return new Date(dt).toLocaleDateString('et-EE', { day: 'numeric', month: 'short', year: 'numeric' });
}
</script>

<template>
    <Head title="API Võtmed" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">🔑 API Võtmed</h2>
        </template>

        <div class="keys-wrap">
            <div class="keys-container">

                <!-- New key alert -->
                <div v-if="newKey" class="new-key-box">
                    <div class="new-key-title">✅ Uus API võti loodud — kopeeri see kohe, seda ei näidata uuesti!</div>
                    <div class="new-key-row">
                        <code class="new-key-code">{{ newKey }}</code>
                        <button @click="copyKey(newKey)" class="copy-btn">
                            {{ copied ? '✅ Kopeeritud!' : '📋 Kopeeri' }}
                        </button>
                    </div>
                </div>

                <!-- Docs -->
                <div class="docs-card">
                    <h3 class="docs-title">📖 API kasutamine</h3>
                    <p class="docs-text">Lisa API võti päisesse või URL parameetrisse:</p>
                    <div class="code-block">
                        <div class="code-label">Päisena (soovitatav)</div>
                        <code>X-API-Key: sinu_api_võti</code>
                    </div>
                    <div class="code-block">
                        <div class="code-label">URL parameetrina</div>
                        <code>/api/sharks?api_key=sinu_api_võti</code>
                    </div>
                    <div class="code-block">
                        <div class="code-label">Filtreerimine</div>
                        <code>/api/sharks?search=valge&danger_level=kõrge&limit=5</code>
                    </div>
                </div>

                <!-- Generate -->
                <div class="generate-card">
                    <h3 class="generate-title">Genereeri uus võti</h3>
                    <form @submit.prevent="generate" class="generate-form">
                        <input v-model="form.name" type="text" class="generate-inp" placeholder="Võtme nimi (nt Minu projekt)" />
                        <button type="submit" class="generate-btn" :disabled="form.processing || !form.name.trim()">
                            {{ form.processing ? 'Loon...' : '+ Genereeri' }}
                        </button>
                    </form>
                </div>

                <!-- Keys list -->
                <div class="keys-card">
                    <h3 class="keys-title">Minu API võtmed <span class="keys-count">{{ apiKeys.length }}</span></h3>

                    <div v-if="apiKeys.length === 0" class="no-keys">
                        Sul pole veel API võtmeid. Genereeri esimene!
                    </div>

                    <div v-else class="keys-list">
                        <div v-for="k in apiKeys" :key="k.id" class="key-row">
                            <div class="key-info">
                                <div class="key-name">{{ k.name }}</div>
                                <div class="key-value">{{ k.key }}</div>
                                <div class="key-meta">
                                    Loodud: {{ formatDate(k.created_at) }}
                                    <span v-if="k.last_used_at"> · Viimati kasutatud: {{ formatDate(k.last_used_at) }}</span>
                                </div>
                            </div>
                            <button @click="deleteKey(k.id)" class="delete-btn">🗑️</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.keys-wrap { background: #f8fafc; min-height: 100vh; padding: 32px 16px; font-family: 'Inter', sans-serif; }
.keys-container { max-width: 760px; margin: 0 auto; display: flex; flex-direction: column; gap: 16px; }

.new-key-box { background: #f0fdf4; border: 1px solid #86efac; border-radius: 14px; padding: 20px; }
.new-key-title { font-size: 14px; font-weight: 600; color: #15803d; margin-bottom: 12px; }
.new-key-row { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
.new-key-code { background: #dcfce7; border: 1px solid #86efac; border-radius: 8px; padding: 8px 14px; font-size: 13px; color: #166534; word-break: break-all; flex: 1; }
.copy-btn { background: #16a34a; color: white; border: none; border-radius: 8px; padding: 8px 16px; font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap; }

.docs-card { background: #0f172a; border-radius: 14px; padding: 24px; }
.docs-title { font-size: 15px; font-weight: 700; color: white; margin: 0 0 8px; }
.docs-text { font-size: 13px; color: #94a3b8; margin: 0 0 16px; }
.code-block { margin-bottom: 12px; }
.code-label { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
.code-block code { display: block; background: #1e293b; color: #7dd3fc; padding: 10px 14px; border-radius: 8px; font-size: 13px; word-break: break-all; }

.generate-card { background: white; border-radius: 14px; padding: 20px; border: 1px solid #e2e8f0; }
.generate-title { font-size: 15px; font-weight: 700; color: #0f172a; margin: 0 0 14px; }
.generate-form { display: flex; gap: 10px; }
.generate-inp { flex: 1; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; font-size: 14px; font-family: 'Inter', sans-serif; color: #0f172a; outline: none; transition: border 0.15s; }
.generate-inp:focus { border-color: #3b82f6; }
.generate-btn { background: #0f172a; color: white; border: none; border-radius: 8px; padding: 10px 20px; font-size: 14px; font-weight: 600; cursor: pointer; white-space: nowrap; transition: background 0.15s; }
.generate-btn:hover:not(:disabled) { background: #3b82f6; }
.generate-btn:disabled { opacity: 0.5; cursor: not-allowed; }

.keys-card { background: white; border-radius: 14px; padding: 20px; border: 1px solid #e2e8f0; }
.keys-title { font-size: 15px; font-weight: 700; color: #0f172a; margin: 0 0 16px; display: flex; align-items: center; gap: 8px; }
.keys-count { background: #f1f5f9; color: #64748b; border-radius: 20px; padding: 1px 8px; font-size: 12px; }
.no-keys { font-size: 14px; color: #94a3b8; text-align: center; padding: 24px; }
.keys-list { display: flex; flex-direction: column; gap: 10px; }
.key-row { display: flex; align-items: center; gap: 12px; padding: 14px; border: 1px solid #e2e8f0; border-radius: 10px; }
.key-info { flex: 1; min-width: 0; }
.key-name { font-size: 14px; font-weight: 600; color: #0f172a; margin-bottom: 4px; }
.key-value { font-family: monospace; font-size: 12px; color: #64748b; margin-bottom: 4px; }
.key-meta { font-size: 11px; color: #94a3b8; }
.delete-btn { background: none; border: none; cursor: pointer; font-size: 16px; opacity: 0.4; transition: opacity 0.15s; }
.delete-btn:hover { opacity: 1; }
</style>