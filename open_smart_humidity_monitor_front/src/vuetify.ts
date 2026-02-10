import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

export const vuetify = createVuetify({
  components,
  directives,
  theme: {
    defaultTheme: 'dashboard',
    themes: {
      dashboard: {
        dark: false,
        colors: {
          primary: '#1976D2',
          secondary: '#78909C',
          surface: '#FAFAFA',
          background: '#ECEFF1',
        },
      },
    },
  },
  defaults: {
    VCard: {
      rounded: 'xl',
      elevation: 2,
    },
    VBtn: {
      rounded: 'lg',
    },
  },
})
