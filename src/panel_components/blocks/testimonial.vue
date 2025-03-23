<script>
export default {
    computed: {
          image() {
            return this.content.image[0] || {};
          },
          bio() {
            return [this.content.jobposition, this.content.company].filter(el => {
              return el != null && el != '';
            }).join(', ');
          },
          quoteField() {
            return this.field("quote");
          }
        },
}
</script>

<template>
<blockquote class="k-block-type-testimonial-quote" @dblclick="open">
            <k-writer
              ref="quote"
              :inline="true"
              :marks="false"
              :value="content.quote"
              :placeholder="quoteField.placeholder"
              @input="update({ quote: $event })"
            />
            <footer>
              <figure class="k-block-type-testimonial-voice">
                <img
                  v-if="image.url"
                  :src="image.url"
                  width="48px"
                  height="48px"
                  :alt="'Photo of ' + content.name"
                >
                <figcaption>
                  {{content.name }}<br>
                  {{ bio }}
                 </figcaption>
              </figure>
            </footer>
          </blockquote>
</template>