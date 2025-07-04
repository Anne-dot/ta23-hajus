<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardFooter from '@/components/ui/card/CardFooter.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import Table from '@/components/ui/table/Table.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
import TableHead from '@/components/ui/table/TableHead.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { Minus, Plus, ShoppingCart, Trash2 } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface CartItem {
    id: number;
    name: string;
    price: number;
    image: string;
    quantity: number;
}

interface Props {
    cartItems: Record<string, CartItem>;
    total: number;
    itemCount: number;
}

const props = defineProps<Props>();
const page = usePage();

const errorMessage = ref<string>('');
const showError = ref(false);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: '/products',
    },
    {
        title: 'Cart',
        href: '/cart',
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

watch(
    () => page.props.flash.success,
    (success) => {
        if (success) {
            showError.value = false;
        }
    },
);

const cartItemsArray = computed(() => {
    if (!props.cartItems) return [];
    return Object.values(props.cartItems);
});

const updateQuantity = (productId: number, quantity: number) => {
    if (quantity > 0) {
        router.patch(
            route('cart.update', productId),
            {
                quantity: quantity,
            },
            {
                preserveScroll: true,
                onError: (errors) => {
                    console.error('Update error:', errors);
                },
            },
        );
    }
};

// Remove item
const removeItem = (productId: number) => {
    router.delete(route('cart.destroy', productId), {
        preserveScroll: true,
        onError: (errors) => {
            console.error('Delete error:', errors);
        },
    });
};

// Clear cart
const clearCart = () => {
    if (confirm('Are you sure you want to clear your cart?')) {
        router.delete(route('cart.clear'), {
            preserveScroll: true,
        });
    }
};

const handleCheckout = () => {
    // Use a regular form submission for Stripe redirect
    // Inertia's router.post() uses AJAX which causes CORS issues with Stripe
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = route('checkout');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (csrfToken) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = '_token';
        input.value = csrfToken;
        form.appendChild(input);
    }

    document.body.appendChild(form);
    form.submit();
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
            <div class="mb-8 flex items-center gap-3">
                <ShoppingCart class="h-8 w-8" />
                <h1 class="text-3xl font-bold tracking-tight">Shopping Cart</h1>
            </div>

            <!-- Empty Cart -->
            <div v-if="props.itemCount === 0" class="flex flex-col items-center justify-center py-16">
                <ShoppingCart class="mb-4 h-16 w-16 text-muted-foreground" />
                <p class="mb-6 text-xl text-muted-foreground">Your cart is empty</p>
                <Button size="lg" @click="router.visit('/products')"> Start Shopping </Button>
            </div>

            <!-- Cart with Items -->
            <div v-else class="grid gap-8 lg:grid-cols-3">
                <!-- Desktop: Table View -->
                <div class="hidden md:block lg:col-span-2">
                    <Card>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-[100px]">Product</TableHead>
                                    <TableHead>Details</TableHead>
                                    <TableHead class="text-center">Quantity</TableHead>
                                    <TableHead class="text-right">Price</TableHead>
                                    <TableHead class="text-right">Total</TableHead>
                                    <TableHead class="w-[50px]"></TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="item in cartItemsArray" :key="item.id">
                                    <TableCell>
                                        <img
                                            :src="`https://picsum.photos/seed/product-${item.id}/640/480`"
                                            :alt="item.name"
                                            class="h-20 w-20 rounded object-cover"
                                        />
                                    </TableCell>
                                    <TableCell>
                                        <div>
                                            <p class="font-medium">{{ item.name }}</p>
                                            <p class="text-sm text-muted-foreground">ID: #{{ item.id }}</p>
                                        </div>
                                    </TableCell>
                                    <TableCell>
                                        <div class="flex items-center justify-center gap-2">
                                            <Button
                                                size="icon"
                                                variant="outline"
                                                class="h-8 w-8"
                                                @click="updateQuantity(item.id, item.quantity - 1)"
                                                :disabled="item.quantity <= 1"
                                            >
                                                <Minus class="h-4 w-4" />
                                            </Button>
                                            <span class="w-12 text-center font-medium">{{ item.quantity }}</span>
                                            <Button size="icon" variant="outline" class="h-8 w-8" @click="updateQuantity(item.id, item.quantity + 1)">
                                                <Plus class="h-4 w-4" />
                                            </Button>
                                        </div>
                                    </TableCell>
                                    <TableCell class="text-right">€{{ (item.price || 0).toFixed(2) }}</TableCell>
                                    <TableCell class="text-right font-medium"> €{{ ((item.price || 0) * item.quantity).toFixed(2) }} </TableCell>
                                    <TableCell>
                                        <Button size="icon" variant="ghost" class="h-8 w-8" @click="removeItem(item.id)">
                                            <Trash2 class="h-4 w-4 text-destructive" />
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </Card>

                    <!-- Action Buttons -->
                    <div class="mt-4 flex items-center justify-between">
                        <Button variant="outline" @click="router.visit('/products')"> Continue Shopping </Button>
                        <Button variant="outline" @click="clearCart" class="text-destructive"> Clear Cart </Button>
                    </div>
                </div>

                <!-- Mobile: Card View -->
                <div class="space-y-4 md:hidden lg:col-span-2">
                    <Card v-for="item in cartItemsArray" :key="item.id">
                        <CardContent class="p-4">
                            <div class="flex gap-4">
                                <img
                                    :src="`https://picsum.photos/seed/product-${item.id}/640/480`"
                                    :alt="item.name"
                                    class="h-24 w-24 rounded object-cover"
                                />
                                <div class="flex-1 space-y-2">
                                    <h3 class="font-semibold">{{ item.name }}</h3>
                                    <p class="text-sm text-muted-foreground">€{{ (item.price || 0).toFixed(2) }} each</p>

                                    <!-- Quantity Controls -->
                                    <div class="flex items-center gap-2">
                                        <Button
                                            size="icon"
                                            variant="outline"
                                            class="h-8 w-8"
                                            @click="updateQuantity(item.id, item.quantity - 1)"
                                            :disabled="item.quantity <= 1"
                                        >
                                            <Minus class="h-3 w-3" />
                                        </Button>
                                        <span class="w-8 text-center text-sm">{{ item.quantity }}</span>
                                        <Button size="icon" variant="outline" class="h-8 w-8" @click="updateQuantity(item.id, item.quantity + 1)">
                                            <Plus class="h-3 w-3" />
                                        </Button>
                                        <Button size="icon" variant="ghost" class="ml-auto h-8 w-8" @click="removeItem(item.id)">
                                            <Trash2 class="h-4 w-4 text-destructive" />
                                        </Button>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 flex justify-between border-t pt-3">
                                <span class="text-sm text-muted-foreground">Subtotal</span>
                                <span class="font-semibold">€{{ ((item.price || 0) * item.quantity).toFixed(2) }}</span>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Mobile Action Buttons -->
                    <div class="flex flex-col gap-2">
                        <Button variant="outline" @click="router.visit('/products')"> Continue Shopping </Button>
                        <Button variant="outline" @click="clearCart" class="text-destructive"> Clear Cart </Button>
                    </div>
                </div>

                <!-- Order Summary - Both Mobile and Desktop -->
                <div class="lg:col-span-1">
                    <Card>
                        <CardHeader>
                            <CardTitle>Order Summary</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground">Subtotal ({{ props.itemCount }} items)</span>
                                    <span>€{{ (props.total || 0).toFixed(2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground">Shipping</span>
                                    <span class="text-green-600">Free</span>
                                </div>
                            </div>

                            <div class="border-t pt-4">
                                <div class="flex justify-between">
                                    <span class="text-lg font-semibold">Total</span>
                                    <span class="text-lg font-semibold">€{{ (props.total || 0).toFixed(2) }}</span>
                                </div>
                            </div>
                        </CardContent>
                        <CardFooter>
                            <Button @click="handleCheckout" class="w-full" size="lg" :disabled="props.itemCount === 0"> Proceed to Checkout </Button>
                        </CardFooter>
                    </Card>

                    <!-- Trust Badges -->
                    <Card class="mt-4">
                        <CardContent class="p-4 text-center text-sm text-muted-foreground">
                            <p>✓ Secure Checkout</p>
                            <p>✓ Free Shipping</p>
                            <p>✓ 30-Day Returns</p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
