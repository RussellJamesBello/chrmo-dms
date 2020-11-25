<template>
	<field>
		<label :for="name">{{ label }}</label>
		<div class="ui fluid labeled input">
			<div :class="['ui label', labelClass]">
				{{ leftLabel }}
			</div>
			<input :id="name" :type="type" :name="name" :value="value" @input="$emit('input', $event.target.value)" :class="inputClass" :placeholder="placeholder">
		</div>
	</field>
</template>

<script>
	import InputFieldMixin from '../../mixins/InputFieldMixin.js';
	import FormElementMixin from '../../mixins/FormElementMixin.js';

	export default {
		mixins: [InputFieldMixin, FormElementMixin],

		props: ['labelClass', 'leftLabel']
	}
</script>

<style scoped>
	.label{
		font-size: 9.5pt !important;
		font-weight: normal !important;
		padding-top: 10pt !important;
	}
</style>