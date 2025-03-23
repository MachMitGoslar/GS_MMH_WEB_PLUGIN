<script>
export default {
  data() {
    return {
      text: "No text value"
    };
  },
  computed: {
    cardType() {
      return this.content.cardtype;
    },
    headline() {
      return (this.cardType === 'manual') ? this.content.headline : this.page.text;
    },

    image() {
      if (this.cardType === 'manual') {
        return this.content.image[0] || {};
      } else {
        return this.page.image || {}
      }
    },
    description_content() {
      console.log(this.content.description_content);
      return this.content.description_content
    },
    pageId() {
      return this.page ? this.page.id : '';
    },
    page() {
      return this.content.page[0] || {};
    },
  },
  watch: {
    "description_content": {
      handler(value) {
        if (value === 'page' && this.pageId) {
          this.$api.get('pages/' + this.pageId.replaceAll('/', '+')).then(page => {
            this.description_content = page.content.text.replace(/(<([^>]+)>)/gi, "") || this.text;
          });
        } else if (value === 'manual') {
          this.description_content = this.content.description_content || this.description_content;
        }
      },
      immediate: true
    },
    "cardType": {
      handler(value) {
        if (value === 'page' && this.pageId) {
          this.$api.get('pages/' + this.pageId.replaceAll('/', '+')).then(page => {
            this.description_content = page.content.text.replace(/(<([^>]+)>)/gi, "") || this.description_content;
          });
        } else if (value === 'manual') {
          this.description_content = this.content.description_content || this.description_content;
        }

      },
      immediate: true
    },
    "page": {
      handler(value) {
        if (this.cardType === 'page' && this.pageId) {
          this.$api.get('pages/' + this.pageId.replaceAll('/', '+')).then(page => {
            this.description_content = page.content.text.replace(/(<([^>]+)>)/gi, "") || this.description_content;
          });
        } else if (value === 'manual') {
          this.text = this.content.description_content || this.text;
        }
      },
      immediate: true
    },
  },
  methods: {
    updateItem(content, index,name, value) {
      content.description_content[index].content[name] = value;
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
    <div class="k-block-type-card k-card">
      <k-frame v-if="image.url" class="hero" cover="true" ratio="1/1">
        <img :src="image.url" alt="" />
      </k-frame>
      <div class="content">
        <k-writer class="k-block-type-card-headline font-headline" 
        :value="headline" 
        :marks="false" 
        :nodes="false"
        :inline="true"
        @input="update({ headline: $event })" />
        
        <k-writer v-if="content.subheadline" class="k-block-type-card-subheadline font-subheadline" 
        :nodes="false"
        :inline="true"
        @input="update({ subheadline: $event })" 
        :value="content.subheadline" 
        :marks="false" />

        <div v-if="typeof (description_content) === 'string'" class="k-block-type-card-text">{{ text }}</div>
        <div v-else>
          <div v-for="(item, index) in description_content">
            <k-block v-if="item.type != 'button'"
              :type="item.type" 
              :content="item.content"
              @update="updateItem(content, index, $event)">
            </k-block>
            <div v-else> 
              <button :class="['k-panel-button']" 
                :data-style="item.content.buttontype.style || 'pill'"
                :data-type="item.content.buttontype.color || 'primary'"
                > 
                <k-writer 
                  ref="link_text"
                  :inline="true "
                  :marks="false"
                  :value="item.content.linktext"
                  :placeholder="'Call to Action Text'"
                  @input="updateItem(content, index, 'linktext', $event )"
                />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

</template>