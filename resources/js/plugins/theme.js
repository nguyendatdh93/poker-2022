import { config } from './config'

const mode = config('settings.theme.mode')

export default {
  dark: mode === 'transparent',
  options: {
    customProperties: true
  },
  themes: {
    [mode]: config('settings.theme.colors')
  }
}
