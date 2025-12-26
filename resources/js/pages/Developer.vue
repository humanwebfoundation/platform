<script setup lang="ts">
import CreateClientDialog from '@/components/developer/CreateClientDialog.vue';
import OAuthClientCard from '@/components/developer/OAuthClientCard.vue';
import ClientSecretReveal from '@/components/developer/ClientSecretReveal.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as developerIndex } from '@/routes/developer';
import { type BreadcrumbItem, type OAuthClient, type OAuthClientWithSecret } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { Code2, Plus } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Props {
    clients: OAuthClient[];
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Developer',
        href: developerIndex.url(),
    },
];

const page = usePage();
const isCreateDialogOpen = ref(false);
const isSecretRevealOpen = ref(false);

const flashedClient = computed(() => {
    const client = page.props.flash?.client as OAuthClientWithSecret | undefined;
    return client;
});

const handleClientCreated = () => {
    isCreateDialogOpen.value = false;
    if (flashedClient.value) {
        isSecretRevealOpen.value = true;
    }
};

const handleOpenCreateDialog = () => {
    isCreateDialogOpen.value = true;
};
</script>

<template>
    <Head title="Developer" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6">
            <div class="flex items-start justify-between">
                <Heading
                    title="OAuth Applications"
                    description="Manage OAuth applications that can access your platform"
                />

                <Button @click="handleOpenCreateDialog" class="shrink-0">
                    <Plus class="mr-2 h-4 w-4" />
                    Create Application
                </Button>
            </div>

            <div v-if="clients.length === 0" class="mt-12 flex flex-col items-center justify-center text-center">
                <div class="rounded-full bg-muted p-4">
                    <Code2 class="h-8 w-8 text-muted-foreground" />
                </div>
                <h3 class="mt-4 text-lg font-semibold">No OAuth applications yet</h3>
                <p class="mt-2 max-w-sm text-sm text-muted-foreground">
                    Create your first OAuth application to allow third-party developers to authenticate users through your platform.
                </p>
                <Button @click="handleOpenCreateDialog" class="mt-6">
                    <Plus class="mr-2 h-4 w-4" />
                    Create Your First Application
                </Button>
            </div>

            <div v-else class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <OAuthClientCard
                    v-for="client in clients"
                    :key="client.id"
                    :client="client"
                />
            </div>
        </div>

        <CreateClientDialog
            v-model:open="isCreateDialogOpen"
            @success="handleClientCreated"
        />

        <ClientSecretReveal
            v-if="flashedClient"
            v-model:open="isSecretRevealOpen"
            :client="flashedClient"
        />
    </AppLayout>
</template>

