<template>
  <k-section class="k-newsletter-recipients-section" headline="Newsletter-Empfänger">
    <template #options>
      <k-button icon="add" size="sm" variant="filled" @click="createRecipient">
        Empfänger hinzufügen
      </k-button>
    </template>

    <div v-if="isLoading" class="newsletter-recipients-empty">
      <k-loader />
      <p>Empfänger werden geladen.</p>
    </div>

    <div v-else-if="visibleRecipients.length === 0" class="newsletter-recipients-empty">
      <k-icon type="email" />
      <p>Noch keine Empfänger eingetragen.</p>
    </div>

    <div v-else class="newsletter-recipients-table-wrap">
      <table class="newsletter-recipients-table">
        <thead>
          <tr>
            <th>Vorname</th>
            <th>Nachname</th>
            <th>E-Mail-Adresse</th>
            <th class="newsletter-recipients-actions"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="recipient in visibleRecipients" :key="recipient.id">
            <td>{{ recipient.first_name }}</td>
            <td>{{ recipient.last_name }}</td>
            <td>
              <a :href="'mailto:' + recipient.email">{{ recipient.email }}</a>
            </td>
            <td class="newsletter-recipients-actions">
              <k-button-group>
                <k-button icon="edit" size="xs" @click="editRecipient(recipient.id)" />
                <k-button
                  icon="trash"
                  size="xs"
                  theme="negative"
                  @click="deleteRecipient(recipient.id)"
                />
              </k-button-group>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </k-section>
</template>

<script>
export default {
  props: {
    recipients: {
      type: Array,
      default: () => [],
    },
    total: {
      type: Number,
      default: 0,
    },
  },
  data() {
    return {
      loadedRecipients: this.recipients,
      isLoading: false,
    };
  },
  computed: {
    visibleRecipients() {
      return this.loadedRecipients || [];
    },
  },
  mounted() {
    this.loadRecipients();
  },
  methods: {
    async loadRecipients() {
      this.isLoading = true;

      try {
        const response = await this.$api.get('gs-mmh-web-plugin/newsletter-recipients');
        this.loadedRecipients = Array.isArray(response?.recipients) ? response.recipients : [];
      } catch (error) {
        this.$panel.notification.error(
          error?.message || 'Empfänger konnten nicht geladen werden.'
        );
      } finally {
        this.isLoading = false;
      }
    },
    createRecipient() {
      this.openDialog('newsletter-recipients/create');
    },
    editRecipient(id) {
      this.openDialog('newsletter-recipients/' + id);
    },
    deleteRecipient(id) {
      this.openDialog('newsletter-recipients/' + id + '/delete');
    },
    openDialog(path) {
      this.$panel.dialog.open(path, {
        on: {
          success: async response => {
            if (response && response.message) {
              this.$panel.notification.success(response.message);
            }

            await this.loadRecipients();
            this.$panel.dialog.close();
          },
        },
      });
    },
  },
};
</script>

<style>
.newsletter-recipients-empty {
  display: grid;
  justify-items: center;
  gap: 0.75rem;
  padding: 2rem;
  color: var(--color-text-dimmed);
  background: var(--color-background);
  border: 1px solid var(--color-border);
  border-radius: var(--rounded);
}

.newsletter-recipients-table-wrap {
  overflow-x: auto;
  background: var(--color-background);
  border: 1px solid var(--color-border);
  border-radius: var(--rounded);
}

.newsletter-recipients-table {
  width: 100%;
  border-collapse: collapse;
}

.newsletter-recipients-table th,
.newsletter-recipients-table td {
  padding: 0.625rem 0.75rem;
  text-align: left;
  font-size: var(--text-sm);
  border-bottom: 1px solid var(--color-border);
}

.newsletter-recipients-table th {
  font-weight: 600;
  background: var(--color-gray-100);
  white-space: nowrap;
}

.newsletter-recipients-table tbody tr:hover {
  background: var(--color-gray-100);
}

.newsletter-recipients-actions {
  width: 5rem;
  text-align: right;
  white-space: nowrap;
}
</style>
