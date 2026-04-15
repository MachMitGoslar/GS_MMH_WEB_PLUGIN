<script>
export default {
  data() {
    return {
      isOpen: false,
      displayIndex: 1,
    };
  },

  computed: {
    questionField() {
      return this.field('question') || this.field('summary') || {};
    },

    answerField() {
      return this.field('answer') || this.field('details') || {};
    },

    questionValue() {
      return this.content.question || this.content.summary || '';
    },

    answerValue() {
      return this.content.answer || this.content.details || '';
    },
  },
  mounted() {
    this.$nextTick(() => {
      this.updateDisplayIndex();
      this.resizeAnswerInput();
    });
  },
  updated() {
    this.$nextTick(() => {
      this.updateDisplayIndex();
      this.resizeAnswerInput();
    });
  },

  methods: {
    stripHtml(value) {
      return String(value || '')
        .replace(/<[^>]*>/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
    },

    updateQuestion(value) {
      this.update({
        [this.content.question !== undefined ? 'question' : 'summary']: value,
      });
    },

    updateAnswer(value) {
      this.update({
        [this.content.answer !== undefined ? 'answer' : 'details']: value,
      });
    },
    updateDisplayIndex() {
      const container = this.$el?.closest('.k-block-container-type-accordion');

      if (!container || !container.parentElement) {
        this.displayIndex = 1;
        return;
      }

      const siblings = Array.from(container.parentElement.children).filter((element) =>
        element.classList.contains('k-block-container-type-accordion')
      );

      const index = siblings.indexOf(container);
      this.displayIndex = index >= 0 ? index + 1 : 1;
    },
    resizeAnswerInput() {
      const element = this.$el?.querySelector('.k-block-type-accordion-answer-input');

      if (!element) {
        return;
      }

      element.style.height = 'auto';
      element.style.height = `${element.scrollHeight}px`;
    },
  },
};
</script>
<template>
  <div class="k-block-type-accordion" @dblclick="open">

    <div class="k-block-type-accordion-item k-block-faq-item" :class="{ 'is-open': isOpen }">

      <!-- HEADER -->
      <button
        type="button"
        class="k-block-type-accordion-header k-block-faq-toggle"
        @click="isOpen = !isOpen"
      >

        <div class="k-block-faq-index">
          {{ displayIndex }}
        </div>

        <div class="k-block-type-accordion-question k-block-faq-preview">
          <div>{{ stripHtml(questionValue) || 'Frage eingeben…' }}</div>
        </div>

        <k-icon
          class="k-icon k-block-faq-icon"
          type="angle-down"
        />

      </button>

      <!-- ANSWER -->
      <div v-show="isOpen" class="k-block-type-accordion-body k-block-faq-body">
        <textarea
          class="k-block-type-accordion-answer-input k-block-faq-answer-input"
          rows="1"
          :placeholder="answerField.placeholder || 'Antwort eingeben…'"
          :value="stripHtml(answerValue)"
          @input="updateAnswer($event.target.value); resizeAnswerInput()"
        ></textarea>

      </div>

    </div>

  </div>
</template>
<style>
.k-block-type-accordion-item {
  border: 1px solid var(--color-border);
  border-radius: 6px;
  background: var(--color-white);
  overflow: hidden;
}

/* HEADER */
.k-block-type-accordion-header {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.75rem;
  background: var(--color-gray-100);
  cursor: pointer;
  border: 0;
}

/* QUESTION WRAPPER */
.k-block-type-accordion-question {
  flex: 1;
  min-width: 0;
}

.k-block-faq-icon {
  margin-left: auto;
  align-self: center;
  flex-shrink: 0;
}

/* 🔥 CRITICAL FIX: REMOVE ALL CLIPPING */
.k-block-type-accordion-question .k-writer,
.k-block-type-accordion-question .ProseMirror {
  width: 100%;
  white-space: normal !important;
  overflow: visible !important;
  text-overflow: unset !important;
}

/* BODY */
.k-block-type-accordion-body {
  padding: 0.75rem;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  background: var(--color-background);
  color: var(--color-text);
}

.k-block-type-accordion-question-input,
.k-block-type-accordion-answer-input {
  color: var(--color-text) !important;
  -webkit-text-fill-color: var(--color-text);
  background: var(--color-background);
  width: 100%;
  border: 1px solid var(--color-border);
  border-radius: 0.375rem;
  padding: 0.625rem 0.75rem;
  font: inherit;
}

.k-block-type-accordion-answer-input {
  min-height: 2.5rem;
  overflow: hidden;
  resize: none;
  border: 0;
  box-shadow: none;
  background: transparent;
  padding: 0;
  line-height: 1.5;
}

.k-block-type-accordion-question-input {
  border: 0;
  box-shadow: none;
  background: transparent;
  padding: 0;
}

</style>
