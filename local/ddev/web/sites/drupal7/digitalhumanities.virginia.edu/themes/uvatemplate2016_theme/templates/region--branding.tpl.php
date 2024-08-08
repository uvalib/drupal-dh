<div<?php print $attributes; ?>>
  <div<?php print $content_attributes; ?>>
    <?php if ($linked_logo_img || $site_name || $site_slogan): ?>
    <div class="branding-data clearfix">
      	<div class="logo-img">
          <a href="/" title="<?php print strip_tags($site_name) . ', U.Va.'; ?>"><img src="/sites/digitalhumanities.virginia.edu/themes/uvatemplate2016_theme/images/dh-logo_new.png" alt="<?php print strip_tags($site_name) . ', U.Va.' ?>" /></a>
      	</div>
      <?php if ($site_name || $site_slogan): ?>
      <?php $class = $site_name_hidden && $site_slogan_hidden ? ' element-invisible' : ''; ?>
      <hgroup class="site-name-slogan<?php print $class; ?>">        
        <?php if ($site_name): ?>
			<?php $class = $site_name_hidden ? ' element-invisible' : ''; ?>

            <?php if ($is_front): ?>        
            <h1 class="site-name<?php print $class; ?>"><?php print $linked_site_name; ?></h1>
            <?php else: ?>
            <div class="site-name<?php print $class; ?>"><?php print $linked_site_name; ?></div>
            <?php endif; ?>
        
        <?php endif; ?>
        
        <?php if ($site_slogan): ?>
        <?php $class = $site_slogan_hidden ? ' element-invisible' : ''; ?>
        <div class="site-slogan<?php print $class; ?>"><?php print $site_slogan; ?></div>
        <?php endif; ?>
      </hgroup>
      <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php print $content; ?>
  </div>
</div>