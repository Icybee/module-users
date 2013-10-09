
Brickrouge.Widget.Login = new Class({

	Extends: Brickrouge.Form,

	options: {

		useXHR: true
	},

	onSuccess: function(response)
	{
		var location = response.redirect_to || response.location // response.location is deprecated

		if (location)
		{
			window.location = location
		}
		else
		{
			window.location.reload(true)
		}
	}
})

Brickrouge.Widget.LoginCombo = new Class({

	initialize: function(el, options)
	{
		this.element = el = document.id(el)

		var forms = el.getElements('form')
		, login = forms[0]
		, nonce = forms[1]
		, loginSlide = new Fx.Slide(login, { duration: 'short', wrapper: login.getParent(), resetHeight: true })
		, nonceSlide = new Fx.Slide(nonce, { duration: 'short', wrapper: nonce.getParent(), resetHeight: true })
		, shake

		function nonceIn()
		{
			nonce.get('widget').clearAlert()
			loginSlide.slideOut().chain(nonceSlide.slideIn.bind(nonceSlide))

			return nonceSlide;
		}

		function nonceOut()
		{
			nonceSlide.slideOut().chain(loginSlide.slideIn.bind(loginSlide))

			return loginSlide
		}

		login.getElement('a').addEvent('click', function(ev) {

			ev.stop()

			nonceIn()
		})

		nonce.getElement('a').addEvent('click', function(ev) {

			ev.stop()

			nonceOut()
		})

		shake = (function (target, amplitude, duration)
		{
			target = document.id(target)
			target.setStyle('position', 'relative')

			var fx = new Fx.Tween(target, { property: 'left', duration: duration / 5 })

			return function()
			{
				fx.start(-amplitude).chain
				(
					function () { this.start(amplitude) },
					function () { this.start(-amplitude) },
					function () { this.start(amplitude) },
					function () { this.start(0) }
				)
			}

		}) (el.getParent('shakable') || el, 50, 200)

		login.get('widget').addEvent('failure', shake)
		nonce.get('widget').addEvent('success', function(response) {

			this.alert(response.message, 'success')
			this.element.reset()

			nonceOut.delay(6000)

		})
	}
})