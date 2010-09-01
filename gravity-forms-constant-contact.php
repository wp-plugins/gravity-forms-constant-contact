<?php
/*
Plugin Name: Gravity Forms + Constant Contact
Plugin URI: http://www.seodenver.com/gravity-forms-constant-contact/
Description: Add contacts to your Constant Contact mailing list when they fill out a Gravity Forms form.
Author: Zack Katz
Version: 1.0
Author URI: http://www.seodenver.com

--------------------------------------------------
 
Copyright 2010  Katz Web Services, Inc.  (email : info@katzwebservices.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
*/


###
### Check whether this plugin has the dependencies it requires
### 

add_action('admin_notices', 'gf_cc_check'); 

function gf_cc_check() {
	$message = '';
	if(!function_exists('constant_contact_create_object') || !class_exists('RGForms')) { 
		$message = '<div id="message" class="error">';
	}
	if(!function_exists('constant_contact_create_object')) { 
		$message .= '<p>You do not have the Constant Contact API plugin enabled. <a href="'.admin_url('plugin-install.php?s=Constant%20Contact%20for%20Wordpress').'">Add it to your site.</a></p>';
	} 
	if(!class_exists('RGForms')) {
		$message .= '<p>You do not have the Gravity Forms plugin enabled. <a href="http://www.gravityforms.com">Get Gravity Forms</a>.</p>';
	}
	if($message) {
		$message .= '</div>';
		echo $message;
	}
}


add_action('admin_head-toplevel_page_gf_edit_forms', 'gf_cc_add_help' );

add_action('admin_head-toplevel_page_gf_edit_forms', 'gf_cc_add_help' );

function gf_cc_add_help() {
	
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
		
	$message .= '
	<h2>To integrate Constant Contact using Gravity Forms, you must modify your form.</h2>
	<p>For each field you want to be inserted into the Constant Contact database, you must modify it in the <em>Form Editor</em> below.</p>
	
	<h3>To add user-submissions to Constant Contact:</h3>
	<ol>
	<li>Click the "Edit" link on the field (for example, "Name")</li>
	<li>Click the tab called "Advanced" (to the right of "Properties")</li>
	<li>Check the checkbox for "Allow field to be populated dynamically"</li>
	<li>In the "Parameter Name" textbox, insert the corresponding field name for each field (For the Name field, you would add "FirstName" and "LastName")</li>
	</ol>';
	
	$message .= '<h3>YOU MUST DO THE FOLLOWING to integrate your form</h3><p>In order to comply with Constant Contact policy, you must add a checkbox field with the "Parameter Name" of <code>AddNewsletter</code>.</p><p>If you don\'t care about their policies, you must at least add a Hidden field with the "Parameter Name" of <code>AddNewsletter</code></p>';

	$message .= '<div class="clear"></div>';
	
	add_contextual_help( 'toplevel_page_gf_edit_forms', $message );
	
	// I hate enqueue scripts.
	echo '<style type="text/css">#wpbody #screen-meta { z-index:999999!important; }</style>';
}

function gf_cc_style() {  }

function gf_cc_fields() {
	return array(
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
	$ccsubmit = array();
	$checked = false;
	foreach($submit as $key => $field) {
		if(empty($field['name'])) { unset($submit[$key]); }
		if(in_array($field['name'], $gf_cc_fields)) {
			if($field['name'] == 'AddNewsletter') { $submit[$key]['value'] = $field['value'] = $checked = 1; }
			$ccsubmit["{$field['name']}"] = $field['value'];
		}
		if(is_email($field['value'])) { $email = $field['value']; }
	}
	
	###
	### Add submission to Constant Contact
	###
	
	if(!empty($ccsubmit['AddNewsletter']) && $email) {
		$cc = constant_contact_create_object();
		if(!is_object($cc)) { return; }
		
		// See if the default list is already set
		$default = get_option('cc_default_list');
		
		// If not, then let's do this.
		if(!$default || !is_array($default)) {
			
			$lists = array();
			$_lists = $cc->get_all_lists();
	
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
		}
	} else {
		if($email) {
			echo '<!-- Gravity Forms + Constant Contact: The user did not check the AddNewsletter checkbox, or it may not exist. It is required for the plugin to work. -->';
		} else {
			echo '<!-- Gravity Forms + Constant Contact: The user did not provide an email address. -->';
		}
	}
}
?>
