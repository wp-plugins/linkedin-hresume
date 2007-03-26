=== LinkedIn hResume ===
Contributors: bradt
Tags: linkedin, resume, hresume, microformat, cv
Requires at least: 2.0.0
Tested up to: 2.1
Stable tag: trunk

Include your LinkedIn resume in any Wordpress page and style it how you like.

== Description ==

LinkedIn hResume for Wordpress grabs the [hResume microformat](http://microformats.org/wiki/hresume) 
block from your LinkedIn public profile page allowing you to add it to any Wordpress page and apply 
your own styles to it.

== Installation ==

Stii has written a [detailed installation guide with screenshots](http://stii.za.net/archives/162). 

1. Set your LinkedIn public profile to "Full View"<br />
   (default is "Basic View")
1. Download the plugin
1. Unzip the package and edit `linkedin_hresume.php`
1. In `linkedin_hresume.php`, update the URL to your LinkedIn profile URL
1. Upload `linkedin_hresume.php` to the `/wp-content/plugins/` directory
1. Activate the plugin in the Wordpress Admin
1. In the Wordpress Admin, create a new page containing:<br />
   `<!--LinkedIn hResume-->`;

== Screenshots ==

[My resume](http://brad.touesnard.com/documents/resume/) is a live demonstration of LinkedIn hResume 
for Wordpress in action. [Send me](http://brad.touesnard.com/contact/) your resume and I will post it 
here as a demo.

<h4>Live Demos</h4>

* [Stiaan Pretorius](http://stii.za.net/my-resume/)
* [Brad Touesnard](http://brad.touesnard.com/documents/resume/)

== Customization ==

In order to get your resume to look like [mine](http://brad.touesnard.com/documents/resume/), you will 
need to apply some custom CSS.  I've also used a [Wordpress page template](http://codex.wordpress.org/Pages#Page_Templates) 
to get rid of the sidebar, header, etc.  My CSS and page template are included in the plugin download 
in the `extras` folder.

== To Do ==

* Apply a Microformat Parser to the LinkedIn page and extract only the hResume data
* Ability to enable caching even when Wordpress caching is disabled

== Release Notes ==

* 0.1 - 2007-02-27<br />
  First release.
