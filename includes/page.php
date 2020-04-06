<!DOCTYPE html>
<html lang="en">
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta charset="UTF-8">
		<title><?php echo $pageDisplay; ?> - Predict</title>
		<link rel="stylesheet" href="lib/mdc.min.css" defer>
		<style>
			body {
				margin: 0;
				background-color: var(--mdc-theme-background);
			}
			#centerSection {
				width: calc(100% - 2rem);
				max-width: 800px;
				margin: auto;
				padding: 1rem 0;
				position: relative;
			}
			#inlineScrim {
				background-color: transparent;
			}
		</style>
<?php file_exists('../includes/'.$pageName.'/css.php') AND include($pageName.'/css.php'); ?>
	</head>
	<body>
		<header class="mdc-top-app-bar mdc-top-app-bar--fixed" data-mdc-auto-init="MDCTopAppBar">
			<div class="mdc-top-app-bar__row">
				<section class="mdc-top-app-bar__section mdc-top-app-bar__section--align-start">
					<button class="material-icons mdc-top-app-bar__navigation-icon mdc-icon-button">menu</button>
					<span class="mdc-top-app-bar__title">Predict - <?php echo $pageDisplay; ?></span>
				</section>
			</div>
<?php if (isset($set['setLen'])): ?>
			<div id="sp">
				<?php echo str_repeat('<div class="spborder"></div><div class="spitem"></div>', $set['setLen']) . '<div class="spborder"></div>'; ?>
			</div>
			<div id="mtime"></div>
<?php endif ?>
		</header>
		<div class="mdc-top-app-bar--fixed-adjust">
			<aside class="mdc-drawer mdc-drawer--modal" data-mdc-auto-init="MDCDrawer">
				<div class="mdc-drawer__content">
					<nav class="mdc-list">
						<a class="mdc-list-item mdc-list-item--activated" href="/" aria-current="page">
							<i class="material-icons mdc-list-item__graphic" aria-hidden="true">home</i>
							<span class="mdc-list-item__text">Home</span>
						</a>
						<a class="mdc-list-item mdc-list-item--activated" href="about.php" aria-current="page">
							<i class="material-icons mdc-list-item__graphic" aria-hidden="true">info</i>
							<span class="mdc-list-item__text">About</span>
						</a>
					</nav>
				</div>
			</aside>
			<div id="inlineScrim" class="mdc-drawer-scrim"></div>
			<div id="centerSection">
<?php require_once($pageName.'/html.php'); ?>
			</div>
		</div>
		<script src="lib/mdc.min.js"></script>
		<script>
			mdc.autoInit();
			document.querySelector('.mdc-top-app-bar--fixed-adjust').style.paddingTop = document.querySelector('header.mdc-top-app-bar').offsetHeight + 'px';
			document.querySelector('.mdc-top-app-bar__navigation-icon').addEventListener('click', function () {
				document.querySelector('.mdc-drawer').MDCDrawer.open = !document.querySelector('.mdc-drawer').MDCDrawer.open;
			});
			document.querySelector('#inlineScrim').style.background = 'linear-gradient(transparent 0%, transparent '+document.querySelector('header.mdc-top-app-bar').offsetHeight+'px, rgb(0, 0, 0, 0.64) '+document.querySelector('header.mdc-top-app-bar').offsetHeight+'px, rgb(0, 0, 0, 0.64) 100%)';
		</script>
<?php file_exists('../includes/'.$pageName.'/js.php') AND include($pageName.'/js.php'); ?>
	</body>
</html>