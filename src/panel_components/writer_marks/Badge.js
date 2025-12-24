export default {
  get button() {
    return {
      icon: "tag",
      label: "Badge"
    };
  },

  commands() {
    return {
      badge: (event) => {
        if (event.altKey || event.metaKey) {
          return this.remove();
        }
        this.editor.command("openBadgeDialog");
      },
      insertBadge: (attrs = {}) => {
        const { selection } = this.editor.state;
        
        if (
          selection.empty &&
          this.editor.activeMarks.includes("badge") === false
        ) {
          this.editor.insertText(attrs.text || "Badge", true);
        }
        
        if (attrs.text) {
          return this.update(attrs);
        }
      },
      removeBadge: () => {
        return this.remove();
      },
      toggleBadge: (attrs = {}) => {
        if (attrs.text?.length > 0) {
          this.editor.command("insertBadge", attrs);
        } else {
          this.editor.command("removeBadge");
        }
      },
      openBadgeDialog: () => {
        const currentAttrs = this.editor.getMarkAttrs("badge") || {};
        const selection = this.editor.state.selection;
        const selectedText = selection.empty ? "" : this.editor.state.doc.textBetween(selection.from, selection.to);
        
        console.log("currentAttrs", currentAttrs);
        console.log("selectedText", selectedText);
        
        window.panel.dialog.open({
          component: "k-form-dialog",
          props: {
            fields: {
              text: {
                type: "text",
                label: "Badge Text",
                value: currentAttrs.text || selectedText,
                placeholder: selectedText || "Badge"
              },
              variant: {
                type: "select",
                label: "Variant",
                options: [
                  { value: "primary", text: "Primary" },
                  { value: "secondary", text: "Secondary" },
                  { value: "success", text: "Success" },
                  { value: "warning", text: "Warning" },
                  { value: "error", text: "Error" }
                ],
                value: currentAttrs.variant || "primary"
              }
            },
            submitButton: "Save Badge"
          },
          on: {
            submit: (value) => {
              this.editor.command("toggleBadge", value);
              if (window.panel && window.panel.dialog) {
                window.panel.dialog.close();
              }
            }
          }
        });
      }
    };
  },

  get name() {
    return "badge";
  },

  get schema() {
    return {
      attrs: {
        text: { default: null },
        variant: { default: "primary" }
      },
      parseDOM: [
        {
          tag: "span.badge",
          getAttrs(node) {
            return {
              text: node.textContent,
              variant: node.getAttribute("data-variant") || "primary"
            };
          }
        }
      ],
      toDOM(mark) {
        const { text, variant } = mark.attrs;
        const baseStyle = "display: inline-block; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: 600; border-radius: 0.375rem; white-space: nowrap; margin: 0 2px;";
        
        let variantStyle = "";
        switch (variant) {
          case "secondary":
            variantStyle = "background-color: #C6C6C6; color: #3F3F3E;";
            break;
          case "success":
            variantStyle = "background-color: #10b981; color: white;";
            break;
          case "warning":
            variantStyle = "background-color: #f59e0b; color: white;";
            break;
          case "error":
            variantStyle = "background-color: #ef4444; color: white;";
            break;
          case "primary":
          default:
            variantStyle = "background-color: #FCCE4C; color: #222222;";
            break;
        }
        
        return ["span", {
          class: "badge",
          "data-variant": variant || "primary",
          style: baseStyle + variantStyle
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