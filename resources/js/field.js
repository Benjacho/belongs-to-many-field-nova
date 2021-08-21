import IndexField from "./components/IndexField";
import DetailField from "./components/DetailField";
import FormField from "./components/FormField";

Nova.booting((Vue, router, store) => {
  Vue.component("index-BelongsToManyField", IndexField);
  Vue.component("detail-BelongsToManyField", DetailField);
  Vue.component("form-BelongsToManyField", FormField);
});
