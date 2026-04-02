<template>
    <div class="flex justify-center items-center h-full">
        <div class="w-full max-w-xl">
            <Form v-slot="$form" :resolver="resolver" v-if="state.loaded" :initialValues="initialValues" @submit="proceedForm" class="w-full">
                <div class="flex flex-col justify-center items-center gap-4">
                    <div class="flex w-full items-start pb-5">
                        <p class="text-4xl font-extrabold">Profile</p>
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
import { getProfile } from '@/services/api/user'
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
        password: z
            .string({
                required_error: "Password is required.",
            })
    })
));

const selectedCountry = ref(false);
const counties = ref();
const phoneCodes = ref();
const state = reactive({ loading: false, loaded: false });
const router = useRouter()
const authStore = useAuth()

/// Methods
const loadData = async () => {
    counties.value = countryCodes.map((data) =>
        ({ value: data.countryName, code: data.countryCode, iso2: data.countryCode })
    )
    phoneCodes.value = countryCodes.map((data) =>
        ({ value: data.callingCode + ' ' + data.countryName, code: data.callingCode, iso2: data.countryCode })
    )

    try {
        const res = await getProfile()

        if (!res.status) {
            toast.add({ severity: 'error', summary: 'Cannot load profile', life: 3000 });
            return
        }

        initialValues.value.firstName = res.data.user.first_name
        initialValues.value.lastName = res.data.user.last_name
        initialValues.value.email = res.data.user.email
        const userCountryCode = res.data.user.country.toUpperCase();
        initialValues.value.country = counties.value.find((c: any) => c.iso2 === userCountryCode) || null
        initialValues.value.phonePrefix = phoneCodes.value.find((data: any) => data.iso2 === userCountryCode) || null
        initialValues.value.phone = res.data.user.phone
    } catch (error: any) {
        toast.add({ severity: 'error', summary: 'Cannot load profile', life: 3000 });
    } finally {
        state.loaded = true
    }
}

const proceedForm = async (form: any) => {
  if (!form.valid) {
    return
  }

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
      toast.add({ severity: 'success', summary: 'Profile successfully updated', life: 3000 });
      authStore.setFullName(form.values.firstName + ' ' + form.values.lastName)
      authStore.setEmail(form.values.email)
      form.states.password.value = ''
    }

    if (!res.status) {
      if (res?.errors) {
        for (const [field, messages] of Object.entries(res.errors)) {
            const formField = toCamelCase(field);
            if (form.states[formField]) {
                form.states[formField].error = { message: messages[0] };
                form.states[formField].invalid = true;
            }
        }
      } else {
        toast.add({ severity: 'error', summary: res.error, life: 3000 });
      }
    }
  } catch (error: any) {
    toast.add({ severity: 'error', summary: 'Something went wrong', life: 3000 });
  } finally {
    state.loading = false
  }
}

const toCamelCase = (str: string) => str.replace(/_([a-z])/g, (g) => g[1].toUpperCase());

function onCountryChange(form: any) {
  form.phonePrefix.value = phoneCodes.value.find((data: any) => data.iso2 === form.country.value.iso2)
}

/// Lifecycle
onMounted(() => {
    loadData()
})
</script>

<style scoped>
.country-flag {
    width: 20px;
    margin-right: 0.5rem;
}
</style>
