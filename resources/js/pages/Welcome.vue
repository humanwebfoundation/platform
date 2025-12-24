<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import { Button } from '@/components/ui/button';
import { dashboard, login, register } from '@/routes';
import '@fontsource-variable/dm-sans';
import '@fontsource-variable/merriweather';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col items-center">
        <header class="w-full max-w-5xl p-4 not-has-[nav]:hidden lg:max-w-4xl">
            <nav class="flex w-full items-center gap-4">
                <h1 class="font-serif text-xl font-semibold">human web</h1>
                <div class="ml-auto flex items-center gap-2">
                    <Button v-if="isAuthenticated" as-child>
                        <Link :href="dashboard()"> Dashboard </Link>
                    </Button>
                    <template v-else>
                        <Button as-child>
                            <Link :href="login()"
                                ><Icon name="LogIn" /> Sign in
                            </Link>
                        </Button>
                        <Button v-if="canRegister" as-child variant="outline">
                            <Link :href="register()"> Sign up </Link>
                        </Button>
                    </template>
                </div>
            </nav>
        </header>
    </div>
</template>
