<template>
  <div>
    <span v-for="(resource, key) in field.value" class="single">
        <span v-if="field.viewable && field.value">
          <Link
              @click.stop
              :href="$url(`/resources/${field.resourceNameRelationship}/${resource.id}`)"
              class="link-default"
          >
            {{ get(resource, field.optionsLabel) }}
          </Link>
        </span>
      <span v-else>{{ get(resource, field.optionsLabel) }}</span>
    </span>
  </div>
</template>

<script>
import get from "lodash.get";
export default {
  name: "IndexField",
  props: ["resourceName", "field"],
  methods: {
    get(object, path, defaultValue) {
      return get(object, path, defaultValue);
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
