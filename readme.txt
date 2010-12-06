=== Gravity Forms + Constant Contact ===
Tags: gravity forms, forms, gravity, form, crm, gravity form, mail, email, newsletter, Constant Contact, plugin, sidebar, widget, mailing list, API, email marketing, newsletters
Requires at least: 2.8
Tested up to: 3.0.2
Stable tag: trunk
Contributors: katzwebdesign
Donate link:https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=zackkatz%40gmail%2ecom&item_name=Gravity%20Forms+Constant%20Contact&no_shipping=0&no_note=1&tax=0&currency_code=USD&lc=US&bn=PP%2dDonationsBF&charset=UTF%2d8

Add contacts to your Constant Contact mailing list when they submit a <a href="http://sn.im/gravityforms" rel="nofollow">Gravity Forms</a> form.

== Description ==

> This plugin requires the magnificent <a href="http://sn.im/gravityforms" rel="nofollow">Gravity Forms</a> plugin and a <a href="http://bit.ly/tryconstantcontact" rel="nofollow">Constant Contact</a> account.

###Integrate Constant Contact with Gravity Forms
If you use Constant Contact email service and the Gravity Forms plugin, you're going to want this plugin!

Activate this plugin and slightly modify your forms, and you'll be able to add users to your Constant Contact lists.  Users can choose which email lists they are subscribed to.

####Gravity Forms + Constant Contact Requirements
* <a href="http://wordpress.org/extend/plugins/constant-contact-api/">Constant Contact API plugin</a>
* <a href="http://sn.im/gravityforms" rel="nofollow">Gravity Forms plugin</a>
* PHP 5

### You may also be interested in:

* <a href="http://wordpress.org/extend/plugins/gravity-forms-addons/">Gravity Forms Directory & Addons Plugin</a> - Turn Gravity Forms into a directory plugin, and extend the functionality

== Installation == 
<h2>To integrate Constant Contact using Gravity Forms, you must modify your form.</h2>
<p>For each field you want to be inserted into the Constant Contact database, you must modify it in the <em>Form Editor</em> below.</p>
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
<h3>To add user-submissions to Constant Contact:</h3>
<ol>
<li>Click the "Edit" link on the field (for example, "Name")</li>
<li>Click the tab called "Advanced" (to the right of "Properties")</li>
<li>Check the checkbox for "Allow field to be populated dynamically"</li>
<li>In the "Parameter Name" textbox, insert the corresponding field name for each field (For the Name field, you would add "FirstName" and "LastName")</li>
<li>Save the form.</li>
<li>Try the form. If it doesn't work, <a href="mailto:info@katzwebservices.com">contact the plugin author</a>.</li>

<p>If you don't give users the choice of which lists to subscribe to, in order to comply with Constant Contact policy, you must add a checkbox field with the "Parameter Name" of <code>AddNewsletter</code>. (see instructions above).</p>

</ol>
<h3>Available fields to integrate:</h3>
<ul>
<li>EmailLists</li>
<li>AddNewsletter <strong>(required if not using <code>EmailLists</code>)</strong></li>
<li>FirstName</li>
<li>MiddleName</li>
<li>LastName</li>
<li>JobTitle</li>
<li>CompanyName</li>
<li>HomePhone</li>
<li>WorkPhone</li>
<li>Addr1</li>
<li>Addr2</li>
<li>Addr3</li>
<li>City</li>
<li>StateCode</li>
<li>StateName</li>
<li>CountryCode</li>
<li>CountryName</li>
<li>PostalCode</li>
<li>SubPostalCode</li>
<li>Note</li>
<li>CustomField1</li>
<li>CustomField2</li>
<li>CustomField3</li>
<li>CustomField4</li>
<li>CustomField5</li>
<li>CustomField6</li>
<li>CustomField7</li>
<li>CustomField8</li>
<li>CustomField9</li>
<li>CustomField10</li>
<li>CustomField11</li>
<li>CustomField12</li>
<li>CustomField13</li>
<li>CustomField14</li>
<li>CustomField15</li>
</ul>

== Frequently Asked Questions == 

= How do I use the plugin? =
You can read the Installation tips above, or you can also click the <strong>Help</strong> tab on the Gravity Forms <em>Edit Forms</em> page. The Help tab will have all the instructions right there.

<em>There will be a YouTube video soon showing how to configure this plugin in more detail.</em>

= What's the benefit of this plugin? =
Instead of using the Constant Contact API plugin signup form and the Gravity Forms contact form, you can now integrate them easily. You save a step!

= Does a form need to have an email address? =
For a submitted entry to be added to the Constant Contact database, the form requires an email address.

= What is the plugin license? =
This plugin is released under a GPL license.

== Screenshots ==

1. Users can choose which Constant Contact lists they are added to.

== Upgrade Notice ==

= 1.1 = 
* Added list selection capability - allow users to choose which lists they are subscribed to (view the plugin's Installation tab or the Help tab on the Edit Form page to learn more)
* Improved notices if Gravity Forms or Constant Contact API is not installed or activated

= 1.0 = 
* No upgrade notice, since this is the first version!

== Changelog ==

= 1.1 = 
* Added list selection capability - allow users to choose which lists they are subscribed to (view the plugin's Installation tab or the Help tab on the Edit Form page to learn more)
* Improved notices if Gravity Forms or Constant Contact API is not installed or activated

= 1.0 =
* Launched plugin