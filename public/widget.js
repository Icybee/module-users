!function (Brickrouge) {

	Brickrouge.Widget.Login = new Class({

		Extends: Brickrouge.Form,

		options: {

			useXHR: true
		},

		initialize: function(el, options)
		{
			this.parent(el, options)

			if (document.body.hasClass('page-slug-authenticate'))
			{
				this.element.elements.username.focus()
			}
		},

		success: function(response)
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
			var forms = el.getElements('form')
			, login = forms[0]
			, nonce = forms[1]
			, shake

			function zoomTransition(el, transition, to) {

				var scale = transition.scale[0] + (transition.scale[1] - transition.scale[0]) * to
				, opacity = transition.opacity[0] + (transition.opacity[1] - transition.opacity[0]) * to

				el.setStyles({

					transform: 'scale(' + scale + ')',
					'-moz-transform': 'scale(' + scale + ')',
					'-webkit-transform': 'scale(' + scale + ')',
					opacity: opacity,
					visibility: opacity ? 'visible' : 'hidden'

				})
			}

			function zoomOut(el) {

				var fx = new Fx(el)
				, transition = {

					scale: [1, .5],
					opacity: [1, 0]

				}

				fx.set = function(to) {

					zoomTransition(el, transition, to)

				}

				fx.start(0, 1)
			}

			function zoomIn(el) {

				var fx = new Fx(el)
				, transition = {

					scale: [1.5, 1],
					opacity: [0, 1]

				}

				fx.set = function(to) {

					zoomTransition(el, transition, to)

				}

				fx.start(0, 1)
			}

			function nonceIn()
			{
				Brickrouge.from(nonce).clearAlert()

				zoomOut(login)
				zoomIn(nonce)
			}

			function nonceOut()
			{
				zoomOut(nonce)
				zoomIn(login)
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

			Brickrouge.from(login).observe(Brickrouge.Form.EVENT_FAILURE, shake)
			Brickrouge.from(nonce).observe(Brickrouge.Form.EVENT_SUCCESS, () => {

				this.element.reset()

				nonceOut.delay(6000)

			})
		}
	})

	Brickrouge.register('user-login', (element, options) => {

		return new Brickrouge.Widget.Login(element, options)

	})

	Brickrouge.register('user-login-combo', (element, options) => {

		return new Brickrouge.Widget.LoginCombo(element, options)

	})

} (Brickrouge)
