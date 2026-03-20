<script>
export default {
  props: {
    content: Object,
    endpoints: Object,
    fieldset: Object,
  },
  computed: {
    germanDateTime() {
      return new Intl.DateTimeFormat('de-DE', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      });
    },
    publishDisplay() {
      return this.formatDateTime(this.content.publish_date || '');
    },
    endDisplay() {
      return this.formatDateTime(this.content.end_date || '');
    },
    titleText() {
      return this.content.title || 'Datei Download';
    },
    descriptionText() {
      return this.content.text || '';
    },
    fileName() {
      const file = Array.isArray(this.content.file) ? this.content.file[0] : null;
      if (!file) {
        return '';
      }
      return file.filename || file.name || file.id || '';
    },
    scheduleLabel() {
      const publish = this.publishDisplay;
      const end = this.endDisplay;
      if (publish && end) {
        return `🕒 ${publish} → ${end}`;
      }
      if (publish) {
        return `🕒 ab ${publish}`;
      }
      if (end) {
        return `🕒 bis ${end}`;
      }
      return null;
    },
  },
  methods: {
    formatDateTime(value) {
      if (!value) {
        return '';
      }
      const normalized = String(value).replace(' ', 'T');
      const date = new Date(normalized);
      if (Number.isNaN(date.getTime())) {
        return String(value);
      }
      return this.germanDateTime.format(date);
    },
    open() {
      this.$emit('open');
    },
  },
};
</script>

<template>
  <div class="k-block-type-download" @dblclick="open">
    <div class="k-block-type-download-body">
      <div class="k-block-type-download-title">{{ titleText }}</div>
      <div v-if="descriptionText" class="k-block-type-download-text">{{ descriptionText }}</div>
      <div v-if="fileName" class="k-block-type-download-file">{{ fileName }}</div>
      <div v-if="scheduleLabel" class="k-block-type-download-schedule">
        {{ scheduleLabel }}
      </div>
    </div>
  </div>
</template>

<style scoped>
.k-block-type-download {
  position: relative;
  padding-bottom: 2rem;
}

.k-block-type-download-body {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.k-block-type-download-title {
  font-weight: 600;
}

.k-block-type-download-text,
.k-block-type-download-file {
  font-size: 0.875rem;
  opacity: 0.8;
}

.k-block-type-download-schedule {
  position: absolute;
  right: 0.5rem;
  bottom: 0.2rem;
  font-size: 0.75rem;
  padding: 0.2rem 0.5rem;
  border-radius: 999px;
  background: #1d4ed8;
  color: #ffffff;
}
</style>
