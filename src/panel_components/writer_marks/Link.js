const modalIdFromFormId = formId => `form-modal-${String(formId || '').replace(/[^a-zA-Z0-9_-]+/g, '-')}`;

const linkTypeFromHref = href => {
  const value = String(href || '');

  if (value.startsWith('page://')) return 'page';
  if (value.startsWith('/@/page/')) return 'page';
  if (value.startsWith('file://')) return 'file';
  if (value.startsWith('mailto:')) return 'email';
  if (value.startsWith('tel:')) return 'tel';
  if (value.startsWith('#')) return 'anchor';
  return 'url';
};

const fieldNameForLinkType = type => {
  return {
    anchor: 'anchor',
    email: 'email',
    file: 'file',
    page: 'page',
    tel: 'tel',
    url: 'url',
  }[type] || 'url';
};

const pageUuidFromValue = value => {
  return String(value || '')
    .replace(/^page:\/\//, '')
    .replace(/^\/@\/page\//, '');
};

const linkFieldValue = (value, linkType) => {
  if (linkType === 'page') {
    const uuid = pageUuidFromValue(value);

    return uuid ? `page://${uuid}` : '';
  }

  return value || '';
};

const pageHrefFromPicker = value => {
  const item = Array.isArray(value) ? value[0] : value;

  if (!item) {
    return '';
  }

  const rawValue =
    typeof item === 'string'
      ? item
      : item.uuid || item.value || item.id || item.link || item.url || '';

  if (!rawValue) {
    return '';
  }

  if (String(rawValue).startsWith('/@/page/')) {
    return rawValue;
  }

  if (String(rawValue).startsWith('page://')) {
    return `/@/page/${pageUuidFromValue(rawValue)}`;
  }

  if (String(rawValue).startsWith('/')) {
    return rawValue;
  }

  return `/@/page/${rawValue}`;
};

const hrefFromDialogValue = value => {
  if (value.href) {
    if (value.linkType === 'page') {
      return pageHrefFromPicker(value.href);
    }

    return value.href;
  }

  switch (value.linkType) {
    case 'anchor':
      return value.anchor || '';
    case 'email':
      return value.email ? `mailto:${String(value.email).replace(/^mailto:/, '')}` : '';
    case 'file':
      return value.file || '';
    case 'page':
      return pageHrefFromPicker(value.page);
    case 'tel':
      return value.tel ? `tel:${String(value.tel).replace(/^tel:/, '')}` : '';
    case 'url':
    default:
      return value.url || '';
  }
};

const normalizeOptions = options => {
  if (!Array.isArray(options)) {
    return [];
  }

  return options
    .map(option => {
      if (typeof option === 'string') {
        return {
          value: option,
          text: option,
        };
      }

      return {
        value: option.value || option.id || '',
        text: option.text || option.label || option.title || option.value || option.id || '',
      };
    })
    .filter(option => option.value.length > 0);
};

const dialogValuesFromHref = href => {
  const type = linkTypeFromHref(href);
  const values = {
    anchor: '',
    email: '',
    file: '',
    page: '',
    tel: '',
    url: '',
  };

  values[fieldNameForLinkType(type)] = String(href || '')
    .replace(/^mailto:/, '')
    .replace(/^tel:/, '');

  return values;
};

const markRange = (state, markName) => {
  const { selection, schema } = state;
  const markType = schema.marks[markName];

  if (!markType) {
    return null;
  }

  if (!selection.empty) {
    return {
      from: selection.from,
      to: selection.to,
      text: state.doc.textBetween(selection.from, selection.to),
    };
  }

  const { $from } = selection;
  const parent = $from.parent;
  const offset = $from.parentOffset;
  const before = parent.childBefore(offset);
  const after = parent.childAfter(offset);
  const mark =
    markType.isInSet($from.marks()) ||
    (before.node ? markType.isInSet(before.node.marks) : null) ||
    (after.node ? markType.isInSet(after.node.marks) : null);

  if (!mark) {
    return null;
  }

  let start = offset;
  let end = offset;

  for (let node = parent.childBefore(start); node.node; node = parent.childBefore(start)) {
    const activeMark = markType.isInSet(node.node.marks);

    if (!activeMark || !activeMark.eq(mark)) {
      break;
    }

    start = node.offset;
  }

  for (let node = parent.childAfter(end); node.node; node = parent.childAfter(end)) {
    const activeMark = markType.isInSet(node.node.marks);

    if (!activeMark || !activeMark.eq(mark)) {
      break;
    }

    end = node.offset + node.node.nodeSize;
  }

  const from = $from.start() + start;
  const to = $from.start() + end;

  return {
    from,
    to,
    text: state.doc.textBetween(from, to),
  };
};

export default {
  get button() {
    return {
      icon: 'url',
      label: 'Link',
    };
  },

  commands() {
    return {
      link: event => {
        if (event.altKey || event.metaKey) {
          return this.remove();
        }

        this.editor.command('openLinkDialog');
      },
      insertLink: (attrs = {}) => {
        const state = this.editor.state;
        const { selection, schema } = state;
        const formId = attrs.formId || '';
        const href = formId ? `#${modalIdFromFormId(formId)}` : attrs.href || '';
        const title = attrs.title || '';
        const range = markRange(state, 'link');
        const selectedText = range ? range.text : '';
        const markAttrs = {
          href,
          formId: formId || null,
          title: title || null,
        };

        if (href && title.length > 0 && range && this.editor.view) {
          const transaction = state.tr.insertText(title, range.from, range.to);
          transaction.addMark(
            range.from,
            range.from + title.length,
            schema.marks.link.create(markAttrs),
          );
          this.editor.view.dispatch(transaction);
          return;
        }

        if (title.length > 0 && (selection.empty || title !== selectedText)) {
          this.editor.insertText(title, true);
        } else if (selection.empty && this.editor.activeMarks.includes('link') === false) {
          this.editor.insertText(href || 'Link', true);
        }

        if (href) {
          return this.update(markAttrs);
        }
      },
      removeLink: () => {
        return this.remove();
      },
      toggleLink: (attrs = {}) => {
        if (attrs.href?.length > 0 || attrs.formId?.length > 0) {
          this.editor.command('insertLink', attrs);
        } else {
          this.editor.command('removeLink');
        }
      },
      openLinkDialog: async () => {
        const currentAttrs = this.editor.getMarkAttrs('link') || {};
        const range = markRange(this.editor.state, 'link');
        const selectedText = range ? range.text : '';
        const currentLinkType = currentAttrs.formId ? 'form' : linkTypeFromHref(currentAttrs.href);
        const linkValues = dialogValuesFromHref(currentAttrs.href);
        let forms = [];

        try {
          const api = window.panel.api || window.panel.$api;
          const response = await api.get('gs-mmh-web-plugin/forms');
          forms = normalizeOptions(response.forms || []);
        } catch (error) {
          forms = [];
        }

        const openTargetDialog = linkType => {
          const fields = {
            title: {
              type: 'text',
              label: 'Linktext',
              value: currentAttrs.title || selectedText,
              placeholder: selectedText || 'Linktext',
              required: true,
            },
          };

          if (linkType === 'url') {
            fields.href = {
              type: 'link',
              label: 'Link',
              options: ['url'],
              value: linkFieldValue(linkValues.url, linkType),
              required: true,
            };
          }

          if (linkType === 'page') {
            fields.href = {
              type: 'link',
              label: 'Link',
              options: ['page'],
              value: linkFieldValue(linkValues.page, linkType),
              required: true,
            };
          }

          if (linkType === 'file') {
            fields.href = {
              type: 'link',
              label: 'Link',
              options: ['file'],
              value: linkFieldValue(linkValues.file, linkType),
              required: true,
            };
          }

          if (linkType === 'email') {
            fields.href = {
              type: 'link',
              label: 'Link',
              options: ['email'],
              value: linkFieldValue(currentAttrs.href, linkType),
              required: true,
            };
          }

          if (linkType === 'tel') {
            fields.href = {
              type: 'link',
              label: 'Link',
              options: ['tel'],
              value: linkFieldValue(currentAttrs.href, linkType),
              required: true,
            };
          }

          if (linkType === 'anchor') {
            fields.href = {
              type: 'link',
              label: 'Link',
              options: ['anchor'],
              value: linkFieldValue(linkValues.anchor, linkType),
              required: true,
            };
          }

          if (linkType === 'form') {
            fields.formId = {
              type: 'select',
              label: 'Formular auswählen',
              options: forms,
              value: currentAttrs.formId || '',
              placeholder: 'Formular auswählen',
              required: true,
            };
          }

          window.panel.dialog.open({
            component: 'k-form-dialog',
            props: {
              fields,
              submitButton: 'Link setzen',
            },
            on: {
              submit: value => {
                if (linkType === 'form') {
                  this.editor.command('toggleLink', {
                    formId: value.formId || '',
                    title: value.title || selectedText || 'Formular öffnen',
                  });
                } else {
                  this.editor.command('toggleLink', {
                    href: hrefFromDialogValue({
                      ...value,
                      linkType,
                    }),
                    title: value.title || selectedText || '',
                  });
                }

                if (window.panel && window.panel.dialog) {
                  window.panel.dialog.close();
                }
              },
            },
          });
        };

        window.panel.dialog.open({
          component: 'k-form-dialog',
          props: {
            fields: {
              linkType: {
                type: 'select',
                label: 'Linktyp',
                value: currentLinkType,
                options: [
                  { value: 'url', text: 'URL' },
                  { value: 'page', text: 'Seite' },
                  { value: 'file', text: 'Datei' },
                  { value: 'email', text: 'E-Mail' },
                  { value: 'tel', text: 'Telefon' },
                  { value: 'anchor', text: 'Anker' },
                  { value: 'form', text: 'Formular' },
                ],
                required: true,
              },
            },
            submitButton: 'Weiter',
          },
          on: {
            submit: value => {
              if (window.panel && window.panel.dialog) {
                window.panel.dialog.close();
              }

              window.setTimeout(() => openTargetDialog(value.linkType || 'url'), 0);
            },
          },
        });
      },
    };
  },

  get defaults() {
    return {
      href: null,
      formId: null,
      title: null,
    };
  },

  get name() {
    return 'link';
  },

  get schema() {
    return {
      attrs: {
        href: { default: null },
        formId: { default: null },
        title: { default: null },
      },
      inclusive: false,
      priority: 60,
      parseDOM: [
        {
          priority: 70,
          tag: 'a[data-form-modal-trigger]',
          getAttrs(node) {
            return {
              href: node.getAttribute('href'),
              formId: node.getAttribute('data-form-modal-trigger'),
              title: node.getAttribute('title') || node.textContent,
            };
          },
        },
        {
          tag: 'a[href]:not([data-button]):not([data-form-modal-trigger])',
          getAttrs(node) {
            return {
              href: node.getAttribute('href'),
            };
          },
        },
      ],
      toDOM(mark) {
        if (mark.attrs.formId) {
          return [
            'a',
            {
              href: mark.attrs.href || `#${modalIdFromFormId(mark.attrs.formId)}`,
              title: mark.attrs.title || '',
              'data-form-modal-trigger': mark.attrs.formId,
            },
            0,
          ];
        }

        return [
          'a',
          {
            href: mark.attrs.href || '#',
          },
          0,
        ];
      },
    };
  },

  remove() {
    this.editor.removeMark(this.name);
  },

  update(attrs) {
    this.editor.updateMark(this.name, attrs);
  },
};
