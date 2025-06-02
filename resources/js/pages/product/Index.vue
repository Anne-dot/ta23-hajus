<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import Button from '@/components/ui/button/Button.vue';
import { ShoppingCart } from 'lucide-vue-next';

interface Product {
    id: number;
    name: string;
    description: string;
    price: number;
    image: string;
    quantity: number;
}

interface CartItem {
    id: number;
    quantity: number;
}

interface Props {
    products: Product[];
    cartItems?: Record<string, CartItem>;
}

const props = defineProps<Props>();
const page = usePage();

const justAdded = ref<Set<number>>(new Set());
const errorMessage = ref<string>('');
const showError = ref(false);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: '/products',
    },
];

watch(() => page.props.flash.error, (error) => {
    if (error) {
        errorMessage.value = error as string;
        showError.value = true;
        setTimeout(() => {
            showError.value = false;
        }, 3000);
    }
});

const getCartQuantity = (productId: number): number => {
    return props.cartItems?.[productId]?.quantity || 0;
};

// Handle image loading errors
const handleImageError = (event: Event) => {
    const target = event.target as HTMLImageElement;
    target.src = 'https://via.placeholder.com/400x400/f3f4f6/9ca3af?text=No+Image';
};

// Add to cart function
const addToCart = (product: Product) => {
    router.post(route('cart.store'), {
        product_id: product.id,
        quantity: 1
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Show overlay briefly
            justAdded.value.add(product.id);
            
            // Remove overlay after 1 second
            setTimeout(() => {
                justAdded.value.delete(product.id);
            }, 1000);
        }
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Error Toast -->
        <div v-if="showError" 
            class="fixed top-20 right-4 z-50 bg-pink-500 text-white px-6 py-4 rounded-lg shadow-lg transition-all duration-300"
            :class="showError ? 'translate-x-0 opacity-100' : 'translate-x-full opacity-0'">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="font-medium">{{ errorMessage }}</p>
            </div>
        </div>
        
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
                    class="overflow-hidden hover:shadow-lg transition-shadow flex flex-col">
                    <!-- Product Image with Button -->
                    <div class="aspect-square bg-accent relative">
                        <img 
                            :src="`https://picsum.photos/seed/product-${product.id}/640/480`" 
                            :alt="product.name" 
                            class="w-full h-full object-cover"
                            @error="handleImageError"
                        />
                        
                        <!-- Success Overlay -->
                        <div v-if="justAdded.has(product.id)" 
                            class="absolute inset-0 bg-primary/90 flex items-center justify-center transition-all duration-300 z-10">
                            <div class="text-white text-center">
                                <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <p class="text-lg font-semibold">Added to Cart!</p>
                            </div>
                        </div>
                        
                        <!-- Cart Indicator - Shows if product is in cart -->
                        <div v-if="getCartQuantity(product.id) > 0 && !justAdded.has(product.id)" 
                            class="absolute top-4 left-4 bg-primary text-white rounded-full px-3 py-1 flex items-center gap-2 shadow-lg">
                            <ShoppingCart class="w-4 h-4" />
                            <span class="font-semibold">{{ getCartQuantity(product.id) }}</span>
                        </div>
                        
                        <!-- Low Stock Badge -->
                        <div v-if="product.quantity > 0 && product.quantity <= 3" 
                            class="absolute bottom-0 left-0 right-0 bg-primary/90 text-primary-foreground py-2 px-3 text-center font-semibold text-sm backdrop-blur-sm">
                            Only {{ product.quantity }} left!
                        </div>
                        
                        <!-- Add to Cart Button on Image -->
                        <div class="absolute top-4 right-4">
                            <Button 
                                size="sm"
                                @click="addToCart(product)"
                                :disabled="product.quantity === 0 || getCartQuantity(product.id) >= product.quantity"
                                class="shadow-lg"
                            >
                                {{ product.quantity === 0 ? 'Out of Stock' : 
                                   getCartQuantity(product.id) >= product.quantity ? 'Max in Cart' : 
                                   'Add to Cart' }}
                            </Button>
                        </div>
                    </div>
                    
                    <!-- Product Info -->
                    <CardContent class="p-6 flex flex-col flex-1">
                        <!-- Fixed height title container - increased height -->
                        <h3 class="text-xl font-semibold mb-2 text-card-foreground h-[3.5rem] line-clamp-2">
                            {{ product.name }}
                        </h3>
                        <p class="text-muted-foreground text-sm mb-4 line-clamp-2">
                            {{ product.description }}
                        </p>
                        
                        <!-- Price and Stock at bottom -->
                        <div class="mt-auto">
                            <div class="flex items-end justify-between">
                                <span class="text-2xl font-bold text-card-foreground">
                                    €{{ product.price }}
                                </span>
                                <span v-if="product.quantity > 0" class="text-sm text-muted-foreground">
                                    {{ product.quantity }} in stock
                                </span>
                            </div>
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