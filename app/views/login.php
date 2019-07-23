<div id="loginContainer">
	<div class="formContainer" id="signInFormWrapper">
		<h2>Welcome Back!</h2>
		<p>Please enter your personal details and continue using Camagru!</p>
		<p class='message' id="signInMessage"><?php if(isset($data['message']))
													echo $data['message']?></p>
		<p class='error' id="sigInError"><?php if(isset($data['error']))
												echo $data['error'] ?></p>
		<form id="loginForm">
			<div class="inputLine">
				<input type="text" name="login"  autocomplete="off" spellcheck="false" required>
				<label for="login" class="labelName">
					<span class="contentName">Login</span>
				</label>
			</div>
			<div class="inputLine">
				<input type="password" name="password" autocomplete="off" spellcheck="false" required>
				<label for="password" class="labelName">
					<span class="contentName">Password</span>
				</label>
			</div>
			<input type="submit" value="Sign In">
		</form>
		<p>Forgot your <span id="forgotBtn">password</span>?</p>
		<p>Don't have an <span id="signUpBtn">account</span> yet?</p>
	</div>
	<div class="formContainer" id="signUpFormWrapper">
		<h2>Create Account!</h2>
		<p>Please enter your personal details and start using Camagru!</p>
		<form id="registerForm">
			<div id="userpicImg">
				<img src="https://cdn.intra.42.fr/users/medium_default.png" alt="userpic">
			</div>
			<p class='error' id="sigUpError"></p>
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
				<input type="password" name="password"  autocomplete="off" spellcheck="false" required>
				<label for="password" class="labelName">
					<span class="contentName">Password</span>
				</label>
			</div>
			<div class="inputLine">
				<input type="password" name="passwordConfirm"  autocomplete="off" spellcheck="false" required>
				<label for="passwordConfirm" class="labelName">
					<span class="contentName">Confirm Password</span>
				</label>
			</div>
			<input type="submit" value="Sign Up">
		</form>
		<p>Back to <span class="signInBtn">sign in</span> form!</p>
	</div>
	<div class="formContainer" id="forgotFormWrapper">
		<h2>Forgot your password?</h2>
		<p>Please type your login or email and we will send you a new password!</p>
		<p class='error' id="forgotError"></p>
		<form id="forgotForm">
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
			<input type="submit" value="Send">
		</form>
		<p>Back to <span class="signInBtn">sign in</span> form!</p>
	</div>
</div>
<div class='popup' id="userpicMethodPopup">
	<img class='icon' id='userpicSnapshotBtn' src="/public/img/camera-diaphragm.png" alt="Make snapshot" height="80px" title="Make snapshot">
	<label for="userpicFile">
		<img class='icon' id='userpicUploadBtn' src="/public/img/cloud-backup-up-arrow.png" alt="Upload from disc" height="100px" title="Upload from disc">
	</label>
	<form>
		<input type="file" id="userpicFile" accept="image/jpeg,image/png">
	</form>
</div>
<div class='popup' id="cameraPreview">
	<video id="video"></video>
	<div id="snapBtn"></div>
	<canvas id="canvas"></canvas>
</div>
<script src="/public/js/login.js"></script>

