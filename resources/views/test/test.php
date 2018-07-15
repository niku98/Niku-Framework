<!DOCTYPE html>
<html lang="vi" dir="ltr">
<head>
	<meta charset="utf-8">
	<title></title>
</head>
<body>
	<form method="post" action="<?php echo url("post-process") ?>" enctype="multipart/form-data">
		<input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
		<fieldset class="form-group">
			<input type="file" name="file">
			<input type="text" name="name" value="a;lsdkfjasoi">
		</fieldset>
		<button type="submit" class="btn btn-primary">Mạnh</button>
	</form>
	<script
	src="http://code.jquery.com/jquery-3.3.1.min.js"
	integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
	crossorigin="anonymous"></script>
</body>
</html>
