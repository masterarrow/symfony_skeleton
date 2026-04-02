<template>
    <div class="flex justify-center items-center h-full">
        <div class="w-full max-w-md">
            <Form v-slot="$form" :resolver="resolver" :initialValues="initialValues" @submit="proceedForm" class="w-full">
                <div class="flex flex-col justify-center items-center gap-4">
                    <div class="flex w-full items-start pb-5">
                        <p class="text-4xl font-extrabold">Sign In</p>
                    </div>

                    <div class="flex flex-col w-full">
                        <FloatLabel variant="on" class="w-full">
                            <InputText id="email" name="email" type="email" class="w-full" autocomplete="on" fluid />
                            <label for="email">Email</label>
                        </FloatLabel>
                        <Message
                            v-if="$form.email?.invalid"
                            severity="error"
                            size="small"
                            variant="simple"
                            class="flex w-full items-start -mt-0.8"
                        >
                            {{ $form.email.error?.message }}
                        </Message>
                    </div>

                    <div class="flex flex-col w-full">
                        <FloatLabel variant="on" class="w-full">
                            <Password id="password" name="password" class="w-full" :feedback="false" fluid />
                            <label for="password">Password</label>
                        </FloatLabel>
                        <Message
                            v-if="$form.password?.invalid"
                            severity="error"
                            size="small"
                            variant="simple"
                            class="flex w-full items-start"
                        >
                            {{ $form.password.error?.message }}
                        </Message>
                    </div>

                    <div class="pt-4 w-full">
                        <Button type="submit" severity="secondary" label="Submit" class="w-full" :loading="state.loading" />
                    </div>
                </div>
            </Form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { sendLoginForm } from '@/services/api/login'
import { useAuth } from '@/stores/useAuth'
import { zodResolver } from '@primevue/forms/resolvers/zod';
import { z } from 'zod';
import { useToast } from 'primevue/usetoast';
import Message from 'primevue/message';
import { Form } from '@primevue/forms';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Button from 'primevue/button';
import FloatLabel from 'primevue/floatlabel';

const toast = useToast();
const initialValues = ref({
    email: '',
    password: ''
});
const resolver = ref(zodResolver(
    z.object({
        email: z
            .string()
            .email('Must be a valid email.'),
        password: z
            .string()
            .trim()
            .pipe(
                z.string()
                    .min(1, 'Password is required.')
            )
    })
));

const state = reactive({ loading: false })
const router = useRouter()
const authStore = useAuth()

/// Methods
const proceedForm = async (form: any) => {
  if (!form.valid) {
    return
  }

  authStore.reset()
  state.loading = true

  try {
    const { email, password } = form.values
    authStore.setEmail(email)

    const res = await sendLoginForm({ email, password })

    if (res?.error) {
      toast.add({ severity: 'error', summary: res.data.error, life: 3000 });
      authStore.setError(res.error)
      form.reset()
    }

    if (res.status) {
      const user = res.data.user
      toast.add({ severity: 'success', summary: res.data.message, life: 3000 });
      authStore.setLoggedIn(true)
      authStore.setFullName(user.full_name)
      router.push({ name: 'dashboard' })
    }
  } catch (error: any) {
    toast.add({ severity: 'error', summary: 'Something went wrong', life: 3000 });
  } finally {
    state.loading = false
  }
}
</script>

<style scoped></style>
