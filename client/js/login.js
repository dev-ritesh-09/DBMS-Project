(() => {
	const loginForm = document.getElementById("login-form");
	const registerForm = document.getElementById("register-form");
	const loginTab = document.getElementById("show-login");
	const registerTab = document.getElementById("show-register");

	if (!loginForm || !registerForm || !loginTab || !registerTab) {
		return;
	}

	if (AppAPI.getCurrentUser()) {
		window.location.href = "dashboard.html";
	}

	function switchTab(showLogin) {
		loginForm.classList.toggle("hidden", !showLogin);
		registerForm.classList.toggle("hidden", showLogin);
		loginTab.classList.toggle("active", showLogin);
		registerTab.classList.toggle("active", !showLogin);
		AppAPI.showMessage("auth-message", "");
	}

	loginTab.addEventListener("click", () => switchTab(true));
	registerTab.addEventListener("click", () => switchTab(false));

	loginForm.addEventListener("submit", async (event) => {
		event.preventDefault();
		AppAPI.showMessage("auth-message", "Signing in...");

		try {
			const payload = await AppAPI.request("/user/login.php", {
				method: "POST",
				body: {
					email: document.getElementById("login-email").value.trim(),
					password: document.getElementById("login-password").value,
				},
			});

			AppAPI.setCurrentUser(payload.data.user);
			window.location.href = "dashboard.html";
		} catch (error) {
			AppAPI.showMessage("auth-message", error.message, true);
		}
	});

	registerForm.addEventListener("submit", async (event) => {
		event.preventDefault();
		AppAPI.showMessage("auth-message", "Creating account...");

		try {
			await AppAPI.request("/user/register.php", {
				method: "POST",
				body: {
					name: document.getElementById("register-name").value.trim(),
					email: document.getElementById("register-email").value.trim(),
					password: document.getElementById("register-password").value,
					role: document.getElementById("register-role").value,
				},
			});

			AppAPI.showMessage("auth-message", "Account created. Please login.");
			switchTab(true);
			registerForm.reset();
		} catch (error) {
			AppAPI.showMessage("auth-message", error.message, true);
		}
	});
})();
