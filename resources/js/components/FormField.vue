<template>
  <default-field :field="field" :errors="errors" :show-help-text="true">
    <template slot="field">
      <div
        :style="{ height: field.height ? field.height : 'auto' }"
        class="relative"
      >
        <div
          v-if="loading"
          class="py-6 px-8 flex justify-center items-center absolute pin z-50 bg-white"
        >
          <loader class="text-60" />
        </div>
        <div v-if="this.field.selectAll" class="mb-2">
          <input
            type="checkbox"
            id="checkbox"
            class="checkbox"
            v-model="selectAll"
          />
          <label for="checkbox">{{ this.field.messageSelectAll }}</label>
        </div>
        <!--          <label v-if="this.field.selectAll"><input type="checkbox" class="checkbox mb-2 mr-2">{{this.field.messageSelectAll}}</label>-->
        <multi-select
          ref="multiselect"
          @open="() => repositionDropdown(true)"
          :options="options"
          v-bind="multiSelectProps"
          v-model="value"
          @[searchChange]="searchableFetchOptions"
          :loading="loading"
        >
          <template slot="noOptions">{{
            field.multiselectSlots.noOptions
          }}</template>
          <template slot="noResult">{{
            field.multiselectSlots.noResult
          }}</template>
        </multi-select>
      </div>
    </template>
  </default-field>
</template>

<script>
import { FormField, HandlesValidationErrors } from "laravel-nova";
import MultiSelect from "vue-multiselect";
import get from "lodash.get";

export default {
  mixins: [FormField, HandlesValidationErrors],

  props: ["resourceName", "resourceId", "field"],

  components: {
    MultiSelect,
  },
  data() {
    return {
      options: [],
      optionsLabel: "name",
      trackBy: "id",
      dependsOnValue: null,
      isDependant: false,
      shouldClear: false,
      loading: true,
      selectAll: false,
      debounceTimer: null,
      relatableDependenciesValues: {},
    };
  },
  mounted() {
    window.addEventListener("scroll", this.repositionDropdown);
  },
  destroyed() {
    window.removeEventListener("scroll", this.repositionDropdown);
  },
  created() {
    if (this.field.dependsOn !== undefined) {
      this.isDependant = true;
      this.registerDependencyWatchers(this.$root);
    }

    if (Array.isArray(this.field.relatableDependencies) && this.field.relatableDependencies.length) {
      for (let dependency of this.field.relatableDependencies) {
        this.relatableDependenciesValues[dependency]=null;
      }
      this.registerRelatableDependenciesWatchers(this.$root);
    }
  },

  computed: {
    multiSelectProps() {
      return {
        multiple: true,
        customLabel: (el) => get(el, this.optionsLabel),
        trackBy: this.trackBy,
        preselectFirst: false,
        class: this.errorClasses,
        placeholder: this.field.name,
        internalSearch: ! this.field.searchable,
        preserveSearch: this.field.searchable,
        clearOnSelect: ! this.field.searchable,
        ...(this.field.multiselectOptions ? this.field.multiselectOptions : {}),
      };
    },

    /**
     * Determine if the related resources is searchable
     */
    isSearchable() {
      return this.field.searchable;
    },

    searchChange: function() {
      return this.field.searchable ? "search-change" : null;
    },

    /**
     * Get the query params for getting available resources
     */
    queryParams() {
      return {
        params: {
          resourceId: this.resourceId,
        },
      }
    },

  },
  watch: {
    selectAll(value) {
      if (value) {
        this.value = [...this.options];
      } else {
        this.value = [];
      }
    },
  },
  methods: {
    repositionDropdown(onOpen = false) {
      const ms = this.$refs.multiselect;
      if (!ms) return;
      const el = ms.$el;
      const handlePositioning = () => {
        const { top, height, bottom } = el.getBoundingClientRect();
        if (onOpen) ms.$refs.list.scrollTop = 0;
        const fromBottom =
          (window.innerHeight || document.documentElement.clientHeight) -
          bottom;
        ms.$refs.list.style.position = "fixed";
        ms.$refs.list.style.width = `${el.clientWidth}px`;
        if (fromBottom < 300) {
          ms.$refs.list.style.top = "auto";
          ms.$refs.list.style.bottom = `${fromBottom + height}px`;
          ms.$refs.list.style["border-radius"] = "5px 5px 0 0";
        } else {
          ms.$refs.list.style.bottom = "auto";
          ms.$refs.list.style.top = `${top + height}px`;
          ms.$refs.list.style["border-radius"] = "0 0 5px 5px";
        }
      };
      if (onOpen) this.$nextTick(handlePositioning);
      else handlePositioning();
    },
    registerDependencyWatchers(root) {
      root.$children.forEach((component) => {
        if (this.componentIsDependency(component)) {
          if (component.selectedResourceId !== undefined) {
            let attribute = this.findWatchableComponentAttribute(component);
            component.$watch(attribute, this.dependencyWatcher, {
              immediate: true,
            });
            this.dependencyWatcher(component.selectedResourceId);
          }
        }
        this.registerDependencyWatchers(component);
      });
    },

    registerRelatableDependenciesWatchers(root) {
      root.$children.forEach((component) => {
        if (this.componentIsRelatableDependency(component)) {
          let watchable_component_attribute=this.findWatchableComponentAttributeForRelatableDependency(component);

          if (watchable_component_attribute !== undefined) {
            let initial_value=component.field.value || null;
            component.$watch(watchable_component_attribute, (value) => {
                if (watchable_component_attribute === "selectedResource") {
                  value = (value && value.value) || null;
                }
                this.relatableDependencyWatcher(component.field.attribute, value);
              }, {
              immediate: true,
            });
            if (component.selectedResourceId !== undefined) {
              //reset initial_value when using selectedResource attribute
              initial_value=component.selectedResourceId;
            }
            if (component.selectedResource) {
              initial_value=component.selectedResource.value || null;
            }
            this.relatableDependencyWatcher(component.field.attribute, initial_value);
          }
        }
        this.registerRelatableDependenciesWatchers(component);
      });
    },

    findWatchableComponentAttribute(component) {
      let attribute;
      if (component.field.component === "belongs-to-field") {
        attribute = "selectedResource";
      } else {
        attribute = "value";
      }
      return attribute;
    },

    findWatchableComponentAttributeForRelatableDependency(component) {
      let attribute;
      if (component.selectedResourceId !== undefined) {
        attribute = "selectedResource";
      } else if (component.value !== undefined) {
        attribute = "value";
      }
      return attribute;
    },

    componentIsDependency(component) {
      if (component.field === undefined) {
        return false;
      }
      return component.field.attribute === this.field.dependsOn;
    },

    componentIsRelatableDependency(component) {
      if (component.field === undefined) {
        return false;
      }
      for (let dependency of this.field.relatableDependencies) {
        if (component.field.attribute === dependency) {
          return true;
        }
      }
      return false;
    },

    dependencyWatcher(value) {
      if (value === this.dependsOnValue) {
        return;
      }
      this.dependsOnValue = value.value;
      this.fetchOptions();
    },

    relatableDependencyWatcher(attribute, value) {
      if (this.relatableDependenciesValues[attribute] === value) {
        return;
      }
      this.relatableDependenciesValues[attribute] =  value;
      this.loading = true;
      clearTimeout(this.debounceTimer);
      this.debounceTimer = setTimeout(() => {
        this.fetchOptions();
      }, this.field.debounce);
    },

    /*
     * Set the initial, internal value for the field.
     */
    setInitialValue() {
      this.optionsLabel = this.field.optionsLabel
        ? this.field.optionsLabel
        : "name";
      this.trackBy = this.field.trackBy ? this.field.trackBy : "id";
      this.value = this.field.value.map((el) => ({
        ...el,
        [this.optionsLabel]: get(el, this.optionsLabel),
      }));
      this.fetchOptions();
    },

    fetchOptions(query="") {
      if (this.field.options) {
        this.options = this.field.options;
        this.loading = false;
        return;
      }

      if (this.isSearchable && ! query) {
        //inital empty options and keep previously loaded options
        this.loading = false;
        return;
      }

      let qParams={ ...this.queryParams };

      if (query) {
        qParams.params.search=query;
      }

      for (let dependency in this.relatableDependenciesValues) {
        if (this.relatableDependenciesValues[dependency]) {
          qParams.params[_.camelCase('selected_' + dependency)]=this.relatableDependenciesValues[dependency];
        } else if (_.camelCase('selected_' + dependency) in qParams.params) {
          delete qParams.params[_.camelCase('selected_' + dependency)];
        }
      }

      let baseUrl = "/nova-vendor/belongs-to-many-field/";
      if (this.isDependant) {
        if (this.dependsOnValue) {
          this.loading = true;
          Nova.request().get(
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
              this.field.dependsOnKey,
            qParams
          ).then((data) => {
            this.options = data.data;
            this.loading = false;
          });
        } else {
          this.options = [];
          this.loading = false;
        }
      } else {
        Nova.request().get(
          baseUrl +
            this.resourceName +
            "/" +
            "options/" +
            this.field.attribute +
            "/" +
            this.optionsLabel,
          qParams
        ).then((data) => {
          this.options = data.data;
          this.loading = false;
        });
      }
    },

    searchableFetchOptions (query) {
      this.loading = true;
      clearTimeout(this.debounceTimer);
      this.debounceTimer = setTimeout(() => {
        this.fetchOptions(query);
      }, this.field.debounce);
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
      this.$nextTick(() => this.repositionDropdown());
    },
  },
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style type="text/css">
.multiselect__placeholder {
  font-size: 1rem;
  color: var(--70) !important;
  margin-left: 4px;
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
