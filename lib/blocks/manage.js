manager.addEvent('ready', function() {

	manager.element.getElements('td.cell--is-activated input[type="checkbox"]').each(function(el) {

		el.addEvent('click', function(ev) {

			var destination = this.form[ICanBoogie.Operation.DESTINATION].value;

			new Request.API
			({
				url: destination + '/' + this.value + '/' + (this.checked ? 'activate' : 'deactivate'),

				onRequest: function()
				{
					this.disabled = true
				},

				onComplete: function()
				{
					this.disabled = false
				},

				onFailure: function(response)
				{
					this.checked = !this.checked
					this.fireEvent('change', {})
				}.bind(this)
			}).get()
		})
	})
})