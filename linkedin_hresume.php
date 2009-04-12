<?php
/*
Plugin Name: LinkedIn hResume
Plugin URI: http://brad.touesnard.com/projects/wordpress/linkedin-hresume/
Description: LinkedIn hResume grabs the Microformated hResume block from your LinkedIn public profile page allowing you to add it to any page and apply your own styles to it.
Author: Brad Touesnard
Author URI: http://brad.touesnard.com/
Version: 0.2
*/

// Your public LinkedIn profile URL 
$linkedin_url = 'http://www.linkedin.com/in/bradt';
$lnhr_enable_cache = false;

/* INSTALLATION
 * Please see the readme.txt file for installation details.
 */

/* Content filter callback function to replace the 
   page comment with the hResume */
function lnhr_callback($content)
{
	global $linkedin_url, $lnhr_enable_cache, $wp_filter;
	
	if(!preg_match('@(?:<p>)?<!--LinkedIn hResume(.*)-->(?:</p>)?@', $content, $matches)) {
		return $content;
	}

	if ($matches[1]) {
		list($url, $cache) = split(',', $matches[1]);
		if ($url) {
			$linkedin_url = trim($url);
		}
		if ($cache == 'true' || $cache == '1') {
			$lnhr_enable_cache = true;
		}
	}
	
	$hresume = '';
	if ($lnhr_enable_cache) {
		$cache = get_option('lnhr_cache');
		if ($cache !== false) {
			list($expiry, $data) = $cache;
			if ($expiry > time()) {
				$hresume = $data;
			}
		}
	}

	if (!$hresume) {
		$hresume = lnhr_get_linkedin_page();
		$hresume = lnhr_stripout_hresume($hresume);

		$hresume = balanceTags($hresume, true);
	
		if ($lnhr_enable_cache) {
			update_option('lnhr_cache', array(time()+21600, $hresume));
		}
	}
	
	return str_replace($matches[0], $hresume, $content);
}

function lnhr_get_linkedin_page() {
	global $linkedin_url;

	// Request the LinkedIn page
	if(function_exists('wp_remote_fopen'))
    {
        $data = wp_remote_fopen($linkedin_url);
    }
	else {
		$data = "Sorry, your version of Wordpress does not support the 'wp_remote_fopen' function. Please upgrade your version of Wordpress.";
	}
	
	return $data;
}

function lnhr_format_block($matches) {
	$desc = $matches[2];
	
	$desc = strip_tags($desc);
	$desc = Markdown($desc);
		
	// Make links clickable
	$desc = preg_replace('@(http:\/\/[^\s<>]+)@i', '<a href="$1">$1</a>', $desc);
	
	$desc = wpautop($desc);
	
	return '<div class="' . $matches[1] . '">' . $desc . '</div>';
}

function lnhr_stripout_hresume($content) {
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
	
	// This is the path to markdown.php
	if ( !defined('AUTOMATTIC_README_MARKDOWN') )
		define('AUTOMATTIC_README_MARKDOWN', dirname(__FILE__) . '/markdown.php');

	if ( !function_exists('Markdown') )
		require( AUTOMATTIC_README_MARKDOWN );

	$hresume = preg_replace_callback('@<p class="(description)">(.*?)</p>@s', 'lnhr_format_block', $hresume);
	$hresume = preg_replace_callback('@<p class="(skills)">(.*?)</p>@s', 'lnhr_format_block', $hresume);
	
	// Markup abbrivations INCOMPLETE
	$hresume = preg_replace('/([^a-zA-Z0-9])(CVS)([^a-zA-Z0-9])/', '$1<abbr title="Concurrent Versioning System">$2</abbr>$3', $hresume);
	
	// Convert LinkedIn tags to XHTML
	$hresume = preg_replace('/<\s*br\s*>/si', '<br />', $hresume);

	// Why does LinkedIn repeat your name so much on the same page?
	if (preg_match('@<span class="given-name">([^<]+)</span>@', $hresume, $matches)) {
		$name = $matches[1];
		$matches = array();
		if (preg_match('@<span class="family-name">([^<]+)</span>@', $hresume, $matches)) {
			$name = $name . ' ' . $matches[1];
			$hresume = str_ireplace($name . '&#8217;s ', '', $hresume);
		}
	}
	
	return $hresume;
}

add_filter('the_content', 'lnhr_callback', 50);
?>