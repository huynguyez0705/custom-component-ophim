<!DOCTYPE html>
<html lang="vi">
	<head>
		<meta name="viewport" content="initial-scale=1.0, width=device-width">
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<link rel="profile" href="http://gmgp.org/xfn/11" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		<?php wp_head(); ?>
		<script>
			var url ='<?= get_template_directory_uri() ?>';
		</script>
		<link rel="stylesheet" type="text/css" href="<?= get_template_directory_uri() ?>/assets/css/owl.carousel.css" />
		<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,500" rel="stylesheet" type="text/css">
		<link rel="stylesheet" type="text/css" href="<?= get_template_directory_uri() ?>/assets/css/font-face.css?v=1.3.1" />
		<link rel="stylesheet" type="text/css" href="<?= get_template_directory_uri() ?>/assets/css/font-awesome.css" />
		<link rel="stylesheet" type="text/css" href="<?= get_template_directory_uri() ?>/assets/css/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="<?= get_template_directory_uri() ?>/assets/css/jquery-ui.min.css" />
		<link rel="stylesheet" type="text/css" href="<?= get_template_directory_uri() ?>/assets/css/index.css?v=<?= filemtime(get_template_directory() . '/assets/css/index.css') ?>" />
		<link rel="stylesheet" type="text/css" href="<?= get_template_directory_uri() ?>/assets/css/styles.css?v=<?= filemtime(get_template_directory() . '/assets/css/styles.css') ?>" />
		<link rel="stylesheet" type="text/css" href="<?= get_template_directory_uri() ?>/assets/css/responsive.css?v=<?= filemtime(get_template_directory() . '/assets/css/responsive.css') ?>" />

		<link rel="stylesheet" type="text/css"href="<?= get_template_directory_uri() ?>/assets/css/custom.css?v=<?= filemtime(get_template_directory() . '/assets/css/custom.css') ?>" />
		<script type="text/javascript" src="<?= get_template_directory_uri() ?>/assets/js/jquery.min.js"></script>
		<script type="text/javascript" src="<?= get_template_directory_uri() ?>/assets/js/jquery.slimscroll.min.js"></script>
		<script type="text/javascript" src="<?= get_template_directory_uri() ?>/assets/js/bootstrap2.min.js"></script>
		<script type="text/javascript" src="<?= get_template_directory_uri() ?>/assets/js/jquery-ui.min.js"></script>
		<script type="text/javascript" src="<?= get_template_directory_uri() ?>/assets/js/jquery.lazyload.min.js"></script>
		<script type="text/javascript" src="<?= get_template_directory_uri() ?>/assets/js/jquery.core.min.js"></script>
		<script type="text/javascript" src="<?= get_template_directory_uri() ?>/assets/js/functions.js?v=2.0.1"></script>
		<script type="text/javascript" src="<?= get_template_directory_uri() ?>/assets/js/js.cookie.js?v=2.1"></script>

	</head>
	<body >
		<div id="page">
			<?php include_once THEME_URL.'/templates/header.php' ?>
			<div id="content">
				<div class="main-content">

