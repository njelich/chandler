<template>
  <div>
    <h3 class="mb-3 flex border-b border-gray-200 pb-1 font-medium dark:border-gray-700">
      <span class="relative mr-2">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="icon-sidebar relative inline h-4 w-4 text-gray-300 hover:text-gray-600 dark:text-gray-400"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          strokeWidth="{2}">
          <path strokeLinecap="round" strokeLinejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </span>

      <span class="mr-2 inline">
        {{ $t('vault.dashboard_last_updated_contacts_title') }}
      </span>

      <help :url="$page.props.help_links.last_updated_contacts" :top="'4px'" />
    </h3>
    <div v-for="contact in data" :key="contact.id" class="mb-2 flex items-center text-sm">
      <avatar :data="contact.avatar" :classes="'mr-2 h-5 w-5 rounded-full'" />

      <inertia-link :href="contact.url.show" class="text-blue-500 hover:underline">
        {{ contact.name }}
      </inertia-link>
    </div>
  </div>
</template>

<script>
import Help from '@/Shared/Help.vue';
import Avatar from '@/Shared/Avatar.vue';

export default {
  components: {
    Help,
    Avatar,
  },

  props: {
    data: {
      type: Object,
      default: null,
    },
  },
};
</script>

<style lang="scss" scoped>
.icon-sidebar {
  color: #737e8d;
  top: -2px;
}
</style>
