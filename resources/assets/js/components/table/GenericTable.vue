<template>
	<table class="ui table">
		<thead>
			<slot name="head"></slot>
		</thead>

		<tbody>
			<slot name="body"></slot>
		</tbody>

		<tfoot>
			<slot name="foot"></slot>
		</tfoot>
	</table>
</template>