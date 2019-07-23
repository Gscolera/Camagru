
document.addEventListener('DOMContentLoaded', () => {
	$('#notifications').onclick = toggleNotifications;
	$('#editPers').onclick = showEditPersonalDetailsMenu;
	$('#editFormWrapper input[type=submit]').onclick = e => changePersonalInfo(e);
});
