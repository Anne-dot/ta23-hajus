<script setup lang="ts">
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import Button from '@/components/ui/button/Button.vue';

interface Product {
    id: number;
    name: string;
    description: string;
    price: number;
    image: string;
    quantity: number;
}

interface Props {
    products: Product[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: '/products',
    },
];

// Handle image loading errors
const handleImageError = (event: Event) => {
    const target = event.target as HTMLImageElement;
    target.src = 'https://via.placeholder.com/400x400/f3f4f6/9ca3af?text=No+Image';
};

// Add to cart function (placeholder for now)
const addToCart = (product: Product) => {
    // TODO: Implement cart functionality
    console.log('Adding to cart:', product);
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto my-12 w-full max-w-7xl px-4">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold tracking-tight text-foreground">
                    Products
                </h1>
            </div>

            <!-- Product Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <Card v-for="product in products" :key="product.id"
                    class="overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Product Image -->
                    <div class="aspect-square bg-accent">
                        <img 
                            :src="product.image" 
                            :alt="product.name" 
                            class="w-full h-full object-cover"
                            @error="handleImageError"
                        />
                    </div>
                    
                    <!-- Product Info -->
                    <CardContent class="p-6">
                        <h3 class="text-xl font-semibold mb-2 text-card-foreground">
                            {{ product.name }}
                        </h3>
                        <p class="text-muted-foreground text-sm mb-4 line-clamp-2">
                            {{ product.description }}
                        </p>
                        
                        <!-- Price and Cart -->
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-card-foreground">
                                ${{ product.price }}
                            </span>
                            <Button 
                                size="sm" 
                                @click="addToCart(product)"
                                :disabled="product.quantity === 0"
                            >
                                {{ product.quantity === 0 ? 'Out of Stock' : 'Add to Cart' }}
                            </Button>
                        </div>
                        
                        <!-- Stock indicator (optional) -->
                        <div v-if="product.quantity > 0 && product.quantity <= 5" 
                            class="mt-2 text-xs text-amber-600 dark:text-amber-400">
                            Only {{ product.quantity }} left in stock
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Product count -->
            <div class="mt-8 text-sm text-muted-foreground text-center">
                Showing {{ products.length }} products
            </div>
        </div>
    </AppLayout>
</template>