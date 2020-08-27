=== Contact Information Widget ===
Contributors: jaydeepchauhan
Donate link: https://jaydeepchauhanblog.wordpress.com/
Tags: widget,contact-information,contact-information-widget,contact,contact information 
Requires at least: 3.5.0
Tested up to: 5.4.2
Stable tag: 1.0.2
Requires PHP: 5.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Contact Information Widget.

== Description ==

Contact Information Plugin manage the contact related information like compnay name, description, address, email, fax, phone, website etc. in widget. You can show/hide label and icon. also you can embed the map with conformation. 

== Installation ==

1. Upload `contact-information` to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress

You will find 'Contact Information' widget in Appearance > Widgets menu.

== Frequently Asked Questions ==

= Where can I change the icon ? =

You can easily change the icon by the css. Those icon are wordpress deshicon. So you can change the dashicon by content code.

.contact_information.ci_icon .item-ci.item-ci-company::before {
    content: "\f338";
}
  
= Where can I change the label name ? =
Simple you can also manage the label name by the css. something like below,
.contact_information.ci_label .label-ci.label-ci-company::after {
    content: "My label name:";
}	

= Where can I hide the any item of contact-information ? =
Simply you can make empty value of text box and save the widget.

= Can I change the order of any item of contact-information ? =
Yes, it possible to change the order of contact-information item by the css. List support the flex css so you can manage. you have to override below css with correct order no.
.contact_information .item-ci.item-ci-company {
	order:1;
}
.contact_information .item-ci.item-ci-aboutus {
	order:2;
}
.contact_information .item-ci.item-ci-address {
	order:3;
}
.contact_information .item-ci.item-ci-phone {
	order:4;
}
.contact_information .item-ci.item-ci-mobilephone {
	order:5;
}
.contact_information .item-ci.item-ci-email {
	order:6;
}
.contact_information .item-ci.item-ci-fax {
	order:7;
}
.contact_information .item-ci-website {
	order:8;
}

== Screenshots ==

1. Admin contact information widget screen-1
2. Admin contact information widget screen-2
3. Contact information front view
4. Contact information live customize view

== Changelog ==

= 1.0.0 =
* Initial Release.

= 1.0.1 =
* Fixed small bug

= 1.0.2 =
* Tested up to 5.1.1


== Upgrade Notice ==

= 1.0 =
* Initial Release.

= 1.0.1 =
* Fixed small bug

= 1.0.2 =
* Tested up to 5.1.1