<script>
export default {
    computed: {
          summaryField() {
            return this.field("summary");
          },
          detailsField() {
            return this.field("details");
          }
        },
}
</script>

<template>
    <div @dblclick="open">
            <details>
              <summary>
                <k-writer
                  ref="summary"
                  :inline="true"
                  marks="false"
                  :placeholder="summaryField.placeholder || 'Add a summary…'"
                  :value="content.summary"
                  @input="update({ summary: $event })"
                />
              </summary>
              <k-writer
                  ref="details"
                  :inline="detailsField.inline || false"
                  :marks="detailsField.marks"
                  :value="content.details"
                  :placeholder="detailsField.placeholder || 'Add some details'"
                  @input="update({ details: $event })"
                />
            </details>
          </div>
</template>