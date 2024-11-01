<?php

/**
 * Plugin Name: Gutenberg Template Manager
 * Plugin URI: https://wordpress.org/plugins/template-manager/
 * Description: This plugin useful for the create template and update the template if needed.
 * Version:     1.0.5
 * Author: Thedotstore  
 * Author URI: http://thedotstore.com/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: gutenberg-template-manager
 */
/**
 * Exit if accessed directly
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'tm_fs' ) ) {
    tm_fs()->set_basename( false, __FILE__ );
    return;
}


if ( !function_exists( 'tm_fs' ) ) {
    // Create a helper function for easy SDK access.
    function tm_fs()
    {
        global  $tm_fs ;
        
        if ( !isset( $tm_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $tm_fs = fs_dynamic_init( array(
                'id'             => '4764',
                'slug'           => 'template-manager',
                'premium_slug'   => 'wp-custom-field-for-gutenberg-editor-premium',
                'type'           => 'plugin',
                'public_key'     => 'pk_82d2078b6c4c1cea64a6cd2138fc6',
                'is_premium'     => false,
                'premium_suffix' => 'Pro',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'menu'           => array(
                'first-path' => 'plugins.php',
                'support'    => false,
            ),
                'is_live'        => true,
            ) );
        }
        
        return $tm_fs;
    }
    
    // Init Freemius.
    tm_fs();
    // Signal that SDK was initiated.
    do_action( 'tm_fs_loaded' );
    tm_fs()->add_action( 'after_uninstall', 'tm_fs_uninstall_cleanup' );
}

class GutenbergTemplateManager
{
    protected  $gtm_prefix = 'gtm_' ;
    protected  $gtm_custom_meta_fields = array() ;
    public function __construct()
    {
        add_action( 'init', array( $this, 'gtm_register_template_post_type' ) );
        add_action( 'add_meta_boxes', array( $this, 'gtm_add_custom_meta_box' ) );
        add_action( 'save_post_templates', array( $this, 'gtm_save_custom_meta' ) );
        add_action( 'save_post_templates', array( $this, 'gtm_update_content' ), 15 );
        add_action( 'admin_init', array( $this, 'gtm_create_template' ) );
        add_filter( 'manage_edit-templates_columns', array( $this, 'gtm_templates_columns' ) );
        add_action(
            'manage_templates_posts_custom_column',
            array( $this, 'gtm_manage_templates_columns' ),
            10,
            2
        );
        //Action for add gutenberg custom block.
        add_action( 'enqueue_block_editor_assets', array( $this, 'gtm_add_block_editor_script' ) );
    
        // Add Javascript and CSS for admin screens.
        add_action( 'admin_enqueue_scripts', array( $this, 'gtm_enqueue_admin' ) );
        $this->gtm_custom_meta_fields = array( array(
            'label' => 'Select Post Type',
            'desc'  => 'Select Post Type for this Template.',
            'id'    => $this->gtm_prefix . 'select',
            'type'  => 'select',
        ), array(
            'label'   => 'Add Sections',
            'desc'    => 'Add title for the block.',
            'id'      => $this->gtm_prefix . 'repeatable',
            'type'    => 'repeatable',
            'options' => array( 'Section Name:', 'Section ID:' ),
        ) );
    }
    
    public static function gtm_add_block_editor_script()
    {
        wp_enqueue_script( 'gtm-gutenberg-block', plugins_url( 'assets/js/blocks/block.build.js', __FILE__ ), array(
            'wp-blocks',
            'wp-i18n',
            'wp-element',
            'wp-editor',
            'wp-components',
            'jquery'
        ) );
    }
    
    public function gtm_register_template_post_type()
    {
        // define labels for custom post type
        $labels = array(
            'name'               => 'Templates',
            'singular_name'      => 'Template',
            'add_new_item'       => 'Add New Template',
            'edit_item'          => 'Edit Template',
            'view_item'          => 'View Template',
            'not_found'          => 'Template not found',
            'not_found_in_trash' => 'Template Not Found in Trash',
        );
        $args = array(
            'labels'    => $labels,
            'public'    => true,
            'supports'  => array( 'title', 'thumbnail' ),
            'menu_icon' => 'dashicons-groups',
        );
        register_post_type( 'templates', $args );
    }
    
    public function gtm_add_custom_meta_box()
    {
        add_meta_box(
            'gtm_custom_meta_box',
            // $id
            'Template Settings',
            // $title
            array( $this, 'gtm_show_custom_meta_box' ),
            // $callback
            'Templates',
            // $page
            'normal',
            // $context
            'high'
        );
        // $priority
    }
    
    public function gtm_show_custom_meta_box()
    {
        global  $post ;
        $gtm_custom_meta_fields = $this->gtm_custom_meta_fields;
        // Use nonce for verification
        echo  '<input type="hidden" name="gtm_custom_meta_box_nonce" value="' . esc_attr( wp_create_nonce( basename( __FILE__ ) ) ) . '" />' ;
        // Begin the field table and loop
        echo  '<table class="form-table">' ;
        foreach ( $gtm_custom_meta_fields as $gtm_field ) {
            // get value of this field if it exists for this post
            $gtm_meta = get_post_meta( $post->ID, $gtm_field['id'], true );
            // begin a table row with
            echo  '<tr>
                <th><label for="' . esc_attr( $gtm_field['id'] ) . '">' . esc_html( $gtm_field['label'] ) . '</label></th>
                <td>' ;
            switch ( $gtm_field['type'] ) {
                // case items will go here
                // text
                case 'text':
                    echo  '<input type="text" name="' . esc_attr( $gtm_field['id'] ) . '" id="' . esc_attr( $gtm_field['id'] ) . '" value="' . esc_attr( $gtm_meta ) . '" size="30" />
                    <br /><span class="description">' . esc_html( $gtm_field['desc'] ) . '</span>' ;
                    break;
                    // textarea
                    // textarea
                    // textarea
                    // textarea
                // textarea
                // textarea
                // textarea
                // textarea
                case 'textarea':
                    echo  '<textarea name="' . esc_attr( $gtm_field['id'] ) . '" id="' . esc_attr( $gtm_field['id'] ) . '" cols="60" rows="4">' . esc_html( $gtm_meta ) . '</textarea>
        			<br /><span class="description">' . esc_html( $gtm_field['desc'] ) . '</span>' ;
                    break;
                    // checkbox
                    // checkbox
                    // checkbox
                    // checkbox
                // checkbox
                // checkbox
                // checkbox
                // checkbox
                case 'checkbox':
                    echo  '<input type="checkbox" name="' . esc_attr( $gtm_field['id'] ) . '" id="' . esc_attr( $gtm_field['id'] ) . '" ', ( $gtm_meta ? ' checked="checked"' : '' ), '/>
                    <label for="' . esc_attr( $gtm_field['id'] ) . '">' . esc_html( $gtm_field['desc'] ) . '</label>' ;
                    break;
                    // select
                    // select
                    // select
                    // select
                // select
                // select
                // select
                // select
                case 'select':
                    $gtm_get_post_type_args = array(
                        'public' => true,
                    );
                    $gtm_post_types = get_post_types( $gtm_get_post_type_args, object );
                    // use 'names' if you want to get only name of the post type.
                    echo  '<select required name="' . esc_attr( $gtm_field['id'] ) . '" id="' . esc_attr( $gtm_field['id'] ) . '">' ;
                    echo  '<option value="">Select Post Type</option>' ;
                    $gtm_exclude_post_type = array( 'attachment', 'templates', 'product' );
                    foreach ( $gtm_post_types as $gtm_option ) {
                        if ( true === in_array( $gtm_option->name, $gtm_exclude_post_type, true ) ) {
                            continue;
                        }
                        echo  '<option', ( $gtm_meta === $gtm_option->name ? ' selected="selected"' : '' ), ' value="' . esc_attr( $gtm_option->name ) . '">' . esc_html( $gtm_option->label ) . '</option>' ;
                    }
                    echo  '</select><br /><span class="description">' . esc_html( $gtm_field['desc'] ) . '</span>' ;
                    break;
                    // repeatable
                    // repeatable
                    // repeatable
                    // repeatable
                // repeatable
                // repeatable
                // repeatable
                // repeatable
                case 'repeatable':
                    echo  '<a class="repeatable-add button" href="#">+</a>
            		<ul id="' . esc_attr( $gtm_field['id'] ) . '-repeatable" class="custom_repeatable">' ;
                    $i = 0;
                    
                    if ( $gtm_meta ) {
                        foreach ( $gtm_meta as $gtm_row ) {
                            echo  '<li>
									<span style="cursor: move;" class="sort handle">|||</span>
									<label>' . esc_html( $gtm_field['options'][0] ) . '</label>
			                        <input class="repeatable_heading" type="text" name="' . esc_attr( $gtm_field['id'] ) . '[' . esc_attr( $i ) . '][0]" id="' . esc_attr( $gtm_field['id'] ) . '" value="' . esc_attr( $gtm_row[0] ) . '" size="30" />
			                        <label>' . esc_html( $gtm_field['options'][1] ) . '</label>
			                        <input readonly class="repeatable_uniqid" type="text" name="' . esc_attr( $gtm_field['id'] ) . '[' . esc_attr( $i ) . '][1]" id="' . esc_attr( $gtm_field['id'] ) . '" value="' . esc_attr( $gtm_row[1] ) . '" size="30" />
			                        <a style="display: none" class="repeatable-remove button" href="#">-</a>
								</li>' ;
                            $i++;
                        }
                    } else {
                        $gtm_uniqid = uniqid( 'gtm' );
                        echo  '<li>
								<span style="cursor: move;" class="sort handle">|||</span>
								<label>' . esc_html( $gtm_field['options'][0] ) . '</label>
			                    <input class="repeatable_heading" type="text" name="' . esc_attr( $gtm_field['id'] ) . '[' . esc_attr( $i ) . '][0]" id="' . esc_attr( $gtm_field['id'] ) . '" value="" size="30" />
			                    <label>' . esc_html( $gtm_field['options'][1] ) . '</label>
			                    <input readonly class="repeatable_uniqid" type="text" name="' . esc_attr( $gtm_field['id'] ) . '[' . esc_attr( $i ) . '][1]" id="' . esc_attr( $gtm_field['id'] ) . '" value="' . esc_attr( $gtm_uniqid ) . '" size="30" />
			                    <a style="display: none" class="repeatable-remove button" href="#">-</a>
							</li>' ;
                    }
                    
                    echo  '</ul>
        			<span class="description">' . esc_html( $gtm_field['desc'] ) . '</span>' ;
                    break;
            }
            //end switch
            echo  '</td></tr>' ;
        }
        // end foreach
        echo  '</table>' ;
        // end table
    }
    
    public function gtm_save_custom_meta( $post_id )
    {
        $gtm_custom_meta_fields = $this->gtm_custom_meta_fields;
        // verify nonce
        if ( !isset( $_POST['gtm_custom_meta_box_nonce'] ) || !wp_verify_nonce( sanitize_key( $_POST['gtm_custom_meta_box_nonce'] ), basename( __FILE__ ) ) ) {
            return $post_id;
        }
        // check autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }
        // check permissions
        
        if ( isset( $_POST['post_type'] ) && 'page' === $_POST['post_type'] ) {
            if ( !current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } elseif ( !current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }
        
        // loop through fields and save the data
        foreach ( $gtm_custom_meta_fields as $gtm_field ) {
            $gtm_old_meta = get_post_meta( $post_id, $gtm_field['id'], true );
            $post_array_sanitise_ = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
            $gtm_new_meta = ( !empty($post_array_sanitise_[$gtm_field['id']]) ? $post_array_sanitise_[$gtm_field['id']] : '' );
            
            if ( $gtm_new_meta && $gtm_new_meta !== $gtm_old_meta ) {
                update_post_meta( $post_id, $gtm_field['id'], $gtm_new_meta );
            } elseif ( '' === $gtm_new_meta && $gtm_old_meta ) {
                delete_post_meta( $post_id, $gtm_field['id'], $gtm_old_meta );
            }
        
        }
        // end foreach
    }    
    public function gtm_update_content( $post_id )
    {
        // verify nonce
        if ( !isset( $_POST['gtm_custom_meta_box_nonce'] ) || !wp_verify_nonce( sanitize_key( $_POST['gtm_custom_meta_box_nonce'] ), basename( __FILE__ ) ) ) {
            return $post_id;
        }
        $gtm_get_post_type = get_post_meta( $post_id, $this->gtm_prefix . 'select', true );
        $gtm_get_template_structure = get_post_meta( $post_id, $this->gtm_prefix . 'repeatable', true );
        $gtm_order = array();
        $gtm_order = array_column( $gtm_get_template_structure, 1 );
        $gtm_get_post_args = array(
            'post_type'      => $gtm_get_post_type,
            'posts_per_page' => -1,
        );
        $gtm_new_blocks = array();
        $gtm_pages = new WP_Query( $gtm_get_post_args );
        
        if ( $gtm_pages->have_posts() ) {
            while ( $gtm_pages->have_posts() ) {
                $gtm_pages->the_post();
                $gtm_content = get_the_content();
                
                if ( has_blocks( $gtm_content ) ) {
                    $gtm_blocks = parse_blocks( $gtm_content );
                    foreach ( $gtm_get_template_structure as $gtm_item ) {
                        $gtm_array_column = array_column( array_column( $gtm_blocks, 'attrs' ), 'blockId' );
                        $gtm_array_search = array_search( $gtm_item[1], $gtm_array_column, true );
                        if ( $gtm_array_search === false ) {
                            $gtm_new_blocks[] = array(
                                'blockName'    => 'gtm/gtm-block',
                                'attrs'        => array(
                                'blockId'      => $gtm_item[1],
                                'blockHeading' => $gtm_item[0],
                            ),
                                'innerBlocks'  => array(),
                                'innerHTML'    => '',
                                'innerContent' => array(),
                            );
                        }
                    }
                    $gtm_blocks = array_merge( $gtm_blocks, $gtm_new_blocks );
                    usort( $gtm_blocks, function ( $a, $b ) use( $gtm_order ) {
                        
                        if ( isset( $a['attrs']['blockId'] ) ) {
                            $a = array_search( $a['attrs']['blockId'], $gtm_order, true );
                        } else {
                            $a = false;
                        }
                        
                        
                        if ( isset( $b['attrs']['blockId'] ) ) {
                            $b = array_search( $b['attrs']['blockId'], $gtm_order, true );
                        } else {
                            $b = false;
                        }
                        
                        
                        if ( $a === false && $b === false ) {
                            // both items are dont cares
                            return 0;
                            // a == b
                        } else {
                            
                            if ( $a === false ) {
                                // $a is a dont care item
                                return 1;
                            } else {
                                
                                if ( $b === false ) {
                                    // $b is a dont care item
                                    return -1;
                                } else {
                                    return $a - $b;
                                }
                            
                            }
                        
                        }
                    
                    } );
                    $gtm_post_content = $this->gtm_serialize_blocks( $gtm_blocks );
                    $gtm_post = array(
                        'ID'           => get_the_ID(),
                        'post_content' => $gtm_post_content,
                    );
                    remove_action( 'save_post', array( $this, 'gtm_update_content' ), 15 );
                    wp_update_post( $gtm_post );
                    add_action( 'save_post', array( $this, 'gtm_update_content' ), 15 );
                }
            
            }
            wp_reset_postdata();
        }
    
    }
    
    /**
     * Renders an HTML-serialized form of a list of block objects
     *
     * @param array $blocks The list of parsed block objects.
     *
     * @return string The HTML-serialized form of the list of blocks.
     * @since 5.3.0
     *
     */
    public function gtm_serialize_blocks( $blocks )
    {
        return implode( "\n\n", array_map( array( $this, 'gtm_serialize_block' ), $blocks ) );
    }
    
    /**
     * Renders an HTML-serialized form of a block object
     *
     * @param array $block The block being rendered.
     *
     * @return string The HTML-serialized form of the block
     * @since 5.3.0
     *
     */
    public function gtm_serialize_block( $block )
    {
        // Non-block content has no block name.
        if ( null === $block['blockName'] ) {
            return $block['innerHTML'];
        }
        $unwanted = array(
            '--',
            '<',
            '>',
            '&',
            '\\"'
        );
        $wanted = array(
            '\\u002d\\u002d',
            '\\u003c',
            '\\u003e',
            '\\u0026',
            '\\u0022'
        );
        $name = ( 0 === strpos( $block['blockName'], 'core/' ) ? substr( $block['blockName'], 5 ) : $block['blockName'] );
        $has_attrs = !empty($block['attrs']);
        $attrs = ( $has_attrs ? str_replace( $unwanted, $wanted, wp_json_encode( $block['attrs'] ) ) : '' );
        // Early abort for void blocks holding no content.
        if ( empty($block['innerContent']) ) {
            return ( $has_attrs ? "<!-- wp:{$name} {$attrs} /-->" : "<!-- wp:{$name} /-->" );
        }
        $output = ( $has_attrs ? "<!-- wp:{$name} {$attrs} -->\n" : "<!-- wp:{$name} -->\n" );
        $inner_block_index = 0;
        foreach ( $block['innerContent'] as $chunk ) {
            $output .= ( null === $chunk ? $this->gtm_serialize_block( $block['innerBlocks'][$inner_block_index++] ) : $chunk );
            $output .= "\n";
        }
        $output .= "<!-- /wp:{$name} -->";
        return $output;
    }
    
    public function gtm_create_template()
    {
        $args = array(
            'post_type'      => 'templates',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        );
        $gtm_templates = new WP_Query( $args );
        
        if ( $gtm_templates->have_posts() ) {
            while ( $gtm_templates->have_posts() ) {
                $gtm_templates->the_post();
                $gtm_get_post_type = get_post_meta( get_the_ID(), 'gtm_select', true );
                $gtm_post_type_object = get_post_type_object( $gtm_get_post_type );
                $gtm_get_template_structure = get_post_meta( get_the_ID(), 'gtm_repeatable', true );
                $gtm_template_array = array();
                if ( is_array( $gtm_get_template_structure ) || is_object( $gtm_get_template_structure ) ) {
                    foreach ( $gtm_get_template_structure as $gtm_item ) {
                        $gtm_template_array[] = array( 'gtm/gtm-block', array(
                            'blockId'      => $gtm_item[1],
                            'blockHeading' => $gtm_item[0],
                        ) );
                    }
                }
                $gtm_post_type_object->template = $gtm_template_array;
            }
            wp_reset_postdata();
        }
    
    }
    
    public function gtm_templates_columns( $columns )
    {
        $columns = array(
            'cb'         => '&lt;input type="checkbox" />',
            'title'      => __( 'Title' ),
            'gtm_select' => __( 'Post Type' ),
            'date'       => __( 'Date' ),
        );
        return $columns;
    }
    
    /*
     * Enqueue gutenberg custom block script
     * @since 1.0
     */
    public function gtm_manage_templates_columns( $column, $post_id )
    {
        switch ( $column ) {
            /* If displaying the 'gtm_select' column. */
            case 'gtm_select':
                /* Get the post meta. */
                $gtm_select = get_post_meta( $post_id, 'gtm_select', true );
                /* If no gtm_select is found, output a default message. */
                
                if ( empty($gtm_select) ) {
                    echo  esc_attr( 'Unknown' ) ;
                } else {
                    printf( esc_html( $gtm_select ) );
                }
                
                break;
                /* Just break out of the switch statement for everything else. */
                /* Just break out of the switch statement for everything else. */
                /* Just break out of the switch statement for everything else. */
                /* Just break out of the switch statement for everything else. */
            /* Just break out of the switch statement for everything else. */
            /* Just break out of the switch statement for everything else. */
            /* Just break out of the switch statement for everything else. */
            /* Just break out of the switch statement for everything else. */
            default:
                break;
        }
    }
    
    public function gtm_enqueue_admin()
    {
        wp_enqueue_script(
            'gtm-custom',
            plugins_url( 'assets/js/gtm-custom.js', __FILE__ ),
            array( 'jquery' ),
            '1.0',
            true
        );
    }

}
// Create an instance of our class to kick off the whole thing
$GutenbergTemplateManager = new GutenbergTemplateManager();