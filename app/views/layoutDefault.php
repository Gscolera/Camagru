<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="/public/css/style.css">
	<link rel="shortcut icon" href="/public/img/camera.png" type="image/png">
	<script src="/public/js/lib.js"></script>
	<script src="/public/js/commonFeatures.js"></script>
	<?php if(isset($_SESSION['user'])): ?>
		<script src="/public/js/user.js"></script>
	<?php endif ?>
	<title><?=$title?></title>
</head>
<body>
	<div id="bodyContainer">
		<header>
			<div id="headerInner">
				<h1><a href="/">Camagru</a></h1>
				<h3 id="galleryBtn"><a href="/image">Gallery</a></h3>
				<div id="headerSideMenu">
					<nav>
						<?php if (isset($_SESSION['user'])): ?>
							<img id='avatar' src="" alt="userpic">
						<?php endif ?>
						<p id="username"></p>
						<img id='settingsGear' class='icon' src="/public/img/settings-gears.png" alt="settings">
					</nav>
				</div>
			</div>
			<div id="userMenu">
				<div class="menuItem">
					<p>Night mode</p>
					<input type="checkbox" id="themeSwitch" class="checkbox">
				</div>
				<?php if (isset($_SESSION['user'])) : ?>
					<div class="menuItem">
						<p>Notifications </p>
						<input type="checkbox" id="notifications" class="checkbox">
					</div>
					<div class="menuItem">
						<p>Edit personal details</p>
						<img id='editPers' src="/public/img/edit.png" alt="edit login">
					</div>
					<div class="menuItem">
						<p>Logout</p>
						<a href="/user/logout"><img src="/public/img/logout.png" alt="logout"></a>
					</div>
				<?php endif ?>
			</div>
		</header>
		<main><?php include_once $content ?></main>
		<footer><p>Created by <span>Gscolera 2019&copy;</span></p></footer>
		<div class='popup' id="editFormWrapper">
			<img class='close' src="/public/img/delete.png" alt="close">
			<h4>Insert new data into the fields you are going to change!</h4>
			<p class="error"></p>
			<form id="editForm">
				<div class="inputLine">
					<input type="text" name="login"  autocomplete="off" spellcheck="false" required>
					<label for="login" class="labelName">
						<span class="contentName">Login</span>
					</label>
				</div>
				<div class="inputLine">
					<input type="text" name="email"  autocomplete="off" spellcheck="false" required>
					<label for="email" class="labelName">
						<span class="contentName">Email</span>
					</label>
				</div>
				<div class="inputLine">
					<input type="password" name="newPassword"  autocomplete="off" spellcheck="false" required>
					<label for="newPassword" class="labelName">
						<span class="contentName">New Password</span>
					</label>
				</div>
				<div class="inputLine">
					<input type="password" name="newPasswordConfirm"  autocomplete="off" spellcheck="false" required>
					<label for="newPasswordConfirm" class="labelName">
						<span class="contentName">Confirm New Password</span>
					</label>
				</div>
				<h4>Type your password to submit changes</h4>
				<div class="inputLine">
					<input type="password" name="password"  autocomplete="off" spellcheck="false" required>
					<label for="Password" class="labelName">
						<span class="contentName">Password</span>
					</label>
				</div>
				<input type="submit" value='Submit changes'>
			</form>
		</div>
	</div>
	<div class='popup' id="imageView">
		<img src="">
		<div id="imageInfoContainer">
			<p id="imageInfo">
				Posted by <span></span> on <span></span>.
			</p>
			<div id="likeContainer">
				<p id="likesCount"></p>
				<img class='like' src="/public/img/heart.png" alt="like">
			</div>
		</div>
		<div id="commentInputContainer">
			<form id="commentForm">
				<textarea name="comment" rows="2" placeholder="Leave your comment..."></textarea>
				<input type="submit" value="Add Comment">
			</form>
		</div>
		<div id="comments">
			<h4>Comments</h4>
		</div>
	</div>
</body>
</html>
