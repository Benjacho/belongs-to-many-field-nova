<template>
    <default-field :field="field" :errors="errors">
        <template slot="field">
            <div :style="{height: field.height ? field.height : 'auto'}" class="relative">
            <div v-if="loading" class="py-6 px-8 flex justify-center items-center absolute pin z-50 bg-white">
                <loader class="text-60"/>
            </div>
            <multi-select
                :options="options"
                v-bind="multiSelectProps"
                v-model="value"
            />
            </div>
        </template>
    </default-field>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova';
import MultiSelect from 'vue-multiselect';

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ['resourceName', 'resourceId', 'field'],

    components: {
        MultiSelect
    },
    data(){
        return {
            options: [],
            optionsLabel: "name",
            loading: true,
        }
    },

  computed: {
    multiSelectProps() {
      return {
        multiple: true,
	      label: this.optionsLabel,
	      trackBy: this.optionsLabel,
	      class: this.errorClasses,
	      placeholder: this.field.name,
        ...(this.field.multiselectOptions ? this.field.multiselectOptions : {}),
      }
    }
  },

    methods: {
        /*
         * Set the initial, internal value for the field.
         */
        setInitialValue() {
            this.optionsLabel = this.field.optionsLabel ? this.field.optionsLabel : 'name';
            this.value = this.field.value || ''
            this.fetchOptions();
        },

      fetchOptions () {
        if (this.field.options) {
          this.options = this.field.options
          this.loading = false;
          return;
        }

        let baseUrl = '/nova-vendor/belongs-to-many-field/'

        Nova.request(baseUrl + this.resourceName + '/' + 'options/' + this.field.attribute)
          .then((data) => {
            this.options = data.data;
            this.loading = false;
          })
      },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            formData.append(this.field.attribute, JSON.stringify(this.value) || '')
        },

        /**
         * Update the field's internal value.
         */
        handleChange(value) {
            this.value = value
        },
    },
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
