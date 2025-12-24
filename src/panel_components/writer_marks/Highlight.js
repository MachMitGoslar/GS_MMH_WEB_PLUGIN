export default {
  get button() {
    return {
      icon: "highlight",
      label: "Highlight"
    };
  },

  commands() {
    return {
      highlight: (event) => {
        if (event.altKey || event.metaKey) {
          return this.remove();
        }
        return this.toggle();
      }
    };
  },

  get name() {
    return "highlight";
  },

  get schema() {
    return {
      parseDOM: [
        {
          tag: "mark"
        },
        {
          tag: "span.highlight"
        }
      ],
      toDOM() {
        return ["mark", { 
          style: "background-color: #FEEFC3; color: #222222; padding: 0.125rem 0.25rem; border-radius: 0.25rem; font-weight: 500; margin: 0 1px;"
        }, 0];
      }
    };
  },

  toggle() {
    return this.editor.toggleMark(this.name);
  },

  remove() {
    this.editor.removeMark(this.name);
  },

  update(attrs) {
    this.editor.updateMark(this.name, attrs);
  }
}