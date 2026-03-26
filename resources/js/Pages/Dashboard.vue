<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

const user = usePage().props.auth.user;
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="dashboard-wrap">
            <!-- Greeting -->
            <div class="greeting">
                <p class="greeting-label">Tere tulemast tagasi</p>
                <h1 class="greeting-title">{{ user.name }} <span class="wave">👋</span></h1>
                <p class="greeting-sub">Mida täna teha soovid?</p>
            </div>

            <!-- Cards grid -->
            <div class="cards-grid">

                <Link :href="route('weather.index')" class="feature-card feature-card--large">
                    <div class="card-icon-wrap">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M12 3V4M12 20V21M4.22 4.22L5.64 5.64M18.36 18.36L19.78 19.78M3 12H4M20 12H21M4.22 19.78L5.64 18.36M18.36 5.64L19.78 4.22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </div>
                    <div class="card-body">
                        <h2 class="card-title">Ilmateade</h2>
                        <p class="card-desc">Otsi reaalajas ilmainfot mis tahes linna kohta. Temperatuur, tuul ja ilmastiku ülevaade.</p>
                    </div>
                    <span class="card-arrow">→</span>
                </Link>

                <Link :href="route('map.index')" class="feature-card">
                    <div class="card-icon-wrap">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                            <circle cx="12" cy="9" r="2.5" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </div>
                    <div class="card-body">
                        <h2 class="card-title">Kaart</h2>
                        <p class="card-desc">Lisa, muuda ja halda kaardimärke interaktiivsel kaardil.</p>
                    </div>
                    <span class="card-arrow">→</span>
                </Link>

                <Link :href="route('blog.index')" class="feature-card">
                    <div class="card-icon-wrap">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M4 4h16v12H4z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                            <path d="M8 20h8M12 16v4M8 8h8M8 11h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div class="card-body">
                        <h2 class="card-title">Blogi</h2>
                        <p class="card-desc">Kirjuta, muuda ja kommenteeri postitusi. Täisfunktsionaalne blogisüsteem.</p>
                    </div>
                    <span class="card-arrow">→</span>
                </Link>

                <Link :href="route('shop.index')" class="feature-card">
                    <div class="card-icon-wrap">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                            <path d="M3 6h18M16 10a4 4 0 0 1-8 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div class="card-body">
                        <h2 class="card-title">Pood</h2>
                        <p class="card-desc">Sirvi tooteid, lisa ostukorvi ja lõpeta ost läbi Stripe'i maksesüsteemi.</p>
                    </div>
                    <span class="card-arrow">→</span>
                </Link>

                <Link :href="route('sharks.index')" class="feature-card feature-card--dark">
                    <div class="card-icon-wrap">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M3 12C3 7 8 3 12 3c2 0 4 1 5.5 2.5L21 9l-3 1-1 3-4 1-2 4-3-1-2-3-3-2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                            <circle cx="16" cy="8" r="1" fill="currentColor"/>
                        </svg>
                    </div>
                    <div class="card-body">
                        <h2 class="card-title">Haid</h2>
                        <p class="card-desc">Halda haiandmeid — lisa liike, muuda kirjeid ja tutvu hajusandmebaasiga.</p>
                    </div>
                    <span class="card-arrow">→</span>
                </Link>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Sans:wght@300;400;500&display=swap');

.dashboard-wrap {
    max-width: 1080px;
    margin: 0 auto;
    padding: 3rem 2rem 4rem;
    font-family: 'DM Sans', sans-serif;
}

/* Greeting */
.greeting {
    margin-bottom: 2.5rem;
}

.greeting-label {
    font-size: 0.75rem;
    font-weight: 500;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #999;
    margin-bottom: 0.5rem;
}

.greeting-title {
    font-family: 'Instrument Serif', serif;
    font-size: clamp(1.8rem, 4vw, 2.6rem);
    font-weight: 400;
    color: #0a0a0a;
    letter-spacing: -0.02em;
    line-height: 1.15;
    margin-bottom: 0.4rem;
}

.wave { font-style: normal; }

.greeting-sub {
    font-size: 0.9rem;
    font-weight: 300;
    color: #999;
}

/* Grid */
.cards-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

/* Cards */
.feature-card {
    background: #ffffff;
    border: 1.5px solid #e5e5e5;
    border-radius: 16px;
    padding: 1.75rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    text-decoration: none;
    color: inherit;
    transition: border-color 0.2s, transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
}

.feature-card:hover {
    border-color: #aaa;
    transform: translateY(-2px);
    box-shadow: 0 6px 24px rgba(0,0,0,0.07);
}

.feature-card--large {
    grid-column: span 2;
    flex-direction: row;
    align-items: flex-start;
}

.feature-card--large .card-body { flex: 1; min-width: 0; }
.feature-card--large .card-arrow { align-self: center; margin-top: 0; margin-left: auto; }

.feature-card--dark {
    background: #0a0a0a;
    border-color: #0a0a0a;
}

.feature-card--dark .card-title { color: #f0f0f0; }
.feature-card--dark .card-desc  { color: rgba(240,240,240,0.45); }
.feature-card--dark .card-icon-wrap { background: rgba(255,255,255,0.08); color: #fff; }
.feature-card--dark .card-arrow { background: rgba(255,255,255,0.08); color: #fff; }
.feature-card--dark:hover { border-color: #333; box-shadow: 0 6px 24px rgba(0,0,0,0.2); }

/* Icon */
.card-icon-wrap {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #0a0a0a;
    transition: background 0.2s;
}

.card-icon-wrap svg { width: 21px; height: 21px; }

.feature-card:not(.feature-card--dark):hover .card-icon-wrap {
    background: #e5e5e5;
}

/* Text */
.card-body { display: flex; flex-direction: column; }

.card-title {
    font-family: 'Instrument Serif', serif;
    font-size: 1.2rem;
    font-weight: 400;
    color: #0a0a0a;
    letter-spacing: -0.01em;
    margin-bottom: 0.4rem;
}

.card-desc {
    font-size: 0.85rem;
    font-weight: 300;
    color: #888;
    line-height: 1.65;
}

/* Arrow */
.card-arrow {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #f0f0f0;
    color: #0a0a0a;
    font-size: 0.95rem;
    transition: background 0.2s, transform 0.15s;
    flex-shrink: 0;
    align-self: flex-end;
    margin-top: auto;
}

.feature-card:not(.feature-card--dark):hover .card-arrow {
    background: #e0e0e0;
    transform: translateX(3px);
}

.feature-card--dark:hover .card-arrow {
    background: rgba(255,255,255,0.15);
    transform: translateX(3px);
}

/* Responsive */
@media (max-width: 768px) {
    .cards-grid { grid-template-columns: 1fr; }
    .feature-card--large { grid-column: span 1; flex-direction: column; }
    .feature-card--large .card-arrow { align-self: flex-end; margin-left: 0; margin-top: auto; }
    .dashboard-wrap { padding: 2rem 1rem 3rem; }
}
</style>