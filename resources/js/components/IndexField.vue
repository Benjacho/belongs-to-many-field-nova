<template>
  <div>
    <span v-for="(resource, key) in field.value" class="single">
      <router-link
        :to="{
          name: 'detail',
          params: {
            resourceName: field.resourceNameRelationship,
            resourceId: resource.id,
          },
        }"
        class="no-underline dim text-primary font-bold"
        v-if="field.viewable"
      >{{get(resource, field.optionsLabel)}}</router-link>
      <span v-else>{{get(resource, field.optionsLabel)}}</span>
    </span>
  </div>
</template>

<script>
import get from 'lodash.get'
export default {
  props: ["resourceName", "field"],
  methods: {
    get(object, path, defaultValue) {
      return get(object, path, defaultValue);
    }
  },
};
</script>

<style lang="scss" scoped>
  .single:not(:last-of-type) {
    &:after {
      content: ', ';
    }
  }
</style>