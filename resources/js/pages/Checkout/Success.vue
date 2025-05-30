<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import Button from '@/components/ui/button/Button.vue';
import { CheckCircle } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Checkout',
        href: '/checkout',
    },
    {
        title: 'Success',
        href: '/checkout/success',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto my-12 w-full max-w-2xl px-4">
            <Card>
                <CardHeader class="text-center">
                    <div class="flex justify-center mb-4">
                        <CheckCircle class="h-16 w-16 text-green-600" />
                    </div>
                    <CardTitle class="text-2xl">Payment Successful!</CardTitle>
                </CardHeader>
                <CardContent class="text-center space-y-4">
                    <p class="text-lg text-muted-foreground">
                        Thank you for your order. Your payment has been processed successfully.
                    </p>
                    <p class="text-sm text-muted-foreground">
                        A confirmation email will be sent to you shortly.
                    </p>
                    
                    <div class="pt-6 space-y-3">
                        <Button size="lg" @click="router.visit('/products')" class="w-full sm:w-auto">
                            Continue Shopping
                        </Button>
                        <div>
                            <Button variant="outline" @click="router.visit('/dashboard')" class="w-full sm:w-auto">
                                Go to Dashboard
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>