<template>
	<div class="column">
		<h4 class="ui header">
			<i class="ui caret right icon"></i>
			<i class="name">
				<slot></slot>
			</i>
			<u>
				<slot name="value"></slot>
			</u>
		</h4>
	</div>
</template>

<style scoped>
	i.ui.caret
	{
		display: inline;
	}

	.name
	{
		font-weight: normal;
	}
</style>