<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import InputError from '@/components/InputError.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Link, router, useForm } from '@inertiajs/vue3';

// Define interfaces for our data structures
interface User {
    id: number;
    name: string;
    email: string;
    is_admin: boolean;
}

interface Comment {
    id: number;
    post_id: number;
    user_id: number;
    comment: string;
    created_at: string;
    updated_at: string;
    created_at_for_humans: string;
    user: User;
}

interface Post {
    id: number;
    title: string;
    description: string;
    user_id: number;
    created_at: string;
    updated_at: string;
    user?: User;
    comments?: Comment[];
}

interface NavigationPost {
    id: number;
    title: string;
}

interface Auth {
    user: User | null;
}

// Define prop types
interface Props {
    post: Post;
    previousPost?: NavigationPost | null;
    nextPost?: NavigationPost | null;
    auth: Auth;
}

const props = defineProps<Props>();

// Type-safe form
const commentForm = useForm({
    post_id: props.post.id,
    comment: '',
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Posts',
        href: '/posts',
    },
    {
        title: props.post.title,
        href: `/posts/${props.post.id}`,
    },
];

function submitComment(): void {
    commentForm.post('/comments', {
        preserveScroll: true,
        onSuccess: () => commentForm.reset('comment'),
    });
}

// Check if current user is the post author
const isAuthor = props.post.user_id === props.auth?.user?.id;

function deleteComment(id: number): void {
    if (confirm('Are you sure you want to delete this comment?')) {
        router.delete(`/comments/${id}`, {
            preserveScroll: true,
        });
    }
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto my-12 w-full max-w-2xl">
            <!-- Post navigation -->
            <div class="mb-6 rounded-lg border bg-muted/30 p-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <!-- Previous post -->
                    <div v-if="previousPost" class="text-center md:text-left">
                        <p class="mb-2 text-xs uppercase tracking-wider text-muted-foreground">Previous Post</p>
                        <Link
                            :href="route('posts.show', previousPost.id)"
                            class="group inline-flex items-center gap-2 text-foreground transition-colors hover:text-primary"
                        >
                            <Icon name="arrow-left" :size="20" class="transition-transform group-hover:-translate-x-1" />
                            <span class="max-w-[200px] truncate font-medium">{{ previousPost.title }}</span>
                        </Link>
                    </div>
                    <div v-else class="hidden md:block"></div>

                    <!-- All posts -->
                    <div class="text-center">
                        <Link :href="route('posts.index')">
                            <Button variant="outline" class="w-full md:w-auto">
                                <Icon name="layout-grid" :size="16" class="mr-2" />
                                All Posts
                            </Button>
                        </Link>
                    </div>

                    <!-- Next post -->
                    <div v-if="nextPost" class="text-center md:text-right">
                        <p class="mb-2 text-xs uppercase tracking-wider text-muted-foreground">Next Post</p>
                        <Link
                            :href="route('posts.show', nextPost.id)"
                            class="group inline-flex items-center justify-center gap-2 text-foreground transition-colors hover:text-primary md:justify-end"
                        >
                            <span class="max-w-[200px] truncate font-medium">{{ nextPost.title }}</span>
                            <Icon name="arrow-right" :size="20" class="transition-transform group-hover:translate-x-1" />
                        </Link>
                    </div>
                    <div v-else class="hidden md:block"></div>
                </div>
            </div>

            <Card class="border bg-muted/30">
                <CardContent class="flex flex-col gap-4 pt-6">
                    <div class="flex items-center justify-between">
                        <CardTitle>{{ post.title }}</CardTitle>

                        <!-- Edit/Delete buttons (only for author) -->
                        <div v-if="isAuthor" class="flex gap-2">
                            <Link :href="route('posts.edit', post.id)">
                                <Button size="sm">Edit</Button>
                            </Link>
                            <Link
                                :href="route('posts.destroy', post.id)"
                                method="delete"
                                as="button"
                                :onBefore="() => confirm('Are you sure you want to delete this post?')"
                            >
                                <Button size="sm" class="bg-black text-white hover:bg-gray-800">Delete</Button>
                            </Link>
                        </div>
                    </div>

                    <!-- Author and date info -->
                    <div class="mb-4 text-sm text-muted-foreground">By {{ post.user?.name || 'Unknown' }} | {{ post.created_at_for_humans }}</div>

                    <CardDescription class="whitespace-pre-wrap break-words">{{ post.description }}</CardDescription>
                </CardContent>
            </Card>

            <!-- Comment section -->
            <div class="mt-8">
                <h2 class="mb-4 text-xl font-semibold">Comments</h2>
                <form v-if="auth.user" @submit.prevent="submitComment">
                    <div class="flex flex-col gap-2">
                        <div class="flex items-start gap-4">
                            <input type="hidden" v-model="commentForm.post_id" />
                            <Textarea v-model="commentForm.comment" placeholder="Add a comment..." class="flex-grow"></Textarea>
                            <Button type="submit" :disabled="commentForm.processing">Comment</Button>
                        </div>
                        <InputError :message="commentForm.errors.comment" class="ml-1" />
                    </div>
                </form>
                <div v-else class="rounded-lg border bg-muted/50 p-4 text-center text-muted-foreground">
                    <Link :href="route('login')" class="text-primary hover:underline">Sign in</Link> to leave a comment
                </div>
            </div>

            <div class="mt-4 space-y-4">
                <div v-if="post.comments && post.comments.length > 0">
                    <div v-for="comment in post.comments" :key="comment.id" class="rounded border p-3">
                        <div class="flex justify-between">
                            <strong class="text-sm text-foreground">{{ comment.user.email }}</strong>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-muted-foreground">{{ comment.created_at_for_humans }}</span>
                                <!-- Delete button for comment owner or admins -->
                                <Button
                                    v-if="auth.user && (comment.user_id === auth.user.id || auth.user.is_admin)"
                                    @click="deleteComment(comment.id)"
                                    size="sm"
                                    class="h-6 bg-black px-2 text-xs text-white hover:bg-gray-800"
                                >
                                    Delete
                                </Button>
                            </div>
                        </div>
                        <div class="mt-2 whitespace-pre-wrap break-words">{{ comment.comment }}</div>
                    </div>
                </div>
                <div v-else class="text-muted-foreground">No comments yet</div>
            </div>
        </div>
    </AppLayout>
</template>
