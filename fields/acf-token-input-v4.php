<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

// check if class already exists
if( !class_exists('acf_field_acf_token_input') ) :


class acf_field_acf_token_input extends acf_field {
	
	// vars
	var $settings, // will hold info such as dir / path
		$defaults; // will hold default field options
		
		
	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function __construct( $settings )
	{
		// vars
		$this->name = 'acf_token_input';
		$this->label = __('Token Input');
		$this->category = __("Choice",'acf'); // Basic, Content, Choice, etc
		$this->defaults = array(

		);
		// do not delete!
    parent::__construct();
    // settings
		$this->settings = $settings;
	}
	
	
	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like below) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function create_options( $field )
{
		$key = $field['name'];
		$field['allow_null'] = null;
		$field['serialize'] = 0;
		// implode choices so they work in a textarea
		if( is_array($field['choices']) ){		
			foreach( $field['choices'] as $k => $v ){
				$field['choices'][ $k ] = $k . ' : ' . $v;
			}
			$field['choices'] = implode("\n", $field['choices']);
		}
		// Create Field Options HTML
		?>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label for=""><?php _e("Choices",'acf'); ?></label>
		<p><?php _e("Enter each choice on a new line.",'acf'); ?></p>
		<p><?php _e("For more control, you may specify both a value and label like this:",'acf'); ?></p>
		<p><?php _e("red : Red",'acf'); ?><br /><?php _e("blue : Blue",'acf'); ?></p>
		<p><?php _e("<br />There is also a way to make this field dynamic. Check the documentation.",'acf'); ?></p>
	</td>
	<td>
		<?php
		do_action('acf/create_field', array(
			'type'	=>	'textarea',
			'class' => 	'textarea field_option-choices',
			'name'	=>	'fields['.$key.'][choices]',
			'value'	=>	$field['choices'],
		));
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Serialize Values?",'acf'); ?></label>
	</td>
	<td>
		<?php
		echo $field['serialize'];
		do_action('acf/create_field', array(
			'type'	=>	'radio',
			'name'	=>	'fields['.$key.'][serialize]',
			'value'	=>	$field['serialize'],
			'choices'	=>	array(
				1	=>	__("Yes",'acf'),
				0	=>	__("No",'acf'),
			),
			'layout'	=>	'horizontal',
		));
		?>
	</td>
</tr>
<?php		
	}
	
	
	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function create_field( $field )
	{
		
		global $post;
		$prePopulate = null;
		$populate = null;
		$data = get_post_meta($post->ID, $field['_name'], true);
		$populate = acf_get_the_choices($field);
		
		if( !empty($data) ){
			$prePopulate = acf_get_the_choices($field, $data);
		} else {
			$prePopulate = null;
		}
		?>
		<div>
			<input type="hidden" name="acf-token-input-key" value="<?php echo $field['key']; ?>" />
			<input type="hidden" name="acf-token-input-name" value="<?php echo $field['_name']; ?>" />
			<input type="text" class="acf-token-input-handler" name="<?php echo $field['_name']; ?>" />
			<script type="text/javascript">
			(function( $ ){
				var options = {
					theme: "facebook", 
					preventDuplicates: true, 
					tokenValue:"id",
					<?php if( $prePopulate ){ ?>prePopulate: <?php echo $prePopulate; ?><?php } ?>
				};
				$(".acf-token-input-handler").tokenInput(<?php echo $populate; ?>, options);
			})(jQuery);
			</script>
		</div>
		<?php
	}
	
	
	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add CSS + JavaScript to assist your create_field() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function input_admin_enqueue_scripts()
	{
		// Note: This function can be removed if not used
		
		// vars
		$url = $this->settings['url'];
		$version = $this->settings['version'];
				
		// register & include JS
		wp_register_script( 'acf-input-acf_token_input', "{$url}assets/js/jquery.tokeninput.js", array('acf-input'), $version );
		wp_enqueue_script('acf-input-acf_token_input');
		
		// register & include CSS
		wp_register_style( 'acf-input-acf_token_input', "{$url}assets/css/token-input.css", array(), $version );
		wp_enqueue_style('acf-input-acf_token_input');
		
		wp_register_style( 'acf-input-acf_token_input_facebook_theme', "{$url}assets/css/token-input-facebook.css", array(), $version );
		wp_enqueue_style('acf-input-acf_token_input_facebook_theme');
		
	}
	
	
	/*
	*  input_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your create_field() action.
	*
	*  @info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_head
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function input_admin_head()
	{
		// Note: This function can be removed if not used
	}
	
	
	/*
	*  field_group_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	*  Use this action to add CSS + JavaScript to assist your create_field_options() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function field_group_admin_enqueue_scripts()
	{
		// Note: This function can be removed if not used
	}

	
	/*
	*  field_group_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is edited.
	*  Use this action to add CSS and JavaScript to assist your create_field_options() action.
	*
	*  @info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_head
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function field_group_admin_head()
	{
		// Note: This function can be removed if not used
	}


	/*
	*  load_value()
	*
	*  This filter is applied to the $value after it is loaded from the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value found in the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the value to be saved in the database
	*/
	
	function load_value( $value, $post_id, $field )
	{
		// Note: This function can be removed if not used
		return $value;
	}
	
	
	/*
	*  update_value()
	*
	*  This filter is applied to the $value before it is updated in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field )
	{
		
		// Note: This function can be removed if not used
		return $value;
	}
	
	
	/*
	*  format_value()
	*
	*  This filter is applied to the $value after it is loaded from the db and before it is passed to the create_field action
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/
	
	function format_value( $value, $post_id, $field )
	{
		// defaults?
		/*
		$field = array_merge($this->defaults, $field);
		*/
		
		// perhaps use $field['preview_size'] to alter the $value?
		
		
		// Note: This function can be removed if not used
		return $value;
	}
	
	
	/*
	*  format_value_for_api()
	*
	*  This filter is applied to the $value after it is loaded from the db and before it is passed back to the API functions such as the_field
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/
	
	function format_value_for_api( $value, $post_id, $field )
	{
		// defaults?
		/*
		$field = array_merge($this->defaults, $field);
		*/
		
		// perhaps use $field['preview_size'] to alter the $value?
		
		
		// Note: This function can be removed if not used
		return $value;
	}
	
	
	/*
	*  load_field()
	*
	*  This filter is applied to the $field after it is loaded from the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$field - the field array holding all the field options
	*/
	
	function load_field( $field )
	{
		// Note: This function can be removed if not used
		return $field;
	}
	
	
	/*
	*  update_field()
	*
	*  This filter is applied to the $field before it is saved to the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*  @param	$post_id - the field group ID (post_type = acf)
	*
	*  @return	$field - the modified field
	*/

	function update_field( $field, $post_id )
	{
		// Note: This function can be removed if not used
		return $field;
	}

}

// initialize
new acf_field_acf_token_input( $this->settings );

// class_exists check
endif;


function acf_save_token_inputs( $post_id ){
	if ( $parent_id = wp_is_post_revision( $post_id ) ){
		$post_id = $parent_id;
	}
	remove_action( 'save_post', 'acf_save_token_inputs' );
	if( !empty($_POST['acf-token-input-key']) and !empty($_POST['acf-token-input-name']) ){
		$key = $_POST['acf-token-input-key'];
		$name = $_POST['acf-token-input-name'];
		$values = $_POST[$name];
		$options = get_field_object($key);
		$serialize = $options['serialize'];
		if( $serialize == 1 ){
			$values = serialize( $values );
		}
		if( trim($values) ){
			//file_put_contents(dirname(__FILE__) . '/array.php', print_r($values, true));
			update_post_meta($post_id, $name, $values);
		} else {
			delete_post_meta($post_id, $name);
		}
		add_action( 'save_post', 'acf_save_token_inputs' );
	}
}
add_action( 'save_post', 'acf_save_token_inputs' );

function acf_prepopulate_token_input( $data ){
	if( $data ){
		$data = explode(',', $data);
		$array = array();
		foreach($data as $item){
			$array[] = [
				'id' => $item,
				'name' => get_the_title($item)
			];
		}			
		$prePopulate = json_encode( $array );
	}
}

function acf_get_the_choices($field, $data = null){	
	if( !empty($field['choices']) ){
		$choices = $field['choices'];
		$choices = explode("\n", $choices);
		$choice_array = array();
		if( count($choices) ){
			if( $data ){ $data = explode(',', $data); }
			foreach($choices as $choice){
				$choice = explode(':', $choice);
				$choice = array_filter($choice);
				$id = trim($choice[0]);
				$name = trim($choice[1]);
				if( $data ){
					if( in_array($id, $data) ){
						$choice_array[] = ['id' => $id, 'name' => $name];
					}
				} else {
					$choice_array[] = ['id' => trim($id), 'name' => trim($name)];
				}
			}
		}
		$populate = json_encode( $choice_array );
		return $populate;
	}
}








