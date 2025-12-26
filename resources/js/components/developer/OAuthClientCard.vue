<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { destroy as destroyClient } from '@/routes/developer/clients';
import { type OAuthClient } from '@/types';
import { router } from '@inertiajs/vue3';
import { Check, Copy, ExternalLink, Loader2, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    client: OAuthClient;
}

const props = defineProps<Props>();

const isDeleting = ref(false);
const isCopied = ref(false);

const handleCopyClientId = async () => {
    await navigator.clipboard.writeText(props.client.id);
    isCopied.value = true;
    setTimeout(() => {
        isCopied.value = false;
    }, 2000);
};

const handleDelete = () => {
    isDeleting.value = true;
    router.delete(destroyClient.url(props.client.id), {
        preserveScroll: true,
        onFinish: () => {
            isDeleting.value = false;
        },
    });
};

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};
</script>

<template>
    <Card class="flex flex-col">
        <CardHeader class="pb-3">
            <CardTitle class="text-lg">{{ client.name }}</CardTitle>
            <CardDescription>
                Created {{ formatDate(client.created_at) }}
            </CardDescription>
        </CardHeader>

        <CardContent class="flex-1 space-y-3">
            <div class="space-y-1">
                <p class="text-xs font-medium text-muted-foreground uppercase tracking-wide">
                    Client ID
                </p>
                <div class="flex items-center gap-2">
                    <code class="flex-1 truncate rounded bg-muted px-2 py-1 font-mono text-xs">
                        {{ client.id }}
                    </code>
                    <Button
                        variant="ghost"
                        size="icon"
                        class="h-7 w-7 shrink-0"
                        @click="handleCopyClientId"
                        :aria-label="isCopied ? 'Copied' : 'Copy client ID'"
                    >
                        <Check v-if="isCopied" class="h-3.5 w-3.5 text-green-600" />
                        <Copy v-else class="h-3.5 w-3.5" />
                    </Button>
                </div>
            </div>

            <div class="space-y-1">
                <p class="text-xs font-medium text-muted-foreground uppercase tracking-wide">
                    Redirect URL
                </p>
                <div class="flex items-center gap-2">
                    <code class="flex-1 truncate rounded bg-muted px-2 py-1 font-mono text-xs">
                        {{ client.redirect_uris[0] }}
                    </code>
                    <a
                        :href="client.redirect_uris[0]"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="shrink-0"
                        :aria-label="`Open ${client.redirect_uris[0]} in new tab`"
                    >
                        <Button variant="ghost" size="icon" class="h-7 w-7">
                            <ExternalLink class="h-3.5 w-3.5" />
                        </Button>
                    </a>
                </div>
            </div>
        </CardContent>

        <CardFooter class="border-t pt-4">
            <Dialog>
                <DialogTrigger as-child>
                    <Button variant="destructive" size="sm" class="w-full">
                        <Trash2 class="mr-2 h-4 w-4" />
                        Revoke Application
                    </Button>
                </DialogTrigger>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Revoke OAuth Application</DialogTitle>
                        <DialogDescription>
                            Are you sure you want to revoke
                            <strong>{{ client.name }}</strong>? This will invalidate all
                            existing access tokens and users will need to re-authorize.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter class="gap-2">
                        <DialogClose as-child>
                            <Button variant="outline">Cancel</Button>
                        </DialogClose>
                        <Button
                            variant="destructive"
                            :disabled="isDeleting"
                            @click="handleDelete"
                        >
                            <Loader2
                                v-if="isDeleting"
                                class="mr-2 h-4 w-4 animate-spin"
                            />
                            Revoke Application
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </CardFooter>
    </Card>
</template>

