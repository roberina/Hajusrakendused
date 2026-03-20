<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

const props = defineProps({
    products:  { type: Array,  required: true },
    stripeKey: { type: String, required: true },
});

const cart = ref([]);
const cartOpen = ref(false);
const checkoutOpen = ref(false);

const form = ref({ first_name: '', last_name: '', email: '', phone: '' });
const formErrors = ref({});
const paying = ref(false);
const paymentStatus = ref(null);
const paymentError = ref('');

let stripe = null;
let cardElement = null;
let stripeLoaded = ref(false);

async function loadStripe() {
    if (stripeLoaded.value) return;
    await new Promise(resolve => {
        const s = document.createElement('script');
        s.src = 'https://js.stripe.com/v3/';
        s.onload = resolve;
        document.head.appendChild(s);
    });
    stripe = window.Stripe(props.stripeKey);
    stripeLoaded.value = true;
}

const cartTotal = computed(() =>
    cart.value.reduce((sum, i) => sum + i.price * i.quantity, 0).toFixed(2)
);
const cartCount = computed(() =>
    cart.value.reduce((sum, i) => sum + i.quantity, 0)
);

function addToCart(product, qty = 1) {
    const existing = cart.value.find(i => i.id === product.id);
    if (existing) { existing.quantity += qty; } else { cart.value.push({ ...product, quantity: qty }); }
}
function updateQty(id, qty) {
    const item = cart.value.find(i => i.id === id);
    if (item) item.quantity = Math.max(1, qty);
}
function removeFromCart(id) { cart.value = cart.value.filter(i => i.id !== id); }

const quantities = ref({});
function getQty(id) { return quantities.value[id] || 1; }
function setQty(id, val) { quantities.value[id] = Math.max(1, parseInt(val) || 1); }

async function openCheckout() {
    cartOpen.value = false;
    checkoutOpen.value = true;
    await loadStripe();
    setTimeout(() => {
        const elements = stripe.elements();
        cardElement = elements.create('card', {
            style: { base: { fontSize: '15px', color: '#0f172a', '::placeholder': { color: '#94a3b8' } } }
        });
        cardElement.mount('#card-element');
    }, 100);
}

async function pay() {
    formErrors.value = {};
    const errors = {};
    if (!form.value.first_name) errors.first_name = 'Nõutud';
    if (!form.value.last_name)  errors.last_name  = 'Nõutud';
    if (!form.value.email)      errors.email      = 'Nõutud';
    if (!form.value.phone)      errors.phone      = 'Nõutud';
    if (Object.keys(errors).length) { formErrors.value = errors; return; }

    paying.value = true;
    paymentError.value = '';

    try {
        const { data } = await axios.post(route('shop.checkout'), {
            ...form.value,
            cart: cart.value,
        });

        const result = await stripe.confirmCardPayment(data.clientSecret, {
            payment_method: {
                card: cardElement,
                billing_details: {
                    name: form.value.first_name + ' ' + form.value.last_name,
                    email: form.value.email,
                },
            },
        });

        if (result.error) {
            paymentError.value = result.error.message;
            paying.value = false;
            return;
        }

        await axios.post(route('shop.confirm'), {
            order_id: data.orderId,
            payment_intent: result.paymentIntent.id,
        });

        paymentStatus.value = 'success';
        cart.value = [];
        checkoutOpen.value = false;

    } catch (e) {
        paymentError.value = 'Makse ebaõnnestus. Proovi uuesti.';
        paying.value = false;
    }
}
</script>

<template>
    <Head title="E-pood" />
    <AuthenticatedLayout>
        <template #header>
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">🛒 E-pood</h2>
                <button class="cart-btn" @click="cartOpen = true">
                    🛒 Ostukorv
                    <span v-if="cartCount > 0" class="cart-badge">{{ cartCount }}</span>
                </button>
            </div>
        </template>

        <div class="shop-wrap">
            <div v-if="paymentStatus === 'success'" class="success-banner">
                ✅ Makse õnnestus! Täname ostu eest. Tellimus on kinnitatud.
            </div>

            <div class="products-grid">
                <div v-for="product in products" :key="product.id" class="product-card">
                    <img :src="product.image" :alt="product.name" class="product-img" />
                    <div class="product-body">
                        <h3 class="product-name">{{ product.name }}</h3>
                        <p class="product-desc">{{ product.description }}</p>
                        <div class="product-footer">
                            <span class="product-price">{{ product.price.toFixed(2) }} €</span>
                            <div class="product-actions">
                                <div class="qty-control">
                                    <button @click="setQty(product.id, getQty(product.id) - 1)">−</button>
                                    <span>{{ getQty(product.id) }}</span>
                                    <button @click="setQty(product.id, getQty(product.id) + 1)">+</button>
                                </div>
                                <button class="add-btn" @click="addToCart(product, getQty(product.id))">
                                    Lisa korvi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teleport paneelidele body tasemele, nii ei blokeeri nav -->
        <Teleport to="body">

            <!-- Cart sidebar -->
            <div v-if="cartOpen" class="overlay">
                <div class="overlay-bg" @click="cartOpen = false"></div>
                <div class="cart-panel">
                    <div class="panel-header">
                        <h3>🛒 Ostukorv</h3>
                        <button @click="cartOpen = false" class="close-btn">✕</button>
                    </div>

                    <div v-if="cart.length === 0" class="empty-cart">
                        <div style="font-size:40px;">🛒</div>
                        <p>Ostukorv on tühi</p>
                    </div>

                    <div v-else style="display:flex; flex-direction:column; flex:1; overflow:hidden;">
                        <div class="cart-items">
                            <div v-for="item in cart" :key="item.id" class="cart-item">
                                <img :src="item.image" class="cart-item-img" />
                                <div class="cart-item-info">
                                    <div class="cart-item-name">{{ item.name }}</div>
                                    <div class="cart-item-price">{{ item.price.toFixed(2) }} €</div>
                                </div>
                                <div class="cart-item-qty">
                                    <button @click="updateQty(item.id, item.quantity - 1)">−</button>
                                    <span>{{ item.quantity }}</span>
                                    <button @click="updateQty(item.id, item.quantity + 1)">+</button>
                                </div>
                                <button class="remove-btn" @click="removeFromCart(item.id)">🗑️</button>
                            </div>
                        </div>
                        <div class="cart-footer">
                            <div class="cart-total">Kokku: <strong>{{ cartTotal }} €</strong></div>
                            <button class="checkout-btn" @click="openCheckout">Maksma →</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Checkout modal -->
            <div v-if="checkoutOpen" class="overlay">
                <div class="overlay-bg" @click="checkoutOpen = false"></div>
                <div class="checkout-panel">
                    <div class="panel-header">
                        <h3>💳 Maksmine</h3>
                        <button @click="checkoutOpen = false" class="close-btn">✕</button>
                    </div>

                    <div class="checkout-body">
                        <div class="checkout-section">
                            <div class="section-title">Isikuandmed</div>
                            <div class="form-row">
                                <div class="field">
                                    <label>Eesnimi *</label>
                                    <input v-model="form.first_name" type="text" :class="['field-inp', formErrors.first_name ? 'err' : '']" />
                                    <span v-if="formErrors.first_name" class="err-msg">{{ formErrors.first_name }}</span>
                                </div>
                                <div class="field">
                                    <label>Perenimi *</label>
                                    <input v-model="form.last_name" type="text" :class="['field-inp', formErrors.last_name ? 'err' : '']" />
                                    <span v-if="formErrors.last_name" class="err-msg">{{ formErrors.last_name }}</span>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="field">
                                    <label>E-mail *</label>
                                    <input v-model="form.email" type="email" :class="['field-inp', formErrors.email ? 'err' : '']" />
                                    <span v-if="formErrors.email" class="err-msg">{{ formErrors.email }}</span>
                                </div>
                                <div class="field">
                                    <label>Telefon *</label>
                                    <input v-model="form.phone" type="tel" :class="['field-inp', formErrors.phone ? 'err' : '']" />
                                    <span v-if="formErrors.phone" class="err-msg">{{ formErrors.phone }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="checkout-section">
                            <div class="section-title">Tellimus</div>
                            <div v-for="item in cart" :key="item.id" class="summary-row">
                                <span>{{ item.name }} × {{ item.quantity }}</span>
                                <span>{{ (item.price * item.quantity).toFixed(2) }} €</span>
                            </div>
                            <div class="summary-total">
                                <span>Kokku</span>
                                <strong>{{ cartTotal }} €</strong>
                            </div>
                        </div>

                        <div class="checkout-section">
                            <div class="section-title">Kaardi andmed</div>
                            <div id="card-element" class="card-element"></div>
                            <div v-if="paymentError" class="payment-error">⚠️ {{ paymentError }}</div>
                        </div>

                        <button class="pay-btn" @click="pay" :disabled="paying">
                            {{ paying ? 'Töötleb...' : `Maksa ${cartTotal} €` }}
                        </button>

                        <p class="stripe-note">🔒 Makse töödeldakse turvaliselt Stripe kaudu</p>
                    </div>
                </div>
            </div>

        </Teleport>

    </AuthenticatedLayout>
</template>

<style>
.overlay {
    position: fixed;
    inset: 0;
    z-index: 99999;
    display: flex;
    justify-content: flex-end;
    font-family: 'Inter', sans-serif;
}
.overlay-bg {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.4);
}
.cart-panel {
    position: relative;
    z-index: 1;
    background: white;
    width: 400px;
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.checkout-panel {
    position: relative;
    z-index: 1;
    background: white;
    width: 520px;
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
}
.panel-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #e2e8f0; }
.panel-header h3 { font-size: 18px; font-weight: 700; color: #0f172a; margin: 0; }
.close-btn { background: none; border: none; font-size: 18px; cursor: pointer; color: #64748b; }
.empty-cart { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; gap: 8px; }
.cart-items { flex: 1; overflow-y: auto; padding: 16px; display: flex; flex-direction: column; gap: 12px; }
.cart-item { display: flex; align-items: center; gap: 10px; padding: 10px; border: 1px solid #e2e8f0; border-radius: 10px; }
.cart-item-img { width: 52px; height: 52px; object-fit: cover; border-radius: 8px; flex-shrink: 0; }
.cart-item-info { flex: 1; min-width: 0; }
.cart-item-name { font-size: 13px; font-weight: 600; color: #0f172a; }
.cart-item-price { font-size: 12px; color: #64748b; margin-top: 2px; }
.cart-item-qty { display: flex; align-items: center; gap: 6px; }
.cart-item-qty button { background: #f1f5f9; border: none; border-radius: 6px; width: 26px; height: 26px; cursor: pointer; font-size: 14px; font-weight: 700; }
.cart-item-qty span { font-size: 13px; font-weight: 600; min-width: 20px; text-align: center; }
.remove-btn { background: none; border: none; cursor: pointer; font-size: 16px; opacity: 0.5; }
.remove-btn:hover { opacity: 1; }
.cart-footer { padding: 16px 24px; border-top: 1px solid #e2e8f0; }
.cart-total { font-size: 16px; color: #0f172a; margin-bottom: 12px; }
.checkout-btn { width: 100%; background: #3b82f6; color: white; border: none; border-radius: 10px; padding: 13px; font-size: 15px; font-weight: 600; cursor: pointer; }
.checkout-btn:hover { background: #2563eb; }
.checkout-body { padding: 24px; display: flex; flex-direction: column; gap: 20px; }
.checkout-section { background: #f8fafc; border-radius: 12px; padding: 16px; }
.section-title { font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 12px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px; }
.form-row:last-child { margin-bottom: 0; }
.field { display: flex; flex-direction: column; gap: 4px; }
.field label { font-size: 12px; font-weight: 600; color: #475569; }
.field-inp { border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 9px 12px; font-size: 14px; font-family: 'Inter', sans-serif; color: #0f172a; outline: none; transition: border 0.15s; }
.field-inp:focus { border-color: #3b82f6; }
.field-inp.err { border-color: #fca5a5; }
.err-msg { font-size: 11px; color: #dc2626; }
.summary-row { display: flex; justify-content: space-between; font-size: 14px; color: #334155; padding: 4px 0; }
.summary-total { display: flex; justify-content: space-between; font-size: 15px; color: #0f172a; padding-top: 10px; margin-top: 8px; border-top: 1px solid #e2e8f0; }
.card-element { background: white; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 12px; }
.payment-error { color: #dc2626; font-size: 13px; margin-top: 8px; }
.pay-btn { background: #3b82f6; color: white; border: none; border-radius: 10px; padding: 14px; font-size: 15px; font-weight: 600; cursor: pointer; transition: background 0.15s; }
.pay-btn:hover:not(:disabled) { background: #2563eb; }
.pay-btn:disabled { opacity: 0.6; cursor: not-allowed; }
.stripe-note { text-align: center; font-size: 12px; color: #94a3b8; margin: 0; }
</style>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.shop-wrap { background: #f1f5f9; min-height: 100vh; padding: 32px 16px; font-family: 'Inter', sans-serif; }
.success-banner { max-width: 1100px; margin: 0 auto 24px; background: #f0fdf4; border: 1px solid #86efac; color: #15803d; border-radius: 12px; padding: 16px 20px; font-weight: 600; }
.products-grid { max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
.product-card { background: white; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; transition: all 0.15s; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
.product-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }
.product-img { width: 100%; height: 180px; object-fit: cover; }
.product-body { padding: 16px; }
.product-name { font-size: 15px; font-weight: 700; color: #0f172a; margin: 0 0 6px; }
.product-desc { font-size: 13px; color: #64748b; line-height: 1.5; margin: 0 0 14px; }
.product-footer { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; }
.product-price { font-size: 18px; font-weight: 700; color: #0f172a; }
.product-actions { display: flex; gap: 8px; align-items: center; }
.qty-control { display: flex; align-items: center; gap: 6px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 4px 8px; }
.qty-control button { background: none; border: none; cursor: pointer; font-size: 16px; color: #3b82f6; font-weight: 700; width: 20px; }
.qty-control span { font-size: 14px; font-weight: 600; min-width: 20px; text-align: center; }
.add-btn { background: #3b82f6; color: white; border: none; border-radius: 8px; padding: 8px 14px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background 0.15s; }
.add-btn:hover { background: #2563eb; }
.cart-btn { position: relative; background: #0f172a; color: white; border: none; border-radius: 10px; padding: 9px 18px; font-size: 14px; font-weight: 600; cursor: pointer; }
.cart-badge { position: absolute; top: -6px; right: -6px; background: #ef4444; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 11px; display: flex; align-items: center; justify-content: center; font-weight: 700; }
</style>