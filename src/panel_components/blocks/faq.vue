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
  <div class="k-block-type-faq" @dblclick="open">
    <header class="k-block-header">
      <h3 class="k-block-title k-block-type-faq-title">
        <k-icon type="question" />
        {{ headingTitle }}
      </h3>
    </header>

    <k-writer
      class="k-block-type-faq-heading"
      :inline="headingField.inline !== false"
      :marks="headingField.marks"
      :placeholder="headingField.placeholder || 'FAQ heading'"
      :value="content.heading"
      @input="updateHeading"
    />

    <div v-if="items.length" class="k-block-type-faq-list">
      <article
        v-for="(item, index) in items"
        :key="item.id || index"
        class="k-block-type-faq-item"
        :class="{ 'is-open': isOpen(index) }"
      >
        <button
          type="button"
          class="k-block-type-faq-toggle"
          @click.stop="toggleItem(index)"
        >
          <span class="k-block-type-faq-index">{{ index + 1 }}</span>
          <span class="k-block-type-faq-preview">
            <span class="k-block-type-faq-question-preview">
              {{ questionValue(item) || 'Frage eingeben…' }}
            </span>
          </span>
          <k-icon class="k-block-type-faq-arrow" type="angle-right" />
        </button>

        <div v-if="isOpen(index)" class="k-block-type-faq-fields">
          <div
            v-if="answerValue(item)"
            class="k-block-type-faq-answer-preview"
            v-html="answerValue(item)"
          ></div>
          <k-writer
            class="k-block-type-faq-answer"
            :inline="false"
            marks="true"
            :placeholder="'Antwort eingeben…'"
            :value="answerValue(item)"
            @input="updateItem(index, item.content.answer !== undefined ? 'answer' : 'details', $event)"
          />
        </div>
      </article>
    </div>

    <div v-else class="k-block-type-faq-empty">
      Noch keine FAQ-Einträge. Per Block-Editor Einträge hinzufügen.
    </div>
  </div>
</template>
