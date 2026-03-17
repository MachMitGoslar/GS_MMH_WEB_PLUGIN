<script>
export default {
  computed: {
    items() {
      return this.content.faq || {};
    },
    headingField() {
      return this.field('heading') || '';
    },
    headingText() {
      const raw = this.content.heading || '';
      return String(raw).replace(/<[^>]*>/g, '').trim();
    },
    headingTitle() {
      return this.headingText || this.headingField.placeholder || 'FAQ';
    },
  },
  methods: {
    updateItem(content, index, name, value) {
      console.log(value);
      content.faq[index].content[name] = value;
      this.$emit('update', {
        ...this.content,
        ...content,
      });
    },
  },
};
</script>

<template>
  <div @dblclick="open">
    <header class="k-block-header">
      <h3
        class="k-block-title"
        style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis"
      >
        <k-icon type="question" />
        {{ headingTitle }}
      </h3>
    </header>
    <div v-if="!content.faq.length">No items yet</div>
  </div>
</template>
