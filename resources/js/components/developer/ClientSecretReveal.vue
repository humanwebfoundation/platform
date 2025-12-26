<script setup lang="ts">
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { type OAuthClientWithSecret } from '@/types';
import { AlertTriangle, Check, Copy } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    client: OAuthClientWithSecret;
}

defineProps<Props>();

const open = defineModel<boolean>('open', { required: true });

const isClientIdCopied = ref(false);
const isSecretCopied = ref(false);

const handleCopyClientId = async (clientId: string) => {
    await navigator.clipboard.writeText(clientId);
    isClientIdCopied.value = true;
    setTimeout(() => {
        isClientIdCopied.value = false;
    }, 2000);
};

const handleCopySecret = async (secret: string) => {
    await navigator.clipboard.writeText(secret);
    isSecretCopied.value = true;
    setTimeout(() => {
        isSecretCopied.value = false;
    }, 2000);
};

const handleClose = () => {
    open.value = false;
};
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="sm:max-w-lg">
            <DialogHeader>
                <DialogTitle class="flex items-center gap-2">
                    <Check class="h-5 w-5 text-green-600" />
                    Application Created Successfully
                </DialogTitle>
                <DialogDescription>
                    Your OAuth application <strong>{{ client.name }}</strong> has been created.
                </DialogDescription>
            </DialogHeader>

            <Alert variant="destructive" class="mt-4">
                <AlertTriangle class="h-4 w-4" />
                <AlertTitle>Important</AlertTitle>
                <AlertDescription>
                    Make sure to copy your client secret now. You will not be able to see it again.
                </AlertDescription>
            </Alert>

            <div class="mt-4 space-y-4">
                <div class="space-y-2">
                    <label class="text-sm font-medium">Client ID</label>
                    <div class="flex items-center gap-2">
                        <code class="flex-1 overflow-x-auto rounded-md bg-muted px-3 py-2 font-mono text-sm">
                            {{ client.id }}
                        </code>
                        <Button
                            variant="outline"
                            size="icon"
                            @click="handleCopyClientId(client.id)"
                            :aria-label="isClientIdCopied ? 'Copied' : 'Copy client ID'"
                        >
                            <Check v-if="isClientIdCopied" class="h-4 w-4 text-green-600" />
                            <Copy v-else class="h-4 w-4" />
                        </Button>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium">Client Secret</label>
                    <div class="flex items-center gap-2">
                        <code class="flex-1 overflow-x-auto rounded-md bg-muted px-3 py-2 font-mono text-sm break-all">
                            {{ client.secret }}
                        </code>
                        <Button
                            variant="outline"
                            size="icon"
                            @click="handleCopySecret(client.secret)"
                            :aria-label="isSecretCopied ? 'Copied' : 'Copy client secret'"
                        >
                            <Check v-if="isSecretCopied" class="h-4 w-4 text-green-600" />
                            <Copy v-else class="h-4 w-4" />
                        </Button>
                    </div>
                </div>
            </div>

            <DialogFooter class="mt-6">
                <Button @click="handleClose">
                    I've Saved My Credentials
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

