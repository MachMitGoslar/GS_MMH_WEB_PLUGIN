import Accordion from "./panel_components/blocks/accordion.vue";
import Box from "./panel_components/blocks/box.vue";
import Card from "./panel_components/blocks/card.vue";
import Cta from "./panel_components/blocks/cta.vue";
import Faq from "./panel_components/blocks/faq.vue";
import Testimonial from "./panel_components/blocks/testimonial.vue";
import Title from "./panel_components/nodes/title.vue";
import TitleXL from "./panel_components/nodes/titleXL.vue";
import TitleXXL from "./panel_components/nodes/titleXXL.vue";

panel.plugin("gs-mmh/gs-mmh-web-plugin", {
  blocks: {
    box: Box,
    accordion: Accordion,
    cta: Cta,
    faq: Faq,
    card: Card,
    testimonial: Testimonial
  },

  writerNodes: {
    titleXXL: TitleXXL,
    titleXL: TitleXL,
    title: Title
  }
});

