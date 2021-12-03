import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'
import { UserConfig } from 'vite'

const config: UserConfig = {
	resolve: {
		alias: {
			'@/src': resolve(__dirname, 'src'),
		},
	},
	plugins: [
		vue(),
	],
	define: {
		// Improve Vue i18n tree-shaking
		__VUE_I18N_LEGACY_API__: false,
		__VUE_I18N_FULL_INSTALL__: false,
		__INTLIFY_PROD_DEVTOOLS__: false,
	},
	server: {
		port: 8080,
		cors: true,
		https: {
			key: process.env.SSL_KEY_PATH,
			cert: process.env.SSL_CERT_PATH,
		},
		hmr: {
			overlay: false,
		},
	},
	clearScreen: false,
}

export default config
