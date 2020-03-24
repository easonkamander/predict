<style>
	@keyframes halfslide {
		to {
			background-position: 50% 0;
		}
	}
	@keyframes slide {
		to {
			background-position: 0 0;
		}
	}
	#sp {
		display: grid;
		height: 0.75rem;
		background-color: #AEAEAE;
		border-top: 0.1rem solid #424242;
		border-bottom: 0.1rem solid #424242;
<?php if ($setLen > 1): ?>
		grid-template-columns: 0 repeat(calc(<?php echo $setLen; ?> - 1), 4fr minmax(1em, 1fr)) 4fr 0;
<?php else: ?>
		grid-template-columns: 0 4fr 0;
<?php endif ?>
	}
	.spborder:not(:first-child):not(:last-child) {
		background-color: var(--mdc-theme-primary);
		border-left: 0.1rem solid #424242;
		border-right: 0.1rem solid #424242;
	}
	.spborder:nth-child(<?php echo 2 * $setInd + 1; ?>) {
		background-size: 200% 100%;
		background-image: linear-gradient(to right, var(--mdc-theme-primary) 50%, var(--mdc-theme-secondary) 50%);
		background-position: 100% 0;
<?php if ($loadAnimation): ?>
		animation: slide 0.75s forwards;
<?php else: ?>
		animation: slide 0s forwards;
<?php endif ?>
	}
	.spitem:nth-child(<?php echo 2 * $setInd + 2; ?>) {
		background-size: 200% 100%;
		background-image: linear-gradient(to right, var(--mdc-theme-secondary) 50%, transparent 50%);
		background-position: 100% 0;
<?php if ($loadAnimation): ?>
		animation: slide 0.75s forwards;
<?php else: ?>
		animation: slide 0s forwards;
<?php endif ?>
	}
	#mtime {
		height: 0.75rem;
		background-color: #AEAEAE;
<?php if ($maxTime and $minTime): ?>
		background-size: 300% 100%;
		background-image: linear-gradient(to right, var(--mdc-theme-secondary) 33.333333%, var(--mdc-theme-primary) 33.333333%, var(--mdc-theme-primary) 66.666666%, #AEAEAE 66.666666%);
		background-position: 100% 0;
		animation: halfslide <?php echo $minTime; ?>s linear forwards, slide <?php echo $maxTime; ?>s linear <?php echo $minTime; ?>s forwards;
<?php elseif ($minTime): ?>
		background-size: 200% 100%;
		background-image: linear-gradient(to right, var(--mdc-theme-primary) 50%, #AEAEAE 50%);
		background-position: 100% 0;
		animation: slide <?php echo $minTime; ?>s linear forwards;
<?php elseif ($maxTime): ?>
		background-size: 200% 100%;
		background-image: linear-gradient(to right, var(--mdc-theme-secondary) 50%, var(--mdc-theme-primary) 50%);
		background-position: 100% 0;
		animation: slide <?php echo $maxTime; ?>s linear forwards;
<?php else: ?>
		background-color: var(--mdc-theme-primary);
<?php endif ?>
	}
	#waitCover {
		position: absolute;
		width: 100%;
		height: calc(100% - 32px);
		z-index: 2;
	}
	.choicebox {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(<?php echo $_SESSION['question']['displayWidth']; ?>ch, 1fr));
		grid-gap: 1rem;
		justify-items: stretch;
		align-items: stretch;
	}
	.choiceOuter input {
		display: none;
	}
	.choice {
		width: 100%;
		cursor: pointer;
		text-align: center;
		min-width: auto;
	}
	.choice:not(.mdc-button--raised) {
		color: black;
	}
	.choice .mdc-button__label {
		font-size: 1rem;
	}
</style>