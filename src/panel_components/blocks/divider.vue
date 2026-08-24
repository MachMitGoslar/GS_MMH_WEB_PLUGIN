<script>
export default {
  props: {
    content: Object,
    endpoints: Object,
    fieldset: Object,
  },
  computed: {
    // kirbyup does not emit a stylesheet for this plugin (see kirbyup.config.js),
    // so the preview styling has to be inline
    dividerStyle() {
      const thickness = {
        thin: '1px',
        regular: '2px',
        thick: '4px',
      };
      const spacing = {
        small: '0.75rem',
        regular: '1.5rem',
        large: '3rem',
      };
      const color = {
        light: 'var(--color-gray-300, #d9d9d9)',
        dark: 'var(--color-gray-800, #3f3f3e)',
        brand: 'var(--color-yellow-500, #fbc62e)',
      };

      return {
        width: '100%',
        height: 0,
        border: 0,
        borderTop: [
          thickness[this.content.thickness] || thickness.thin,
          this.content.linestyle || 'solid',
          color[this.content.color] || color.light,
        ].join(' '),
        marginBlock: spacing[this.content.spacing] || spacing.regular,
      };
    },
  },
  methods: {
    open() {
      this.$emit('open');
    },
  },
};
</script>

<template>
  <div @dblclick="open" class="k-block-type-divider">
    <hr class="c-divider" :style="dividerStyle" />
  </div>
</template>
