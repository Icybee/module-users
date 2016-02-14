;!function() {

	var NAME_AS_USERNAME = 0
	, NAME_AS_FIRSTNAME = 1
	, NAME_AS_LASTNAME = 2
	, NAME_AS_FIRSTNAME_LASTNAME = 3
	, NAME_AS_LASTNAME_FIRSTNAME = 4
	, NAME_AS_NICKNAME = 5

	window.addEvent('domready', function() {

		var form = document.body.getElement('form.edit')

		if (!form)
		{
			return
		}

		var username = document.id(form.elements.username)

		if (!username)
		{
			return
		}

		var firstname = document.id(form.elements.firstname)
		, lastname = document.id(form.elements.lastname)
		, nickname = document.id(form.elements.nickname)
		, email = document.id(form.elements.email)
		, auto_username = !username.value
		, uid = form.elements[ICanBoogie.Operation.KEY] ? form.elements[ICanBoogie.Operation.KEY].value : null
		, usernameGroup = username.getParent('.form-group')
		, emailGroup = email.getParent('.form-group')
		, lastCheckUsername = null
		, lastCheckEmail = null

		var operation_check_unique = new Request.API
		({
			url: 'users/is_unique',

			onFailure: function(xhr, response)
			{
				usernameGroup[response.errors.username ? 'addClass' : 'removeClass']('error')
				emailGroup[response.errors.email ? 'addClass' : 'removeClass']('error')
			},

			onSuccess: function(response)
			{
				usernameGroup.removeClass('error')
				emailGroup.removeClass('error')
			}
		})

		function check_unique()
		{
			var value = username.value

			if (!value || (value == lastCheckUsername && lastCheckEmail == email.value)) return

			lastCheckUsername = value
			lastCheckEmail = email.value

			operation_check_unique.get({ uid: uid, username: value, email: email.value })
		}

		username.addEvent('keyup', function(ev) {

			auto_username = !username.value

			if (ev.key.length > 1 && ev.key != 'backspace' && ev.key != 'delete')
			{
				return
			}

			check_unique()
		})

		email.addEvent('keyup', function(ev) {

			if (ev.key.length > 1 && ev.key != 'backspace' && ev.key != 'delete')
			{
				return
			}

			check_unique()
		})

		function update()
		{
			if (!auto_username)
			{
				return
			}

			value = ((firstname.value ? firstname.value[0] : '') + (lastname.value ? lastname.value : '')).toLowerCase()

			value = value.replace(/[àáâãäåąă]/g,"a")
			value = value.replace(/[çćčċ]/g,"c")
			value = value.replace(/[èéêëēęė]/g,"e")
			value = value.replace(/[ìîïīĩį]/g,"i")
			value = value.replace(/[óôõöøőŏ]/g,"o")
			value = value.replace(/[ùúûüų]/g,"u")
			value = value.replace(' ', '')

			username.value = value
			username.fireEvent('change', {})

			check_unique()
		}

		firstname.addEvent('keyup', update)
		firstname.addEvent('change', update)
		lastname.addEvent('keyup', update)
		lastname.addEvent('change', update)

		//
		//
		//

		var nameAs = document.id(form.elements.name_as)

		function updateDisplayOption(index, value)
		{
			var el = nameAs.getElement('option[value=' + index + ']')

			if (!value)
			{
				if (el)
				{
					el.destroy()
				}

				return
			}

			if (!el)
			{
				el = new Element('option', { value: index, text: value })

				el.inject(nameAs)
			}
			else
			{
				el.set('text', value)
			}
		}

		function updateDisplayComposedOption()
		{
			if (!firstname.value || !lastname.value)
			{
				updateDisplayOption(NAME_AS_FIRSTNAME_LASTNAME, null)
				updateDisplayOption(NAME_AS_LASTNAME_FIRSTNAME, null)

				return
			}

			updateDisplayOption(NAME_AS_FIRSTNAME_LASTNAME, firstname.value + ' ' + lastname.value)
			updateDisplayOption(NAME_AS_LASTNAME_FIRSTNAME, lastname.value + ' ' + firstname.value)
		}

		firstname.addEvent('keyup', function() {

			updateDisplayOption(NAME_AS_FIRSTNAME, this.value)
			updateDisplayComposedOption()
		})

		lastname.addEvent('keyup', function() {

			updateDisplayOption(NAME_AS_LASTNAME, this.value)
			updateDisplayComposedOption()
		})

		nickname.addEvent('keyup', function() {

			updateDisplayOption(NAME_AS_NICKNAME, this.value)
			updateDisplayComposedOption()
		})

		username.addEvents({

			change: function()
			{
				updateDisplayOption(NAME_AS_USERNAME, this.value ? this.value : '<username>')
			},

			keyup: function()
			{
				updateDisplayOption(NAME_AS_USERNAME, this.value ? this.value : '<username>')
			}
		})
	})
} ()
