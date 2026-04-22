(() => {
	const user = AppAPI.requireAuth();
	if (!user) {
		return;
	}

	const createForm = document.getElementById("folder-create-form");
	const moveForm = document.getElementById("folder-move-form");
	const listEl = document.getElementById("folder-list");

	async function loadFolders() {
		if (!listEl) {
			return;
		}

		try {
			const payload = await AppAPI.request(`/folder/get.php?user_id=${user.user_id}`);
			const folders = payload.data.folders || [];
			listEl.innerHTML = "";

			if (folders.length === 0) {
				listEl.innerHTML = "<p class='muted'>No folders yet.</p>";
				return;
			}

			folders.forEach((folder) => {
				const item = document.createElement("div");
				item.className = "item";
				item.innerHTML = `
					<div>
						<strong>${escapeHtml(folder.folder_name)}</strong>
						<div class="item-meta">ID: ${folder.folder_id} | Documents: ${folder.document_count}</div>
					</div>
				`;
				listEl.appendChild(item);
			});
		} catch (error) {
			listEl.innerHTML = `<p class='message' style='color:#b23a48'>${escapeHtml(error.message)}</p>`;
		}
	}

	if (createForm) {
		createForm.addEventListener("submit", async (event) => {
			event.preventDefault();
			try {
				await AppAPI.request("/folder/create.php", {
					method: "POST",
					body: {
						folder_name: document.getElementById("folder-name").value.trim(),
						created_by: user.user_id,
					},
				});

				AppAPI.showMessage("folder-create-message", "Folder created successfully.");
				createForm.reset();
				await loadFolders();
			} catch (error) {
				AppAPI.showMessage("folder-create-message", error.message, true);
			}
		});
	}

	if (moveForm) {
		moveForm.addEventListener("submit", async (event) => {
			event.preventDefault();
			try {
				await AppAPI.request("/folder/move.php", {
					method: "POST",
					body: {
						document_id: Number(document.getElementById("move-document-id").value),
						folder_id: Number(document.getElementById("move-folder-id").value),
						user_id: user.user_id,
					},
				});

				AppAPI.showMessage("folder-move-message", "Document moved successfully.");
				moveForm.reset();
			} catch (error) {
				AppAPI.showMessage("folder-move-message", error.message, true);
			}
		});
	}

	const refreshBtn = document.getElementById("refresh-folders");
	if (refreshBtn) {
		refreshBtn.addEventListener("click", loadFolders);
	}

	loadFolders();
})();

function escapeHtml(value) {
	return String(value)
		.replace(/&/g, "&amp;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;")
		.replace(/"/g, "&quot;")
		.replace(/'/g, "&#39;");
}
