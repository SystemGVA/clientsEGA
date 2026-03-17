// resources/js/plugins/vuetify.js
import "vuetify/styles";
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
//import { aliases, mdi } from "vuetify/iconsets/mdi-svg";
import { aliases, mdi } from "vuetify/iconsets/mdi";
import "@mdi/font/css/materialdesignicons.css";
import { PurpleTheme } from '@/plugins/LightTheme';

export default createVuetify({
    components,
    directives,
    theme: {
        defaultTheme: "PurpleTheme",
        themes: {
            PurpleTheme,
        },
    },
    defaults: {
        global: {
            style: {
                fontFamily: "Outfit, sans-serif",
            },
        },
    },
    icons: {
        defaultSet: "mdi",
        aliases,
        sets: { mdi },
    },
});
