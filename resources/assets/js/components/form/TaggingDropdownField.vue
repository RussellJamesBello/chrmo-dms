<template>
	<field>
		<label>{{ label }}</label>
		<div class="ui fluid multiple search selection dropdown">
			<input :name="name" type="hidden" :value="value" @change="$emit('change', $event.target.value)">
			<i class="dropdown icon"></i>
			<div class="default text">{{ defaultText }}</div>
		</div>
	</field>
</template>

<script>
	import FormElementMixin from '../../mixins/FormElementMixin.js';
	import DropDownMixin from '../../mixins/DropDownMixin.js';

	export default {
		mixins: [FormElementMixin, DropDownMixin],

		model: {
			event: 'change'
		},

		props: {
			defaultText: {
				type: String,
				required: true
			},
			options: ''
		}
	}
</script>