// Block imports
import Accordion from './panel_components/blocks/accordion.vue';
import Box from './panel_components/blocks/box.vue';
import Button from './panel_components/blocks/button.vue';
import Card from './panel_components/blocks/card.vue';
import Cta from './panel_components/blocks/cta.vue';
import Faq from './panel_components/blocks/faq.vue';
import Testimonial from './panel_components/blocks/testimonial.vue';
import Timeline from './panel_components/blocks/timeline.vue';

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

panel.plugin('gs-mmh/gs-mmh-web-plugin', {
  blocks: {
    accordion: Accordion,
    box: Box,
    button: Button,
    card: Card,
    cta: Cta,
    faq: Faq,
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
    'k-dreamform-db-overview': DreamformDbOverview,
    'k-dreamform-db-form': DreamformDbForm,
  },
});
