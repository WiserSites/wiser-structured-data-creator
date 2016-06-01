<?php
    global $post;

    $post_id = $post->ID;
    $sdc_data = get_post_meta($post_id, SDC_DATA_NAME,true);
    //echo SDC::sdc_generator( $post_id );

    $sdc_data_content = get_post_meta($post_id, SDC_DATA_CONTENT, true);

    $schema_type_structure = array();
    foreach( $schema_types as $schema_type){
        $schema_type_structure[ $schema_type['id'] ] = $schema_type['fields'];
    }

    ?>
    <script type="text/javascript">
        var schema_types = jQuery.parseJSON('<?php echo json_encode( $schema_type_structure ); ?>');
        var schema_types_add = jQuery.parseJSON('<?php echo json_encode( $schema_types_add ); ?>');
        var schema_options = jQuery.parseJSON('<?php echo json_encode( $schema_options ); ?>');

        //console.log( schema_types_add );
         /*
        for(var index in schema_types) {
            var schema_type = schema_types[ index ];
            console.log( schema_type['fields'] );
        }*/
    </script>
    <?php

    echo '<link type="text/css" href="'.SDC_PLUGIN_URL.'resources/style/admin_style.css" rel="stylesheet"/>';
?>
    <div class="wp-list-table i_list_types i_metabox_sdc_list">
        <input type="hidden" id="i_post_link" value="<?php echo get_permalink(); ?>" >

        <div class="i_row clearfix">
            <label for="sdc_position"><?php _e('Scheme position', SDC_NAME); ?>:</label>
            <?php $sdc_position = ( isset($sdc_data_content['style']) && $sdc_data_content['style']['position'] )?$sdc_data_content['style']['position']:'after'; ?>
            <select name="<?php echo SDC_DATA_CONTENT; ?>[style][position]" id="sdc_position" class="sdc_input">
                <option value="before" id="sdc_position_before" <?php echo ($sdc_position=='before')?'selected':''; ?> > <?php _e('Before Content', SDC_NAME); ?> </option>
                <option value="after" id="sdc_position_after" <?php echo ($sdc_position=='after')?'selected':''; ?> > <?php _e('After Content', SDC_NAME); ?> </option>
                <option value="hidden" id="sdc_position_hidden" <?php echo ($sdc_position=='hidden')?'selected':''; ?> > <?php _e('Hidden', SDC_NAME); ?> </option>
            </select>
        </div> <hr>
        <div class="i_row clearfix">
            <label for="sdc_type"><b><?php _e('Type', SDC_NAME); ?>:</b></label>
            <select name="<?php echo SDC_DATA_NAME; ?>[type]" id="sdc_type_changer" class="sdc_input sdc_type_changer i_sdc_meta_field">
                <option value="null" id="type_null"> (<?php _e('Select A Type', SDC_NAME); ?>) </option>
                <?php foreach( $schema_types as $schema_type){
                    $attrs = '';
                    if( $itemtype = $schema_type['itemtype'] )$attrs.= ' data-itemtype="'.$itemtype.'"';
                    $i_selected =''; if( isset( $sdc_data['type'] ) && $schema_type['id'] == $sdc_data['type'] ) $i_selected='selected';
                    echo '<option value="'.$schema_type['id'].'" id="type_'.$schema_type['id'].'" '.$attrs.' '.$i_selected.' > '. $schema_type['title'] .' </option>';
                } ?>
            </select>
        </div> <hr>

        <?php
        foreach( $schema_options as $schema_option ){
            $schema_id = $schema_option['id']; $class_types = '';
            foreach( $schema_types as $schema_type ){
                if( ( $t_key = array_searchRecursive( $schema_id, $schema_type['fields'] ) ) !== FALSE ){
                    $class_types.= ' for_type_'.$schema_type['id'];
                }
            }
            $field = $schema_option;
            ?>
            <div class="i_row i_field_wrapper i_div_<?php echo $field['id']; ?> <?php echo $class_types; ?>">
                <?php

                $f_value = ( isset( $sdc_data[ $field['id'] ] ) ) ? $sdc_data[ $field['id'] ] : '';

                $attrs = '';
                if( $itemprop = $field['itemprop'] )$attrs.= ' data-itemprop="'.$itemprop.'"';
                //if( $itemtype_url = $field['itemtype-url'] )$attrs.= ' data-itemtype-url="'.$itemtype_url.'"';
                $item_label = ( $field['label'] ) ? $field['label'] . ':' : '';
                $item_wrap_tag = ( $field['wrap_tag'] ) ? $field['wrap_tag'] : '';
                $attrs.= ' data-label="'.$item_label.'" data-wrap_tag="'.$item_wrap_tag.'"';
                if( $field['data-attrs'] && count( $field['data-attrs'] ))
                    foreach( $field['data-attrs'] as $data_attr => $data_attr_val ){
                        $attrs.= ' data-'.$data_attr.'="'.$data_attr_val.'" ';
                    }

                echo self::sdc_field_generator( $field, $f_value, $attrs, $sdc_data );
                ?>
            </div>
            <?php
        }
        ?>
        <div>
            <h2> <?php _e('Scheme View Option'); ?></h2>

        </div>
        <div class="i_sdc_meta_preview_div">
            <h2> <?php _e('Scheme Preview'); ?></h2>
            <div class="i_sdc_meta_preview">

            </div>
            <h2> <?php _e('Scheme HTML Preview'); ?></h2>
            <textarea class="i_sdc_html_preview" name="<?php echo SDC_DATA_CONTENT; ?>[content]" readonly></textarea>
        </div>
    </div>

<?php


?>
