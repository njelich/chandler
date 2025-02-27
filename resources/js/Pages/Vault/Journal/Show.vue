<script setup>
import Layout from '@/Shared/Layout.vue';
import PrettyLink from '@/Shared/Form/PrettyLink.vue';

defineProps({
  layoutData: Object,
  data: Object,
});
</script>

<template>
  <layout :layout-data="layoutData" :inside-vault="true">
    <!-- breadcrumb -->
    <nav class="bg-white dark:bg-gray-900 sm:mt-20 sm:border-b">
      <div class="max-w-8xl mx-auto hidden px-4 py-2 sm:px-6 md:block">
        <div class="flex items-baseline justify-between space-x-6">
          <ul class="text-sm">
            <li class="mr-2 inline text-gray-600 dark:text-gray-400">
              {{ $t('app.breadcrumb_location') }}
            </li>
            <li class="mr-2 inline">
              <inertia-link :href="layoutData.vault.url.journals" class="text-blue-500 hover:underline">
                {{ $t('app.breadcrumb_journal_index') }}
              </inertia-link>
            </li>
            <li class="relative mr-2 inline">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="icon-breadcrumb relative inline h-3 w-3"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </li>
            <li class="inline">
              {{ data.name }}
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="sm:mt-18 relative">
      <div class="mx-auto max-w-6xl px-2 py-2 sm:py-6 sm:px-6 lg:px-8">
        <h1 class="mb-8 text-2xl">{{ data.name }}</h1>

        <div class="special-grid grid grid-cols-1 gap-6 sm:grid-cols-3">
          <!-- left -->
          <div class="p-3 sm:p-0">
            <!-- years -->
            <p v-if="data.years.length > 0" class="mb-2 font-medium">
              <span class="mr-1"> 📆 </span> {{ $t('vault.journal_show_years') }}
            </p>
            <ul v-if="data.years.length > 0" class="mb-8">
              <li v-for="year in data.years" :key="year.year" class="mb-2 flex items-center justify-between last:mb-0">
                <inertia-link :href="year.url.show" class="text-blue-500 hover:underline">{{ year.year }}</inertia-link>
                <span class="text-sm text-gray-400">{{ year.posts }}</span>
              </li>
            </ul>

            <!-- tags -->
            <p v-if="data.tags.length > 0" class="mb-2 font-medium">
              <span class="mr-1"> ⚡ </span> {{ $t('vault.journal_show_tags') }}
            </p>
            <ul v-if="data.tags.length > 0">
              <li v-for="tag in data.tags" :key="tag.id" class="mb-2 flex items-center justify-between">
                <span>{{ tag.name }}</span>
                <span class="text-sm text-gray-400">{{ tag.count }}</span>
              </li>
            </ul>
          </div>

          <!-- middle -->
          <div class="p-3 sm:p-0">
            <!-- all months in the year -->
            <div class="mb-2 grid grid-cols-12 gap-2">
              <div v-for="month in data.months" :key="month.id" class="text-center">
                <div class="mb-1 text-xs">{{ month.month }}</div>

                <div :class="month.color" class="h-3 rounded-md border border-gray-200"></div>
              </div>
            </div>

            <!-- list of posts -->
            <ul
              v-if="data.months.length > 0 && data.years.length > 0"
              class="post-list mb-6 rounded-lg border border-b-0 border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
              <!-- loop on months -->
              <li v-for="month in data.months" :key="month.id">
                <div v-if="month.posts.length > 0">
                  <div class="border-b border-gray-200 bg-gray-100 px-5 py-2 text-sm font-semibold">
                    {{ month.month_human_format }}
                  </div>

                  <ul class="border-b border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                    <li
                      v-for="post in month.posts"
                      :key="post.id"
                      class="flex items-center border-b border-gray-200 px-5 py-2 hover:bg-slate-50 dark:border-gray-700 dark:bg-slate-900 hover:dark:bg-slate-800">
                      <!-- written at -->
                      <div class="mr-4 rounded-lg border border-gray-200 p-2 text-center leading-tight">
                        <span class="block text-xs uppercase">{{ post.written_at_day }}</span>
                        <span class="text-xl">{{ post.written_at_day_number }}</span>
                      </div>

                      <!-- content -->
                      <div>
                        <span
                          ><inertia-link :href="post.url.show" class="text-blue-500 hover:underline">{{
                            post.title
                          }}</inertia-link></span
                        >
                        <p v-if="post.excerpt" class="">{{ post.excerpt }}</p>
                      </div>
                    </li>
                  </ul>
                </div>
              </li>
            </ul>

            <!-- blank state -->
            <div v-else class="mb-6 rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
              <p class="p-5 text-center">
                {{ $t('vault.journal_show_blank') }}
              </p>
            </div>
          </div>

          <!-- right -->
          <div class="p-3 sm:p-0">
            <!-- cta -->
            <div class="mb-6 flex justify-center">
              <pretty-link
                v-if="layoutData.vault.permission.at_least_editor"
                :href="data.url.create"
                :text="$t('vault.journal_show_cta')"
                :icon="'plus'" />
            </div>

            <!-- slices of life -->
            <p class="mb-2 font-medium"><span class="mr-1"> 🍕 </span> Slices of life</p>
            <div v-for="slice in data.slices" :key="slice.id" class="mb-6 last:mb-0">
              <img v-if="slice.cover_image" class="h-32 w-full rounded-t" :src="slice.cover_image" alt="" />
              <div
                class="rounded-b border-b border-r border-l border-gray-200 px-3 py-2 hover:bg-slate-50 dark:border-gray-700 dark:bg-slate-900 hover:dark:bg-slate-800"
                :class="slice.cover_image ? '' : 'border-t'">
                <inertia-link :href="slice.url.show" class="font-semibold">{{ slice.name }}</inertia-link>
                <p class="text-xs text-gray-600">{{ slice.date_range }}</p>
              </div>
            </div>

            <div>
              <inertia-link :href="data.url.slice_index" class="text-sm text-blue-500 hover:underline"
                >View all slices</inertia-link
              >
            </div>
          </div>
        </div>
      </div>
    </main>
  </layout>
</template>

<style lang="scss" scoped>
.post-list {
  li:hover:first-child {
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
  }

  li:last-child {
    border-bottom: 0;
  }

  li:hover:last-child {
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
  }
}

.special-grid {
  grid-template-columns: 150px 1fr 200px;
}

@media (max-width: 480px) {
  .special-grid {
    grid-template-columns: 1fr;
  }
}
</style>
