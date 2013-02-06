=== Response Promotion Redeemer (PRO) ===
Contributors: bielefeldt
Tags: promotion, promo, promo portal, coupon codes, partner promotion
Requires at least: 3.3.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The Response Promotion Redemption plugin allows you to create partner promotions with list of your promotion codes and your partners.

== Description ==

With the Response Promotion Redemption plugin installed you will have the option to include the functionality onto a page or on a custom content type page called Promo. 

You will enter the Partner Portal URL (the url to which the promotion is allocated), the send from email address(the email in which the user will receiver their partner code from), select the partner type (redirect, query and soon cross domain mysql connection) the options that go with each are self explanatory. 

Then you will upload your CSV file with your codes and the corresponding partner codes, this will create a table on the db for tracking used and unused codes. There is a download link for an example CSV file for you to populate with your own codes. 

When a code is used then the user name and email will be stored in the db and displayed in the individual edit page/post screen. From there you can download a CSV version of your code db with current user info. 

Note: once you upload a CSV you cannot change it, mainly be cause if you uploaded another csv it would over right your currently used codes and user info(the plugin won't allow you to do that by design). 

The plugin also creates a short code selector in the WYSIWYG editor so theta you can embed the form into the actual page/promo. The plugin takes it from there.



== Installation ==


1. Upload `response-promotion-redeemer` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

Form short-code:

[promo-form]
attr 1 = start_message="This is the message to be used in the beginig of your email" 
attr 2 = end_message="This is the message to be used in the end of your email and confirmation display message on the front end to users"

adding "<nl />" in either attribute will create a paragraph break in you plaintext email
adding "<br />" in either attribute will create a line break in you plaintext email

== Frequently Asked Questions ==

= How do I get the form to show to the user? =

From the edit page/promo screen select the "Visual" tab and select and copy "[promo-form]" from the Promo Codes short codes meta-box and paste it into your editor where you would like the form to be displayed.

= Who do I export the current user base and information? =

From the edit page/promo screen you can either click the excel icon in the "Add Promo.csv File" tab on the right or you can click the "Download The complete .CSV file here" in the blue bar under the main WYSIWYG editor.

= Before you upload your .csv file... =

Be absolutely positive that you DO NOT have duplicate codes, if there are duplicates you will get invalid promo code errors on the front end for the duplicated codes. The plugin is developed so that once you upload you .csv you cannot change it, if you could you would run into issues with database over/rewrites.

I have found that comma separated .CSV files saved on a mac will cause the import function to break. Be sure to save your .CSV files out as a "Windows Comma Separated (.csv)" if you are using a mac. Also Note: there is a link to download a template .CSV file in the right meta sidebar of the edit page window. when you create a new page or promo
