<?php
/**
 * Plugin Name: Convert Existing Images to WebP
 * Description: A plugin to convert all existing uploaded images to WebP and keep the original images as a backup.
 * Version: 1.0
 * Author: Torsten Wenzel
 */

function register_webp_converter_page() {
    add_menu_page('Convert Images to WebP', 'Convert to WebP', 'manage_options', 'convert-to-webp', 'webp_converter_page', 'dashicons-format-image');
}
add_action('admin_menu', 'register_webp_converter_page');

function webp_converter_page() {
?>
    <div class="wrap" x-data="webpConverter()">
        <h2>Convert Existing Images to WebP</h2>
        <button @click="convertImages" :disabled="isConverting" class="button button-primary">
            Start Conversion
        </button>
        <div x-show="isConverting">
            <p>Converting: <span x-text="currentImage"></span></p>
            <p><strong>Converted: <span x-text="convertedCount"></span> images</strong></p>
        </div>
    </div>
    <?php
}


function enqueue_webp_converter_script() {
    ?>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
 
    <script type="text/javascript">
        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
        
        function webpConverter() {
            return {
                isConverting: false,
                currentImage: '',
                convertedCount: 0,
                convertImages() {
                    console.log('Button clicked');
                    this.isConverting = true;
                    this.convertBatch();
                },
                async convertBatch() {
                    console.log('Converting batch');
                    try {
                        const response = await fetch(ajaxurl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'action=convert_images_to_webp',
                        });

                        console.log('Response received', response);

                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }

                        const data = await response.json();

                        console.log('Data received', data);

                        if (data.converted.length) {
                            this.convertedCount += data.converted.length;
                            this.currentImage = data.converted[data.converted.length - 1];
                            this.convertBatch();
                        } else {
                            this.isConverting = false;
                        }
                    } catch (error) {
                        console.error('There has been a problem with your fetch operation:', error);
                        this.isConverting = false;
                    }
                },
            };
        }
    </script>
    <?php
}



add_action('admin_footer', 'enqueue_webp_converter_script');

function convert_images_to_webp() {
    $batch_size = 5;
    $converted = [];
    $upload_dir = wp_upload_dir();
    $images_dir = $upload_dir['basedir'];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($images_dir));

    foreach ($iterator as $file) {
        if (count($converted) >= $batch_size) {
            break;
        }

        if ($file->isDir()) {
            continue;
        }

        $file_path = $file->getPathname();
        $file_info = pathinfo($file_path);

        if (in_array(strtolower($file_info['extension']), ['jpg', 'jpeg', 'png'])) {
            $webp_file_path = $file_info['dirname'] . '/' . $file_info['filename'] . '.webp';

            if (file_exists($webp_file_path)) {
                continue;
            }

            $image = null;
            switch (strtolower($file_info['extension'])) {
                case 'jpg':
                case 'jpeg':
                    $image = imagecreatefromjpeg($file_path);
                    break;
                case 'png':
                    $image = imagecreatefrompng($file_path);
                    break;
            }

            if ($image) {
                imagewebp($image, $webp_file_path);
                imagedestroy($image);
                $converted[] = $file_info['basename'];
            }
        }
    }

    wp_send_json(['converted' => $converted]);
}

add_action('wp_ajax_convert_images_to_webp', 'convert_images_to_webp');
