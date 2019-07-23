document.addEventListener('DOMContentLoaded', () => {

	const toggleSwitch = document.querySelector('#themeSwitch');
	const currentTheme = sessionStorage.getItem('theme');

	toggleSwitch.addEventListener('change', switchTheme);
	if (currentTheme) {
		document.documentElement.setAttribute('data-theme', currentTheme);
		if (currentTheme === 'dark') {
			toggleSwitch.checked = true;
		}
	}
	$('#settingsGear').onclick = openUserMenu;


});

