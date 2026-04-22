(() => {
	const user = AppAPI.requireAuth();
	if (!user) {
		return;
	}

	const form = document.getElementById("share-form");
	if (!form) {
		return;
	}

	const params = new URLSearchParams(window.location.search);
	const incomingDocId = params.get("document_id");
	if (incomingDocId) {
		document.getElementById("share-document-id").value = incomingDocId;
	}

	form.addEventListener("submit", async (event) => {
		event.preventDefault();
		AppAPI.showMessage("share-message", "Sharing document...");

		try {
			await AppAPI.request("/collaboration/share.php", {
				method: "POST",
				body: {
					document_id: Number(document.getElementById("share-document-id").value),
					owner_id: user.user_id,
					user_id: Number(document.getElementById("share-user-id").value),
					permission_type: document.getElementById("share-permission").value,
				},
			});

			AppAPI.showMessage("share-message", "Document shared successfully.");
			form.reset();
		} catch (error) {
			AppAPI.showMessage("share-message", error.message, true);
		}
	});
})();
