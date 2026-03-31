<template>
    <div class="flex flex-col items-center justify-center h-full gap-8">
        <Form v-slot="$form" :resolver="resolver" :initialValues="initialValues" @submit="proceedForm" class="w-full">
            <div class="flex flex-col justify-center items-center">
                <p class="text-4xl font-extrabold pb-8">Domain Whois</p>

                <div class="flex w-full items-center gap-4 max-w-1/2">
                    <div class="flex flex-col w-full">
                        <FloatLabel variant="on" class="w-full">
                            <InputText id="domain" name="domain" type="text" class="w-full" autocomplete="off" fluid />
                            <label for="email">Domain</label>
                        </FloatLabel>
                        <Message
                            v-if="$form.domain?.invalid"
                            severity="error"
                            size="small"
                            variant="simple"
                            class="flex w-full items-start -mt-0.8"
                        >
                            {{ $form.domain.error?.message }}
                        </Message>
                    </div>

                    <Button type="submit" severity="secondary" label="Check" :loading="state.loading" />
                </div>
            </div>
        </Form>

        <Message v-if="!state.loading && message.length" severity="error" size="large" variant="simple" class="flex w-full justify-center">
            {{ message }}
        </Message>

        <div v-if="!state.loading && domaiInfo.available" class="flex w-full items-center max-w-1/2">
            <div class="flex flex-col justify-start items-start gap-2">
                <p class="text-xl font-extrabold flex gap-3">
                    <span class="text-gray-400">Registrar: </span>
                    <a class="text-blue-800" :href="domaiInfo.registrar_url" target="_blank">{{ domaiInfo.registrar }}</a>
                </p>
                <p class="text-xl font-extrabold flex gap-3">
                    <span class="text-gray-400">Registrant Email: </span>
                    <a class="text-blue-800" :href="'mailto:' + domaiInfo.registrant_email">{{ domaiInfo.registrant_email }}</a>
                </p>
                <p class="text-xl font-extrabold flex gap-3">
                    <span class="text-gray-400">Registrar Abuse Email: </span>
                    <a class="text-blue-800" :href="'mailto:' + domaiInfo.registrar_abuse">{{ domaiInfo.registrar_abuse }}</a>
                </p>
                <p class="text-xl font-extrabold flex gap-3">
                    <span class="text-gray-400">Owner: </span>
                    {{ domaiInfo.owner }}
                </p>
                <p class="text-xl font-extrabold flex gap-3">
                    <span class="text-gray-400">Whois Server: </span>
                    {{ domaiInfo.whois_server }}
                </p>
                <p class="text-xl font-extrabold flex gap-3">
                    <span class="text-gray-400">Creation Date: </span>
                    {{ domaiInfo.cr_date }}
                </p>
                <p class="text-xl font-extrabold flex gap-3">
                    <span class="text-gray-400">Updated Date: </span>
                    {{ domaiInfo.updated_date }}
                </p>
                <p class="text-xl font-extrabold flex gap-3">
                    <span class="text-gray-400">Expiration Date: </span>
                    {{ domaiInfo.exp_date }}
                </p>
                <div class="flex text-xl font-extrabold gap-3">
                    <span class="text-gray-400">Name Servers: </span>
                    <div class="flex flex-col w-1/2">
                        <span v-for="server in domaiInfo.name_servers">
                            {{ server }}
                        </span>
                    </div>
                </div>
                <p class="text-xl font-extrabold flex gap-3">
                    <span class="text-gray-400">DNSSEC: </span>
                    {{ domaiInfo.dnssec }}
                </p>
                <div class="flex text-xl font-extrabold gap-3">
                    <span class="text-gray-400">States: </span>
                    <div class="flex flex-col w-1/2">
                        <span v-for="state in domaiInfo.states">
                            {{ state.split(' ')[0] }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import { zodResolver } from '@primevue/forms/resolvers/zod';
import { z } from 'zod';
import isFQDN from 'validator/lib/isFQDN';
import { useToast } from 'primevue/usetoast';
import Message from 'primevue/message';
import { Form } from '@primevue/forms';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import FloatLabel from 'primevue/floatlabel';
import { domainWhois } from '@/services/api/whois';

const toast = useToast();
const initialValues = ref({
    domain: '',
});
const resolver = ref(zodResolver(
    z.object({
        domain: z
            .string()
            .trim()
            .refine((val) => isFQDN(val), {
                message: 'Invalid domain name format.',
            }),
    })
));
const state = reactive({ loading: false })
const message = ref('')
const domaiInfo = reactive<{[key: string]: string|string[]|boolean}>({
    available: false,
    registrar: '',
    registrar_url: '',
    registrant_email: '',
    registrar_abuse: '',
    name_servers: [],
    dnssec: '',
    whois_server: '',
    cr_date: '',
    updated_date: '',
    exp_date: '',
    owner: '',
    states: [],
})

const proceedForm = async (form: any) => {
    if (!form.valid) {
        return
    }

    state.loading = true
    message.value = ''

    for (const key in domaiInfo) {
        if (key === 'available') {
            domaiInfo.available = false
        }
        domaiInfo[key] = ''
    }

    try {
        const { domain } = form.values

        const res = await domainWhois(domain)

        if (res.data?.error) {
            message.value = res.data.error
        }

        if (res.status) {
            if (!res.data?.available) {
                message.value = 'Domain is not registered yet'
                return
            }

            Object.assign(domaiInfo, res.data);
        }
    } catch (error: any) {
        toast.add({ severity: 'error', summary: 'Something went wrong', life: 3000 });
    } finally {
        state.loading = false
    }
}
</script>
