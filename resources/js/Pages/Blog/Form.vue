<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    post: { type: Object, default: null },
});

const isEdit = !!props.post;

const form = useForm({
    title:       props.post?.title       ?? '',
    description: props.post?.description ?? '',
});

function submit() {
    if (isEdit) {
        form.put(route('blog.update', props.post.id));
    } else {
        form.post(route('blog.store'));
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Muuda postitust' : 'Uus postitus'" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('blog.index')" class="back-btn">← Tagasi</Link>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ isEdit ? '✏️ Muuda postitust' : '✍️ Uus postitus' }}
                </h2>
            </div>
        </template>

        <div class="form-wrap">
            <div class="form-container">
                <div class="form-card">
                    <form @submit.prevent="submit" class="form-body">

                        <div class="field">
                            <label class="field-label">Pealkiri *</label>
                            <input
                                v-model="form.title"
                                type="text"
                                class="field-inp"
                                placeholder="Postituse pealkiri..."
                                :class="{ 'field-err': form.errors.title }"
                            />
                            <div v-if="form.errors.title" class="err-msg">{{ form.errors.title }}</div>
                        </div>

                        <div class="field">
                            <label class="field-label">Sisu *</label>
                            <textarea
                                v-model="form.description"
                                class="field-inp field-ta"
                                placeholder="Kirjuta oma postitus siia..."
                                rows="12"
                                :class="{ 'field-err': form.errors.description }"
                            ></textarea>
                            <div v-if="form.errors.description" class="err-msg">{{ form.errors.description }}</div>
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="submit-btn" :disabled="form.processing">
                                {{ form.processing ? 'Salvestab...' : (isEdit ? '✅ Salvesta muudatused' : '✅ Avalda postitus') }}
                            </button>
                            <Link :href="route('blog.index')" class="cancel-btn">Tühista</Link>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.form-wrap { background: #f1f5f9; min-height: 100vh; padding: 32px 16px; font-family: 'Inter', sans-serif; }
.form-container { max-width: 720px; margin: 0 auto; }
.back-btn { font-size: 14px; color: #64748b; text-decoration: none; font-weight: 500; }
.back-btn:hover { color: #3b82f6; }

.form-card { background: white; border-radius: 20px; padding: 36px; box-shadow: 0 1px 3px rgba(0,0,0,0.07); }
.form-body { display: flex; flex-direction: column; gap: 20px; }

.field { display: flex; flex-direction: column; gap: 6px; }
.field-label { font-size: 13px; font-weight: 600; color: #475569; }
.field-inp {
    border: 1.5px solid #e2e8f0; border-radius: 10px;
    padding: 11px 14px; font-size: 15px; font-family: 'Inter', sans-serif;
    color: #0f172a; outline: none; transition: border 0.15s; box-sizing: border-box; width: 100%;
}
.field-inp:focus { border-color: #3b82f6; }
.field-err { border-color: #fca5a5 !important; }
.field-ta { resize: vertical; line-height: 1.6; }
.err-msg { color: #dc2626; font-size: 12px; }

.form-footer { display: flex; gap: 12px; align-items: center; padding-top: 8px; }
.submit-btn {
    background: #3b82f6; color: white; border: none; border-radius: 10px;
    padding: 11px 24px; font-size: 14px; font-weight: 600; cursor: pointer;
    transition: background 0.15s;
}
.submit-btn:hover:not(:disabled) { background: #2563eb; }
.submit-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.cancel-btn {
    text-decoration: none; color: #64748b; font-size: 14px; font-weight: 500;
    padding: 11px 20px; border-radius: 10px; border: 1px solid #e2e8f0;
    transition: all 0.15s;
}
.cancel-btn:hover { background: #f8fafc; }
</style>