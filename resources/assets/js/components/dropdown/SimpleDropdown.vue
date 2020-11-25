<template>
	<div class="ui compact menu">
		<div class="ui simple dropdown item">
			<slot name="label"></slot>
    		<div class="menu">
    			<slot name="items"></slot>
    		</div>
    	</div>
	</div>
</template>