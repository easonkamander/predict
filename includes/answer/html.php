<div class="choicebox">
<?php foreach ($choices as $choice): ?>
	<div class="choice" style="background-position: <?php echo 100*(1 - $choice['prediction']); ?>%;">
		<?php echo $choice['display']; ?>
	</div>
<?php endforeach ?>
</div>

<br />

<?php if ($set['setInd'] == $set['setLen']): ?>
<div class="forwardButtons">
	<a href="question.php" class="mdc-button mdc-button--raised" id="nextsetButton" data-mdc-auto-init="MDCRipple">
		<span class="mdc-button__ripple"></span>
		<span class="mdc-button__label">New set</span>
	</a>
	<a href="/" class="mdc-button" id="backButton" data-mdc-auto-init="MDCRipple">
		<span class="mdc-button__ripple"></span>
		<span class="mdc-button__label">Back</span>
	</a>
</div>
<?php else: ?>
<div class="forwardButtons">
	<a href="question.php" class="mdc-button mdc-button--raised" id="continueButton" data-mdc-auto-init="MDCRipple">
		<span class="mdc-button__ripple"></span>
		<span class="mdc-button__label">Continue</span>
	</a>
</div>
<?php endif ?>