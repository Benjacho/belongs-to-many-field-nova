<template>
  <default-field :field="field" :errors="errors">
    <template slot="field">
      <div :style="{height: field.height ? field.height : 'auto'}" class="relative">
        <div
          v-if="loading"
          class="py-6 px-8 flex justify-center items-center absolute pin z-50 bg-white"
        >
          <loader class="text-60"/>
        </div>
        <multi-select :options="options" v-bind="multiSelectProps" v-model="value"/>
      </div>
    </template>
  </default-field>
</template>

<script>
  import {FormField, HandlesValidationErrors} from "laravel-nova";
  import MultiSelect from "vue-multiselect";

  export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ["resourceName", "resourceId", "field"],

    components: {
      MultiSelect
    },
    data() {
      return {
        options: [],
        optionsLabel: "name",
        dependsOnValue: null,
        isDependant: false,
        loading: true
      };
    },

    created() {
      if (this.field.dependsOn !== undefined) {
        this.isDependant = true;
        this.registerDependencyWatchers(this.$root)
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
          ...(this.field.multiselectOptions ? this.field.multiselectOptions : {})
        };
      }
    },

    methods: {
      registerDependencyWatchers(root) {
        root.$children.forEach(component => {
          if (this.componentIsDependency(component)) {
            if (component.selectedResourceId !== undefined) {
              component.$watch('selectedResourceId', this.dependencyWatcher, {immediate: true});
              this.dependencyWatcher(component.selectedResourceId)
            }
          }
          this.registerDependencyWatchers(component)
        })
      },

      componentIsDependency(component) {
        if (component.field === undefined) {
          return false
        }
        return component.field.attribute === this.field.dependsOn
      },

      dependencyWatcher(value) {
        this.abc();
        if (value === this.dependsOnValue) {
          return
        }
        this.dependsOnValue = value;
        this.fetchOptions()
      },
      /*
       * Set the initial, internal value for the field.
       */
      setInitialValue() {
        this.optionsLabel = this.field.optionsLabel
          ? this.field.optionsLabel
          : "name";
        this.value = this.field.value || "";
        this.fetchOptions();
      },

      fetchOptions() {
        if (this.field.options) {
          this.options = this.field.options;
          this.loading = false;
          return;
        }

        let baseUrl = "/nova-vendor/belongs-to-many-field/";

        if (this.isDependant) {
          if (this.dependsOnValue) {
            Nova.request(
              baseUrl +
              this.resourceName +
              "/" +
              "options/" +
              this.field.attribute +
              "/" +
              this.optionsLabel +
              "/" +
              this.dependsOnValue +
              "/" +
              this.field.dependsOnKey
            ).then(data => {
              this.options = data.data;
              this.loading = false;
            });
          } else {
            this.options = [];
            this.loading = false;
          }
        } else {
          Nova.request(
            baseUrl +
            this.resourceName +
            "/" +
            "options/" +
            this.field.attribute +
            "/" +
            this.optionsLabel
          ).then(data => {
            this.options = data.data;
            this.loading = false;
          });
        }
      },

      /**
       * Fill the given FormData object with the field's internal value.
       */
      fill(formData) {
        formData.append(this.field.attribute, JSON.stringify(this.value) || "");
      },

      /**
       * Update the field's internal value.
       */
      handleChange(value) {
        this.value = value;
      }
    }
  };
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style type="text/css">
  .multiselect__placeholder {
    font-size: 1rem;
    color: var(--70) !important;
    margin-left: 4px
  }

  .multiselect__tags {
    border-width: 1px;
    border-color: var(--60);
  }

  .multiselect__select {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 35px;
  }

  .multiselect__select::before {
    border-width: 0; /* Reset default style */

    /*position: absolute;*/
    top: 0;
    width: 22px;
    height: 6px;
    margin: 0;

    background-repeat: no-repeat;
    background-position: center center;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 6"><path fill="%2335393C" fill-rule="nonzero" d="M8.293.293a1 1 0 0 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4A1 1 0 0 1 1.707.293L5 3.586 8.293.293z"/></svg>');
  }

</style>
