<template>
  <panel-item :field="field">
    <div slot="value">
      <span v-for="(resource, key) in field.value">
        <a
          :href="$router.options.base + '/resources/' + field.attribute +'/' + resource.id"
          class="no-underline dim text-primary font-bold"
        >
          {{resource[field.optionsLabel]}}
          <span v-if="key < (field.value.length - 1)">,</span>
        </a>
      </span>
    </div>
  </panel-item>
</template>

<script>
export default {
  props: ["resource", "resourceName", "resourceId", "field"]
};
</script>
