<template>
    <div class="app-logo">
        <div class="logo-icon">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="1" y="1" width="6.5" height="6.5" rx="1.5" fill="white"/>
                <rect x="10.5" y="1" width="6.5" height="6.5" rx="1.5" fill="white"/>
                <rect x="1" y="10.5" width="6.5" height="6.5" rx="1.5" fill="white"/>
                <rect x="10.5" y="10.5" width="6.5" height="6.5" rx="1.5" fill="white"/>
            </svg>
        </div>
        <span class="logo-text">Hajusrakendused</span>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@700&display=swap');

.app-logo {
    display: flex;
    align-items: center;
    gap: 8px;
}
.logo-icon {
    width: 32px;
    height: 32px;
    background: #1a1a1a;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.logo-text {
    font-family: 'Outfit', sans-serif;
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.2px;
}
</style>