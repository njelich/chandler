<template>
  <div class="mb-10">
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
              d="M15 17C16.1046 17 17 16.1046 17 15C17 13.8954 16.1046 13 15 13C13.8954 13 13 13.8954 13 15C13 16.1046 13.8954 17 15 17Z"
              fill="currentColor" />
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M6 3C4.34315 3 3 4.34315 3 6V18C3 19.6569 4.34315 21 6 21H18C19.6569 21 21 19.6569 21 18V6C21 4.34315 19.6569 3 18 3H6ZM5 18V7H19V18C19 18.5523 18.5523 19 18 19H6C5.44772 19 5 18.5523 5 18Z"
              fill="currentColor" />
          </svg>
        </span>

        <span class="font-semibold"> Reminders </span>
      </div>
      <pretty-button
        :text="'Add a reminder'"
        :icon="'plus'"
        :classes="'sm:w-fit w-full'"
        @click="showCreateReminderModal" />
    </div>

    <!-- add a reminder modal -->
    <form
      v-if="addReminderModalShown"
      class="bg-form mb-6 rounded-lg border border-gray-200 dark:border-gray-700 dark:bg-gray-900"
      @submit.prevent="submit()">
      <div class="border-b border-gray-200 dark:border-gray-700">
        <div v-if="form.errors.length > 0" class="p-5">
          <errors :errors="form.errors" />
        </div>

        <!-- name -->
        <div class="border-b border-gray-200 p-5 dark:border-gray-700">
          <text-input
            :ref="'label'"
            v-model="form.label"
            :label="'Name of the reminder'"
            :type="'text'"
            :autofocus="true"
            :input-class="'block w-full'"
            :required="true"
            :autocomplete="false"
            :maxlength="255"
            @esc-key-pressed="addReminderModalShown = false" />
        </div>

        <div class="border-b border-gray-200 p-5 dark:border-gray-700">
          <!-- case: I know the exact date -->
          <div class="mb-2 flex items-center">
            <input
              id="full_date"
              v-model="form.choice"
              value="full_date"
              name="date"
              type="radio"
              class="h-4 w-4 border-gray-300 text-sky-500 dark:border-gray-700" />
            <label
              for="full_date"
              class="ml-3 block cursor-pointer text-sm font-medium text-gray-700 dark:text-gray-300">
              I know the exact date, including the year
            </label>
          </div>
          <div v-if="form.choice == 'full_date'" class="ml-6 mb-4">
            <v-date-picker v-model="form.date" class="inline-block h-full" :model-config="modelConfig">
              <template #default="{ inputValue, inputEvents }">
                <input
                  class="rounded border bg-white px-2 py-1 dark:bg-gray-900"
                  :value="inputValue"
                  v-on="inputEvents" />
              </template>
            </v-date-picker>
          </div>

          <!-- case: date and month -->
          <div class="flex items-center">
            <input
              id="month_day"
              v-model="form.choice"
              value="month_day"
              name="date"
              type="radio"
              class="h-4 w-4 border-gray-300 text-sky-500 dark:border-gray-700" />
            <label
              for="month_day"
              class="ml-3 block cursor-pointer text-sm font-medium text-gray-700 dark:text-gray-300">
              I only know the day and month, not the year
            </label>
          </div>
          <div v-if="form.choice == 'month_day'" class="mt-2 ml-6 flex">
            <dropdown
              v-model="form.month"
              :data="data.months"
              :required="true"
              :div-outer-class="'mb-5 mr-2'"
              :placeholder="$t('app.choose_value')"
              :dropdown-class="'block w-full'"
              :label="'Month'" />

            <dropdown
              v-model="form.day"
              :data="data.days"
              :required="true"
              :div-outer-class="'mb-5'"
              :placeholder="$t('app.choose_value')"
              :dropdown-class="'block w-full'"
              :label="'Day'" />
          </div>
        </div>

        <!-- reminder options -->
        <div class="p-5">
          <p class="mb-1">How often should we remind you about this date?</p>
          <p class="mb-1 text-sm text-gray-600 dark:text-gray-400">
            If the date is in the past, the next occurence of the date will be next year.
          </p>

          <div class="mt-4 ml-4">
            <div class="mb-2 flex items-center">
              <input
                id="one_time"
                v-model="form.reminderChoice"
                value="one_time"
                name="reminder-frequency"
                type="radio"
                class="h-4 w-4 border-gray-300 text-sky-500 dark:border-gray-700" />
              <label
                for="one_time"
                class="ml-3 block cursor-pointer text-sm font-medium text-gray-700 dark:text-gray-300">
                Only once, when the next occurence of the date occurs.
              </label>
            </div>

            <div class="mb-2 flex items-center">
              <input
                id="recurring"
                v-model="form.reminderChoice"
                value="recurring"
                name="reminder-frequency"
                type="radio"
                class="h-4 w-4 border-gray-300 text-sky-500 dark:border-gray-700" />
              <label
                for="recurring"
                class="ml-3 block flex cursor-pointer items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                <span class="mr-2"> Every </span>

                <select
                  :id="id"
                  v-model="form.frequencyNumber"
                  class="mr-2 rounded-md border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-300 focus:outline-none focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-900 sm:text-sm"
                  :required="required"
                  @change="change">
                  <option v-for="n in 10" :key="n" :value="n">
                    {{ n }}
                  </option>
                </select>

                <select
                  :id="id"
                  v-model="form.frequencyType"
                  class="mr-2 rounded-md border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-300 focus:outline-none focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-900 sm:text-sm"
                  :required="required"
                  @change="change">
                  <option value="recurring_day">day</option>
                  <option value="recurring_month">month</option>
                  <option value="recurring_year">year</option>
                </select>

                <span>after the next occurence of the date.</span>
              </label>
            </div>
          </div>
        </div>
      </div>

      <div class="flex justify-between p-5">
        <pretty-span :text="$t('app.cancel')" :classes="'mr-3'" @click="addReminderModalShown = false" />
        <pretty-button :text="'Add date'" :state="loadingState" :icon="'plus'" :classes="'save'" />
      </div>
    </form>

    <!-- reminders -->
    <div v-if="localReminders.length > 0">
      <ul class="mb-4 rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
        <li
          v-for="reminder in localReminders"
          :key="reminder.id"
          class="item-list border-b border-gray-200 hover:bg-slate-50 dark:border-gray-700 dark:bg-slate-900 hover:dark:bg-slate-800">
          <!-- reminder -->
          <div class="flex items-center justify-between px-3 py-2">
            <div class="flex items-center">
              <span class="mr-2 text-sm text-gray-500">{{ reminder.date }}</span>
              <span class="mr-2">{{ reminder.label }}</span>

              <!-- recurring icon -->
              <a-tooltip v-if="reminder.type != 'one_time'" placement="topLeft" title="Recurring" arrow-point-at-center>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-3 w-3"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
              </a-tooltip>
            </div>

            <!-- actions -->
            <ul class="text-sm">
              <li
                class="mr-4 inline cursor-pointer text-blue-500 hover:underline"
                @click="showEditReminderModal(reminder)">
                {{ $t('app.edit') }}
              </li>
              <li class="inline cursor-pointer text-red-500 hover:text-red-900" @click="destroy(reminder)">
                {{ $t('app.delete') }}
              </li>
            </ul>
          </div>

          <!-- edit reminder modal -->
          <form v-if="editedReminderId == reminder.id" class="bg-form" @submit.prevent="update(reminder)">
            <div class="border-b border-gray-200 dark:border-gray-700">
              <div v-if="form.errors.length > 0" class="p-5">
                <errors :errors="form.errors" />
              </div>

              <!-- name -->
              <div class="border-b border-gray-200 p-5 dark:border-gray-700">
                <text-input
                  :ref="'label' + reminder.id"
                  v-model="form.label"
                  :label="'Name of the reminder'"
                  :type="'text'"
                  :autofocus="true"
                  :input-class="'block w-full'"
                  :required="true"
                  :autocomplete="false"
                  :maxlength="255"
                  @esc-key-pressed="addReminderModalShown = false" />
              </div>

              <div class="border-b border-gray-200 p-5 dark:border-gray-700">
                <!-- case: I know the exact date -->
                <div class="mb-2 flex items-center">
                  <input
                    id="full_date"
                    v-model="form.choice"
                    value="full_date"
                    name="date"
                    type="radio"
                    class="h-4 w-4 border-gray-300 text-sky-500 dark:border-gray-700" />
                  <label
                    for="full_date"
                    class="ml-3 block cursor-pointer text-sm font-medium text-gray-700 dark:text-gray-300">
                    I know the exact date, including the year
                  </label>
                </div>
                <div v-if="form.choice == 'full_date'" class="ml-6 mb-4">
                  <v-date-picker v-model="form.date" class="inline-block h-full" :model-config="modelConfig">
                    <template #default="{ inputValue, inputEvents }">
                      <input
                        class="rounded border bg-white px-2 py-1 dark:bg-gray-900"
                        :value="inputValue"
                        v-on="inputEvents" />
                    </template>
                  </v-date-picker>
                </div>

                <!-- case: date and month -->
                <div class="flex items-center">
                  <input
                    id="month_day"
                    v-model="form.choice"
                    value="month_day"
                    name="date"
                    type="radio"
                    class="h-4 w-4 border-gray-300 text-sky-500 dark:border-gray-700" />
                  <label
                    for="month_day"
                    class="ml-3 block cursor-pointer text-sm font-medium text-gray-700 dark:text-gray-300">
                    I only know the day and month, not the year
                  </label>
                </div>
                <div v-if="form.choice == 'month_day'" class="mt-2 ml-6 flex">
                  <dropdown
                    v-model="form.month"
                    :data="data.months"
                    :required="true"
                    :div-outer-class="'mr-2'"
                    :placeholder="$t('app.choose_value')"
                    :dropdown-class="'block w-full'"
                    :label="'Month'" />

                  <dropdown
                    v-model="form.day"
                    :data="data.days"
                    :required="true"
                    :placeholder="$t('app.choose_value')"
                    :dropdown-class="'block w-full'"
                    :label="'Day'" />
                </div>
              </div>

              <!-- reminder options -->
              <div class="p-5">
                <p class="mb-1">How often should we remind you about this date?</p>
                <p class="mb-1 text-sm text-gray-600 dark:text-gray-400">
                  If the date is in the past, the next occurence of the date will be next year.
                </p>

                <div class="mt-4 ml-4">
                  <div class="mb-2 flex items-center">
                    <input
                      id="one_time"
                      v-model="form.reminderChoice"
                      value="one_time"
                      name="reminder-frequency"
                      type="radio"
                      class="h-4 w-4 border-gray-300 text-sky-500 dark:border-gray-700" />
                    <label
                      for="one_time"
                      class="ml-3 block cursor-pointer text-sm font-medium text-gray-700 dark:text-gray-300">
                      Only once, when the next occurence of the date occurs.
                    </label>
                  </div>

                  <div class="mb-2 flex items-center">
                    <input
                      id="recurring"
                      v-model="form.reminderChoice"
                      value="recurring"
                      name="reminder-frequency"
                      type="radio"
                      class="h-4 w-4 border-gray-300 text-sky-500 dark:border-gray-700" />
                    <label
                      for="recurring"
                      class="ml-3 block flex cursor-pointer items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                      <span class="mr-2">Every</span>

                      <select
                        :id="id"
                        v-model="form.frequencyNumber"
                        class="mr-2 rounded-md border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-300 focus:outline-none focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-900 sm:text-sm"
                        :required="required"
                        @change="change">
                        <option v-for="n in 10" :key="n" :value="n">
                          {{ n }}
                        </option>
                      </select>

                      <select
                        :id="id"
                        v-model="form.frequencyType"
                        class="mr-2 rounded-md border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-300 focus:outline-none focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-900 sm:text-sm"
                        :required="required"
                        @change="change">
                        <option value="recurring_day">day</option>
                        <option value="recurring_month">month</option>
                        <option value="recurring_year">year</option>
                      </select>

                      <span>after the next occurence of the date.</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex justify-between p-5">
              <pretty-span :text="$t('app.cancel')" :classes="'mr-3'" @click="editedReminderId = 0" />
              <pretty-button :text="$t('app.save')" :state="loadingState" :icon="'check'" :classes="'save'" />
            </div>
          </form>
        </li>
      </ul>
    </div>

    <!-- blank state -->
    <div
      v-if="localReminders.length == 0"
      class="mb-6 rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
      <img src="/img/dashboard_blank_reminders.svg" :alt="$t('Reminders')" class="mx-auto mt-4 h-14 w-14" />
      <p class="px-5 pb-5 pt-2 text-center">There are no reminders yet.</p>
    </div>
  </div>
</template>

<script>
import PrettyButton from '@/Shared/Form/PrettyButton.vue';
import PrettySpan from '@/Shared/Form/PrettySpan.vue';
import TextInput from '@/Shared/Form/TextInput.vue';
import Dropdown from '@/Shared/Form/Dropdown.vue';
import Errors from '@/Shared/Form/Errors.vue';

export default {
  components: {
    PrettyButton,
    PrettySpan,
    TextInput,
    Dropdown,
    Errors,
  },

  props: {
    data: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
      loadingState: '',
      titleFieldShown: false,
      emotionFieldShown: false,
      addReminderModalShown: false,
      localReminders: [],
      editedReminderId: 0,
      form: {
        label: '',
        reminderChoice: '',
        day: '',
        month: '',
        choice: '',
        date: '',
        frequencyType: '',
        frequencyNumber: 0,
        errors: [],
      },
    };
  },

  created() {
    this.localReminders = this.data.reminders;
  },

  methods: {
    showCreateReminderModal() {
      this.form.errors = [];
      this.form.label = '';
      this.form.choice = 'full_date';
      this.form.day = '';
      this.form.month = '';
      this.form.reminderChoice = 'recurring';
      this.form.date = '';
      this.form.frequencyType = 'recurring_year';
      this.form.frequencyNumber = 1;
      this.addReminderModalShown = true;

      this.$nextTick(() => {
        this.$refs.label.focus();
      });
    },

    showEditReminderModal(reminder) {
      this.form.errors = [];
      this.editedReminderId = reminder.id;
      this.form.label = reminder.label;
      this.form.day = reminder.day;
      this.form.month = reminder.month;
      this.form.date = reminder.date;
      this.form.reminderChoice = reminder.reminder_choice;
      this.form.frequencyNumber = reminder.frequency_number;
      this.form.frequencyType = reminder.type;
      this.form.choice = reminder.choice;
    },

    submit() {
      this.loadingState = 'loading';

      axios
        .post(this.data.url.store, this.form)
        .then((response) => {
          this.flash('The reminder has been created', 'success');
          this.localReminders.unshift(response.data.data);
          this.loadingState = '';
          this.addReminderModalShown = false;
        })
        .catch((error) => {
          this.loadingState = '';
          this.form.errors = error.response.data;
        });
    },

    update(reminder) {
      this.loadingState = 'loading';

      axios
        .put(reminder.url.update, this.form)
        .then((response) => {
          this.loadingState = '';
          this.flash('The reminder has been edited', 'success');
          this.localReminders[this.localReminders.findIndex((x) => x.id === reminder.id)] = response.data.data;
          this.editedReminderId = 0;
        })
        .catch((error) => {
          this.loadingState = '';
          this.form.errors = error.response.data;
        });
    },

    destroy(reminder) {
      if (confirm('Are you sure? This will delete the reminder permanently.')) {
        axios
          .delete(reminder.url.destroy)
          .then(() => {
            this.flash('The reminder has been deleted', 'success');
            var id = this.localReminders.findIndex((x) => x.id === reminder.id);
            this.localReminders.splice(id, 1);
          })
          .catch((error) => {
            this.loadingState = null;
            this.form.errors = error.response.data;
          });
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.icon-sidebar {
  color: #737e8d;
  top: -2px;
}

.item-list {
  &:hover:first-child {
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
  }

  &:last-child {
    border-bottom: 0;
  }

  &:hover:last-child {
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
  }
}

select {
  padding-left: 8px;
  padding-right: 20px;
  background-position: right 3px center;
}
</style>
