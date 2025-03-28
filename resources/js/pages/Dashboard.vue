<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from "@/components/ui/card";

const props = defineProps({
    weatherData: Object,
    error: String
});

const form = useForm({
    city: ''
});

const submit = () => {
    form.get(route('dashboard'));
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
</script>

<template>

    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <Card class="overflow-hidden">
                    <CardHeader class="weather-header">
                        <CardTitle>Weather</CardTitle>
                        <CardDescription class="text-primary-foreground/90">Current conditions</CardDescription>
                        <div v-if="weatherData" class="absolute top-2 right-4">
                            <img :src="`https://openweathermap.org/img/wn/${weatherData.weather[0].icon}@2x.png`"
                                :alt="weatherData.weather[0].description" class="w-12 h-12" />
                        </div>
                    </CardHeader>
                    <CardContent class="p-4">
                        <form @submit.prevent="submit">
                            <div class="flex items-center gap-2">
                                <input type="text" v-model="form.city"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    placeholder="Enter city name" />
                                <button type="submit"
                                    class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-3 py-1"
                                    :disabled="form.processing">
                                    Search
                                </button>
                            </div>
                        </form>

                        <div v-if="error" class="weather-error">
                            {{ error }}
                        </div>

                        <div v-if="weatherData" class="mt-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold">{{ weatherData.name }}, {{ weatherData.sys.country
                                        }}</h3>
                                    <p class="text-xs text-muted-foreground">{{ new Date().toLocaleString() }}</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold">{{ Math.round(weatherData.main.temp) }}°C</div>
                                    <p class="text-xs text-muted-foreground">Feels like: {{
                                        Math.round(weatherData.main.feels_like) }}°C</p>
                                </div>
                            </div>

                            <div class="my-3 border-t"></div>

                            <p class="font-medium">{{ weatherData.weather[0].description.charAt(0).toUpperCase() +
                                weatherData.weather[0].description.slice(1) }}</p>

                            <div class="flex flex-wrap mt-2">
                                <div class="weather-metric">
                                    <span class="weather-metric-label">Humidity:</span>
                                    <span class="weather-metric-value">{{ weatherData.main.humidity }}%</span>
                                </div>
                                <div class="weather-metric">
                                    <span class="weather-metric-label">Wind:</span>
                                    <span class="weather-metric-value">{{ weatherData.wind.speed }} m/s</span>
                                </div>
                                <div class="weather-metric">
                                    <span class="weather-metric-label">Pressure:</span>
                                    <span class="weather-metric-value">{{ weatherData.main.pressure }} hPa</span>
                                </div>
                                <div class="weather-metric">
                                    <span class="weather-metric-label">Visibility:</span>
                                    <span class="weather-metric-value">{{ Math.round(weatherData.visibility / 1000) }}
                                        km</span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter class="bg-muted/40 py-1.5 px-4">
                        <p class="text-xs text-muted-foreground">Data from OpenWeatherMap</p>
                    </CardFooter>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Card Title</CardTitle>
                        <CardDescription>Card Description</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p>Card Content</p>
                    </CardContent>
                    <CardFooter>
                        <p>Card Footer</p>
                    </CardFooter>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Card Title</CardTitle>
                        <CardDescription>Card Description</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p>Card Content</p>
                    </CardContent>
                    <CardFooter>
                        <p>Card Footer</p>
                    </CardFooter>
                </Card>
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>