<template>
	<div class="item" :id="itemId" :data-content="errorMessage" data-position="top center" @mouseover="$emit('mouseover', $event, itemId)">
		<div class="image">
			<div :class="{'ui active dimmer': loader_state}" style="z-index: 100">
				<div class="ui text loader" v-if="loader_state">Loading Image...</div>
				<img class="ui bordered rounded image" :class="{disabled: errorMessage}" :src="src">
			</div>
		</div>

		<div class="content">
			<div class="header" :class="{'ui red': errorMessage}">
				<i class="ui exclamation circle icon" v-if="errorMessage"></i>
				Page {{ pageNumber }}
			</div>
			<div class="meta">
				<span>Size: {{ fileSize(file.size) }}</span>
				<span>Dimension: {{ width }}px (width) x {{ height }}px (height)</span>
			</div>
			<div class="description">
				<div>File Name: {{ localName }}</div>
				<div>File Name will be changed to <b>{{ pagedName }}</b> after submission.</div>
			</div>
			<br>
			<div class="extra">
				<button type="button" class="ui tiny left floated labeled icon inverted red button" @click="$emit('remove', pageNumber - 1)">
					<i class="trash alternate icon"></i>
					Remove
				</button>

				<div class="right floated ui buttons">
					<button type="button" class="ui compact labeled icon inverted orange button" v-if="identifyListOrder('down')" @click="$emit('move', 'down', pageNumber - 1)">
						<i class="chevron down icon"></i>
						Move Down
					</button>
					<div class="Page Number"></div>
					<button type="button" class="ui compact labeled icon inverted green button" v-if="identifyListOrder('up')" @click="$emit('move', 'up', pageNumber - 1)">
						<i class="chevron up icon"></i>
						Move Up
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import PageItemMixin from '../../mixins/PageItemMixin.js';

	export default {
		mixins: [PageItemMixin],

		props: {
			file: {
				required: true	
			},

			order: {
				validator(value){
					return ['first', 'last', 'none',''].indexOf(value) !== -1;
				},
			},

			errorMessage: {
				type: String,
				default: ''
			}
		},

		data(){
			return {
				localName: '',
				src: '',
				loader_state: true
			}
		},

		methods: {
			identifyListOrder(button){
				if(this.order == 'none')
					return false;

				else if(button == 'down')
				{
					//down button is only enabled if this.order is anything other than 'last'
					if(this.order != 'last')
						return true;

					return false;
				}

				else if(button == 'up')
				{
					//up button is only enabled if this.order is anything other than 'first'
					if(this.order != 'first')
						return true;

					return false;
				}
			}
		},

		computed: {
			pagedName(){
				 let name = this.localName.split('.');
				 name[0] = this.pageNumber;

				 return name.join('.');
			},

			itemId(){
				return 'image_list_' + this.pageNumber;
			}
		},

		mounted(){
			let reader = new FileReader();

			this.localName = this.file.name;

			//https://serversideup.net/preview-file-uploads-with-axios-and-vuejs/
			reader.addEventListener("load", function() {
				this.loader_state = false;
				this.src = reader.result;

				let image = new Image();

				//https://stackoverflow.com/questions/5633264/javascript-get-image-dimensions
				image.onload = function() {
					this.width = image.width;
					this.height = image.height;
				}.bind(this);

				image.src = reader.result;
			}.bind(this), false);

			reader.readAsDataURL(this.file);
		}
	}
</script>