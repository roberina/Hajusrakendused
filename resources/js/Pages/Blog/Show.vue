<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    post:    { type: Object,  required: true },
    isAdmin: { type: Boolean, default: false },
});

const page = usePage();
const currentUserId = page.props.auth.user.id;
const commentForm = useForm({ body: '' });

function submitComment() {
    commentForm.post(route('blog.comment.store', props.post.id), {
        onSuccess: () => commentForm.reset(),
    });
}
function deleteComment(commentId) {
    if (!confirm('Kustuta kommentaar?')) return;
    useForm({}).delete(route('blog.comment.destroy', { post: props.post.id, comment: commentId }));
}
function deletePost() {
    if (!confirm('Kustuta postitus koos kõigi kommentaaridega?')) return;
    useForm({}).delete(route('blog.destroy', props.post.id));
}
function formatDate(dt) {
    return new Date(dt).toLocaleDateString('et-EE', { day: 'numeric', month: 'long', year: 'numeric' });
}
function formatDateTime(dt) {
    return new Date(dt).toLocaleDateString('et-EE', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}
function initials(name) {
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
}
</script>

<template>
    <Head :title="post.title" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('blog.index')" class="back-btn">← Tagasi</Link>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">✍️ Blogi</h2>
            </div>
        </template>

        <div class="show-wrap">
            <div class="show-container">

                <!-- Article -->
                <article class="article">
                    <div class="article-top">
                        <div class="author-row">
                            <div class="author-avatar">{{ initials(post.user.name) }}</div>
                            <div>
                                <div class="author-name">{{ post.user.name }}</div>
                                <div class="article-date">{{ formatDate(post.created_at) }}</div>
                            </div>
                        </div>
                        <div v-if="isAdmin" class="article-actions">
                            <Link :href="route('blog.edit', post.id)" class="btn-edit">✏️ Muuda</Link>
                            <button @click="deletePost" class="btn-delete">🗑️ Kustuta</button>
                        </div>
                    </div>

                    <h1 class="article-title">{{ post.title }}</h1>
                    <div class="article-body">{{ post.description }}</div>
                </article>

                <!-- Comments -->
                <section class="comments-section">
                    <h3 class="comments-heading">💬 Kommentaarid <span class="count">{{ post.comments.length }}</span></h3>

                    <div class="comment-form-card">
                        <form @submit.prevent="submitComment">
                            <textarea
                                v-model="commentForm.body"
                                class="comment-inp"
                                placeholder="Lisa kommentaar..."
                                rows="3"
                            ></textarea>
                            <div v-if="commentForm.errors.body" class="form-err">{{ commentForm.errors.body }}</div>
                            <button type="submit" class="submit-btn" :disabled="commentForm.processing || !commentForm.body.trim()">
                                {{ commentForm.processing ? 'Saadan...' : 'Postita' }}
                            </button>
                        </form>
                    </div>

                    <div v-if="post.comments.length === 0" class="no-comments">Kommentaare pole veel. Ole esimene!</div>

                    <div v-else class="comments-list">
                        <div v-for="comment in post.comments" :key="comment.id" class="comment-card">
                            <div class="comment-header">
                                <div class="comment-avatar">{{ initials(comment.user.name) }}</div>
                                <div class="comment-meta">
                                    <span class="comment-author">{{ comment.user.name }}</span>
                                    <span class="comment-date">{{ formatDateTime(comment.created_at) }}</span>
                                </div>
                                <button
                                    v-if="isAdmin || comment.user_id === currentUserId"
                                    @click="deleteComment(comment.id)"
                                    class="comment-delete"
                                >🗑️</button>
                            </div>
                            <p class="comment-body">{{ comment.body }}</p>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.show-wrap { background: #f8fafc; min-height: 100vh; padding: 40px 16px 80px; font-family: 'Inter', sans-serif; }
.show-container { max-width: 720px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px; }

.back-btn { font-size: 13px; color: #64748b; text-decoration: none; font-weight: 600; transition: color 0.15s; }
.back-btn:hover { color: #3b82f6; }

.article { background: white; border-radius: 16px; padding: 40px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
.article-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.author-row { display: flex; align-items: center; gap: 12px; }
.author-avatar { width: 42px; height: 42px; border-radius: 50%; background: #0f172a; color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; flex-shrink: 0; }
.author-name { font-size: 14px; font-weight: 600; color: #0f172a; }
.article-date { font-size: 12px; color: #94a3b8; margin-top: 2px; }
.article-actions { display: flex; gap: 8px; }
.btn-edit { text-decoration: none; background: #eff6ff; color: #3b82f6; border: 1px solid #bfdbfe; border-radius: 8px; padding: 7px 14px; font-size: 13px; font-weight: 600; transition: all 0.15s; }
.btn-edit:hover { background: #dbeafe; }
.btn-delete { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; border-radius: 8px; padding: 7px 14px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.15s; }
.btn-delete:hover { background: #fee2e2; }
.article-title { font-size: 28px; font-weight: 800; color: #0f172a; margin: 0 0 20px; line-height: 1.3; }
.article-body { font-size: 16px; color: #334155; line-height: 1.8; white-space: pre-wrap; }

.comments-section { display: flex; flex-direction: column; gap: 14px; }
.comments-heading { font-size: 16px; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px; }
.count { background: #f1f5f9; color: #64748b; border-radius: 20px; padding: 1px 8px; font-size: 12px; }

.comment-form-card { background: white; border-radius: 14px; padding: 20px; border: 1px solid #e2e8f0; }
.comment-inp { width: 100%; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 10px 14px; font-size: 14px; font-family: 'Inter', sans-serif; color: #0f172a; outline: none; resize: vertical; box-sizing: border-box; transition: border 0.15s; }
.comment-inp:focus { border-color: #3b82f6; }
.form-err { color: #dc2626; font-size: 12px; margin-top: 6px; }
.submit-btn { margin-top: 10px; background: #0f172a; color: white; border: none; border-radius: 8px; padding: 9px 20px; font-size: 14px; font-weight: 600; cursor: pointer; transition: background 0.15s; }
.submit-btn:hover:not(:disabled) { background: #3b82f6; }
.submit-btn:disabled { opacity: 0.5; cursor: not-allowed; }

.no-comments { background: white; border-radius: 12px; padding: 24px; text-align: center; font-size: 14px; color: #94a3b8; border: 1px solid #e2e8f0; }

.comments-list { display: flex; flex-direction: column; gap: 10px; }
.comment-card { background: white; border-radius: 12px; padding: 16px 20px; border: 1px solid #e2e8f0; }
.comment-header { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
.comment-avatar { width: 32px; height: 32px; border-radius: 50%; background: #f1f5f9; color: #64748b; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; border: 1px solid #e2e8f0; }
.comment-meta { flex: 1; }
.comment-author { font-size: 13px; font-weight: 600; color: #0f172a; display: block; }
.comment-date { font-size: 11px; color: #94a3b8; }
.comment-delete { background: none; border: none; cursor: pointer; font-size: 15px; opacity: 0.3; transition: opacity 0.15s; }
.comment-delete:hover { opacity: 1; }
.comment-body { font-size: 14px; color: #334155; line-height: 1.6; margin: 0; }
</style>    