<script>
<?php if ($maxTime and $minTime): ?>
	minTime = true;
	document.querySelector('#mtime').addEventListener('animationend', function () {
		if (minTime) {
			document.querySelector('#centerSection').removeChild(document.querySelector('#waitCover'));
			Array.from(document.querySelectorAll('.choice')).map(function (a) {
				a.classList.add('mdc-button--raised');
			});
			minTime = false;
		} else {
			location.reload();
		}
	});
<?php elseif ($minTime): ?>
	document.querySelector('#mtime').addEventListener('animationend', function () {
		document.querySelector('#centerSection').removeChild(document.querySelector('#waitCover'));
		Array.from(document.querySelectorAll('.choice')).map(function (a) {
			a.classList.add('mdc-button--raised');
		});
	});
<?php elseif ($maxTime): ?>
	document.querySelector('#centerSection').removeChild(document.querySelector('#waitCover'));
	Array.from(document.querySelectorAll('.choice')).map(function (a) {
		a.classList.add('mdc-button--raised');
	});
	document.querySelector('#mtime').addEventListener('animationend', function () {
		location.reload();
	});
<?php else: ?>
	document.querySelector('#centerSection').removeChild(document.querySelector('#waitCover'));
	Array.from(document.querySelectorAll('.choice')).map(function (a) {
		a.classList.add('mdc-button--raised');
	});
<?php endif ?>
</script>