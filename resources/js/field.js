import IndexField from "./components/IndexField";
import DetailField from "./components/DetailField";
import FormField from "./components/FormField";
Nova.booting((app, router, store) => {
  app.component("index-BelongsToManyField", IndexField);
  app.component("detail-BelongsToManyField", DetailField);
  app.component("form-BelongsToManyField", FormField);
});
