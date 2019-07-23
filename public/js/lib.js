function $(elem) {
	return document.querySelector(elem)
}

function $$(elem) {
	return document.querySelectorAll(elem)
}

function switchTheme(e) {

	if (e.target.checked) {
		document.documentElement.setAttribute('data-theme', 'dark');
		sessionStorage.setItem('theme', 'dark');
	}
	else {
		document.documentElement.setAttribute('data-theme', 'light');
		sessionStorage.setItem('theme', 'light');
	}
}

function openUserMenu() {
	var layout = createLayout('rgba(0, 0, 0, 0)');
	layout.onclick = closeUserMenu;
	$('#userMenu').style.display = 'flex';
}

function closeUserMenu(e) {
	$('#userMenu').style.display = 'none';
	e.target.parentNode.removeChild(e.target);
}

function createLayout(background, filter) {
	var layout = document.createElement('div');
	if (filter)
		$('#bodyContainer').style.filter = filter;
	layout.style.background = background;
	layout.classList.add('layout');
	document.body.appendChild(layout);
	return layout;
}

function showContainer(container) {
	$$('.formContainer').forEach(elem =>
		elem.style.zIndex = (elem === container) ? 2 : 1
	)
}

function showUserpicOptions() {
	var popup = $('#userpicMethodPopup');
	var layout = createLayout('rgba(0, 0, 0, .8)', 'blur(5px)');

	layout.onclick = e => hidePopup(popup);
	popup.style.display = 'flex';
	document.body.appendChild(popup);
}

function hidePopup(popup) {
	var layout = $('.layout');

	layout.parentNode.removeChild(layout);
	$('#bodyContainer').style.filter = 'none';
	popup.style.display = 'none';
}

function showCameraPreview() {

	var cameraPreview = $('#cameraPreview');
	$('#userpicMethodPopup').style.display = 'none';
	cameraPreview.style.display = 'flex';
	document.body.appendChild(cameraPreview);

	var constraints = {width: 800, height: 600, frameRate: 10};

	navigator.mediaDevices.getUserMedia({audio: false, video: constraints})
		.then(playWebcamPreview)
		.catch(error => console.log("navigator.getUserMedia error: ", error));
}

function playWebcamPreview(stream) {
	var video = $('#video');

	video.srcObject = stream;
	video.play();
	$('.layout').onclick = closeWebcamPreview;
}

function closeWebcamPreview() {
	$('#video').srcObject.getVideoTracks().forEach(track => track.stop());
	hidePopup($('#cameraPreview'));
}

function makeSnapShot() {

	var canvas = $('#canvas');
	var context = canvas.getContext('2d');

	canvas.width = 800;
	canvas.height = 600;
	context.drawImage(video, 0, 0, 800, 600);
	canvas.toBlob(sendUserpic);
	closeWebcamPreview();
	$('#userpicMethodPopup form').elements[0].value = '';
}

function uploadUserpic(form) {
	if (form.elements[0].value !== '') {
		sendUserpic(form.elements[0].files[0]);
		hidePopup($('#userpicMethodPopup'));
	}
	form.elements[0].value = '';
}

function sendUserpic(pic) {

	formData = new FormData();
	formData.append('file', pic);
	fetch('/auth/create-userpic', {method: 'POST', body:formData})
		.then(res => res.json())
		.then(response => $('#userpicImg img').src = response.image)
		.catch(error => console.log('Uploading error: ' + error));
}

function signUp(e, form, url) {
	e.preventDefault();
	sendForm(form, url)
		.then(res => {
			switch (res.status) {

				case 'error':
					$('#signUpFormWrapper .error').innerText = res.error;
					hightlightField($$('#signUpFormWrapper .inputLine'), res.field);
					break;
				case 'success':
					showContainer($('#signInFormWrapper'));
					$('#signInFormWrapper .error').innerText = '';
					$('#signInFormWrapper .message').innerText = res.message;
					break;
				default:
					console.log(res);
					break;
			}
		});
}

function signIn(e, form, url) {
	e.preventDefault();
	sendForm(form, url)
		.then(res => {
			switch (res.status) {

				case 'error':
					$('#signInFormWrapper .message').innerText = '';
					$('#signInFormWrapper .error').innerText = res.error;
					hightlightField($$('#signUpFormWrapper .inputLine'), res.field);
					break;
				case 'success':
					window.location.href = '/';
					break;
				default:
					console.log(res);
					break;
			}
		});
}

function resetPassword(e, form, url) {
	e.preventDefault();
	sendForm(form, url)
		.then(res => {
			switch (res.status) {
				case 'error':
					$('#forgotFormWrapper .error').innerText = res.error;
					hightlightField($$('#forgotForm .inputLine'), res.field);
					break;
				case 'success':
					showContainer($('#signInFormWrapper'));
					$('#signInFormWrapper .error').innerText = '';
					$('#signInFormWrapper .message').innerText = res.message;
					break;
				default:
					console.log(res);
					break;
			}
		});
}

async function sendForm(form, url) {

	formData = new FormData(form);
	response = await fetch(url, {method: 'POST', body: formData});
	response = await response.json();
	return response;
}

function hightlightField(elements, field) {

	for (var i = 0; elements[i]; i++) {

		if (i === field)
			elements[i].classList.add('error');
		else
			elements[i].classList.remove('error');
	}
}

function setPersonalPreferences() {

	fetch('/user/check-preferences')
		.then(res => res.json())
		.then(pref => {
			if (pref.error === 'not logged in')
				return;
			if (pref.notification === '1')
				$('#notifications').checked = true;
			var avatar = $('#avatar');
			avatar.src = pref.userpic;
			avatar.title = pref.login;
		});
}

function toggleNotifications() {
	value = $('#notifications').checked;
	fetch('/user/change-preferences?notification=' + value);
}

function showEditPersonalDetailsMenu() {

	hidePopup($('#userMenu'));

	var layout = createLayout('rgba(0, 0, 0, .8)', 'blur(10px)');
	var editPopup = $('#editFormWrapper');

	editPopup.style.display = 'flex';
	document.body.appendChild(editPopup);
	layout.onclick = () => hidePopup(editPopup);
	$('#editFormWrapper .close').onclick = () => hidePopup(editPopup);
}

function changePersonalInfo(e) {

	e.preventDefault();
	sendForm($('#editForm'), '/user/change-personal-info')
		.then(res => {

			var error = $('#editFormWrapper .error');
			var message = $('#editFormWrapper .message');

			switch (res.status) {
				case 'error':
					error.innerText = res.error;
					hightlightField($$('#editForm .inputLine'), res.field);
					break;
				case 'success':
					error.innerText = '';
					$('#avatar').title = res.login;
					hidePopup($('#editFormWrapper'));
					break;
				default:
					console.log(res);
					break;
			}
		});
}

function setWebcam() {

	if (document.documentElement.clientWidth < 800) {
		constraints.width = document.documentElement.clientWidth;
		constraints.height = constraints.width / 4 * 3;
	}

	navigator.mediaDevices.getUserMedia({audio: false, video: constraints})
		.then(playWebcamStream)
		.catch(error => console.log(error));
}

function playWebcamStream(stream) {

	video.srcObject = stream;
	video.play();
}

function showUploadPanel() {

	video.style.zIndex = 5;
	uploadPanel.style.height = constraints.height + 'px';
	uploadPanel.style.width = constraints.width + 'px';
	uploadedImage.style.filter = filter.value;
	uploadPanel.style.display = 'flex';
	mode = 'uploadMode';
}


function showWecamPannel()
{
	setWebcam();
	video.style.zIndex = 7;
	video.style.filter = filter.value;
	mode = 'webcamMode';
}

function uploadImage() {

	var form = $('#uploadPanel form');

	formData = new FormData();
	formData.append('file', form.elements[0].files[0]);
	formData.append('width',uploadPanel.offsetWidth);
	formData.append('height', uploadPanel.offsetHeight);

	fetch('/main/upload', {method: 'POST', body:formData})
		.then(res => res.blob())
		.then(blob => uploadedImage.src = URL.createObjectURL(blob))
		.catch(error => console.log('Uploading error: ' + error));
}

function applyFilter() {

	if (mode === 'webcamMode')
		video.style.filter = filter.value;
	else
		uploadedImage.style.filter = filter.value;

	$$('.stickerImage').forEach(elem => elem.style.filter = filter.value);
}

function dropFilter() {

	var event = new Event('change');

	filter.value = 'none';
	filter.dispatchEvent(event);
}

function applySticker() {

	fetch('/image/get-sticker?sticker=' + sticker.value)
		.then(res => res.blob())
		.then(blob => {
			var image = document.createElement('img');
			var rect = videoWrapper.getBoundingClientRect();
			image.src = URL.createObjectURL(blob);
			image.classList.add('stickerImage');
			image.classList.add(sticker.value);
			image.style.top = rect.y + 'px';
			image.style.filter = filter.value;
			document.body.appendChild(image);
			image.ondragstart = () => false;
			image.onmousedown = e => { if (e.buttons === 1) dragImage(image, e) };
			image.oncontextmenu = e => showImageContextMenu(e);
			sticker.value = 'none';

		})
		.catch(error => console.log(error));
}

function dragImage(image, e) {

	var rect = image.getBoundingClientRect();

	image.centerX = rect.width / 2;
	image.centerY = rect.height / 2;

	moveImage(e);

	function moveImage(e) {

		var x = e.pageX - image.centerX;
		var y = e.pageY - image.centerY;

		image.style.top = y + 'px';
		image.style.left = x + 'px';
	}

	document.onmousemove = moveImage;
	document.onmouseup = () => {
		document.onmousemove = null;
		document.onmouseup = null;
	};
}

function dropStickers() {
	$$('.stickerImage').forEach(sticker => document.body.removeChild(sticker));
}

function showImageContextMenu(e, image) {

	e.preventDefault();
	var layout = createLayout('rgba(0, 0, 0, 0)');
	selectedSticker = e.target;

	contextMenu.style.top = e.pageY + 'px';
	contextMenu.style.left = e.pageX + 'px';
	contextMenu.style.display = 'flex';
	layout.onclick = () => {
		hidePopup(contextMenu);
		$('.contextMenuItem:first-child + div').classList.toggle('hidden');
	}
}

function deleteSticker() {
	document.body.removeChild(selectedSticker);
	hidePopup(contextMenu);
}

function showResizeForm() {
	rect = selectedSticker.getBoundingClientRect();
	document.forms.resizeStickerForm.elements[0].value = rect.width;
	document.forms.resizeStickerForm.elements[1].value = rect.height;
	$('.contextMenuItem:first-child + div').classList.toggle('hidden');
}

function resizeSticker(e) {
	e.preventDefault();
	selectedSticker.style.width = document.forms.resizeStickerForm.elements[0].value + 'px';
	selectedSticker.style.height = document.forms.resizeStickerForm.elements[1].value + 'px';
}

function  mergeImages() {

	if (mode === 'webcamMode') {
		var context = canvas.getContext('2d');
		canvas.width = constraints.width;
		canvas.height = constraints.height;
		context.drawImage(video, 0, 0, constraints.width, constraints.height);
		canvas.toBlob(dispatch);
	}
	else {
		dispatch();
	}
}

function dispatch(blob) {

	var rect = video.getBoundingClientRect();
	var formData = new FormData();
	formData.append('filter', filter.value);
	$$('.stickerImage').forEach(elem => appendSticker(formData, elem));
	image = blob ? blob : 'uploaded';
	formData.append('image', image);
	formData.append('width', rect.width);
	formData.append('height', rect.height);
	formData.append('x', rect.x);
	formData.append('y', rect.y);
	fetch('/main/merge', {method:'POST', body: formData})
		.then(response => response.json())
		.then(showResult)
		.catch(error => console.log(error));
}

function appendSticker(formData, elem) {
	var sticker = {};
	var rect = elem.getBoundingClientRect();

	sticker.name = elem.className.split(' ')[1];
	sticker.x = rect.x;
	sticker.y = rect.y;
	sticker.width = rect.width;
	sticker.height = rect.height;
	formData.append('sticker[]', JSON.stringify(sticker));
}

function  showResult(res) {

	switch(res.status) {
		case 'success':
			image = document.createElement('img');
			image.src = res.image;
			recentPhotosPanel.appendChild(image);
			break;
		case 'error':
			console.log('error');
			break;
		default:
			console.log(res);
	}
}


function fillGallery() {
	var rect = gallery.getBoundingClientRect();
	var rows = Math.ceil(rect.height / 150);
	var cols = Math.floor(rect.width / 200);
	var limit = cols * rows;
	getPhotos(limit);
}

function getPhotos(limit) {
	fetch('/image/fill-gallery?limit=' + limit + '&last=' + photosLoaded)
		.then(res => res.json())
		.then(res => {
			for (var key in res) {
				image = document.createElement('img');
				image.id = key;
				image.classList.add('galleryImage');
				image.src = res[key];
				image.onclick = openImage;
				gallery.appendChild(image);
				photosLoaded++;
			}
		})
}

function scrollGallery(e) {
	if (e.deltaY > 50) {
		getPhotos(1);
	}
}

function openImage(e) {
	var layout = createLayout('rgba(0, 0, 0, .8)', 'blur(5px)');
	gallery.selectedImageId = e.target.id;
	gallery.selectedImage = e.target;

	fetch('/image/get-info?pid=' + gallery.selectedImageId)
		.then(res => res.json())
		.then(applyImageInfo)
		.catch(error => console.log(error));
	gallery.appendChild(layout);
	$('#imageView > img').src = e.target.src;
	gallery.imageView.style.display = 'flex';
	layout.onclick = closeImageView;
}

function closeImageView() {

	var btn = $('#imageInfo button');

	hidePopup(imageView);
	$('#commentInputContainer').style.display = 'none';
	$$('.commentContainer').forEach(comment => comment.parentNode.removeChild(comment));
	if (btn)
		$('#imageInfo').removeChild(btn);
}

function applyImageInfo(info) {

	$('#imageInfo span:first-child').innerText = info.owner;
	$('#imageInfo span:last-child').innerText = info.date;
	$('#likeContainer p').innerText = info.likesCount;
	if (info.authorizedUser === true)
		$('#commentInputContainer').style.display = 'flex';
	for (header in info.comment)
		appendComment(header, info.comment[header]);
	if (info.logged === true)
		createDeleteBtn();

}

function toggleLike() {

	fetch('/image/like?pid=' + gallery.selectedImageId)
		.then(res => res.json())
		.then(changeLikesCount)
		.catch(error => console.log(error));
}

function changeLikesCount(response) {

	var likesCount = $('#likesCount');
	if (response.like === 'set')
		likesCount.innerText = parseInt(likesCount.innerText) + 1;
	else if (response.like === 'unset' )
		likesCount.innerText = parseInt(likesCount.innerText) - 1;
}

function addComment(e) {

	e.preventDefault();
	formData = new FormData();
	formData.append('comment', document.forms.commentForm.elements[0].value);
	formData.append('pid', gallery.selectedImageId);

	fetch('/image/comment', {method:'POST', body: formData})
		.then(res => res.json())
		.then(res => appendComment(res.header, res.comment))
		.catch(error => console.log(error));
}

function appendComment(header, comment) {

	if (header === undefined || comment === undefined)
		return;
	commentContainer = document.createElement('div');
	commentHeader = document.createElement('div');
	commentBody = document.createElement('div');

	commentContainer.classList.add('commentContainer');
	commentHeader.classList.add('commentHeader');
	commentBody.classList.add('commentBody');

	commentBody.innerHTML = comment;
	commentHeader.innerHTML = header;

	$('#comments').appendChild(commentContainer);
	commentContainer.appendChild(commentHeader);
	commentContainer.appendChild(commentBody);
	document.forms.commentForm.elements[0].value = '';
}

function createDeleteBtn() {

	btn = document.createElement('button');

	btn.innerText = 'Delete image';
	btn.onclick = deleteImage;
	$('#imageInfo').appendChild(btn);
}

function deleteImage() {

	gallery.removeChild(gallery.selectedImage);
	fetch('/image/delete?pid=' + gallery.selectedImageId)
		.then(res => res.text())
		.then(res => console.log(res));
	closeImageView();
}