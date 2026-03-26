<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
    laravelVersion: { type: String, required: true },
    phpVersion: { type: String, required: true },
});
</script>

<template>
    <Head title="Welcome" />

    <div class="welcome-root">
        <div class="page-wrap">
            <!-- Header -->
            <header class="site-header">
                <div class="logo-wrap">
                    <div class="logo-icon">
                        <svg viewBox="0 0 16 16" fill="none"><rect x="2" y="2" width="5" height="5" fill="currentColor"/><rect x="9" y="2" width="5" height="5" fill="currentColor"/><rect x="2" y="9" width="5" height="5" fill="currentColor"/><rect x="9" y="9" width="5" height="5" fill="currentColor" opacity=".35"/></svg>
                    </div>
                    <span class="logo-text">Hajusrakendused</span>
                </div>

                <nav v-if="canLogin" class="nav-links">
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="nav-btn nav-btn--primary">Dashboard</Link>
                    <template v-else>
                        <Link :href="route('login')" class="nav-btn">Log in</Link>
                        <Link v-if="canRegister" :href="route('register')" class="nav-btn nav-btn--primary">Register</Link>
                    </template>
                </nav>
            </header>

            <!-- Hero -->
            <section class="hero">
                <p class="hero-eyebrow">Hajusrakendused</p>
                <h1 class="hero-title">Kõik tööriistad<br /><em>ühes kohas</em></h1>
                <p class="hero-sub">Ilm, kaardid, blogi, pood ja palju muud — logi sisse ja alusta.</p>
                <div class="hero-actions">
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="btn-dark">Ava Dashboard →</Link>
                    <template v-else>
                        <Link v-if="canRegister" :href="route('register')" class="btn-dark">Alusta tasuta →</Link>
                        <Link :href="route('login')" class="btn-outline">Logi sisse</Link>
                    </template>
                </div>
            </section>

            <!-- Cards -->
            <section class="cards-section">
                <div class="cards-grid">

                    <div class="feature-card feature-card--large">
                        <div class="card-icon-wrap">
                            <svg viewBox="0 0 24 24" fill="none"><path d="M12 3V4M12 20V21M4.22 4.22L5.64 5.64M18.36 18.36L19.78 19.78M3 12H4M20 12H21M4.22 19.78L5.64 18.36M18.36 5.64L19.78 4.22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.5"/></svg>
                        </div>
                        <div class="card-body">
                            <h2 class="card-title">Ilmateade</h2>
                            <p class="card-desc">Otsi reaalajas ilmainfot mis tahes linna kohta. Näed temperatuuri, tuule kiirust ja ilmastiku ülevaadet.</p>
                        </div>
                        <Link v-if="$page.props.auth.user" :href="route('weather.index')" class="card-arrow">→</Link>
                        <Link v-else :href="route('login')" class="card-arrow">→</Link>
                    </div>

                    <div class="feature-card">
                        <div class="card-icon-wrap">
                            <svg viewBox="0 0 24 24" fill="none"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><circle cx="12" cy="9" r="2.5" stroke="currentColor" stroke-width="1.5"/></svg>
                        </div>
                        <div class="card-body">
                            <h2 class="card-title">Kaart</h2>
                            <p class="card-desc">Lisa, muuda ja halda kaardimärke interaktiivsel kaardil.</p>
                        </div>
                        <Link v-if="$page.props.auth.user" :href="route('map.index')" class="card-arrow">→</Link>
                        <Link v-else :href="route('login')" class="card-arrow">→</Link>
                    </div>

                    <div class="feature-card">
                        <div class="card-icon-wrap">
                            <svg viewBox="0 0 24 24" fill="none"><path d="M4 4h16v12H4z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M8 20h8M12 16v4M8 8h8M8 11h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        </div>
                        <div class="card-body">
                            <h2 class="card-title">Blogi</h2>
                            <p class="card-desc">Kirjuta, muuda ja kommenteeri postitusi. Täisfunktsionaalne blogisüsteem.</p>
                        </div>
                        <Link v-if="$page.props.auth.user" :href="route('blog.index')" class="card-arrow">→</Link>
                        <Link v-else :href="route('login')" class="card-arrow">→</Link>
                    </div>

                    <div class="feature-card">
                        <div class="card-icon-wrap">
                            <svg viewBox="0 0 24 24" fill="none"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M3 6h18M16 10a4 4 0 0 1-8 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        </div>
                        <div class="card-body">
                            <h2 class="card-title">Pood</h2>
                            <p class="card-desc">Sirvi tooteid, lisa ostukorvi ja lõpeta ost läbi Stripe'i.</p>
                        </div>
                        <Link v-if="$page.props.auth.user" :href="route('shop.index')" class="card-arrow">→</Link>
                        <Link v-else :href="route('login')" class="card-arrow">→</Link>
                    </div>

                    <div class="feature-card feature-card--dark">
                        <div class="card-icon-wrap">
                            <svg viewBox="0 0 24 24" fill="none"><path d="M3 12C3 7 8 3 12 3c2 0 4 1 5.5 2.5L21 9l-3 1-1 3-4 1-2 4-3-1-2-3-3-2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><circle cx="16" cy="8" r="1" fill="currentColor"/></svg>
                        </div>
                        <div class="card-body">
                            <h2 class="card-title">Haid</h2>
                            <p class="card-desc">Halda haiandmeid — lisa liike, muuda kirjeid.</p>
                        </div>
                        <Link v-if="$page.props.auth.user" :href="route('sharks.index')" class="card-arrow">→</Link>
                        <Link v-else :href="route('login')" class="card-arrow">→</Link>
                    </div>

                </div>
            </section>

            <footer class="site-footer">
                Laravel v{{ laravelVersion }} · PHP v{{ phpVersion }}
            </footer>
        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Sans:wght@300;400;500&display=swap');

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.welcome-root {
    min-height: 100vh;
    background: #f5f5f3;
    font-family: 'DM Sans', sans-serif;
    color: #0a0a0a;
}

.page-wrap {
    max-width: 1080px;
    margin: 0 auto;
    padding: 0 2rem;
}

/* Header */
.site-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.75rem 0;
    border-bottom: 1px solid #e5e5e5;
}

.logo-wrap { display: flex; align-items: center; gap: 0.6rem; }

.logo-icon {
    width: 34px;
    height: 34px;
    background: #0a0a0a;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    flex-shrink: 0;
}

.logo-icon svg { width: 15px; height: 15px; }
.logo-text { font-size: 0.95rem; font-weight: 500; color: #0a0a0a; }

.nav-links { display: flex; align-items: center; gap: 0.5rem; }

.nav-btn {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 400;
    color: #666;
    border-radius: 8px;
    text-decoration: none;
    transition: color 0.15s, background 0.15s;
    font-family: 'DM Sans', sans-serif;
}

.nav-btn:hover { color: #0a0a0a; background: rgba(0,0,0,0.05); }

.nav-btn--primary {
    background: #0a0a0a;
    color: #fff;
}

.nav-btn--primary:hover { background: #222; color: #fff; }

/* Hero */
.hero {
    padding: 5.5rem 0 4rem;
    text-align: center;
    animation: fadeUp 0.5s cubic-bezier(0.16,1,0.3,1) both;
}

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}

.hero-eyebrow {
    font-size: 0.72rem;
    font-weight: 500;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #999;
    margin-bottom: 1.25rem;
}

.hero-title {
    font-family: 'Instrument Serif', serif;
    font-size: clamp(2.4rem, 5.5vw, 3.8rem);
    font-weight: 400;
    line-height: 1.1;
    letter-spacing: -0.03em;
    color: #0a0a0a;
    margin-bottom: 1.25rem;
}

.hero-title em { font-style: italic; color: #555; }

.hero-sub {
    font-size: 1rem;
    font-weight: 300;
    color: #888;
    max-width: 400px;
    margin: 0 auto 2.5rem;
    line-height: 1.7;
}

.hero-actions { display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap; }

.btn-dark {
    display: inline-flex;
    align-items: center;
    padding: 0.75rem 1.75rem;
    background: #0a0a0a;
    color: #fff;
    border-radius: 10px;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    transition: background 0.2s, transform 0.15s;
}

.btn-dark:hover { background: #222; transform: translateY(-1px); }

.btn-outline {
    display: inline-flex;
    align-items: center;
    padding: 0.75rem 1.75rem;
    background: #fff;
    color: #555;
    border: 1.5px solid #e5e5e5;
    border-radius: 10px;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.9rem;
    font-weight: 400;
    text-decoration: none;
    transition: border-color 0.2s, color 0.2s;
}

.btn-outline:hover { border-color: #aaa; color: #0a0a0a; }

/* Cards */
.cards-section { padding-bottom: 5rem; animation: fadeUp 0.5s 0.1s cubic-bezier(0.16,1,0.3,1) both; }

.cards-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.feature-card {
    background: #ffffff;
    border: 1.5px solid #e5e5e5;
    border-radius: 16px;
    padding: 1.75rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    transition: border-color 0.2s, transform 0.2s, box-shadow 0.2s;
}

.feature-card:hover {
    border-color: #aaa;
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.07);
}

.feature-card--large {
    grid-column: span 2;
    flex-direction: row;
    align-items: flex-start;
}

.feature-card--large .card-body { flex: 1; min-width: 0; }

.feature-card--dark {
    background: #0a0a0a;
    border-color: #0a0a0a;
    color: #f0f0f0;
}

.feature-card--dark .card-title { color: #f0f0f0; }
.feature-card--dark .card-desc { color: rgba(240,240,240,0.5); }
.feature-card--dark .card-icon-wrap { background: rgba(255,255,255,0.08); color: #fff; }
.feature-card--dark .card-arrow { background: rgba(255,255,255,0.08); color: #fff; }
.feature-card--dark:hover { border-color: #333; box-shadow: 0 4px 20px rgba(0,0,0,0.2); }

.card-icon-wrap {
    width: 42px;
    height: 42px;
    border-radius: 11px;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #0a0a0a;
}

.card-icon-wrap svg { width: 20px; height: 20px; }

.card-title {
    font-family: 'Instrument Serif', serif;
    font-size: 1.15rem;
    font-weight: 400;
    color: #0a0a0a;
    letter-spacing: -0.01em;
    margin-bottom: 0.35rem;
}

.card-desc { font-size: 0.85rem; font-weight: 300; color: #888; line-height: 1.65; }

.card-arrow {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #f0f0f0;
    color: #0a0a0a;
    text-decoration: none;
    font-size: 0.95rem;
    transition: background 0.2s, transform 0.15s;
    flex-shrink: 0;
    align-self: flex-end;
    margin-top: auto;
}

.feature-card--large .card-arrow { align-self: center; margin-top: 0; margin-left: auto; }
.card-arrow:hover { background: #e0e0e0; transform: translateX(2px); }

/* Footer */
.site-footer {
    text-align: center;
    padding: 2rem 0;
    font-size: 0.78rem;
    color: #bbb;
    border-top: 1px solid #e5e5e5;
    font-weight: 300;
}

@media (max-width: 768px) {
    .cards-grid { grid-template-columns: 1fr; }
    .feature-card--large { grid-column: span 1; flex-direction: column; }
    .hero { padding: 4rem 0 3rem; }
}
</style>