

//window.Vue.config.productionTip = false;

/* eslint-disable no-new */
new window.Vue({
  el: '#app',
  data() {
    return {
      model: {
            firstName: 'Bruno',
          },
      state: {},
      valid: false,
      schema: {},
      uiSchema: [],
      loaded: false,
      delay: 5000,
    };
  },
  methods: {
    onChange(value) {
      this.model = value;
    },
    onChangeState(value) {
      this.state = value;
    },
    onValidated(value) {
      this.valid = value;
    },
    getUiSchemaFromAPI() {
            var params = params || {};
            var self = this;
            var ajaxUrl = '/assets/components/dimmy/rest/Fields';
            params['gattung'] = 'Fuhrpark';
            return axios.get(ajaxUrl,{params:params})
            .then(function (response) {
                //self.$set(self.fahrten,params.returntype,response.data.results);
                //make sure, the model gets updated, nested properties, which are not defined in the data, are not watched
                //self.$forceUpdate();
                return response.data.results;
            })
            .catch(function (error) {
                console.log(error);
            });         

    },
    getSchemaFromAPI() {
      return new Promise((resolve, reject) => {
        setTimeout(() => {
          resolve({
            type: 'object',
            required: ['firstName'],
            properties: {
              firstName: {
                type: 'string',
              },
            },
          });
        }, this.delay);
      });
    },
    getDataFromAPI() {
      return new Promise((resolve, reject) => {
        setTimeout(() => {
          resolve({
            firstName: 'Tobias',
          });
        }, this.delay);
      });
    },
    getForm() {
      // Reset properties
      this.uiSchema = [];
      this.schema = [];
      //this.model = [];

      // Set form as not loaded
      this.loaded = false;

      // Get the data from the API
      return Promise.all([
        this.getUiSchemaFromAPI(),
        this.getSchemaFromAPI(),
        //this.getDataFromAPI(),
      ]).then(([uiSchema, schema, model]) => {
        // Update the form properties with data from the API
        this.uiSchema = uiSchema;
        this.schema = schema;
        //this.model = model;

        // Set form as loaded
        this.loaded = true;
      });
    },
  },
  created() {
    this.getForm();
  }
});
