<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import type { LngLatBounds } from 'maplibre-gl';
import Radar from 'radar-sdk-js';
import 'radar-sdk-js/dist/radar.css';
import { computed, onMounted, onUnmounted, PropType, ref, watch } from 'vue';

// Make TypeScript window extension for popup functions
declare global {
    interface Window {
        editMarkerFromPopup: (id: number) => void;
        deleteMarkerFromPopup: (id: number) => void;
        closeCurrentPopup: () => void;
        closeCurrentPopupRef: any; // Added direct popup reference
    }
}

// Define marker interface
interface MarkerType {
    id: number;
    name: string;
    latitude: number;
    longitude: number;
    description: string;
    added?: string;
    edited?: string;
}

// Props, form and basic setup
const props = defineProps({
    weatherData: Object,
    error: String,
    markers: Array as PropType<MarkerType[]>,
});

const form = useForm({ city: '' });
const submit = () => form.get(route('dashboard'));
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/dashboard' }];

// Map and markers
const mapInstance = ref<any>(null);
const markerRefs = ref<Record<number, any>>({});
const allMarkers = ref<MarkerType[]>([]);
const visibleMarkers = ref<MarkerType[]>([]);
const currentPopup = ref<any>(null);

// Theme detection
const isDarkMode = (): boolean => {
    return document.documentElement.classList.contains('dark');
};

// Reactive map style based on theme
const mapStyle = computed((): string => {
    return isDarkMode() ? 'radar-dark-v1' : 'radar-default-v1';
});

const createEnhancedPopupHTML = (marker: MarkerType): string => {
    const dark = isDarkMode();

    const bgColor = dark ? 'hsl(var(--background))' : 'hsl(var(--background))';
    const titleColor = dark ? 'hsl(var(--foreground))' : 'hsl(var(--foreground))';
    const textColor = dark ? 'hsl(var(--foreground))' : 'hsl(var(--foreground))';
    const mutedColor = dark ? 'hsl(var(--muted-foreground))' : 'hsl(var(--muted-foreground))';
    const borderColor = dark ? 'hsl(var(--border))' : 'hsl(var(--border))';
    const buttonBgPrimary = dark ? 'hsl(var(--secondary))' : 'hsl(var(--secondary))';
    const buttonBgDanger = dark ? 'hsl(var(--destructive)/15)' : 'hsl(var(--destructive)/15)';
    const buttonTextPrimary = dark ? 'hsl(var(--secondary-foreground))' : 'hsl(var(--secondary-foreground))';
    const buttonTextDanger = dark ? 'hsl(var(--destructive-foreground))' : 'hsl(var(--destructive))';
    const scrollbarTrackColor = dark ? 'hsl(var(--muted))' : 'hsl(var(--muted))';
    const scrollbarThumbColor = dark ? 'hsl(var(--border))' : 'hsl(var(--border))';

    return `
    <div style="margin: -10px; width: calc(100% + 20px); background-color: ${bgColor};">
      <div style="padding: 16px;">
        <h3 style="font-weight: 600; font-size: 18px; margin: 0 0 12px 0; color: ${titleColor};">${marker.name}</h3>
        
        ${
            marker.description
                ? `
          <div style="max-height: 150px; overflow-y: auto; margin: 8px 0 16px; padding-right: 8px; scrollbar-width: thin; scrollbar-color: ${scrollbarThumbColor} ${scrollbarTrackColor};">
            <p style="color: ${textColor}; font-size: 14px; line-height: 1.5; margin: 0;">${marker.description}</p>
          </div>`
                : '<div style="height: 8px;"></div>'
        }
        
        <div style="font-size: 12px; color: ${mutedColor}; margin: 12px 0; border-top: 1px solid ${borderColor}; padding-top: 12px;">
          Coordinates: ${marker.latitude.toFixed(5)}, ${marker.longitude.toFixed(5)}
        </div>
        
        <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
          <button 
            onclick="window.closeCurrentPopup()"
            style="cursor: pointer; padding: 6px 12px; background-color: ${buttonBgPrimary}; color: ${buttonTextPrimary}; border: none; border-radius: 4px; font-size: 13px; font-weight: 500;"
          >
            Close
          </button>
          <button 
            onclick="window.editMarkerFromPopup(${marker.id})"
            style="cursor: pointer; padding: 6px 12px; background-color: ${buttonBgPrimary}; color: ${buttonTextPrimary}; border: none; border-radius: 4px; font-size: 13px; font-weight: 500;"
          >
            Edit
          </button>
          <button 
            onclick="window.deleteMarkerFromPopup(${marker.id})"
            style="cursor: pointer; padding: 6px 12px; background-color: ${buttonBgDanger}; color: ${buttonTextDanger}; border: none; border-radius: 4px; font-size: 13px; font-weight: 500;"
          >
            Delete
          </button>
        </div>
      </div>
    </div>
  `;
};

// Form state for adding/editing markers
const markerForm = ref<{
    name: string;
    latitude: number | null;
    longitude: number | null;
    description: string;
}>({
    name: '',
    latitude: null,
    longitude: null,
    description: '',
});

const markerDialogOpen = ref<boolean>(false);
const editingMarker = ref<MarkerType | null>(null);
const editDialogOpen = ref<boolean>(false);

// CRUD operations for markers
const submitMarker = (): void => {
    router.post(route('markers.store'), markerForm.value, {
        onSuccess: () => {
            markerForm.value = { name: '', latitude: null, longitude: null, description: '' };
            markerDialogOpen.value = false;
        },
    });
};

const editMarker = (marker: MarkerType): void => {
    editingMarker.value = { ...marker };
    editDialogOpen.value = true;
};

const submitEditMarker = (): void => {
    if (!editingMarker.value) return;
    router.put(route('markers.update', editingMarker.value.id), editingMarker.value, {
        onSuccess: () => (editDialogOpen.value = false),
    });
};

const deleteMarker = (marker: MarkerType): void => {
    if (confirm(`Are you sure you want to delete "${marker.name}"?`)) {
        router.delete(route('markers.destroy', marker.id));
    }
};

// Map functions
const showMarkerOnMap = (marker: MarkerType): void => {
    if (!mapInstance.value) return;

    if (currentPopup.value) {
        currentPopup.value.remove();
        currentPopup.value = null;
        window.closeCurrentPopupRef = null;
    }

    mapInstance.value.flyTo({
        center: [marker.longitude, marker.latitude],
        zoom: 15,
        duration: 800,
    });

    currentPopup.value = Radar.ui
        .popup({
            closeButton: false,
            closeOnClick: false,
            html: createEnhancedPopupHTML(marker),
        })
        .setLngLat([marker.longitude, marker.latitude])
        .addTo(mapInstance.value);

    window.closeCurrentPopupRef = currentPopup.value;
};

const updateVisibleMarkers = (): void => {
    if (!mapInstance.value || !allMarkers.value.length) return;

    try {
        const bounds = mapInstance.value.getBounds() as LngLatBounds;

        visibleMarkers.value = allMarkers.value.filter((marker) => {
            return bounds.contains([marker.longitude, marker.latitude]);
        });
    } catch (error) {
        console.error('Error updating visible markers:', error);
    }
};

const addMarkersToMap = (): void => {
    if (!mapInstance.value || !allMarkers.value.length) return;

    try {
        mapInstance.value.clearMarkers();
        markerRefs.value = {};

        allMarkers.value.forEach((marker) => {
            const radarMarker = Radar.ui
                .marker({
                    color: '#e11d48',
                    width: 20,
                    height: 20,
                    popup: {
                        closeButton: false,
                        closeOnClick: false,
                        html: createEnhancedPopupHTML(marker),
                    },
                })
                .setLngLat([marker.longitude, marker.latitude])
                .addTo(mapInstance.value);

            markerRefs.value[marker.id] = radarMarker;

            radarMarker.getElement().addEventListener('click', () => {
                if (radarMarker.getPopup()) {
                    window.closeCurrentPopupRef = radarMarker.getPopup();
                    currentPopup.value = radarMarker.getPopup();
                }
            });
        });

        updateVisibleMarkers();
    } catch (error) {
        console.error('Error adding markers to map:', error);
    }
};

onMounted(() => {
    try {
        window.closeCurrentPopupRef = null;

        window.editMarkerFromPopup = (markerId: number) => {
            const marker = allMarkers.value.find((m) => m.id === markerId);
            if (marker) {
                editMarker(marker);
                // Close the popup after clicking edit
                if (window.closeCurrentPopupRef) {
                    window.closeCurrentPopupRef.remove();
                    window.closeCurrentPopupRef = null;
                    currentPopup.value = null;
                }
            }
        };

        window.deleteMarkerFromPopup = (markerId: number) => {
            const marker = allMarkers.value.find((m) => m.id === markerId);
            if (marker) {
                deleteMarker(marker);
                // Popup will be removed automatically when the marker is deleted
            }
        };

        window.closeCurrentPopup = () => {
            if (window.closeCurrentPopupRef) {
                window.closeCurrentPopupRef.remove();
                window.closeCurrentPopupRef = null;
                currentPopup.value = null;
            }
        };

        Radar.initialize('prj_test_pk_524409d19baf858e22cfe22be9e1125b68f37b48');

        allMarkers.value = props.markers || [];

        mapInstance.value = Radar.ui.map({
            container: 'map',
            style: mapStyle.value,
            center: [22.495, 58.2475],
            zoom: 13,
        });

        mapInstance.value.on('click', (e: any) => {
            markerForm.value.latitude = e.lngLat.lat;
            markerForm.value.longitude = e.lngLat.lng;
            markerDialogOpen.value = true;
        });

        mapInstance.value.on('moveend', updateVisibleMarkers);
        mapInstance.value.on('zoomend', updateVisibleMarkers);

        mapInstance.value.on('load', addMarkersToMap);

        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class' && mutation.target === document.documentElement) {
                    if (mapInstance.value && mapInstance.value.loaded) {
                        mapInstance.value.setStyle(mapStyle.value);

                        // Re-create markers with updated popups after the style change completes
                        mapInstance.value.once('style.load', addMarkersToMap);
                    }
                }
            });
        });

        // Start observing the HTML element for class changes
        observer.observe(document.documentElement, { attributes: true });

        // Clean up observer on unmount
        onUnmounted(() => {
            observer.disconnect();
        });
    } catch (error) {
        console.error('Error initializing map:', error);
    }
});

// Watch for changes in props.markers to update allMarkers
watch(
    () => props.markers,
    (newMarkers) => {
        allMarkers.value = newMarkers || [];

        if (mapInstance.value?.loaded) {
            addMarkersToMap();
        }
    },
    { deep: true },
);
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Grid of cards -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <!-- Weather Card -->
                <Card class="overflow-hidden">
                    <CardHeader class="weather-header relative">
                        <CardTitle>Weather</CardTitle>
                        <CardDescription class="text-primary-foreground/90">Current conditions</CardDescription>
                        <div v-if="weatherData" class="absolute right-4 top-2">
                            <img
                                :src="`https://openweathermap.org/img/wn/${weatherData.weather[0].icon}@2x.png`"
                                :alt="weatherData.weather[0].description"
                                class="h-12 w-12"
                            />
                        </div>
                    </CardHeader>
                    <CardContent class="p-4">
                        <form @submit.prevent="submit">
                            <div class="flex items-center gap-2">
                                <input
                                    type="text"
                                    v-model="form.city"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    placeholder="Enter city name"
                                />
                                <button
                                    type="submit"
                                    class="inline-flex h-9 items-center justify-center whitespace-nowrap rounded-md bg-primary px-3 py-1 text-sm font-medium text-primary-foreground ring-offset-background transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
                                    :disabled="form.processing"
                                >
                                    Search
                                </button>
                            </div>
                        </form>

                        <div v-if="error" class="weather-error">{{ error }}</div>

                        <div v-if="weatherData" class="mt-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold">{{ weatherData.name }}, {{ weatherData.sys.country }}</h3>
                                    <p class="text-xs text-muted-foreground">{{ new Date().toLocaleString() }}</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold">{{ Math.round(weatherData.main.temp) }}°C</div>
                                    <p class="text-xs text-muted-foreground">Feels like: {{ Math.round(weatherData.main.feels_like) }}°C</p>
                                </div>
                            </div>

                            <div class="my-3 border-t"></div>

                            <p class="font-medium">
                                {{ weatherData.weather[0].description.charAt(0).toUpperCase() + weatherData.weather[0].description.slice(1) }}
                            </p>

                            <div class="mt-2 flex flex-wrap gap-4">
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
                                    <span class="weather-metric-value">{{ Math.round(weatherData.visibility / 1000) }} km</span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter class="bg-muted/40 px-4 py-1.5">
                        <p class="text-xs text-muted-foreground">Data from OpenWeatherMap</p>
                    </CardFooter>
                </Card>

                <!-- Visible Markers List Card -->
                <Card class="overflow-hidden">
                    <CardHeader>
                        <CardTitle>Visible Markers</CardTitle>
                        <CardDescription>Markers in current map view</CardDescription>
                    </CardHeader>
                    <CardContent class="overflow-y-auto" style="height: calc(100% - 85px); max-height: 280px">
                        <div v-if="!visibleMarkers.length" class="py-4 text-center text-muted-foreground">
                            No markers visible in current view.
                            <div class="mt-2 text-sm">Try zooming out or panning the map.</div>
                        </div>
                        <ul v-else class="divide-y">
                            <li v-for="marker in visibleMarkers" :key="marker.id" class="py-3">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="cursor-pointer font-medium hover:text-blue-500" @click="showMarkerOnMap(marker)">
                                            {{ marker.name }}
                                        </h4>
                                        <p class="text-sm text-muted-foreground">{{ marker.description }}</p>
                                        <div class="mt-1 text-xs text-muted-foreground">
                                            {{ marker.latitude.toFixed(5) }}, {{ marker.longitude.toFixed(5) }}
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <Button variant="outline" size="sm" @click="editMarker(marker)">Edit</Button>
                                        <Button variant="destructive" size="sm" @click="deleteMarker(marker)">Delete</Button>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </CardContent>
                </Card>

                <!-- Stats Card -->
                <Card>
                    <CardHeader>
                        <CardTitle>Map Stats</CardTitle>
                        <CardDescription>Location information</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p class="mb-2">Visible markers: {{ visibleMarkers.length }} of {{ allMarkers.length }}</p>
                        <p>Pan and zoom the map to filter visible markers, or click on the map to add new ones.</p>
                    </CardContent>
                    <CardFooter>
                        <p class="text-xs text-muted-foreground">Using Radar Maps API</p>
                    </CardFooter>
                </Card>
            </div>

            <!-- Map Container -->
            <div class="relative flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <div id="map" style="width: 100%; height: 500px" />
            </div>
        </div>

        <!-- Add Marker Dialog -->
        <Dialog :open="markerDialogOpen" @update:open="markerDialogOpen = $event">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Add New Marker</DialogTitle>
                </DialogHeader>
                <form @submit.prevent="submitMarker">
                    <div class="grid gap-4 py-4">
                        <div class="grid gap-2">
                            <Label for="name">Name</Label>
                            <Input id="name" v-model="markerForm.name" required />
                        </div>
                        <div class="grid gap-2">
                            <Label for="description">Description</Label>
                            <Textarea id="description" v-model="markerForm.description" />
                        </div>
                        <div class="text-sm text-muted-foreground">
                            Location: {{ markerForm.latitude?.toFixed(5) }}, {{ markerForm.longitude?.toFixed(5) }}
                        </div>
                    </div>
                    <DialogFooter>
                        <Button type="button" variant="outline" @click="markerDialogOpen = false">Cancel</Button>
                        <Button type="submit">Save</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Edit Marker Dialog -->
        <Dialog :open="editDialogOpen" @update:open="editDialogOpen = $event">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Edit Marker</DialogTitle>
                </DialogHeader>
                <form @submit.prevent="submitEditMarker" v-if="editingMarker">
                    <div class="grid gap-4 py-4">
                        <div class="grid gap-2">
                            <Label for="edit-name">Name</Label>
                            <Input id="edit-name" v-model="editingMarker.name" required />
                        </div>
                        <div class="grid gap-2">
                            <Label for="edit-description">Description</Label>
                            <Textarea id="edit-description" v-model="editingMarker.description" />
                        </div>
                        <div class="text-sm text-muted-foreground">
                            Location: {{ editingMarker.latitude?.toFixed(5) }}, {{ editingMarker.longitude?.toFixed(5) }}
                        </div>
                    </div>
                    <DialogFooter>
                        <Button type="button" variant="outline" @click="editDialogOpen = false">Cancel</Button>
                        <Button type="submit">Save Changes</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
