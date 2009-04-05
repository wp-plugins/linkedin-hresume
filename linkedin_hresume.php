<?php
/*
Plugin Name: LinkedIn hResume
Plugin URI: http://brad.touesnard.com/projects/wordpress/linkedin-hresume/
Description: LinkedIn hResume grabs the Microformated hResume block from your LinkedIn public profile page allowing you to add it to any page and apply your own styles to it. It currently works with WordPress 2.0+.
Author: Brad Touesnard
Author URI: http://brad.touesnard.com/
Version: 0.2
*/

// Your public LinkedIn profile URL 
$linkedin_url = 'http://www.linkedin.com/in/bradt';
$lnhr_your_name = 'Brad Touesnard';

/* 
INSTALLATION
1. Update the URL above with your LinkedIn profile URL
2. Upload this file to your Wordpress plugins directory
3. Activate the plugin in the Wordpress Admin
4. In the Wordpress Admin, create a new page containing:
   <!--LinkedIn hResume-->
*/

/* Content filter callback function to replace the 
   page comment with the hResume */
function lnhr_callback($content)
{
	global $linkedin_url, $lnhr_your_name;
	
	if(!preg_match('@<!--LinkedIn hResume(.*)-->@', $content, $matches)) {
		return $content;
	}

	if ($matches[1]) {
		list($name, $url) = split(',', $matches[1]);
		if ($url) {
			$linkedin_url = trim($url);
		}
		if ($name) {
			$lnhr_your_name = trim($name);
		}
	}
	
	$hresume = lnhr_get_linkedin_page();
	$hresume = lnhr_stripout_hresume($hresume);
	
	return str_replace($matches[0], $hresume, $content);
}

function lnhr_get_linkedin_page() {
	global $linkedin_url;
	
	// If Wordpress caching is enabled, get content from the cache
	$data = wp_cache_get('lnhr_data');
	if ($data !== false) {
		return $data;
	}

	// Split up the URL
	$matches = array();
	preg_match('/^http:\/\/([^\/]+)(\/.*)$/', $linkedin_url, $matches);
	$server = $matches[1];
	$page = $matches[2];

	// Request the LinkedIn page
	if(function_exists('wp_remote_fopen'))
    {
        $data = wp_remote_fopen($linkedin_url);
    }
	else {
		$data = "Sorry, your version of Wordpress does not support the 'wp_remote_fopen' function. Please upgrade your version of Wordpress.";
	}
	
	// If Wordpress caching is enabled, cache the content
	wp_cache_set('lnhr_data', $data, '', 3600);
	
	return $data;
}

function lnhr_stripout_hresume($content) {
	global $lnhr_your_name;
	
	// Just grab the hResume part minus some extra LinkedIn junk
	// Kind of lazy, but maybe do some parsing in another version
	$hresume = strstr($content, '<div class="hresume">');
	$pos = strpos($hresume, '<div id="contact-settings">');
	if ($pos !== false) {
		$hresume = substr($hresume, 0, $pos);
		$hresume .= '</div>';
	}

	// Remove any Javascript
	$hresume = preg_replace('/<[ \n\r]*script[^>]*>.*<[ \n\r]*\/script[^>]*>/si', '', $hresume);
	
	// Convert wiki style formatting to XHTML
	$hresume = preg_replace("/(<br>\s*){2,}\*[ ]([^\n\r]*)(\s*<br>)/si", "<ul>\n<li>$2</li>", $hresume);
	$hresume = preg_replace("/\*[ ]([^\n\r]*)(\s*<br>)/si", "<li>$1</li>", $hresume);
	$hresume = preg_replace("/\*[ ]([^\n\r]*)(\s*(<\/p>|<\/dd>))/si", "<li>$1</li>\n</ul>\n$2", $hresume);
	$hresume = preg_replace("/(<\/li>)\s*<br>/si", "$1\n</ul>", $hresume);
	
	// Make links clickable
	$hresume = preg_replace('/([^"\'])(http:\/\/[^\s]+)([^"\'])/i', '$1<a href="$2">$2</a>$3', $hresume);
	
	// Markup abbrivations INCOMPLETE
	$hresume = preg_replace('/([^a-zA-Z0-9])(CVS)([^a-zA-Z0-9])/', '$1<abbr title="Concurrent Versioning System">$2</abbr>$3', $hresume);
	
	// Convert LinkedIn tags to XHTML
	$hresume = preg_replace('/<\s*br\s*>/si', '<br />', $hresume);
	
	// Why does LinkedIn repeat your name so much on the same page?
	$hresume = preg_replace('/'.$lnhr_your_name.'&#8217;s /si', '', $hresume);
	
	return $hresume;
}

add_filter('the_content', 'lnhr_callback', 50);
?>