<template>
	<field>
		<label :for="name">{{ label }}</label>
		<input :id="name" :type="type" :name="name" :value="value" @input="$emit('input', $event.target.value, $event)" :class="inputClass" :placeholder="placeholder" v-bind="dynamicAttributes">
	</field>
</template>

<script>
	import InputFieldMixin from '../../mixins/InputFieldMixin.js';
	import FormElementMixin from '../../mixins/FormElementMixin.js';

	export default {
		mixins: [InputFieldMixin, FormElementMixin]
	}
</script>