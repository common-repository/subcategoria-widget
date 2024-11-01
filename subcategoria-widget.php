<?php
/*
Plugin Name: Subcategoria Widget
Plugin URI: http://wordpresslivro.com/subcategoria-widget-plugin
Description: Permite você exibir Subcategorias de uma determinada categoria em um Widget
Author: Anderson Makiyama
Version: 1.0
Author URI: http://wordpresslivro.com
*/


class Subcategoria_Widget extends WP_Widget {

	function Subcategoria_Widget() {
		$widget_ops = array( 'classname' => 'Subcategoria_Widget', 'description' => __( "Subcategoria Widget" ) );
		$this->WP_Widget('Subcategoria_Widget', __('Subcategoria Widget'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Categories' ) : $instance['title'], $instance, $this->id_base);
		$c = $instance['count'] ? '1' : '0';
		$h = $instance['hierarchical'] ? (bool) $instance['hierarchical'] : false;
		$d = $instance['dropdown'] ? '1' : '0';
		$co = $instance['child_of'] ? $instance['child_of']: '';
		$he = (bool) $instance['hide_empty'];
		
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

		$cat_args = array('orderby' => 'name', 'show_count' => $c, 'hierarchical' => $h, 'hide_empty' => $he, 'child_of' => $co);
			/*echo "<pre>";
			print_r($cat_args);
			echo "</pre>";
			exit();*/
		if ( $d ) {
			$cat_args['show_option_none'] = __('Select Category');
			wp_dropdown_categories(apply_filters('widget_categories_dropdown_args', $cat_args));
?>

<script type='text/javascript'>
/* <![CDATA[ */
	var dropdown = document.getElementById("cat");
	function onCatChange() {
		if ( dropdown.options[dropdown.selectedIndex].value > 0 ) {
			location.href = "<?php echo home_url(); ?>/?cat="+dropdown.options[dropdown.selectedIndex].value;
		}
	}
	dropdown.onchange = onCatChange;
/* ]]> */
</script>

<?php
		} else {
?>
		<ul>
<?php
		$cat_args['title_li'] = '';
		wp_list_categories(apply_filters('widget_categories_args', $cat_args));
?>
		</ul>
<?php
		}

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['count'] = !empty($new_instance['count']) ? 1 : 0;
		$instance['hierarchical'] = !empty($new_instance['hierarchical']) ? 1 : 0;
		$instance['dropdown'] = !empty($new_instance['dropdown']) ? 1 : 0;
		$instance['child_of'] = strip_tags($new_instance['child_of']);
		$instance['hide_empty'] = strip_tags( $new_instance['hide_empty'] );

		return $instance;
	}

	function form( $instance ) {
		global $wpdb;
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = esc_attr( $instance['title'] );
		$count = isset($instance['count']) ? (bool) $instance['count'] :false;
		$hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
		$dropdown = isset( $instance['dropdown'] ) ? (bool) $instance['dropdown'] : false;
		$child_of = isset( $instance['child_of'] ) ? esc_attr( $instance['child_of'] ): '';
		$hide_empty = esc_attr( $instance['hide_empty']); 
		
		$categorias = $wpdb->get_results("select t.term_id, t.name from wp_terms t inner join wp_term_taxonomy tt on tt.term_id = t.term_id where tt.taxonomy='category' and tt.parent=0 ORDER BY t.term_id ASC");
		$cats = "";
		//$cats= $cats .'<select name="' . $this->get_field_name('include') . '" id="' . $this->get_field_id('include') . '" class="widefat" />';
		foreach($categorias as $linha)
		{
			$cats = $cats . "<option value='" . $linha->term_id . "' " . get_selected($linha->term_id,$child_of) . ">" . $linha->name . "  </option>";
		} 		
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
        <p>        
        <label for="<?php echo $this->get_field_id('child_of'); ?>"><?php _e( 'Exibir Subcategorias de:' ); ?></label> 

        <?php
           		echo "<select name='" . $this->get_field_name('child_of') . "' id='" . $this->get_field_id('child_of') . "' class='widefat'>" .  $cats . "</select>";
			?>
        </p>
		<p>
		<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['hide_empty'], true) ?> id="<?php echo $this->get_field_id('hide_empty'); ?>" name="<?php echo $this->get_field_name('hide_empty'); ?>" />
        <label for="<?php echo $this->get_field_id('hide_empty'); ?>"><?php _e('Ocultar subcategorias sem Posts'); ?></label><br />

		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('dropdown'); ?>" name="<?php echo $this->get_field_name('dropdown'); ?>"<?php checked( $dropdown ); ?> />
		<label for="<?php echo $this->get_field_id('dropdown'); ?>"><?php _e( 'Show as dropdown' ); ?></label><br />

		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>"<?php checked( $count ); ?> />
		<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e( 'Show post counts' ); ?></label><br />

		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('hierarchical'); ?>" name="<?php echo $this->get_field_name('hierarchical'); ?>"<?php checked( $hierarchical ); ?> />
		<label for="<?php echo $this->get_field_id('hierarchical'); ?>"><?php _e( 'Show hierarchy' ); ?></label></p>       
<?php
	}

}


add_action( 'widgets_init', create_function('', 'return register_widget("Subcategoria_Widget");') );

function get_selected($x,$y){
	if($x == $y) return " selected='selected' ";
	return "";
}
?>
