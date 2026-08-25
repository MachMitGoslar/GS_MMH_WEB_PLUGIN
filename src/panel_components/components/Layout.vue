<template>
  <section
    class="k-layout k-layout-with-schedule"
    :data-selected="isSelected"
    tabindex="0"
    @click="$emit('select')"
  >
    <k-grid class="k-layout-columns">
      <k-layout-column
        v-for="(column, columnIndex) in columns"
        :key="column.id"
        v-bind="{ ...column, endpoints, fieldsetGroups, fieldsets }"
        @input="$emit('updateColumn', { column, columnIndex, blocks: $event })"
      />
    </k-grid>
    <nav v-if="disabled !== true" class="k-layout-toolbar">
      <div v-if="scheduleLabel" class="k-layout-schedule">
        {{ scheduleLabel }}
      </div>
      <k-button
        v-if="settings"
        class="k-layout-toolbar-button"
        :title="$t('settings')"
        icon="settings"
        @click="openSettings"
      />
      <k-button class="k-layout-toolbar-button" icon="angle-down" @click="$refs.options.toggle()" />
      <k-dropdown-content ref="options" :options="options" align-x="end" />
      <k-sort-handle />
    </nav>
  </section>
</template>

<script>
export default {
  extends: 'k-layout',

  computed: {
    scheduleLabel() {
      const format = new Intl.DateTimeFormat('de-DE', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      });

      const publish = this.attrs?.publish_date
        ? format.format(new Date(String(this.attrs.publish_date).replace(' ', 'T')))
        : null;

      const end = this.attrs?.end_date
        ? format.format(new Date(String(this.attrs.end_date).replace(' ', 'T')))
        : null;

      if (publish && end) return `🕒 ${publish} → ${end}`;
      if (publish) return `🕒 ab ${publish}`;
      if (end) return `🕒 bis ${end}`;
      return null;
    },
  },
};
</script>
