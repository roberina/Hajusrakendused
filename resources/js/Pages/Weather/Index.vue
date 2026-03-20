<script setup>
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    weather:  { type: Object,  default: null  },
    forecast: { type: Array,   default: () => [] },
    error:    { type: String,  default: null  },
    cached:   { type: Boolean, default: false },
});

const form = useForm({ city: '' });
function search() { form.post(route('weather.search')); }

function getTheme(iconCode) {
    if (!iconCode) return { emoji: '🌡️', bg: '#1e293b' };
    const code = iconCode.replace('n', 'd');
    const map = {
        '01d': { emoji: '☀️',  bg: '#92400e' },
        '02d': { emoji: '⛅',  bg: '#1e3a5f' },
        '03d': { emoji: '☁️',  bg: '#374151' },
        '04d': { emoji: '☁️',  bg: '#1f2937' },
        '09d': { emoji: '🌧️', bg: '#1e1b4b' },
        '10d': { emoji: '🌦️', bg: '#1e3a5f' },
        '11d': { emoji: '⛈️', bg: '#0f0f1a' },
        '13d': { emoji: '❄️',  bg: '#1e3a5f' },
        '50d': { emoji: '🌫️', bg: '#374151' },
    };
    return map[code] ?? { emoji: '🌡️', bg: '#1e293b' };
}

function formatDay(ts) {
    return new Date(ts * 1000).toLocaleDateString('et-EE', { weekday: 'short', day: 'numeric', month: 'short' });
}
function formatTime(ts) {
    return new Date(ts * 1000).toLocaleTimeString('et-EE', { hour: '2-digit', minute: '2-digit' });
}
function windDir(deg) {
    return ['P', 'KP', 'K', 'KL', 'L', 'LL', 'I', 'IP'][Math.round(deg / 45) % 8];
}
</script>

<template>
    <Head title="Ilm" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">🌤️ Ilmateenistus</h2>
        </template>

        <div class="ilm-wrap">

            
            <div class="ilm-container">
                <div class="search-card">
                    <form @submit.prevent="search" class="search-form">
                        <span class="search-icon">🔍</span>
                        <input
                            v-model="form.city"
                            type="text"
                            placeholder="Otsi linna... (nt Tallinn, Tartu, London, Tokyo)"
                            class="search-inp"
                            :disabled="form.processing"
                            autocomplete="off"
                        />
                        <button type="submit" class="search-btn" :disabled="form.processing || !form.city.trim()">
                            {{ form.processing ? 'Otsib...' : 'Otsi' }}
                        </button>
                    </form>
                </div>

                
                <div v-if="error" class="err-box">⚠️ {{ error }}</div>

                
                <div v-if="weather">

                    
                    <div class="hero-card" :style="{ background: getTheme(weather.weather[0].icon).bg }">
                        <!-- Left -->
                        <div class="hero-left">
                            <div class="hero-location">
                                <span class="hero-city">{{ weather.name }}</span>
                                <img :src="`https://flagcdn.com/20x15/${weather.sys.country.toLowerCase()}.png`" class="hero-flag" />
                            </div>
                            <div class="hero-date">
                                {{ new Date().toLocaleDateString('et-EE', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) }}
                            </div>
                            <div class="hero-desc">
                                {{ getTheme(weather.weather[0].icon).emoji }}&nbsp; {{ weather.weather[0].description }}
                            </div>
                            <div class="hero-feels">Feels like <b>{{ Math.round(weather.main.feels_like) }}°C</b></div>
                            <div v-if="cached" class="hero-cache">📦 Andmed vahemälust</div>
                        </div>
                        
                        <div class="hero-right">
                            <div class="hero-temp">{{ Math.round(weather.main.temp) }}<span class="hero-deg">°C</span></div>
                            <div class="hero-minmax">↑ {{ Math.round(weather.main.temp_max) }}° &nbsp;·&nbsp; ↓ {{ Math.round(weather.main.temp_min) }}°</div>
                        </div>
                    </div>

                    
                    <div class="stats-row">
                        <div class="stat-item">
                            <div class="stat-ico">💧</div>
                            <div class="stat-val">{{ weather.main.humidity }}%</div>
                            <div class="stat-lbl">Niiskus</div>
                        </div>
                        <div class="stat-sep"></div>
                        <div class="stat-item">
                            <div class="stat-ico">💨</div>
                            <div class="stat-val">{{ weather.wind.speed }} <span class="stat-unit">m/s</span></div>
                            <div class="stat-lbl">Tuul · {{ windDir(weather.wind.deg) }}</div>
                        </div>
                        <div class="stat-sep"></div>
                        <div class="stat-item">
                            <div class="stat-ico">👁️</div>
                            <div class="stat-val">{{ (weather.visibility / 1000).toFixed(0) }} <span class="stat-unit">km</span></div>
                            <div class="stat-lbl">Nähtavus</div>
                        </div>
                        <div class="stat-sep"></div>
                        <div class="stat-item">
                            <div class="stat-ico">🌡️</div>
                            <div class="stat-val">{{ weather.main.pressure }} <span class="stat-unit">hPa</span></div>
                            <div class="stat-lbl">Õhurõhk</div>
                        </div>
                        <div class="stat-sep"></div>
                        <div class="stat-item">
                            <div class="stat-ico">🌅</div>
                            <div class="stat-val">{{ formatTime(weather.sys.sunrise) }}</div>
                            <div class="stat-lbl">Päikesetõus</div>
                        </div>
                        <div class="stat-sep"></div>
                        <div class="stat-item">
                            <div class="stat-ico">🌇</div>
                            <div class="stat-val">{{ formatTime(weather.sys.sunset) }}</div>
                            <div class="stat-lbl">Päikeseloojang</div>
                        </div>
                        <div class="stat-sep"></div>
                        <div class="stat-item">
                            <div class="stat-ico">☁️</div>
                            <div class="stat-val">{{ weather.clouds.all }}%</div>
                            <div class="stat-lbl">Pilvisus</div>
                        </div>
                    </div>

                    
                    <div v-if="forecast.length" class="forecast-card">
                        <div class="forecast-heading">5-päeva prognoos</div>
                        <div class="forecast-row">
                            <div v-for="day in forecast" :key="day.dt" class="fc-item">
                                <div class="fc-day">{{ formatDay(day.dt) }}</div>
                                <div class="fc-emoji">{{ getTheme(day.weather[0].icon).emoji }}</div>
                                <div class="fc-temp">{{ Math.round(day.main.temp) }}°C</div>
                                <div class="fc-desc">{{ day.weather[0].description }}</div>
                                <div class="fc-hum">💧 {{ day.main.humidity }}%</div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div v-else-if="!error" class="empty-card">
                    <div class="empty-icon">🌍</div>
                    <div class="empty-title">Otsi linna ilmainfot</div>
                    <div class="empty-sub">Sisesta linna nimi otsinguvälja ja vajuta "Otsi"</div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.ilm-wrap {
    background: #f1f5f9;
    min-height: 100vh;
    padding: 32px 16px;
    font-family: 'Inter', sans-serif;
}
.ilm-container {
    max-width: 860px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

/* Search */
.search-card {
    background: white;
    border-radius: 16px;
    padding: 16px 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}
.search-form {
    display: flex;
    align-items: center;
    gap: 10px;
}
.search-icon { font-size: 18px; flex-shrink: 0; }
.search-inp {
    flex: 1;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 14px;
    font-family: 'Inter', sans-serif;
    color: #1e293b;
    outline: none;
    transition: border 0.15s;
    background: #f8fafc;
}
.search-inp:focus { border-color: #3b82f6; background: white; }
.search-inp::placeholder { color: #94a3b8; }
.search-btn {
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 10px;
    padding: 10px 22px;
    font-size: 14px;
    font-weight: 600;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    transition: background 0.15s;
    flex-shrink: 0;
}
.search-btn:hover:not(:disabled) { background: #2563eb; }
.search-btn:disabled { opacity: 0.5; cursor: not-allowed; }

.err-box {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 14px;
}


.hero-card {
    border-radius: 20px;
    padding: 32px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
    flex-wrap: wrap;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
}
.hero-left { flex: 1; min-width: 200px; }
.hero-location { display: flex; align-items: center; gap: 10px; margin-bottom: 6px; }
.hero-city { font-size: 32px; font-weight: 700; color: white; letter-spacing: -0.5px; }
.hero-flag { border-radius: 2px; box-shadow: 0 1px 4px rgba(0,0,0,0.3); }
.hero-date { color: rgba(255,255,255,0.65); font-size: 13px; margin-bottom: 12px; }
.hero-desc { color: white; font-size: 17px; font-weight: 500; text-transform: capitalize; margin-bottom: 6px; }
.hero-feels { color: rgba(255,255,255,0.7); font-size: 13px; }
.hero-feels b { color: white; }
.hero-cache { color: rgba(255,255,255,0.4); font-size: 11px; margin-top: 8px; }
.hero-right { text-align: right; flex-shrink: 0; }
.hero-temp { font-size: 72px; font-weight: 700; color: white; line-height: 1; letter-spacing: -2px; }
.hero-deg { font-size: 36px; font-weight: 400; vertical-align: top; margin-top: 8px; display: inline-block; }
.hero-minmax { color: rgba(255,255,255,0.6); font-size: 13px; margin-top: 8px; text-align: right; }


.stats-row {
    background: white;
    border-radius: 16px;
    padding: 20px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 1px 3px rgba(0,0,0,0.07);
    flex-wrap: wrap;
    gap: 12px;
}
.stat-item { text-align: center; flex: 1; min-width: 70px; }
.stat-ico { font-size: 20px; margin-bottom: 6px; }
.stat-val { font-size: 16px; font-weight: 700; color: #0f172a; }
.stat-unit { font-size: 11px; font-weight: 500; color: #64748b; }
.stat-lbl { font-size: 11px; color: #94a3b8; margin-top: 3px; }
.stat-sep { width: 1px; height: 40px; background: #e2e8f0; flex-shrink: 0; }


.forecast-card {
    background: white;
    border-radius: 16px;
    padding: 20px 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.07);
}
.forecast-heading {
    font-size: 12px;
    font-weight: 600;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 16px;
}
.forecast-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; }
.fc-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 8px;
    text-align: center;
    transition: all 0.15s;
}
.fc-item:hover { background: #eff6ff; border-color: #bfdbfe; transform: translateY(-2px); }
.fc-day { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
.fc-emoji { font-size: 26px; margin: 8px 0; }
.fc-temp { font-size: 18px; font-weight: 700; color: #0f172a; }
.fc-desc { font-size: 10px; color: #94a3b8; margin-top: 4px; text-transform: capitalize; }
.fc-hum { font-size: 11px; color: #3b82f6; margin-top: 6px; font-weight: 500; }

/* Empty */
.empty-card {
    background: white;
    border-radius: 20px;
    padding: 64px 32px;
    text-align: center;
    box-shadow: 0 1px 3px rgba(0,0,0,0.07);
}
.empty-icon { font-size: 56px; margin-bottom: 16px; }
.empty-title { font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
.empty-sub { font-size: 14px; color: #94a3b8; }
</style>