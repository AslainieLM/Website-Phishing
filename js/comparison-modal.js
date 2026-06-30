(function () {
	var modal = document.getElementById('shell-compare-modal');
	if (!modal) {
		return;
	}

	if (modal.parentElement && modal.parentElement !== document.body) {
		document.body.appendChild(modal);
	}

	function openModal() {
		modal.hidden = false;
		document.body.classList.add('shell-compare-modal-open');
	}

	function closeModal() {
		modal.hidden = true;
		document.body.classList.remove('shell-compare-modal-open');
	}

	modal.querySelectorAll('[data-compare-close]').forEach(function (el) {
		el.addEventListener('click', closeModal);
	});

	document.addEventListener('keydown', function (event) {
		if (event.key === 'Escape' && !modal.hidden) {
			closeModal();
		}
	});

	var trigger = document.getElementById('shell-compare-open');
	if (trigger) {
		trigger.addEventListener('click', openModal);
	}

	if (modal.getAttribute('data-auto-open') === 'true') {
		openModal();
	}
})();
