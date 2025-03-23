<script>
    export default {
        computed: {
          linkTextField() {
            return this.field("linktext");
          },
          typeFields() {
            let type = this.field("buttonType")
            return type
          }
        },
        watch: {
          "linkTextField": {
            handler(value) {
              console.log("Hallo:", value)
            }
          }
        },
    }
</script>

<template>
    <div @dblclick="open">
            <k-button :class="['k-panel-button']" 
              :data-style="typeField.style || 'pill'"
              :data-type="typeField.color || 'primary'"
              > 
              <k-writer 
                ref="link_text"
                :inline="true "
                :marks="false"
                :value="content.linktext"
                :placeholder="'Call to Action Text'"
                @input="update({linkTextField: $event})"
              />
            </k-button>

          </div>
</template>