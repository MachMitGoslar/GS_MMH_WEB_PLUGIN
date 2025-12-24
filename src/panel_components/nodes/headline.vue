<script>
export default {
  get button() {
    return {
      icon: 'h5',
      id: 'headline',
      label: 'Headline',
      when: ['heading', 'paragraph'],
    };
  },

  commands({ type, utils, schema }) {
    return () => utils.toggleBlockType(type, schema.nodes.paragraph, { level: 5 });
  },

  get name() {
    return 'headline';
  },

  get schema() {
    return {
      content: 'text*',
      group: 'block',
      defining: false,
      draggable: false,
      priority: 71,
      parseDOM: [
        {
          tag: 'h5',
          attrs: { level: 5 },
        },
      ],
      toDOM(node) {
        return ['h5', { class: 'font-headline' }, 0];
      },
    };
  },
};
</script>
