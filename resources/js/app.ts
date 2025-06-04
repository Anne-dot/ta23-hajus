import '../css/app.css';

import { useToast } from '@/components/ui/toast/use-toast';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';

// Extend ImportMeta interface for Vite...
declare module 'vite/client' {
    interface ImportMetaEnv {
        readonly VITE_APP_NAME: string;
        [key: string]: string | boolean | undefined;
    }

    interface ImportMeta {
        readonly env: ImportMetaEnv;
        readonly glob: <T>(pattern: string) => Record<string, () => Promise<T>>;
    }
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

initializeTheme();

document.addEventListener('DOMContentLoaded', () => {
    const { toast } = useToast();

    document.addEventListener('inertia:exception', (event: any) => {
        if (event.detail.response?.status === 419) {
            event.preventDefault();

            toast({
                title: 'Session Expired',
                description: 'Your session has expired. Please refresh the page to continue.',
                variant: 'destructive',
                action: {
                    label: 'Refresh',
                    onClick: () => window.location.reload(),
                },
            });
        }
    });
});
