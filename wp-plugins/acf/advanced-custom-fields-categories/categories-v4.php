<?php

	class acf_field_categories extends
		acf_field {
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

		function __construct() {
			// vars
			$this->name     = 'categories';
			$this->label    = __( 'Categories' );
			$this->category = __( "Relational", 'acf' ); // Basic, Content, Choice, etc
			$this->defaults = array( // add default here to merge into your field.
			                         // This makes life easy when creating the field options as you don't need to use any if( isset('') ) logic. eg:
			                         //'preview_size' => 'thumbnail'
			);


			// do not delete!
			parent::__construct();


			// settings
			$this->settings = array(
				'path'    => apply_filters( 'acf/helpers/get_path', __FILE__ ),
				'dir'     => apply_filters( 'acf/helpers/get_dir', __FILE__ ),
				'version' => '1.0.0'
			);

		}


		/*
		*  create_options()
		*
		*  Create extra options for your field. This is rendered when editing a field.
		*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
		*
		*  @type	action
		*  @since	3.6
		*  @date	23/01/13
		*
		*  @param	$field	- an array holding all the field's data
		*/

		function create_field( $field ) {
			// defaults?

			$field['value'] = isset( $field['value'] ) ? $field['value'] : '';
			$type           = ( isset( $field['post_type'] ) ) ? ( empty( $field['post_type'] ) ? 'post' : $field['post_type'] ) : 'post';
			$child_of       = ( isset( $field['child_of'] ) ) ? ( empty( $field['child_of'] ) ? 0 : $field['child_of'] ) : 0;
			$parent         = ( isset( $field['parent'] ) ) ? ( empty( $field['parent'] ) ? '' : $field['parent'] ) : '';
			$hide_empty     = isset( $field['hide_empty'] ) ? $field['hide_empty'] : '1';
			$hierarchical   = isset( $field['hierarchical'] ) ? $field['hierarchical'] : '1';
			$taxonomy       = ( isset( $field['taxonomy'] ) ) ? ( empty( $field['taxonomy'] ) ? 'category' : $field['taxonomy'] ) : 'category';
			$include        = ( isset( $field['include'] ) ) ? ( empty( $field['include'] ) ? '' : $field['include'] ) : '';
			$exclude        = ( isset( $field['exclude'] ) ) ? ( empty( $field['exclude'] ) ? '' : $field['exclude'] ) : '';
			$orderby        = ( isset( $field['orderby'] ) ) ? ( empty( $field['orderby'] ) ? 'name' : $field['orderby'] ) : 'name';
			$order          = ( isset( $field['order'] ) ) ? ( empty( $field['order'] ) ? 'ASC' : $field['order'] ) : 'ASC';
			$multiple       = isset( $field['multiple'] ) ? $field['multiple'] : '0';
			$start_state    = isset( $field['start_state'] ) ? $field['start_state'] : '1';
			$post_count     = isset( $field['post_count'] ) ? $field['post_count'] : '0';

			$args = array(
				'type'         => $type,
				'child_of'     => $child_of,
				'parent'       => $parent,
				'hide_empty'   => $hide_empty,
				'hierarchical' => $hierarchical,
				'exclude'      => $exclude,
				'include'      => $include,
				'taxonomy'     => $taxonomy,
				'orderby'      => $orderby,
				'order'        => $order,
				'pad_counts'   => 1
			);

			$categories = get_categories( $args );
			$value      = $field['value']; ?>

			<div class="acf-categories">
				<!-- Single Value -->
				<?php if ( $multiple == 0 ) : ?>
					<label for="<?php echo $field['name'] ?>"></label>
					<select id="<?php echo $field['name'] ?>" class="<?php echo $field['class'] ?>" name="<?php echo $field['name']; ?>">
						<?php foreach ( $categories as $category ) : ?>
							<?php
							if ( $post_count == 1 ) {
								$cat_count = ' (' . $category->category_count . ')';
							} else {
								$cat_count = '';
							}
							?>

							<?php if ( $category->term_id == $value ) {
								$is_selected = 'selected="selected"';
							} else {
								$is_selected = '';
							} ?>
							<option value="<?php echo $category->term_id; ?>" <?php echo $is_selected; ?>><?php echo $category->name . '' . $cat_count; ?></option>
						<?php endforeach ?>
					</select>
				<?php endif; ?>
				<!-- End of Single Value -->

				<!-- Multiple Values -->
				<?php if ( $multiple == 1 ) : ?>
					<?php
					if ( $start_state == 1 ) {
						$start_state_class = 'block';
						$start_state_label = '- Hide Categories';
					} else {
						$start_state_class = 'none';
						$start_state_label = '+ Show Categories';
					}
					?>

					<div class="cat-toggle">
						<a href="#" class="cat-toggle-btn"><?php echo $start_state_label; ?></a>
					</div>

					<div class="cat-container" style="display: <?php echo $start_state_class; ?>">
						<div class="cat-buttons">
							<a href="#" class="select-all">Select All</a>
							<a href="#" class="deselect-all">Clear All</a>
							<a href="#" class="select-main">Select Main Categories</a>
						</div>

						<ul class="acf-categories-list">
							<?php foreach ( $categories as $category ) : ?>
								<?php $is_subcategory = $category->category_parent ? true : false; ?>
								<?php
								if ( $category->count == 1 ) {
									$cat_count = '<a href="' . admin_url() . '/edit.php?category_name=' . $category->slug . '"><span class="cat-post-count">' . $category->category_count . '</span></a>';
								} else {
									$cat_count = '';
								}
								?>
								<?php $subcategory_class = $category->category_parent ? '<span class="acf-subcategory">-</span>' : ''; ?>

								<?php if ( is_array( $value ) && in_array( $category->term_id, $value ) ) {
									$is_selected = 'checked';
								} else {
									$is_selected = '';
								} ?>

								<li>
									<label for="<?php echo $field['name'] ?>"></label>
									<input id="<?php echo $field['name'] ?>" type="checkbox" class="cat-categories-check <?php echo $is_subcategory ? 'cat-subcategories-check' : ''; ?>" value="<?php echo $category->term_id; ?>" name="<?php echo $field['name'] . '[]' ?>" <?php echo $is_selected ?>/><?php echo $subcategory_class . '' . $category->name . '' . $cat_count; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
				<!-- End of Multiple Values -->
			</div>
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

		function create_options( $field ) {
			// defaults?
			$field['child_of']     = isset( $field['child_of'] ) ? $field['child_of'] : '0';
			$field['parent']       = isset( $field['parent'] ) ? $field['parent'] : '';
			$field['hide_empty']   = isset( $field['hide_empty'] ) ? $field['hide_empty'] : '1';
			$field['hierarchical'] = isset( $field['hierarchical'] ) ? $field['hierarchical'] : '1';
			$field['taxonomy']     = isset( $field['taxonomy'] ) ? $field['taxonomy'] : 'category';
			$field['include']      = isset( $field['include'] ) ? $field['include'] : '';
			$field['exclude']      = isset( $field['exclude'] ) ? $field['exclude'] : '';
			$field['multiple']     = isset( $field['multiple'] ) ? $field['multiple'] : '1';
			$field['ret_val']      = isset( $field['ret_val'] ) ? $field['ret_val'] : 'category_slug';
			$field['orderby']      = isset( $field['orderby'] ) ? $field['orderby'] : 'id';
			$field['order']        = isset( $field['order'] ) ? $field['order'] : 'ASC';
			$field['post_count']   = isset( $field['post_count'] ) ? $field['post_count'] : '0';
			$field['start_state']  = isset( $field['start_state'] ) ? $field['start_state'] : '1';

			/*
			$field = array_merge($this->defaults, $field);
			*/

			// key is needed in the field names to correctly save the data
			$key = $field['name'];


			// Create Field Options HTML
			?>

			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( "Type", 'acf' ); ?></label>

					<p class="description">
						<a href="http://codex.wordpress.org/Function_Reference/get_categories"
						   target="_blank">See Documentation
						</a>
					</p>

				</td>
				<td>
					<?php
						$args = array(
							'public'   => true,
							'_builtin' => false
						);
						$post_types = get_post_types( $args, 'objects' );
						$types = array();
						$types['post'] = 'Post';

						foreach ( $post_types as $post_type ) {
							$types[$post_type->name] = $post_type->label;
						}

						do_action( 'acf/create_field', array(
							'type'    => 'select',
							'name'    => 'fields[' . $key . '][post_type]',
							'value'   => $field['post_type'],
							'choices' => $types
						) );
						unset( $types );
					?>
				</td>
			</tr>

			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( "Child Of", 'acf' ); ?></label>

					<p class="description">
						<a href="http://codex.wordpress.org/Function_Reference/get_categories"
						   target="_blank">See Documentation
						</a>
					</p>
				</td>
				<td>
					<?php do_action( 'acf/create_field', array(
						'type'  => 'text',
						'name'  => 'fields[' . $key . '][child_of]',
						'value' => $field['child_of'],
					) );
					?>
				</td>
			</tr>

			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( "Parent", 'acf' ); ?></label>

					<p class="description">
						<a href="http://codex.wordpress.org/Function_Reference/get_categories"
						   target="_blank">See Documentation
						</a>
					</p>
				</td>
				<td>
					<?php do_action( 'acf/create_field', array(
						'type'  => 'text',
						'name'  => 'fields[' . $key . '][parent]',
						'value' => $field['parent'],
					) );
					?>
				</td>
			</tr>

			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( "Order By", 'acf' ); ?></label>

					<p class="description">
						<a href="http://codex.wordpress.org/Function_Reference/get_categories"
						   target="_blank">See Documentation
						</a>
					</p>
				</td>
				<td>
					<?php do_action( 'acf/create_field', array(
						'type'    => 'select',
						'name'    => 'fields[' . $key . '][orderby]',
						'value'   => $field['orderby'],
						'choices' => array(
							'id'    => 'Category ID',
							'name'  => 'Category Title',
							'slug'  => 'Category Slug',
							'count' => 'Categories Count'
						)
					) );
					?>
				</td>
			</tr>

			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( "Order", 'acf' ); ?></label>

					<p class="description">
						<a href="http://codex.wordpress.org/Function_Reference/get_categories"
						   target="_blank">See Documentation
						</a>
					</p>
				</td>
				<td>
					<?php do_action( 'acf/create_field', array(
						'type'    => 'radio',
						'name'    => 'fields[' . $key . '][order]',
						'value'   => $field['order'],
						'choices' => array(
							'ASC'  => 'Asc',
							'DESC' => 'Desc',
						),
						'layout'  => 'horizontal',
					) );
					?>
				</td>
			</tr>

			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( "Hide Empty", 'acf' ); ?></label>

					<p class="description">
						<a href="http://codex.wordpress.org/Function_Reference/get_categories"
						   target="_blank">See Documentation
						</a>
					</p>
				</td>
				<td>
					<?php do_action( 'acf/create_field', array(
						'type'    => 'radio',
						'name'    => 'fields[' . $key . '][hide_empty]',
						'value'   => $field['hide_empty'],
						'choices' => array(
							'1' => 'Yes',
							'0' => 'No',
						),
						'layout'  => 'horizontal',
					) );
					?>
				</td>
			</tr>

			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( "Hierarchical", 'acf' ); ?></label>

					<p class="description">
						<a href="http://codex.wordpress.org/Function_Reference/get_categories"
						   target="_blank">See Documentation
						</a>
					</p>
				</td>
				<td>
					<?php do_action( 'acf/create_field', array(
						'type'    => 'radio',
						'name'    => 'fields[' . $key . '][hierarchical]',
						'value'   => $field['hierarchical'],
						'choices' => array(
							'1' => 'Yes',
							'0' => 'No',
						),
						'layout'  => 'horizontal',
					) );
					?>
				</td>
			</tr>

			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( "Taxonomy", 'acf' ); ?></label>

					<p class="description">
						<a href="http://codex.wordpress.org/Function_Reference/get_categories"
						   target="_blank">See Documentation
						</a>
					</p>

				</td>
				<td>
					<?php
						$args = array(
							'public' => true
						);
						$post_types = get_taxonomies( $args, 'objects' );
						$taxonomies = array();

						foreach ( $post_types as $post_type ) {
							$taxonomies[$post_type->name] = $post_type->label;
						}

						do_action( 'acf/create_field', array(
							'type'    => 'select',
							'name'    => 'fields[' . $key . '][taxonomy]',
							'value'   => $field['taxonomy'],
							'choices' => $taxonomies
						) );

						unset( $taxonomies );
					?>
				</td>
			</tr>

			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( "Include Categories", 'acf' ); ?></label>

					<p class="description">
						<a href="http://codex.wordpress.org/Function_Reference/get_categories"
						   target="_blank">See Documentation
						</a>
					</p>
				</td>
				<td>
					<?php do_action( 'acf/create_field', array(
						'type'  => 'text',
						'name'  => 'fields[' . $key . '][include]',
						'value' => $field['include'],
					) );
					?>
				</td>
			</tr>

			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( "Exclude Categories", 'acf' ); ?></label>

					<p class="description">
						<a href="http://codex.wordpress.org/Function_Reference/get_categories"
						   target="_blank">See Documentation
						</a>
					</p>
				</td>
				<td>
					<?php do_action( 'acf/create_field', array(
						'type'  => 'text',
						'name'  => 'fields[' . $key . '][exclude]',
						'value' => $field['exclude'],
					) );
					?>
				</td>
			</tr>

			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( "Multiple Values", 'acf' ); ?></label>

					<p class="description">If user can select multiple categories</p>
				</td>
				<td>
					<?php do_action( 'acf/create_field', array(
						'type'    => 'radio',
						'name'    => 'fields[' . $key . '][multiple]',
						'value'   => $field['multiple'],
						'choices' => array(
							'1' => 'Yes',
							'0' => 'No',
						),
						'layout'  => 'horizontal',
					) );
					?>
				</td>
			</tr>

			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( "Start State", 'acf' ); ?></label>

					<p class="description">
						If multiple values you have a slide down layout. This option indicates whether the layout should start opened or closed
					</p>
				</td>
				<td>
					<?php do_action( 'acf/create_field', array(
						'type'    => 'radio',
						'name'    => 'fields[' . $key . '][start_state]',
						'value'   => $field['start_state'],
						'choices' => array(
							'1' => 'Opened',
							'0' => 'Closed',
						),
						'layout'  => 'horizontal',
					) );
					?>
				</td>
			</tr>

			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( "Display Posts Count", 'acf' ); ?></label>

					<p class="description">Display a post count indicator next to each category</p>
				</td>
				<td>
					<?php do_action( 'acf/create_field', array(
						'type'    => 'radio',
						'name'    => 'fields[' . $key . '][post_count]',
						'value'   => $field['post_count'],
						'choices' => array(
							'1' => 'Yes',
							'0' => 'No',
						),
						'layout'  => 'horizontal',
					) );
					?>
				</td>
			</tr>

		<?php

		}


		/*
		*  input_admin_enqueue_scripts()
		*
		*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
		*  Use this action to add css + javascript to assist your create_field() action.
		*
		*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
		*  @type	action
		*  @since	3.6
		*  @date	23/01/13
		*/

		function field_group_admin_enqueue_scripts() {
			// Note: This function can be removed if not used
		}


		/*
		*  input_admin_head()
		*
		*  This action is called in the admin_head action on the edit screen where your field is created.
		*  Use this action to add css and javascript to assist your create_field() action.
		*
		*  @info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_head
		*  @type	action
		*  @since	3.6
		*  @date	23/01/13
		*/

		function field_group_admin_head() {
			// Note: This function can be removed if not used
		}


		/*
		*  field_group_admin_enqueue_scripts()
		*
		*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
		*  Use this action to add css + javascript to assist your create_field_options() action.
		*
		*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
		*  @type	action
		*  @since	3.6
		*  @date	23/01/13
		*/

		function format_value( $value, $post_id, $field ) {
			// defaults?
			/*
			$field = array_merge($this->defaults, $field);
			*/

			// perhaps use $field['preview_size'] to alter the $value?


			// Note: This function can be removed if not used
			return $value;
		}


		/*
		*  field_group_admin_head()
		*
		*  This action is called in the admin_head action on the edit screen where your field is edited.
		*  Use this action to add css and javascript to assist your create_field_options() action.
		*
		*  @info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_head
		*  @type	action
		*  @since	3.6
		*  @date	23/01/13
		*/

		function format_value_for_api( $value, $post_id, $field ) {
			// defaults?
			/*
			$field = array_merge($this->defaults, $field);
			*/

			// perhaps use $field['preview_size'] to alter the $value?


			// Note: This function can be removed if not used
			$multiple        = isset( $field['multiple'] ) ? $field['multiple'] : '0';
			$taxonomy        = ( isset( $field['taxonomy'] ) ) ? ( empty( $field['taxonomy'] ) ? 'category' : $field['taxonomy'] ) : 'category';
			$selected_values = $value;
			$object          = array();
			$ret_value       = '';

			if ( $multiple == 1 ) {
				if ( is_array( $selected_values ) ) {
					if ( ! empty( $selected_values ) ) {
						foreach ( $selected_values as $selected_value ) {
							array_push( $object, get_term_by( 'id', $selected_value, $taxonomy ) );
						}
					}
				}

				if ( ! empty( $object ) ) {
					$ret_value = $object;
				}

			} else {
				$ret_value = get_term_by( 'id', $value, $taxonomy );
			}

			return $ret_value;
		}


		/*
		*  load_value()
		*
		*  This filter is appied to the $value after it is loaded from the db
		*
		*  @type	filter
		*  @since	3.6
		*  @date	23/01/13
		*
		*  @param	$value - the value found in the database
		*  @param	$post_id - the $post_id from which the value was loaded from
		*  @param	$field - the field array holding all the field options
		*
		*  @return	$value - the value to be saved in te database
		*/

		function input_admin_enqueue_scripts() {
			// Note: This function can be removed if not used


			// register acf scripts
			wp_register_script( 'acf-input-categories', $this->settings['dir'] . 'js/input.js', array( 'acf-input' ), $this->settings['version'] );

			wp_register_style( 'acf-input-categories', $this->settings['dir'] . 'css/input.css', array( 'acf-input' ), $this->settings['version'] );


			// scripts
			wp_enqueue_script( array(
				'acf-input-categories'
			) );

			// styles
			wp_enqueue_style( array(
				'acf-input-categories'
			) );
		}


		/*
		*  update_value()
		*
		*  This filter is appied to the $value before it is updated in the db
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

		function input_admin_head() {
			// Note: This function can be removed if not used
		}


		/*
		*  format_value()
		*
		*  This filter is appied to the $value after it is loaded from the db and before it is passed to the create_field action
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

		function load_field( $field ) {
			// Note: This function can be removed if not used
			return $field;
		}


		/*
		*  format_value_for_api()
		*
		*  This filter is appied to the $value after it is loaded from the db and before it is passed back to the api functions such as the_field
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

		function load_value( $value, $post_id, $field ) {
			// Note: This function can be removed if not used
			return $value;
		}


		/*
		*  load_field()
		*
		*  This filter is appied to the $field after it is loaded from the database
		*
		*  @type	filter
		*  @since	3.6
		*  @date	23/01/13
		*
		*  @param	$field - the field array holding all the field options
		*
		*  @return	$field - the field array holding all the field options
		*/

		function update_field( $field, $post_id ) {
			// Note: This function can be removed if not used
			return $field;
		}


		/*
		*  update_field()
		*
		*  This filter is appied to the $field before it is saved to the database
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

		function update_value( $value, $post_id, $field ) {
			// Note: This function can be removed if not used

			return $value;
		}

	}


	// create field
	new acf_field_categories();

?>