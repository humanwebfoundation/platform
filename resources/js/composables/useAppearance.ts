import { onMounted, ref } from 'vue';

type Appearance = 'light' | 'dark' | 'system';

export function updateTheme() {
    if (typeof window === 'undefined') {
        return;
    }

    // Always force light theme
    document.documentElement.classList.remove('dark');
}

export function initializeTheme() {
    if (typeof window === 'undefined') {
        return;
    }

    // Always initialize to light theme
    updateTheme();
}

const appearance = ref<Appearance>('light');

export function useAppearance() {
    onMounted(() => {
        // Always set to light theme
        appearance.value = 'light';
        updateTheme();
    });

    function updateAppearance() {
        // Theme switching is disabled - do nothing
        // Always keep light theme
        appearance.value = 'light';
        updateTheme();
    }

    return {
        appearance,
        updateAppearance,
    };
}
