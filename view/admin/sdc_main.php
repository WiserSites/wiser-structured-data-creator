<?php
    echo '<link type="text/css" href="'.SDC_PLUGIN_URL.'resources/style/admin_style.css" rel="stylesheet"/>';
    echo '<script type="text/javascript" src="'.SDC_PLUGIN_URL.'resources/js/admin_js.js" > </script>';

    $sdc_uploads=get_option('sdc_uploads',true);
    $home_url = home_url(); //i_print($sdc_uploads);
?>

<h1> <?php _e( 'Mockups', 'sdc_plugin' ); ?> </h1>
<form method="post" action="">
    <div class="wrap i_sdc_wrap">
        <div class="postbox i_sdc_div">

        <?php
        if( !isset( $sdc_uploads['item_link'] ) ) {
            ?>
            <h3> <?php esc_attr_e( 'Please upload a mockup:', 'sdc_plugin' ); ?> </h3>

            <label for="mockup_image_url"> <?php _e( 'File:', 'sdc_plugin' ); ?> </label>
            <input type="text" name="sdc_uploads[image_url]" value="<?php echo ( $sdc_uploads['image_url'] ) ? $sdc_uploads['image_url'] : ''; ?>" id="mockup_image_url" class="regular-text i_mockup_url i_mouckup_uploader" />
            <input type="hidden" name="sdc_uploads[item_link]" value="<?php echo ( $sdc_uploads['item_link'] ) ? $sdc_uploads['item_link'] : ''; ?>" id="mockup_item_link" />
            <input type="hidden" name="sdc_uploads[attachment_id]" value="<?php echo ( $sdc_uploads['attachment_id'] ) ? $sdc_uploads['attachment_id'] : ''; ?>" id="mockup_attachment_id" />
            <!--<input type="button" class="button upload_image_button" value="Upload"/>-->
            <?php submit_button('Upload', 'primary', 'sdc_submit', false); ?>
            <p id="i_mockup_preview" style="display: <?php echo ( $sdc_uploads['image_url'] ) ? 'block' : 'none'; ?>;">
                <img src="<?php echo ( $sdc_uploads['image_url'] ) ? $sdc_uploads['image_url'] : ''; ?>" >
            </p>
            <input type="hidden" name="sdc_action" value="update_mockup" />
        <?php
        } else {
            $item_link = $sdc_uploads['item_link'];
            $sdc_link = $home_url.'/'.SDC_PAGE_LINK.'/'.$item_link;
            ?>

            <h3> <?php esc_attr_e('File uploaded successfully', 'sdc_plugin'); ?> </h3>
            <h3> <?php esc_attr_e('Your share URL:', 'sdc_plugin'); ?> </h3>
            <h4>
                <a href="<?php echo $sdc_link; ?>" target="_blank" > <?php echo $sdc_link; ?> </a>
            </h4>
            <br>
            <a class="i_remove" href="#">Delete Permanently</a>
            <input type="hidden" name="sdc_action" value="remove_mockup" >
            <input type="hidden" name="attachment_id" value="<?php echo $sdc_uploads['attachment_id']; ?>" >
        <?php
        }

        wp_nonce_field( SDC_PROTECTION_H, 'sdc_class_nonce' );
        ?>

        </div>
    </div>
</form>