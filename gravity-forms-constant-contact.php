<?php
/*
Plugin Name: Gravity Forms + Constant Contact
Plugin URI: http://www.seodenver.com/gravity-forms-constant-contact/
Description: Add contacts to your Constant Contact mailing list when they fill out a Gravity Forms form.
Author: Katz Web Services, Inc.
Version: 1.1
Author URI: http://www.seodenver.com

--------------------------------------------------

Copyright 2010 Katz Web Services, Inc.  (email: info@katzwebservices.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
	
*/


###
### Check whether this plugin has the dependencies it requires
### 

add_action('admin_notices', 'gf_cc_check'); 

function gf_cc_check() {
	global $pagenow, $page;
	
	if($pagenow != 'plugins.php') { return false;}
	$message = '';
	if(!function_exists('constant_contact_create_object')) { 
		if(file_exists(WP_PLUGIN_DIR.'/constant-contact-api/constant-contact-api.php')) {
			$message .= '<p>Constant Contact API is installed. <strong>Activate Constant Contact API</strong> to use the Gravity Forms + Constant Contact plugin.</p>';
		} else {
			$message .= '<p>You do not have the Constant Contact API plugin enabled. <a href="'.admin_url('plugin-install.php?tab=plugin-information&plugin=constant-contact-api&from=plugins&TB_iframe=true&width=600&height=550').'" class="thickbox" title="Install Constant Contact API">Add it to your site.</a></p>';
		}
	} 
	if(!class_exists('RGForms')) {
		if(file_exists(WP_PLUGIN_DIR.'/gravityforms/gravityforms.php')) {
			$message .= '<p>Gravity Forms is installed. <strong>Activate Gravity Forms</strong> to use the Gravity Forms + Constant Contact plugin.</p>';
		} else {
			$message .= '<h2><a href="http://sn.im/gravityforms">Gravity Forms</a> is required.</h2><p>You do not have the Gravity Forms plugin enabled. <a href="http://sn.im/gravityforms">Get Gravity Forms</a>.</p>';
		}
	}
	if(!empty($message)) {
		echo '<div id="message" class="error">'.$message.'</div>';
	}
}

add_action('admin_head-toplevel_page_gf_edit_forms', 'gf_cc_add_help' );
add_action('admin_head-forms_page_gf_new_form', 'gf_cc_add_help' );

function gf_cc_add_help() {
	$i = 0;
	$gf_cc_fields = gf_cc_fields();
	
	$message = '
	<div style="float:right; width:40%; ">
		<h3>Available fields are:</h3>
		<ul style="width:28%; float:left;">';
		foreach($gf_cc_fields as $field) {
			$message .= '<li>'.$field.'</li>';
			$i++;
			if($i == floor(sizeof($gf_cc_fields)/3)) {
				$message .= '</ul><ul style="width:28%; float:left;">';
				$i = 0;
			}
		}
		$message .= '</ul></div>';
	$a = ' style="text-align:left!important;"';	
	$message .= <<<EOD
<div class="wrap">
	<h2>To integrate Constant Contact using Gravity Forms, you must modify your form.</h2>
	<p$a>For each field you want to be inserted into the Constant Contact database, you must modify it in the <em>Form Editor</em> below.</p>
	
	<h3>To show email list choices:</h3>
	<ol>
	<li>Add a field for users to choose email lists (Checkboxes, Dropdown, or Multiple Choice fields are good)</li>
	<li>Click the "Bulk Add / Predefined Choices" button</li>
	<li>Choose "Constant Contact Lists" from the bottom of the left column</li>
	<li>Click the "Update Choices" button</li>
	<li>Check the box to the left of the names of the lists you would like selected by default</li>
	<li>Click the tab called "Advanced" (to the right of "Properties")</li>
	<li>Check the checkbox for "Allow field to be populated dynamically"</li>
	<li>In the "Parameter Name" textbox, enter <code>EmailLists</code></li>
	<li>Save the form</li>
	</ol>
	
	<h3>To add fields to Constant Contact:</h3>
	<ol>
	<li>Click the "Edit" link on the field (for example, "Name")</li>
	<li>Click the tab called "Advanced" (to the right of "Properties")</li>
	<li>Check the checkbox for "Allow field to be populated dynamically"</li>
	<li>In the "Parameter Name" textbox, insert the <strong>corresponding field name</strong> for each field (For the Name field, you would add <code>FirstName</code> and <code>LastName</code>)</li>
	</ol>
	
	<p$a>If you don't give users the choice of which lists to subscribe to, in order to comply with Constant Contact policy, you must add a checkbox field with the "Parameter Name" of <code>AddNewsletter</code>. (see instructions above).</p>
</div>
	<div class="clear"></div>
EOD;
	
	add_contextual_help( 'toplevel_page_gf_edit_forms', $message );
	add_contextual_help( 'forms_page_gf_new_form', $message );
	
	// I hate enqueue scripts.
	echo '<style type="text/css">#wpbody #screen-meta { z-index:999999!important; }</style>';
}


function gf_cc_fields() {
	return array(
			'EmailLists', // ZK added
			'AddNewsletter', // ZK added
			'FirstName', 
			'MiddleName', 
			'LastName',
			'JobTitle', 
			'CompanyName',
			'HomePhone', 
			'WorkPhone',
			'Addr1',
			'Addr2',
			'Addr3',
			'City',
			'StateCode',
			'StateName',
			'CountryCode',
			'CountryName',
			'PostalCode',
			'SubPostalCode',
			'Note',
			'CustomField1',
			'CustomField2',
			'CustomField3',
			'CustomField4',
			'CustomField5',
			'CustomField6',
			'CustomField7',
			'CustomField8',
			'CustomField9',
			'CustomField10',
			'CustomField11',
			'CustomField12',
			'CustomField13',
			'CustomField14',
			'CustomField15',
		);
}

###
###	Add a hook into Gravity Forms' submission process and add the contact
###

add_action('gform_post_submission', 'gf_cc_submit', 1, 2);

function gf_cc_submit($entry, $form) {
	
	if(!function_exists('constant_contact_create_object')) { return; }
	
	$gf_cc_fields = gf_cc_fields();
	
	function gf_cc_process_field($field, $entry) {
		$result = array();
		$entries = false;
		
		if(is_array($field['inputs'])) {
			foreach($field['inputs'] as $input) { if(isset($entry["{$input['id']}"])) { $entries = true; }}
			if(!$entries) { return false; }
		}
		
		if(((isset($entry["{$field['id']}"]) || $entries) || $field['type'] == 'hidden') && is_numeric($field['id'])) { 
			$result['value'] = $entry["{$field['id']}"];
			$result['name'] = isset($field['inputName']) ? $field['inputName'] : $field['name'];
			if(empty($result['name']) && !empty($field['parentName'])) { $result['name'] = $field['parentName']; }
			$result['id'] = $field['id'];
			$result['label'] = $field['label'];
			return array($result, $form);
		} 
		
		return false;
	}

	$i = 0;
	foreach($form['fields'] as $key => $field) {	
		
		// For main-level fields with no sub-fields
		if($result = gf_cc_process_field($field, $entry)) {
			$submit[$i] = $result[0];
			$form = $result[1];
			$i++;
		}
		
		// For all sub-fields
		if(is_array($field['inputs'])) {
			foreach($field['inputs'] as $input) {
				if($result = gf_cc_process_field($input, $entry)) {
					$submit[$i] = $result[0];
					$form = $result[1];
					$i++;
				}
			}
		} else {
			$i++;
		}
	}

	// We format the submission to match up with  Constant Contact's API field names
	$ccsubmit = $addedlists = array();
	$checked = false;
	$listid = '';
	foreach($submit as $key => $field) {
		if(empty($field['name'])) { unset($submit[$key]); }
		if(in_array($field['name'], $gf_cc_fields)) {
			if($field['name'] == 'AddNewsletter') { $submit[$key]['value'] = $field['value'] = $checked = 1; }
			$ccsubmit["{$field['name']}"] = $field['value'];
		}
		if($field['name'] == 'Lists' || $field['name'] == 'CCLists' || $field['name'] == 'EmailLists') {
			$ccsubmit["{$field['name']}"] = $field['value'];
			$listid = (int)$field['id'];
		}
		if(floor($field['id']) == $listid) {
			if(!empty($field['value']) && is_numeric($field['value']) || $field['value'] === '0') {
				$addedlists[$field['value']] = $field['label']; 
			}
		}
		
		if(is_email($field['value'])) { $email = $field['value']; }
	}
	
	
	###
	### Add submission to Constant Contact
	###
	
	if((!empty($ccsubmit['AddNewsletter']) || !empty($addedlists)) && $email) {
		$cc = constant_contact_create_object();
		if(!is_object($cc)) { return; }
		
		if(empty($addedlists)) {
			// See if the default list is already set
			$default = get_option('cc_default_list');
			
			// If not, then let's do this.
			if((empty($default) && $default !== 0) || !is_array($default)) {
				
				$lists = array();
				if(function_exists('constant_contact_get_lists')) {
					$_lists = constant_contact_get_lists();
				} else {
					$_lists = $cc->get_all_lists();
				}
				
				if($_lists) {
					foreach($_lists as $k => $v) {
						if(!empty($v['OptInDefault']) && $v['OptInDefault'] != 'false') {
							$_lists[$k] = $v['id'];
						} else {
							unset($_lists[$k]);
						}
					}
					
					$newlists = array();
					foreach($_lists as $list_id):
							$list = $cc->get_list($list_id);
							$newlists[$list['id']] = $list['Name'];
					endforeach;
					$lists = $newlists;
				}
				
				update_option('cc_default_list', $lists);
			} else {
				$lists = $default;
			}
		} else {
			$lists = $addedlists;
		}

		$lists = array_keys($lists);
		
		$cc->set_action_type('contact'); // important, tell CC that the contact made this action
		
		// Find out whether the user has already registered. If so, update don't create
		$contact = $cc->query_contacts($email);
		
		if($contact) {
			$contact = $cc->get_contact($contact['id']);
			$status = $cc->update_contact($contact['id'], $email, $lists, $ccsubmit);
		} else {
			$status = $cc->create_contact($email, $lists, $ccsubmit);
		}
		if(empty($status)) {
			echo '<!-- Gravity Forms + Constant Contact: There was an error. Please report to info@katzwebservices.com -->';
		} else {
			echo '<!-- Gravity Forms + Constant Contact: Successfully added to lists '.implode(', ', $lists).' -->';
		}
	} else {
		if($email) {
			echo '<!-- Gravity Forms + Constant Contact: The user was not added: the user did not check the AddNewsletter checkbox or it may not exist...and/or the user did not select any email lists shown.  -->';
		} else {
			echo '<!-- Gravity Forms + Constant Contact: The user did not provide an email address. -->';
		}
	}
}

add_filter("gform_predefined_choices", "gf_cc_add_predefined_choice");
function gf_cc_add_predefined_choice($choices){
	
	$lists = get_transient('gfcc_lists');
		
	if(!$lists) {
		$cc = constant_contact_create_object();
		if(!is_object($cc)) { return; }
			
		$_lists = array();
		if(function_exists('constant_contact_get_lists')) {
			$_lists = constant_contact_get_lists();
		} else {
			$_lists = $cc->get_all_lists();
		}
		if($_lists) {
			foreach($_lists as $k => $v) {
				$_lists[$k] = $v['id'];
			}
			
			$newlists = array();
			foreach($_lists as $list_id):
				$list = $cc->get_list($list_id);
				$newlists[] = $list['Name']. '|'.$list['id'];
			endforeach;
			$lists = $newlists;
		}
		set_transient('gfcc_lists', $lists, 60*60*24*7);
	}
	if(is_array($lists)) {
		$choices["Constant Contact Lists"] = $lists;
		return $choices;
	}
    return;
}

?>