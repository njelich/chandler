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
              <inertia-link :href="layoutData.vault.url.contacts" class="text-blue-500 hover:underline">
                {{ $t('app.breadcrumb_contact_index') }}
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
            <li class="mr-2 inline">
              <inertia-link :href="data.url.show" class="text-blue-500 hover:underline">
                {{ $t('app.breadcrumb_contact_show', { name: data.contact.name }) }}
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
              {{ $t('app.breadcrumb_contact_photo') }}
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="sm:mt-18 relative">
      <div class="mx-auto max-w-3xl px-2 py-2 sm:py-6 sm:px-6 lg:px-8">
        <!-- title + cta -->
        <div class="mb-3 items-center justify-between border-b border-gray-200 pb-2 dark:border-gray-700 sm:flex">
          <div class="mb-2 sm:mb-0">
            <span class="relative mr-1">
              <svg
                class="icon-sidebar relative inline h-4 w-4"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M6 6C6 5.44772 6.44772 5 7 5H17C17.5523 5 18 5.44772 18 6C18 6.55228 17.5523 7 17 7H7C6.44771 7 6 6.55228 6 6Z"
                  fill="currentColor" />
                <path
                  d="M6 10C6 9.44771 6.44772 9 7 9H17C17.5523 9 18 9.44771 18 10C18 10.5523 17.5523 11 17 11H7C6.44771 11 6 10.5523 6 10Z"
                  fill="currentColor" />
                <path
                  d="M7 13C6.44772 13 6 13.4477 6 14C6 14.5523 6.44771 15 7 15H17C17.5523 15 18 14.5523 18 14C18 13.4477 17.5523 13 17 13H7Z"
                  fill="currentColor" />
                <path
                  d="M6 18C6 17.4477 6.44772 17 7 17H11C11.5523 17 12 17.4477 12 18C12 18.5523 11.5523 19 11 19H7C6.44772 19 6 18.5523 6 18Z"
                  fill="currentColor" />
                <path
                  fill-rule="evenodd"
                  clip-rule="evenodd"
                  d="M2 4C2 2.34315 3.34315 1 5 1H19C20.6569 1 22 2.34315 22 4V20C22 21.6569 20.6569 23 19 23H5C3.34315 23 2 21.6569 2 20V4ZM5 3H19C19.5523 3 20 3.44771 20 4V20C20 20.5523 19.5523 21 19 21H5C4.44772 21 4 20.5523 4 20V4C4 3.44772 4.44771 3 5 3Z"
                  fill="currentColor" />
              </svg>
            </span>

            <span class="font-semibold"> Photos </span>
          </div>

          <!-- upload -->
          <uploadcare
            v-if="data.uploadcarePublicKey && data.canUploadFile"
            :public-key="data.uploadcarePublicKey"
            :tabs="'file'"
            :multiple="false"
            :preview-step="false"
            @success="onSuccess"
            @error="onError">
            <pretty-button :text="$t('contact.photos_cta')" :icon="'plus'" :classes="'sm:w-fit w-full'" />
          </uploadcare>
        </div>

        <div v-if="localPhotos.length > 0" class="mb-4">
          <div class="grid grid-cols-3 gap-4">
            <div
              v-for="photo in localPhotos"
              :key="photo.id"
              class="rounded-md border border-gray-200 p-2 shadow-sm hover:bg-slate-50 hover:shadow-lg dark:border-gray-700 dark:bg-slate-900 hover:dark:bg-slate-800">
              <inertia-link :href="photo.url.show">
                <img :src="photo.url.display" :alt="photo.name" />
              </inertia-link>
            </div>
          </div>
        </div>

        <!-- pagination -->
        <Pagination :items="paginator" />

        <div
          v-if="localPhotos.length == 0"
          class="mb-6 rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
          <p class="p-5 text-center">
            {{ $t('contact.photos_blank') }}
          </p>
        </div>
      </div>
    </main>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout.vue';
import PrettyButton from '@/Shared/Form/PrettyButton.vue';
import Pagination from '@/Components/Pagination.vue';
import Uploadcare from '@/Components/Uploadcare.vue';

export default {
  components: {
    Layout,
    PrettyButton,
    Pagination,
    Uploadcare,
  },

  props: {
    layoutData: {
      type: Object,
      default: null,
    },
    data: {
      type: Object,
      default: null,
    },
    paginator: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
      localPhotos: [],
      form: {
        uuid: null,
        name: null,
        original_url: null,
        cdn_url: null,
        mime_type: null,
        size: null,
      },
    };
  },

  created() {
    this.localPhotos = this.data.photos;
  },

  methods: {
    onSuccess(file) {
      this.form.uuid = file.uuid;
      this.form.name = file.name;
      this.form.original_url = file.originalUrl;
      this.form.cdn_url = file.cdnUrl;
      this.form.mime_type = file.mimeType;
      this.form.size = file.size;

      this.upload();
    },

    upload() {
      axios
        .post(this.data.url.store, this.form)
        .then((response) => {
          this.localPhotos.unshift(response.data.data);
          this.flash(this.$t('contact.photos_new_success'), 'success');
        })
        .catch((error) => {
          this.form.errors = error.response.data;
        });
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
