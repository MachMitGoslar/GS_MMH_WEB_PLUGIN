<template>
  <k-panel-inside class="k-dreamform-db-form">
    <k-header>
      {{ formTitle }}
      <k-button-group slot="left">
        <k-button icon="angle-left" @click="$panel.open('formular-eingaenge')"> Zurück </k-button>
      </k-button-group>
    </k-header>

    <div v-if="submissions.length === 0" class="dfdb-empty">
      <k-icon type="email" />
      <p>Keine Einträge für dieses Formular.</p>
    </div>

    <div v-else>
      <div class="dfdb-table-wrap">
        <table class="dfdb-table">
          <thead>
            <tr>
              <th>Datum</th>
              <th v-for="col in displayColumns" :key="col">{{ formatCol(col) }}</th>
              <th class="dfdb-col-actions"></th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="sub in submissions"
              :key="sub.id"
              class="dfdb-row"
              @click="showDetail(sub.id)"
            >
              <td class="dfdb-col-date">{{ formatDate(sub.submittedAt) }}</td>
              <td v-for="col in displayColumns" :key="col">{{ truncate(sub.data[col]) }}</td>
              <td class="dfdb-col-actions" @click.stop>
                <k-button icon="trash" theme="negative" size="xs" @click="confirmDelete(sub.id)" />
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="pagination.pages > 1" class="dfdb-pagination">
        <k-button
          :disabled="pagination.page <= 1"
          icon="angle-left"
          @click="goToPage(pagination.page - 1)"
        >
          Zurück
        </k-button>
        <span class="dfdb-page-info">
          Seite {{ pagination.page }} von {{ pagination.pages }} ({{ pagination.total }} Einträge)
        </span>
        <k-button
          :disabled="pagination.page >= pagination.pages"
          icon="angle-right"
          @click="goToPage(pagination.page + 1)"
        >
          Weiter
        </k-button>
      </div>
    </div>
  </k-panel-inside>
</template>

<script>
export default {
  props: {
    formSlug: String,
    formTitle: String,
    resourceKey: String,
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
    formatCol(key) {
      return key.replace(/[-_]/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
    },
    truncate(value) {
      if (value === undefined || value === null) return '';
      const str = Array.isArray(value) ? value.join(', ') : String(value);
      return str.length > 60 ? str.substring(0, 60) + '…' : str;
    },
    showDetail(id) {
      this.$panel.dialog.open('dreamform-db/' + this.resourceKey + '/' + id);
    },
    confirmDelete(id) {
      this.$panel.dialog.open('dreamform-db/' + this.resourceKey + '/' + id + '/delete');
    },
    goToPage(page) {
      this.$panel.open('formular-eingaenge/' + this.formSlug + '?page=' + page);
    },
  },
};
</script>

<style>
.dfdb-table-wrap {
  overflow-x: auto;
  background: var(--color-background);
  border: 1px solid var(--color-border);
  border-radius: var(--rounded);
}

.dfdb-table {
  width: 100%;
  border-collapse: collapse;
}

.dfdb-table th,
.dfdb-table td {
  padding: 0.625rem 0.75rem;
  text-align: left;
  font-size: var(--text-sm);
  border-bottom: 1px solid var(--color-border);
}

.dfdb-table th {
  font-weight: 600;
  background: var(--color-gray-100);
  white-space: nowrap;
}

.dfdb-row {
  cursor: pointer;
}

.dfdb-row:hover {
  background: var(--color-gray-100);
}

.dfdb-col-date {
  white-space: nowrap;
}

.dfdb-col-actions {
  width: 3rem;
  text-align: center;
}

.dfdb-pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  padding: 1rem;
}

.dfdb-page-info {
  font-size: var(--text-sm);
  color: var(--color-text-dimmed);
}
</style>
