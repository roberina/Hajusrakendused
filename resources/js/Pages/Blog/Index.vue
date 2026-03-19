<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    posts: { type: Array, default: () => [] },
});

function formatDate(dt) {
    return new Date(dt).toLocaleDateString('et-EE', { day: 'numeric', month: 'long', year: 'numeric' });
}
</script>

<template>
    <Head title="Blogi" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">✍️ Blogi</h2>
                <Link :href="route('blog.create')" class="create-btn">+ Uus postitus</Link>
            </div>
        </template>

        <div class="blog-wrap">
            <div class="blog-container">

                <div v-if="posts.length === 0" class="empty-state">
                    <div class="empty-icon">📝</div>
                    <div class="empty-title">Postitusi pole veel</div>
                    <div class="empty-sub">Ole esimene, kes kirjutab!</div>
                    <Link :href="route('blog.create')" class="create-btn mt-4 inline-block">+ Uus postitus</Link>
                </div>

                <div v-else class="posts-grid">
                    <Link
                        v-for="post in posts"
                        :key="post.id"
                        :href="route('blog.show', post.id)"
                        class="post-card"
                    >
                        <div class="post-meta">
                            <span class="post-author">{{ post.user.name }}</span>
                            <span class="post-date">{{ formatDate(post.created_at) }}</span>
                        </div>
                        <h3 class="post-title">{{ post.title }}</h3>
                        <p class="post-excerpt">{{ post.description.substring(0, 160) }}{{ post.description.length > 160 ? '...' : '' }}</p>
                        <div class="post-footer">
                            <span class="comment-count">💬 {{ post.comments_count }} kommentaari</span>
                            <span class="read-more">Loe edasi →</span>
                        </div>
                    </Link>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.blog-wrap { background: #f1f5f9; min-height: 100vh; padding: 32px 16px; font-family: 'Inter', sans-serif; }
.blog-container { max-width: 860px; margin: 0 auto; }

.create-btn {
    background: #3b82f6; color: white; border: none; border-radius: 10px;
    padding: 9px 18px; font-size: 14px; font-weight: 600; cursor: pointer;
    text-decoration: none; transition: background 0.15s;
}
.create-btn:hover { background: #2563eb; }

.empty-state { text-align: center; background: white; border-radius: 20px; padding: 64px 32px; }
.empty-icon { font-size: 56px; margin-bottom: 16px; }
.empty-title { font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
.empty-sub { font-size: 14px; color: #94a3b8; }

.posts-grid { display: flex; flex-direction: column; gap: 16px; }
.post-card {
    background: white; border-radius: 16px; padding: 24px;
    text-decoration: none; display: block;
    border: 1px solid #e2e8f0;
    transition: all 0.15s; box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.post-card:hover { border-color: #bfdbfe; transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0,0,0,0.1); }
.post-meta { display: flex; gap: 12px; align-items: center; margin-bottom: 10px; }
.post-author { font-size: 13px; font-weight: 600; color: #3b82f6; }
.post-date { font-size: 12px; color: #94a3b8; }
.post-title { font-size: 20px; font-weight: 700; color: #0f172a; margin: 0 0 10px; line-height: 1.3; }
.post-excerpt { font-size: 14px; color: #64748b; line-height: 1.6; margin: 0 0 16px; }
.post-footer { display: flex; justify-content: space-between; align-items: center; }
.comment-count { font-size: 13px; color: #94a3b8; }
.read-more { font-size: 13px; font-weight: 600; color: #3b82f6; }
</style>