<?php
/**
 * Plugin Name: Book option metabox
 * Description: This plugin will show option
 * Version: 1.0.0
 * Author: Runjie Chen
 * License: GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('ABSPATH') || exit;

//When the order includes “Online Consultation”, need to input “ZOOM Meeting ID” in order edit page.
if (!function_exists('ch_bookoption_mb')) {

    function ch_bookoption_mb() {
        global $post;
        $bookoption = get_post_meta($post->ID, 'bookoption', true) ? get_post_meta($post->ID, 'bookoption', true) : '';
        ob_start();
        $arr_option = array(
            'Sports Interaction',
            'Bovada',
            'Betway',
            'Bodog',
            'Spin Sports'
        );
        ?>
        <input type="hidden" name="ch_meta_field_nonce" value="<?php echo wp_create_nonce(); ?>">
        <div>
            <select name="bookoption">
                <?php
                foreach ($arr_option as $value) {
                    $selected = '';
                    if ($bookoption == $value) {
                        $selected = 'selected ="true"';
                    }
                    ?>
                    <option <?php echo $selected; ?> value="<?php echo $value; ?>"><?php echo $value; ?></option>
                    <?php
                }
                ?>
            </select>
        </div>
        <?php
        $contents = ob_get_contents();
        ob_end_clean();
        echo $contents;
    }

}

add_action('add_meta_boxes', 'ch_bookoption_mb_mtbox');
if (!function_exists('ch_bookoption_mb_mtbox')) {

    function ch_bookoption_mb_mtbox() {
        add_meta_box('ch_bookoption_mb_mtbox', __('Book options', 'zoa'), 'ch_bookoption_mb', 'page', 'side', 'core');
    }

}
add_action('save_post', 'ch_bookoption_mb_mtbox_save', 10, 1);
if (!function_exists('ch_bookoption_mb_mtbox_save')) {

    function ch_bookoption_mb_mtbox_save($post_id) {
        if (!isset($_REQUEST['ch_meta_field_nonce'])) {
            return $post_id;
        }
        $nonce = $_REQUEST['ch_meta_field_nonce'];
        if (!wp_verify_nonce($nonce)) {
            return $post_id;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        if (isset($_REQUEST['bookoption'])) {
            update_post_meta($post_id, 'bookoption', $_REQUEST['bookoption']);
        }
    }

}

function ch_to_content($content) {
    global $post;
    $bookoption = get_post_meta($post->ID, 'bookoption', true) ? get_post_meta($post->ID, 'bookoption', true) : '';
    if (!empty($bookoption)) {
        $content = 'You choose the sportsbook: ' . $bookoption . "<br/>" . $content;
    }
    return $content;
}

add_filter('the_content', 'ch_to_content', 99);
