<script>
    export default {
        computed: {
          items() {
            return this.content.faq || {};
          },
          headingField() {
            return this.field("heading") || '';
          }
        },
        methods: {
          updateItem(content, index, name, value) {
            console.log(value)
            content.faq[index].content[name]= value;
            this.$emit("update", {
                ...this.content,
                ...content
              });
          }
        },
    }
</script>

<template>
<div @dblclick="open">
            <h2 class="k-block-type-faq-heading">
              <k-writer
                ref="heading"
                :inline="headingField.inline"
                :marks="headingField.marks"
                :placeholder="headingField.placeholder || 'Add a heading'"
                :value="content.heading"
                @input="update({ heading: $event })"
              />
            </h2>
            <div v-if="content.faq.length">
              <details
                class="k-block-type-faq-item"
                v-for="(item, index) in items"
                :key="index"
              >
              <summary>
                <k-writer
                  ref="summary"
                  :inline="true"
                  :marks="false"
                  :value="item.content.summary"
                  @input="updateItem(content, index, 'summary', $event)"
                />
              </summary>
              <div>
                <k-writer
                  ref="details"
                  :marks="true"
                  :value="item.content.details"
                  @input="updateItem(content, index, 'details', $event)"
              />
              </div>
              </details>
            </div>
            <div v-else>No items yet</div>
          </div>
</template>