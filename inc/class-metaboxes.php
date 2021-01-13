<?php
namespace Openagenda;
/**
 * Class for handling individual calendar settings on calendar edit page.
 */
class Metaboxes implements Hookable {
    /**
     * Main metabox to register
     */
    protected $metaboxes = array();
    
    /**
     * Fields to display
     */
    protected $fields = array();

    /**
     * Constructor
     */
    public function __construct(){
        $this->metaboxes = array(
            'oa-calendar-settings' => array(
                'id'            => 'oa-calendar-settings',
                'title'         => __( 'Calendar settings', 'openagenda' ),
                'callback'      => array( $this, 'calendar_settings_markup' ),
                'screen'        => 'oa-calendar',
                'context'       => 'side',
                'priority'      => 'default',
                'callback_args' => array(),
            ), 
        );
        $this->fields = array(
            'oa-calendar-uid' => array(
                'metabox' => 'oa-calendar-settings',
                'type'    => 'text',
                'label'   => __( 'Calendar UID', 'openagenda' ),
                'default' => '',
            ),
            'oa-calendar-per-page' => array(
                'metabox' => 'oa-calendar-settings',
                'type'    => 'number',
                'label'   => __( 'Events per page', 'openagenda' ),
                'default' => (int) get_option( 'posts_per_page' ),
            ),
        );
    }


    /**
     * Registers hooks
     */
    public function register_hooks(){
        add_action( 'add_meta_boxes', array( $this, 'register_metaboxes' ), 10, 2 );
        add_action( 'save_post_oa-calendar', array( $this, 'calendar_settings_save' ), 10, 3 );
    }


    /**
     * Returns the parent admin page to register
     * 
     * @return  array  Main page arguments 
     */
    public function get_metaboxes(){
        return apply_filters( 'openagenda_metaboxes', $this->metaboxes );
    }


    /**
     * Returns the list of fields
     * 
     * @return  array  Fields array : 'name' => $args 
     */
    public function get_fields(){
        return apply_filters( 'openagenda_fields', $this->fields );
    }

    /**
     * Register the metaboxes
     * 
     * @param  string   $post_type  The post type for the current page.
     * @param  WP_Post  $post       The post object.
     */
    public function register_metaboxes( $post_type, $post ){
        foreach ( $this->get_metaboxes() as $id => $args ) {
            add_meta_box( 
                $args['id'],
                $args['title'],
                $args['callback'],
                $args['screen'],
                $args['context'],
                $args['priority'],
                $args['callback_args']
            );
        }
    }

    
    /**
     * Settings metabox markup
     * 
     * @param  WP_Post  $post  Current post
     * @param  array    $args  Additional callback arguments, passed via add_meta_box() function call
     */
    public function calendar_settings_markup( $post, $args ){
        wp_nonce_field( 'oa_calendar_settings_metabox_save_' . (int) $post->ID, 'oa_calendar_settings_nonce' );
        foreach ( $this->get_fields() as $name => $args ) {
            $this->render_field( $name, $args );
        }
    }


    /**
     * Renders our metabox fields
     * 
     * @param  string  $name  Name of the field. Used in id and name attributes 
     * @param  array   $args  Array of arguments for the field 
     */
    public function render_field( $name, $args = array() ){
        global $post;

        $args = wp_parse_args( $args, array(
            'metabox' => 'calendar-settings',
            'type'    => 'text',
            'label'   => __( 'New field', 'openagenda' ),
            'default' => '',
        ) );

        $field_value = get_post_meta( $post->ID, $name, true ) ? get_post_meta( $post->ID, $name, true ) : $args['default'];  

        switch ( $args['type'] ) {            
            default:
                ?>
                    <div class="components-base-control">
                        <div class="components-base-control__field">
                            <label for="<?php echo esc_attr( $name ); ?>" class="components-base-control__label" style="display: block; margin-bottom: 8px"><?php echo esc_html( $args['label'] ); ?></label>
                            <input id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" type="<?php echo esc_attr( $args['type'] ); ?>" class="components-text-control__input" value="<?php echo esc_attr( $field_value ); ?>" />
                        </div>
                    </div>
                <?php
                break;
        }
    }

    /**
     * Saves settings metabox fields
     *
     * @param  int      $post_ID  Post ID.
     * @param  WP_Post  $post     Post object.
     * @param  bool     $update   Whether this is an existing post being updated or not.
     */
    public function calendar_settings_save( $post_ID, $post, $update ){
        
        // Check nonce
        if ( ! isset( $_POST['oa_calendar_settings_nonce'] ) || ! wp_verify_nonce( $_POST['oa_calendar_settings_nonce'], 'oa_calendar_settings_metabox_save_' . (int) $post->ID ) ) {
            return;
        }

        // Check user has permissions
        if ( ! current_user_can( 'edit_post', (int) $post_ID ) ) {
            return;
        }

        // If autosaving, do nothing
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if( ! empty( $_POST['oa-calendar-uid'] ) ){
            update_post_meta( $post_ID, 'oa-calendar-uid', sanitize_text_field( $_POST['oa-calendar-uid'] ) );
        }

        if( ! empty( $_POST['oa-calendar-per-page'] ) ){
            update_post_meta( $post_ID, 'oa-calendar-per-page', (int) $_POST['oa-calendar-per-page'] );
        }
    }
}