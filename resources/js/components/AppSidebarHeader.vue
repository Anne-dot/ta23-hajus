<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { Button } from '@/components/ui/button';
import type { BreadcrumbItemType } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ShoppingCart } from 'lucide-vue-next';
import { computed } from 'vue';

defineProps<{
    breadcrumbs?: BreadcrumbItemType[];
}>();

const page = usePage();
const cartCount = computed(() => page.props.cart?.count || 0);
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2 flex-1">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>
        
        <!-- Cart Icon -->
        <Link :href="route('cart.index')" class="relative ml-auto">
            <Button variant="ghost" size="icon" class="group h-9 w-9 cursor-pointer relative">
                <ShoppingCart class="size-5 opacity-80 group-hover:opacity-100" />
                <span 
                    v-if="cartCount > 0"
                    class="absolute -top-1 -right-1 bg-primary text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-semibold"
                >
                    {{ cartCount }}
                </span>
            </Button>
        </Link>
    </header>
</template>
