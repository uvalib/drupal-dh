<?php
/**
 * @file
 * uva_template_2015_elements.features.fe_block_boxes.inc
 */

/**
 * Implements hook_default_fe_block_boxes().
 */
function uva_template_2015_elements_default_fe_block_boxes() {
  $export = array();

  $fe_block_boxes = new stdClass();
  $fe_block_boxes->info = 'Call to Action Block';
  $fe_block_boxes->format = 'filtered_html';
  $fe_block_boxes->machine_name = 'calltoaction';
  $fe_block_boxes->body = '<h2>
	Call <span>to</span> Action</h2>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras lacinia, lorem non volutpat tristique, tortor felis tincidunt lacus, sed porta elit velit sed dui. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sollicitudin nulla eu metus congue, at ultricies metus tincidunt.</p>
<h4 class="button">
	<a href="#">Click this Button</a></h4>
';

  $export['calltoaction'] = $fe_block_boxes;

  $fe_block_boxes = new stdClass();
  $fe_block_boxes->info = 'Footer Contact';
  $fe_block_boxes->format = 'filtered_html';
  $fe_block_boxes->machine_name = 'footer_contact';
  $fe_block_boxes->body = '<div class="contact first">
	<p>P.O. Box 400229</p>
	<p>400 Ray C. Hunt Drive</p>
	<p>Second Floor</p>
	<p>Charlottesville, VA 22901</p>
</div>
<div class="contact second">
	<p><strong>PHONE: </strong>434 924 1400</p>
	<p><strong>FAX: </strong>434 924-0938</p>
	<p><strong>EMAIL:</strong> <a href="cacsdrupal@virginia.edu">cacsdrupal@virginia.edu</a></p>
</div>
<p class="copyright">&copy; [date:custom:Y] By the Rector and Visitors of the University of Virginia</p>
';

  $export['footer_contact'] = $fe_block_boxes;

  $fe_block_boxes = new stdClass();
  $fe_block_boxes->info = 'UVA Bar - Search Toggle';
  $fe_block_boxes->format = 'filtered_html';
  $fe_block_boxes->machine_name = 'search_toggle';
  $fe_block_boxes->body = '<div id="mobile-menu"><p>&nbsp;</p></div>
<div class="search-open-button" id="search-toggle-button">
	<p>&nbsp;</p>
</div>
';

  $export['search_toggle'] = $fe_block_boxes;

  $fe_block_boxes = new stdClass();
  $fe_block_boxes->info = 'Social Media Icons';
  $fe_block_boxes->format = 'full_html';
  $fe_block_boxes->machine_name = 'social_media';
  $fe_block_boxes->body = '<div>
	<p><img alt="YouTube" id="youtube" src="/sites/all/themes/global/images/2015-template/youtube-white.png" /> YouTube</p>
	<p><img alt="Twitter" id="twitter" src="/sites/all/themes/global/images/2015-template/twitter-white.png" /> Twitter</p>
	<p><img alt="Facebook" id="facebook" src="/sites/all/themes/global/images/2015-template/facebook-white.png" /> Facebook</p>
</div>
';

  $export['social_media'] = $fe_block_boxes;

  $fe_block_boxes = new stdClass();
  $fe_block_boxes->info = 'UVA Bar - UVA Logo ';
  $fe_block_boxes->format = 'filtered_html';
  $fe_block_boxes->machine_name = 'uvabar_logo';
  $fe_block_boxes->body = '<p><a href="http://www.virginia.edu/" target="_self"><img src="/sites/all/themes/global/images/2015-template/uva-logo-inline.png" /></a></p>
';

  $export['uvabar_logo'] = $fe_block_boxes;

  return $export;
}
