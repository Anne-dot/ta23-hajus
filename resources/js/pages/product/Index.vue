<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { ShoppingCart } from 'lucide-vue-next';
import { ref, watch } from 'vue';

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

watch(
    () => page.props.flash.error,
    (error) => {
        if (error) {
            errorMessage.value = error as string;
            showError.value = true;
            setTimeout(() => {
                showError.value = false;
            }, 3000);
        }
    },
);

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
    router.post(
        route('cart.store'),
        {
            product_id: product.id,
            quantity: 1,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                // Show overlay briefly
                justAdded.value.add(product.id);

                // Remove overlay after 1 second
                setTimeout(() => {
                    justAdded.value.delete(product.id);
                }, 1000);
            },
        },
    );
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Error Toast -->
        <div
            v-if="showError"
            class="fixed right-4 top-20 z-50 rounded-lg bg-pink-500 px-6 py-4 text-white shadow-lg transition-all duration-300"
            :class="showError ? 'translate-x-0 opacity-100' : 'translate-x-full opacity-0'"
        >
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    ></path>
                </svg>
                <p class="font-medium">{{ errorMessage }}</p>
            </div>
        </div>

        <div class="mx-auto my-12 w-full max-w-7xl px-4">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold tracking-tight text-foreground">Products</h1>
            </div>

            <!-- Product Grid -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                <Card v-for="product in products" :key="product.id" class="flex flex-col overflow-hidden transition-shadow hover:shadow-lg">
                    <!-- Product Image with Button -->
                    <div class="relative aspect-square bg-accent">
                        <img
                            :src="`https://picsum.photos/seed/product-${product.id}/640/480`"
                            :alt="product.name"
                            class="h-full w-full object-cover"
                            @error="handleImageError"
                        />

                        <!-- Success Overlay -->
                        <div
                            v-if="justAdded.has(product.id)"
                            class="absolute inset-0 z-10 flex items-center justify-center bg-primary/90 transition-all duration-300"
                        >
                            <div class="text-center text-white">
                                <svg class="mx-auto mb-2 h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <p class="text-lg font-semibold">Added to Cart!</p>
                            </div>
                        </div>

                        <!-- Cart Indicator - Shows if product is in cart -->
                        <div
                            v-if="getCartQuantity(product.id) > 0 && !justAdded.has(product.id)"
                            class="absolute left-4 top-4 flex items-center gap-2 rounded-full bg-primary px-3 py-1 text-white shadow-lg"
                        >
                            <ShoppingCart class="h-4 w-4" />
                            <span class="font-semibold">{{ getCartQuantity(product.id) }}</span>
                        </div>

                        <!-- Low Stock Badge -->
                        <div
                            v-if="product.quantity > 0 && product.quantity <= 3"
                            class="absolute bottom-0 left-0 right-0 bg-primary/90 px-3 py-2 text-center text-sm font-semibold text-primary-foreground backdrop-blur-sm"
                        >
                            Only {{ product.quantity }} left!
                        </div>

                        <!-- Add to Cart Button on Image -->
                        <div class="absolute right-4 top-4">
                            <Button
                                size="sm"
                                @click="addToCart(product)"
                                :disabled="product.quantity === 0 || getCartQuantity(product.id) >= product.quantity"
                                class="shadow-lg"
                            >
                                {{
                                    product.quantity === 0
                                        ? 'Out of Stock'
                                        : getCartQuantity(product.id) >= product.quantity
                                          ? 'Max in Cart'
                                          : 'Add to Cart'
                                }}
                            </Button>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <CardContent class="flex flex-1 flex-col p-6">
                        <!-- Fixed height title container - increased height -->
                        <h3 class="mb-2 line-clamp-2 h-[3.5rem] text-xl font-semibold text-card-foreground">
                            {{ product.name }}
                        </h3>
                        <p class="mb-4 line-clamp-2 text-sm text-muted-foreground">
                            {{ product.description }}
                        </p>

                        <!-- Price and Stock at bottom -->
                        <div class="mt-auto">
                            <div class="flex items-end justify-between">
                                <span class="text-2xl font-bold text-card-foreground"> €{{ product.price }} </span>
                                <span v-if="product.quantity > 0" class="text-sm text-muted-foreground"> {{ product.quantity }} in stock </span>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Product count -->
            <div class="mt-8 text-center text-sm text-muted-foreground">Showing {{ products.length }} products</div>
        </div>
    </AppLayout>
</template>
