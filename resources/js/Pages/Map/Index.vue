<script setup>
import { ref, onMounted, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

const props = defineProps({
    markers: { type: Array, default: () => [] },
});

// State
const markers     = ref([...props.markers]);
const mapEl       = ref(null);
const sidebarMode = ref('list'); // 'list' | 'add' | 'edit'
const selected    = ref(null);
const pendingLatLng = ref(null);

const form = ref({ name: '', description: '' });
const saving  = ref(false);
const deleting = ref(false);

let map = null;
let leafletMarkers = {};
let pendingMarkerObj = null;

// Init Leaflet
onMounted(async () => {
    // Load Leaflet CSS
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
    document.head.appendChild(link);

    // Load Leaflet JS
    await loadScript('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js');

    const L = window.L;

    map = L.map(mapEl.value).setView([58.5953, 25.0136], 7); // Eesti keskel

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(map);

    // Add existing markers
    markers.value.forEach(m => addLeafletMarker(m));

    // Click to add
    map.on('click', (e) => {
        pendingLatLng.value = e.latlng;
        form.value = { name: '', description: '' };
        sidebarMode.value = 'add';

        // Show pending pin
        if (pendingMarkerObj) pendingMarkerObj.remove();
        pendingMarkerObj = L.marker([e.latlng.lat, e.latlng.lng], {
            icon: L.divIcon({
                className: '',
                html: `<div style="width:28px;height:28px;background:#3b82f6;border:3px solid white;border-radius:50% 50% 50% 0;transform:rotate(-45deg);box-shadow:0 2px 8px rgba(0,0,0,0.3);"></div>`,
                iconSize: [28, 28],
                iconAnchor: [14, 28],
            }),
        }).addTo(map);
    });
});

function loadScript(src) {
    return new Promise(resolve => {
        if (document.querySelector(`script[src="${src}"]`)) { resolve(); return; }
        const s = document.createElement('script');
        s.src = src; s.onload = resolve;
        document.head.appendChild(s);
    });
}

function addLeafletMarker(m) {
    if (!window.L || !map) return;
    const L = window.L;
    const icon = L.divIcon({
        className: '',
        html: `<div style="width:24px;height:24px;background:#ef4444;border:3px solid white;border-radius:50% 50% 50% 0;transform:rotate(-45deg);box-shadow:0 2px 8px rgba(0,0,0,0.3);"></div>`,
        iconSize: [24, 24],
        iconAnchor: [12, 24],
    });
    const lm = L.marker([m.latitude, m.longitude], { icon })
        .addTo(map)
        .bindPopup(`<b>${m.name}</b>${m.description ? '<br>' + m.description : ''}`);
    lm.on('click', () => openEdit(m));
    leafletMarkers[m.id] = lm;
}

function removeLeafletMarker(id) {
    if (leafletMarkers[id]) {
        leafletMarkers[id].remove();
        delete leafletMarkers[id];
    }
}

function openEdit(m) {
    selected.value = m;
    form.value = { name: m.name, description: m.description || '' };
    sidebarMode.value = 'edit';
    if (pendingMarkerObj) { pendingMarkerObj.remove(); pendingMarkerObj = null; }
}

function cancelForm() {
    sidebarMode.value = 'list';
    selected.value = null;
    if (pendingMarkerObj) { pendingMarkerObj.remove(); pendingMarkerObj = null; }
    pendingLatLng.value = null;
}

function flyTo(m) {
    map?.flyTo([m.latitude, m.longitude], 14, { duration: 0.8 });
    leafletMarkers[m.id]?.openPopup();
}

// CRUD
async function saveNew() {
    if (!pendingLatLng.value || !form.value.name.trim()) return;
    saving.value = true;
    try {
        const { data } = await axios.post(route('map.store'), {
            name: form.value.name,
            description: form.value.description,
            latitude: pendingLatLng.value.lat,
            longitude: pendingLatLng.value.lng,
        });
        markers.value.unshift(data);
        if (pendingMarkerObj) { pendingMarkerObj.remove(); pendingMarkerObj = null; }
        addLeafletMarker(data);
        sidebarMode.value = 'list';
        pendingLatLng.value = null;
    } finally {
        saving.value = false;
    }
}

async function saveEdit() {
    if (!selected.value || !form.value.name.trim()) return;
    saving.value = true;
    try {
        const { data } = await axios.put(route('map.update', selected.value.id), {
            name: form.value.name,
            description: form.value.description,
        });
        const idx = markers.value.findIndex(m => m.id === data.id);
        if (idx !== -1) markers.value[idx] = data;
        leafletMarkers[data.id]?.setPopupContent(`<b>${data.name}</b>${data.description ? '<br>' + data.description : ''}`);
        sidebarMode.value = 'list';
        selected.value = null;
    } finally {
        saving.value = false;
    }
}

async function deleteMarker() {
    if (!selected.value) return;
    if (!confirm(`Kustuta marker "${selected.value.name}"?`)) return;
    deleting.value = true;
    try {
        await axios.delete(route('map.destroy', selected.value.id));
        removeLeafletMarker(selected.value.id);
        markers.value = markers.value.filter(m => m.id !== selected.value.id);
        sidebarMode.value = 'list';
        selected.value = null;
    } finally {
        deleting.value = false;
    }
}

function formatDate(dt) {
    return dt ? new Date(dt).toLocaleDateString('et-EE', { day: 'numeric', month: 'short', year: 'numeric' }) : '';
}
</script>

<template>
    <Head title="Kaart" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">🗺️ Kaardirakendus</h2>
        </template>

        <div class="map-page">

            <!-- Sidebar -->
            <div class="sidebar">

                <!-- List mode -->
                <div v-if="sidebarMode === 'list'" class="sidebar-inner">
                    <div class="sidebar-header">
                        <h3 class="sidebar-title">Markerid <span class="marker-count">{{ markers.length }}</span></h3>
                        <p class="sidebar-hint">Klõpsa kaardil uue markeri lisamiseks</p>
                    </div>

                    <div v-if="markers.length === 0" class="no-markers">
                        <div class="no-markers-icon">📍</div>
                        <p>Markereid pole veel lisatud</p>
                        <p class="no-markers-sub">Klõpsa kaardil esimese markeri lisamiseks</p>
                    </div>

                    <div v-else class="marker-list">
                        <div
                            v-for="m in markers"
                            :key="m.id"
                            class="marker-row"
                            @click="flyTo(m)"
                        >
                            <div class="marker-pin">📍</div>
                            <div class="marker-info">
                                <div class="marker-name">{{ m.name }}</div>
                                <div class="marker-desc" v-if="m.description">{{ m.description }}</div>
                                <div class="marker-date">{{ formatDate(m.added) }}</div>
                            </div>
                            <button class="edit-btn" @click.stop="openEdit(m)">✏️</button>
                        </div>
                    </div>
                </div>

                <!-- Add mode -->
                <div v-else-if="sidebarMode === 'add'" class="sidebar-inner">
                    <div class="sidebar-header">
                        <h3 class="sidebar-title">Lisa marker</h3>
                        <p class="sidebar-hint">
                            📍 {{ pendingLatLng ? pendingLatLng.lat.toFixed(5) + ', ' + pendingLatLng.lng.toFixed(5) : '' }}
                        </p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nimi *</label>
                        <input v-model="form.name" type="text" class="form-inp" placeholder="Markeri nimi" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kirjeldus</label>
                        <textarea v-model="form.description" class="form-inp form-ta" placeholder="Valikuline kirjeldus..." rows="3"></textarea>
                    </div>
                    <div class="form-actions">
                        <button class="btn-primary" @click="saveNew" :disabled="saving || !form.name.trim()">
                            {{ saving ? 'Salvestab...' : '✅ Salvesta' }}
                        </button>
                        <button class="btn-secondary" @click="cancelForm">Tühista</button>
                    </div>
                </div>

                <!-- Edit mode -->
                <div v-else-if="sidebarMode === 'edit'" class="sidebar-inner">
                    <div class="sidebar-header">
                        <h3 class="sidebar-title">Muuda markerit</h3>
                        <p class="sidebar-hint" v-if="selected">
                            📍 {{ selected.latitude.toFixed(5) }}, {{ selected.longitude.toFixed(5) }}
                        </p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nimi *</label>
                        <input v-model="form.name" type="text" class="form-inp" placeholder="Markeri nimi" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kirjeldus</label>
                        <textarea v-model="form.description" class="form-inp form-ta" placeholder="Valikuline kirjeldus..." rows="3"></textarea>
                    </div>
                    <div class="form-actions">
                        <button class="btn-primary" @click="saveEdit" :disabled="saving || !form.name.trim()">
                            {{ saving ? 'Salvestab...' : '✅ Salvesta' }}
                        </button>
                        <button class="btn-danger" @click="deleteMarker" :disabled="deleting">
                            {{ deleting ? 'Kustutab...' : '🗑️ Kustuta' }}
                        </button>
                        <button class="btn-secondary" @click="cancelForm">Tühista</button>
                    </div>
                </div>

            </div>

            <!-- Map -->
            <div ref="mapEl" class="map-el"></div>

        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.map-page {
    display: flex;
    height: calc(100vh - 120px);
    font-family: 'Inter', sans-serif;
}

/* Sidebar */
.sidebar {
    width: 320px;
    flex-shrink: 0;
    background: white;
    border-right: 1px solid #e2e8f0;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}
.sidebar-inner { padding: 20px; display: flex; flex-direction: column; gap: 12px; }
.sidebar-header { margin-bottom: 4px; }
.sidebar-title { font-size: 16px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px; margin: 0 0 4px; }
.marker-count { background: #eff6ff; color: #3b82f6; border-radius: 20px; padding: 1px 8px; font-size: 12px; font-weight: 600; }
.sidebar-hint { font-size: 12px; color: #94a3b8; margin: 0; }

/* No markers */
.no-markers { text-align: center; padding: 40px 20px; color: #94a3b8; font-size: 14px; }
.no-markers-icon { font-size: 40px; margin-bottom: 12px; }
.no-markers-sub { font-size: 12px; margin-top: 4px; }

/* Marker list */
.marker-list { display: flex; flex-direction: column; gap: 8px; }
.marker-row {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0;
    cursor: pointer; transition: all 0.15s;
}
.marker-row:hover { background: #f8fafc; border-color: #bfdbfe; }
.marker-pin { font-size: 18px; flex-shrink: 0; margin-top: 1px; }
.marker-info { flex: 1; min-width: 0; }
.marker-name { font-size: 14px; font-weight: 600; color: #0f172a; }
.marker-desc { font-size: 12px; color: #64748b; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.marker-date { font-size: 11px; color: #cbd5e0; margin-top: 4px; }
.edit-btn { background: none; border: none; cursor: pointer; font-size: 16px; padding: 2px; opacity: 0.5; transition: opacity 0.15s; flex-shrink: 0; }
.edit-btn:hover { opacity: 1; }

/* Form */
.form-group { display: flex; flex-direction: column; gap: 5px; }
.form-label { font-size: 12px; font-weight: 600; color: #475569; }
.form-inp {
    border: 1.5px solid #e2e8f0; border-radius: 8px;
    padding: 9px 12px; font-size: 14px; color: #0f172a;
    outline: none; transition: border 0.15s;
    font-family: inherit; width: 100%; box-sizing: border-box;
}
.form-inp:focus { border-color: #3b82f6; }
.form-ta { resize: vertical; }
.form-actions { display: flex; flex-direction: column; gap: 8px; margin-top: 4px; }
.btn-primary {
    background: #3b82f6; color: white; border: none; border-radius: 8px;
    padding: 10px; font-size: 14px; font-weight: 600; cursor: pointer;
    transition: background 0.15s;
}
.btn-primary:hover:not(:disabled) { background: #2563eb; }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-danger {
    background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;
    border-radius: 8px; padding: 10px; font-size: 14px; font-weight: 600;
    cursor: pointer; transition: all 0.15s;
}
.btn-danger:hover:not(:disabled) { background: #fee2e2; }
.btn-danger:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-secondary {
    background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;
    border-radius: 8px; padding: 10px; font-size: 14px; font-weight: 600;
    cursor: pointer; transition: all 0.15s;
}
.btn-secondary:hover { background: #f1f5f9; }

/* Map */
.map-el { flex: 1; }
</style>