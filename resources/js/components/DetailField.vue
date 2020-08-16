<template>
  <panel-item :field="field">
    <div slot="value">
      <div v-if="field.showAsList">
        <div class="relative rounded-lg bg-white shadow border border-60">
          <div class="overflow-hidden rounded-b-lg rounded-t-lg">
            <div class="border-b border-50 cursor-text font-mono text-sm py-2 px-4"
                 v-for="(resource, key) in field.value"
                 :key="key">
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
              >{{resource['label']}}
              </router-link>
              <span v-else>{{resource['label']}}</span>
            </div>
          </div>
        </div>
      </div>
      <div v-else>
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
        >{{resource['label']}}</router-link>
        <span v-else>{{resource['label']}}</span>
      </span>
      </div>
    </div>
  </panel-item>
</template>

<script>
  export default {
    props: ["resource", "resourceName", "resourceId", "field"],
    mounted() {
      console.log( this.field ); 
      if (this.field.showAsList) {
        console.log(this.field)
      }
    }
  };
</script>

<style lang="scss" scoped>
  .single:not(:last-of-type) {
    &:after {
      content: ', ';
    }
  }
</style>
