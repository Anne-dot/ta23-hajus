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


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Create post',
        href: '/posts/create',
    },
];

const form = useForm({
    title: '',
    description: '',
})

function submit() {
  form.post('/posts');
}

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto my-12 w-full max-w-2xl">
            <div>
                <Card>
                <CardHeader>
                    <CardTitle>Create post</CardTitle>
                    <CardDescription>Create a new blog post</CardDescription>
                </CardHeader>
                <CardContent class="flex flex-col gap-4">
                    <div>
                        <Label>Title</Label>
                        <Input v-model="form.title" />
                    </div>
                    <div>
                        <Label>Description</Label>
                        <Textarea 
                        v-model="form.description" 
                        class="flex-grow min-h-[200px]"
                        ></Textarea>
                        
                    </div>
                </CardContent>
                <CardFooter class="flex justify-end"> 
                    <Button type="button" @click="submit" :disabled="form.processing">Submit</Button>
                </CardFooter>
            </Card>
            </div>
            
        </div>

    </AppLayout>
</template>