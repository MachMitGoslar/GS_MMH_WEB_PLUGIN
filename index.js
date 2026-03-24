(function () {
  'use strict';
  function a(e, t, n, r, i, s, c, l) {
    var o = typeof e == 'function' ? e.options : e;
    return (
      t && ((o.render = t), (o.staticRenderFns = n), (o._compiled = !0)),
      s && (o._scopeId = 'data-v-' + s),
      { exports: e, options: o }
    );
  }
  const d = {
    data() {
      return { isOpen: !1 };
    },
    computed: {
      questionField() {
        return this.field('question') || this.field('summary');
      },
      answerField() {
        return this.field('answer') || this.field('details');
      },
      questionValue() {
        const e = this.content.question || this.content.summary || '';
        return String(e)
          .replace(/<[^>]*>/g, '')
          .trim();
      },
    },
  };
  var u = function () {
      var t = this,
        n = t._self._c;
      return n('div', { on: { dblclick: t.open } }, [
        n(
          'div',
          { staticClass: 'k-block-type-accordion-details', class: { 'is-open': t.isOpen } },
          [
            n(
              'div',
              {
                staticClass: 'k-block-type-accordion-summary',
                on: {
                  click: function (r) {
                    t.isOpen = !t.isOpen;
                  },
                },
              },
              [
                n('k-icon', {
                  staticClass: 'k-block-type-accordion-arrow',
                  attrs: { type: 'angle-right' },
                }),
                n('span', { staticClass: 'k-block-type-accordion-question' }, [
                  t._v(
                    ' ' +
                      t._s(t.questionValue || t.questionField.placeholder || 'Add a question…') +
                      ' '
                  ),
                ]),
                n('k-writer', {
                  ref: 'question',
                  attrs: {
                    inline: !0,
                    marks: 'false',
                    placeholder: t.questionField.placeholder || 'Add a question…',
                    value: t.questionValue,
                  },
                  on: {
                    input: function (r) {
                      return t.update({ question: r });
                    },
                  },
                }),
              ],
              1
            ),
            n('k-writer', {
              ref: 'answer',
              staticClass: 'k-block-type-accordion-answer',
              attrs: {
                inline: t.answerField.inline || !1,
                marks: t.answerField.marks,
                value: t.content.answer || t.content.details,
                placeholder: t.answerField.placeholder || 'Add an answer',
              },
              on: {
                input: function (r) {
                  return t.update({ answer: r });
                },
              },
            }),
          ],
          1
        ),
      ]);
    },
    p = [],
    m = a(d, u, p, !1, null, null);
  const h = m.exports,
    g = {
      computed: {
        textField() {
          return this.field('text');
        },
      },
    };
  var f = function () {
      var t = this,
        n = t._self._c;
      return n(
        'div',
        { class: 'k-block-type-box box-' + t.content.boxtype },
        [
          n('k-writer', {
            ref: 'textbox',
            staticClass: 'label',
            attrs: {
              marks: t.textField.marks,
              value: t.content.text,
              placeholder: t.textField.placeholder || 'Enter some stuff…',
            },
            on: {
              input: function (r) {
                return t.update({ text: r });
              },
            },
          }),
          t.content.type !== 'neutral'
            ? n('k-icon', {
                staticClass: 'k-block-type-box-icon',
                attrs: { type: t.content.boxtype },
              })
            : t._e(),
        ],
        1
      );
    },
    _ = [],
    b = a(g, f, _, !1, null, null);
  const v = b.exports,
    k = {
      props: { content: Object, endpoints: Object, fieldset: Object },
      methods: {
        open() {
          this.$emit('open');
        },
      },
    };
  var y = function () {
      var t = this,
        n = t._self._c;
      return n('div', { on: { dblclick: t.open } }, [
        n('header', { staticClass: 'k-block-header' }, [
          n(
            'h3',
            { staticClass: 'k-block-title' },
            [n('k-icon', { attrs: { type: 'button' } }), t._v(' Button ')],
            1
          ),
        ]),
        n(
          'div',
          {
            staticClass: 'gs-c-btn',
            attrs: {
              'data-style': (t.content.buttontype && t.content.buttontype.style) || 'pill',
              'data-type': (t.content.buttontype && t.content.buttontype.color) || 'primary',
              'data-size':
                (t.content.buttontype && t.content.buttontype.size) === 'normal'
                  ? 'regular'
                  : (t.content.buttontype && t.content.buttontype.size) || 'regular',
            },
          },
          [t._v(' ' + t._s(t.content.linktext || 'Click here') + ' ')]
        ),
      ]);
    },
    w = [],
    x = a(k, y, w, !1, null, '52f9e8a4');
  const $ = x.exports,
    C = {
      data() {
        return {
          text: 'No text value',
          pagePreviewText: '',
          pagePreviewHeadline: '',
          imagePreviewUrl: '',
        };
      },
      computed: {
        cardType() {
          return this.content.cardtype;
        },
        headline() {
          return this.cardType === 'manual'
            ? this.content.headline
            : this.pagePreviewHeadline || this.page.text || this.page.title || '';
        },
        headline_html() {
          const e = this.headline || '';
          return String(e).trim();
        },
        subheadline_html() {
          const e = this.content.subheadline || '';
          return String(e).trim();
        },
        image() {
          return this.cardType === 'manual' ? this.content.image[0] || {} : this.page.image || {};
        },
        description_content() {
          const e = this.content.description_content;
          return this.parseBlocks(e) || e;
        },
        description_blocks() {
          const e = this.parseBlocks(this.content.description_content);
          return Array.isArray(e) ? e : [];
        },
        description_text() {
          if (this.cardType === 'page') return this.pagePreviewText || '';
          if (this.description_blocks.length) {
            const t = [];
            return (
              this.description_blocks.forEach(n => {
                const r = n && n.content ? n.content : {};
                ['text', 'heading', 'summary', 'question', 'title', 'caption'].forEach(i => {
                  typeof r[i] == 'string' && t.push(this.stripHtml(r[i]));
                });
              }),
              t.join(' ').trim()
            );
          }
          const e = this.content.description_content;
          return typeof e != 'string' ? '' : this.stripHtml(e);
        },
        manual_content_html() {
          if (this.cardType !== 'manual' || !this.description_blocks.length) return '';
          const e = [];
          return (
            this.description_blocks.forEach(t => {
              if (!t || !t.type) return;
              const n = t.content || {};
              if (t.type === 'heading') {
                const r = n.level || 'h3',
                  i = n.text || '';
                e.push(`<${r}>${i}</${r}>`);
                return;
              }
              if (t.type === 'button') {
                const r = n.linktext || n.text || 'Button';
                e.push(`<span class="k-block-type-card-button">${r}</span>`);
                return;
              }
              if (n.text) {
                e.push(n.text);
                return;
              }
              if (n.quote) {
                e.push(`<blockquote>${n.quote}</blockquote>`);
                return;
              }
            }),
            e.join('')
          );
        },
        pageId() {
          return this.page ? this.page.id : '';
        },
        page() {
          return this.content.page[0] || {};
        },
      },
      watch: {
        cardType: {
          handler(e) {
            e === 'page' && this.loadPagePreview();
          },
          immediate: !0,
        },
        page: {
          handler() {
            this.cardType === 'page' && this.loadPagePreview();
          },
          immediate: !0,
        },
        image: {
          handler() {
            this.loadImagePreview();
          },
          immediate: !0,
        },
      },
      methods: {
        loadPagePreview() {
          if (!this.pageId) {
            ((this.pagePreviewText = ''), (this.pagePreviewHeadline = ''));
            return;
          }
          this.$api.get('pages/' + this.pageId.replaceAll('/', '+')).then(e => {
            const t = e && e.content ? e.content : {},
              n = e && e.title ? e.title : '',
              r = t.headline || n || '',
              i = t.text || '';
            ((this.pagePreviewHeadline = this.stripHtml(r)),
              (this.pagePreviewText = this.stripHtml(i)));
          });
        },
        loadImagePreview() {
          if (this.cardType !== 'manual') {
            this.imagePreviewUrl = this.image && this.image.url ? this.image.url : '';
            return;
          }
          if (this.image && this.image.url) {
            this.imagePreviewUrl = this.image.url;
            return;
          }
          const e = this.image && (this.image.id || this.image.uuid);
          if (!e) {
            this.imagePreviewUrl = '';
            return;
          }
          this.$api
            .get('files/' + String(e).replaceAll('/', '+'))
            .then(t => {
              this.imagePreviewUrl = (t && t.url) || '';
            })
            .catch(() => {
              this.imagePreviewUrl = '';
            });
        },
        parseBlocks(e) {
          if (Array.isArray(e)) return e;
          if (typeof e != 'string') return null;
          try {
            const t = JSON.parse(e);
            return Array.isArray(t) ? t : null;
          } catch {
            return null;
          }
        },
        stripHtml(e) {
          return String(e)
            .replace(/<[^>]*>/g, '')
            .replace(/\s+/g, ' ')
            .trim();
        },
        updateItem(e, t, n, r) {
          ((e.description_content[t].content[n] = r),
            this.$emit('update', { ...this.content, ...e }));
        },
        updateBlock(e, t) {
          const n = this.parseBlocks(this.content.description_content) || [];
          n[e] &&
            ((n[e] = t),
            this.$emit('update', { ...this.content, description_content: JSON.stringify(n) }));
        },
      },
    };
  var F = function () {
      var t = this,
        n = t._self._c;
      return n('div', { on: { dblclick: t.open } }, [
        n(
          'div',
          { staticClass: 'k-block-type-card k-card' },
          [
            t.imagePreviewUrl
              ? n('k-frame', { staticClass: 'hero', attrs: { cover: 'true', ratio: '1/1' } }, [
                  n('img', { attrs: { src: t.imagePreviewUrl, alt: '' } }),
                ])
              : t._e(),
            n('div', { staticClass: 'content' }, [
              t.headline_html
                ? n('div', {
                    staticClass: 'k-block-type-card-headline font-headline',
                    domProps: { innerHTML: t._s(t.headline_html) },
                  })
                : t._e(),
              t.subheadline_html
                ? n('div', {
                    staticClass: 'k-block-type-card-subheadline font-subheadline',
                    domProps: { innerHTML: t._s(t.subheadline_html) },
                  })
                : t._e(),
              t.manual_content_html
                ? n('div', {
                    staticClass: 'k-block-type-card-text',
                    domProps: { innerHTML: t._s(t.manual_content_html) },
                  })
                : n('div', { staticClass: 'k-block-type-card-text' }, [
                    t._v(' ' + t._s(t.description_text || t.text) + ' '),
                  ]),
            ]),
          ],
          1
        ),
      ]);
    },
    T = [],
    D = a(C, F, T, !1, null, null);
  const A = D.exports,
    B = {
      computed: {
        alignment() {
          switch (this.content.alignment) {
            case 'left':
              return 'start';
            case 'right':
              return 'end';
            default:
              return 'center';
          }
        },
        descriptionField() {
          return this.field('description');
        },
        titleField() {
          return this.field('title');
        },
        buttonItems() {
          return this.content.buttons || [];
        },
        typeFields() {
          return this.content.button.buttonType;
        },
      },
      watch: {
        linkTextField: {
          handler(e) {
            console.log('Hallo:', e);
          },
        },
      },
      methods: {
        updateItem(e, t, n, r) {
          ((e.buttons[t].content[n] = r), this.$emit('update', { ...this.content, ...e }));
        },
      },
    };
  var M = function () {
      var t = this,
        n = t._self._c;
      return n(
        'div',
        {
          staticClass: 'k-panel-cta',
          attrs: { 'data-position': t.content.alignment },
          on: { dblclick: t.open },
        },
        [
          n('k-writer', {
            ref: 'title',
            style: 'text-align:' + t.content.alignment,
            attrs: {
              inline: t.titleField.inline || !1,
              marks: !1,
              placeholder: t.titleField.placeholder || 'Add a Title',
              value: t.content.title,
              nodes: t.titleField.nodes || !1,
            },
            on: {
              input: function (r) {
                return t.update({ title: r });
              },
            },
          }),
          n('k-writer', {
            ref: 'description',
            style: 'text-align:' + t.content.alignment,
            attrs: {
              inline: t.descriptionField.inline || !0,
              marks: t.descriptionField.marks,
              value: t.content.description,
              placeholder: t.descriptionField.placeholder || 'Add some description',
            },
            on: {
              input: function (r) {
                return t.update({ description: r });
              },
            },
          }),
          t.buttonItems.length > 0
            ? n(
                'div',
                { style: 'align-self:' + t.alignment },
                t._l(t.buttonItems, function (r, i) {
                  return n(
                    'button',
                    {
                      class: ['k-panel-button'],
                      attrs: {
                        'data-style': r.content.buttontype.style || 'pill',
                        'data-type': r.content.buttontype.color || 'primary',
                      },
                    },
                    [
                      n('k-writer', {
                        ref: 'link_text',
                        refInFor: !0,
                        attrs: {
                          inline: !0,
                          marks: !1,
                          value: r.content.linktext,
                          placeholder: 'Call to Action Text',
                        },
                        on: {
                          input: function (s) {
                            return t.updateItem(t.content, i, 'linktext', s);
                          },
                        },
                      }),
                    ],
                    1
                  );
                }),
                0
              )
            : t._e(),
        ],
        1
      );
    },
    S = [],
    q = a(B, M, S, !1, null, null);
  const O = q.exports,
    P = {
      props: { content: Object, endpoints: Object, fieldset: Object },
      computed: {
        germanDateTime() {
          return new Intl.DateTimeFormat('de-DE', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
          });
        },
        publishDisplay() {
          return this.formatDateTime(this.content.publish_date || '');
        },
        endDisplay() {
          return this.formatDateTime(this.content.end_date || '');
        },
        titleText() {
          return this.content.title || 'Datei Download';
        },
        descriptionText() {
          return this.content.text || '';
        },
        fileName() {
          const e = Array.isArray(this.content.file) ? this.content.file[0] : null;
          return (e && (e.filename || e.name || e.id)) || '';
        },
        scheduleLabel() {
          const e = this.publishDisplay,
            t = this.endDisplay;
          return e && t ? `🕒 ${e} → ${t}` : e ? `🕒 ab ${e}` : t ? `🕒 bis ${t}` : null;
        },
      },
      methods: {
        formatDateTime(e) {
          if (!e) return '';
          const t = String(e).replace(' ', 'T'),
            n = new Date(t);
          return Number.isNaN(n.getTime()) ? String(e) : this.germanDateTime.format(n);
        },
        open() {
          this.$emit('open');
        },
      },
    };
  var L = function () {
      var t = this,
        n = t._self._c;
      return n('div', { staticClass: 'k-block-type-download', on: { dblclick: t.open } }, [
        n('div', { staticClass: 'k-block-type-download-body' }, [
          n('div', { staticClass: 'k-block-type-download-title' }, [t._v(t._s(t.titleText))]),
          t.descriptionText
            ? n('div', { staticClass: 'k-block-type-download-text' }, [
                t._v(t._s(t.descriptionText)),
              ])
            : t._e(),
          t.fileName
            ? n('div', { staticClass: 'k-block-type-download-file' }, [t._v(t._s(t.fileName))])
            : t._e(),
          t.scheduleLabel
            ? n('div', { staticClass: 'k-block-type-download-schedule' }, [
                t._v(' ' + t._s(t.scheduleLabel) + ' '),
              ])
            : t._e(),
        ]),
      ]);
    },
    E = [],
    R = a(P, L, E, !1, null, 'e5b4baf2');
  const z = R.exports,
    H = {
      computed: {
        items() {
          return this.content.faq || {};
        },
        headingField() {
          return this.field('heading') || '';
        },
        headingText() {
          const e = this.content.heading || '';
          return String(e)
            .replace(/<[^>]*>/g, '')
            .trim();
        },
        headingTitle() {
          return this.headingText || this.headingField.placeholder || 'FAQ';
        },
      },
      methods: {
        updateItem(e, t, n, r) {
          (console.log(r),
            (e.faq[t].content[n] = r),
            this.$emit('update', { ...this.content, ...e }));
        },
      },
    };
  var N = function () {
      var t = this,
        n = t._self._c;
      return n('div', { on: { dblclick: t.open } }, [
        n('header', { staticClass: 'k-block-header' }, [
          n(
            'h3',
            {
              staticClass: 'k-block-title',
              staticStyle: {
                'white-space': 'nowrap',
                overflow: 'hidden',
                'text-overflow': 'ellipsis',
              },
            },
            [n('k-icon', { attrs: { type: 'question' } }), t._v(' ' + t._s(t.headingTitle) + ' ')],
            1
          ),
        ]),
        t.content.faq.length ? t._e() : n('div', [t._v('No items yet')]),
      ]);
    },
    X = [],
    j = a(H, N, X, !1, null, null);
  const I = j.exports,
    K = {
      computed: {
        image() {
          return this.content.image[0] || {};
        },
        bio() {
          return [this.content.jobposition, this.content.company]
            .filter(e => e != null && e != '')
            .join(', ');
        },
        quoteField() {
          return this.field('quote');
        },
      },
    };
  var V = function () {
      var t = this,
        n = t._self._c;
      return n(
        'blockquote',
        { staticClass: 'k-block-type-testimonial-quote', on: { dblclick: t.open } },
        [
          n('k-writer', {
            ref: 'quote',
            attrs: {
              inline: !0,
              marks: !1,
              value: t.content.quote,
              placeholder: t.quoteField.placeholder,
            },
            on: {
              input: function (r) {
                return t.update({ quote: r });
              },
            },
          }),
          n('footer', [
            n('figure', { staticClass: 'k-block-type-testimonial-voice' }, [
              t.image.url
                ? n('img', {
                    attrs: {
                      src: t.image.url,
                      width: '48px',
                      height: '48px',
                      alt: 'Photo of ' + t.content.name,
                    },
                  })
                : t._e(),
              n('figcaption', [
                t._v(' ' + t._s(t.content.name)),
                n('br'),
                t._v(' ' + t._s(t.bio) + ' '),
              ]),
            ]),
          ]),
        ],
        1
      );
    },
    U = [],
    J = a(K, V, U, !1, null, null);
  const Z = J.exports,
    Q = {
      computed: {
        entriesArray() {
          if (!this.content || !this.content.entries) return [];
          const e = this.content.entries;
          if (Array.isArray(e)) return e;
          if (typeof e == 'string')
            try {
              const t = JSON.parse(e);
              return Array.isArray(t) ? t : [];
            } catch (t) {
              return (console.warn('Failed to parse timeline entries:', t), []);
            }
          return typeof e == 'object' && e !== null ? Object.values(e) : [];
        },
        hasEntries() {
          try {
            return this.entriesArray.length > 0;
          } catch (e) {
            return (console.warn('Error checking timeline entries:', e), !1);
          }
        },
        displayEntries() {
          try {
            return this.entriesArray.slice(0, 3);
          } catch (e) {
            return (console.warn('Error getting display entries:', e), []);
          }
        },
        totalEntries() {
          try {
            return this.entriesArray.length;
          } catch (e) {
            return (console.warn('Error getting total entries:', e), 0);
          }
        },
      },
      methods: {
        open() {
          this.$emit('open');
        },
      },
    };
  var W = function () {
      var t = this,
        n = t._self._c;
      return n(
        'div',
        { staticClass: 'k-block-type k-block-type-timeline', on: { dblclick: t.open } },
        [
          n('header', { staticClass: 'k-block-header' }, [
            n(
              'h3',
              { staticClass: 'k-block-title' },
              [n('k-icon', { attrs: { type: 'list' } }), t._v(' Timeline ')],
              1
            ),
          ]),
          n('div', { staticClass: 'k-block-body' }, [
            t.content.title
              ? n('div', { staticClass: 'k-block-timeline-title' }, [
                  n('strong', [t._v(t._s(t.content.title))]),
                ])
              : t._e(),
            t.hasEntries
              ? n(
                  'div',
                  { staticClass: 'k-block-timeline-preview' },
                  [
                    t._l(t.displayEntries, function (r, i) {
                      return n('div', { key: i, staticClass: 'k-block-timeline-item' }, [
                        n('div', { staticClass: 'k-block-timeline-date' }, [
                          t._v(t._s(r.year || 'No year')),
                        ]),
                        n('div', { staticClass: 'k-block-timeline-text' }, [
                          t._v(t._s(r.summary || 'No summary')),
                        ]),
                        r.image
                          ? n('div', { staticClass: 'k-block-timeline-image' }, [t._v('📷')])
                          : t._e(),
                      ]);
                    }),
                    t.totalEntries > 3
                      ? n('div', { staticClass: 'k-block-timeline-more' }, [
                          t._v(' ... und ' + t._s(t.totalEntries - 3) + ' weitere Einträge '),
                        ])
                      : t._e(),
                  ],
                  2
                )
              : n('div', { staticClass: 'k-block-empty' }, [
                  t._v('Noch keine Timeline-Einträge vorhanden'),
                ]),
          ]),
        ]
      );
    },
    G = [],
    Y = a(Q, W, G, !1, null, 'e5145f8f');
  const tt = Y.exports,
    et = {
      props: { content: Object, endpoints: Object, fieldset: Object },
      computed: {
        formFieldComponent() {
          var t, n;
          const e = (n = (t = this.fieldset) == null ? void 0 : t.fields) == null ? void 0 : n.form;
          return !e || !e.type ? null : 'k-' + e.type + '-field';
        },
        formValue() {
          var e;
          return ((e = this.content) == null ? void 0 : e.form) || null;
        },
        formLabel() {
          var t;
          const e = this.formValue;
          return e
            ? Array.isArray(e) && (t = e[0]) != null && t.text
              ? e[0].text
              : e != null && e.text
                ? e.text
                : e != null && e.title
                  ? e.title
                  : typeof e == 'string'
                    ? e
                    : 'Formular'
            : 'Kein Formular ausgewählt';
        },
        scheduleLabel() {
          const e = new Intl.DateTimeFormat('de-DE', {
              day: '2-digit',
              month: '2-digit',
              year: 'numeric',
              hour: '2-digit',
              minute: '2-digit',
            }),
            t = this.content.publish_date
              ? e.format(new Date(String(this.content.publish_date).replace(' ', 'T')))
              : null,
            n = this.content.end_date
              ? e.format(new Date(String(this.content.end_date).replace(' ', 'T')))
              : null;
          return t && n ? `🕒 ${t} → ${n}` : t ? `🕒 ab ${t}` : n ? `🕒 bis ${n}` : null;
        },
      },
      methods: {
        updateForm(e) {
          this.$emit('update', { ...this.content, form: e });
        },
        open() {
          this.$emit('open');
        },
      },
    };
  var nt = function () {
      var t = this,
        n = t._self._c;
      return n('div', { staticClass: 'k-block-type-form', on: { dblclick: t.open } }, [
        n('div', { staticClass: 'k-block-type-form-body' }, [
          n('div', { staticClass: 'k-block-type-form-title' }, [
            n('span', { staticStyle: { display: 'inline-flex', 'align-items': 'center' } }, [
              n(
                'svg',
                {
                  staticStyle: { 'margin-right': '4px', opacity: '0.7' },
                  attrs: { width: '18', height: '18', viewBox: '0 0 24 24' },
                },
                [
                  n('path', {
                    attrs: {
                      fill: 'currentColor',
                      d: 'M17 2a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zm0 2H7v16h10zm-2 3v2H9V7zm0 4v2H9v-2zm0 4v2H9v-2z',
                    },
                  }),
                ]
              ),
              t._v(' ' + t._s(t.formLabel) + ' '),
            ]),
            n(
              'button',
              {
                staticClass: 'k-block-type-form-select',
                attrs: { type: 'button' },
                on: {
                  click: function (r) {
                    return (r.stopPropagation(), t.open.apply(null, arguments));
                  },
                },
              },
              [t._v(' Auswählen ')]
            ),
          ]),
          t.formFieldComponent
            ? n(
                'div',
                { staticClass: 'k-block-type-form-field' },
                [
                  n(
                    t.formFieldComponent,
                    t._b(
                      {
                        tag: 'component',
                        attrs: { value: t.formValue, endpoints: t.endpoints },
                        on: { input: t.updateForm },
                      },
                      'component',
                      t.fieldset.fields.form,
                      !1
                    )
                  ),
                ],
                1
              )
            : t._e(),
          t.scheduleLabel
            ? n('div', { staticClass: 'k-block-type-form-schedule' }, [
                t._v(' ' + t._s(t.scheduleLabel) + ' '),
              ])
            : t._e(),
        ]),
      ]);
    },
    rt = [],
    it = a(et, nt, rt, !1, null, '9ec79fd9');
  const at = it.exports;
  var st = a(
    {
      get button() {
        return { icon: 'quote', id: 'blockquote', label: 'Quote', when: ['paragraph'] };
      },
      commands({ type: e, utils: t, schema: n }) {
        return () => t.toggleBlockType(e, n.nodes.paragraph);
      },
      get name() {
        return 'blockquote';
      },
      get schema() {
        return {
          content: 'paragraph+',
          group: 'block',
          defining: !1,
          draggable: !1,
          priority: 69,
          parseDOM: [{ tag: 'blockquote' }],
          toDOM(e) {
            return ['blockquote', 0];
          },
        };
      },
    },
    null,
    null,
    !1,
    null,
    null
  );
  const ot = st.exports;
  var lt = a(
    {
      get button() {
        return { icon: 'h5', id: 'headline', label: 'Headline', when: ['heading', 'paragraph'] };
      },
      commands({ type: e, utils: t, schema: n }) {
        return () => t.toggleBlockType(e, n.nodes.paragraph, { level: 5 });
      },
      get name() {
        return 'headline';
      },
      get schema() {
        return {
          content: 'text*',
          group: 'block',
          defining: !1,
          draggable: !1,
          priority: 71,
          parseDOM: [{ tag: 'h5', attrs: { level: 5 } }],
          toDOM(e) {
            return ['h5', { class: 'font-headline' }, 0];
          },
        };
      },
    },
    null,
    null,
    !1,
    null,
    null
  );
  const ct = lt.exports;
  var dt = a(
    {
      get button() {
        return {
          icon: 'h6',
          id: 'subheadline',
          label: 'Subheadline',
          when: ['heading', 'paragraph'],
        };
      },
      commands({ type: e, utils: t, schema: n }) {
        return () => t.toggleBlockType(e, n.nodes.paragraph, { level: 6 });
      },
      get name() {
        return 'subheadline';
      },
      get schema() {
        return {
          content: 'text*',
          group: 'block',
          defining: !1,
          draggable: !1,
          priority: 70,
          parseDOM: [{ tag: 'h6', attrs: { level: 6 } }],
          toDOM(e) {
            return ['h6', { class: 'font-subheadline' }, 0];
          },
        };
      },
    },
    null,
    null,
    !1,
    null,
    null
  );
  const ut = dt.exports;
  var pt = a(
    {
      get button() {
        return { icon: 'h4', id: 'title', label: 'Title', when: ['heading', 'paragraph'] };
      },
      commands({ type: e, utils: t, schema: n }) {
        return () => t.toggleBlockType(e, n.nodes.paragraph, { level: 4 });
      },
      get name() {
        return 'title';
      },
      get schema() {
        return {
          content: 'text*',
          group: 'block',
          defining: !1,
          draggable: !1,
          priority: 72,
          parseDOM: [{ tag: 'h4', class: 'font-title' }],
          toDOM: () => ['h4', { class: 'font-title' }, 0],
        };
      },
    },
    null,
    null,
    !1,
    null,
    null
  );
  const mt = pt.exports;
  var ht = a(
    {
      get button() {
        return { icon: 'h3', id: 'titleXL', label: 'Title XL', when: ['heading', 'paragraph'] };
      },
      commands({ type: e, utils: t, schema: n }) {
        return () => t.toggleBlockType(e, n.nodes.paragraph, { level: 3 });
      },
      get name() {
        return 'titleXL';
      },
      get schema() {
        return {
          content: 'text*',
          group: 'block',
          defining: !0,
          draggable: !1,
          priority: 70,
          parseDOM: [{ tag: 'h3', class: 'font-titleXL' }],
          toDOM: () => ['h3', { class: 'font-titleXL' }, 0],
        };
      },
    },
    null,
    null,
    !1,
    null,
    null
  );
  const gt = ht.exports;
  var ft = a(
    {
      get button() {
        return { icon: 'h2', id: 'titleXXL', label: 'Title XXL', when: ['heading', 'paragraph'] };
      },
      commands({ type: e, utils: t, schema: n }) {
        return () => t.toggleBlockType(e, n.nodes.paragraph);
      },
      get name() {
        return 'titleXXL';
      },
      get schema() {
        return {
          content: 'text*',
          group: 'block',
          defining: !0,
          draggable: !1,
          priority: 70,
          parseDOM: [{ tag: 'h2', class: 'font-titleXXL' }],
          toDOM: () => ['h2', { class: 'font-titleXXL' }, 0],
        };
      },
    },
    null,
    null,
    !1,
    null,
    null
  );
  const _t = ft.exports,
    bt = {
      get button() {
        return { icon: 'tag', label: 'Badge' };
      },
      commands() {
        return {
          badge: e => {
            if (e.altKey || e.metaKey) return this.remove();
            this.editor.command('openBadgeDialog');
          },
          insertBadge: (e = {}) => {
            const { selection: t } = this.editor.state;
            if (
              (t.empty &&
                this.editor.activeMarks.includes('badge') === !1 &&
                this.editor.insertText(e.text || 'Badge', !0),
              e.text)
            )
              return this.update(e);
          },
          removeBadge: () => this.remove(),
          toggleBadge: (e = {}) => {
            var t;
            ((t = e.text) == null ? void 0 : t.length) > 0
              ? this.editor.command('insertBadge', e)
              : this.editor.command('removeBadge');
          },
          openBadgeDialog: () => {
            const e = this.editor.getMarkAttrs('badge') || {},
              t = this.editor.state.selection,
              n = t.empty ? '' : this.editor.state.doc.textBetween(t.from, t.to);
            (console.log('currentAttrs', e),
              console.log('selectedText', n),
              window.panel.dialog.open({
                component: 'k-form-dialog',
                props: {
                  fields: {
                    text: {
                      type: 'text',
                      label: 'Badge Text',
                      value: e.text || n,
                      placeholder: n || 'Badge',
                    },
                    variant: {
                      type: 'select',
                      label: 'Variant',
                      options: [
                        { value: 'primary', text: 'Primary' },
                        { value: 'secondary', text: 'Secondary' },
                        { value: 'success', text: 'Success' },
                        { value: 'warning', text: 'Warning' },
                        { value: 'error', text: 'Error' },
                      ],
                      value: e.variant || 'primary',
                    },
                  },
                  submitButton: 'Save Badge',
                },
                on: {
                  submit: r => {
                    (this.editor.command('toggleBadge', r),
                      window.panel && window.panel.dialog && window.panel.dialog.close());
                  },
                },
              }));
          },
        };
      },
      get name() {
        return 'badge';
      },
      get schema() {
        return {
          attrs: { text: { default: null }, variant: { default: 'primary' } },
          parseDOM: [
            {
              tag: 'span.badge',
              getAttrs(e) {
                return {
                  text: e.textContent,
                  variant: e.getAttribute('data-variant') || 'primary',
                };
              },
            },
          ],
          toDOM(e) {
            const { text: t, variant: n } = e.attrs,
              r =
                'display: inline-block; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: 600; border-radius: 0.375rem; white-space: nowrap; margin: 0 2px;';
            let i = '';
            switch (n) {
              case 'secondary':
                i = 'background-color: #C6C6C6; color: #3F3F3E;';
                break;
              case 'success':
                i = 'background-color: #10b981; color: white;';
                break;
              case 'warning':
                i = 'background-color: #f59e0b; color: white;';
                break;
              case 'error':
                i = 'background-color: #ef4444; color: white;';
                break;
              case 'primary':
              default:
                i = 'background-color: #FCCE4C; color: #222222;';
                break;
            }
            return ['span', { class: 'badge', 'data-variant': n || 'primary', style: r + i }, 0];
          },
        };
      },
      toggle() {
        return this.editor.toggleMark(this.name);
      },
      remove() {
        this.editor.removeMark(this.name);
      },
      update(e) {
        this.editor.updateMark(this.name, e);
      },
    },
    vt = {
      get button() {
        return { icon: 'bolt', label: 'Button' };
      },
      commands() {
        return {
          button: e => {
            if (e.altKey || e.metaKey) return this.remove();
            this.editor.command('openButtonDialog');
          },
          insertButton: (e = {}) => {
            const { selection: t } = this.editor.state;
            if (
              (t.empty &&
                this.editor.activeMarks.includes('button') === !1 &&
                this.editor.insertText(e.href || 'Button', !0),
              e.href)
            )
              return (console.log('TZhis:', e), this.update(e));
          },
          removeButton: () => this.remove(),
          toggleButton: (e = {}) => {
            var t;
            ((t = e.href) == null ? void 0 : t.length) > 0
              ? this.editor.command('insertButton', e)
              : this.editor.command('removeButton');
          },
          openButtonDialog: () => {
            const e = this.editor.getMarkAttrs('button') || {},
              t = this.editor.state.selection;
            (console.log('currentAttrs', e),
              window.panel.dialog.open({
                component: 'k-form-dialog',
                props: {
                  fields: {
                    title: {
                      type: 'text',
                      label: 'Text',
                      value: e.title,
                      input: e.title,
                      placeholder:
                        e.title ||
                        (t.empty ? 'Button' : this.editor.state.doc.textBetween(t.from, t.to)),
                      required: !0,
                    },
                    href: {
                      type: 'url',
                      label: 'Link',
                      input: e.href,
                      placeholder: e.href,
                      required: !0,
                    },
                    target: {
                      type: 'select',
                      label: 'Target',
                      options: [
                        { value: '', text: 'Same window' },
                        { value: '_blank', text: 'New window' },
                      ],
                      placeholder: e.target || '',
                      value: e.target || '',
                    },
                    buttonColor: {
                      type: 'select',
                      label: 'Button Color',
                      options: [
                        { value: 'primary', text: 'Gelb' },
                        { value: 'secondary', text: 'Dunkel' },
                        { value: 'tertiary', text: 'Lila' },
                      ],
                      value: e.buttonColor || 'primary',
                    },
                    buttonSize: {
                      type: 'select',
                      label: 'Button Size',
                      options: [
                        { value: 'small', text: 'Small' },
                        { value: 'normal', text: 'Normal' },
                        { value: 'large', text: 'Large' },
                      ],
                      value: e.buttonSize || 'normal',
                    },
                    buttonStyle: {
                      type: 'select',
                      label: 'Button Style',
                      options: [
                        { value: 'pill', text: 'Pille' },
                        { value: 'rounded-corners', text: 'Abgerundet' },
                        { value: 'square', text: 'Rechteckig' },
                      ],
                      value: e.buttonStyle || 'pill',
                    },
                  },
                  submitButton: 'Save Button',
                },
                on: {
                  submit: n => {
                    (console.log('Saving: ', n),
                      this.editor.command('toggleButton', n),
                      window.panel && window.panel.dialog && window.panel.dialog.close());
                  },
                },
              }));
          },
        };
      },
      get defaults() {
        return { target: null, buttonColor: 'primary', buttonSize: 'normal', buttonStyle: 'pill' };
      },
      get name() {
        return 'button';
      },
      plugins() {
        return [
          {
            props: {
              handleClick: (e, t, n) => {
                const r = this.editor.getMarkAttrs('button');
                r.href &&
                  n.altKey === !0 &&
                  n.target instanceof HTMLAnchorElement &&
                  (n.stopPropagation(), window.open(r.href, r.target));
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
          inclusive: !1,
          priority: 100,
          parseDOM: [
            {
              priority: 60,
              tag: 'a[data-button].gs-c-btn',
              getAttrs(e) {
                return (
                  console.log('getting attrs:', e),
                  {
                    href: e.getAttribute('href'),
                    target: e.getAttribute('target'),
                    title: e.text,
                    buttonColor: e.getAttribute('data-type') || 'primary',
                    buttonSize: e.getAttribute('data-size') || 'normal',
                    buttonStyle: e.getAttribute('data-style') || 'pill',
                  }
                );
              },
            },
          ],
          toDOM(e) {
            console.log('Mark is: ', e.attrs);
            const {
                href: t,
                target: n,
                buttonColor: r,
                buttonSize: i,
                buttonStyle: s,
                title: c,
              } = e.attrs,
              l = {
                href: t || '#',
                class: 'gs-c-btn',
                title: c || '',
                'data-button': 'true',
                'data-type': r || 'primary',
                'data-size': i || 'normal',
                'data-style': s || 'pill',
              };
            return (n && (l.target = n), ['a', l, 0]);
          },
        };
      },
      toggle() {
        return this.editor.toggleMark(this.name);
      },
      update(e) {
        this.editor.updateMark(this.name, e);
      },
    },
    kt = {
      get button() {
        return { icon: 'text', label: 'Footnote' };
      },
      commands() {
        return { footnote: e => (e.altKey || e.metaKey ? this.remove() : this.toggle()) };
      },
      get name() {
        return 'footnote';
      },
      get schema() {
        return {
          parseDOM: [{ tag: 'small' }, { tag: 'span.font-footnote' }],
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
      update(e) {
        this.editor.updateMark(this.name, e);
      },
    },
    yt = {
      get button() {
        return { icon: 'highlight', label: 'Highlight' };
      },
      commands() {
        return { highlight: e => (e.altKey || e.metaKey ? this.remove() : this.toggle()) };
      },
      get name() {
        return 'highlight';
      },
      get schema() {
        return {
          parseDOM: [{ tag: 'mark' }, { tag: 'span.highlight' }],
          toDOM() {
            return [
              'mark',
              {
                style:
                  'background-color: #FEEFC3; color: #222222; padding: 0.125rem 0.25rem; border-radius: 0.25rem; font-weight: 500; margin: 0 1px;',
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
      update(e) {
        this.editor.updateMark(this.name, e);
      },
    },
    wt = {
      props: { forms: Array },
      methods: {
        formatDate(e) {
          return e
            ? new Date(e).toLocaleDateString('de-DE', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
              })
            : '';
        },
      },
    };
  var xt = function () {
      var t = this,
        n = t._self._c;
      return n(
        'k-panel-inside',
        { staticClass: 'k-dreamform-db-overview' },
        [
          n('k-header', [t._v(' Formular-Eingänge ')]),
          t.forms.length === 0
            ? n(
                'div',
                { staticClass: 'dfdb-empty' },
                [
                  n('k-icon', { attrs: { type: 'email' } }),
                  n('p', [t._v('Noch keine Formular-Eingänge vorhanden.')]),
                ],
                1
              )
            : n(
                'div',
                { staticClass: 'dfdb-forms' },
                t._l(t.forms, function (r) {
                  return n(
                    'a',
                    {
                      key: r.slug,
                      staticClass: 'dfdb-form-card',
                      attrs: { href: '/panel/formular-eingaenge/' + r.slug },
                      on: {
                        click: function (i) {
                          return (
                            i.preventDefault(),
                            t.$panel.open('formular-eingaenge/' + r.slug)
                          );
                        },
                      },
                    },
                    [
                      n(
                        'div',
                        { staticClass: 'dfdb-form-card-icon' },
                        [n('k-icon', { attrs: { type: 'form' } })],
                        1
                      ),
                      n('div', { staticClass: 'dfdb-form-card-content' }, [
                        n('h3', [t._v(t._s(r.title))]),
                        n('p', { staticClass: 'dfdb-meta' }, [
                          t._v(
                            ' ' +
                              t._s(r.count) +
                              ' ' +
                              t._s(r.count === 1 ? 'Eintrag' : 'Einträge') +
                              ' '
                          ),
                          r.last
                            ? n('span', [t._v(' · Letzter: ' + t._s(t.formatDate(r.last)))])
                            : t._e(),
                        ]),
                      ]),
                      n(
                        'div',
                        { staticClass: 'dfdb-form-card-arrow' },
                        [n('k-icon', { attrs: { type: 'angle-right' } })],
                        1
                      ),
                    ]
                  );
                }),
                0
              ),
        ],
        1
      );
    },
    $t = [],
    Ct = a(wt, xt, $t, !1, null, null);
  const Ft = Ct.exports,
    Tt = {
      props: {
        formSlug: String,
        formTitle: String,
        tableName: String,
        submissions: Array,
        columns: Array,
        pagination: Object,
      },
      computed: {
        displayColumns() {
          return this.columns.slice(0, 5);
        },
      },
      methods: {
        formatDate(e) {
          return e
            ? new Date(e).toLocaleDateString('de-DE', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
              })
            : '';
        },
        formatCol(e) {
          return e.replace(/[-_]/g, ' ').replace(/\b\w/g, t => t.toUpperCase());
        },
        truncate(e) {
          if (e == null) return '';
          const t = Array.isArray(e) ? e.join(', ') : String(e);
          return t.length > 60 ? t.substring(0, 60) + '…' : t;
        },
        showDetail(e) {
          this.$panel.dialog.open('dreamform-db/' + this.tableName + '/' + e);
        },
        confirmDelete(e) {
          this.$panel.dialog.open('dreamform-db/' + this.tableName + '/' + e + '/delete');
        },
        goToPage(e) {
          this.$panel.open('formular-eingaenge/' + this.formSlug + '?page=' + e);
        },
      },
    };
  var Dt = function () {
      var t = this,
        n = t._self._c;
      return n(
        'k-panel-inside',
        { staticClass: 'k-dreamform-db-form' },
        [
          n(
            'k-header',
            [
              t._v(' ' + t._s(t.formTitle) + ' '),
              n(
                'k-button-group',
                { attrs: { slot: 'left' }, slot: 'left' },
                [
                  n(
                    'k-button',
                    {
                      attrs: { icon: 'angle-left' },
                      on: {
                        click: function (r) {
                          return t.$panel.open('formular-eingaenge');
                        },
                      },
                    },
                    [t._v(' Zurück ')]
                  ),
                ],
                1
              ),
            ],
            1
          ),
          t.submissions.length === 0
            ? n(
                'div',
                { staticClass: 'dfdb-empty' },
                [
                  n('k-icon', { attrs: { type: 'email' } }),
                  n('p', [t._v('Keine Einträge für dieses Formular.')]),
                ],
                1
              )
            : n('div', [
                n('div', { staticClass: 'dfdb-table-wrap' }, [
                  n('table', { staticClass: 'dfdb-table' }, [
                    n('thead', [
                      n(
                        'tr',
                        [
                          n('th', [t._v('Datum')]),
                          t._l(t.displayColumns, function (r) {
                            return n('th', { key: r }, [t._v(t._s(t.formatCol(r)))]);
                          }),
                          n('th', { staticClass: 'dfdb-col-actions' }),
                        ],
                        2
                      ),
                    ]),
                    n(
                      'tbody',
                      t._l(t.submissions, function (r) {
                        return n(
                          'tr',
                          {
                            key: r.id,
                            staticClass: 'dfdb-row',
                            on: {
                              click: function (i) {
                                return t.showDetail(r.id);
                              },
                            },
                          },
                          [
                            n('td', { staticClass: 'dfdb-col-date' }, [
                              t._v(t._s(t.formatDate(r.submittedAt))),
                            ]),
                            t._l(t.displayColumns, function (i) {
                              return n('td', { key: i }, [t._v(t._s(t.truncate(r.data[i])))]);
                            }),
                            n(
                              'td',
                              {
                                staticClass: 'dfdb-col-actions',
                                on: {
                                  click: function (i) {
                                    i.stopPropagation();
                                  },
                                },
                              },
                              [
                                n('k-button', {
                                  attrs: { icon: 'trash', theme: 'negative', size: 'xs' },
                                  on: {
                                    click: function (i) {
                                      return t.confirmDelete(r.id);
                                    },
                                  },
                                }),
                              ],
                              1
                            ),
                          ],
                          2
                        );
                      }),
                      0
                    ),
                  ]),
                ]),
                t.pagination.pages > 1
                  ? n(
                      'div',
                      { staticClass: 'dfdb-pagination' },
                      [
                        n(
                          'k-button',
                          {
                            attrs: { disabled: t.pagination.page <= 1, icon: 'angle-left' },
                            on: {
                              click: function (r) {
                                return t.goToPage(t.pagination.page - 1);
                              },
                            },
                          },
                          [t._v(' Zurück ')]
                        ),
                        n('span', { staticClass: 'dfdb-page-info' }, [
                          t._v(
                            ' Seite ' +
                              t._s(t.pagination.page) +
                              ' von ' +
                              t._s(t.pagination.pages) +
                              ' (' +
                              t._s(t.pagination.total) +
                              ' Einträge) '
                          ),
                        ]),
                        n(
                          'k-button',
                          {
                            attrs: {
                              disabled: t.pagination.page >= t.pagination.pages,
                              icon: 'angle-right',
                            },
                            on: {
                              click: function (r) {
                                return t.goToPage(t.pagination.page + 1);
                              },
                            },
                          },
                          [t._v(' Weiter ')]
                        ),
                      ],
                      1
                    )
                  : t._e(),
              ]),
        ],
        1
      );
    },
    At = [],
    Bt = a(Tt, Dt, At, !1, null, null);
  const Mt = Bt.exports;
  panel.plugin('gs-mmh/gs-mmh-web-plugin', {
    blocks: {
      accordion: h,
      box: v,
      button: $,
      card: A,
      cta: O,
      download: z,
      faq: I,
      form: at,
      testimonial: Z,
      timeline: tt,
    },
    writerNodes: {
      blockquote: ot,
      headline: ct,
      subheadline: ut,
      title: mt,
      titleXL: gt,
      titleXXL: _t,
    },
    writerMarks: { badge: bt, button: vt, footnote: kt, highlight: yt },
    components: { 'k-dreamform-db-overview': Ft, 'k-dreamform-db-form': Mt },
  });
})();
