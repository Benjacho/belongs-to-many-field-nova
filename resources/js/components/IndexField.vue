<template>
  <div>
    <span v-for="(resource, key) in field.value" class="single">
      <Link
        @click.stop
        :href="$url(`/resources/${field.resourceNameRelationship}/${resource.id}`)"
        class="link-default"
        v-if="field.viewable"
        >
        {{ get(resource, field.optionsLabel) }}
      </Link>
      <span v-else>{{ get(resource, field.optionsLabel) }}</span>
    </span>
  </div>
</template>

<script>
export default {
  name: "IndexField",
  props: ["resourceName", "field"],
  methods: {
    get(object, path, defaultValue) {
      return _.get(object, path, defaultValue);
    },
  },
};
</script>

<style lang="scss" scoped>
.single:not(:last-of-type) {
  &:after {
    content: ", ";
  }
}
</style>
