<div class="mdc-typography mdc-typography--headline5">Welcome To The Predictability Test</div>
<p class="mdc-typography mdc-typography--body1">You will be asked to choose from a list of numbers.</p>
<p class="mdc-typography mdc-typography--body1">Try your best to choose a random item &ndash; something that would be difficult to guess.</p>

<br />

<div class="authButtons">
<?php if (isset($_SESSION['user']) AND $_SESSION['user']): ?>
	<a href="question.php" class="mdc-button mdc-button--raised" id="authBegin" data-mdc-auto-init="MDCRipple">
		<span class="mdc-button__ripple"></span>
		<span class="mdc-button__label">Begin Test</span>
	</a>
	<a href="auth_callback.php?logout" class="mdc-button" id="authLogout" data-mdc-auto-init="MDCRipple">
		<span class="mdc-button__ripple"></span>
		<span class="mdc-button__label">Logout</span>
	</a>
<?php elseif (isset($_SESSION['user'])): ?>
	<a href="auth_callback.php" class="mdc-button mdc-button--raised" id="authGoogle" data-mdc-auto-init="MDCRipple">
		<span class="mdc-button__ripple"></span>
		<span class="mdc-button__label">Sign In</span>
	</a>
	<a href="question.php" class="mdc-button" id="authProceed" data-mdc-auto-init="MDCRipple">
		<span class="mdc-button__ripple"></span>
		<span class="mdc-button__label">Proceed</span>
	</a>
<?php else: ?>
	<a href="auth_callback.php" class="mdc-button mdc-button--raised" id="authGoogle" data-mdc-auto-init="MDCRipple">
		<span class="mdc-button__ripple"></span>
		<span class="mdc-button__label">Sign In</span>
	</a>
	<button class="mdc-button" id="authProceed" data-mdc-auto-init="MDCRipple">
		<span class="mdc-button__ripple"></span>
		<span class="mdc-button__label">Proceed Anyway</span>
	</button>
<?php endif ?>
</div>

<br />

<div id="aboutCard" class="mdc-card">
	<div class="mdc-card__primary-action" tabindex="0">
		<div id="aboutCardMedia" class="mdc-card__media mdc-card__media--16-9"></div>
		<div class="mdc-typography mdc-typography--headline5">About</div>
		<div class="mdc-typography mdc-typography--body1">
			From
			<a href='https://www.nytimes.com/2019/02/05/business/media/artificial-intelligence-journalism-robots.html'>writing news articles</a>
			to
			<a href='https://medium.com/@ahmed_elgammal/generating-art-by-learning-about-styles-and-deviating-from-style-norms-8037a13ae027'>painting abstract art</a>,
			<span class='f2'>computers</span>,
			which are completely
			<span class='f2'>logical</span>,
			are getting better at imitating 
			<span class='f1'>creative human behavior</span>.
		</div>
		<button id="expandCardButton" class="mdc-icon-button material-icons">expand_more</button>
	</div>
</div>

<?php if (empty($_SESSION['user'])): ?>
<div class="mdc-dialog" role="alertdialog" aria-modal="true" aria-labelledby="ssi-title" aria-describedby="ssi-content" data-mdc-auto-init="MDCDialog">
	<div class="mdc-dialog__container">
		<div class="mdc-dialog__surface">
			<h2 class="mdc-dialog__title" id="ssi-title">Skip Sign In?</h2>
			<div class="mdc-dialog__content" id="ssi-content">
				Signing in with Google lets us remember your results for next time and makes our data more extensive.
			</div>
			<footer class="mdc-dialog__actions">
				<button type="button" class="mdc-button mdc-dialog__button" data-mdc-dialog-action="close">
					<span class="mdc-button__label">Cancel</span>
				</button>
				<a href="auth_callback.php?nonauth" type="button" class="mdc-button mdc-dialog__button" data-mdc-dialog-action="accept" data-mdc-auto-init="MDCRipple">
					<span class="mdc-button__ripple"></span>
					<span class="mdc-button__label">Proceed</span>
				</a>
			</footer>
		</div>
	</div>
	<div class="mdc-dialog__scrim"></div>
</div>
<?php endif ?>