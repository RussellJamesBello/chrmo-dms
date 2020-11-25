<template>
	<div class="ui fullscreen modal" :id="modalId">
		<i class="close icon" @click="emitClosed()"></i>
		<div class="header">
			Page {{ currentPageNumber }}
		</div>
		<div class="scrolling image content">
			<div class="ui fluid image">
				<img id="pageImage" :src="currentPageUrl">
			</div>


		</div>
		<div class="actions" style="overflow: auto;">
			<button class="ui inverted blue left floated icon button" @click="paginationButtonClicked('left')">
				<i class="arrow left icon"></i>
			</button>

			<button class="ui inverted blue right floated icon button" @click="paginationButtonClicked('right')">
				<i class="arrow right icon"></i>
			</button>
		</div>
	</div>
</template>

<script>
	export default {
		props: {
			modalId: {
				type: String,
				required: true
			},

			modalShow: {
				type: Boolean,
				default: false
			},

			clickedPage: {
				type: String,
				default: null
			},

			pageUrl: {
				type: String,
				required: true
			},

			pages: {
				type: Array,
				required: true
			}
		},

		data(){
			return {
				previousClicked: false,
				nextClicked: false,
				currentPage: null
			};
		},

		methods: {
			emitClosed(){
				this.previousClicked = false;
				this.nextClicked = false;
				this.currentPage = null;
				
				this.$emit('closed');
			},

			paginationButtonClicked(direction)
			{
				if(direction == 'left')
				{
					let previous_index = this.getCurrentPageIndex() - 1;

					if(previous_index >= 0)
					{
						this.nextClicked = false;
						this.previousClicked = true;
						this.currentPage = this.pages[previous_index].id;
					}
				}

				else if(direction == 'right')
				{
					let next_index = this.getCurrentPageIndex() + 1;

					if(next_index <= this.pages.length - 1)
					{
						this.previousClicked = false;
						this.nextClicked = true;
						this.currentPage = this.pages[next_index].id;
					}
				}
			},

			getCurrentPageIndex(){
				return this.pages.findIndex(function(page){
					if(this.currentPage == null)
						return page.id == this.clickedPage;

					else
						return page.id == this.currentPage;
				}, this);
			}
		},

		computed: {
			currentPageUrl(){
				if(this.clickedPage != null)
				{
					if(this.currentPage != null)
						return this.pageUrl + this.currentPage;

					else
						return this.pageUrl + this.clickedPage;
				}
					
				else
					return '';
			},

			currentPageNumber(){
				if(this.clickedPage != null)
					return this.pages[this.getCurrentPageIndex()].page_number;
				else
					return '';
			}
		},

		updated(){
			if(this.modalShow == true)
				$('#' + this.modalId)
					.modal('setting', 'closable', false)
					.modal('setting', 'autofocus', false)
					.modal('setting', 'transition', 'fade')
					.modal('setting', 'duration', 750)
					.modal('show');
		}
	}
</script>