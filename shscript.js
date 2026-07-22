document.addEventListener('DOMContentLoaded', function(){
	// User menu toggle
	const userBtn = document.getElementById('userBtn');
	const userMenu = document.getElementById('userMenu');
	if(userBtn && userMenu){
		userBtn.addEventListener('click', function(e){
			e.preventDefault();
			userMenu.style.display = userMenu.style.display === 'block' ? 'none' : 'block';
		});

		// close when clicking outside
		document.addEventListener('click', function(e){
			if(!userBtn.contains(e.target) && !userMenu.contains(e.target)){
				userMenu.style.display = 'none';
			}
		});
	}

	// Smooth scroll for nav links
	document.querySelectorAll('a[href^="#"]').forEach(function(anchor){
		anchor.addEventListener('click', function(e){
			const targetId = this.getAttribute('href').slice(1);
			const target = document.getElementById(targetId);
			if(target){
				e.preventDefault();
				target.scrollIntoView({behavior:'smooth',block:'start'});
				// hide menu if open
				if(userMenu) userMenu.style.display = 'none';
				// hide mobile nav if open
				const navbarCenter = document.getElementById('navbarCenter');
				if(navbarCenter && navbarCenter.classList.contains('show')) navbarCenter.classList.remove('show');
			}
		});
	});

	// Mobile navbar toggle
	const mobileToggle = document.getElementById('mobileToggle');
	const navbarCenter = document.getElementById('navbarCenter');
	if(mobileToggle && navbarCenter){
		mobileToggle.addEventListener('click', function(){
			navbarCenter.classList.toggle('show');
		});
	}

	// Filter room cards by beds query param (e.g. ?beds=2 or ?beds=4)
	const urlParams = new URLSearchParams(window.location.search);
	const bedsParam = urlParams.get('beds');
	if(bedsParam){
		document.querySelectorAll('.room-card').forEach(card => {
			const beds = card.getAttribute('data-beds');
			if(beds !== bedsParam) card.style.display = 'none';
		});
	}

	// Admin create-account link: show informational message instead of redirect
	const adminCreate = document.getElementById('adminCreate');
	if(adminCreate){
		adminCreate.addEventListener('click', function(e){
			e.preventDefault();
			alert('admin account already exists please login');
		});
	}
});
