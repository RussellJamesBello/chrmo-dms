<template>
	<button :type="type" class="ui animated button" tabindex="0" @click="$emit('click', $event)">
		<div class="visible content">
			<i :class="visibleIcon + ' icon'"></i>
		</div>
		<div class="hidden content">
			{{ hiddenText }}
		</div>
	</button>
</template>

<script>
	import ButtonMixin from '../../mixins/ButtonMixin.js';

	export default {
		mixins: [ButtonMixin],

		props: {
			visibleIcon: {
				required: true
			},

			hiddenText: {
				required: true
			}
		}
	}
</script>