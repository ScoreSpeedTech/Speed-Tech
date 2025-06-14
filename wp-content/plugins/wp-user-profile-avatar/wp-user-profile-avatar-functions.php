<?php
if ( ! function_exists( 'wpupa_get_rating' ) ) {

    /**
     * get user avatar rating function.
     *
     * @access public
     * @param
     * @return array
     * @since 1.0
     */
    function wpupa_get_rating() {
        return apply_filters(
            'wp_user_avatar_rating',
            array(
                'G'  => __( 'G &#8212; Suitable for all audiences', 'wp-user-profile-avatar' ),
                'PG' => __( 'PG &#8212; Possibly offensive, usually for audiences 13 and above', 'wp-user-profile-avatar' ),
                'R'  => __( 'R &#8212; Intended for adult audiences above 17', 'wp-user-profile-avatar' ),
                'X'  => __( 'X &#8212; Even more mature than above', 'wp-user-profile-avatar' ),
            )
        );
    }
}

if ( ! function_exists( 'wpupa_get_file_size' ) ) {

    /**
     * get file size function.
     *
     * @access public
     * @param
     * @return array
     * @since 1.0
     */
    function wpupa_get_file_size() {
        return apply_filters(
            'wp_user_avatar_file_size',
            array(
                '1'    => __( '1MB', 'wp-user-profile-avatar' ),
                '2'    => __( '2MB', 'wp-user-profile-avatar' ),
                '4'    => __( '4MB', 'wp-user-profile-avatar' ),
                '8'    => __( '8MB', 'wp-user-profile-avatar' ),
                '16'   => __( '16MB', 'wp-user-profile-avatar' ),
                '32'   => __( '32MB', 'wp-user-profile-avatar' ),
                '64'   => __( '64MB', 'wp-user-profile-avatar' ),
                '128'  => __( '128MB', 'wp-user-profile-avatar' ),
                '256'  => __( '256MB', 'wp-user-profile-avatar' ),
                '512'  => __( '512MB', 'wp-user-profile-avatar' ),
                '1024' => __( '1024MB', 'wp-user-profile-avatar' ),
            )
        );
    }
}

if ( ! function_exists( 'wpupa_get_default_avatar' ) ) {

    /**
     * get default user avatar function.
     *
     * @access public
     * @param
     * @return array
     * @since 1.0
     */
    function wpupa_get_default_avatar() {
        return apply_filters(
            'wp_user_default_avatar',
            array(
                'mystery'          => __( 'Mystery Man', 'wp-user-profile-avatar' ),
                'blank'            => __( 'Blank', 'wp-user-profile-avatar' ),
                'gravatar_default' => __( 'Gravatar Logo', 'wp-user-profile-avatar' ),
                'identicon'        => __( 'Identicon (Generated)', 'wp-user-profile-avatar' ),
                'wavatar'          => __( 'Wavatar (Generated)', 'wp-user-profile-avatar' ),
                'monsterid'        => __( 'MonsterID (Generated)', 'wp-user-profile-avatar' ),
                'retro'            => __( 'Retro (Generated)', 'wp-user-profile-avatar' ),
                'robohash'         => __( 'RoboHash (Generated)', 'wp-user-profile-avatar' ),
            )
        );
    }
}

if ( ! function_exists( 'wpupa_get_default_avatar_url' ) ) {

    /**
     * get default avatar urlS function.
     *
     * @access public
     * @param $args
     * @return string
     * @since 1.0
     */
    function wpupa_get_default_avatar_url( $args = array(), $avatar_args = array(), $url = '') {

        $size          = ! empty( $args['size'] ) ? $args['size'] : 'thumbnail';
        $user_id       = ! empty( $args['user_id'] ) ? $args['user_id'] : '';
        $wpupa_default = isset($avatar_args['default']) ? $avatar_args['default'] : get_option( 'avatar_default' );
        $avatar_size   = get_option( 'avatar_size' );
        if ( $avatar_size ) {
            $size = get_option( 'avatar_size' );
        }
        if($wpupa_default !== 'wp_user_profile_avatar' ) {
            return $url;
        }
        if ( $wpupa_default == 'wp_user_profile_avatar' || $size == 'admin' ) {
            $attachment_id = get_option( 'wpupa_attachment_id' );

            if ( ! empty( $attachment_id ) ) {
                $image_attributes = wp_get_attachment_image_src( $attachment_id, $size );
                if ( ! empty( $image_attributes ) ) {
                    return $image_attributes[0];
                } else {
                    return WPUPA_PLUGIN_URL . '/assets/images/wp-user-' . $size . '.png';
                }
            } else {
                return WPUPA_PLUGIN_URL . '/assets/images/wp-user-' . $size . '.png';
            }
        } else {
            if ( ! empty( $wpupa_default ) ) {
                if ( $size == 'admin' ) {
                    return WPUPA_PLUGIN_URL . '/assets/images/wp-user-' . $size . '.png';
                } elseif ( $size == 'original' ) {
                    $size_no = 512;
                } elseif ( $size == 'medium' ) {
                    $size_no = 150;
                } elseif ( $size == 'thumbnail' ) {
                    $size_no = 150;
                } else {
                    $size_no = 32;
                }

                $avatar = get_avatar( 'unknown@gravatar.com', $size_no, $wpupa_default );

                preg_match( '%<img.*?src=["\'](.*?)["\'].*?/>%i', $avatar, $matches );

                if ( ! empty( $matches[1] ) ) {
                    return $matches[1] . 'forcedefault=1';
                } else {
                    return WPUPA_PLUGIN_URL . '/assets/images/wp-user-' . $size . '.png';
                }
            } else {
                return WPUPA_PLUGIN_URL . '/assets/images/wp-user-' . $size . '.png';
            }
        }
    }
}

if ( ! function_exists( 'wpupa_get_url' ) ) {

    /**
     * get url fro usser profile avatar function.
     *
     * @access public
     * @param $user_id, $args
     * @return string
     * @since 1.0
     */
    function wpupa_get_url( $user_id, $args = array(), $avatar_args = array(), $url = '') {
        $size = ! empty( $args['size'] ) ? $args['size'] : 'thumbnail';

        $wpupa_url = esc_url( get_user_meta( $user_id, '_wpupa_url', true ) );

        $attachment_id = esc_attr( get_user_meta( $user_id, '_wpupa_attachment_id', true ) );

        $wpupa_default = esc_attr( get_user_meta( $user_id, '_wpupa_default', true ) );

        $wpupa_size = esc_attr( get_user_meta( $user_id, 'wpupa-size', true ) );

        add_image_size( 'wpupavatar-default', $wpupa_size, $wpupa_size, true );

        if ( ! empty( $wpupa_url ) ) {
            return $wpupa_url;
        } elseif ( ! empty( $attachment_id ) ) {

                $image_attributes = wp_get_attachment_image_src( $attachment_id, $size );

            if ( ! empty( $image_attributes ) ) {
                if ( $size == 'wpupavatar-default' ) {
                    return $image_attributes;
                } else {
                    return $image_attributes[0];
                }
            } else {
                return wpupa_get_default_avatar_url(
                    array(
                        'user_id' => $user_id,
                        'size'    => $size,
                    ), $avatar_args, $url
                );
            }
        } else {
            return wpupa_get_default_avatar_url(
                array(
                    'user_id' => $user_id,
                    'size'    => $size,
                ),$avatar_args, $url
            );
        }
    }
}

if ( ! function_exists( 'wpupa_check_wpupa_url' ) ) {

    /**
     * check if profile avatar url or not function.
     *
     * @access public
     * @param $user_id
     * @return boolean
     * @since 1.0
     */
    function wpupa_check_wpupa_url( $user_id = '' ) {
        $attachment_url = esc_url( get_user_meta( $user_id, '_wpupa_url', true ) );

        $attachment_id = esc_attr( get_user_meta( $user_id, '_wpupa_attachment_id', true ) );

        $wpupa_default = esc_attr( get_user_meta( $user_id, '_wpupa_default', true ) );

        if ( ! empty( $attachment_url ) || ! empty( $attachment_id ) ) {
            return true;
        } else {
            return false;
        }
    }
}

if ( ! function_exists( 'wpupa_check_wpupa_gravatar' ) ) {

    /**
     * check it is gratavatar or not function.
     *
     * @access public
     * @param $id_or_email, $check_gravatar, $user, $email
     * @return boolean
     * @since 1.0
     */
    function wpupa_check_wpupa_gravatar( $id_or_email = '', $check_gravatar = 0, $user = '', $email = '' ) {
        $wp_user_hash_gravatar = get_option( 'wp_user_hash_gravatar' );

        $wpupa_default = get_option( 'avatar_default' );

        if ( trim( $wpupa_default ) != 'wp_user_profile_avatar' ) {
            return true;
        }

        if ( ! is_object( $id_or_email ) && ! empty( $id_or_email ) ) {
            // Find user by ID or e-mail address
            $user = is_numeric( $id_or_email ) ? get_user_by( 'id', $id_or_email ) : get_user_by( 'email', $id_or_email );
            // Get registered user e-mail address
            $email = ! empty( $user ) ? $user->user_email : '';
        }

        if ( $email == '' ) {

            if ( ! is_numeric( $id_or_email ) and ! is_object( $id_or_email ) ) {
                $email = $id_or_email;
            } elseif ( ! is_numeric( $id_or_email ) and is_object( $id_or_email ) ) {
                $email = $id_or_email->comment_author_email;
            }
        }
        if ( $email != '' ) {
            $hash = md5( strtolower( trim( $email ) ) );
            // check if gravatar exists for hashtag using options

            if ( is_array( $wp_user_hash_gravatar ) ) {

                if ( array_key_exists( $hash, $wp_user_hash_gravatar ) and is_array( $wp_user_hash_gravatar[ $hash ] ) and array_key_exists( gmdate( 'm-d-Y' ), $wp_user_hash_gravatar[ $hash ] ) ) {
                    return (bool) $wp_user_hash_gravatar[ $hash ][ gmdate( 'm-d-Y' ) ];
                }
            }

            // end
            if ( isset( $_SERVER['HTTPS'] ) && ( 'on' == $_SERVER['HTTPS'] || 1 == $_SERVER['HTTPS'] ) || isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
                $http = 'https';
            } else {
                $http = 'http';
            }
            $gravatar = $http . '://www.gravatar.com/avatar/' . $hash . '?d=404';

            $data = wp_cache_get( $hash );

            if ( false === $data ) {
                $response = wp_remote_head( $gravatar );
                $data     = is_wp_error( $response ) ? 'not200' : $response['response']['code'];

                wp_cache_set( $hash, $data, $group = '', $expire = 60 * 5 );
                // here set if hashtag has avatar
                $check_gravatar = ( $data == '200' ) ? true : false;
                if ( $wp_user_hash_gravatar == false ) {
                    $wp_user_hash_gravatar[ $hash ][ gmdate( 'm-d-Y' ) ] = (bool) $check_gravatar;
                    add_option( 'wp_user_hash_gravatar', serialize( $wp_user_hash_gravatar ) );
                } else {

                    if ( is_array( $wp_user_hash_gravatar ) && ! empty( $wp_user_hash_gravatar ) ) {

                        if ( array_key_exists( $hash, $wp_user_hash_gravatar ) ) {

                            unset( $wp_user_hash_gravatar[ $hash ] );
                            $wp_user_hash_gravatar[ $hash ][ gmdate( 'm-d-Y' ) ] = (bool) $check_gravatar;
                            update_option( 'wp_user_hash_gravatar', serialize( $wp_user_hash_gravatar ) );
                        } else {
                            $wp_user_hash_gravatar[ $hash ][ gmdate( 'm-d-Y' ) ] = (bool) $check_gravatar;
                            update_option( 'wp_user_hash_gravatar', serialize( $wp_user_hash_gravatar ) );
                        }
                    }
                }
                // end
            }
            $check_gravatar = ( $data == '200' ) ? true : false;
        } else {
            $check_gravatar = false;
        }
        // Check if Gravatar image returns 200 (OK) or 404 (Not Found)
        return (bool) $check_gravatar;
    }
}

if ( ! function_exists( 'wpupa_get_image_sizes' ) ) {

    /**
     * get profile image size function.
     *
     * @access public
     * @param
     * @return array
     * @since 1.0
     */
    function wpupa_get_image_sizes() {
        return apply_filters(
            'wp_image_sizes',
            array(
                'original'  => __( 'Original', 'wp-user-profile-avatar' ),
                'large'     => __( 'Large', 'wp-user-profile-avatar' ),
                'medium'    => __( 'Medium', 'wp-user-profile-avatar' ),
                'thumbnail' => __( 'Thumbnail', 'wp-user-profile-avatar' ),
            )
        );
    }
}

if ( ! function_exists( 'wpupa_get_image_alignment' ) ) {

    /**
     * get profile image alignment function.
     *
     * @access public
     * @param
     * @return array
     * @since 1.0
     */
    function wpupa_get_image_alignment() {
        return apply_filters(
            'wp-image-alignment',
            array(
                'aligncenter' => __( 'Center', 'wp-user-profile-avatar' ),
                'alignleft'   => __( 'Left', 'wp-user-profile-avatar' ),
                'alignright'  => __( 'Right', 'wp-user-profile-avatar' ),
            )
        );
    }
}

if ( ! function_exists( 'wpupa_get_image_link_to' ) ) {

    /**
     * get profile image link function.
     *
     * @access public
     * @param
     * @return array
     * @since 1.0
     */
    function wpupa_get_image_link_to() {
        return apply_filters(
            'wp-image-link-to',
            array(
                'none'       => __( 'None', 'wp-user-profile-avatar' ),
                'image'      => __( 'Image File', 'wp-user-profile-avatar' ),
                'attachment' => __( 'Attachment Page', 'wp-user-profile-avatar' ),
                'custom'     => __( 'Custom URL', 'wp-user-profile-avatar' ),
            )
        );
    }
}
// Restrict access to Media Library (users can only see/select own media)

if ( ! function_exists( 'wpb_show_current_user_attachments' ) ) {

    add_filter( 'ajax_query_attachments_args', 'wpb_show_current_user_attachments' );

    /**
     * show current user attachments function.
     *
     * @access public
     * @param
     * @return array
     * @since 1.0
     */
    function wpb_show_current_user_attachments( $query ) {
        $user_id = get_current_user_id();
        if ( $user_id ) {
            $query['author']      = $user_id;
            $query['subscriber']  = $user_id;
            $query['contributor'] = $user_id;
            $query['editor']      = $user_id;
        }
        return $query;
    }
}

if ( ! function_exists( 'wpupa_file_size_limit' ) ) {

    /**
     * get file size limit function.
     *
     * Limit upload size for non-admins. Admins get the default limit
     *
     * @access public
     * @param
     * @return array
     * @since 1.0
     */
    function wpupa_file_size_limit( $limit ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            $wpupa_file_size = get_option( 'wpupa_file_size' );
            $limit           = $wpupa_file_size * 1048576;
        }
        return $limit;
    }

    add_filter( 'upload_size_limit', 'wpupa_file_size_limit' );
}
if ( ! function_exists( 'wpupa_delete_comments_everywhere' ) ) {
    
	/**
     * delete comments from entire website function.
     *
     * delete comment on entire website
     *
     * @access public
     * @param
     * @return array
     * @since 1.0
     */
    function wpupa_delete_comments_everywhere() {
        global $wpdb;
        $result = $wpdb->query( "DELETE FROM {$wpdb->comments} WHERE 1=1" );

        // Clear the cache for comments to ensure fresh data is fetched
        wp_cache_delete( 'comments', 'comment' );

        $post_ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts}" );
        foreach ( $post_ids as $post_id ) {
            wp_update_comment_count_now( $post_id );
        }

        return $result;
    }
}

if ( ! function_exists( 'wpupa_delete_comments_by_post_types' ) ) {
    
	/**
     * delete comments from selected post types in website function.
     *
     * delete comment on selected places on website
     *
     * @access public
     * @param
     * @return array
     * @since 1.0
     */
    function wpupa_delete_comments_by_post_types( $post_types ) {
        global $wpdb;

        $post_type_placeholders = implode( "','", $post_types );

        $post_ids = $wpdb->get_col( $wpdb->prepare( 
            "SELECT ID FROM {$wpdb->posts} WHERE post_type IN (%s)", 
            $post_type_placeholders 
        ) );

        $result = $wpdb->query( 
            $wpdb->prepare( 
                "DELETE FROM {$wpdb->comments} WHERE comment_post_ID IN (SELECT ID FROM {$wpdb->posts} WHERE post_type IN (%s))", 
                $post_type_placeholders 
            )
        );

        foreach ( $post_ids as $post_id ) {
            wp_update_comment_count_now( $post_id );
        }

        return $result;
    }
}
use \WPUPA_WpUserNameChange\WPUPA_WpUserNameChange;

if ( ! function_exists( 'wpupa_username_edit' ) ) {
	/**
     * edit user name function.
     *
     * edit username from backend side 
     *
     * @access public
     * @param
     * @return array
     * @since 1.0
     */
	function wpupa_username_edit() { ?> 
		<div class="wrap userupdater">
			<p>
				<h1>
					<?php esc_html_e( 'Wp Users List', 'wp-user-profile-avatar' ); ?>
				</h1>
			</p>
			<?php
			$wpuser  = new WPUPA_WpUserNameChange();
			$records = $wpuser->wpuser_select();

			if ( $records ) {
				?>
				<table class="wp-list-table widefat fixed striped users"  cellpadding="3" cellspacing="3" width="100%">
					<thead>
						<tr>
							<th>
								<strong>
									<?php esc_html_e( 'User ID', 'wp-user-profile-avatar' ); ?>
								</strong>
							</th>
							<th>
								<strong>
									<?php esc_html_e( 'User Name', 'wp-user-profile-avatar' ); ?>
								</strong>
							</th>
							<th>
								<strong>
									<?php esc_html_e( 'Role', 'wp-user-profile-avatar' ); ?>
								</strong>
							</th>
							<th>
								<strong>
									<?php esc_html_e( 'Update', 'wp-user-profile-avatar' ); ?>
								</strong>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $records as $user ) {
							$user_info = get_userdata( $user->ID );
							?>
							<tr>
								<td><?php echo esc_attr( $user->ID ); ?></td>
								<td><?php echo esc_attr( $user->user_login ); ?></td>
								<td><?php echo esc_html( implode( ', ', ( $user_info->roles ) ) ); ?></td>
								<td>
									<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=wpupa_username_update&update=' . $user->ID ) ) ); ?>">
										<?php esc_html_e( 'update', 'wp-user-profile-avatar' ); ?>
									</a>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php } ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'wpupa_user_update' ) ) {
	/**
     * update username function.
     *
     * update username from backend side 
     *
     * @access public
     * @param
     * @return array
     * @since 1.0
     */
	function wpupa_user_update() {
		if ( isset( $_REQUEST['update'] ) ) {
			if(!current_user_can('manage_options' ) || wp_verify_nonce( 'update','_wpnonce' ) ){
				return;
			}
			$wpuser = new WPUPA_WpUserNameChange();
			global $wpdb;
			$id        = trim( sanitize_text_field( wp_unslash( $_REQUEST['update'] ) ) );
			$user_info = get_userdata( $id );
			$result    = $wpdb->get_results( $wpdb->prepare( "SELECT * from $wpdb->users WHERE ID = %d", $id ) );
			foreach ( $result as $user ) {
				$username = $user->user_login;
			}
			if ( ! empty( $_REQUEST['submit'] ) ) {
                $name = isset( $_POST['user_login'] ) ? sanitize_user( wp_unslash( $_POST['user_login'] ) ) : '';
				if ( empty( $name ) ) {
					$errorMsg = 'Error : Please do not enter  empty username.';
				} elseif ( username_exists( $name ) ) {
					$errorMsg = 'Error: This username(<i>' . esc_attr( $name ) . '</i>) is already exist.';
				} else {
					$wpuser->wpuser_update( $id, $name );
					echo '<div class="updated"><p><strong>Username Updated</strong></p></div>';
				}
			}
			?>
			<div class="wrap">
				<h1><?php esc_html_e( 'Update WP Username', 'wp-user-profile-avatar' ); ?></h1>
				<?php
				if ( isset( $errorMsg ) ) {
					echo "<div class='error'><p><strong>" . esc_attr( $errorMsg ) . '</strong></p></div>';
				}
				?>
			</div>
			<form method="post" id="user-udate" action="<?php echo esc_url( isset( $_SERVER['REQUEST_URI'] ) ? sanitize_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '' ); ?>">
				<table class="form-table">
					<tr>
						<th><label for="olduser-login"><?php esc_html_e( 'Old Username', 'wp-user-profile-avatar' ); ?></label></th>
						<td><strong><?php echo esc_attr( $username ); ?></strong></td>
					</tr>
					<tr>
						<th><label for="user-login"><?php esc_html_e( 'New Username', 'wp-user-profile-avatar' ); ?></label></th>
						<td><input type="text" name="user_login" class="regular-text" id="user_login" value="
						<?php
						if ( ! empty( $_POST['user-login'] ) ) {
							echo esc_attr( $name );
						}
						?>
						"/></td>
					</tr>
				</table>
				<input type="submit" name="submit" id="submit" class="button button-primary" value="Update WP Username">
			</form>
		<?php } else { ?>
			<script>
				window.location = '<?php echo esc_url( admin_url( 'admin.php?page=wpupa_username_change' ) ); ?>'
			</script>
			<?php
		}
	}
}
