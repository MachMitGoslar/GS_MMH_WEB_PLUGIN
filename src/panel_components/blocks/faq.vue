<script>
export default {
  data() {
    return {
      openItems: [],
    };
  },
  computed: {
    items() {
      return Array.isArray(this.content.faq) ? this.content.faq : [];
    },
    headingField() {
      return this.field('heading') || {};
    },
    headingText() {
      const raw = this.content.heading || '';
      return this.stripHtml(raw);
    },
    headingTitle() {
      return this.headingText || this.headingField.placeholder || 'FAQ';
    },
  },
  created() {
    this.syncOpenItems();
  },
  watch: {
    items: {
      handler() {
        this.syncOpenItems();
      },
      deep: true,
    },
  },
  methods: {
    stripHtml(value) {
      return String(value || '')
        .replace(/<[^>]*>/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
    },
    questionValue(item) {
      if (!item || !item.content) {
        return '';
      }

      return this.stripHtml(item.content.question || item.content.summary || '');
    },
    answerValue(item) {
      if (!item || !item.content) {
        return '';
      }

      return item.content.answer || item.content.details || '';
    },
    answerPreview(item) {
      const text = this.stripHtml(this.answerValue(item));

      if (text.length <= 140) {
        return text;
      }

      return text.slice(0, 140).trim() + '…';
    },
    syncOpenItems() {
      if (this.openItems.length) {
        return;
      }

      if (this.items.length === 1) {
        this.openItems = [0];
      }
    },
    isOpen(index) {
      return this.openItems.includes(index);
    },
    toggleItem(index) {
      if (this.isOpen(index)) {
        this.openItems = this.openItems.filter(item => item !== index);
        return;
      }

      this.openItems = [...this.openItems, index];
    },
    updateHeading(value) {
      this.update({ heading: value });
    },
    updateItem(index, name, value) {
      const faq = this.items.map(item => ({
        ...item,
        content: { ...(item.content || {}) },
      }));

      if (!faq[index]) {
        return;
      }

      faq[index].content[name] = value;

      this.$emit('update', {
        ...this.content,
        faq,
      });
    },
  },
};
</script>

<template>
  <div class="k-block-type k-block-type-faq" @dblclick="open">

    <div class="k-block-body">

      <!-- HEADER -->
      <div class="k-block-faq-title">
        <k-icon type="question" />
        <strong>{{ headingTitle }}</strong>
      </div>

      <!-- HEADING EDITOR -->
      <k-writer
        class="k-block-faq-heading"
        :inline="headingField.inline !== false"
        :marks="headingField.marks"
        :placeholder="headingField.placeholder || 'FAQ heading'"
        :value="content.heading"
        @input="updateHeading"
      />

      <!-- LIST -->
      <div v-if="items.length" class="k-block-faq-list">

        <article
          v-for="(item, index) in items"
          :key="item.id || index"
          class="k-block-faq-item"
          :class="{ 'is-open': isOpen(index) }"
        >

          <!-- TOGGLE ROW -->
          <button
            type="button"
            class="k-block-faq-toggle"
            @click.stop="toggleItem(index)"
          >

            <div class="k-block-faq-index">
              {{ index + 1 }}
            </div>

            <div class="k-block-faq-preview">
              {{ questionValue(item) || 'Frage eingeben…' }}
            </div>

            <k-icon type="angle-right" class="k-block-faq-icon" />

          </button>

          <!-- EXPANDED -->
          <div v-if="isOpen(index)" class="k-block-faq-body">

            <div
              v-if="answerValue(item)"
              class="k-block-faq-answer-preview"
              v-html="answerValue(item)"
            ></div>

            <k-writer
              class="k-block-faq-answer"
              :inline="false"
              marks="true"
              placeholder="Antwort eingeben…"
              :value="answerValue(item)"
              @input="updateItem(index, item.content.answer !== undefined ? 'answer' : 'details', $event)"
            />

          </div>

        </article>

      </div>

      <!-- EMPTY -->
      <div v-else class="k-block-empty">
        Noch keine FAQ-Einträge
      </div>

    </div>

  </div>
</template>
<style>
.k-block-faq-title {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  margin-bottom: 0.75rem;
}

.k-block-faq-heading {
  margin-bottom: 0.75rem;
}

.k-block-faq-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

/* ITEM CARD */
.k-block-faq-item {
  background: var(--color-gray-100);
  border-radius: 0.25rem;
  overflow: hidden;
}

/* TOGGLE */
.k-block-faq-toggle {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem;
  border: 0;
  background: transparent;
  cursor: pointer;
  font-size: 0.875rem;
}

.k-block-faq-index {
  min-width: 1.5rem;
  font-weight: 600;
  color: var(--color-focus);
}

.k-block-faq-preview {
  flex: 1;
  text-align: left;
  color: var(--color-text);
}

/* ICON */
.k-block-faq-icon {
  transition: transform 0.2s ease;
  opacity: 0.6;
}

.k-block-faq-item.is-open .k-block-faq-icon {
  transform: rotate(90deg);
}

/* EXPANDED AREA */
.k-block-faq-body {
  padding: 0.5rem 0.75rem 0.75rem;
  border-top: 1px solid var(--color-border);
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.k-block-faq-answer-preview {
  font-size: 0.85rem;
  color: var(--color-text-light);
}
</style>
