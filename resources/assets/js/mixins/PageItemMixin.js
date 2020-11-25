export default {
	props: {
		pageNumber: {
			type: Number,
			required: true
		}
	},

	data(){
		return {
			width: '',
			height: '',
		}
	},

	methods: {
		fileSize(bytes){
			//https://gist.github.com/lanqy/5193417
			var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];

			if (bytes == 0) 
				return 'n/a';
			
			var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));

			if (i == 0) return bytes + ' ' + sizes[i];
				return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i];
		}
	}
}