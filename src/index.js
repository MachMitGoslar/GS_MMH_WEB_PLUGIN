// Block imports
import Accordion from './panel_components/blocks/accordion.vue';
import Box from './panel_components/blocks/box.vue';
import Button from './panel_components/blocks/button.vue';
import Card from './panel_components/blocks/card.vue';
import Cta from './panel_components/blocks/cta.vue';
import Download from './panel_components/blocks/download.vue';
import Faq from './panel_components/blocks/faq.vue';
import Testimonial from './panel_components/blocks/testimonial.vue';
import Timeline from './panel_components/blocks/timeline.vue';
import Form from './panel_components/blocks/form.vue';

// Node imports
import Blockquote from './panel_components/nodes/blockquote.vue';
import Headline from './panel_components/nodes/headline.vue';
import Subheadline from './panel_components/nodes/subheadline.vue';
import Title from './panel_components/nodes/title.vue';
import TitleXL from './panel_components/nodes/titleXL.vue';
import TitleXXL from './panel_components/nodes/titleXXL.vue';

// Writer marks imports
import BadgeMark from './panel_components/writer_marks/Badge.js';
import ButtonMark from './panel_components/writer_marks/Button.js';
import FootnoteMark from './panel_components/writer_marks/Footnote.js';
import HighlightMark from './panel_components/writer_marks/Highlight.js';

// View imports
import DreamformDbOverview from './panel_components/views/DreamformDbOverview.vue';
import DreamformDbForm from './panel_components/views/DreamformDbForm.vue';

const Layout = {
  extends: 'k-layout',

  computed: {
    scheduleLabel() {
      const format = new Intl.DateTimeFormat('de-DE', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      });

      const publish = this.attrs?.publish_date
        ? format.format(new Date(String(this.attrs.publish_date).replace(' ', 'T')))
        : null;

      const end = this.attrs?.end_date
        ? format.format(new Date(String(this.attrs.end_date).replace(' ', 'T')))
        : null;

      if (publish && end) return `🕒 ${publish} → ${end}`;
      if (publish) return `🕒 ab ${publish}`;
      if (end) return `🕒 bis ${end}`;
      return null;
    },
  },

  template: `
    <section class="k-layout k-layout-with-schedule" :data-selected="isSelected" tabindex="0" @click="$emit('select')">
      <k-grid class="k-layout-columns">
        <k-layout-column
          v-for="(column, columnIndex) in columns"
          :key="column.id"
          v-bind="{ ...column, endpoints, fieldsetGroups, fieldsets }"
          @input="$emit('updateColumn', { column, columnIndex, blocks: $event })"
        />
      </k-grid>
      <nav v-if="disabled !== true" class="k-layout-toolbar">
        <div v-if="scheduleLabel" class="k-layout-schedule">{{ scheduleLabel }}</div>
        <k-button
          v-if="settings"
          class="k-layout-toolbar-button"
          :title="$t('settings')"
          icon="settings"
          @click="openSettings"
        />
        <k-button
          class="k-layout-toolbar-button"
          icon="angle-down"
          @click="$refs.options.toggle()"
        />
        <k-dropdown-content ref="options" :options="options" align-x="end" />
        <k-sort-handle />
      </nav>
    </section>
  `,
};

const PdfViewButton = {
  props: {
    disabled: Boolean,
    icon: String,
    link: String,
    target: String,
    text: [String, Array],
    title: [String, Array],
  },

  computed: {
    buttonText() {
      if (Array.isArray(this.text)) {
        return this.text[0] || 'PDF';
      }

      return this.text || 'PDF';
    },
    buttonTitle() {
      if (Array.isArray(this.title)) {
        return this.title[0] || this.buttonText;
      }

      return this.title || this.buttonText;
    },
    resolvedLink() {
      return this.link || this.$attrs.link || this.derivePdfLinkFromPanel();
    },
  },

  methods: {
    derivePdfLinkFromPanel() {
      const panelPath = window.panel?.view?.path || '';

      const match = panelPath.match(/^pages\/(.+)$/);

      if (!match) {
        return null;
      }

      const pageId = match[1].replaceAll('+', '/');

      if (!pageId.startsWith('newsletter/')) {
        return null;
      }

      return `${window.location.origin}/${pageId}?pdf=1`;
    },
    handleClick(event) {
      event.preventDefault();
      event.stopPropagation();

      if (this.disabled === true) {
        return;
      }

      if (!this.resolvedLink) {
        return;
      }

      window.open(this.resolvedLink, this.target || '_blank');
    },
  },

  template: `
    <k-button
      class="k-pdf-view-button"
      :icon="icon || 'download'"
      :text="buttonText"
      :title="buttonTitle"
      :disabled="disabled"
      size="sm"
      variant="filled"
      :responsive="false"
      @click="handleClick"
    />
  `,
};

panel.plugin('gs-mmh/gs-mmh-web-plugin', {
  blocks: {
    accordion: Accordion,
    box: Box,
    button: Button,
    card: Card,
    cta: Cta,
    download: Download,
    faq: Faq,
    form: Form,
    testimonial: Testimonial,
    timeline: Timeline,
  },

  writerNodes: {
    blockquote: Blockquote,
    headline: Headline,
    subheadline: Subheadline,
    title: Title,
    titleXL: TitleXL,
    titleXXL: TitleXXL,
  },

  writerMarks: {
    badge: BadgeMark,
    button: ButtonMark,
    footnote: FootnoteMark,
    highlight: HighlightMark,
  },

  components: {
    'k-layout': Layout,
    'k-pdf-view-button': PdfViewButton,
    'k-dreamform-db-overview': DreamformDbOverview,
    'k-dreamform-db-form': DreamformDbForm,
  },
});
