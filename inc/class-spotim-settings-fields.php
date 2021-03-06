<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * SpotIM_Settings_Fields
 *
 * Plugin settings fields.
 *
 * @since 2.0.0
 */
class SpotIM_Settings_Fields {

    /**
     * Constructor
     *
     * Get things started.
     *
     * @since 2.0.0
     *
     * @access public
     *
     * @param SpotIM_Options $options Plugin options.
     */
    public function __construct( $options ) {
        $this->options = $options;
    }

    /**
     * Register Settings
     *
     * Register admin settings for the plugin.
     *
     * @since 2.0.0
     *
     * @access public
     *
     * @return void
     */
    public function register_settings() {
        register_setting(
            $this->options->option_group,
            $this->options->slug,
            array( $this->options, 'validate' )
        );
    }

    /**
     * General Settings Section Header
     *
     * @since 2.0.0
     *
     * @access public
     *
     * @return void
     */
    public function general_settings_section_header() {
        echo '<p>' . esc_html__( 'Basic settings to integrate your Spot.IM account with WordPress.', 'spotim-comments' ) . '</p>';
    }

    /**
     * Display Settings Section Header
     *
     * @since 2.0.0
     *
     * @access public
     *
     * @return void
     */
    public function display_settings_section_header() {
        echo '<p>' . esc_html__( 'Select where to show Spot.IM component.', 'spotim-comments' ) . '</p>';
    }

    /**
     * Import Settings Section Header
     *
     * @since 2.0.0
     *
     * @access public
     *
     * @return void
     */
    public function import_settings_section_header() {
        echo '<p>' . esc_html__( 'Import your comments from Spot.IM to WordPress.', 'spotim-comments' ) . '</p>';
    }

    /**
     * Export Settings Section Header
     *
     * @since 4.0.0
     *
     * @access public
     *
     * @return void
     */
    public function export_settings_section_header() {
        echo '<p>' . esc_html__( 'Export your comments from WordPress to Spot.IM.', 'spotim-comments' ) . '</p>';
    }

    /**
     * Register General Section
     *
     * @since 2.0.0
     *
     * @access public
     *
     * @return void
     */
    public function register_general_section() {
        add_settings_section(
            'general_settings_section',
            esc_html__( 'General Options', 'spotim-comments' ),
            array( $this, 'general_settings_section_header' ),
            $this->options->slug
        );

        $translated_spot_id_description = sprintf(
            __( 'Find your Spot ID at the <a href="%s" target="_blank">Spot.IM\'s Admin Dashboard</a> under "Features" section.' , 'spotim-comments' ),
            'https://admin.spot.im/login'
        );

        add_settings_field(
            'spot_id',
            esc_html__( 'Spot ID', 'spotim-comments' ),
            array( 'SpotIM_Form_Helper', 'text_field' ),
            $this->options->slug,
            'general_settings_section',
            array(
                'id' => 'spot_id',
                'page' => $this->options->slug,
                'description' => $translated_spot_id_description,
                'value' => $this->options->get( 'spot_id' )
            )
        );

        add_settings_field(
            'embed_method',
            esc_html__( 'Embed Method', 'spotim-comments' ),
            array( 'SpotIM_Form_Helper', 'radio_fields' ),
            $this->options->slug,
            'general_settings_section',
            array(
                'id' => 'embed_method',
                'page' => $this->options->slug,
                'fields' => array(
                    'comments' => esc_html__( 'Replace WordPress Comments', 'spotim-comments' ),
                    'content' => esc_html__( 'Insert After the Content', 'spotim-comments' ),
                ),
                'description' => esc_html__( 'Dafault methode should be replacing WordPress Comments. If it\'s not working, insert Spot.IM after the comments.', 'spotim-comments' ),
                'value' => $this->options->get( 'embed_method' )
            )
        );

        add_settings_field(
            'display_priority',
            esc_html__( 'Display Priority', 'spotim-comments' ),
            array( 'SpotIM_Form_Helper', 'number_field' ),
            $this->options->slug,
            'general_settings_section',
            array(
                'id' => 'display_priority',
                'page' => $this->options->slug,
                'description' => esc_html__( 'Set display priority among the items added by other plugin that use "the_content" filter.', 'spotim-comments' ),
                'value' => $this->options->get( 'display_priority' ),
                'min' => '0',
                'max' => '10000'
            )
        );

        add_settings_field(
            'comments_per_page',
            esc_html__( 'Comments Per Page', 'facebook-comments-plugin' ),
            array( 'SpotIM_Form_Helper', 'number_field' ),
            $this->options->slug,
            'general_settings_section',
            array(
                'id' => 'comments_per_page',
                'page' => $this->options->slug,
                'value' => $this->options->get( 'comments_per_page' ),
                'min' => 1,
                'max' => '999'
            )
        );

    }

    /**
     * Register Display Section
     *
     * @since 2.0.0
     *
     * @access public
     *
     * @return void
     */
    public function register_display_section() {
        add_settings_section(
            'display_settings_section',
            esc_html__( 'Display Options', 'spotim-comments' ),
            array( $this, 'display_settings_section_header' ),
            $this->options->slug
        );

        $post_types = get_post_types( array( 'public' => true ), 'objects' );

        if ( ! empty( $post_types ) ) {

            foreach ( $post_types as $key => $value ) {

                // Check if post type support comments
                if ( post_type_supports( $value->name, 'comments' ) ) {

                    add_settings_field(
                        "display_{$value->name}",
                        sprintf( esc_html__( 'Display on %s', 'spotim-comments' ), $value->label ),
                        array( 'SpotIM_Form_Helper', 'radio_fields' ),
                        $this->options->slug,
                        'display_settings_section',
                        array(
                            'id' => "display_{$value->name}",
                            'page' => $this->options->slug,
                            'fields' => array(
                                '0' => esc_html__( 'Disable', 'spotim-comments' ),
                                'comments' => esc_html__( 'Enable Comments', 'spotim-comments' ),
                                'comments_recirculation' => esc_html__( 'Enable Comments & Recirculation', 'spotim-comments' ),
                            ),
                            'value' => $this->options->get( "display_{$value->name}" )
                        )
                    );

                }

            }

        }

    }

    /**
     * Register Import Section
     *
     * @since 2.0.0
     *
     * @access public
     *
     * @return void
     */
    public function register_import_section() {

        add_settings_section(
            'import_settings_section',
            esc_html__( 'Import Options', 'spotim-comments' ),
            array( $this, 'import_settings_section_header' ),
            $this->options->slug
        );

        add_settings_field(
            'import_token',
            esc_html__( 'Import Token', 'spotim-comments' ),
            array( 'SpotIM_Form_Helper', 'text_field' ),
            $this->options->slug,
            'import_settings_section',
            array(
                'id' => 'import_token',
                'page' => $this->options->slug,
                'description' => sprintf ( esc_html__( 'Don\'t have a token? please send us an email to %s and get one.', 'spotim-comments' ), '<a href="mailto:support@spot.im">support@spot.im</a>' ),
                'value' => $this->options->get( 'import_token' )
            )
        );

        add_settings_field(
            'posts_per_request',
            esc_html__( 'Posts Per Request', 'spotim-comments' ),
            array( 'SpotIM_Form_Helper', 'number_field' ),
            $this->options->slug,
            'import_settings_section',
            array(
                'id' => 'posts_per_request',
                'page' => $this->options->slug,
                'description' => esc_html__( 'Amount of posts to retrieve in each request, depending on your server\'s strength.', 'spotim-comments' ),
                'value' => $this->options->get( 'posts_per_request' ),
                'min' => '0',
                'max' => '100'
            )
        );

        $schedule_fields['0'] = esc_html__( 'No', 'spotim-comments' );
        $registered_schedules = wp_get_schedules();
        if ( ! empty( $registered_schedules ) ) {
            foreach ( $registered_schedules as $key => $value ) {
                $schedule_fields[ $key ] = $value['display'];
            }
        }

        add_settings_field(
            'auto_import',
            esc_html__( 'Auto Import', 'spotim-comments' ),
            array( 'SpotIM_Form_Helper', 'radio_fields' ),
            $this->options->slug,
            'import_settings_section',
            array(
                'id' => 'auto_import',
                'page' => $this->options->slug,
                'description' => esc_html__( 'Enable auto-import and set how often should it reoccur.', 'spotim-comments' )
                    . '<br>'
                    . $this->options->get_next_cron_execution( wp_next_scheduled( 'spotim_scheduled_import' ) )
                    . ( ( empty( $this->options->get( 'spot_id' ) ) ) ? ' ' . esc_html__( 'Spot ID is missing.', 'spotim-comments' ) : '' )
                    . ( ( empty( $this->options->get( 'import_token' ) ) ) ? ' ' . esc_html__( 'Import token is missing.', 'spotim-comments' ) : '' ),
                'fields' => $schedule_fields,
                'value' => $this->options->get( 'auto_import' )
            )
        );

        add_settings_field(
            'import_button',
            esc_html__( 'Manual Import', 'spotim-comments' ),
            array( 'SpotIM_Form_Helper', 'import_button' ),
            $this->options->slug,
            'import_settings_section',
            array(
                'import_button' => array(
                    'id' => 'import_button',
                    'text' => esc_html__( 'Import Now!', 'spotim-comments' )
                ),
                'cancel_import_link' => array(
                    'id' => 'cancel_import_link',
                    'text' => esc_html__( 'Cancel', 'spotim-comments' )
                )
            )
        );

        // hidden spot id for the import js
        add_settings_field(
            'spot_id',
            null,
            array( 'SpotIM_Form_Helper', 'hidden_field' ),
            $this->options->slug,
            'import_settings_section',
            array(
                'id' => 'spot_id',
                'page' => $this->options->slug,
                'value' => $this->options->get( 'spot_id' )
            )
        );

    }

    /**
     * Register Export Section
     *
     * @since 4.0.0
     *
     * @access public
     *
     * @return void
     */
    public function register_export_section() {
        add_settings_section(
            'export_settings_section',
            esc_html__( 'Export Options', 'spotim-comments' ),
            array( $this, 'export_settings_section_header' ),
            $this->options->slug
        );

        add_settings_field(
            'plugin_secret',
            esc_html__( 'Export Token', 'spotim-comments' ),
            array( 'SpotIM_Form_Helper', 'text_field' ),
            $this->options->slug,
            'export_settings_section',
            array(
                'id' => 'plugin_secret',
                'page' => $this->options->slug,
                'description' => sprintf ( esc_html__( 'Don\'t have a token? please send us an email to %s and get one.', 'spotim-comments' ), '<a href="mailto:support@spot.im">support@spot.im</a>' ),
                'value' => $this->options->get( 'plugin_secret' )
            )
        );

    }
}
