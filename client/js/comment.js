const CommentAPI = (() => {
	async function loadComments(documentId) {
		const listEl = document.getElementById("comments-list");
		if (!listEl) {
			return;
		}

		try {
			const payload = await AppAPI.request(`/comment/get.php?document_id=${documentId}`);
			const comments = payload.data.comments || [];
			listEl.innerHTML = "";

			if (comments.length === 0) {
				listEl.innerHTML = "<p class='muted'>No comments yet.</p>";
				return;
			}

			comments.forEach((comment) => {
				const item = document.createElement("div");
				item.className = "item";
				item.innerHTML = `
					<div>
						<strong>${escapeHtml(comment.user_name)}</strong>
						<div>${escapeHtml(comment.comment_text)}</div>
						<div class="item-meta">${comment.timestamp}</div>
					</div>
				`;
				listEl.appendChild(item);
			});
		} catch (error) {
			listEl.innerHTML = `<p class='message' style='color:#b23a48'>${escapeHtml(error.message)}</p>`;
		}
	}

	function initCommentSection(documentId, userId) {
		const form = document.getElementById("comment-form");
		if (!form) {
			return;
		}

		form.onsubmit = async (event) => {
			event.preventDefault();
			try {
				await AppAPI.request("/comment/add.php", {
					method: "POST",
					body: {
						user_id: userId,
						document_id: documentId,
						comment_text: document.getElementById("comment-text").value.trim(),
					},
				});

				form.reset();
				await loadComments(documentId);
			} catch (error) {
				AppAPI.showMessage("view-doc-message", error.message, true);
			}
		};

		loadComments(documentId);
	}

	return {
		initCommentSection,
	};
})();

window.CommentAPI = CommentAPI;

function escapeHtml(value) {
	return String(value)
		.replace(/&/g, "&amp;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;")
		.replace(/"/g, "&quot;")
		.replace(/'/g, "&#39;");
}
