<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import Table from '@/components/ui/table/Table.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
import TableHead from '@/components/ui/table/TableHead.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Link } from '@inertiajs/vue3';

interface Props {
    data: any[];
    customFields: string[];
    currentType: string;
    datasets: string[];
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Subjects',
        href: '/display-subjects',
    },
];

// Function to get the value from nested properties
const getFieldValue = (item: any, field: string) => {
    return item[field] || '-';
};

// Function to format field names for display
const formatFieldName = (field: string) => {
    return field
        .split('_')
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
};

// Handle image loading errors
const handleImageError = (event: Event, title: string) => {
    const target = event.target as HTMLImageElement;
    // Use a placeholder image or generate one based on the title
    target.src = `https://via.placeholder.com/300x450/1a1a1a/ffffff?text=${encodeURIComponent(title)}`;
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto my-12 w-full max-w-7xl px-4">
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">
                        {{ currentType === 'emotions' ? 'Emotions Display' : 'Movies Display' }}
                    </h1>
                    <Link v-if="currentType === 'emotions'" href="/subjects/create">
                        <Button class="bg-primary text-primary-foreground hover:bg-primary/90"> Add New </Button>
                    </Link>
                </div>
            </div>

            <!-- Grid layout for emotions -->
            <div v-if="currentType === 'emotions'" class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                <Card v-for="(item, index) in data" :key="index" class="transition-all hover:scale-[1.02] hover:shadow-lg">
                    <CardContent class="p-6">
                        <!-- Emoji and Title on same line -->
                        <div class="mb-4 flex items-center justify-center gap-3">
                            <span class="text-4xl">{{ item.emoji }}</span>
                            <h3 class="text-xl font-semibold text-card-foreground">{{ item.title }}</h3>
                        </div>

                        <!-- Category badge -->
                        <div class="mb-4 text-center">
                            <span
                                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium ring-1 ring-inset"
                                :class="{
                                    'bg-yellow-50 text-yellow-700 ring-yellow-600/20 dark:bg-yellow-500/10 dark:text-yellow-400 dark:ring-yellow-500/20':
                                        item.category === 'happy',
                                    'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20':
                                        item.category === 'sad',
                                    'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20':
                                        item.category === 'angry',
                                    'bg-purple-50 text-purple-700 ring-purple-600/20 dark:bg-purple-500/10 dark:text-purple-400 dark:ring-purple-500/20':
                                        item.category === 'fear',
                                    'bg-pink-50 text-pink-700 ring-pink-600/20 dark:bg-pink-500/10 dark:text-pink-400 dark:ring-pink-500/20':
                                        item.category === 'surprised',
                                    'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20':
                                        item.category === 'love',
                                }"
                            >
                                {{ item.category }}
                            </span>
                        </div>

                        <!-- Description - larger text -->
                        <p class="mb-4 min-h-[4rem] text-base leading-relaxed text-muted-foreground">
                            {{ item.description }}
                        </p>

                        <!-- Intensity bar -->
                        <div class="mt-auto">
                            <div class="mb-1 flex justify-between text-xs text-muted-foreground">
                                <span>Intensity</span>
                                <span class="font-semibold text-card-foreground">{{ item.intensity }}/10</span>
                            </div>
                            <div class="h-3 w-full overflow-hidden rounded-full bg-accent">
                                <div
                                    class="h-3 rounded-full transition-all"
                                    :class="{
                                        'bg-emerald-500 dark:bg-emerald-400': item.intensity <= 3,
                                        'bg-amber-500 dark:bg-amber-400': item.intensity > 3 && item.intensity <= 7,
                                        'bg-red-500 dark:bg-red-400': item.intensity > 7,
                                    }"
                                    :style="`width: ${item.intensity * 10}%`"
                                ></div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Grid layout for movies -->
            <div v-else-if="currentType === 'andrus'" class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                <Card v-for="(movie, index) in data" :key="index" class="overflow-hidden transition-shadow hover:shadow-lg">
                    <!-- Movie poster -->
                    <div class="aspect-[2/3] bg-accent">
                        <img
                            v-if="movie.image || movie.poster_url"
                            :src="movie.image || movie.poster_url"
                            :alt="movie.title"
                            class="h-full w-full object-cover"
                            @error="handleImageError($event, movie.title)"
                        />
                        <div v-else class="flex h-full w-full items-center justify-center text-muted-foreground">
                            <svg class="h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M7 4v16M17 4v16M3 8h4m10 0h4M3 16h4m10 0h4M3 12h18"
                                />
                            </svg>
                        </div>
                    </div>

                    <!-- Movie info -->
                    <CardContent class="p-4">
                        <h3 class="mb-1 line-clamp-2 text-sm font-semibold text-card-foreground">
                            {{ movie.title || movie.name }}
                        </h3>
                        <p class="mb-1 text-xs text-muted-foreground">
                            {{ movie.director }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            {{ movie.release_year || movie.year }}
                        </p>
                        <div v-if="movie.rating" class="mt-2 flex items-center gap-1">
                            <span class="text-yellow-500">★</span>
                            <span class="text-sm text-card-foreground">{{ movie.rating }}/10</span>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Fallback table view for other types -->
            <div v-else class="rounded-md border shadow-sm">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead v-for="field in customFields" :key="field">
                                {{ formatFieldName(field) }}
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="(item, index) in data" :key="index">
                            <TableCell v-for="field in customFields" :key="field">
                                {{ getFieldValue(item, field) }}
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Data count -->
            <div class="mt-8 text-center text-sm text-muted-foreground">
                Showing {{ data.length }} {{ currentType === 'emotions' ? 'emotions' : 'items' }}
            </div>
        </div>
    </AppLayout>
</template>
