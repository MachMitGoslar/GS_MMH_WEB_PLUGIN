<template>
  <div @dblclick="open" class="k-block-type k-block-type-timeline">
    <div class="k-block-body">
      <div v-if="content.title" class="k-block-timeline-title">
        <strong>{{ content.title }}</strong>
      </div>

      <div v-if="hasEntries" class="k-block-timeline-preview">
        <div v-for="(entry, index) in displayEntries" :key="index" class="k-block-timeline-item">
          <div class="k-block-timeline-date">{{ entry.year || 'No year' }}</div>
          <div class="k-block-timeline-text">{{ entry.summary || 'No summary' }}</div>
          <div v-if="entry.image?.length" class="k-block-timeline-image">
            <img :src="entry.image[0].url" alt="" />
          </div>
        </div>
        <div v-if="totalEntries > 3" class="k-block-timeline-more">
          ... und {{ totalEntries - 3 }} weitere Einträge
        </div>
      </div>

      <div v-else class="k-block-empty">Noch keine Timeline-Einträge vorhanden</div>
    </div>
  </div>
</template>

<script>
export default {
  computed: {
    entriesArray() {
      // Get entries from content, handle different formats
      if (!this.content || !this.content.entries) {
        return [];
      }

      const entries = this.content.entries;

      // If it's already an array, return it
      if (Array.isArray(entries)) {
        return entries;
      }

      // If it's a string, try to parse it
      if (typeof entries === 'string') {
        try {
          const parsed = JSON.parse(entries);
          return Array.isArray(parsed) ? parsed : [];
        } catch (e) {
          console.warn('Failed to parse timeline entries:', e);
          return [];
        }
      }

      // If it's an object, convert to array
      if (typeof entries === 'object' && entries !== null) {
        return Object.values(entries);
      }

      return [];
    },
    hasEntries() {
      try {
        return this.entriesArray.length > 0;
      } catch (e) {
        console.warn('Error checking timeline entries:', e);
        return false;
      }
    },
    displayEntries() {
      try {
        return this.entriesArray.slice(0, 3);
      } catch (e) {
        console.warn('Error getting display entries:', e);
        return [];
      }
    },
    totalEntries() {
      try {
        return this.entriesArray.length;
      } catch (e) {
        console.warn('Error getting total entries:', e);
        return 0;
      }
    },
  },
  methods: {
    open() {
      this.$emit('open');
    },
  },
};
</script>

<style scoped>
.k-block-timeline-title {
  margin-bottom: 0.75rem;
  font-size: 1rem;
}

.k-block-timeline-preview {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.k-block-timeline-item {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  position: relative;
  min-height: 4.5rem;
  padding: 0.75rem 4.5rem 0.75rem 0.875rem;
  background: var(--color-white);
  border: 1px solid var(--color-border);
  border-radius: 0.75rem;
  box-shadow: 0 2px 8px rgb(0 0 0 / 8%);
  font-size: 0.875rem;
}

.k-block-timeline-date {
  display: block;
  margin-bottom: 0.25rem;
  font-weight: 600;
  color: var(--color-text);
}

.k-block-timeline-text {
  flex: 1;
  color: var(--color-text);
  line-height: 1.45;
}

.k-block-timeline-image {
  position: absolute;
  top: 0.75rem;
  right: 0.75rem;
  width: 3rem;
  height: 3rem;
}
.k-block-timeline-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 999px;
  border: 2px solid var(--color-white);
  box-shadow: 0 2px 6px rgb(0 0 0 / 12%);
}

.k-block-timeline-more {
  font-size: 0.75rem;
  color: var(--color-text-light);
  font-style: italic;
  margin-top: 0.25rem;
}

.k-block-empty {
  color: var(--color-text-light);
  font-style: italic;
  padding: 1rem;
  text-align: center;
  background: var(--color-background);
  border-radius: 0.25rem;
}
</style>
