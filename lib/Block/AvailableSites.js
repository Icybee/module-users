window.addEvent('domready', function() {

	var form = document.body.querySelector('form[name="change-working-site"]')

	if (!form) return

	form.addEvent('submit', function() {

		form.action = form.querySelector('select').value

	})
})
