document.addEventListener('DOMContentLoaded', () => {
	gallery = $('#gallery');
	gallery.imageView = $('#imageView');
	photosLoaded = 0;

	gallery.addEventListener('wheel', e => scrollGallery(e));
	document.forms.commentForm.onsubmit = addComment;
	setPersonalPreferences();
	fillGallery();
	$('.like').onclick = toggleLike;
});