<template>
	<field class="ui search">
		<label :for="name">{{ label }}</label>

		<div class="ui fluid icon input">
			<input class="prompt" :id="name" type="text" :name="name" :value="value" @input="$emit('input', $event.target.value)" v-bind="dynamicAttributes">
			<i class="search icon"></i>
		</div>

		<div class="results" style="width: 100%; text-align: center;"></div>
	</field>
</template>

<script>
	import FormElementMixin from '../../mixins/FormElementMixin.js';

	export default {
		mixins: [FormElementMixin],

		props: ['value']
	}
</script>