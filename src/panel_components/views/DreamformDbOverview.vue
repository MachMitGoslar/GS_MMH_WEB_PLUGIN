<template>
  <k-panel-inside class="k-dreamform-db-overview">
    <k-header> Formular-Eingänge </k-header>

    <div v-if="forms.length === 0" class="dfdb-empty">
      <k-icon type="email" />
      <p>Noch keine Formular-Eingänge vorhanden.</p>
    </div>

    <div v-else class="dfdb-forms">
      <a
        v-for="form in forms"
        :key="form.slug"
        :href="'/panel/formular-eingaenge/' + form.slug"
        class="dfdb-form-card"
        @click.prevent="$panel.open('formular-eingaenge/' + form.slug)"
      >
        <div class="dfdb-form-card-icon">
          <k-icon type="form" />
        </div>
        <div class="dfdb-form-card-content">
          <h3>{{ form.title }}</h3>
          <p class="dfdb-meta">
            {{ form.count }} {{ form.count === 1 ? 'Eintrag' : 'Einträge' }}
            <span v-if="form.last"> · Letzter: {{ formatDate(form.last) }}</span>
          </p>
        </div>
        <div class="dfdb-form-card-arrow">
          <k-icon type="angle-right" />
        </div>
      </a>
    </div>
  </k-panel-inside>
</template>

<script>
export default {
  props: {
    forms: Array,
  },
  methods: {
    formatDate(dateStr) {
      if (!dateStr) return '';
      const d = new Date(dateStr);
      return d.toLocaleDateString('de-DE', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      });
    },
  },
};
</script>

<style>
.dfdb-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  color: var(--color-text-dimmed);
  background: var(--color-background);
  border-radius: var(--rounded);
  border: 1px solid var(--color-border);
}

.dfdb-empty .k-icon {
  width: 3rem;
  height: 3rem;
  margin-bottom: 0.75rem;
}

.dfdb-forms {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.dfdb-form-card {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 1.25rem;
  background: var(--color-background);
  border: 1px solid var(--color-border);
  border-radius: var(--rounded);
  cursor: pointer;
  transition:
    border-color 0.15s,
    box-shadow 0.15s;
  text-decoration: none;
  color: inherit;
}

.dfdb-form-card:hover {
  border-color: var(--color-focus);
  box-shadow: var(--shadow);
}

.dfdb-form-card-icon {
  flex-shrink: 0;
  width: 2.5rem;
  height: 2.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--color-gray-200);
  border-radius: var(--rounded);
}

.dfdb-form-card-content {
  flex: 1;
}

.dfdb-form-card-content h3 {
  font-size: var(--text-base);
  font-weight: 600;
  margin: 0;
}

.dfdb-meta {
  font-size: var(--text-sm);
  color: var(--color-text-dimmed);
  margin: 0.25rem 0 0 0;
}

.dfdb-form-card-arrow {
  flex-shrink: 0;
  color: var(--color-text-dimmed);
}
</style>
