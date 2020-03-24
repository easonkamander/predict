<style>
	#sp {
		display: grid;
		height: 0.75rem;
		background-color: #AEAEAE;
		border-top: 0.1rem solid #424242;
		border-bottom: 0.1rem solid #424242;
<?php if ($set['setLen'] > 1): ?>
		grid-template-columns: 0 repeat(calc(<?php echo $set['setLen']; ?> - 1), 4fr minmax(1em, 1fr)) 4fr 0;
<?php else: ?>
		grid-template-columns: 0 4fr 0;
<?php endif ?>
	}
	@keyframes slide {
		to {
			background-position: 0 0;
		}
	}
	.spborder:not(:first-child):not(:last-child) {
		background-color: var(--mdc-theme-primary);
		border-left: 0.1rem solid #424242;
		border-right: 0.1rem solid #424242;
	}
	.spitem:nth-child(<?php echo 2 * $set['setInd']; ?>) {
		background-size: 200% 100%;
		background-image: linear-gradient(to right, transparent 50%, var(--mdc-theme-secondary) 50%);
		background-position: 100% 0;
		animation: slide 0.75s forwards;
	}
	.spborder:nth-child(<?php echo 2 * $set['setInd'] + 1; ?>) {
		background-size: 200% 100%;
		background-image: linear-gradient(to right, var(--mdc-theme-secondary) 50%, var(--mdc-theme-primary) 50%);
		background-position: 100% 0;
		animation: slide 0.75s forwards;
	}
<?php if ($set['setInd'] == $set['setLen']): ?>
	@keyframes compressBorder {
		to {
			margin: 0 50%;
			border: 0;
		}
	}
	.spborder {
		animation: compressBorder 1s ease 0.75s forwards;
	}
	@keyframes fadeBackground {
		to {
			background-color: transparent;
			border-color: transparent;
		}
	}
	#sp {
		animation: fadeBackground 1s ease 1.75s forwards;
	}
<?php endif ?>
	#mtime {
		height: 0.75rem;
		background-color: var(--mdc-theme-primary);
	}
	.forwardButtons {
		display: grid;
		grid-template-columns: 1fr;
		grid-gap: 0.75rem;
		max-width: max-content;
		margin: auto;
	}
	.choicebox {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(<?php echo $displayWidth; ?>ch, 1fr));
		grid-gap: 1rem;
		justify-items: stretch;
		align-items: stretch;
	}
	.choice {
		width: 100%;
		background-size: 200% 100%;
		background-position: 0 0;
		border-radius: 4px;
		color: white;
		font-size: 1rem;
		font-family: Roboto, sans-serif;
		letter-spacing: .0892857143em;
		text-transform: uppercase;
		padding: 0 8px 0 8px;
		display: inline-flex;
		position: relative;
		align-items: center;
		justify-content: center;
		box-sizing: border-box;
		border: none;
		outline: none;
		line-height: inherit;
		user-select: none;
		-webkit-appearance: none;
		overflow: visible;
		vertical-align: middle;
		height: 36px;
	}
	.choice#actual {
		background-image: linear-gradient(to left, var(--mdc-theme-primary) 50%, var(--mdc-theme-secondary) 50%);
	}
	.choice:not(#actual) {
		background-image: linear-gradient(to left, grey 50%, var(--mdc-theme-secondary) 50%);
	}
</style>