<script setup lang="ts">
import { ref } from 'vue'
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import * as z from 'zod'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Slider } from '@/components/ui/slider'
import {
    Form,
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import { BreadcrumbItem } from '@/types'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Subjects',
        href: '/display-subjects',
    },
    {
        title: 'Create emotion',
        href: '/subjects/create',
    },
];

const processing = ref(false)

const formSchema = toTypedSchema(
    z.object({
        title: z.string().min(2, 'Emotion name must be at least 2 characters'),
        description: z.string().min(10, 'Description must be at least 10 characters').max(150, 'Description must be less than 150 characters'),
        category: z.enum(['happy', 'sad', 'angry', 'fear', 'surprised', 'love']),
        emoji: z.string().optional(),
        intensity: z.array(z.number()).min(1).max(1).default([5]),
        color: z.string().optional(),
    })
)

const form = useForm({
    validationSchema: formSchema,
    initialValues: {
        title: '',
        description: '',
        category: '',
        emoji: '',
        intensity: [5],
        color: '#FFD700',
    },
})

const onSubmit = form.handleSubmit((values) => {
    processing.value = true
    
    router.post('/subjects', {
        ...values,
        intensity: values.intensity[0], // Convert array to number
    }, {
        onFinish: () => {
            processing.value = false
        },
        onSuccess: () => {
            router.visit('/display-subjects')
        },
    })
})
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto my-6 w-full max-w-3xl">
            <Card>
                <CardHeader>
                    <CardTitle>Create New Emotion</CardTitle>
                    <CardDescription>Add a new emotion to your collection</CardDescription>
                </CardHeader>
                <form @submit="onSubmit">
                    <CardContent class="space-y-4">
                        <!-- First row: Name, Category, Emoji -->
                        <div class="grid grid-cols-3 gap-4">
                            <FormField v-slot="{ componentField }" name="title">
                                <FormItem>
                                    <FormLabel>Emotion Name</FormLabel>
                                    <FormControl>
                                        <Input
                                            type="text"
                                            placeholder="e.g., Joyful"
                                            v-bind="componentField"
                                        />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="category">
                                <FormItem>
                                    <FormLabel>Category</FormLabel>
                                    <Select v-bind="componentField">
                                        <FormControl>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select category" />
                                            </SelectTrigger>
                                        </FormControl>
                                        <SelectContent>
                                            <SelectItem value="happy">😊 Happy</SelectItem>
                                            <SelectItem value="sad">😢 Sad</SelectItem>
                                            <SelectItem value="angry">😠 Angry</SelectItem>
                                            <SelectItem value="fear">😨 Fear</SelectItem>
                                            <SelectItem value="surprised">😲 Surprised</SelectItem>
                                            <SelectItem value="love">❤️ Love</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="emoji">
                                <FormItem>
                                    <FormLabel>Emoji</FormLabel>
                                    <FormControl>
                                        <Input
                                            type="text"
                                            placeholder="😊"
                                            maxlength="2"
                                            class="text-2xl text-center"
                                            v-bind="componentField"
                                        />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>
                        </div>

                        <!-- Second row: Description -->
                        <FormField v-slot="{ componentField }" name="description">
                            <FormItem>
                                <FormLabel>Description</FormLabel>
                                <FormControl>
                                    <Textarea
                                        placeholder="Describe how this emotion feels..."
                                        class="min-h-[80px] resize-none"
                                        maxlength="150"
                                        v-bind="componentField"
                                    />
                                </FormControl>
                                <FormDescription>
                                    {{ form.values.description?.length || 0 }}/150 characters
                                </FormDescription>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <!-- Third row: Intensity and Color -->
                        <div class="grid grid-cols-2 gap-4">
                            <FormField v-slot="{ componentField }" name="intensity">
                                <FormItem>
                                    <div class="flex justify-between items-center mb-2">
                                        <FormLabel>Intensity Level</FormLabel>
                                        <span class="text-sm font-medium">{{ componentField.modelValue?.[0] || 5 }}/10</span>
                                    </div>
                                    <FormControl>
                                        <Slider
                                            v-bind="componentField"
                                            :min="1"
                                            :max="10"
                                            :step="1"
                                            class="w-full"
                                        />
                                    </FormControl>
                                    <div class="w-full bg-accent rounded-full h-2 mt-2">
                                        <div 
                                            :class="[
                                                'h-2 rounded-full transition-all',
                                                (componentField.modelValue?.[0] || 5) <= 3 ? 'bg-emerald-500' :
                                                (componentField.modelValue?.[0] || 5) <= 7 ? 'bg-amber-500' :
                                                'bg-red-500'
                                            ]"
                                            :style="`width: ${(componentField.modelValue?.[0] || 5) * 10}%`"
                                        ></div>
                                    </div>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="color">
                                <FormItem>
                                    <FormLabel>Color</FormLabel>
                                    <div class="flex gap-2">
                                        <FormControl>
                                            <Input
                                                type="color"
                                                class="w-16 h-10 p-1 cursor-pointer"
                                                v-bind="componentField"
                                            />
                                        </FormControl>
                                        <FormControl>
                                            <Input
                                                type="text"
                                                placeholder="#FFD700"
                                                class="flex-1"
                                                v-bind="componentField"
                                            />
                                        </FormControl>
                                    </div>
                                    <FormMessage />
                                </FormItem>
                            </FormField>
                        </div>
                    </CardContent>
                    <CardFooter class="flex justify-end gap-2">
                        <Button 
                            type="button" 
                            variant="outline"
                            @click="router.visit('/display-subjects')"
                        >
                            Cancel
                        </Button>
                        <Button 
                            type="submit" 
                            :disabled="processing"
                        >
                            {{ processing ? 'Creating...' : 'Create Emotion' }}
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>