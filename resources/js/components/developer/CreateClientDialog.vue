<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { store as storeClient } from '@/routes/developer/clients';
import { useForm } from '@inertiajs/vue3';
import { Loader2 } from 'lucide-vue-next';

const open = defineModel<boolean>('open', { required: true });

const emit = defineEmits<{
    success: [];
}>();

const form = useForm({
    name: '',
    redirect_url: '',
});

const handleSubmit = () => {
    form.post(storeClient.url(), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            emit('success');
        },
    });
};

const handleClose = () => {
    form.reset();
    form.clearErrors();
    open.value = false;
};
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="sm:max-w-md">
            <form @submit.prevent="handleSubmit">
                <DialogHeader>
                    <DialogTitle>Create OAuth Application</DialogTitle>
                    <DialogDescription>
                        Create a new OAuth application to allow third-party authentication.
                    </DialogDescription>
                </DialogHeader>

                <div class="mt-6 space-y-4">
                    <div class="grid gap-2">
                        <Label for="name">Application Name</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            type="text"
                            placeholder="My Application"
                            required
                            autocomplete="off"
                        />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="redirect_url">Redirect URL</Label>
                        <Input
                            id="redirect_url"
                            v-model="form.redirect_url"
                            type="url"
                            placeholder="https://example.com/callback"
                            required
                            autocomplete="off"
                        />
                        <p class="text-xs text-muted-foreground">
                            The URL users will be redirected to after authorization.
                        </p>
                        <InputError :message="form.errors.redirect_url" />
                    </div>
                </div>

                <DialogFooter class="mt-6 gap-2">
                    <Button
                        type="button"
                        variant="outline"
                        @click="handleClose"
                    >
                        Cancel
                    </Button>
                    <Button
                        type="submit"
                        :disabled="form.processing"
                    >
                        <Loader2
                            v-if="form.processing"
                            class="mr-2 h-4 w-4 animate-spin"
                        />
                        Create Application
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>

