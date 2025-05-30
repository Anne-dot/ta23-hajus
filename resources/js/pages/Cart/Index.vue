<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardFooter from '@/components/ui/card/CardFooter.vue';
import Button from '@/components/ui/button/Button.vue';
import Table from '@/components/ui/table/Table.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
import TableHead from '@/components/ui/table/TableHead.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import { Trash2, Plus, Minus, ShoppingCart } from 'lucide-vue-next';

interface CartItem {
    id: number;
    name: string;
    price: number;
    image: string;
    quantity: number;
}

interface Props {
    cart: Record<string, CartItem>;
    total: number;
    itemCount: number;
}

const props = defineProps<Props>();

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

// Convert cart object to array for easier iteration
const cartItems = computed(() => {
    if (!props.cart) return [];
    return Object.values(props.cart);
});

// Update quantity
const updateQuantity = (productId: number, quantity: number) => {
    if (quantity > 0) {
        console.log('Updating product:', productId, 'to quantity:', quantity);
        router.patch(route('cart.update', productId), {
            quantity: quantity
        }, {
            preserveScroll: true,
            onSuccess: () => {
                console.log('Update successful');
            },
            onError: (errors) => {
                console.error('Update error:', errors);
            }
        });
    }
};

// Remove item
const removeItem = (productId: number) => {
    router.delete(route('cart.destroy', productId), {
        preserveScroll: true,
        onError: (errors) => {
            console.error('Delete error:', errors);
        }
    });
};

// Clear cart
const clearCart = () => {
    if (confirm('Are you sure you want to clear your cart?')) {
        router.delete(route('cart.clear'), {
            preserveScroll: true
        });
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto my-12 w-full max-w-7xl px-4">
            <div class="flex items-center gap-3 mb-8">
                <ShoppingCart class="h-8 w-8" />
                <h1 class="text-3xl font-bold tracking-tight">Shopping Cart</h1>
            </div>

            <!-- Empty Cart -->
            <div v-if="props.itemCount === 0" class="flex flex-col items-center justify-center py-16">
                <ShoppingCart class="h-16 w-16 text-muted-foreground mb-4" />
                <p class="text-xl text-muted-foreground mb-6">Your cart is empty</p>
                <Button size="lg" @click="router.visit('/products')">
                    Start Shopping
                </Button>
            </div>

            <!-- Cart with Items -->
            <div v-else class="grid gap-8 lg:grid-cols-3">
                <!-- Desktop: Table View -->
                <div class="lg:col-span-2 hidden md:block">
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
                                <TableRow v-for="item in cartItems" :key="item.id">
                                    <TableCell>
                                        <img 
                                            :src="`https://picsum.photos/seed/product-${item.id}/640/480`"
                                            :alt="item.name"
                                            class="w-20 h-20 object-cover rounded"
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
                                            <Button 
                                                size="icon" 
                                                variant="outline"
                                                class="h-8 w-8"
                                                @click="updateQuantity(item.id, item.quantity + 1)"
                                            >
                                                <Plus class="h-4 w-4" />
                                            </Button>
                                        </div>
                                    </TableCell>
                                    <TableCell class="text-right">${{ item.price.toFixed(2) }}</TableCell>
                                    <TableCell class="text-right font-medium">
                                        ${{ (item.price * item.quantity).toFixed(2) }}
                                    </TableCell>
                                    <TableCell>
                                        <Button 
                                            size="icon" 
                                            variant="ghost"
                                            class="h-8 w-8"
                                            @click="removeItem(item.id)"
                                        >
                                            <Trash2 class="h-4 w-4 text-destructive" />
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </Card>

                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center mt-4">
                        <Button variant="outline" @click="router.visit('/products')">
                            Continue Shopping
                        </Button>
                        <Button variant="outline" @click="clearCart" class="text-destructive">
                            Clear Cart
                        </Button>
                    </div>
                </div>

                <!-- Mobile: Card View -->
                <div class="lg:col-span-2 md:hidden space-y-4">
                    <Card v-for="item in cartItems" :key="item.id">
                        <CardContent class="p-4">
                            <div class="flex gap-4">
                                <img 
                                    :src="`https://picsum.photos/seed/product-${item.id}/640/480`"
                                    :alt="item.name"
                                    class="w-24 h-24 object-cover rounded"
                                />
                                <div class="flex-1 space-y-2">
                                    <h3 class="font-semibold">{{ item.name }}</h3>
                                    <p class="text-sm text-muted-foreground">${{ item.price.toFixed(2) }} each</p>
                                    
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
                                        <Button 
                                            size="icon" 
                                            variant="outline"
                                            class="h-8 w-8"
                                            @click="updateQuantity(item.id, item.quantity + 1)"
                                        >
                                            <Plus class="h-3 w-3" />
                                        </Button>
                                        <Button 
                                            size="icon" 
                                            variant="ghost"
                                            class="h-8 w-8 ml-auto"
                                            @click="removeItem(item.id)"
                                        >
                                            <Trash2 class="h-4 w-4 text-destructive" />
                                        </Button>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t flex justify-between">
                                <span class="text-sm text-muted-foreground">Subtotal</span>
                                <span class="font-semibold">${{ (item.price * item.quantity).toFixed(2) }}</span>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Mobile Action Buttons -->
                    <div class="flex flex-col gap-2">
                        <Button variant="outline" @click="router.visit('/products')">
                            Continue Shopping
                        </Button>
                        <Button variant="outline" @click="clearCart" class="text-destructive">
                            Clear Cart
                        </Button>
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
                                    <span>${{ props.total.toFixed(2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground">Shipping</span>
                                    <span class="text-green-600">Free</span>
                                </div>
                            </div>
                            
                            <div class="border-t pt-4">
                                <div class="flex justify-between">
                                    <span class="text-lg font-semibold">Total</span>
                                    <span class="text-lg font-semibold">${{ props.total.toFixed(2) }}</span>
                                </div>
                            </div>
                        </CardContent>
                        <CardFooter>
                            <Button class="w-full" size="lg">
                                Proceed to Checkout
                            </Button>
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