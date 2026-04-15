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
  <div @dblclick="open" class="k-block-type k-block-type-download">
    <div class="k-block-body">

      <!-- Title -->
      <div v-if="titleText" class="k-block-download-title">
        <strong>{{ titleText }}</strong>
      </div>

      <!-- Preview -->
      <div v-if="fileName" class="k-block-download-preview">
        <div class="k-block-download-item">

          <!-- Icon -->
          <div class="k-block-download-icon">
            <k-icon type="download" />
          </div>

          <!-- Content -->
          <div class="k-block-download-content">
            <div class="k-block-download-file">
              {{ fileName }}
            </div>

            <div v-if="descriptionText" class="k-block-download-text">
              {{ descriptionText }}
            </div>
          </div>

        </div>
      </div>

      <!-- Empty -->
      <div v-else class="k-block-empty">
        Keine Datei ausgewählt
      </div>

      <!-- Schedule -->
      <div v-if="scheduleLabel" class="k-block-download-schedule">
        {{ scheduleLabel }}
      </div>

    </div>
  </div>
</template>

<style scoped>
.k-block-download-title {
  margin-bottom: 0.75rem;
  font-size: 1rem;
}

.k-block-download-preview {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.k-block-download-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem;
  background: var(--color-gray-100);
  border-radius: 0.25rem;
  font-size: 0.875rem;
}

.k-block-download-icon {
  font-size: 1rem;
  opacity: 0.7;
}

.k-block-download-content {
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
  min-width: 0;
}

.k-block-download-file {
  font-weight: 600;
  color: var(--color-text);
  word-break: break-all;
}

.k-block-download-text {
  font-size: 0.8rem;
  color: var(--color-text-light);
}

.k-block-download-schedule {
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
