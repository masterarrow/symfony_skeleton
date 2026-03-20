<template>
    <div class="bg-gray-50 pt-8 w-full">
      <div class="w-full mx-auto px-4">
        <div class="flex flex-wrap justify-around mt-10 pb-8">
          <img v-for="(logo, i) in logos" :key="i" :src="logo" alt="Company logo" class="h-8" />
        </div>
      </div>
    </div>
</template>

<script setup lang="ts">
const logos = [
  'https://automation.vuejs.org/images/vuemastery.png',
  'https://www.pngmart.com/files/23/Intel-Logo-PNG-HD-Isolated.png',
  'https://www.pngmart.com/files/16/official-Google-Logo-PNG-Clipart.png',
  'https://automation.vuejs.org/images/chrome_frameworks_fund.png',
  'https://www.pngmart.com/files/22/GitHub-Transparent-PNG.png',
];
</script>
