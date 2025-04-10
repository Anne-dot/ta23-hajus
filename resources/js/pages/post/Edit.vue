<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardFooter from '@/components/ui/card/CardFooter.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    post: Object
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Edit post',
        href: `/posts/${props.post.id}/edit`,
    },
];

const form = useForm({
    title: props.post.title,
    description: props.post.description,
});

function submit() {
    form.put(`/posts/${props.post.id}`);
}

function discard() {
    router.visit('/posts');
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto my-12 w-full max-w-2xl">
            <form @submit.prevent="submit">
                <Card>
                    <CardHeader>
                        <CardTitle>Edit post</CardTitle>
                        <CardDescription>Update your blog post</CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div>
                            <Label>Title</Label>
                            <Input v-model="form.title" />
                        </div>
                        <div>
                            <Label>Description</Label>
                            <Textarea v-model="form.description" class="flex-grow min-h-[200px]"></Textarea>
                        </div>
                    </CardContent>
                    <CardFooter class="flex justify-end gap-2">
                        <Button type="button" @click="discard" variant="outline"
                            class="border border-input bg-background hover:bg-accent hover:text-accent-foreground">
                            Discard
                        </Button>
                        <Button type="submit" :disabled="form.processing">Save changes</Button>
                    </CardFooter>
                </Card>
            </form>
        </div>
    </AppLayout>
</template>