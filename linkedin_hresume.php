<?php
/*
Plugin Name: LinkedIn hResume
Plugin URI: http://wordpress.org/extend/plugins/linkedin-hresume/
Description: LinkedIn hResume grabs the Microformated hResume block from your LinkedIn public profile page allowing you to add it to any page and apply your own styles to it.
Author: Brad Touesnard
Author URI: http://bradt.ca/
Version: 0.3.2
*/

// Your public LinkedIn profile URL 
$linkedin_url = 'http://www.linkedin.com/in/bradt';
$lnhr_enable_cache = false;

/* INSTALLATION
 * Please see the readme.txt file for installation details.
 */

function lnhr_shortcode($atts) {
	global $linkedin_url, $lnhr_enable_cache;

	extract(shortcode_atts(array(
		'url' => $linkedin_url,
		'caching' => $lnhr_enable_cache,
	), $atts));
	
	$caching = lnhr_is_caching($caching);
	
	$lnhr_enable_cache = $caching;
	$linkedin_url = $url;

	return lnhr_get_hresume($url, $caching);
}

/* Backward compatibility: Support for comment code */
function lnhr_callback($content)
{
	global $linkedin_url, $lnhr_enable_cache;
	
	if(!preg_match('@(?:<p>)?(?:<|&lt;)!(?:--|&#8211;)LinkedIn hResume(.*)(?:--|&#8211;)(?:>|&gt;)(?:</p>)?@', $content, $matches)) {
		return $content;
	}

	if ($matches[1]) {
		list($url, $cache) = split(',', $matches[1]);
		if ($url) {
			$linkedin_url = trim($url);
		}
		$lnhr_enable_cache = lnhr_is_caching($cache);
	}
	
	$hresume = lnhr_get_hresume($linkedin_url, $lnhr_enable_cache);

	return str_replace($matches[0], $hresume, $content);
}

// Developers: This function can be used in your Wordpress templates
function lnhr_get_hresume($url, $caching = false) {
	$hresume = '';
	if ($caching) {
		$cache = get_option('lnhr_cache');
		if ($cache !== false) {
			list($cache_url, $expiry, $data) = $cache;
			if ($url == $cache_url && $expiry > time()) {
				$hresume = $data;
			}
		}
	}

	if (!$hresume) {
		$hresume = lnhr_get_linkedin_page($url);
		lnhr_error_check($hresume, $url);
		$hresume = lnhr_stripout_hresume($hresume);

		$hresume = balanceTags($hresume, true);
	
		if ($caching) {
			update_option('lnhr_cache', array($url, time()+21600, $hresume));
		}
	}
	
	return $hresume;
}

function lnhr_is_caching($value) {
	return (in_array($value, explode(',', 'on,true,1')));
}

function lnhr_get_linkedin_page($url) {
	// Request the LinkedIn page
	if(function_exists('wp_remote_get'))
    {
		$parsed_url = @parse_url( $url );
	
		if ( !$parsed_url || !is_array( $parsed_url ) )
			$data = "Invalid LinkedIn URL.";

		$options = array();
		$options['timeout'] = 100;
	
		$response = wp_remote_get( $url, $options );
	
		if ( is_wp_error( $response ) )
			return false;
	
		$data = $response['body'];
    }
	else {
		$data = "Sorry, your version of Wordpress does not support the 'wp_remote_fopen' function. Please upgrade your version of Wordpress.";
	}
	
	return $data;
}

function lnhr_format_block($matches) {
	$desc = $matches[2];
	
	$desc = strip_tags($desc);
	$desc = trim($desc);
	$desc = Markdown($desc);
		
	// Make links clickable
	$desc = preg_replace('@(http:\/\/[^\s<>]+)@i', '<a href="$1">$1</a>', $desc);
	
	$desc = wpautop($desc);
	
	return '<div class="' . $matches[1] . '">' . $desc . '</div>';
}

function lnhr_error_check($content, $url) {
	$pos = strpos($content, '<div class="hresume">');
	if ($pos === false) {
		$pos = strpos($content, 'Profile Not Found');
		if ($pos !== false) {
			wp_die('<h1>Profile Not Found</h1><p>The profile <a href="' . $url . '">' . $url . '</a> could not be found.</p>');
		}
		elseif (preg_match('@<body class="public-profile">(.*?)</body>@s', $content, $matches)) {
			wp_die($matches[1]);
		}
		else {
			wp_die('<h1>Communication Error</h1><p>There was an error retrieving your LinkedIn public profile.</p>');
		}
	}
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
	$hresume = preg_replace_callback('@<p class="(honors)">(.*?)</p>@s', 'lnhr_format_block', $hresume);
	$hresume = preg_replace_callback('@<p class="(notes)">(.*?)</p>@s', 'lnhr_format_block', $hresume);

	// Make the links clickable for groups and companies
	$hresume = preg_replace_callback('@<a href="(/.*?)"@s', 'lnhr_format_link', $hresume, -1, $count);
	
	// Markup abbrevations INCOMPLETE
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

function lnhr_format_link($matches) {
	$linktext = $matches[1];

	if (substr($linktext, 0, 1) == '/')
	{
		$linktext = 'http://www.linkedin.com' . $linktext;
	}

	return '<a href="' . $linktext . '"';
}

add_filter('the_content', 'lnhr_callback', 50);
add_shortcode('lnhr', 'lnhr_shortcode');
?>
