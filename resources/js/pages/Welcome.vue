<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import { Badge } from '@/components/ui/badge';
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
        <header class="w-full border-b p-4 not-has-[nav]:hidden">
            <nav class="m-auto flex w-full max-w-5xl items-center gap-4">
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
        <div class="flex flex-col items-center gap-4 py-24 text-center">
            <Badge variant="outline" class="text-sm"
                ><Icon name="CheckCircle" color="green" /> 100% Human
                certified</Badge
            >
            <h1 class="font-serif text-6xl font-black">
                Our Web, <span class="text-primary">Reclaimed.</span>
            </h1>

            <p class="my-4">
                A space for authentic human connection. <br />Zero AI. Zero
                Algorithms. Zero Hate.
            </p>

            <div class="flex items-center gap-2">
                <Button size="lg" as-child>
                    <Link :href="dashboard()"> Enter human space </Link>
                </Button>
                <Button variant="outline" size="lg"
                    ><Icon name="ExternalLink" /> Read manifesto</Button
                >
            </div>
        </div>
    </div>
</template>
