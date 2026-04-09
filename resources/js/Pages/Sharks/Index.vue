<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    sharks:  { type: Array, default: () => [] },
});

const search      = ref('');
const habitat     = ref('');
const dangerLevel = ref('');
const sort        = ref('created_at');
const order       = ref('desc');

const filtered = computed(() => {
    let result = [...props.sharks];
    if (search.value)      result = result.filter(s => s.title.toLowerCase().includes(search.value.toLowerCase()));
    if (habitat.value)     result = result.filter(s => s.habitat.toLowerCase().includes(habitat.value.toLowerCase()));
    if (dangerLevel.value) result = result.filter(s => s.danger_level === dangerLevel.value);

    result.sort((a, b) => {
        let valA = a[sort.value], valB = b[sort.value];
        if (typeof valA === 'string') valA = valA.toLowerCase();
        if (typeof valB === 'string') valB = valB.toLowerCase();
        if (valA < valB) return order.value === 'asc' ? -1 : 1;
        if (valA > valB) return order.value === 'asc' ? 1 : -1;
        return 0;
    });

    return result;
});

function dangerColor(level) {
    return { madal: '#22c55e', keskmine: '#f59e0b', kõrge: '#ef4444' }[level] ?? '#94a3b8';
}
function dangerEmoji(level) {
    return { madal: '🟢', keskmine: '🟡', kõrge: '🔴' }[level] ?? '⚪';
}

function deleteShark(id) {
    if (!confirm('Kustuta hai?')) return;
    useForm({}).delete(route('sharks.destroy', id));
}
</script>

<template>
    <Head title="Haid" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">🦈 Haid</h2>
                <Link :href="route('sharks.create')" class="create-btn">+ Lisa hai</Link>
            </div>
        </template>

        <div class="sharks-wrap">
            <div class="sharks-container">

                <!-- Filters -->
                <div class="filters-card">
                    <div class="filters-row">
                        <input v-model="search" type="text" placeholder="🔍 Otsi hainime..." class="filter-inp" />
                        <input v-model="habitat" type="text" placeholder="🌊 Elupaik..." class="filter-inp" />
                        <select v-model="dangerLevel" class="filter-inp">
                            <option value="">Kõik ohtlikkused</option>
                            <option value="madal">🟢 Madal</option>
                            <option value="keskmine">🟡 Keskmine</option>
                            <option value="kõrge">🔴 Kõrge</option>
                        </select>
                        <select v-model="sort" class="filter-inp">
                            <option value="created_at">Lisamise aeg</option>
                            <option value="title">Nimi</option>
                            <option value="max_length">Pikkus</option>
                        </select>
                        <select v-model="order" class="filter-inp">
                            <option value="desc">Kahanevalt</option>
                            <option value="asc">Kasvavalt</option>
                        </select>
                    </div>
                    <div class="results-count">{{ filtered.length }} haid leitud</div>
                </div>

                <!-- Empty -->
                <div v-if="filtered.length === 0" class="empty-state">
                    <div style="font-size:56px;">🦈</div>
                    <div class="empty-title">Haisid ei leitud</div>
                    <Link :href="route('sharks.create')" class="create-btn" style="margin-top:16px; display:inline-block;">+ Lisa esimene hai</Link>
                </div>

                <!-- Grid -->
                <div v-else class="sharks-grid">
                    <div v-for="shark in filtered" :key="shark.id" class="shark-card">
                        <div class="shark-img-wrap">
                            <img v-if="shark.image" :src="shark.image" :alt="shark.title" class="shark-img" />
                            <div v-else class="shark-img-placeholder">🦈</div>
                            <span class="danger-badge" :style="{ background: dangerColor(shark.danger_level) }">
                                {{ dangerEmoji(shark.danger_level) }} {{ shark.danger_level }}
                            </span>
                        </div>
                        <div class="shark-body">
                            <h3 class="shark-title">{{ shark.title }}</h3>
                            <p class="shark-desc">{{ shark.description.substring(0, 120) }}{{ shark.description.length > 120 ? '...' : '' }}</p>
                            <div class="shark-stats">
                                <span>📏 {{ shark.max_length }} m</span>
                                <span>🌊 {{ shark.habitat }}</span>
                            </div>
                            <div class="shark-meta">
                                <span class="shark-author">{{ shark.user?.name }}</span>
                                <div class="shark-actions">
                                    <Link :href="route('sharks.edit', shark.id)" class="btn-edit">✏️</Link>
                                    <button @click="deleteShark(shark.id)" class="btn-delete">🗑️</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.sharks-wrap { background: #f0f9ff; min-height: 100vh; padding: 32px 16px; font-family: 'Inter', sans-serif; }
.sharks-container { max-width: 1100px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px; }

.create-btn { background: #0284c7; color: white; border: none; border-radius: 8px; padding: 9px 18px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.15s; }
.create-btn:hover { background: #0369a1; }

.filters-card { background: white; border-radius: 14px; padding: 20px; border: 1px solid #e0f2fe; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
.filters-row { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 10px; }
.filter-inp { border: 1.5px solid #e0f2fe; border-radius: 8px; padding: 9px 12px; font-size: 13px; font-family: 'Inter', sans-serif; color: #0f172a; outline: none; flex: 1; min-width: 140px; transition: border 0.15s; }
.filter-inp:focus { border-color: #0284c7; }
.results-count { font-size: 12px; color: #94a3b8; }

.empty-state { text-align: center; background: white; border-radius: 16px; padding: 64px 32px; border: 1px solid #e0f2fe; }
.empty-title { font-size: 20px; font-weight: 700; color: #0f172a; margin-top: 12px; }

.sharks-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
.shark-card { background: white; border-radius: 14px; overflow: hidden; border: 1px solid #e0f2fe; box-shadow: 0 1px 3px rgba(0,0,0,0.05); transition: all 0.15s; }
.shark-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(2,132,199,0.1); border-color: #7dd3fc; }
.shark-img-wrap { position: relative; }
.shark-img { width: 100%; height: 180px; object-fit: cover; }
.shark-img-placeholder { width: 100%; height: 180px; background: #e0f2fe; display: flex; align-items: center; justify-content: center; font-size: 48px; }
.danger-badge { position: absolute; top: 10px; right: 10px; color: white; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; text-transform: capitalize; }
.shark-body { padding: 16px; }
.shark-title { font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 6px; }
.shark-desc { font-size: 13px; color: #64748b; line-height: 1.5; margin: 0 0 10px; }
.shark-stats { display: flex; gap: 12px; font-size: 12px; color: #0284c7; font-weight: 600; margin-bottom: 10px; }
.shark-meta { display: flex; justify-content: space-between; align-items: center; }
.shark-author { font-size: 11px; color: #94a3b8; }
.shark-actions { display: flex; gap: 6px; }
.btn-edit { text-decoration: none; background: #f0f9ff; border: 1px solid #e0f2fe; border-radius: 6px; padding: 4px 8px; font-size: 13px; transition: all 0.15s; }
.btn-edit:hover { background: #e0f2fe; }
.btn-delete { background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 4px 8px; font-size: 13px; cursor: pointer; transition: all 0.15s; }
.btn-delete:hover { background: #fee2e2; }
</style>