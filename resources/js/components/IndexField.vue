<template>
  <div>
    <span v-for="(resource, key) in field.value">
      <a :href="field.attribute +'/' + resource.id" class="no-underline dim text-primary font-bold">
        {{resource[field.optionsLabel]}}
        <span v-if="key < (field.value.length - 1)">,</span>
      </a>
    </span>
  </div>
</template>

<script>
export default {
  props: ["resourceName", "field"]
};
</script>
