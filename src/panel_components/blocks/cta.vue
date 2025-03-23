<script>
    export default {
        computed: {
          alignment() {
            switch (this.content.alignment) {
              case 'left':
                return 'start';
                break;
              case 'right':
                return 'end';
                break;
              default:
                return "center"
                break;
            }
          },  
          descriptionField() {
            return this.field("description");
          },
          titleField() {
            return this.field("title");
          },
          buttonItems() {
            return this.content.buttons || [];
          },
          typeFields() {
            let type = this.content.button.buttonType
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
        methods: {
          updateItem(content, index, name, value) {
            content.buttons[index].content[name]= value;
            this.$emit("update", {
                ...this.content,
                ...content
              });
          }
        }
    }
</script>

<template>
    <div @dblclick="open" class="k-panel-cta" :data-position="content.alignment">
            <k-writer
                  ref="title"
                  :inline="titleField.inline || false"
                  :marks="false"
                  :placeholder="titleField.placeholder || 'Add a Title'"
                  :value="content.title"
                  :nodes="titleField.nodes || false"
                  :style="'text-align:'+content.alignment"
                  @input="update({ title: $event })"
                />
            <k-writer
                  ref="description"
                  :inline="descriptionField.inline || true"
                  :marks="descriptionField.marks"
                  :style="'text-align:'+content.alignment"
                  :value="content.description"
                  :placeholder="descriptionField.placeholder || 'Add some description'"
                  @input="update({ description: $event })"
                />
            <div v-if="buttonItems.length > 0"
              :style="'align-self:'+ alignment"
              >
              <button v-for="(button, index) of buttonItems" :class="['k-panel-button']" 
                :data-style="button.content.buttontype.style || 'pill'"
                :data-type="button.content.buttontype.color || 'primary'"
                > 
                <k-writer 
                  ref="link_text"
                  :inline="true "
                  :marks="false"
                  :value="button.content.linktext"
                  :placeholder="'Call to Action Text'"
                  @input="updateItem(content, index, 'linktext', $event )"
                />
              </button>
            </div>

          </div>
</template>