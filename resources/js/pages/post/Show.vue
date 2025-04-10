<script setup lang="ts">
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardFooter from '@/components/ui/card/CardFooter.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { ref } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    post: Object,
    auth: Object
});

const commentForm = useForm({
    post_id: props.post.id,
    comment: ''
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Show post',
        href: '/posts/show',
    },
];

function submitComment() {
    commentForm.post('/comments', {
        preserveScroll: true,
        onSuccess: () => commentForm.reset('comment')
    });
}

// Check if current user is the post author
const isAuthor = props.post.user_id === props.auth?.user?.id;

function deleteComment(id) {
  if (confirm('Are you sure you want to delete this comment?')) {
    router.delete(`/comments/${id}`, {
      preserveScroll: true
    });
  }
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto my-12 w-full max-w-2xl">
            <Card class="bg-transparent border-0">
                <CardContent class="flex flex-col gap-4">
                    <div class="flex justify-between items-center">
                        <CardTitle>{{ post.title }}</CardTitle>

                        <!-- Edit/Delete buttons (only for author) -->
                        <div v-if="isAuthor" class="flex gap-2">
                            <Link :href="`/posts/${post.id}/edit`"
                                class="px-3 py-1 bg-primary text-primary-foreground hover:bg-primary/90 rounded">Edit
                            </Link>
                            <Link :href="`/posts/${post.id}`" method="delete" as="button"
                                class="px-3 py-1 bg-black text-white hover:bg-gray-900 rounded"
                                onclick="return confirm('Are you sure you want to delete this post?')">Delete</Link>
                        </div>
                    </div>

                    <!-- Author and date info -->
                    <div class="text-sm text-gray-600 mb-4">
                        By {{ post.user?.name || 'Unknown' }} | {{ post.created_at }}
                    </div>

                    <CardDescription class="break-words whitespace-pre-wrap">{{ post.description }}</CardDescription>
                </CardContent>
            </Card>

            <!-- Comment section -->
            <div class="mt-8">
                <h2 class="text-xl font-semibold mb-4">Comments</h2>
                <form @submit.prevent="submitComment">
                    <div class="flex gap-4 items-start">
                        <input type="hidden" v-model="commentForm.post_id">
                        <Textarea v-model="commentForm.comment" placeholder="Add a comment..."
                            class="flex-grow"></Textarea>
                        <Button type="submit" :disabled="commentForm.processing">Comment</Button>
                    </div>
                </form>
            </div>

            <div class="mt-4 space-y-4">
                <div v-for="comment in post.comments" :key="comment.id" class="p-3 border rounded">
                    <div class="flex justify-between">
                        <strong class="text-sm text-blue-600">{{ comment.user.email }}</strong>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500">{{ comment.created_at }}</span>
                            <!-- Delete button only for admins -->
                            <button v-if="auth.user.is_admin" @click="deleteComment(comment.id)"
                                class="text-xs bg-red-500 text-white px-2 py-1 rounded">
                                Delete
                            </button>
                        </div>
                    </div>
                    <div class="mt-2 break-words whitespace-pre-wrap">{{ comment.comment }}</div>
                </div>
                <div v-if="!post.comments || post.comments.length === 0" class="text-gray-500">No comments yet</div>
            </div>
        </div>
    </AppLayout>
</template>