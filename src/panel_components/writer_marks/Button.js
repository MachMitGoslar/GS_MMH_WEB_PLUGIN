export default {
  get button() {
    return {
      icon: 'circle',
      label: 'Button',
    };
  },

  commands() {
    return {
      button: event => {
        if (event.altKey || event.metaKey) {
          return this.remove();
        }
        this.editor.command('openButtonDialog');
      },
      insertButton: (attrs = {}) => {
        const { selection } = this.editor.state;

        // if no text is selected and button mark is not active
        // we insert the button text as placeholder
        if (selection.empty && this.editor.activeMarks.includes('button') === false) {
          this.editor.insertText(attrs.href || 'Button', true);
        }

        if (attrs.href) {
          console.log('TZhis:', attrs);
          return this.update(attrs);
        }
      },
      removeButton: () => {
        return this.remove();
      },
      toggleButton: (attrs = {}) => {
        if (attrs.href?.length > 0) {
          this.editor.command('insertButton', attrs);
        } else {
          this.editor.command('removeButton');
        }
      },
      openButtonDialog: () => {
        const currentAttrs = this.editor.getMarkAttrs('button') || {};
        const selection = this.editor.state.selection;
        console.log('currentAttrs', currentAttrs);

        window.panel.dialog.open({
          component: 'k-form-dialog',
          props: {
            fields: {
              title: {
                type: 'text',
                label: 'Text',
                value: currentAttrs.title,
                input: currentAttrs.title,
                placeholder:
                  currentAttrs.title ||
                  (selection.empty
                    ? 'Button'
                    : this.editor.state.doc.textBetween(selection.from, selection.to)),
                required: true,
              },
              href: {
                type: 'url',
                label: 'Link',
                input: currentAttrs.href,
                placeholder: currentAttrs.href,
                required: true,
              },
              target: {
                type: 'select',
                label: 'Target',
                options: [
                  { value: '', text: 'Same window' },
                  { value: '_blank', text: 'New window' },
                ],
                placeholder: currentAttrs.target || '',
                value: currentAttrs.target || '',
              },
              buttonColor: {
                type: 'select',
                label: 'Button Color',
                options: [
                  { value: 'primary', text: 'Gelb' },
                  { value: 'secondary', text: 'Dunkel' },
                  { value: 'tertiary', text: 'Lila' },
                ],
                value: currentAttrs.buttonColor || 'primary',
              },
              buttonSize: {
                type: 'select',
                label: 'Button Size',
                options: [
                  { value: 'small', text: 'Small' },
                  { value: 'normal', text: 'Normal' },
                  { value: 'large', text: 'Large' },
                ],
                value: currentAttrs.buttonSize || 'normal',
              },
              buttonStyle: {
                type: 'select',
                label: 'Button Style',
                options: [
                  { value: 'pill', text: 'Pille' },
                  { value: 'rounded-corners', text: 'Abgerundet' },
                  { value: 'square', text: 'Rechteckig' },
                ],
                value: currentAttrs.buttonStyle || 'pill',
              },
            },
            submitButton: 'Save Button',
          },
          on: {
            submit: value => {
              console.log('Saving: ', value);
              this.editor.command('toggleButton', value);
              if (window.panel && window.panel.dialog) {
                window.panel.dialog.close();
              }
            },
          },
        });
      },
    };
  },

  get defaults() {
    return {
      target: null,
      buttonColor: 'primary',
      buttonSize: 'normal',
      buttonStyle: 'pill',
    };
  },

  get name() {
    return 'button';
  },

  plugins() {
    return [
      {
        props: {
          handleClick: (view, pos, event) => {
            const attrs = this.editor.getMarkAttrs('button');

            if (attrs.href && event.altKey === true && event.target instanceof HTMLAnchorElement) {
              event.stopPropagation();
              window.open(attrs.href, attrs.target);
            }
          },
        },
      },
    ];
  },

  remove() {
    this.editor.removeMark(this.name);
  },

  get schema() {
    return {
      attrs: {
        href: { default: null },
        target: { default: null },
        title: { default: null },
        buttonColor: { default: 'primary' },
        buttonSize: { default: 'normal' },
        buttonStyle: { default: 'pill' },
      },
      inclusive: false,
      priority: 100,
      parseDOM: [
        {
          priority: 60,
          tag: 'a[data-button].gs-c-btn',
          getAttrs(node) {
            console.log('getting attrs:', node);
            return {
              href: node.getAttribute('href'),
              target: node.getAttribute('target'),
              title: node.text,
              buttonColor: node.getAttribute('data-type') || 'primary',
              buttonSize: node.getAttribute('data-size') || 'normal',
              buttonStyle: node.getAttribute('data-style') || 'pill',
            };
          },
        },
      ],
      toDOM(mark) {
        console.log('Mark is: ', mark.attrs);
        const { href, target, buttonColor, buttonSize, buttonStyle, title } = mark.attrs;
        const attrs = {
          href: href || '#',
          class: 'gs-c-btn',
          title: title || '',
          'data-button': 'true',
          'data-type': buttonColor || 'primary',
          'data-size': buttonSize || 'normal',
          'data-style': buttonStyle || 'pill',
        };
        if (target) {
          attrs.target = target;
        }
        return ['a', attrs, 0];
      },
    };
  },

  toggle() {
    return this.editor.toggleMark(this.name);
  },

  update(attrs) {
    this.editor.updateMark(this.name, attrs);
  },
};
