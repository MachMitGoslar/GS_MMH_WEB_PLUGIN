<script>
export default {
  data() {
    return {
      isOpen: false
    };
  },

  computed: {
    questionField() {
      return this.field('question') || this.field('summary') || {};
    },

    answerField() {
      return this.field('answer') || this.field('details') || {};
    }
  }
};
</script>
<template>
  <div class="k-block-type-accordion" @dblclick="open">

    <div class="k-block-type-accordion-item" :class="{ 'is-open': isOpen }">

      <!-- HEADER -->
      <button
        type="button"
        class="k-block-type-accordion-header"
        @click="isOpen = !isOpen"
      >

        <k-icon
          class="k-block-type-accordion-icon"
          :type="isOpen ? 'angle-down' : 'angle-right'"
        />

        <div class="k-block-type-accordion-question">

          <div>{{ content.summary }}</div>

        </div>

      </button>

      <!-- ANSWER -->
      <div v-show="isOpen" class="k-block-type-accordion-body">

        <div>{{ content.details }}</div>


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
}

/* ICON */
.k-block-type-accordion-icon {
  transition: transform 0.2s ease;
  opacity: 0.6;
}

.k-block-type-accordion-item.is-open .k-block-type-accordion-icon {
  transform: rotate(90deg);
}
</style>