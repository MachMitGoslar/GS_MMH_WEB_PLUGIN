<script>
    export default{
        get button() {
          return {
            icon: "h4",
            id: "title",
            label: "Title",
            when: ["heading", "paragraph"]
          };
        },
  
        commands({ type, utils, schema }) {
          return () => utils.toggleBlockType(type, schema.nodes.paragraph, { level: 4 });
  
        },
  
        get name() {
          return "title";
        },
  
        get schema() {
          return {
            content: "text*",
            group: "block",
            defining: false,
            draggable: false,
            priority: 72,
            parseDOM: [
              {
                tag: "h4",
                class: "font-title",
              }
            ],
            toDOM: () => ["h4", {class: "font-title"}, 0]
          };
        }
      }
</script>