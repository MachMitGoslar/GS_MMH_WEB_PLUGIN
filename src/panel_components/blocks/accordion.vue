<script>
export default {
  data() {
    return { isOpen: false };
  },
  computed: {
    questionField() {
      return this.field('question') || this.field('summary');
    },
    answerField() {
      return this.field('answer') || this.field('details');
    },
    questionValue() {
      const raw = this.content.question || this.content.summary || '';
      return String(raw).replace(/<[^>]*>/g, '').trim();
    },
  },
};
</script>

<template>
  <div @dblclick="open">
    <div class="k-block-type-accordion-details" :class="{ 'is-open': isOpen }">
      <div class="k-block-type-accordion-summary" @click="isOpen = !isOpen">
        <k-icon class="k-block-type-accordion-arrow" type="angle-right" />
        <span class="k-block-type-accordion-question">
          {{ questionValue || (questionField.placeholder || 'Add a question…') }}
        </span>
        <k-writer
          ref="question"
          :inline="true"
          marks="false"
          :placeholder="questionField.placeholder || 'Add a question…'"
          :value="questionValue"
          @input="update({ question: $event })"
        />
      </div>
      <k-writer
        ref="answer"
        class="k-block-type-accordion-answer"
        :inline="answerField.inline || false"
        :marks="answerField.marks"
        :value="content.answer || content.details"
        :placeholder="answerField.placeholder || 'Add an answer'"
        @input="update({ answer: $event })"
      />
    </div>
  </div>
</template>
