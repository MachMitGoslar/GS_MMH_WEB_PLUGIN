<template>
  <k-section label="Link-Vorschau">
    <k-loader v-if="isLoading" />

    <template v-else>
      <figure :style="styles.card">
        <img
          v-if="preview.imageUrl"
          :src="preview.imageUrl"
          :alt="preview.imageAlt"
          :style="styles.image"
          loading="lazy"
        />
        <figcaption :style="styles.body">
          <span :style="styles.domain">{{ domain }}</span>
          <strong :style="styles.title">{{ preview.title }}</strong>
          <span :style="styles.description">{{ preview.description }}</span>
        </figcaption>
      </figure>

      <ul :style="styles.facts">
        <li :style="styles.fact">
          <span>Typ</span>
          <code>{{ preview.type }}</code>
        </li>
        <li :style="styles.fact">
          <span>Suchmaschinen</span>
          <code :style="preview.indexable ? null : styles.negative">
            {{ preview.robots }}
          </code>
        </li>
      </ul>

      <p :style="styles.hint">Die Vorschau zeigt den zuletzt gespeicherten Stand.</p>
    </template>
  </k-section>
</template>

<script>
export default {
  data() {
    return {
      preview: {},
      isLoading: true,
    };
  },
  computed: {
    domain() {
      try {
        return new URL(this.preview.canonical).hostname.replace(/^www\./, '');
      } catch (error) {
        return '';
      }
    },
    // kirbyup does not emit a stylesheet for this plugin (see
    // kirbyup.config.js), so the styling has to be inline
    styles() {
      const dimmed = 'var(--color-text-dimmed, #777)';

      return {
        card: {
          border: '1px solid var(--color-border, #ddd)',
          borderRadius: 'var(--rounded, 4px)',
          overflow: 'hidden',
          background: 'var(--color-white, #fff)',
        },
        image: {
          display: 'block',
          width: '100%',
          aspectRatio: '1200 / 630',
          objectFit: 'cover',
          background: 'var(--color-light, #efefef)',
        },
        body: {
          display: 'flex',
          flexDirection: 'column',
          gap: '0.25rem',
          padding: '0.75rem',
        },
        domain: {
          fontSize: 'var(--text-xs, 0.75rem)',
          color: dimmed,
        },
        title: {
          fontSize: 'var(--text-sm, 0.875rem)',
          lineHeight: '1.3',
        },
        description: {
          fontSize: 'var(--text-xs, 0.75rem)',
          color: dimmed,
          lineHeight: '1.4',
        },
        facts: {
          display: 'flex',
          flexDirection: 'column',
          gap: '0.25rem',
          marginTop: '0.75rem',
        },
        fact: {
          display: 'flex',
          justifyContent: 'space-between',
          gap: '0.5rem',
          fontSize: 'var(--text-xs, 0.75rem)',
          color: dimmed,
        },
        negative: {
          color: 'var(--color-negative, #c82829)',
        },
        hint: {
          marginTop: '0.5rem',
          fontSize: 'var(--text-xs, 0.75rem)',
          color: dimmed,
        },
      };
    },
  },
  created() {
    // Sections receive their computed props through their own API
    // endpoint, not as component props.
    this.load()
      .then(response => {
        this.preview = response.preview ?? {};
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>
