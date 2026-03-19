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
    return new Date(dt).toLocaleDateString('et-EE', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
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

                <!-- Post -->
                <article class="post-article">
                    <div class="post-top">
                        <div class="post-meta">
                            <span class="post-author">{{ post.user.name }}</span>
                            <span class="post-date">{{ formatDate(post.created_at) }}</span>
                            <span v-if="post.updated_at !== post.created_at" class="post-edited">(muudetud {{ formatDate(post.updated_at) }})</span>
                        </div>
                        <div v-if="isAdmin" class="post-actions">
                            <Link :href="route('blog.edit', post.id)" class="action-edit">✏️ Muuda</Link>
                            <button @click="deletePost" class="action-delete">🗑️ Kustuta</button>
                        </div>
                    </div>

                    <h1 class="post-title">{{ post.title }}</h1>
                    <div class="post-body">{{ post.description }}</div>
                </article>

                <!-- Comments -->
                <div class="comments-section">
                    <h3 class="comments-title">💬 Kommentaarid ({{ post.comments.length }})</h3>

                    <!-- Add comment -->
                    <div class="comment-form-card">
                        <form @submit.prevent="submitComment">
                            <textarea
                                v-model="commentForm.body"
                                class="comment-inp"
                                placeholder="Kirjuta kommentaar..."
                                rows="3"
                            ></textarea>
                            <div v-if="commentForm.errors.body" class="form-err">{{ commentForm.errors.body }}</div>
                            <button type="submit" class="submit-btn" :disabled="commentForm.processing || !commentForm.body.trim()">
                                {{ commentForm.processing ? 'Saadan...' : 'Saada kommentaar' }}
                            </button>
                        </form>
                    </div>

                    <!-- Comments list -->
                    <div v-if="post.comments.length === 0" class="no-comments">
                        Kommentaare pole veel. Ole esimene!
                    </div>

                    <div v-else class="comments-list">
                        <div v-for="comment in post.comments" :key="comment.id" class="comment-card">
                            <div class="comment-header">
                                <div class="comment-left">
                                    <span class="comment-author">{{ comment.user.name }}</span>
                                    <span class="comment-date">{{ formatDate(comment.created_at) }}</span>
                                </div>
                                <button
                                    v-if="isAdmin || comment.user_id === currentUserId"
                                    @click="deleteComment(comment.id)"
                                    class="comment-delete"
                                    title="Kustuta kommentaar"
                                >🗑️</button>
                            </div>
                            <p class="comment-body">{{ comment.body }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.show-wrap { background: #f1f5f9; min-height: 100vh; padding: 32px 16px; font-family: 'Inter', sans-serif; }
.show-container { max-width: 720px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px; }

.back-btn { font-size: 14px; color: #64748b; text-decoration: none; font-weight: 500; }
.back-btn:hover { color: #3b82f6; }

/* Post */
.post-article { background: white; border-radius: 20px; padding: 36px; box-shadow: 0 1px 3px rgba(0,0,0,0.07); }
.post-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; flex-wrap: wrap; gap: 12px; }
.post-meta { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
.post-author { font-size: 13px; font-weight: 600; color: #3b82f6; }
.post-date { font-size: 12px; color: #94a3b8; }
.post-edited { font-size: 11px; color: #cbd5e0; }
.post-actions { display: flex; gap: 8px; }
.action-edit {
    text-decoration: none; background: #eff6ff; color: #3b82f6;
    border: 1px solid #bfdbfe; border-radius: 8px; padding: 6px 14px;
    font-size: 13px; font-weight: 600; transition: all 0.15s;
}
.action-edit:hover { background: #dbeafe; }
.action-delete {
    background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;
    border-radius: 8px; padding: 6px 14px; font-size: 13px; font-weight: 600;
    cursor: pointer; transition: all 0.15s;
}
.action-delete:hover { background: #fee2e2; }
.post-title { font-size: 28px; font-weight: 700; color: #0f172a; margin: 0 0 20px; line-height: 1.3; }
.post-body { font-size: 16px; color: #334155; line-height: 1.8; white-space: pre-wrap; }

/* Comments */
.comments-section { display: flex; flex-direction: column; gap: 14px; }
.comments-title { font-size: 16px; font-weight: 700; color: #0f172a; margin: 0; }

.comment-form-card { background: white; border-radius: 16px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.07); }
.comment-inp {
    width: 100%; border: 1.5px solid #e2e8f0; border-radius: 10px;
    padding: 10px 14px; font-size: 14px; font-family: 'Inter', sans-serif;
    color: #0f172a; outline: none; resize: vertical; box-sizing: border-box;
    transition: border 0.15s;
}
.comment-inp:focus { border-color: #3b82f6; }
.form-err { color: #dc2626; font-size: 12px; margin-top: 6px; }
.submit-btn {
    margin-top: 10px; background: #3b82f6; color: white; border: none;
    border-radius: 8px; padding: 9px 20px; font-size: 14px; font-weight: 600;
    cursor: pointer; transition: background 0.15s;
}
.submit-btn:hover:not(:disabled) { background: #2563eb; }
.submit-btn:disabled { opacity: 0.5; cursor: not-allowed; }

.no-comments { background: white; border-radius: 12px; padding: 24px; text-align: center; font-size: 14px; color: #94a3b8; }

.comments-list { display: flex; flex-direction: column; gap: 10px; }
.comment-card { background: white; border-radius: 12px; padding: 16px 20px; border: 1px solid #e2e8f0; }
.comment-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
.comment-left { display: flex; gap: 10px; align-items: center; }
.comment-author { font-size: 13px; font-weight: 600; color: #0f172a; }
.comment-date { font-size: 11px; color: #94a3b8; }
.comment-delete { background: none; border: none; cursor: pointer; font-size: 15px; opacity: 0.4; transition: opacity 0.15s; }
.comment-delete:hover { opacity: 1; }
.comment-body { font-size: 14px; color: #334155; line-height: 1.6; margin: 0; }
</style>