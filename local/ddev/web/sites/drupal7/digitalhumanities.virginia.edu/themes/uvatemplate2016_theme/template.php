<?php

/**
 * @file
 * This file is empty by default because the base theme chain (Alpha & Omega) provides
 * all the basic functionality. However, in case you wish to customize the output that Drupal
 * generates through Alpha & Omega this file is a good place to do so.
 * 
 * Alpha comes with a neat solution for keeping this file as clean as possible while the code
 * for your subtheme grows. Please read the README.txt in the /preprocess and /process subfolders
 * for more information on this topic.
 */
 
 // Add some cool text to the search block form
function uvatemplate2016_theme_form_alter(&$form, &$form_state, $form_id) {
  if ($form_id == 'search_block_form') {
    // HTML5 placeholder attribute
    $form['search_block_form']['#attributes']['placeholder'] = t('Search');
	//Change the text on the search button
	$form['actions']['submit']['#value'] = t(' ');
  }

}

function uvatemplate2016_theme_preprocess_html(&$variables) {
	if (!$variables['is_front']) {
		$head_title = array(
		  'title' => strip_tags(drupal_get_title()),
		  'name' => strip_tags(variable_get('site_name', 'Drupal')) . '',
		);
		
		$variables['head_title_array'] = $head_title;
		$variables['head_title'] = implode(' | ', $head_title);
	} else {
		$head_title = array(
		  'title' => '',
		  'name' => strip_tags(variable_get('site_name', 'Drupal')) . ', U.Va.',
		);
		
		$variables['head_title_array'] = $head_title;
		$variables['head_title'] = implode('', $head_title);
	}
	
	//Fonts.com include for Bodoni Poster font
	drupal_add_css(
		'https://fast.fonts.net/cssapi/2ed92370-fb3f-4cf8-b1ab-37056ab1e4cc.css',	
	array('type' => 'external')
  	);
	
	/*Univ. Communications fonts.com account: http://fast.fonts.net/cssapi/f24e4e65-a424-4777-b426-5f56347bb31c.css */
    
}

/**
* hook_form_FORM_ID_alter
*/
function uvatemplate2016_theme_form_search_block_form_alter(&$form, &$form_state, $form_id) {
    $form['search_block_form']['#title'] = t('Search'); // Change the text on the label element
    $form['search_block_form']['#title_display'] = 'invisible'; // Toggle label visibilty
    $form['search_block_form']['#size'] = 40;  // define size of the textfield
    $form['search_block_form']['#default_value'] = t(''); // Set a default value for the textfield
    $form['actions']['submit']['#value'] = t('SEARCH'); // Change the text on the submit button

    // Add extra attributes to the text box
    // $form['search_block_form']['#attributes']['onblur'] = "if (this.value == '') {this.value = 'Search';}";
    // $form['search_block_form']['#attributes']['onfocus'] = "if (this.value == 'Search') {this.value = '';}";
    // Prevent user from searching the default text
    $form['#attributes']['onsubmit'] = "if(this.search_block_form.value==''){ alert('Please enter a search'); return false; }";

    // Alternative (HTML5) placeholder attribute instead of using the javascript
    $form['search_block_form']['#attributes']['placeholder'] = t('Enter your search term');
} 

/**
* Breadcrumb Preprocessing
*/
function uvatemplate2016_theme_breadcrumb($variables) {
	$sep = ' / ';
	if (count($variables['breadcrumb']) > 0) {
		$current_page = drupal_get_title();
		$current_page = '<span class="last-breadcrumb">' . $current_page . '</span>';
		array_push($variables['breadcrumb'], $current_page);
	  	$crumbs = implode($sep, $variables['breadcrumb']);
	  	return $crumbs; 
	}
	else {
	  	return '';
	}
 }

function uvatemplate2016_theme_alpha_preprocess_region(&$variables) {
	$theme = alpha_get_theme();
	$variables['breadcrumb'] = $theme->page['breadcrumb'];
}

function uvatemplate2016_theme_html_head_alter(&$head_elements) {
  unset($head_elements['alpha-viewport']);
}
