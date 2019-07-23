<div id="applicationWrapper">
	<div id="videoWrapper">
		<video></video>
		<canvas></canvas>
		<div id="uploadPanel">
			<label for="uploadFile">
				<img src="/public/img/cloud-backup-up-arrow.png" alt="upload">
			</label>
			<form>
				<input type="file" id="uploadFile" accept="image/jpeg,image/png">
			</form>
			<img>
		</div>
		<div id="controlPanel">
			<select id="filter">
				<option value="none">Filter</option>
				<option value="grayscale(100%)">Grayscale</option>
				<option value="sepia(100%)">Sepia</option>
				<option value="invert(100%)">Invert</option>
				<option value="contrast(200%)">Contrast</option>
			</select>
			<button id="dropFilterBtn">Drop filter</button>
			<button id="useWebcamBtn">Use Webcam</button>
			<div id="shootBtn"></div>
			<button id="useUploadBtn">Upload photo</button>
			<button id="dropStickersBtn">Drop stickers</button>
			<select id="sticker">
				<option value="none">Sticker</option>
				<option value="AngryBird">Angry Bird</option>
				<option value="wreath">Wreath</option>
				<option value="maraca">Maraca</option>
				<option value="pontus">Pontus</option>
				<option value="sombrero">Sombrero</option>
				<option value="sunglasses">Sunglasses</option>
			</select>
		</div>
	</div>
	<div id="recentPhotos"></div>
</div>
<div id="imageContextMenu">
	<div class="contextMenuItem">
		<p>Resize image</p>
	</div>
	<div class="contextMenuItem hidden">
		<form id="resizeStickerForm">
			<input type="text">
			<p>x</p>
			<input type="text">
			<input type="submit">
		</form>
	</div>
	<div class="contextMenuItem">
		<p>Delete image</p>
	</div>
</div>
<script src="/public/js/main.js"></script>