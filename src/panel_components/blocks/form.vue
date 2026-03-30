<script>
export default {
  props: {
    content: Object,
    endpoints: Object,
    fieldset: Object,
  },

  data() {
    return {
      text: "Kein Formular ausgewählt",
      forms: [],
      formsLoaded: false,
    };
  },

  computed: {
    formValue() {
      return this.content?.form || [];
    },

    fieldConfig() {
      return this.fieldset?.fields?.form || null;
    },

    selectedForm() {
      return Array.isArray(this.formValue) ? this.formValue[0] || null : null;
    },

    hasSelection() {
      return !!this.selectedForm;
    },

    selectedFormLabel() {
      return this.selectedForm?.text || "Kein Formular ausgewählt";
    },

    scheduleLabel() {
      const format = new Intl.DateTimeFormat("de-DE", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
      });

      const publish = this.content.publish_date
          ? format.format(
              new Date(String(this.content.publish_date).replace(" ", "T"))
          )
          : null;

      const end = this.content.end_date
          ? format.format(
              new Date(String(this.content.end_date).replace(" ", "T"))
          )
          : null;

      if (publish && end) return `🕒 ${publish} → ${end}`;
      if (publish) return `🕒 ab ${publish}`;
      if (end) return `🕒 bis ${end}`;
      return null;
    },
  },

  methods: {
    async loadForms() {
      if (this.formsLoaded) return;

      try {
        const response = await this.$api.get("gs-mmh-web-plugin/forms");
        this.forms = Array.isArray(response?.forms) ? response.forms : [];
        this.formsLoaded = true;
      } catch (error) {
        this.forms = [];
      }
    },

    updateForm(value) {
      this.$emit("update", {
        ...this.content,
        form: value,
      });
    },

    openPicker() {
      if (!this.$panel?.dialog) return;

      this.$panel.dialog.open({
        component: "k-form-dialog",
        props: {
          fields: {
            form: {
              label: this.fieldConfig?.label || "Formulare",
              type: "select",
              options: this.forms,
              value: this.selectedForm?.id || "",
              placeholder: "Bitte Formular auswählen",
              required: false,
            },
          },
          submitButton: "OK",
        },
        on: {
          submit: (value) => {
            const selectedId = value.form || "";
            const selectedForm = this.forms.find((form) => form.value === selectedId);

            this.updateForm(
              selectedForm
                ? [
                    {
                      id: selectedForm.value,
                      text: selectedForm.text,
                    },
                  ]
                : []
            );
            this.$panel?.dialog?.close();
          },
          cancel: () => {
            this.$panel?.dialog?.close();
          },
        },
      });
    },

    clearSelection() {
      this.updateForm([]);
    },

    open() {
      this.$emit("open");
    },
  },

  mounted() {
    this.loadForms();
  },
};
</script>

<template>
  <div class="k-block-type-form" @dblclick="open">
    <div class="k-block-type-form-body">
      <div class="k-block-type-form-field">
        <div
          class="k-block-type-form-display"
          :class="{ 'is-empty': !hasSelection }"
          @click.stop="openPicker"
        >
          <div class="k-block-type-form-display-main">
            <div class="k-block-type-form-file-icon">
              <k-icon type="survey" />
            </div>
            <span class="k-block-type-form-display-text">{{ selectedFormLabel  }}</span>
          </div>

          <button
            v-if="hasSelection"
            class="k-block-type-form-clear"
            type="button"
            @click.stop="clearSelection"
          >
            ⊖
          </button>
        </div>
      </div>

      <div v-if="scheduleLabel" class="k-block-type-form-schedule">
        {{ scheduleLabel }}
      </div>
    </div>
  </div>
</template>

<style scoped>
.k-block-type-form {
  position: relative;
}

.k-block-type-form-body {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.k-block-type-form-field {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}

.k-block-type-form-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.k-block-type-form-label {
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--color-text);
}

.k-block-type-form-action {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.45rem 0.7rem;
  border: 0;
  border-radius: 0.375rem;
  background: var(--color-gray-200);
  color: var(--color-text);
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
}

.k-block-type-form-action-icon {
  opacity: 0.7;
}

.k-block-type-form-display {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  min-height: 2.75rem;
  padding: 0.75rem 0.9rem;
  border: 1px solid var(--color-border);
  border-radius: 0.375rem;
  background: var(--color-background);
  cursor: pointer;
}

.k-block-type-form-display.is-empty {
  color: var(--color-text-light);
  border-style: dashed;
}

.k-block-type-form-display-main {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  min-width: 0;
}

.k-block-type-form-file-icon {
  opacity: 0.55;
}

.k-block-type-form-display-text {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.k-block-type-form-clear {
  border: 0;
  background: transparent;
  color: var(--color-text-light);
  font-size: 1rem;
  cursor: pointer;
}

.k-block-type-form-schedule {
  position: absolute;
  right: 0.5rem;
  bottom: 0.2rem;
  font-size: 0.75rem;
  padding: 0.2rem 0.5rem;
  border-radius: 999px;
  background: #1d4ed8;
  color: #ffffff;
}
</style>
