<script>
    export default {
        computed: {
            textField() {
                return this.field("text");
            }
        }
    }
</script>

  <template>
    <div :class="'k-block-type-box box-' + content.boxtype">
      <k-writer
        class="label"
        ref="textbox"
        :marks="textField.marks"
        :value="content.text"
        :placeholder="textField.placeholder || 'Enter some stuff…'"
        @input="update({ text: $event })"
      />
      <k-icon
        v-if="content.type !== 'neutral'"
        class="k-block-type-box-icon"
        :type="content.boxtype"
      />
    </div>
</template>