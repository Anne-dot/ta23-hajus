<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import Button from '@/components/ui/button/Button.vue';
import { BreadcrumbItem } from '@/types';
import { ref } from 'vue';
import Table from '@/components/ui/table/Table.vue';
import TableCaption from '@/components/ui/table/TableCaption.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import TableHead from '@/components/ui/table/TableHead.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';

interface Post {
  id: number,
  title: string,
  description: string,
  created_at_for_humans: string
}

defineProps<{
  posts: Post[],
  auth: Object
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Posts',
    href: '/posts',
  },
];

const postToDelete = ref();

const deletePost = () => {
  router.delete(route('posts.destroy', postToDelete.value), {
    onSuccess: () => {
      postToDelete.value = undefined
    }
  })
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto my-12 w-full max-w-2xl">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Blog Posts</h1>
        <Link href="/posts/create" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
          Create New Post
        </Link>
      </div>

      <div v-if="posts.data.length" class="space-y-6">
        <Card v-for="post in posts.data" :key="post.id">
          <CardContent class="flex flex-col gap-4">
            <div class="flex justify-between items-start">
              <div>
                <Link :href="`/posts/${post.id}`" class="text-xl font-semibold text-primary hover:underline">
                  {{ post.title }}
                </Link>
                <div class="text-sm text-muted-foreground mt-1">
                  By {{ post.user?.name || 'Unknown' }} | {{ post.created_at_for_humans }}
                </div>
              </div>
              
              <div v-if="post.is_owner" class="flex gap-2">
                <Link :href="`/posts/${post.id}/edit`" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-8 px-3 py-1">
                  Edit
                </Link>
                <Link 
                  :href="`/posts/${post.id}`" 
                  method="delete" 
                  as="button"
                  class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-black text-white hover:bg-gray-900 h-8 px-3 py-1"
                  onclick="return confirm('Are you sure you want to delete this post?')"
                >
                  Delete
                </Link>
              </div>
            </div>
            
            <CardDescription>{{ post.excerpt }}</CardDescription>
            
            <div>
              <Link :href="`/posts/${post.id}`" class="text-primary hover:underline">
                Read more
              </Link>
            </div>
          </CardContent>
        </Card>
        
       
        <div class="flex justify-between items-center mt-8">
          <Link 
            v-if="posts.prev_page_url" 
            :href="posts.prev_page_url" 
            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2"
          >
            Previous
          </Link>
          <span v-else class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-input bg-background text-muted-foreground h-10 px-4 py-2 opacity-50">Previous</span>
          
          <span class="text-muted-foreground">
            Page {{ posts.current_page }} of {{ posts.last_page }}
          </span>
          
          <Link 
            v-if="posts.next_page_url" 
            :href="posts.next_page_url" 
            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2"
          >
            Next
          </Link>
          <span v-else class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-input bg-background text-muted-foreground h-10 px-4 py-2 opacity-50">Next</span>
        </div>
      </div>
      
      <div v-else class="text-center py-12 bg-background rounded-lg border">
        <p class="text-muted-foreground text-lg">No posts found.</p>
        <Link href="/posts/create" class="mt-4 inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
          Create your first post
        </Link>
      </div>
    </div>
  </AppLayout>
</template>