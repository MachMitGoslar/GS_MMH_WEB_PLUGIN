export default {
  get button() {
    return {
      icon: "text",
      label: "Footnote"
    };
  },

  commands() {
    return {
      footnote: (event) => {
        if (event.altKey || event.metaKey) {
          return this.remove();
        }
        return this.toggle();
      }
    };
  },

  get name() {
    return "footnote";
  },

  get schema() {
    return {
      parseDOM: [
        {
          tag: "small"
        },
        {
          tag: "span.font-footnote"
        }
      ],
      toDOM() {
        return ["small", { 
          class: "font-footnote",
          style: "font-size: 0.875rem; font-weight: 400; color: #666666; margin: 0 1px;"
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
}export default {
  get button() {
    return {
      icon: 'text',
      label: 'Footnote',
    };
  },

  commands() {
    return {
      footnote: event => {
        if (event.altKey || event.metaKey) {
          return this.remove();
        }
        return this.toggle();
      },
    };
  },

  get name() {
    return 'footnote';
  },

  get schema() {
    return {
      parseDOM: [
        {
          tag: 'small',
        },
        {
          tag: 'span.font-footnote',
        },
      ],
      toDOM() {
        return [
          'small',
          {
            class: 'font-footnote',
            style: 'font-size: 0.875rem; font-weight: 400; color: #666666; margin: 0 1px;',
          },
          0,
        ];
      },
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
  },
};
