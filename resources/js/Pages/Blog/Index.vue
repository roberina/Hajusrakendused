<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    posts:   { type: Array,   default: () => [] },
    isAdmin: { type: Boolean, default: false },
});

function formatDate(dt) {
    return new Date(dt).toLocaleDateString('et-EE', { day: 'numeric', month: 'long', year: 'numeric' });
}

function initials(name) {
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
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

                <!-- Empty -->
                <div v-if="posts.length === 0" class="empty-state">
                    <div class="empty-icon">📝</div>
                    <div class="empty-title">Postitusi pole veel</div>
                    <div class="empty-sub">Ole esimene, kes kirjutab!</div>
                    <Link :href="route('blog.create')" class="create-btn" style="display:inline-block; margin-top:20px;">+ Uus postitus</Link>
                </div>

                <div v-else class="posts-list">
                    <Link
                        v-for="post in posts"
                        :key="post.id"
                        :href="route('blog.show', post.id)"
                        class="post-card"
                    >
                        <div class="post-avatar">{{ initials(post.user.name) }}</div>
                        <div class="post-content">
                            <div class="post-meta">
                                <span class="post-author">{{ post.user.name }}</span>
                                <span class="sep">·</span>
                                <span class="post-date">{{ formatDate(post.created_at) }}</span>
                            </div>
                            <h3 class="post-title">{{ post.title }}</h3>
                            <p class="post-excerpt">{{ post.description.substring(0, 160) }}{{ post.description.length > 160 ? '...' : '' }}</p>
                            <div class="post-footer">
                                <span class="comment-badge">💬 {{ post.comments_count }} kommentaari</span>
                                <span class="read-more">Loe edasi →</span>
                            </div>
                        </div>
                    </Link>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.blog-wrap { background: #f8fafc; min-height: 100vh; padding: 40px 16px; font-family: 'Inter', sans-serif; }
.blog-container { max-width: 760px; margin: 0 auto; }

.create-btn {
    background: #0f172a; color: white; border: none; border-radius: 8px;
    padding: 9px 18px; font-size: 13px; font-weight: 600; cursor: pointer;
    text-decoration: none; transition: background 0.15s;
}
.create-btn:hover { background: #3b82f6; }

.empty-state { text-align: center; background: white; border-radius: 16px; padding: 64px 32px; border: 1px solid #e2e8f0; }
.empty-icon { font-size: 48px; margin-bottom: 16px; }
.empty-title { font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
.empty-sub { font-size: 14px; color: #94a3b8; }

.posts-list { display: flex; flex-direction: column; gap: 12px; }

.post-card {
    background: white; border-radius: 14px; padding: 24px;
    text-decoration: none; display: flex; gap: 16px;
    border: 1px solid #e2e8f0; transition: all 0.15s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.post-card:hover { border-color: #93c5fd; box-shadow: 0 4px 16px rgba(59,130,246,0.1); transform: translateY(-1px); }

.post-avatar {
    width: 44px; height: 44px; border-radius: 50%; background: #0f172a;
    color: white; display: flex; align-items: center; justify-content: center;
    font-size: 14px; font-weight: 700; flex-shrink: 0;
}

.post-content { flex: 1; min-width: 0; }
.post-meta { display: flex; gap: 6px; align-items: center; margin-bottom: 8px; }
.post-author { font-size: 13px; font-weight: 600; color: #0f172a; }
.sep { color: #cbd5e1; font-size: 12px; }
.post-date { font-size: 12px; color: #94a3b8; }
.post-title { font-size: 18px; font-weight: 700; color: #0f172a; margin: 0 0 8px; line-height: 1.4; }
.post-excerpt { font-size: 14px; color: #64748b; line-height: 1.6; margin: 0 0 14px; }
.post-footer { display: flex; justify-content: space-between; align-items: center; }
.comment-badge { font-size: 12px; color: #64748b; background: #f1f5f9; padding: 3px 10px; border-radius: 20px; }
.read-more { font-size: 13px; font-weight: 600; color: #3b82f6; }
</style>