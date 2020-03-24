<div id="waitCover"></div>
<form action="answer.php" method="POST">
	<div class="choicebox">
<?php foreach ($choices as $i => $choice): ?>
		<label class="choiceOuter" for="choice<?php echo $i; ?>">
			<input type="submit" id="choice<?php echo $i; ?>" name="answer" value="<?php echo $i; ?>" />
			<div class="choice mdc-button mdc-button">
				<div class="mdc-button__ripple"></div>
				<span class="mdc-button__label">
					<?php echo $choice['display']; ?>
				</span>
			</div>
		</label>
<?php endforeach ?>
	</div>
</form>