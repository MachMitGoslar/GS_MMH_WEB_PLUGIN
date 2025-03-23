<script>
    export default {
        get button() {
          return {
            icon: "h2",
            id: "titleXXL",
            label: "Title XXL",
            when: ["heading", "paragraph"]
          };
        },
  
        commands({ type, utils, schema }) {
          return () => utils.toggleBlockType(type, schema.nodes.paragraph);
  
        },
  
        get name() {
          return "titleXXL";
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
                tag: "h2",
                class: "font-titleXXL",
              }
            ],
            toDOM: () => ["h2", {class: "font-titleXXL"}, 0]
          };
        }
      }
</script>