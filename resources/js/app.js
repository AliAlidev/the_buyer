require("./bootstrap");

import { createApp } from "vue";
import { StreamBarcodeReader } from "vue-barcode-reader";
import { ImageBarcodeReader } from "vue-barcode-reader";
import codereader from "./components/codereader.vue";

const app = createApp({});

// app.component("barcode", StreamBarcodeReader);
// app.component("imgbarcode", ImageBarcodeReader);
app.component("codereader", codereader);

const mountedApp = app.mount("#app");
