<script>
export default {
  get button() {
    return {
      icon: 'h6',
      id: 'subheadline',
      label: 'Subheadline',
      when: ['heading', 'paragraph'],
    };
  },

  commands({ type, utils, schema }) {
    return () => utils.toggleBlockType(type, schema.nodes.paragraph, { level: 6 });
  },

  get name() {
    return 'subheadline';
  },

  get schema() {
    return {
      content: 'text*',
      group: 'block',
      defining: false,
      draggable: false,
      priority: 70,
      parseDOM: [
        {
          tag: 'h6',
          attrs: { level: 6 },
        },
      ],
      toDOM(node) {
        return ['h6', { class: 'font-subheadline' }, 0];
      },
    };
  },
};
</script>
