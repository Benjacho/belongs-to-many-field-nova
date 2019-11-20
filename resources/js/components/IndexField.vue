<template>
  <div>
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
</template>

<script>
export default {
  props: ["resourceName", "field"]
};
</script>
