<template>
	<div class="field">
		<div class="ui radio checkbox">
			<input type="radio" :name="name" :value="value" @change="$emit('input', $event.target.value)" class="hidden" v-bind="dynamicAttributes">
			<label>{{ label }}</label>
		</div>
	</div>
</template>

<script>
	import FormElementMixin from '../../mixins/FormElementMixin.js';

	export default {
		mixins: [FormElementMixin],
		props: ['value']
	}
</script>