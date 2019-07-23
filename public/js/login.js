
document.addEventListener('DOMContentLoaded', () => {

	$('#signUpBtn').addEventListener('click', () => showContainer($('#signUpFormWrapper')));
	$('#forgotBtn').addEventListener('click', () => showContainer($('#forgotFormWrapper')));
	$$('.signInBtn').forEach(elem =>
		elem.addEventListener('click', () => showContainer($('#signInFormWrapper')))
	);
	$('#userpicImg').onclick = showUserpicOptions;
	$('#userpicMethodPopup > form').addEventListener('change', e => uploadUserpic(e.target.parentNode));
	$('#userpicSnapshotBtn').addEventListener('click', () => showCameraPreview());
	$('#snapBtn').addEventListener('click', makeSnapShot);
	$('#registerForm input[type=submit]').addEventListener('click', e => signUp(e, $('#registerForm'),'/auth/sign-up'));
	$('#loginForm input[type=submit]').addEventListener('click', e => signIn(e, $('#loginForm'), '/auth/sign-in'));
	$('#forgotForm input[type=submit]').addEventListener('click', e => resetPassword(e, $('#forgotForm'), '/auth/forgot'));

});




