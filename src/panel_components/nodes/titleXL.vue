<script>
    export default {
        get button() {
          return {
            icon: "h3",
            id: "titleXL",
            label: "Title XL",
            when: ["heading", "paragraph"]
          };
        },
  
        commands({ type, utils, schema }) {
          return () => utils.toggleBlockType(type, schema.nodes.paragraph, { level: 3 });
  
        },
  
        get name() {
          return "titleXL";
        },
  
        get schema() {
          return {
            content: "text*",
            group: "block",
            defining: true,
            draggable: false,
            priority: 70,
            parseDOM: [
              {
                tag: "h3",
                class: "font-titleXL",
              }
            ],
            toDOM: () => ["h3", {class: "font-titleXL"}, 0]
          };
        }
      }
</script>