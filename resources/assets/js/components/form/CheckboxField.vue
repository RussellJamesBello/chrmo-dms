<template>
	<field>
		<div class="ui checkbox" :class="kind">
			<label>{{ label }}</label>
			<input type="checkbox" :name="name" :checked="checked" @change="$emit('change', $event.target.checked)" class="hidden" v-bind="dynamicAttributes">
		</div>
	</field>
</template>

<script>
	import FormElementMixin from '../../mixins/FormElementMixin.js';

	export default {
		mixins: [FormElementMixin],

		model: {
				prop: 'checked',
				event: 'change'
			},

		props: {
			kind: {
				validator: function (value) {
					return ['toggle', 'slider', ''].indexOf(value) !== -1;
				},
				default: ''
			},

			checked: {
				type: Boolean
			}
		}
	}
</script>