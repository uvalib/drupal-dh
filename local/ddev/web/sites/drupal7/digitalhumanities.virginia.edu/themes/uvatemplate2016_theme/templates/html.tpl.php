<?php print $doctype; ?>
<html lang="<?php print $language->language; ?>" dir="<?php print $language->dir; ?>"<?php print $rdf->version . $rdf->namespaces; ?>>
<head<?php print $rdf->profile; ?>>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php print $head; ?>
  <title><?php print $head_title; ?></title>  
  <?php print $styles; ?>
  <?php print $scripts; ?>
  <!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
  <link rel="stylesheet" href="<?php echo $GLOBALS['base_url']; ?>/sites/all/themes/global/css/font-awesome.min.css">
 <!--Acess Analytics Script -->
  <script src="https://cdn.levelaccess.net/accessjs/YW1wX3V2YTExMDA/access.js" type="text/javascript"></script>
</head>
<body<?php print $attributes;?>>
  <div id="skip-link">
    <a href="#main-content" class="element-invisible element-focusable"><?php print t('Skip to main content'); ?></a>
  </div>
  <?php print $page_top; ?>
  <?php print $page; ?>
  <?php print $page_bottom; ?>
  <script src="<?php echo $GLOBALS['base_url']; ?>/<?php echo drupal_get_path('theme',$GLOBALS['theme']); ?>/js/imagesloaded.pkgd.min.js"></script>
  <script src="<?php echo $GLOBALS['base_url']; ?>/<?php echo drupal_get_path('theme',$GLOBALS['theme']); ?>/js/masonry.pkgd.min.js"></script>
  <script src="<?php echo $GLOBALS['base_url']; ?>/<?php echo drupal_get_path('theme',$GLOBALS['theme']); ?>/js/script.js"></script>
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-144746181-1"></script>
  <!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-144746181-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-144746181-1');
</script>
</body>
</html>
