<script>
export default {
  data() {
    return {
      openItems: [],
    };
  },
  computed: {
    items() {
      return this.normalizeFaqItems(this.content.faq);
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
  mounted() {
    this.$nextTick(() => {
      this.resizeAnswerInputs();
    });
  },
  updated() {
    this.$nextTick(() => {
      this.resizeAnswerInputs();
    });
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
    normalizeFaqItems(value) {
      if (Array.isArray(value)) {
        return value;
      }

      if (value && typeof value === 'object') {
        if (Array.isArray(value.blocks)) {
          return value.blocks;
        }

        if (Array.isArray(value.value)) {
          return value.value;
        }
      }

      if (typeof value === 'string' && value.trim() !== '') {
        try {
          const parsed = JSON.parse(value);
          return this.normalizeFaqItems(parsed);
        } catch (error) {
          return [];
        }
      }

      return [];
    },
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
    questionRawValue(item) {
      if (!item || !item.content) {
        return '';
      }

      return item.content.question || item.content.summary || '';
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
    resizeTextarea(element) {
      if (!element) {
        return;
      }

      element.style.height = 'auto';
      element.style.height = `${element.scrollHeight}px`;
    },
    resizeAnswerInputs() {
      const elements = this.$el?.querySelectorAll('.k-block-faq-answer-input') || [];
      elements.forEach((element) => this.resizeTextarea(element));
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

            <k-icon type="angle-down" class="k-block-faq-icon" />
            

          </button>

          <!-- EXPANDED -->
          <div v-if="isOpen(index)" class="k-block-faq-body">

            <textarea
              class="k-block-faq-answer-input"
              rows="1"
              placeholder="Antwort eingeben…"
              :value="stripHtml(answerValue(item))"
              @input="updateItem(index, item.content.answer !== undefined ? 'answer' : 'details', $event.target.value); resizeTextarea($event.target)"
            ></textarea>

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
  border: 1px solid var(--color-border);
  border-radius: 6px;
  background: var(--color-white);
  overflow: hidden;
}

/* TOGGLE */
.k-block-faq-toggle {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.75rem;
  border: 0;
  background: var(--color-gray-100);
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
  margin-left: auto;
  align-self: center;
  flex-shrink: 0;
  transition: transform 0.2s ease;
  opacity: 0.6;
}


/* EXPANDED AREA */
.k-block-faq-body {
  padding: 0.75rem;
  border-top: 1px solid var(--color-border);
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  background: var(--color-background);
  color: var(--color-text);
}

.k-block-faq-question-input,
.k-block-faq-answer-input {
  color: var(--color-text) !important;
  -webkit-text-fill-color: var(--color-text);
  background: var(--color-background);
  width: 100%;
  border: 1px solid var(--color-border);
  border-radius: 0.375rem;
  padding: 0.625rem 0.75rem;
  font: inherit;
}

.k-block-faq-answer-input {
  min-height: 2.5rem;
  overflow: hidden;
  resize: none;
  border: 0;
  box-shadow: none;
  background: transparent;
  padding: 0;
  line-height: 1.5;
}

.k-block-faq-question-input {
  border: 0;
  box-shadow: none;
  background: transparent;
  padding: 0;
}
</style>
