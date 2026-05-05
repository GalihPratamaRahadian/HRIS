$(function(){

	const navItems = $('#sidebar').find('li.nav-item')
	$.each(navItems, (i, navItem) => {
		$(navItem).removeClass('active')
		$(navItem).find('.nav-link').removeClass('active')
		$(navItem).find('.nav-link[data-toggle="collapse"]').attr('aria-expanded', false)
		$(navItem).find('.collapse').removeClass('show')
		$(navItem).find('.collapse').find('.nav-item').find('.nav-link').removeAttr('aria-expanded')
	})

	let { origin, pathname } = window.location
	pathname = pathname.substr(1);
	const uri = pathname.split('/')
	let isFound = false

	for (let iter = uri.length - 1; iter >= 0; iter--) {
		let fullUri = '';
		for (let uriIter = 0; uriIter <= iter; uriIter++) {
			fullUri += `/${uri[uriIter]}`;
		}

		fullUri = `${origin}${fullUri}`;

		$.each(navItems, (i, navItem) => {
			const navLinks = $(navItem).find('.nav-link')
			$.each(navLinks, (i, navLink) => {
				let href = $(navLink).attr('href')

				if(fullUri == href && !isFound) {
					console.log(`I'm found it ${href}`)
					$(navLink).addClass('active')
					$(navItem).addClass('active')
					$(navItem).find('.collapse').addClass('show')
					$(navItem).find('.nav-link[data-toggle="collapse"]').attr('aria-expanded', true)
					isFound = true
				}
			})
		})
	}
})