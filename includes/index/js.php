<script>
document.querySelector('#aboutCard').addEventListener('click', function () {
	location.assign('about.php');
});
<?php if (!isset($_SESSION['user'])): ?>
	document.querySelector('#authProceed').addEventListener('click', function () {
		document.querySelector('.mdc-dialog').MDCDialog.open();
	});
<?php endif ?>
</script>