<?php 
/*
Plugin Name: Image Optimization by WalidDeveloper.com
Plugin URI: https://github.com/Walid-Developer/Image-Optimize-by-WalidDeveloper.com
Description: This plugin optimizes images for faster loading times by WalidDeveloper.com
Version: 1.0
Author: WalidDeveloper.com
Author URI: WalidDeveloper.com
License: GPL2
*/
function optimize_images($path) {
    if (extension_loaded('imagick')) {
        $imagick = new \Imagick();
        $imagick->readImage($path);
        $imagick->setImageCompression(\Imagick::COMPRESSION_JPEG);
        $imagick->setImageCompressionQuality(get_option('image_optimization_quality', 60));
        $imagick->stripImage();
        $imagick->writeImage($path);
        $imagick->clear();
        $imagick->destroy();
    }
}

function image_optimization_menu() {
    add_menu_page(
        'Image Optimization',
        'Image Optimization',
        'manage_options',
        'image-optimization',
        'image_optimization_settings_page',
        'dashicons-format-image',
        30
    );
}

function image_optimization_settings_page() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }
    if (isset($_POST['submit'])) {
        $quality = $_POST['quality'];
        update_option('image_optimization_quality', $quality);
        echo '<div class="updated"><p>Settings saved.</p></div>';
    }
    $quality = get_option('image_optimization_quality', 60);
    ?>
    <div class="wrap">
        <h1>Image Optimization Settings</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="quality">Image Quality</label></th>
                    <td>
                        <input type="number" id="quality" name="quality" min="1" max="100" value="<?php echo $quality; ?>" />
                        <p class="description">Enter a number between 1 and 100.</p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="submit" class="button-primary" value="Save Settings" />
            </p>
        </form>
    </div>
    <?php
}

add_action('admin_menu', 'image_optimization_menu');

add_filter('wp_handle_upload', 'image_optimization_upload', 10, 2);

function image_optimization_upload($file, $context) {
    if ($context == 'upload') {
        $path = $file['file'];
        optimize_images($path);
    }
    return $file;
}
