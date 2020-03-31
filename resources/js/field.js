Nova.booting((Vue, router, store) => {
  Vue.component('index-BelongsToManyField', require('./components/IndexField'))
  Vue.component('detail-BelongsToManyField', require('./components/DetailField'))
  Vue.component('form-BelongsToManyField', require('./components/FormField'))
})
