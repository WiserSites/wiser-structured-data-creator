<?php

class SDC {

    private static $initiated = false;
	public static $schema_types;
	public static $schema_types_add;
	public static $schema_options;
	public static $exclude_options;

	public static function init( ) {

		require_once( SDC_PLUGIN_DIR . 'lib/schema-types.php' );
		self::$schema_types = $schema_types;
		self::$schema_types_add = $schema_types_add;
		self::$schema_options = $schema_options;
		self::$exclude_options = array('header', 'end_section');

		if ( ! self::$initiated ) {
			self::init_hooks();
		}

	}

	/**
	 * Initializes WordPress hooks
	 */
	private static function init_hooks( ) {
		self::$initiated = true;

        self::register_cpt_designfeedback();

		add_filter( 'the_content', array( 'SDC', 'sdc_content_append') );

        add_action( 'wp_enqueue_scripts', array( 'SDC', 'load_resources' ) );
	}

    public static function plugin_activation( ) {

	}

    public static function register_cpt_designfeedback() {


    }

    public static function load_resources(){
		//SDC_PLUGIN_URL.'resources/style/admin_style.css
		wp_enqueue_style( 'sdc_style', SDC_PLUGIN_URL.'resources/style/front_style.css', array(), SDCVersion, 'all' );
    }

	public function sdc_content_append( $content ){
		global $post;
		$post_id = $post->ID;
		$sdc_data_content = SDC_DATA_CONTENT;

		$sdc_data = get_post_meta($post_id, SDC_DATA_NAME,true);
		$sdc_data_content = get_post_meta($post_id, $sdc_data_content, true); //i_print($sdc_data_content);
		$sdc_position = ( isset($sdc_data_content['style']) && $sdc_data_content['style']['position'] )?$sdc_data_content['style']['position']:'after';

		//$sdc_content = self::sdc_generator(  );

		if( is_array($sdc_data_content) && count($sdc_data_content) && $sdc_content = $sdc_data_content['content']){
			if( $sdc_position == 'before' ){
				$sdc_content.= $content;
			} elseif( $sdc_position == 'after' ) {
				$sdc_content = $content . $sdc_content;
			} else {
				$sdc_content = $content . '<div style="display: none;" >' .$sdc_content . '</div>';
			}

			return $sdc_content;
		}
		return $content;
	}

	public function sdc_generator( $post_id = 0 ){
		$schema_types = self::$schema_types;
		$schema_types_add = self::$schema_types_add;
		$schema_options = self::$schema_options;
		$exclude_options = self::$exclude_options;

		$i_post_link = get_permalink($post_id);
		$sdc_data = get_post_meta($post_id, SDC_DATA_NAME, true);

		$sdc_html = ''; $sdc_html_div = '';
		if( is_array($sdc_data) && count($sdc_data) && $type = $sdc_data['type'] ){
			$schema = $schema_types[ $type ]; //i_print( $schema ); exit;
			//i_print( $schema );
			$sdc_html_div = '<div itemscope itemtype="'.$schema['itemtype'].'" class="i_sdc_div"> ';
			foreach( $schema['fields'] as $schema_key => $schema_field ){

				$i_itemprop = $schema_key;

				if( is_array( $schema_field ) ){
					$sdc_html.= '<div itemprop="'.$i_itemprop.'" itemscope itemtype="'.$schema_types_add[$i_itemprop]['itemtype'].'"> ';
					foreach($schema_field as $f => $sdc_field) {
						if( isset( $exclude_options[ $schema_options[ $schema_field[$f] ]['type'] ] ) ) continue;
						if( ($schema_val = $sdc_data[ $sdc_field ]) != '' ){
							$schema_option = $schema_options[ $sdc_field ];
							$i_itemprop = $schema_option['itemprop'];
							$i_label = ($schema_option['label'])?$schema_option['label'].': ':'';

							$sdc_html.= '<div class="schema_'.$i_itemprop.'"> '.$i_label.' ';
							$sdc_html.= '<span class="schema_'.$i_itemprop.'_span" itemprop="'.$i_itemprop.'">'.$schema_val.'</span>';
							$sdc_html.= ' </div>';
						}
					}
					$sdc_html.= '</div> ';
				} else {
					$sdc_field = $schema_field;
					//i_print( $sdc_field );
					if( isset( $exclude_options[ $schema_options[ $schema_field ]['type'] ] ) ) continue;
					if( ($schema_val = $sdc_data[ $sdc_field ]) != '' ){
						$schema_option = $schema_options[ $sdc_field ];
						$i_itemprop = $schema_option['itemprop'];
						$i_label = ($schema_option['label'])?$schema_option['label'].': ':'';

						if( $i_itemprop == 'sdc_type_changer' ){
							$sdc_html_div = '<div itemscope itemtype="http://schema.org/'.$schema_val.'" class="i_sdc_div"> ';
							continue;
						}

						if( $schema_field == 'name' && $schema_option['id'] == 'name' ){
							$sdc_html.= '<a href="'.$i_post_link.'" itemprop="url" class="schema_url">';
							$sdc_html.= '<div class="schema_'.$i_itemprop.'" itemprop="'.$i_itemprop.'"> '.$i_label.' '.$schema_val.'</div></a>';
						} else {
							$sdc_html.= '<div class="schema_'.$i_itemprop.'" itemprop="'.$i_itemprop.'">'.$i_label.' '.$schema_val.'</div>';
						}

					}
				}

			}

			$sdc_html = $sdc_html_div . $sdc_html . '</div>';
		}
		return $sdc_html;
		//exit;
	}

	/**
	 * Removes all connection options
	 * @static
	 */
	public static function plugin_deactivation( ) {
        flush_rewrite_rules();
	}

}