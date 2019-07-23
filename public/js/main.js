
document.addEventListener('DOMContentLoaded', () => {

	mode = 'webcamMode';
	constraints = {width: 800, height: 600, frameRate: 10};
	video = $('#applicationWrapper video');
	uploadPanel = $('#uploadPanel');
	uploadFile = $('#uploadFile');
	uploadedImage = $('#uploadPanel > img');
	filter = $('#filter');
	sticker = $('#sticker');
	videoWrapper = $('#videoWrapper');
	contextMenu = $('#imageContextMenu');
	canvas = $('canvas');
	recentPhotosPanel = $('#recentPhotos');
	photosLoaded = 0;
	selectedSticker = '';

	setPersonalPreferences();
	$('#useUploadBtn').addEventListener('click', () => { if (mode !== 'uploadMode') showUploadPanel() });
	$('#useWebcamBtn').addEventListener('click', () => {if (mode !== 'webcamMode') showWecamPannel() });
	$('#dropFilterBtn').onclick = dropFilter;
	$('#dropStickersBtn').onclick = dropStickers;
	$('.contextMenuItem:last-child').onclick = deleteSticker;
	$('.contextMenuItem:first-child').onclick = showResizeForm;
	$('#shootBtn').addEventListener('click', mergeImages);
	uploadFile.addEventListener('change',uploadImage);
	filter.addEventListener('change', applyFilter);
	sticker.addEventListener('change', applySticker);
	document.forms.resizeStickerForm.addEventListener('submit', e => resizeSticker(e));
	setWebcam();
});

