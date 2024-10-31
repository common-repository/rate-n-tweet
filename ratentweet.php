<?php
/*
Plugin Name: RatenTweet
Plugin URI: http://ratentweet.com
Description: Add Rate 'n Tweet code for Twitter rating. Readers will click on the rating and will be forwarded to twitter.
Version: 1.1
Author: Softerize
Author URI: http://www.softerize.com
*/


/*  Copyright 2010  Rate 'n Tweet  (email : contact@ratentweet.com)

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

// Check for location modifications in wp-config
// Then define accordingly
if ( !defined('WP_CONTENT_URL') ) {
	define('RATENTWEET_PLUGPATH',get_option('siteurl').'/wp-content/plugins/'.plugin_basename(dirname(__FILE__)).'/');
	define('RATENTWEET_PLUGDIR', ABSPATH.'/wp-content/plugins/'.plugin_basename(dirname(__FILE__)).'/');
} else {
	define('RATENTWEET_PLUGPATH',WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)).'/');
	define('RATENTWEET_PLUGDIR',WP_CONTENT_DIR.'/plugins/'.plugin_basename(dirname(__FILE__)).'/');
}

/*
 * Administration menu
 */
function ratentweet_admin() {

    $pluginname = "Rate 'n Tweet";

    $i=0;

    if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$pluginname.' settings saved.</strong></p></div>';
    if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$pluginname.' settings reset.</strong></p></div>';

?>
<div class="wrap">
<h2><?php echo $pluginname; ?> Settings</h2>

<form method="post">

<p>
    <label for="ratentweet_icon">Icon Type</label>:
    <select name="ratentweet_icon" id="ratentweet_icon">
        <option value="star" <?php if (get_option( 'ratentweet_icon' ) == 'star') { echo 'selected="selected"'; } ?>>Star</option>
        <option value="smiley" <?php if (get_option( 'ratentweet_icon' ) == 'smiley') { echo 'selected="selected"'; } ?>>Smiley</option>
        <option value="bird" <?php if (get_option( 'ratentweet_icon' ) == 'bird') { echo 'selected="selected"'; } ?>>Bird</option>
    </select>
    <small>Select the icon appearence</small>
</p>

<p>
    <label for="ratentweet_size">Icon Type</label>:
    <select name="ratentweet_size" id="ratentweet_icon">
        <option value="small" <?php if (get_option( 'ratentweet_size' ) == 'small') { echo 'selected="selected"'; } ?>>Small - 16px</option>
        <option value="medium" <?php if (get_option( 'ratentweet_size' ) == 'medium') { echo 'selected="selected"'; } ?>>Medium - 24px</option>
        <option value="big" <?php if (get_option( 'ratentweet_size' ) == 'big') { echo 'selected="selected"'; } ?>>Big - 32px</option>
    </select>
    <small>Select the icon size</small>
</p>

<p>
    <label for="ratentweet_size">URL Shortener</label>:
    <select name="ratentweet_short" id="ratentweet_short">
        <option value="b2l" <?php if (get_option( 'ratentweet_short' ) == 'b2l') { echo 'selected="selected"'; } ?>>b2l.me</option>
        <option value="tinyurl" <?php if (get_option( 'ratentweet_short' ) == 'tinyurl') { echo 'selected="selected"'; } ?>>tinyurl.com</option>
        <option value="bitly" <?php if (get_option( 'ratentweet_short' ) == 'bitly') { echo 'selected="selected"'; } ?>>bit.ly</option>
        <option value="migreme" <?php if (get_option( 'ratentweet_short' ) == 'migreme') { echo 'selected="selected"'; } ?>>migre.me</option>
    </select>
    <small>Select your preferred URL Shortener</small>
</p>
<div id="ratentweet_bitly"<?php if(get_option( 'ratentweet_short' ) != "bitly") { ?> class="hidden"<?php } ?>>
    <p>
        <label for="ratentweet-bitly-user">User ID:</label>
        <input type="text" id="ratentweet_bitly_user" name="ratentweet_bitly_user" value="<?php echo get_option( 'ratentweet_bitly_user' ); ?>" />
        <label for="ratentweet-bitly-key">API Key:</label>
        <input type="text" id="ratentweet_bitly_key" name="ratentweet_bitly_key" value="<?php echo get_option( 'ratentweet_bitly_key' ); ?>" />
    </p>
</div>

</div>

<p class="submit">
<input type="submit" name="save" value="Update Options" />
<input type="hidden" name="action" value="save" />
</p>
</form>

<?php
}

function ratentweet_add_admin() {

    if ( $_GET['page'] == basename(__FILE__) ) {
        if ( 'save' == $_REQUEST['action'] ) {

            $icon = $_REQUEST['ratentweet_icon'];
            update_option('ratentweet_icon', $icon);

            $size = $_REQUEST['ratentweet_size'];
            update_option('ratentweet_size', $size);

            $short = $_REQUEST['ratentweet_short'];
            update_option('ratentweet_short', $short);

            if ($short == 'bitly') {
                $bitly_user = $_REQUEST['ratentweet_bitly_user'];
                update_option('ratentweet_bitly_user', $bitly_user);

                $bitly_key = $_REQUEST['ratentweet_bitly_key'];
                update_option('ratentweet_bitly_key', $bitly_key);
            } else {
                delete_option('ratentweet_bitly_user');
                delete_option('ratentweet_bitly_key');
            }

            header("Location: options-general.php?page=ratentweet.php&saved=true");

            die;
        }
    }

    $ratentweet_admin_page = add_options_page("Rate 'n Tweet", "Rate 'n Tweet", 1, "ratentweet.php", "ratentweet_admin");
    add_action( "admin_print_scripts-$ratentweet_admin_page", 'ratentweet_admin_scripts' );
    add_action( "admin_print_styles-$ratentweet_admin_page", 'ratentweet_admin_styles' );
}
function ratentweet_admin_scripts() {
    wp_enqueue_script('ratentweet-js', RATENTWEET_PLUGPATH.'js/ratentweet-admin.js', 'jquery');
}
function ratentweet_admin_styles() {
    wp_enqueue_style('ratentweet-css', RATENTWEET_PLUGPATH.'css/admin-style.css', false);
}

add_action('admin_menu', 'ratentweet_add_admin');



/*
 * Plugin code
 */
function ratentweet_getB2lMe($url) {
    $response = wp_remote_retrieve_body(wp_remote_get('http://b2l.me/api.php?alias=&url='.$url));
    return $response;
}
function ratentweet_getTinyUrl($url) {
    $response = wp_remote_retrieve_body(wp_remote_get('http://tinyurl.com/api-create.php?url='.$url));
    return $response;
}
function ratentweet_getBitLy($url, $user, $key) {
    $response = wp_remote_retrieve_body(wp_remote_get('http://api.bit.ly/shorten?version=2.0.1&longUrl='.$url.'&history=1&login='.$user.'&apiKey='.$key.'&format=json'));
    return $response;
}
function ratentweet_getMigreMe($url) {
    $response = wp_remote_retrieve_body(wp_remote_get('http://migre.me/api.xml?url='.$url));
    return $response;
}
function add_ratentweetjs()
{
    if (is_single()) {
        $short = get_option( 'ratentweet_short' );
        $original_link = get_permalink();
        if ($short == 'b2l') {
            $link = ratentweet_getB2lMe($original_link);
        } else if ($short == 'tinyurl') {
            $link = ratentweet_getTinyUrl($original_link);
        } else if ($short == 'bitly') {
            $link = ratentweet_getBitLy($original_link, get_option( 'ratentweet_bitly_user' ), get_option( 'ratentweet_bitly_key' ));

            $fetch_array = json_decode($link, true);
            $link = $fetch_array['results'][$original_link]['shortUrl'];
        } else if ($short == 'migreme') {
            $link = ratentweet_getMigreMe($original_link);
        } else {
            // Default
            $link = ratentweet_getB2lMe($original_link);
        }

        if (empty ($link)) $link = ratentweet_getB2lMe($original_link); // Check if link is not empty
        if (empty ($link)) $link = get_permalink(); // If still empty, something went wrong

        $post_title = the_title('', '', false);
        $message = 'x+stars+to+'.$post_title.'+-+'.$link.'+#ratentweet';
        if (strlen($message) > 140) {
            $cut_length = strlen($post_title) - ((strlen($message) - 140) + 3);
            $post_title = substr($post_title, 0, $cut_length).'...';
        }

        echo '<script type="text/javascript">
            window.onload = function() {
                new Ratentweet( {
                        title: "'.urlencode($post_title).'",
                        link: "'.$link.'",
                        starStyle: "'.get_option( 'ratentweet_icon' ).'",
                        size: "'.get_option( 'ratentweet_size' ).'"
                } );
            }
            </script>';
    }
}

function add_ratentweet($content)
{
    if(is_single()) {
        $ratentweet= '<div id="ratentweet"></div>';
        return $content.$ratentweet;
    } else {
        return $content;
    }
}

wp_enqueue_script('ratentweet_min',RATENTWEET_PLUGPATH.'js/ratentweet_min.js');
add_action('wp_head', 'add_ratentweetjs');
add_filter('the_content', 'add_ratentweet');

?>