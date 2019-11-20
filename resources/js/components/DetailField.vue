<template>
  <panel-item :field="field">
    <div slot="value">
      <span v-for="(resource, key) in field.value">
        <router-link
            :to="{
          name: 'detail',
          params: {
            resourceName: field.resourceNameRelationship,
            resourceId: resource.id,
          },
        }"
            class="no-underline dim text-primary font-bold"
        >
        {{resource[field.optionsLabel]}}
        <span v-if="key < (field.value.length - 1)">,</span>
      </router-link>
      </span>
    </div>
  </panel-item>
</template>

<script>
export default {
  props: ["resource", "resourceName", "resourceId", "field"]
};
</script>
