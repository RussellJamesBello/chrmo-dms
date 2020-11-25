import Field from '../components/form/Field.vue';

export default {
	components:{Field},

	computed: {
		dynamicAttributes() {
			return this.dynamicAttribs;
    	}
	},

	props: {
		label: {
			type: String,
			default: ''
		},

		name: {
			type: String,
			default: ''
		},

		dataOld: {
			type: String,
			default: ''
		},

		dynamicAttribs: {
			type: Object,
			default: null
		}
	}
}