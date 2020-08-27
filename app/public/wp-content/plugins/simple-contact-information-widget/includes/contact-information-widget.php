<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 *
 * @package    contact-information
 * @subpackage contact-information/includes
 */

/**
 * The main widget class.
 *
 *
 * @since      1.0.0
 * @package    contact-information
 * @subpackage contact-information/includes
 * @author     Jaydeep Chauhan <jd.dev777@gmail.com>
 */
class Contact_Information_Widget extends WP_Widget {

	/**
	 * Define the core functionality of the widget.
	 *
	 * Register widget name and description.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
	  	$widget_options = array( 
			'classname' 	=> 'Contact_Information_Widget',
			'description' 	=> 'Contact Information' 
		);
	  	parent::__construct( 'Contact_Information_Widget', 'Contact Information', $widget_options );
	}
  
  
	/**
	 * Method to define the widget output that will be displayed on the site front end.
	 * 
	 * @since     1.0.0
	 */
	public function widget( $args, $instance ) {
		$data = array();
		$param = array(
			'title',
			'company',
			'aboutus',
			'address',
			'phone',
			'mobile',
			'email',
			'fax',
			'website',
			'map_url',
			'map_width',
			'map_height',
			'is_ci_label',
			'is_ci_icon'
		);
	
		foreach( $param as $key ) {
			$data[$key] = '';
			if( isset($instance[$key]) ) {
				$data[$key] = trim($instance[$key]);
			}
		}
	
		$map_url= $data['map_url'];;
		$map_width= $data['map_width'] ? $data['map_width'] : '100%';unset($data['map_width']);
		$map_height= $data['map_height'] ? $data['map_height'] : 300;unset($data['map_height']);
		$is_ci_label = $data['is_ci_label'] ? 'ci_label' : 'ci_label_not'; unset($data['is_ci_label']);
		$is_ci_icon = $data['is_ci_icon'] ? 'ci_icon' : 'ci_icon_not'; unset($data['is_ci_icon']);
	
	  	echo $args['before_widget'];
			echo $args['before_title'];
				echo $data['title'];  unset($data['title']);
			echo $args['after_title'];
			echo '<ul class="contact_information '.$is_ci_label.' '.$is_ci_icon.'">';
				foreach($data as $k=>$val) {
					if( $val ) {
						//mailto
						if( $k == 'email' ) {
							$val = '<a href="mailto:'.$val.'">'.$val.'</a>';
						}
						//tel
						if( $k == 'mobile' || $k == 'phone' ) {
							$val = '<a href="tel:'.$val.'">'.$val.'</a>';
						}
						//anchor
						if( $k == 'website' ) {
							$val = '<a href="'.$val.'" target="_blank">'.$val.'</a>';
						}
						//map
						if( $k == 'map_url' ) {
							$val = '<iframe src="'.$val.'&output=embed" width="'.$map_width.'" height="'.$map_height.'" frameborder="0" style="border:0" allowfullscreen></iframe>';
						}
							
						echo '<li class="item-ci item-ci-'.$k.'"><p><span class="label-ci label-ci-'.$k.'" data-label="'.ucfirst($k).'"></span> '.$val.'</p></li>';
					}
				}
			echo '</ul>';
	  	echo $args['after_widget'];
	}
  
	
	/**
	 * Method is used to add setting fields to the widget which will be 
	 * displayed in the WordPress admin area
	 * 
	 * @since     1.0.0
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : ''; 
		$company = ! empty( $instance['company'] ) ? $instance['company'] : ''; 
		$aboutus = ! empty( $instance['aboutus'] ) ? $instance['aboutus'] : ''; 
		$address = ! empty( $instance['address'] ) ? $instance['address'] : ''; 
		$email = ! empty( $instance['email'] ) ? $instance['email'] : ''; 
		$fax = ! empty( $instance['fax'] ) ? $instance['fax'] : ''; 
		$phone = ! empty( $instance['phone'] ) ? $instance['phone'] : ''; 
		$mobile = ! empty( $instance['mobile'] ) ? $instance['mobile'] : ''; 
		$website = ! empty( $instance['website'] ) ? $instance['website'] : ''; 
		$map_url = ! empty( $instance['map_url'] ) ? $instance['map_url'] : ''; 
		$map_width = ! empty( $instance['map_width'] ) ? $instance['map_width'] : ''; 
		$map_height = ! empty( $instance['map_height'] ) ? $instance['map_height'] : ''; 
		$is_ci_label = ! empty( $instance['is_ci_label'] ) ? $instance['is_ci_label'] : ''; 
		$is_ci_icon = ! empty( $instance['is_ci_icon'] ) ? $instance['is_ci_icon'] : ''; 
		
		?>
	  	<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
	  	</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'company' ); ?>">Compnay Name:</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'company' ); ?>" name="<?php echo $this->get_field_name( 'company' ); ?>" value="<?php echo esc_attr( $company ); ?>" />
	  	</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'aboutus' ); ?>">About:</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'aboutus' ); ?>" name="<?php echo $this->get_field_name( 'aboutus' ); ?>" value="<?php echo esc_attr( $aboutus ); ?>" />
	  	</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'address' ); ?>">Address:</label>
			<textarea class="widefat code content" id="<?php echo $this->get_field_id( 'address' ); ?>" name="<?php echo $this->get_field_name( 'address' ); ?>"><?php echo esc_attr( $address ); ?></textarea>
	  	</p>  

		<p>
			<label for="<?php echo $this->get_field_id( 'email' ); ?>">Email:</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'email' ); ?>" value="<?php echo esc_attr( $email ); ?>" />
	  	</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'fax' ); ?>">Fax:</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'fax' ); ?>" name="<?php echo $this->get_field_name( 'fax' ); ?>" value="<?php echo esc_attr( $fax ); ?>" />
	  	</p>	  	

		<p>
			<label for="<?php echo $this->get_field_id( 'phone' ); ?>">Phone:</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'phone' ); ?>" name="<?php echo $this->get_field_name( 'phone' ); ?>" value="<?php echo esc_attr( $phone ); ?>" />
	  	</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'mobile' ); ?>">Mobile:</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'mobile' ); ?>" name="<?php echo $this->get_field_name( 'mobile' ); ?>" value="<?php echo esc_attr( $mobile ); ?>" />
	  	</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'website' ); ?>">Website:</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'website' ); ?>" name="<?php echo $this->get_field_name( 'website' ); ?>" value="<?php echo esc_attr( $website ); ?>" />
	  	</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'map_url' ); ?>">Map URL:</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'map_url' ); ?>" name="<?php echo $this->get_field_name( 'map_url' ); ?>" value="<?php echo esc_attr( $map_url ); ?>" />
	  	</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'map_widget' ); ?>">Map Width:</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'map_width' ); ?>" name="<?php echo $this->get_field_name( 'map_width' ); ?>" value="<?php echo esc_attr( $map_width ); ?>" />
	  	</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'map_height' ); ?>">Map Height:</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'map_height' ); ?>" name="<?php echo $this->get_field_name( 'map_height' ); ?>" value="<?php echo esc_attr( $map_height ); ?>" />
	  	</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'is_ci_icon' ); ?>"><input <?php echo ($is_ci_icon == 1 ) ? "checked": ""; ?> type="checkbox" id="<?php echo $this->get_field_id( 'is_ci_icon' ); ?>" name="<?php echo $this->get_field_name( 'is_ci_icon' ); ?>" value="1" /> Show Icon</label>
	  	</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'is_ci_label' ); ?>"><input <?php echo ($is_ci_label == 1 ) ? "checked": ""; ?> type="checkbox" id="<?php echo $this->get_field_id( 'is_ci_label' ); ?>" name="<?php echo $this->get_field_name( 'is_ci_label' ); ?>" value="1" /> Show Label</label>
	  	</p>
		
		<?php
	}
  
  
	/** 
	 * Method is used to update the widget information in the WordPress database 
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
		
		$instance[ 'company' ] = strip_tags( $new_instance[ 'company' ] );

		$instance[ 'aboutus' ] = strip_tags( $new_instance[ 'aboutus' ] );

		$instance[ 'address' ] = $new_instance[ 'address' ];

		$instance[ 'email' ] = strip_tags( $new_instance[ 'email' ] );

		$instance[ 'fax' ] = strip_tags( $new_instance[ 'fax' ] );

		$instance[ 'phone' ] = strip_tags( $new_instance[ 'phone' ] );

		$instance[ 'mobile' ] = strip_tags( $new_instance[ 'mobile' ] );
		
		$instance[ 'website' ] = strip_tags( $new_instance[ 'website' ] );

		$instance[ 'map_url' ] = strip_tags( $new_instance[ 'map_url' ] );
		
		$instance[ 'map_width' ] = strip_tags( $new_instance[ 'map_width' ] );
		
		$instance[ 'map_height' ] = strip_tags( $new_instance[ 'map_height' ] );
		
		$instance[ 'is_ci_icon' ] = strip_tags( $new_instance[ 'is_ci_icon' ] );
		
		$instance[ 'is_ci_label' ] = strip_tags( $new_instance[ 'is_ci_label' ] );	

	  	return $instance;
	}
  
  }