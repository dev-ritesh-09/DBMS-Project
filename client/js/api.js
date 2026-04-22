const AppAPI = (() => {
	const API_BASE = "../server/api";

	async function request(path, options = {}) {
		const config = {
			method: options.method || "GET",
			headers: {
				"Content-Type": "application/json",
			},
		};

		if (options.body) {
			config.body = JSON.stringify(options.body);
		}

		const response = await fetch(`${API_BASE}${path}`, config);
		const payload = await response.json().catch(() => ({ success: false, message: "Invalid response" }));

		if (!response.ok || payload.success === false) {
			const error = new Error(payload.message || "Request failed");
			error.payload = payload;
			throw error;
		}

		return payload;
	}

	function getCurrentUser() {
		const raw = localStorage.getItem("docflow_user");
		return raw ? JSON.parse(raw) : null;
	}

	function setCurrentUser(user) {
		localStorage.setItem("docflow_user", JSON.stringify(user));
	}

	function clearCurrentUser() {
		localStorage.removeItem("docflow_user");
	}

	function requireAuth(redirect = "index.html") {
		const user = getCurrentUser();
		if (!user) {
			window.location.href = redirect;
			return null;
		}
		return user;
	}

	function showMessage(elementId, message, isError = false) {
		const el = document.getElementById(elementId);
		if (!el) {
			return;
		}
		el.textContent = message || "";
		el.style.color = isError ? "#b23a48" : "#0a6d5d";
	}

	return {
		request,
		getCurrentUser,
		setCurrentUser,
		clearCurrentUser,
		requireAuth,
		showMessage,
	};
})();

window.AppAPI = AppAPI;
