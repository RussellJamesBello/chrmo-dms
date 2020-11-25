<template>
	<div class="item">
		<div :id="page_id" class="image" @click="$emit('clicked-page', pageId)">
		</div>

		<div class="content">
			<div class="header">
				Page {{ pageNumber }}
			</div>
			<div class="meta">
				<span>Size: </span>
				<span>Dimension: {{ width }}px (width) x {{ height }}px (height)</span>
			</div>
			<div class="description">
				<div>File Name: {{ fileName }}</div>
			</div>
		</div>
	</div>
</template>

<script>
	import PageItemMixin from '../../mixins/PageItemMixin.js';

	export default {
		mixins: [PageItemMixin],

		props: {
			src: {
				type: String,
				required: true
			},

			fileName: {
				type: String,
				required: true
			},

			pageId: {
				type: String,
				required: true
			}
		},

		data(){
			return {
				size: '',
				page_id: 'page_' + this.pageNumber
			}
		},

		mounted(){
			let image = new Image();

			image.onload = function(){
				this.width = image.width;
				this.height = image.height;

				$('#' + this.page_id).append(image);
			}.bind(this);

			image.src = this.src;
		}
	}
</script>

<style scoped>
	.image
	{
		cursor: pointer;
	}
</style>