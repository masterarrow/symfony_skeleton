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
                    <span class="text-gray-400">Registrar:</span>
                    <a
                        v-if="(domaiInfo.registrar_url as string).length"
                        class="text-blue-800"
                        :href="`${domaiInfo.registrar_url}`"
                        target="_blank"
                    >
                        {{ domaiInfo.registrar }}
                    </a>
                    <span v-else class="text-blue-800">{{ domaiInfo.registrar }}</span>
                </p>
                <p class="text-xl font-extrabold flex gap-3">
                    <span class="text-gray-400 flex w-1/2">Registrant Email:</span>
                    <a
                        v-if="isEmail(domaiInfo.registrant_email as string)"
                        class="text-blue-800"
                        :href="`mailto:${domaiInfo.registrant_email}`"
                    >
                        {{ domaiInfo.registrant_email }}
                    </a>
                    <span v-else class="text-blue-800">{{ domaiInfo.registrant_email }}</span>
                </p>
                <p class="text-xl font-extrabold flex gap-3">
                    <span class="text-gray-400 flex w-1/2">Registrar Abuse Email:</span>
                    <a
                        v-if="isEmail(domaiInfo.registrar_abuse as string)"
                        class="text-blue-800"
                        :href="`mailto:${domaiInfo.registrar_abuse}`"
                    >
                        {{ domaiInfo.registrar_abuse }}
                    </a>
                    <span v-else class="text-blue-800">{{ domaiInfo.registrar_abuse }}</span>
                </p>
                <p class="text-xl font-extrabold flex gap-3">
                    <span class="text-gray-400">Owner:</span>
                    <span class="font-bold!">{{ domaiInfo.owner }}</span>
                </p>
                <p class="text-xl font-extrabold flex gap-3">
                    <span class="text-gray-400">Whois Server:</span>
                    <span @click="copyToClipboard(domaiInfo.whois_server as string)" class="text-blue-800 cursor-pointer">
                        {{ domaiInfo.whois_server }}
                    </span>
                </p>
                <p class="text-xl font-extrabold flex items-center gap-3">
                    <span class="text-gray-400">Creation Date:</span>
                    <div class="flex items-center gap-1.5">
                        <i class="pi pi-clock text-gray-400" style="font-size: 1rem"></i>
                        {{ domaiInfo.cr_date }}
                    </div>
                </p>
                <p class="text-xl font-extrabold flex items-center gap-3">
                    <span class="text-gray-400">Updated Date:</span>
                    <div class="flex items-center gap-1.5">
                        <i class="pi pi-clock text-gray-400" style="font-size: 1rem"></i>
                        {{ domaiInfo.updated_date }}
                    </div>
                </p>
                <p class="text-xl font-extrabold flex items-center gap-3">
                    <span class="text-gray-400">Expiration Date:</span>
                    <div class="flex items-center gap-1.5">
                        <i class="pi pi-clock text-gray-400" style="font-size: 1rem"></i>
                        {{ domaiInfo.exp_date }}
                    </div>
                </p>
                <div class="flex text-xl font-extrabold gap-3 w-full">
                    <span class="text-gray-400 flex">Name Servers:</span>
                    <div class="flex flex-col">
                        <span
                            v-for="server in domaiInfo.name_servers"
                            @click="copyToClipboard(server as string)"
                            class="text-blue-800 cursor-pointer"
                        >
                            {{ server }}
                        </span>
                    </div>
                </div>
                <p class="text-xl font-extrabold flex gap-3">
                    <span class="text-gray-400">DNSSEC:</span>
                    <i>{{ domaiInfo.dnssec }}</i>
                </p>
                <div class="flex text-xl font-extrabold gap-3 w-full">
                    <span class="text-gray-400">States:</span>
                    <div class="flex flex-col">
                        <span class="text-green-800" v-for="state in domaiInfo.states">
                            {{ (state as string).split(' ')[0] }}
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

/// Methods
const isEmail = (email: string) => {
    if (!email) return false;

    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

const copyToClipboard = async (text: string) => {
    if (!text) return;

    if (window.isSecureContext && navigator.clipboard) {
        try {
            await navigator.clipboard.writeText(text);
            toast.add({ severity: 'info', summary: 'Copied to clipboard', detail: text, life: 3000 });
            return;
        } catch (err) { }
    }

    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.left = "-9999px";
    textArea.style.top = "0";
    document.body.appendChild(textArea);

    textArea.focus();
    textArea.select();

    try {
        const successful = document.execCommand('copy');
        if (successful) {
            toast.add({ severity: 'info', summary: 'Copied to clipboard', detail: text, life: 3000 });
            return
        }
    } catch (err) { }

    document.body.removeChild(textArea);
    toast.add({ severity: 'error', summary: 'Error', detail: 'Your browser does not support copying', life: 3000 });
}

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

        if (res?.error) {
            message.value = res.error
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
