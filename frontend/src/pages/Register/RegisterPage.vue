<template>
    <div class="flex justify-center items-center h-full">
        <div class="w-full max-w-xl">
            <Form v-slot="$form" :resolver="resolver" :initialValues="initialValues" @submit="proceedForm" class="w-full">
                <div class="flex flex-col justify-center items-center gap-4">
                    <div class="flex w-full items-start pb-5">
                        <p class="text-4xl font-extrabold">Sign Up</p>
                    </div>

                    <div class="flex w-full items-start gap-3">
                        <div class="flex flex-col w-full">
                            <FloatLabel variant="on" class="w-full">
                                <InputText id="firstName" name="firstName" type="firstName" class="w-full" fluid />
                                <label for="firstName">First name</label>
                            </FloatLabel>
                            <Message
                                v-if="$form.firstName?.invalid"
                                severity="error"
                                size="small"
                                variant="simple"
                                class="flex w-full items-start -mt-0.8"
                            >
                                {{ $form.firstName.error?.message }}
                            </Message>
                        </div>

                        <div class="flex flex-col w-full">
                            <FloatLabel variant="on" class="w-full">
                                <InputText id="lastName" name="lastName" type="lastName" class="w-full" fluid />
                                <label for="lastName">Last name</label>
                            </FloatLabel>
                            <Message
                                v-if="$form.lastName?.invalid"
                                severity="error"
                                size="small"
                                variant="simple"
                                class="flex w-full items-start -mt-0.8"
                            >
                                {{ $form.lastName.error?.message }}
                            </Message>
                        </div>
                    </div>

                    <div class="flex w-full items-start gap-3">
                        <div class="flex flex-col w-full">
                            <FloatLabel variant="on" class="w-full">
                                <InputText id="email" name="email" type="email" class="w-full" fluid />
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
                                <Select
                                    id="country"
                                    name="country"
                                    :options="counties"
                                    optionLabel="value"
                                    placeholder="Country"
                                    @change="onCountryChange($form)"
                                    class="h-[44px]"
                                    filter
                                    fluid
                                >
                                    <template #value="slotProps">
                                        <div v-if="slotProps.value.iso2.length" class="flex items-center">
                                            <img :alt="slotProps.value.label" :src="`https://flagcdn.com/w40/${slotProps.value.iso2.toLowerCase()}.png`" class="country-flag" />
                                            <div>{{ slotProps.value.value }}</div>
                                        </div>
                                    </template>
                                    <template #option="slotProps">
                                        <div class="flex items-center">
                                            <img v-if="slotProps.option.iso2.length" :alt="slotProps.option.label" :src="`https://flagcdn.com/w40/${slotProps.option.iso2.toLowerCase()}.png`" class="country-flag" />
                                            <div>{{ slotProps.option.value }}</div>
                                        </div>
                                    </template>
                                </Select>
                                <label v-if="selectedCountry" for="country">Country</label>
                            </FloatLabel>
                            <Message
                                v-if="$form.country?.invalid"
                                severity="error"
                                size="small"
                                variant="simple"
                                class="flex w-full items-start -mt-0.8"
                            >
                                {{ $form.country.error?.message }}
                            </Message>
                        </div>
                    </div>

                    <div class="flex w-full items-start gap-3">
                        <div class="flex flex-col w-full">
                            <FloatLabel variant="on" class="w-full">
                                <Select
                                    id="phonePrefix"
                                    name="phonePrefix"
                                    :options="phoneCodes"
                                    optionLabel="value"
                                    placeholder="Phone code"
                                    class="h-[44px]"
                                    filter
                                    fluid
                                >
                                    <template #value="slotProps">
                                        <div v-if="slotProps.value.iso2.length" class="flex items-center">
                                            <img :alt="slotProps.value.label" :src="`https://flagcdn.com/w40/${slotProps.value.iso2.toLowerCase()}.png`" class="country-flag" />
                                            <div>{{ slotProps.value.value }}</div>
                                        </div>
                                    </template>
                                    <template #option="slotProps">
                                        <div class="flex items-center">
                                            <img :alt="slotProps.option.label" :src="`https://flagcdn.com/w40/${slotProps.option.iso2.toLowerCase()}.png`" class="country-flag" />
                                            <div>{{ slotProps.option.value }}</div>
                                        </div>
                                    </template>
                                </Select>
                                <label v-if="selectedCountry" for="phonePrefix">Phone code</label>
                            </FloatLabel>
                            <Message
                                v-if="$form.phonePrefix?.invalid"
                                severity="error"
                                size="small"
                                variant="simple"
                                class="flex w-full items-start -mt-0.8"
                            >
                                {{ $form.phonePrefix.error?.message }}
                            </Message>
                        </div>

                        <div class="flex flex-col w-full">
                            <FloatLabel variant="on" class="w-full">
                                <InputText id="phone" name="phone" type="text" class="w-full" fluid />
                                <label for="phone">Phone number</label>
                            </FloatLabel>
                            <Message
                                v-if="$form.phone?.invalid"
                                severity="error"
                                size="small"
                                variant="simple"
                                class="flex w-full items-start -mt-0.8"
                            >
                                {{ $form.phone.error?.message }}
                            </Message>
                        </div>
                    </div>

                    <div class="flex w-full items-start gap-3">
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
                                class="flex w-full items-start -mt-0.8"
                            >
                                {{ $form.password.error?.message }}
                            </Message>
                        </div>

                        <div class="flex flex-col w-full">
                            <FloatLabel variant="on" class="w-full">
                                <Password id="confirmPassword" name="confirmPassword" class="w-full" :feedback="false" fluid />
                                <label for="confirmPassword">Password confirmation</label>
                            </FloatLabel>
                            <Message
                                v-if="$form.confirmPassword?.invalid"
                                severity="error"
                                size="small"
                                variant="simple"
                                class="flex w-full items-start -mt-0.8"
                            >
                                {{ $form.confirmPassword.error?.message }}
                            </Message>
                        </div>
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
import { reactive, ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { sendRegisterForm } from '@/services/api/register'
import { useAuth } from '@/stores/useAuth'
import { zodResolver } from '@primevue/forms/resolvers/zod';
import { z } from 'zod';
import { useToast } from "primevue/usetoast";
import Message from 'primevue/message';
import { Form } from '@primevue/forms';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Password from 'primevue/password';
import FloatLabel from 'primevue/floatlabel';
import Button from 'primevue/button';
import countryCodes from '@/assets/countryCodes.json';

const toast = useToast();
const initialValues = ref({
    firstName: '',
    lastName: '',
    email: '',
    country: { value: '', code: '', iso2: '' },
    phonePrefix: { value: '', code: '', iso2: '' },
    phone: '',
    password: '',
    confirmPassword: '',
});

const lengthValidator = (min: number, max: number) => {
    return z.string().trim()
        .pipe(
            z.string()
                .min(min, `Must be at least ${min} characters.`)
                .max(max, `Must be at most ${max} characters.`)
        );
}

const resolver = ref(zodResolver(
    z.object({
        firstName: lengthValidator(2, 30)
            .pipe(
                z.string()
                    .regex(/^\p{L}+$/u, 'Must constain only letters.')
            ),
        lastName: lengthValidator(2, 30)
            .pipe(
                z.string()
                    .regex(/^\p{L}+$/u, 'Must constain only letters.')
            ),
        email: z
            .string()
            .email('Must be a valid email.'),
        country: z.object({
            value: z.string(),
            code: z.string().optional(),
            iso2: z.string().optional(),
        }).nullable().refine(
            (val) => val !== null && val.value.trim() !== '',
            { message: 'Country is required.' }
        ),
        phonePrefix: z.object({
            value: z.string(),
            code: z.string().optional(),
            iso2: z.string().optional(),
        }).nullable().refine(
            (val) => val !== null && val.value.trim() !== '',
            { message: 'Phone code is required.' }
        ),
        phone: z.union([
            z.literal(''),
            z.string()
                .regex(/^\d+$/, 'Must contain only numbers from 0-9.')
                .min(9, 'Must be at least 9 characters.')
                .max(12, 'Must be at most 12 characters.')
        ]),
        password: lengthValidator(5, 30)
            .pipe(
                z.string()
                .regex(/[a-z]/, 'Must contain a lowercase letter.')
            )
            .pipe(
                z.string()
                .regex(/\d/, 'Must contain a number.')
            ),
        confirmPassword: z.string().min(1, 'Please confirm your password'),
    }).refine(data => data.password === data.confirmPassword, {
        message: 'Passwords must match',
        path: ['confirmPassword'],
    })
));

const selectedCountry = ref(false);
const counties = ref();
const phoneCodes = ref();
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
    const userData = {
        first_name: form.values.firstName,
        last_name: form.values.lastName,
        email: form.values.email,
        country: form.values.country.code,
        phone_prefix: form.values.phonePrefix.code,
        phone: form.values.phone,
        password: form.values.password,
    }

    const res = await sendRegisterForm(userData)

    if (res.status) {
      toast.add({ severity: 'success', summary: 'Successfully registered. Please log in', life: 3000 });
      router.push({ name: 'sign-in' })
    }

    if (!res.status) {
      toast.add({ severity: 'error', summary: res.data.error, life: 3000 });
    }
  } catch (error: any) {
    toast.add({ severity: 'error', summary: 'Something went wrong', life: 3000 });
  } finally {
    state.loading = false
  }
}

function onCountryChange(form: any) {
  form.phonePrefix.value = phoneCodes.value.find((data: any) => data.iso2 === form.country.value.iso2)
}

/// Lifecycle
onMounted(() => {
    counties.value = countryCodes.map((data) =>
        ({ value: data.countryName, code: data.countryCode, iso2: data.countryCode })
    )
    phoneCodes.value = countryCodes.map((data) =>
        ({ value: data.callingCode + ' ' + data.countryName, code: data.callingCode, iso2: data.countryCode })
    )
})
</script>

<style scoped>
.country-flag {
    width: 20px;
    margin-right: 0.5rem;
}
</style>
