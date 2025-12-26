<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { CheckCircle2, Shield, XCircle } from 'lucide-vue-next';
import { ref } from 'vue';

interface Client {
    id: string;
    name: string;
}

interface Scope {
    id: string;
    description: string;
}

const props = defineProps<{
    authToken: string;
    client: Client;
    user: { name: string; email: string };
    scopes: Scope[];
    request: {
        state: string | null;
    };
}>();

const page = usePage();
const csrfToken = (page.props as { csrf_token?: string }).csrf_token ?? '';

const approveFormRef = ref<HTMLFormElement | null>(null);
const denyFormRef = ref<HTMLFormElement | null>(null);
const isProcessing = ref(false);

const handleApprove = (): void => {
    isProcessing.value = true;
    approveFormRef.value?.submit();
};

const handleDeny = (): void => {
    isProcessing.value = true;
    denyFormRef.value?.submit();
};
</script>

<template>
    <AuthBase
        title="Authorization Request"
        :description="`${client.name} is requesting access to your account`"
    >
        <Head title="Authorize Application" />

        <Card class="border-0 shadow-none">
            <CardHeader class="px-0 pt-0">
                <CardTitle class="text-lg">
                    <span class="font-semibold">{{ client.name }}</span>
                </CardTitle>
                <CardDescription>
                    This application would like permission to access your account.
                </CardDescription>
            </CardHeader>

            <CardContent class="px-0">
                <div class="space-y-4">
                    <div class="rounded-lg border bg-muted/50 p-4">
                        <div class="flex items-center gap-3">
                            <Shield class="h-5 w-5 text-muted-foreground" />
                            <div>
                                <p class="text-sm font-medium">Signed in as</p>
                                <p class="text-sm text-muted-foreground">{{ user.email }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="scopes.length > 0" class="space-y-2">
                        <p class="text-sm font-medium">This application will be able to:</p>
                        <ul class="space-y-2">
                            <li
                                v-for="scope in scopes"
                                :key="scope.id"
                                class="flex items-center gap-2 text-sm text-muted-foreground"
                            >
                                <CheckCircle2 class="h-4 w-4 text-green-500" />
                                {{ scope.description }}
                            </li>
                        </ul>
                    </div>

                    <div v-else class="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-950">
                        <div class="flex items-center gap-2">
                            <CheckCircle2 class="h-5 w-5 text-green-600 dark:text-green-400" />
                            <p class="text-sm text-green-700 dark:text-green-300">
                                This application is requesting basic access to your account.
                            </p>
                        </div>
                    </div>
                </div>
            </CardContent>

            <CardFooter class="flex gap-3 px-0 pb-0">
                <!-- Deny Form - Regular HTML form for cross-origin redirect -->
                <form
                    ref="denyFormRef"
                    method="POST"
                    action="/oauth/authorize"
                    class="flex-1"
                >
                    <input type="hidden" name="_token" :value="csrfToken" />
                    <input type="hidden" name="_method" value="DELETE" />
                    <input type="hidden" name="state" :value="request.state" />
                    <input type="hidden" name="client_id" :value="client.id" />
                    <input type="hidden" name="auth_token" :value="authToken" />
                    <Button
                        type="submit"
                        variant="outline"
                        class="w-full"
                        :disabled="isProcessing"
                        @click.prevent="handleDeny"
                    >
                        <XCircle class="mr-2 h-4 w-4" />
                        Deny
                    </Button>
                </form>

                <!-- Approve Form - Regular HTML form for cross-origin redirect -->
                <form
                    ref="approveFormRef"
                    method="POST"
                    action="/oauth/authorize"
                    class="flex-1"
                >
                    <input type="hidden" name="_token" :value="csrfToken" />
                    <input type="hidden" name="state" :value="request.state" />
                    <input type="hidden" name="client_id" :value="client.id" />
                    <input type="hidden" name="auth_token" :value="authToken" />
                    <Button
                        type="submit"
                        class="w-full"
                        :disabled="isProcessing"
                        @click.prevent="handleApprove"
                    >
                        <CheckCircle2 class="mr-2 h-4 w-4" />
                        Authorize
                    </Button>
                </form>
            </CardFooter>
        </Card>
    </AuthBase>
</template>

