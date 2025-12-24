<script>
export default {
  get button() {
    return {
      icon: "quote",
      id: "blockquote",
      label: "Quote",
      when: ["paragraph"]
    };
  },

  commands({ type, utils, schema }) {
    return () => utils.toggleBlockType(type, schema.nodes.paragraph);
  },

  get name() {
    return "blockquote";
  },

  get schema() {
    return {
      content: "paragraph+",
      group: "block",
      defining: false,
      draggable: false,
      priority: 69,
      parseDOM: [
        {
          tag: "blockquote"
        }
      ],
      toDOM(node) {
        return ["blockquote", 0];
      }
    };
  }
};
</script>