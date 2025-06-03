<script setup lang="ts">
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Link } from '@inertiajs/vue3';

interface Post {
    id: number;
    title: string;
    description: string;
    created_at_for_humans: string;
    is_owner: boolean;
    can_edit: boolean;
    user?: {
        id: number;
        name: string;
    };
}

defineProps<{
    posts: Post[];
    auth: object;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Posts',
        href: '/posts',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto my-12 w-full max-w-2xl">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Blog Posts</h1>
                <Link
                    href="/posts/create"
                    class="inline-flex h-10 items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground ring-offset-background transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
                >
                    Create New Post
                </Link>
            </div>

            <div v-if="posts.data.length" class="space-y-6">
                <Card v-for="post in posts.data" :key="post.id">
                    <CardContent class="flex flex-col gap-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <Link :href="`/posts/${post.id}`" class="text-xl font-semibold text-primary hover:underline">
                                    {{ post.title }}
                                </Link>
                                <div class="mt-1 text-sm text-muted-foreground">
                                    By {{ post.user?.name || 'Unknown' }} | {{ post.created_at_for_humans }}
                                </div>
                            </div>

                            <div v-if="post.can_edit" class="flex gap-2">
                                <Link
                                    :href="`/posts/${post.id}/edit`"
                                    class="inline-flex h-8 items-center justify-center rounded-md bg-primary px-3 py-1 text-sm font-medium text-primary-foreground ring-offset-background transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
                                >
                                    Edit
                                </Link>
                                <Link
                                    :href="`/posts/${post.id}`"
                                    method="delete"
                                    as="button"
                                    class="inline-flex h-8 items-center justify-center rounded-md bg-black px-3 py-1 text-sm font-medium text-white ring-offset-background transition-colors hover:bg-gray-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
                                    onclick="return confirm('Are you sure you want to delete this post?')"
                                >
                                    Delete
                                </Link>
                            </div>
                        </div>

                        <CardDescription>{{ post.excerpt }}</CardDescription>

                        <div>
                            <Link :href="`/posts/${post.id}`" class="text-primary hover:underline"> Read more </Link>
                        </div>
                    </CardContent>
                </Card>

                <div class="mt-8 flex items-center justify-between">
                    <Link
                        v-if="posts.prev_page_url"
                        :href="posts.prev_page_url"
                        class="inline-flex h-10 items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium ring-offset-background transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
                    >
                        Previous
                    </Link>
                    <span
                        v-else
                        class="inline-flex h-10 items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium text-muted-foreground opacity-50"
                        >Previous</span
                    >

                    <span class="text-muted-foreground"> Page {{ posts.current_page }} of {{ posts.last_page }} </span>

                    <Link
                        v-if="posts.next_page_url"
                        :href="posts.next_page_url"
                        class="inline-flex h-10 items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium ring-offset-background transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
                    >
                        Next
                    </Link>
                    <span
                        v-else
                        class="inline-flex h-10 items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium text-muted-foreground opacity-50"
                        >Next</span
                    >
                </div>
            </div>

            <div v-else class="rounded-lg border bg-background py-12 text-center">
                <p class="text-lg text-muted-foreground">No posts found.</p>
                <Link
                    href="/posts/create"
                    class="mt-4 inline-flex h-10 items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground ring-offset-background transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
                >
                    Create your first post
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
