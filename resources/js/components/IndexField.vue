<template>
  <div>
    <span v-for="(resource, key) in field.value" class="single">
      <router-link
        :to="{
          name: 'detail',
          params: {
            resourceName: field.resourceNameRelationship,
            resourceId: resource[field.keyField],
          },
        }"
        class="no-underline dim text-primary font-bold"
        v-if="field.viewable"
      >{{resource[field.optionsLabel]}}</router-link>
      <span v-else>{{resource[field.optionsLabel]}}</span>
    </span>
  </div>
</template>

<script>
export default {
  props: ["resourceName", "field"]
};
</script>

<style lang="scss" scoped>
  .single:not(:last-of-type) {
    &:after {
      content: ', ';
    }
  }
</style>
