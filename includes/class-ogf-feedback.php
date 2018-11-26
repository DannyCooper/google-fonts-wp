<?php
/**
 * Plugin review class.
 * Prompts users to give a review of the plugin on WordPress.org after a period of usage.
 *
 * Heavily based on code by Rhys Wynne
 * https://winwar.co.uk/2014/10/ask-wordpress-plugin-reviews-week/
 *
 * @package   olympus-google-fonts
 * @copyright Copyright (c) 2018, Danny Cooper
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'OGF_Feedback' ) ) :
	/**
	 * The feedback.
	 */
	class OGF_Feedback {

		/**
		 * Slug.
		 *
		 * @var string $slug
		 */
		private $slug;
		/**
		 * Name.
		 *
		 * @var string $name
		 */
		private $name;
		/**
		 * Time limit.
		 *
		 * @var string $time_limit
		 */
		private $time_limit;
		/**
		 * No Bug Option.
		 *
		 * @var string $nobug_option
		 */
		public $nobug_option;
		/**
		 * Activation Date Option.
		 *
		 * @var string $date_option
		 */
		public $date_option;
		/**
		 * Class constructor.
		 *
		 * @param string $args Arguments.
		 */
		public function __construct( $args ) {
			$this->slug         = $args['slug'];
			$this->name         = $args['name'];
			$this->date_option  = $this->slug . '_activation_date';
			$this->nobug_option = $this->slug . '_no_bug';
			if ( isset( $args['time_limit'] ) ) {
				$this->time_limit = $args['time_limit'];
			} else {
				$this->time_limit = WEEK_IN_SECONDS;
			}
			// Add actions.
			add_action( 'admin_init', array( $this, 'check_installation_date' ) );
			add_action( 'admin_init', array( $this, 'set_no_bug' ), 5 );
		}
		/**
		 * Seconds to words.
		 *
		 * @param string $seconds Seconds in time.
		 */
		public function seconds_to_words( $seconds ) {
			// Get the years.
			$years = ( intval( $seconds ) / YEAR_IN_SECONDS ) % 100;
			if ( $years > 1 ) {
				/* translators: Number of years */
				return sprintf( __( '%s years', 'olympus-google-fonts' ), $years );
			} elseif ( $years > 0 ) {
				return __( 'a year', 'olympus-google-fonts' );
			}
			// Get the weeks.
			$weeks = ( intval( $seconds ) / WEEK_IN_SECONDS ) % 52;
			if ( $weeks > 1 ) {
				/* translators: Number of weeks */
				return sprintf( __( '%s weeks', 'olympus-google-fonts' ), $weeks );
			} elseif ( $weeks > 0 ) {
				return __( 'a week', 'olympus-google-fonts' );
			}
			// Get the days.
			$days = ( intval( $seconds ) / DAY_IN_SECONDS ) % 7;
			if ( $days > 1 ) {
				/* translators: Number of days */
				return sprintf( __( '%s days', 'olympus-google-fonts' ), $days );
			} elseif ( $days > 0 ) {
				return __( 'a day', 'olympus-google-fonts' );
			}
			// Get the hours.
			$hours = ( intval( $seconds ) / HOUR_IN_SECONDS ) % 24;
			if ( $hours > 1 ) {
				/* translators: Number of hours */
				return sprintf( __( '%s hours', 'olympus-google-fonts' ), $hours );
			} elseif ( $hours > 0 ) {
				return __( 'an hour', 'olympus-google-fonts' );
			}
			// Get the minutes.
			$minutes = ( intval( $seconds ) / MINUTE_IN_SECONDS ) % 60;
			if ( $minutes > 1 ) {
				/* translators: Number of minutes */
				return sprintf( __( '%s minutes', 'olympus-google-fonts' ), $minutes );
			} elseif ( $minutes > 0 ) {
				return __( 'a minute', 'olympus-google-fonts' );
			}
			// Get the seconds.
			$seconds = intval( $seconds ) % 60;
			if ( $seconds > 1 ) {
				/* translators: Number of seconds */
				return sprintf( __( '%s seconds', 'olympus-google-fonts' ), $seconds );
			} elseif ( $seconds > 0 ) {
				return __( 'a second', 'olympus-google-fonts' );
			}
		}
		/**
		 * Check date on admin initiation and add to admin notice if it was more than the time limit.
		 */
		public function check_installation_date() {
			if ( ! get_site_option( $this->nobug_option ) || false === get_site_option( $this->nobug_option ) ) {
				add_site_option( $this->date_option, time() );
				// Retrieve the activation date.
				$install_date = get_site_option( $this->date_option );
				// If difference between install date and now is greater than time limit, then display notice.
				if ( ( time() - $install_date ) > $this->time_limit ) {
					add_action( 'admin_notices', array( $this, 'display_admin_notice' ) );
				}
			}
		}
		/**
		 * Display the admin notice.
		 */
		public function display_admin_notice() {

			$no_bug_url = wp_nonce_url( admin_url( '?' . $this->nobug_option . '=true' ), 'ogf-feedback-nounce' );
			$time       = $this->seconds_to_words( time() - get_site_option( $this->date_option ) );
			?>

			<style>
			.notice.ogf-notice {
				border-left-color: #008ec2 !important;
				padding: 20px;
			}
			.rtl .notice.ogf-notice {
				border-right-color: #008ec2 !important;
			}
			.notice.notice.ogf-notice .ogf-notice-inner {
				display: table;
				width: 100%;
			}
			.notice.ogf-notice .ogf-notice-inner .ogf-notice-icon,
			.notice.ogf-notice .ogf-notice-inner .ogf-notice-content,
			.notice.ogf-notice .ogf-notice-inner .ogf-install-now {
				display: table-cell;
				vertical-align: middle;
			}
			.notice.ogf-notice .ogf-notice-icon {
				color: #509ed2;
				font-size: 50px;
				width: 60px;
			}
			.notice.ogf-notice .ogf-notice-icon img {
				width: 64px;
			}
			.notice.ogf-notice .ogf-notice-content {
				padding: 0 40px 0 20px;
			}
			.notice.ogf-notice p {
				padding: 0;
				margin: 0;
				max-width: 640px;
			}
			.notice.ogf-notice h3 {
				margin: 0 0 5px;
			}
			.notice.ogf-notice .ogf-install-now {
				text-align: center;
			}
			.notice.ogf-notice .ogf-install-now .ogf-install-button {
				padding: 6px 50px;
				height: auto;
				line-height: 20px;
			}
			.notice.ogf-notice a.no-thanks {
				display: block;
				margin-top: 10px;
				color: #72777c;
				text-decoration: none;
			}
			.notice.ogf-notice a.no-thanks:hover {
				color: #444;
			}
			@media (max-width: 767px) {
				.notice.notice.ogf-notice .ogf-notice-inner {
					display: block;
				}
				.notice.ogf-notice {
					padding: 20px !important;
				}
				.notice.ogf-noticee .ogf-notice-inner {
					display: block;
				}
				.notice.ogf-notice .ogf-notice-inner .ogf-notice-content {
					display: block;
					padding: 0;
				}
				.notice.ogf-notice .ogf-notice-inner .ogf-notice-icon {
					display: none;
				}
				.notice.ogf-notice .ogf-notice-inner .ogf-install-now {
					margin-top: 20px;
					display: block;
					text-align: left;
				}
				.notice.ogf-notice .ogf-notice-inner .no-thanks {
					display: inline-block;
					margin-left: 15px;
				}
			}
			</style>
			<div class="notice updated ogf-notice">
				<div class="ogf-notice-inner">
					<div class="ogf-notice-icon">
						<img src="https://ps.w.org/olympus-google-fonts/assets/icon-256x256.jpg" alt="<?php echo esc_attr__( 'Olympus Google Fonts WordPress Plugin', 'olympus-google-fonts' ); ?>" />
					</div>
					<div class="ogf-notice-content">
						<h3><?php echo esc_html__( 'Are you enjoying using Google Fonts?', 'olympus-google-fonts' ); ?></h3>
						<p>
							<?php
							/* translators: 1. Name, 2. Time */
							printf( __( 'You have been using <strong>%1$s</strong> for %2$s now! Could you please do me a BIG favor and give it a 5-star rating on WordPress to help us spread the word and boost our motivation?', 'olympus-google-fonts' ), esc_html( $this->name ), esc_html( $time ) );
							?>
						</p>
					</div>
					<div class="ogf-install-now">
			<?php printf( '<a href="%1$s" class="button button-primary ogf-install-button" target="_blank">%2$s</a>', esc_url( 'https://wordpress.org/support/view/plugin-reviews/olympus-google-fonts#new-post' ), esc_html__( 'Leave a Review', 'olympus-google-fonts' ) ); ?>
						<a href="<?php echo esc_url( $no_bug_url ); ?>" class="no-thanks"><?php echo esc_html__( 'No thanks / I already have', 'olympus-google-fonts' ); ?></a>
					</div>
				</div>
			</div>
			<?php
		}
		/**
		 * Set the plugin to no longer bug users if user asks not to be.
		 */
		public function set_no_bug() {
			// Bail out if not on correct page.
			if ( ! isset( $_GET['_wpnonce'] ) || ( ! wp_verify_nonce( $_GET['_wpnonce'], 'ogf-feedback-nounce' ) || ! is_admin() || ! isset( $_GET[ $this->nobug_option ] ) || ! current_user_can( 'manage_options' ) ) ) {
				return;
			}
			add_site_option( $this->nobug_option, true );
		}
	}
endif;

/*
* Instantiate the OGF_Feedback class.
*/
new OGF_Feedback(
	array(
		'slug'       => 'ogf',
		'name'       => __( 'Google Fonts for WordPress', 'olympus-google-fonts' ),
		'time_limit' => WEEK_IN_SECONDS,
	)
);