export default {
	props: {
		type: {
			validator(value){
		        return ['button', 'reset', 'submit'].indexOf(value) !== -1;
			}
		}
	}
}