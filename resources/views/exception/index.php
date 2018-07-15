<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Error Reporting</title>
	<?php add_style('bootstrap.min') ?>
	<?php add_style('prism') ?>
	<?php add_style('exception') ?>
	<script src="https://use.fontawesome.com/538337b606.js"></script>
</head>
<body>
	<div id="NK-MAIN-CONTENT-ERROR-REPORTER" class="container-fluid">
		<!-- Nav tabs -->
		<div class="row">
			<div class="col-md-3">
				<p class="NK-ERROR-REPORTER-TITLE p-3 border-right">
					All Files with Error/Exception
				</p>
				<ul class="nav NK-NAV-PILL md-pills pills-primary border-right">
					<?php foreach ($traces as $id => $trace): ?>
						<li class="nav-item" data-code="<?= $id ?>">
							<a class="nav-link <?php echo $id == 0 ? 'active' : '' ?>" data-toggle="tab" href="#<?= $id ?>" role="tab">
								<?= $trace['file'] ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div class="col-md-9">
				<!-- Tab panels -->
				<p class="p-3 mb-0 NK-ERROR-REPORTER-TITLE">
					<?= $message ?>
				</p>
				<div class="tab-content vertical">
					<?php foreach ($traces as $id => $trace): ?>
						<div class="px-3 tab-pane fade in show <?php echo $id == 0 ? 'active' : '' ?>" id="<?= $id ?>" role="tabpanel">
							<?php
							$fileContentByLines = explode("\n", file_get_contents($trace['file']));
							$minLine = $trace['line'] - 7 > 0 ? $trace['line'] - 7 : 0;
							$maxLine = $trace['line'] + 7 < count($fileContentByLines) ? $trace['line'] + 7 : count($fileContentByLines) - 1;
							?>
							<pre data-line="<?= $trace['line'] ?>" data-start="<?= $trace['line'] - 6 ?>" class="line-numbers language-php"><code id="code-<?= $id ?>" class="line-numbers language-php"><?php $output = ''; for ($i= $minLine; $i < $maxLine; $i++) {
								$output .= trim(htmlspecialchars($fileContentByLines[$i]), "\n")."\n";
							} echo trim($output, "\n"); ?></code></pre>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<!-- Nav tabs -->
	</div>

	<?php add_script('jquery-3.2.1.min') ?>
	<?php add_script('popper.min') ?>
	<?php add_script('bootstrap.min') ?>
	<?php add_script('prism') ?>
	<script type="text/javascript">
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		var id= $(this).parent().data('code');
		Prism.highlightElement($('#code-' + id)[0]);
	})
	</script>
</body>
</html>
