<script>
export default {
  props: {
    content: Object,
    endpoints: Object,
    fieldset: Object,
  },

  computed: {
    formFieldComponent() {
      const field = this.fieldset?.fields?.form;
      if (!field || !field.type) return null;
      return "k-" + field.type + "-field";
    },

    formValue() {
      return this.content?.form || null;
    },

    formLabel() {
      const value = this.formValue;
      if (!value) return "Kein Formular ausgewählt";
      if (Array.isArray(value) && value[0]?.text) return value[0].text;
      if (value?.text) return value.text;
      if (value?.title) return value.title;
      if (typeof value === "string") return value;
      return "Formular";
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
    updateForm(value) {
      this.$emit("update", {
        ...this.content,
        form: value,
      });
    },

    open() {
      this.$emit("open");
    },
  },
};
</script>

<template>
  <div class="k-block-type-form" @dblclick="open">
    <div class="k-block-type-form-body">

      <!-- Titel -->
      <div class="k-block-type-form-title">
        <span style="display:inline-flex;align-items:center;">
          <svg width="18" height="18" style="margin-right:4px;opacity:0.7;" viewBox="0 0 24 24">
            <path fill="currentColor" d="M17 2a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zm0 2H7v16h10zm-2 3v2H9V7zm0 4v2H9v-2zm0 4v2H9v-2z"/>
          </svg>
          {{ formLabel }}
        </span>

        <!-- Fallback Button -->
        <button
            class="k-block-type-form-select"
            @click.stop="open"
            type="button"
        >
          Auswählen
        </button>
      </div>

      <!-- ✅ INLINE FIELD -->
      <div v-if="formFieldComponent" class="k-block-type-form-field">
        <component
            :is="formFieldComponent"
            v-bind="fieldset.fields.form"
            :value="formValue"
            :endpoints="endpoints"
            @input="updateForm"
        />
      </div>

      <!-- Schedule -->
      <div v-if="scheduleLabel" class="k-block-type-form-schedule">
        {{ scheduleLabel }}
      </div>
    </div>
  </div>
</template>

<style scoped>
.k-block-type-form {
  position: relative;
  padding-bottom: 2.5rem;
}

.k-block-type-form-body {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.k-block-type-form-title {
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.k-block-type-form-select {
  background: #18181b;
  color: #fff;
  border: none;
  border-radius: 6px;
  padding: 0.2rem 0.7rem;
  font-size: 0.9em;
  cursor: pointer;
  transition: background 0.2s;
}

.k-block-type-form-select:hover {
  background: #27272a;
}

.k-block-type-form-field {
  margin-top: 0.25rem;
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