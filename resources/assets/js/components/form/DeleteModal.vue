<template>
	<form :action="formAction" method="POST" :id="id" class="ui basic modal">
		<slot></slot>
		<div class="ui icon header">
			<i class="trash alternate outline icon"></i>
			Remove {{ modalTitle }}
		</div>
		<div class="content">
			<p>
				Are you sure you want to remove <span style="text-transform: uppercase; text-decoration: underline; color: red; font-weight: bold;">{{ deleteName }}</span>? 
				<slot name="additional_message"></slot>
			</p>
		</div>
		<div class="actions">
			<div class="ui grey basic cancel inverted button" @click="$emit('close')">
				<i class="remove icon"></i>
				No
			</div>
			<button type="submit" class="ui red ok inverted button">
				<i class="checkmark icon"></i>
				Yes
			</button>
		</div>
	</form>
</template>

<script>
	export default {
		props: ['formAction', 'id', 'modalTitle', 'deleteName'],

		beforeUpdate(){
			if(this.formAction != "" && this.deleteName != "")
				$('#' + this.id)
					.modal('setting', 'closable', false)
					.modal('show');
		}
	}
</script>