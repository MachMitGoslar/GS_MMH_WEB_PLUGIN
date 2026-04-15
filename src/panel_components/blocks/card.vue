<script>
export default {
  data() {
    return {
      text: '',
      pagePreviewText: '',
      pagePreviewHeadline: '',
      imagePreviewUrl: '',
    };
  },
  computed: {
    cardType() {
      return this.content.cardtype;
    },
    headline() {
      if (this.cardType === 'manual') {
        return this.content.headline;
      }
      return this.pagePreviewHeadline || this.page.text || this.page.title || '';
    },
    headline_html() {
      const value = this.headline || '';
      return String(value).trim();
    },
    subheadline_html() {
      const value = this.content.subheadline || '';
      return String(value).trim();
    },

    image() {
      if (this.cardType === 'manual') {
        return this.content.image[0] || {};
      } else {
        return this.page.image || {};
      }
    },
    description_content() {
      const value = this.content.description_content;
      const parsed = this.parseBlocks(value);
      return parsed || value;
    },
    description_blocks() {
      const parsed = this.parseBlocks(this.content.description_content);
      return Array.isArray(parsed) ? parsed : [];
    },
    description_text() {
      if (this.cardType === 'page') {
        return this.pagePreviewText || '';
      }
      if (this.description_blocks.length) {
        const parts = [];
        this.description_blocks.forEach(block => {
          const content = block && block.content ? block.content : {};
          ['text', 'heading', 'summary', 'question', 'title', 'caption'].forEach(key => {
            if (typeof content[key] === 'string') {
              parts.push(this.stripHtml(content[key]));
            }
          });
        });
        return parts.join(' ').trim();
      }
      const value = this.content.description_content;
      if (typeof value !== 'string') {
        return '';
      }
      return this.stripHtml(value);
    },
    manual_content_html() {
      if (this.cardType !== 'manual' || !this.description_blocks.length) {
        return '';
      }
      const parts = [];
      this.description_blocks.forEach(block => {
        if (!block || !block.type) {
          return;
        }
        const content = block.content || {};
        if (block.type === 'heading') {
          const level = content.level || 'h3';
          const text = content.text || '';
          parts.push(`<${level}>${text}</${level}>`);
          return;
        }
        if (block.type === 'button') {
          const label = content.linktext || content.text || 'Button';
          parts.push(`<span class="k-block-type-card-button">${label}</span>`);
          return;
        }
        if (content.text) {
          parts.push(content.text);
          return;
        }
        if (content.quote) {
          parts.push(`<blockquote>${content.quote}</blockquote>`);
          return;
        }
      });
      return parts.join('');
    },
    pageId() {
      return this.page ? this.page.id : '';
    },
    page() {
      return this.content.page[0] || {};
    },
  },
  watch: {
    cardType: {
      handler(value) {
        if (value === 'page') {
          this.loadPagePreview();
        }
      },
      immediate: true,
    },
    page: {
      handler() {
        if (this.cardType === 'page') {
          this.loadPagePreview();
        }
      },
      immediate: true,
    },
    image: {
      handler() {
        this.loadImagePreview();
      },
      immediate: true,
    },
  },
  methods: {
    loadPagePreview() {
      if (!this.pageId) {
        this.pagePreviewText = '';
        this.pagePreviewHeadline = '';
        return;
      }
      this.$api.get('pages/' + this.pageId.replaceAll('/', '+')).then(page => {
        const content = page && page.content ? page.content : {};
        const title = page && page.title ? page.title : '';
        const headline = content.headline || title || '';
        const textValue = content.text || '';
        this.pagePreviewHeadline = this.stripHtml(headline);
        this.pagePreviewText = this.stripHtml(textValue);
      });
    },
    loadImagePreview() {
      if (this.cardType !== 'manual') {
        this.imagePreviewUrl = this.image && this.image.url ? this.image.url : '';
        return;
      }
      if (this.image && this.image.url) {
        this.imagePreviewUrl = this.image.url;
        return;
      }
      const id = this.image && (this.image.id || this.image.uuid);
      if (!id) {
        this.imagePreviewUrl = '';
        return;
      }
      this.$api
        .get('files/' + String(id).replaceAll('/', '+'))
        .then(file => {
          this.imagePreviewUrl = (file && file.url) || '';
        })
        .catch(() => {
          this.imagePreviewUrl = '';
        });
    },
    parseBlocks(value) {
      if (Array.isArray(value)) {
        return value;
      }
      if (typeof value !== 'string') {
        return null;
      }
      try {
        const parsed = JSON.parse(value);
        return Array.isArray(parsed) ? parsed : null;
      } catch (error) {
        return null;
      }
    },
    stripHtml(value) {
      return String(value)
        .replace(/<[^>]*>/g, '')
        .replace(/\s+/g, ' ')
        .trim();
    },
    updateItem(content, index, name, value) {
      content.description_content[index].content[name] = value;
      this.$emit('update', {
        ...this.content,
        ...content,
      });
    },
    updateBlock(index, value) {
      const blocks = this.parseBlocks(this.content.description_content) || [];
      if (!blocks[index]) {
        return;
      }
      blocks[index] = value;
      this.$emit('update', {
        ...this.content,
        description_content: JSON.stringify(blocks),
      });
    },
  },
};
</script>

<template>
  <div @dblclick="open" class="k-block-type k-block-type-card">
    <div class="k-block-body">

      <!-- IMAGE -->
      <div v-if="imagePreviewUrl" class="k-block-card-image">
        <k-frame cover ratio="1/1">
          <img :src="imagePreviewUrl" alt="" />
        </k-frame>
      </div>

      <!-- ICON fallback -->
      <div v-else class="k-block-card-image-placeholder">
        <k-icon type="info-card" />
      </div>

      <!-- CONTENT -->
      <div class="k-block-card-content">

        <!-- HEADLINE -->
        <div
          v-if="headline_html"
          class="k-block-card-headline"
          v-html="headline_html"
        ></div>

        <!-- SUBHEADLINE -->
        <div
          v-if="subheadline_html"
          class="k-block-card-subheadline"
          v-html="subheadline_html"
        ></div>

        <!-- TEXT -->
        <div v-if="manual_content_html" class="k-block-card-text" v-html="manual_content_html"></div>

        <div v-else class="k-block-card-text">
          {{ description_text || text }}
        </div>

      </div>

    </div>
  </div>
</template>
<style>
.k-block-card-image {
  margin-bottom: 0.5rem;
  border-radius: 0.25rem;
  overflow: hidden;
}

.k-block-card-image-placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 6rem;
  background: var(--color-gray-100);
  border-radius: 0.25rem;
  margin-bottom: 0.5rem;
  color: var(--color-text-light);
}

.k-block-card-content {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.k-block-card-headline {
  font-weight: 600;
  font-size: 1rem;
  line-height: 1.2;
}

.k-block-card-subheadline {
  font-size: 0.875rem;
  color: var(--color-text-light);
}

.k-block-card-text {
  font-size: 0.875rem;
  color: var(--color-text);
  opacity: 0.85;
}

</style>
