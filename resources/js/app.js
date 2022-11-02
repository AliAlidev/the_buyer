require("./bootstrap");

import { createApp } from "vue";
import { StreamBarcodeReader } from "vue-barcode-reader";
import { ImageBarcodeReader } from "vue-barcode-reader";

const app = createApp({
    methods: {
        onDecode(result) {
            alert(result);
            console.log(result);
        },
        onLoaded() {
            console.log('starting');
        },
    },
});

app.component("barcode", StreamBarcodeReader);
app.component("imgbarcode", ImageBarcodeReader );

const mountedApp = app.mount("#app");
