<?php

namespace Elementor;

class SpeedTech_Widget_Popup_Account extends Widget_Base
{
    public function __construct($data = [], $args = null)
    {
        parent::__construct($data, $args);

        $cssPath = get_stylesheet_directory() . '/assets/css/widgets/popup_account.css';
        wp_register_style(
            'popup_account-style',
            get_stylesheet_directory_uri() . '/assets/css/widgets/popup_account.css',
            [],
            file_exists($cssPath) ? filemtime($cssPath) : time()
        );

        $js_uri  = get_stylesheet_directory_uri() . '/assets/js/widgets/popup_account.js';
        $version = time();
        $js_path = get_stylesheet_directory_uri() . '/assets/js/widgets/popup_account.js';
        if (file_exists($js_path)) {
            $version = filemtime($js_path);
        };
        wp_register_script('popup_account-js', $js_uri, array('jquery', 'elementor-frontend'), $version, true);
    }

    public function get_name()
    {
        return 'speedtech_popup_account';
    }

    public function get_title()
    {
        return 'Popup Account';
    }

    public function get_icon()
    {
        return 'eicon-my-account';
    }

    public function get_categories()
    {
        return ['speedtech'];
    }

    public function get_style_depends()
    {
        return ['popup_account-style'];
    }

    public function get_script_depends()
    {
        return array( 'popup_account-js' );
    }

    protected function _register_controls()
    {
        $this->start_controls_section(
            'section_title',
            [
                'label' => __('Content', 'speedtech'),
            ]
        );

        $this->add_control(
			'title_login', [
				'label' => esc_html__( 'Login Title', 'speedtech' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Đăng Nhập' , 'speedtech' ),
				'label_block' => true,
			]
		);

        $this->add_control(
			'link_login',
			[
				'label' => esc_html__( 'Login Link', 'speedtech' ),
				'type' => \Elementor\Controls_Manager::URL,
				'options' => [ 'url'],
				'default' => [
					'url' => '#',
				],
                'dynamic' => [
                    'active' => true,
                ],
				'label_block' => true,
			]
		);

        $this->add_control(
			'title_register', [
				'label' => esc_html__( 'Register Title', 'speedtech' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Đăng Kí' , 'speedtech' ),
				'label_block' => true,
			]
		);

        $this->add_control(
			'link_register',
			[
				'label' => esc_html__( 'Login Link', 'speedtech' ),
				'type' => \Elementor\Controls_Manager::URL,
				'options' => [ 'url'],
				'default' => [
					'url' => '#',
				],
                'dynamic' => [
                    'active' => true,
                ],
				'label_block' => true,
			]
		);

        $this->add_control(
			'icon_dropdown',
			[
				'label' => esc_html__( 'Icon Drop Down', 'speedtech' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
			'title_menu', [
				'label' => esc_html__( 'Title', 'speedtech' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Title Default' , 'speedtech' ),
				'label_block' => true,
			]
		);

        $repeater->add_control(
			'link_menu',
			[
				'label' => esc_html__( 'Link', 'speedtech' ),
				'type' => \Elementor\Controls_Manager::URL,
				'options' => [ 'url'],
				'default' => [
					'url' => '#',
				],
                'dynamic' => [
                    'active' => true,
                ],
				'label_block' => true,
			]
		);

        $this->add_control(
			'menu',
			[
				'label' => esc_html__( 'Popup Menu', 'speedtech' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'title_menu' => esc_html__( 'Tài khoản & Giao dịch', 'speedtech' ),
					],
					[
						'title_menu' => esc_html__( 'Đổi mật khẩu', 'speedtech' ),
					],
					[
						'title_menu' => esc_html__( 'Quên mật khẩu', 'speedtech' ),
					],
				],
				'title_field' => '{{{ title_menu }}}',
			]
		);

        $this->end_controls_section();
        
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display(); 
        
        if ( $settings['menu'] ) { 
            if ( is_user_logged_in() ) {
                $user_id = get_current_user_id();
                if ( function_exists( 'wpupa_get_url' ) ) {
                    $avatar_url = wpupa_get_url( $user_id, array( 'size' => 'original' ) );
                }
                if ( $user_id ) {
                    $user_name = get_the_author_meta('display_name', $user_id);
                }
                ?>
                <div class="profile-dropdown">
                    <div class="profile-trigger" id="profileTrigger">
                        <?php if ( $avatar_url ) {
                            echo '<img src="' . esc_url( $avatar_url ) . '" alt="Ảnh đại diện">';
                        } ?>
                        <?php if($user_name) { ?>
                            <span><?php echo $user_name; ?></span>
                        <?php } ?>
                        <?php if($settings['icon_dropdown']) { ?>
                            <span class="arrow"><?php \Elementor\Icons_Manager::render_icon( $settings['icon_dropdown'], [ 'aria-hidden' => 'true' ] ); ?></span>
                        <?php } ?>
                    </div>
                    <div class="dropdown-menu" id="dropdownMenu">
                        <?php  foreach (  $settings['menu'] as $item ) { ?>
                            <a href="<?php echo $item['link_menu']['url'] ?>"><?php echo $item['title_menu'] ?></a>
                        <?php } ?>
                            <a href="<?php echo wp_logout_url( get_permalink() ); ?>">Đăng xuất</a>
                    </div>
                </div>
            <?php }  else { ?>
                <div class="cta_action_account">
                    <?php if($settings['link_login'] && $settings['title_login']) { ?>
                        <div class="login">
                            <a href="<?php echo $settings['link_login']['url'] ?>" class="cta_login"><?php echo $settings['title_login'] ?></a>
                        </div>
                    <?php } ?>
                    <?php if($settings['link_register'] && $settings['title_register']) { ?>
                        <div class="register">
                            <a href="<?php echo $settings['link_register']['url'] ?>" class="cta_login"><?php echo $settings['title_register'] ?></a>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
		<?php }

    }

    protected function _content_template()
    {
    }
}