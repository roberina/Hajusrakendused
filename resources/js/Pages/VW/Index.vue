<script setup>
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

const cars = ref([]);
const loading = ref(true);
const error = ref(false);
const search = ref('');
const model = ref('');
const sort = ref('created_at');
const order = ref('desc');

async function fetchCars() {
    loading.value = true;
    error.value = false;
    try {
        const params = new URLSearchParams();
        if (search.value) params.set('search', search.value);
        if (model.value)  params.set('model', model.value);
        params.set('sort', sort.value);
        params.set('order', order.value);
        params.set('limit', '50');
        const { data } = await axios.get(`/external/vw?${params}`);
        cars.value = data.data ?? [];
    } catch (e) {
        error.value = true;
    } finally {
        loading.value = false;
    }
}

onMounted(fetchCars);

function formatPrice(p) {
    return new Intl.NumberFormat('et-EE', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(p);
}
function formatMileage(m) {
    return new Intl.NumberFormat('et-EE').format(m) + ' km';
}
</script>

<template>
    <Head title="VW Autod" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">🚗 Ken-Martti Volkswagenid</h2>
        </template>

        <div class="vw-wrap">
            <div class="vw-container">

                <!-- Filters -->
                <div class="filters-card">
                    <div class="filters-row">
                        <input v-model="search" @input="fetchCars" type="text" placeholder="🔍 Otsi..." class="filter-inp" />
                        <select v-model="model" @change="fetchCars" class="filter-inp">
                            <option value="">Kõik mudelid</option>
                            <option value="Golf">Golf</option>
                            <option value="Passat">Passat</option>
                            <option value="Tiguan">Tiguan</option>
                            <option value="Polo">Polo</option>
                            <option value="Touareg">Touareg</option>
                            <option value="Arteon">Arteon</option>
                            <option value="ID.4">ID.4</option>
                            <option value="Caddy">Caddy</option>
                            <option value="Transporter">Transporter</option>
                            <option value="Touran">Touran</option>
                        </select>
                        <select v-model="sort" @change="fetchCars" class="filter-inp">
                            <option value="created_at">Lisamise aeg</option>
                            <option value="price">Hind</option>
                            <option value="year">Aasta</option>
                            <option value="mileage">Läbisõit</option>
                        </select>
                        <select v-model="order" @change="fetchCars" class="filter-inp">
                            <option value="desc">Kahanevalt</option>
                            <option value="asc">Kasvavalt</option>
                        </select>
                    </div>
                    <div class="results-count">{{ cars.length }} autot leitud</div>
                </div>

                <div v-if="loading" class="loading">⏳ Laen autosid...</div>
                <div v-else-if="error" class="error-box">⚠️ API päring ebaõnnestus.</div>
                <div v-else-if="cars.length === 0" class="empty">🚗 Autosid ei leitud.</div>

                <div v-else class="cars-grid">
                    <div v-for="car in cars" :key="car.id" class="car-card">
                        <div class="car-img-wrap">
                            <img v-if="car.image" :src="car.image" :alt="car.title" class="car-img" />
                            <div v-else class="car-img-placeholder">🚗</div>
                            <span class="car-year-badge">{{ car.year }}</span>
                        </div>
                        <div class="car-body">
                            <div class="car-model">{{ car.model }}</div>
                            <h3 class="car-title">{{ car.title }}</h3>
                            <p class="car-desc">{{ car.description }}</p>
                            <div class="car-details">
                                <div class="detail-row">
                                    <span class="detail-icon">🔧</span>
                                    <span>{{ car.engine }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-icon">📏</span>
                                    <span>{{ formatMileage(car.mileage) }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-icon">🎨</span>
                                    <span>{{ car.color }}</span>
                                </div>
                            </div>
                            <div class="car-price">{{ formatPrice(car.price) }}</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.vw-wrap { background: #f8fafc; min-height: 100vh; padding: 32px 16px; font-family: 'Inter', sans-serif; }
.vw-container { max-width: 1100px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px; }

.filters-card { background: white; border-radius: 14px; padding: 20px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
.filters-row { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 10px; }
.filter-inp { border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 9px 12px; font-size: 13px; font-family: 'Inter', sans-serif; color: #0f172a; outline: none; flex: 1; min-width: 140px; transition: border 0.15s; }
.filter-inp:focus { border-color: #3b82f6; }
.results-count { font-size: 12px; color: #94a3b8; }

.loading { text-align: center; padding: 64px; font-size: 16px; color: #64748b; }
.error-box { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; border-radius: 10px; padding: 14px; font-size: 14px; }
.empty { text-align: center; padding: 64px; font-size: 16px; color: #94a3b8; }

.cars-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }

.car-card { background: white; border-radius: 14px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); transition: all 0.15s; }
.car-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.1); border-color: #93c5fd; }

.car-img-wrap { position: relative; }
.car-img { width: 100%; height: 180px; object-fit: cover; }
.car-img-placeholder { width: 100%; height: 180px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 48px; }
.car-year-badge { position: absolute; top: 10px; left: 10px; background: #0f172a; color: white; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 20px; }

.car-body { padding: 16px; }
.car-model { font-size: 11px; font-weight: 600; color: #3b82f6; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 4px; }
.car-title { font-size: 15px; font-weight: 700; color: #0f172a; margin: 0 0 6px; line-height: 1.3; }
.car-desc { font-size: 12px; color: #64748b; line-height: 1.5; margin: 0 0 12px; }

.car-details { display: flex; flex-direction: column; gap: 4px; margin-bottom: 12px; }
.detail-row { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #475569; }
.detail-icon { font-size: 13px; width: 18px; }

.car-price { font-size: 18px; font-weight: 800; color: #0f172a; border-top: 1px solid #f1f5f9; padding-top: 10px; margin-top: 4px; }

@media (max-width: 768px) {
    .cars-grid { grid-template-columns: 1fr; }
}
</style>