<script>
export default {
  props: {
    content: Object,
    endpoints: Object,
    fieldset: Object,
  },
  methods: {
    open() {
      this.$emit('open');
    },
  },
};
</script>

<template>
  <div @dblclick="open">
    <div
      class="gs-c-btn"
      :data-style="(content.buttontype && content.buttontype.style) || 'pill'"
      :data-type="(content.buttontype && content.buttontype.color) || 'primary'"
      :data-size="
        (content.buttontype && content.buttontype.size) === 'normal'
          ? 'regular'
          : (content.buttontype && content.buttontype.size) || 'regular'
      "
    >
      {{ content.linktext || 'Click here' }}
    </div>
  </div>
</template>

<style scoped>
/* Button styles from main design system for Kirby panel preview */

.gs-c-btn {
  /* Background */
  --_background: var(--color-ripe-mango-700, #fcce4cff);

  /* Foreground */
  --_foreground: var(--color-ripe-mango-1400, #433408ff);

  /* Border */
  --_border-width: 1px;
  --_border: var(--_border-width) solid rgb(0 0 0 / 0%);
  --_border-radius: var(--border-radius-pill, 999px);

  /* Font */
  --_font-size: 0.875rem;
  --_font-weight: 600;

  /* Padding */
  --_padding-horizontal: 1rem;
  --_padding-vertical: 0.5rem;
  --_height: 2.25rem;

  text-align: center;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  background: var(--_background);
  color: var(--_foreground);
  border: var(--_border);
  border-radius: var(--_border-radius);
  padding: var(--_padding-vertical) var(--_padding-horizontal);
  font-size: var(--_font-size);
  font-weight: var(--_font-weight);
  height: var(--_height);
  cursor: pointer;
}

.gs-c-btn:hover {
  --_background: var(--color-ripe-mango-300, #feefc3ff);
  --_foreground: var(--color-ripe-mango-1200, #866811ff);
}

.gs-c-btn:focus {
  --_background: var(--color-ripe-mango-200, #fef7e1ff);
  --_foreground: var(--color-ripe-mango-700, #fcce4cff);
}

.gs-c-btn.disabled,
.gs-c-btn[disabled] {
  --_background: var(--color-dead-pixel-25, #f5f5f5ff);
  --_foreground: var(--color-dead-pixel-400, #c6c6c6ff);
  pointer-events: none;
}

/* Type: primary */
.gs-c-btn[data-type='primary'] {
  --_background-primary-enabled: var(--color-ripe-mango-700, #fcce4cff);
  --_background-primary-hover: var(--color-ripe-mango-300, #feefc3ff);
  --_background-primary-pressed: var(--color-ripe-mango-200, #fef7e1ff);
  --_background-primary-disabled: var(--color-dead-pixel-25, #f5f5f5ff);
  --_foreground-primary-enabled: var(--color-ripe-mango-1400, #433408ff);
  --_foreground-primary-hover: var(--color-ripe-mango-1200, #866811ff);
  --_foreground-primary-pressed: var(--color-ripe-mango-700, #fcce4cff);
  --_foreground-primary-disabled: var(--color-dead-pixel-400, #c6c6c6ff);
  --_border-primary-enabled: var(--_border-width) solid rgb(0 0 0 / 0%);
  --_border-primary-hover: var(--_border-width) solid rgb(0 0 0 / 0%);
  --_border-primary-pressed: var(--_border-width) solid rgb(0 0 0 / 0%);
  --_border-primary-disabled: var(--_border-width) solid rgb(0 0 0 / 0%);
  --_background: var(--_background-primary-enabled);
  --_foreground: var(--_foreground-primary-enabled);
}

.gs-c-btn[data-type='primary']:hover {
  --_background: var(--_background-primary-hover);
  --_foreground: var(--_foreground-primary-hover);
}

.gs-c-btn[data-type='primary']:focus {
  --_background: var(--_background-primary-pressed);
  --_foreground: var(--_foreground-primary-pressed);
}

.gs-c-btn[data-type='primary'].disabled,
.gs-c-btn[data-type='primary'][disabled] {
  --_background: var(--_background-primary-disabled);
  --_foreground: var(--_foreground-primary-disabled);
}

/* Type: secondary */
.gs-c-btn[data-type='secondary'] {
  --_background-primary-enabled: none;
  --_background-primary-hover: var(--color-dead-pixel-1200, #3f3f3eff);
  --_background-primary-pressed: none;
  --_background-primary-disabled: none;
  --_foreground-primary-enabled: var(--color-dead-pixel-1200, #3f3f3eff);
  --_foreground-primary-hover: var(--color-dead-pixel-25, #f5f5f5ff);
  --_foreground-primary-pressed: var(--color-dead-pixel-500, #b4b4b4ff);
  --_foreground-primary-disabled: var(--color-dead-pixel-400, #c6c6c6ff);
  --_border-primary-enabled: var(--_border-width) solid var(--color-dead-pixel-1200, #3f3f3eff);
  --_border-primary-hover: var(--_border-width) solid var(--color-dead-pixel-1200, #3f3f3eff);
  --_border-primary-pressed: var(--_border-width) solid var(--color-dead-pixel-400, #c6c6c6ff);
  --_border-primary-disabled: var(--_border-width) solid var(--color-dead-pixel-300, #d9d9d9ff);
  --_background: var(--_background-primary-enabled);
  --_foreground: var(--_foreground-primary-enabled);
  --_border: var(--_border-primary-enabled);
}

.gs-c-btn[data-type='secondary']:hover {
  --_background: var(--_background-primary-hover);
  --_foreground: var(--_foreground-primary-hover);
  --_border: var(--_border-primary-hover);
}

.gs-c-btn[data-type='secondary']:focus {
  --_background: var(--_background-primary-pressed);
  --_foreground: var(--_foreground-primary-pressed);
  --_border: var(--_border-primary-pressed);
}

.gs-c-btn[data-type='secondary'].disabled,
.gs-c-btn[data-type='secondary'][disabled] {
  --_background: var(--_background-primary-disabled);
  --_foreground: var(--_foreground-primary-disabled);
  --_border: var(--_border-primary-disabled);
}

/* Type: tertiary */
.gs-c-btn[data-type='tertiary'] {
  --_background: none;
  --_border: none;
  --_foreground: var(--color-ripe-mango-1200, #866811ff);
}

.gs-c-btn[data-type='tertiary']:hover {
  text-decoration-line: underline;
}

/* Style: pill */
.gs-c-btn[data-style='pill'] {
  border-radius: var(--border-radius-pill, 999px);
}

/* Style: rounded-corners */
.gs-c-btn[data-style='rounded-corners'] {
  border-radius: var(--border-radius-3, 8px);
}

/* Style: square */
.gs-c-btn[data-style='square'] {
  border-radius: var(--border-radius-0, 0);
}

/* Size: small */
.gs-c-btn[data-size='small'] {
  --_height: 1.75rem;
  --_font-size: 0.75rem;
}

/* Size: regular */
.gs-c-btn[data-size='regular'] {
  --_height: 2.25rem;
  --_font-size: 0.875rem;
}

/* Size: large */
.gs-c-btn[data-size='large'] {
  --_height: 2.75rem;
  --_font-size: 1rem;
}
</style>
