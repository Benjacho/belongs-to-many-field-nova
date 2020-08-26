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
      >{{resourceValue(resource[field.optionsLabel])}}</router-link>
      <span v-else>{{resourceValue(resource[field.optionsLabel])}}</span>
    </span>
    </div>
</template>

<script>

    export default {
        props: ["resourceName", "field"],

        methods: {
            resourceValue(value) {
                if (value instanceof Object) {
                    return value[this.field.language]
                } else {
                    return value
                }
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
