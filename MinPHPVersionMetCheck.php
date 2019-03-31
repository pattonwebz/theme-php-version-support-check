<?php
/**
 * Class for performing PHP Version checking on theme activation.
 *
 * You just need to instantiate the class in the theme (optionally passing a
 * php version string matching the minimum version you are supporting).
 *
 * @package   PattonWebz Theme PHP Version Support Check
 * @version   1.0.0
 * @author    William Patton <will@pattonwebz.com>
 * @copyright Copyright (c) 2019, William Patton
 * @license   GPL-2.0-or-later
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace PattonWebz\Theme\PHPCheck;

/**
 * Performs a php version compair check at theme activation time.
 *
 * @since 1.0.0
 */
class Min_PHP_Version_Met_Check {

	/**
	 * Holds a minimum PHP version string which this theme chooses to support.
	 * Defaults to PHP 5.6 (which is WordPress Core min PHP recommendation) but
	 * can be overriden in the constructor.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	public $min_version = '5.6';

	/**
	 * Sets up the action which performs the version check on theme activation.
	 *
	 * @method __construct
	 * @since  1.0.0
	 * @param  string|null $min_version an optional minimum version override.
	 */
	public function __construct( $min_version = null ) {

		// If a certain value was passed in for use then update the property.
		if ( $min_version ) {
			// Always using a version STRING for consistecy reasons.
			$this->min_version = (string) $min_version;
		}

		/**
		 * Immediately after theme switch we we want to check php version and
		 * revert to previously active theme if version is below our minimum.
		 */
		add_action( 'after_switch_theme', array( $this, 'test_for_min_php' ) );
	}

	/**
	 * Switches back to the previous theme if the minimum PHP version is not met.
	 *
	 * @method test_for_min_php
	 * @since  1.0.0
	 * @return bool|void
	 */
	public function test_for_min_php() {

		// Compare versions.
		if ( version_compare( PHP_VERSION, PATTONWEBZ_MIN_PHP_VERSION, '<' ) ) {
			// Site doesn't meet themes min php requirements, add notice...
			add_action( 'admin_notices', array( $this, 'min_php_not_met_notice' ) );
			// ...and switch back to previous theme.
			switch_theme( get_option( 'theme_switched' ) );
			return false;

		};
	}

	/**
	 * A notice that can be displayed if the minimum PHP version is not met.
	 *
	 * @method pattonwebz_min_php_not_met_notice
	 * @since  1.0.0
	 */
	public function min_php_not_met_notice() {
		?>
		<div class="notice notice-error is_dismissable">
			<p>
				<?php esc_html_e( 'You need to update your PHP version to run this theme.', 'pattonwebz' ); ?> <br />
				<?php
				printf(
					/* translators: 1 is the current PHP version string, 2 is the minimum supported php version string of the theme */
					esc_html__( 'Actual version is: %1$s, required version is: %2$s.', 'pattonwebz' ),
					PHP_VERSION,
					esc_html( $this->min_version )
				); // phpcs: XSS ok.
				?>
			</p>
		</div>
		<?php
	}

}
