(() => {
	const user = AppAPI.requireAuth();
	if (!user) {
		return;
	}

	const logoutBtn = document.getElementById("logout-btn");
	if (logoutBtn) {
		logoutBtn.addEventListener("click", () => {
			AppAPI.clearCurrentUser();
			window.location.href = "index.html";
		});
	}

	const welcomeUser = document.getElementById("welcome-user");
	if (welcomeUser) {
		welcomeUser.textContent = `${user.name} (${user.role})`;
	}

	initDashboard(user);
	initCreateDoc(user);
	initViewDoc(user);
})();

function initDashboard(user) {
	const container = document.getElementById("documents-list");
	if (!container) {
		return;
	}

	async function loadDocuments() {
		AppAPI.showMessage("dashboard-message", "Loading documents...");
		try {
			const payload = await AppAPI.request(`/document/get.php?user_id=${user.user_id}`);
			const documents = payload.data.documents || [];

			container.innerHTML = "";
			if (documents.length === 0) {
				container.innerHTML = "<p class='muted'>No documents found.</p>";
			}

			documents.forEach((doc) => {
				const node = document.createElement("article");
				node.className = "item";
				node.innerHTML = `
					<div>
						<strong>${escapeHtml(doc.title)}</strong>
						<div class="item-meta">ID: ${doc.document_id} | Owner: ${escapeHtml(doc.owner_name || "-")} | Updated: ${doc.last_modified}</div>
					</div>
					<div>
						<a class="btn" href="view-doc.html?document_id=${doc.document_id}">Open</a>
						<button class="btn danger" data-action="delete" data-id="${doc.document_id}" type="button">Delete</button>
					</div>
				`;
				container.appendChild(node);
			});

			AppAPI.showMessage("dashboard-message", `Loaded ${documents.length} document(s).`);
		} catch (error) {
			AppAPI.showMessage("dashboard-message", error.message, true);
		}
	}

	container.addEventListener("click", async (event) => {
		const target = event.target;
		if (!(target instanceof HTMLElement)) {
			return;
		}

		if (target.dataset.action === "delete") {
			const docId = Number(target.dataset.id);
			if (!window.confirm("Delete this document?")) {
				return;
			}

			try {
				await AppAPI.request("/document/delete.php", {
					method: "POST",
					body: {
						document_id: docId,
						user_id: user.user_id,
					},
				});
				await loadDocuments();
			} catch (error) {
				AppAPI.showMessage("dashboard-message", error.message, true);
			}
		}
	});

	const refreshBtn = document.getElementById("refresh-docs");
	if (refreshBtn) {
		refreshBtn.addEventListener("click", loadDocuments);
	}

	loadDocuments();
}

function initCreateDoc(user) {
	const form = document.getElementById("create-doc-form");
	if (!form) {
		return;
	}

	form.addEventListener("submit", async (event) => {
		event.preventDefault();
		AppAPI.showMessage("create-doc-message", "Creating document...");

		try {
			await AppAPI.request("/document/create.php", {
				method: "POST",
				body: {
					title: document.getElementById("doc-title").value.trim(),
					content: document.getElementById("doc-content").value,
					owner_id: user.user_id,
					document_status: "Active",
				},
			});

			AppAPI.showMessage("create-doc-message", "Document created successfully.");
			form.reset();
		} catch (error) {
			AppAPI.showMessage("create-doc-message", error.message, true);
		}
	});
}

function initViewDoc(user) {
	const form = document.getElementById("update-doc-form");
	if (!form) {
		return;
	}

	const params = new URLSearchParams(window.location.search);
	const documentId = Number(params.get("document_id") || 0);

	if (!documentId) {
		AppAPI.showMessage("view-doc-message", "Missing document id", true);
		return;
	}

	const titleEl = document.getElementById("view-doc-title");
	const contentEl = document.getElementById("view-doc-content");
	const idEl = document.getElementById("view-doc-id");
	const mainTitle = document.getElementById("view-title");

	async function loadDocument() {
		try {
			const payload = await AppAPI.request(`/document/get.php?document_id=${documentId}&user_id=${user.user_id}`);
			const doc = payload.data.document;
			idEl.value = String(doc.document_id);
			titleEl.value = doc.title;
			contentEl.value = doc.latest_content || "";
			if (mainTitle) {
				mainTitle.textContent = `Document #${doc.document_id}`;
			}
			await loadVersions(documentId, user.user_id);
			if (window.CommentAPI) {
				window.CommentAPI.initCommentSection(documentId, user.user_id);
			}
		} catch (error) {
			AppAPI.showMessage("view-doc-message", error.message, true);
		}
	}

	form.addEventListener("submit", async (event) => {
		event.preventDefault();
		AppAPI.showMessage("view-doc-message", "Saving...");

		try {
			await AppAPI.request("/document/update.php", {
				method: "POST",
				body: {
					document_id: documentId,
					user_id: user.user_id,
					title: titleEl.value.trim(),
					content: contentEl.value,
				},
			});

			AppAPI.showMessage("view-doc-message", "Saved as a new version.");
			await loadVersions(documentId, user.user_id);
		} catch (error) {
			AppAPI.showMessage("view-doc-message", error.message, true);
		}
	});

	loadDocument();
}

async function loadVersions(documentId, userId) {
	const versionsList = document.getElementById("versions-list");
	if (!versionsList) {
		return;
	}

	try {
		const payload = await AppAPI.request(`/document/version.php?document_id=${documentId}&user_id=${userId}`);
		const versions = payload.data.versions || [];

		versionsList.innerHTML = "";
		if (versions.length === 0) {
			versionsList.innerHTML = "<p class='muted'>No versions yet.</p>";
			return;
		}

		versions.forEach((version) => {
			const item = document.createElement("div");
			item.className = "item";
			item.innerHTML = `
				<div>
					<strong>v${version.version_id}</strong>
					<div class="item-meta">By ${escapeHtml(version.modified_by_name)} on ${version.modified_date}</div>
				</div>
			`;
			versionsList.appendChild(item);
		});
	} catch (error) {
		versionsList.innerHTML = `<p class='message' style='color:#b23a48'>${escapeHtml(error.message)}</p>`;
	}
}

function escapeHtml(value) {
	return String(value)
		.replace(/&/g, "&amp;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;")
		.replace(/"/g, "&quot;")
		.replace(/'/g, "&#39;");
}
