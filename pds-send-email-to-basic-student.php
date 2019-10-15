<?php
/**
 * Plugin Name: Peaceful Dragon School - Send Email to Basic Student
 * Description: When a User is switched to the Basic Student Role, this will automatically send them an email
 * Version: 0.1.0
 * Text Domain: pds-send-email-to-basic-student
 * Author: Real Big Marketing
 * Author URI: https://realbigmarketing.com/
 * Contributors: d4mation
 * GitHub Plugin URI: realbig/pds-send-email-to-basic-student
 * GitHub Branch: master
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'PDS_Send_Email_to_Basic_Student' ) ) {

	/**
	 * Main PDS_Send_Email_to_Basic_Student class
	 *
	 * @since	  {{VERSION}}
	 */
	final class PDS_Send_Email_to_Basic_Student {
		
		/**
		 * @var			array $plugin_data Holds Plugin Header Info
		 * @since		{{VERSION}}
		 */
		public $plugin_data;
		
		/**
		 * @var			array $admin_errors Stores all our Admin Errors to fire at once
		 * @since		{{VERSION}}
		 */
		private $admin_errors;

		/**
		 * Get active instance
		 *
		 * @access	  public
		 * @since	  {{VERSION}}
		 * @return	  object self::$instance The one true PDS_Send_Email_to_Basic_Student
		 */
		public static function instance() {
			
			static $instance = null;
			
			if ( null === $instance ) {
				$instance = new static();
			}
			
			return $instance;

		}
		
		protected function __construct() {
			
			$this->setup_constants();
			$this->load_textdomain();
			
			if ( version_compare( get_bloginfo( 'version' ), '4.4' ) < 0 ) {
				
				$this->admin_errors[] = sprintf( _x( '%s requires v%s of %sWordPress%s or higher to be installed!', 'First string is the plugin name, followed by the required WordPress version and then the anchor tag for a link to the Update screen.', 'pds-send-email-to-basic-student' ), '<strong>' . $this->plugin_data['Name'] . '</strong>', '4.4', '<a href="' . admin_url( 'update-core.php' ) . '"><strong>', '</strong></a>' );
				
				if ( ! has_action( 'admin_notices', array( $this, 'admin_errors' ) ) ) {
					add_action( 'admin_notices', array( $this, 'admin_errors' ) );
				}
				
				return false;
				
			}
			
			$this->require_necessities();
			
			// Register our CSS/JS for the whole plugin
			add_action( 'init', array( $this, 'register_scripts' ) );

			add_action( 'set_user_role', array( $this, 'set_user_role' ), 10, 3 );
			
		}

		/**
		 * Setup plugin constants
		 *
		 * @access	  private
		 * @since	  {{VERSION}}
		 * @return	  void
		 */
		private function setup_constants() {
			
			// WP Loads things so weird. I really want this function.
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . '/wp-admin/includes/plugin.php';
			}
			
			// Only call this once, accessible always
			$this->plugin_data = get_plugin_data( __FILE__ );

			if ( ! defined( 'PDS_Send_Email_to_Basic_Student_VER' ) ) {
				// Plugin version
				define( 'PDS_Send_Email_to_Basic_Student_VER', $this->plugin_data['Version'] );
			}

			if ( ! defined( 'PDS_Send_Email_to_Basic_Student_DIR' ) ) {
				// Plugin path
				define( 'PDS_Send_Email_to_Basic_Student_DIR', plugin_dir_path( __FILE__ ) );
			}

			if ( ! defined( 'PDS_Send_Email_to_Basic_Student_URL' ) ) {
				// Plugin URL
				define( 'PDS_Send_Email_to_Basic_Student_URL', plugin_dir_url( __FILE__ ) );
			}
			
			if ( ! defined( 'PDS_Send_Email_to_Basic_Student_FILE' ) ) {
				// Plugin File
				define( 'PDS_Send_Email_to_Basic_Student_FILE', __FILE__ );
			}

		}

		/**
		 * Internationalization
		 *
		 * @access	  private 
		 * @since	  {{VERSION}}
		 * @return	  void
		 */
		private function load_textdomain() {

			// Set filter for language directory
			$lang_dir = PDS_Send_Email_to_Basic_Student_DIR . '/languages/';
			$lang_dir = apply_filters( 'pds_send_email_to_basic_student_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), 'pds-send-email-to-basic-student' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'pds-send-email-to-basic-student', $locale );

			// Setup paths to current locale file
			$mofile_local   = $lang_dir . $mofile;
			$mofile_global  = WP_LANG_DIR . '/pds-send-email-to-basic-student/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/pds-send-email-to-basic-student/ folder
				// This way translations can be overridden via the Theme/Child Theme
				load_textdomain( 'pds-send-email-to-basic-student', $mofile_global );
			}
			else if ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/pds-send-email-to-basic-student/languages/ folder
				load_textdomain( 'pds-send-email-to-basic-student', $mofile_local );
			}
			else {
				// Load the default language files
				load_plugin_textdomain( 'pds-send-email-to-basic-student', false, $lang_dir );
			}

		}
		
		/**
		 * Include different aspects of the Plugin
		 * 
		 * @access	  private
		 * @since	  {{VERSION}}
		 * @return	  void
		 */
		private function require_necessities() {
			
		}
		
		/**
		 * Show admin errors.
		 * 
		 * @access	  public
		 * @since	  {{VERSION}}
		 * @return	  HTML
		 */
		public function admin_errors() {
			?>
			<div class="error">
				<?php foreach ( $this->admin_errors as $notice ) : ?>
					<p>
						<?php echo $notice; ?>
					</p>
				<?php endforeach; ?>
			</div>
			<?php
		}
		
		/**
		 * Register our CSS/JS to use later
		 * 
		 * @access	  public
		 * @since	  {{VERSION}}
		 * @return	  void
		 */
		public function register_scripts() {
			
			wp_register_style(
				'pds-send-email-to-basic-student',
				PDS_Send_Email_to_Basic_Student_URL . 'dist/assets/css/app.css',
				null,
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : PDS_Send_Email_to_Basic_Student_VER
			);
			
			wp_register_script(
				'pds-send-email-to-basic-student',
				PDS_Send_Email_to_Basic_Student_URL . 'dist/assets/js/app.js',
				array( 'jquery' ),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : PDS_Send_Email_to_Basic_Student_VER,
				true
			);
			
			wp_localize_script( 
				'pds-send-email-to-basic-student',
				'pDSSendEmailtoBasicStudent',
				apply_filters( 'pds_send_email_to_basic_student_localize_script', array() )
			);
			
			wp_register_style(
				'pds-send-email-to-basic-student-admin',
				PDS_Send_Email_to_Basic_Student_URL . 'dist/assets/css/admin.css',
				null,
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : PDS_Send_Email_to_Basic_Student_VER
			);
			
			wp_register_script(
				'pds-send-email-to-basic-student-admin',
				PDS_Send_Email_to_Basic_Student_URL . 'dist/assets/js/admin.js',
				array( 'jquery' ),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : PDS_Send_Email_to_Basic_Student_VER,
				true
			);
			
			wp_localize_script( 
				'pds-send-email-to-basic-student-admin',
				'pDSSendEmailtoBasicStudent',
				apply_filters( 'pds_send_email_to_basic_student_localize_admin_script', array() )
			);
			
		}

		/**
		 * Send a welcome email to Basic Students once they're granted that Role
		 * This will fire off no matter how they are made a Basic Student
		 *
		 * @param   integer  $user_id    WP_User ID
		 * @param   string   $new_role   User Role they were granted/switched to
		 * @param   array    $old_roles  User Role(s) they were previously
		 *
		 * @access	public
		 * @since	{{VERSION}}
		 * @return  void
		 */
		public function set_user_role( $user_id, $new_role, $old_roles ) {

			if ( $new_role !== 'basic_student' ) return;

			$user_data = get_userdata( $user_id );

			wp_mail(
				$user_data->user_email,
				sprintf( __( 'Welcome to %s!', 'pds-send-email-to-basic-student' ), trim( get_bloginfo( 'name' ) ) ),
				__( 'Some message', 'pds-send-email-to-basic-student' ),
				'',
				array(
					PDS_Send_Email_to_Basic_Student_DIR . 'welcome.pdf',
				)
			);

		}
		
	}
	
} // End Class Exists Check

/**
 * The main function responsible for returning the one true PDS_Send_Email_to_Basic_Student
 * instance to functions everywhere
 *
 * @since	  {{VERSION}}
 * @return	  \PDS_Send_Email_to_Basic_Student The one true PDS_Send_Email_to_Basic_Student
 */
add_action( 'plugins_loaded', 'pds_send_email_to_basic_student_load' );
function pds_send_email_to_basic_student_load() {

	require_once __DIR__ . '/core/pds-send-email-to-basic-student-functions.php';
	PDSSENDEMAILTOBASICSTUDENT();

}