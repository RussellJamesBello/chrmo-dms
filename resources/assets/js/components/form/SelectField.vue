<template>
	<field>
		<label :for="name">{{ label }}</label>
		<select :id="name" :name="name" :value="value" @input="$emit('input', $event.target.value)" v-bind="dynamicAttributes">
			<option :disabled="defaultIsDisabled" value="">{{ disabledText }}</option>
			<option v-for="option in normalizedOption" :value="option.value">
				{{ option.text }}
			</option>
		</select>
	</field>
</template>

<script>
	import FormElementMixin from '../../mixins/FormElementMixin.js';
	import DropDownMixin from '../../mixins/DropDownMixin.js';

	export default {
		mixins: [FormElementMixin, DropDownMixin],

		props: {
			defaultIsDisabled: {
				type: Boolean,
				default: false
			},

			disabledText: {
				type: String,
				default: ''
			}
		},

		computed: {
			normalizedOption() {
				let options = [];
				let length = this.options.length;

				for(let i = 0; i < length; i++)
					options.push({
									text: this.options[i].text == null ? this.options[i].value : this.options[i].text, 
									value: this.options[i].value
								});

				return options;
			}
		}
	}
</script>