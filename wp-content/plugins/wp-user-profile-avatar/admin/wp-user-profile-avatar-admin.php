<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WPUPA_Admin class.
 */
class WPUPA_Admin {

    public $settings_page;
    
    /**
     * Constructor - get the plugin hooked in and ready
     */
    public function __construct() {

        include_once 'wp-user-profile-avatar-settings.php';
        $this->settings_page = new WPUPA_Settings();

        $wpupa_tinymce = get_option( 'wpupa_tinymce' );
        if ( $wpupa_tinymce ) {
            add_action( 'init', array( $this, 'wpupa_add_buttons' ) );
        }

        include_once plugin_dir_path(__FILE__) . '../includes/wp-username-change.php';

        add_action( 'admin_menu', array( $this, 'wpupa_admin_menu' ), 12 );

        add_action( 'admin_enqueue_scripts', array( $this, 'wpupa_admin_enqueue_scripts' ) );

        add_action( 'show_user_profile', array( $this, 'wpupa_add_fields' ) );
        add_action( 'edit_user_profile', array( $this, 'wpupa_add_fields' ) );

        add_action( 'personal_options_update', array( $this, 'wpupa_save_fields' ) );
        add_action( 'edit_user_profile_update', array( $this, 'wpupa_save_fields' ) );

        add_action( 'admin_init', array( $this, 'wpupa_allow_contributor_subscriber_uploads' ) );

        add_action( 'init', array( $this, 'wpupa_thickbox_model_init' ) );
        add_action( 'wp_ajax_thickbox_model_view', array( $this, 'wpupa_thickbox_model_view' ) );
        add_action( 'wp_ajax_nopriv_thickbox_model_view', array( $this, 'wpupa_thickbox_model_view' ) );

        add_action( 'admin_init', array( $this, 'wpupa_init_size' ) );
        add_action( 'admin_init', array( $this, 'wpem_enable_event_comments' ) );
    }

    /**
     * add admin menu page function.
     *
     * @access public
     * @param
     * @return
     * @since 1.0
     */
    public function wpupa_admin_menu() {
        add_menu_page(
            __( 'Profile Avatar Settings', 'wp-user-profile-avatar' ),
            __( 'WP User Profile Avatar', 'wp-user-profile-avatar' ),
            'manage_options',
            'wp-user-profile-avatar',
            array( $this->settings_page, 'settings' ),
            'dashicons-admin-users'
        );
        if ( function_exists( 'add_submenu_page' ) ) {
            add_submenu_page( 'wp-user-profile-avatar', __( 'WP Username Change', 'wp-user-profile-avatar' ), __( 'WP Username Change ', 'wp-user-profile-avatar' ), 'manage_options', 'wpupa_username_change', 'wpupa_username_edit' );
            add_submenu_page( null, '', '', 'manage_options', 'wpupa_username_update', 'wpupa_user_update' );
            add_submenu_page( 'wp-user-profile-avatar', 'WP Avatar User Role Settings', 'WP Avatar User Role Settings', 'activate_plugins', 'avatar-social-picture', array( $this, 'wpupa_user_admin' ) ) ;
            add_submenu_page( 'wp-user-profile-avatar', 'Disable Comments', 'Disable Comments', 'manage_options', 'disable_comments_settings', array( $this, 'wpupa_comments_settings_page' ) );
            add_submenu_page( 'wp-user-profile-avatar', 'Delete Comments', 'Delete Comments', 'manage_options', 'disable_comments_tools', array( $this, 'wpupa_comments_tools_page' ) );
        }
    }

    /**
     * Render the settings form for Avatar Social Picture.
     */
    public function wpupa_user_admin() {
        if( isset( $_POST['wp-avatar-add-social-picture'] ) ){
            // Check if the nonce is set and valid
            if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'wp_avatar_add_social_picture_nonce' ) ) {
                // Nonce verification failed, possible CSRF attack
                die( 'Nonce verification failed!' );
            }
			$user_role = sanitize_text_field( wp_unslash($_POST['wp-avatar-add-social-picture'] ));
			update_option( 'wpupa_user_role', $user_role ); 
		}
        $user_role = get_option( 'wpupa_user_role' );

        ?>
        <form id="wp-avatar-settings" method="post" action="">
            <h3><?php esc_html_e( 'WP Avatar User Role Settings', 'wp-user-profile-avatar' ); ?></h3>
            <table class="form-table">
                <tr>
                    <th>
                        <label for="wp-avatar-capabilty">Role Required</label>
                    </th>
                    <td>
                        <select id="wp-avatar-add-social-picture" name="wp-avatar-add-social-picture">
                            <option value="read" <?php selected( $user_role, 'read' ); ?> >Subscriber</option>
                            <option value="edit-posts" <?php selected( $user_role, 'edit-posts' ); ?> >Contributor</option>
                            <option value="edit-published-posts" <?php selected( $user_role, 'edit-published-posts' ); ?> >Author</option>
                            <option value="moderate-comments" <?php selected( $user_role, 'moderate-comments' ); ?> >Editor</option>
                            <option value="activate-plugins" <?php selected( $user_role, 'activate-plugins' ); ?> >Administrator</option>
                        </select>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <?php wp_nonce_field( 'submit', 'wp-user-profile-avatar-social' ); ?>
                <input type="submit" class="button button-primary" id="submit" value="Save Changes">
            </p>
        </form>
        <?php
    }

    /**
     * This function is used to add comment setting page for sub menu
     *
     * @since 1.0.2
     */
    public function wpupa_comments_settings_page() {
        include 'templates/comments-settings-page.php';
    }

    /**
     * This function is used to add comment tool page for sub menu
     *
     * @since 1.0.2
     */
    function wpupa_comments_tools_page() {
        include 'templates/comments-tools-page.php';
    }

    /**
     * enqueue script and style function.
     * enqueue style and script for admin
     *
     * @access public
     * @param
     * @return
     * @since 1.0.0
     */
    public function wpupa_admin_enqueue_scripts() {
        global $pagenow;

        wp_register_style( 'wp-user-profile-avatar-backend', WPUPA_PLUGIN_URL . '/assets/css/backend.min.css', array(), WPUPA_VERSION );

        wp_register_script( 'wp-user-profile-avatar-admin-avatar', WPUPA_PLUGIN_URL . '/assets/js/admin-avatar.min.js', array( 'jquery' ), WPUPA_VERSION, true );

        wp_localize_script(
            'wp-user-profile-avatar-admin-avatar',
            'wp_user_profile_avatar_admin_avatar',
            array(
                'thinkbox_ajax_url'               => admin_url( 'admin-ajax.php' ) . '?height=600&width=770&action=thickbox_model_view',
                'thinkbox_title'                  => '',
                'icon_title'                      => __( 'WP User Profile Avatar', 'wp-user-profile-avatar' ),
                'wp_user_profile_avatar_security' => wp_create_nonce( '_nonce_user_profile_avatar_security' ),
                'media_box_title'                 => __( 'Choose Image: Default Avatar', 'wp-user-profile-avatar' ),
                'default_avatar'                  => WPUPA_PLUGIN_URL . '/assets/images/wp-user-thumbnail.png',
            )
        );

        wp_enqueue_style( 'wp-user-profile-avatar-backend' );
        wp_enqueue_script( 'wp-user-profile-avatar-admin-avatar' );
    }

    /**
     * add fields function.
     *
     * @access public
     * @param $user
     * @return
     * @since 1.0
     */
    public function wpupa_add_fields( $user ) {
        wp_enqueue_media();

        wp_enqueue_style( 'wp-user-profile-avatar-backend' );

        wp_enqueue_script( 'wp-user-profile-avatar-admin-avatar' );

        $user_id = get_current_user_id();

        $wpupa_original  = wpupa_get_url( $user->ID, array( 'size' => 'original' ) );
        $wpupa_thumbnail = wpupa_get_url( $user->ID, array( 'size' => 'thumbnail' ) );

        $wpupaattachmentid = get_user_meta( $user->ID, '_wpupa_attachment_id', true );
        $wpupa_url         = get_user_meta( $user->ID, '_wpupa_url', true );

        $wpupa_file_size        = get_user_meta( $user->ID, 'wpupa_file_size', true );
        $wpupa_size             = get_user_meta( $user->ID, 'wpupa-size', true );
        $wpupa_tinymce          = get_option( 'wpupa_tinymce' );
        $wpupa_allow_upload     = get_option( 'wpupa_allow_upload' );
        $wpupa_disable_gravatar = get_option( 'wpupa_disable_gravatar' );

        // Custom uplaod file size
        $wpupa_max_size = get_option( 'wpupa_max_file_size' );
        if ( ! $wpupa_max_size ) {
            $wpupa_max_size = 64 * 1024 * 1024;
        }
        $wpupa_max_size         = $wpupa_max_size / 1024 / 1024;
        $wpupa_upload_sizes     = array( 1, 2, 4, 8, 16, 32, 64, 128, 256, 512, 1024 );
        $wpupa_current_max_size = self::wpupa_get_closest( $wpupa_max_size, $wpupa_upload_sizes );

        include 'templates/user-profile-avatar-settings.php';

    }

    /**
     * save the added fields function.
     *
     * @access public
     * @param $user_id
     * @return
     * @since 1.0
     */
    public function wpupa_save_fields( $user_id ) {
        if ( current_user_can( 'edit_user', $user_id ) ) {

            if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ($_POST['_wpnonce'] ) ), 'update-user_' . $user_id ) ) {
                return;
            }

            if ( isset( $_POST['wpupa-url'] ) ) {
                 $wpupa_url = sanitize_text_field( wp_unslash( $_POST['wpupa-url'] ) );
            }
            if ( isset( $_POST['wpupaattachmentid'] ) ) {
                $wpupaattachmentid = absint( $_POST['wpupaattachmentid'] );
            }

            if ( isset( $_POST['wpupa_file_size'] ) ) {
                $wpupa_file_size = sanitize_text_field( wp_unslash( $_POST['wpupa_file_size'] ) );
                update_user_meta( $user_id, 'wpupa_file_size', $wpupa_file_size );
            }

            if ( isset( $_POST['wpupa-size'] ) ) {
                $wpupa_size = absint( $_POST['wpupa-size'] );
                update_user_meta( $user_id, 'wpupa-size', sanitize_text_field( $wpupa_size ) );
            }

            if ( isset( $wpupa_url, $wpupaattachmentid ) ) {
                update_user_meta( $user_id, '_wpupa_attachment_id', sanitize_text_field( $wpupaattachmentid ) );
                update_user_meta( $user_id, '_wpupa_url', esc_url_raw( $wpupa_url ) );
            }

            $wpupa_tinymce = ! empty( $_POST['wpupa-tinymce'] ) ? sanitize_text_field( wp_unslash( $_POST['wpupa-tinymce'] ) ) : '';

            $wpupa_allow_upload = ! empty( $_POST['wpupa-allow-upload'] ) ? sanitize_text_field( wp_unslash( $_POST['wpupa-allow-upload'] ) ) : '';

            $wpupa_disable_gravatar = ! empty( $_POST['wpupa-disable-gravatar'] ) ? sanitize_text_field( wp_unslash( $_POST['wpupa-disable-gravatar'] ) ) : '';

            if ( ! empty( $wpupaattachmentid ) || ! empty( $wpupa_url ) ) {
                update_user_meta( $user_id, '_wpupa_default', sanitize_text_field( 'wp_user_profile_avatar' ) );
            } else {
                update_user_meta( $user_id, '_wpupa_default', '' );
            }
        } else {
            status_header( '403' );
            die();
        }
    }

    /**
     * add button function.
     *
     * @access public
     * @param
     * @return
     * @since 1.0
     */
    public function wpupa_add_buttons() {
        // Add only in Rich Editor mode
        if ( get_user_option( 'rich_editing' ) == 'true' ) {
            add_filter( 'mce_external_plugins', array( $this, 'wpupa_add_tinymce_plugin' ) );
            add_filter( 'mce_buttons', array( $this, 'wpupa_register_button' ) );
        }
    }

    /**
     * set register button function.
     *
     * @access public
     * @param $buttons
     * @return
     * @since 1.0
     */
    public function wpupa_register_button( $buttons ) {
        array_push( $buttons, 'separator', 'wp_user_profile_avatar_shortcodes' );
        return $buttons;
    }

    /**
     * add tinymice plugin function.
     *
     * @access public
     * @param $plugins
     * @return
     * @since 1.0
     */
    public function wpupa_add_tinymce_plugin( $plugins ) {
        $plugins['wp_user_profile_avatar_shortcodes'] = WPUPA_PLUGIN_URL . '/assets/js/admin-avatar.min.js';
        return $plugins;
    }

    /**
     * add thickbox model function.
     *
     * @access public
     * @param
     * @return
     * @since 1.0
     */
    public function wpupa_thickbox_model_init() {
        add_thickbox();
    }

    /**
     * view thickbox model function.
     *
     * @access public
     * @param
     * @return
     * @since 1.0
     */
    public function wpupa_thickbox_model_view() {
        include_once WPUPA_PLUGIN_DIR . '/admin/templates/shortcode-popup.php';

        wp_die();
    }

    /**
     * allow contributors uploads function.
     * `
     *
     * @access public
     * @param
     * @return
     * @since 1.0
     */
    public function wpupa_allow_contributor_subscriber_uploads() {
        $contributor = get_role( 'contributor' );
        $subscriber  = get_role( 'subscriber' );

        $wpupa_allow_upload = get_option( 'wpupa_allow_upload' );

        if ( ! empty( $contributor ) ) {
            if ( $wpupa_allow_upload ) {
                $contributor->add_cap( 'upload_files' );
            } else {
                $contributor->remove_cap( 'upload_files' );
            }
        }

        if ( ! empty( $subscriber ) ) {
            if ( $wpupa_allow_upload ) {
                $subscriber->add_cap( 'upload_files' );
            } else {
                $subscriber->remove_cap( 'upload_files' );
            }
        }
    }

    /**
     * Get closest value from array
     */
    public function wpupa_get_closest( $wpupa_search, $wpupa_arr ) {
        $wpupa_closest = null;
        foreach ( $wpupa_arr as $wpupa_item ) {
            if ( $wpupa_closest === null || abs( $wpupa_search - $wpupa_closest ) > abs( $wpupa_item - $wpupa_search ) ) {
                $wpupa_closest = $wpupa_item;
            }
        }
        return $wpupa_closest;
    }

    public function wpupa_init_size() {
        if ( isset( $_POST['wpem-upload-max-file-size-field'] ) ) {

            // Verify nonce to ensure form submission is legitimate
            if ( ! isset( $_POST['wpem-upload-max-file-size-nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wpem-upload-max-file-size-nonce'] ) ), 'wpem_upload_max_file_size_action' ) ) {
                // Nonce verification failed, possible CSRF attack
                die( 'Nonce verification failed!' );
            }
            
            $wpupa_max_size = (int) $_POST['wpem-upload-max-file-size-field'] * 1024 * 1024;
            update_option( 'wpupa_max_file_size', sanitize_text_field( $wpupa_max_size ) );
            wp_safe_redirect( admin_url( 'upload.php?page=wpem_upload_max_file_size&max-size-updated=true' ) );
        }
        add_filter( 'upload_size_limit', array( $this, 'wpem_upload_max_increase_upload' ) );
    }
     /**
     * enable comments function.
     *
     * @access public
     * @param
     * @return
     */
    public function wpem_enable_event_comments() {
		
        $post_type = array( 'event_listing', 'event_zoom' );
		foreach( $post_type as $post ){
			add_post_type_support($post, 'comments');
		}
	}

    /**
     * return upload max file size
     */
    public function wpem_upload_max_increase_upload() {
        $wpupa_max_size = (int) get_option( 'wpupa_max_file_size' );

        if ( ! $wpupa_max_size ) {
            $wpupa_max_size = 64 * 1024 * 1024;
        }

        return $wpupa_max_size;
    }
}

new WPUPA_Admin();
