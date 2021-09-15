import {computed, defineComponent, ref} from 'vue'

import ColllectButton from '@/src/components/button/Button.vue'
import ColllectCheckbox from '@/src/components/checkbox/Checkbox.vue'
import ColllectInput from '@/src/components/input/Input.vue'
import ColllectRadio from '@/src/components/radio-group/radio/Radio.vue'
import ColllectRadioGroup from '@/src/components/radio-group/RadioGroup.vue'
import ColllectSelect from '@/src/components/select/Select.vue'

export default defineComponent({
	name: 'StyleguidePage',
	components: {
		ColllectButton,
		ColllectCheckbox,
		ColllectInput,
		ColllectRadio,
		ColllectRadioGroup,
		ColllectSelect,
	},
	setup() {
		const inputEmailValue = ref('')
		const inputPasswordValue = ref('')
		const checkboxRememberMeValue = ref(false)
		const radioValue = ref('')
		const selectValue = ref('')

		const colorLists = [
			{
				'#769bf7': '$cornflower-blue',
				'#7b80de': '$chetwode-blue',
				'#565da3': '$scampi',
				'#313a68': '$rhino',
			},
			{
				'#b49edc': '$cold-purple',
			},
			{
				'#ff3d94': '$wild-strawberry',
				'#f72b86': '$violet-red',
				'#d23636': '$persian-red',
			},
			{
				'#ffffff': '$white',
				'#f3f7fa': '$catskill-white',
				'#92a4b1': '$gull-gray',
				'#8598a5': '$regent-gray',
				'#31383c': '$outer-space',
				'#1d1f21': '$shark',
			},
		]

		const fonts = {
			'16px "Cocogoose"': '$font-cocogoose',
			'600 16px "Source Sans Pro"': '$font-source-sans-pro',
			'400 16px "Source Sans Pro"': '$font-source-sans-pro',
		}

		const isEmailErrored = computed<boolean>(() => {
			return inputEmailValue.value.length > 0 && !/\S+@\S+\.\S+/.test(inputEmailValue.value)
		})

		return {
			inputEmailValue,
			isEmailErrored,
			inputPasswordValue,
			checkboxRememberMeValue,
			radioValue,
			selectValue,
			colorLists,
			fonts,
		}
	},
})
