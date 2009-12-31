=== LinkedIn hResume ===
Contributors: bradt
Tags: linkedin, resume, hresume, microformat, cv
Requires at least: 2.0.0
Tested up to: 2.9
Stable tag: 0.3.2

Include your LinkedIn resume in any Wordpress page and style it how you like.

== Description ==

LinkedIn hResume for Wordpress grabs the [hResume microformat](http://microformats.org/wiki/hresume) 
block from your LinkedIn public profile page allowing you to add it to any Wordpress page and apply 
your own styles to it.

**New in version 0.3 - 2009-04-14**

* Added proper [Wordpress shortcode](http://codex.wordpress.org/Shortcode_API) support
* Added function *lnhr&#95;get&#95;hresume* function for use in Wordpress templates
* Added some error checking

== Installation ==

1. Set your LinkedIn public profile to "Full View"<br />
   (default is "Basic View")
2. Download linkedin-hresume.*&lt;version&gt;*.zip
3. Unzip the archive
4. Upload the *linkedin-hresume* folder to your *wp-content/plugins* directory
5. Activate the plugin in the WordPress Admin
6. In the Wordpress Admin, create a new page containing:<br />
   [lnhr url="*&lt;your Linkedin public profile URL&gt;*" caching="*on|off*"]

**Example**

[lnhr url="http://www.linkedin.com/in/bradt" caching="on"]<br />
This will display my resume with caching turned on.

== Demos ==

The following are live demos:

* [Chris Benard](http://chrisbenard.net/resume/)
* [Brad Touesnard](http://bradt.ca/resume/)

Post your resume URL on the [support forum](http://wordpress.org/tags/linkedin-hresume) and I will add it to the list.

== Customization ==

In order to get your resume to look like [mine](http://bradt.ca/resume/), you will 
need to apply some custom CSS.  I've also used a [Wordpress page template](http://codex.wordpress.org/Pages#Page_Templates) 
to get rid of the sidebar, header, etc.  My CSS and page template are included in the plugin download 
in the `extras` folder.

== Changelog ==

= 0.3.2 - 2009-12-31 =

* Bug fix: increased timeout

= 0.3.1 - 2009-07-22 =

* Format honors and notes blocks
* Bug fix: groups and company links broken
* Bug fix: trim block before formatting
* Thanks to [Chris Benard](http://chrisbenard.net/) for submitting all of these as a patch

= 0.3 - 2009-04-14 =

* Added proper [Wordpress shortcode](http://codex.wordpress.org/Shortcode_API) support
* Added function *lnhr&#95;get&#95;hresume* function for use in Wordpress templates
* Added some error checking

= 0.2 - 2009-04-12 =

* Use of balanceTags to ensure stripped markup does not affect theme layout
* Now full support for Markdown syntax and using a proper Markdown library
* Setting the LinkedIn profile URL can now be done in the short tag. No need to edit the plugin file anymore.
* Added caching support

= 0.1.1 - 2007-10-01 =

* Bug Fix: Now works even if the public profile does not display contact settings.<br />
* Fixed some CSS issues

= 0.1 - 2007-02-27 =

* First release.
