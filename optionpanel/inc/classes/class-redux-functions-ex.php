<?php
/**
 * Redux Framework Private Extended Functions Container Class
 *
 * @class       Redux_Functions_Ex
 * @since       3.0.0
 * @package     Redux_Framework/Classes
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Don't duplicate me!
if ( ! class_exists( 'Redux_Functions_Ex', false ) ) {

	/**
	 * Redux Functions Class
	 * Class of useful functions that can/should be shared among all Redux files.
	 *
	 * @since       3.0.0
	 */
	class Redux_Functions_Ex {

		/**
		 * What is this for?
		 *
		 * @var array
		 */
		public static $args;

		/**
		 * Parses the string into variables without the max_input_vars limitation.
		 *
		 * @since   3.5.7.11
		 *
		 * @param     string $string String of data.
		 *
		 * @return  array|false $result
		 * @author  harunbasic
		 * @access  private
		 */
		public static function parse_str( $string ) {
			if ( '' === $string ) {
				return false;
			}

			$result = array();
			$pairs  = explode( '&', $string );

			foreach ( $pairs as $key => $pair ) {
				// use the original parse_str() on each element.
				parse_str( $pair, $params );

				$k = key( $params );

				if ( ! isset( $result[ $k ] ) ) {
					$result += $params;
				} elseif ( is_array( $result[ $k ] ) && is_array( $params[ $k ] ) ) {
					$result[ $k ] = self::array_merge_recursive_distinct( $result[ $k ], $params[ $k ] );
				}
			}

			return $result;
		}

		/**
		 * Merge arrays without converting values with duplicate keys to arrays as array_merge_recursive does.
		 * As seen here http://php.net/manual/en/function.array-merge-recursive.php#92195
		 *
		 * @since   3.5.7.11
		 *
		 * @param     array $array1 array one.
		 * @param     array $array2 array two.
		 *
		 * @return  array $merged
		 * @author  harunbasic
		 * @access  private
		 */
		public static function array_merge_recursive_distinct( array $array1, array $array2 ) {
			$merged = $array1;

			foreach ( $array2 as $key => $value ) {

				if ( is_array( $value ) && isset( $merged[ $key ] ) && is_array( $merged[ $key ] ) ) {
					$merged[ $key ] = self::array_merge_recursive_distinct( $merged[ $key ], $value );
				} elseif ( is_numeric( $key ) && isset( $merged[ $key ] ) ) {
					$merged[] = $value;
				} else {
					$merged[ $key ] = $value;
				}
			}

			return $merged;
		}

		/**
		 * Records calling function.
		 *
		 * @param     string $opt_name Panel opt_name.
		 */
		public static function record_caller( $opt_name = '' ) {
			global $pagenow;

			// phpcs:ignore WordPress.Security.NonceVerification
			if ( ! ( 'tools.php' === $pagenow && isset( $_GET['page'] ) && ( 'redux-framework' === $_GET['page'] || 'health-check' === $_GET['page'] ) ) ) {
				return;
			}

			// phpcs:ignore WordPress.PHP.DevelopmentFunctions
			$caller = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 2 )[1]['file'];

			if ( ! empty( $caller ) && ! empty( $opt_name ) && class_exists( 'Redux_Core' ) ) {
				if ( ! isset( Redux_Core::$callers[ $opt_name ] ) ) {
					Redux_Core::$callers[ $opt_name ] = array();
				}

				if ( strpos( $caller, 'class-redux-' ) !== false || strpos( $caller, 'redux-core/framework.php' ) ) {
					return;
				}

				if ( ! in_array( $caller, Redux_Core::$callers[ $opt_name ], true ) ) {
					Redux_Core::$callers[ $opt_name ][] = $caller;
				}

				if ( ! empty( self::$args[ $opt_name ]['callers'] ) && ! in_array( $caller, self::$args[ $opt_name ]['callers'], true ) ) {
					self::$args[ $opt_name ]['callers'][] = $caller;
				}
			}
		}

		/**
		 * Normalize path.
		 *
		 * @param     string $path Path to normalize.
		 *
		 * @return mixed|null|string|string[]
		 */
		public static function wp_normalize_path( $path = '' ) {
			if ( function_exists( 'wp_normalize_path' ) ) {
				$path = wp_normalize_path( $path );
			} else {
				// Shim for pre WP 3.9.
				$path = str_replace( '\\', '/', $path );
				$path = preg_replace( '|(?<=.)/+|', '/', $path );

				if ( ':' === substr( $path, 1, 1 ) ) {
					$path = ucfirst( $path );
				}
			}

			return $path;
		}

		/**
		 * Action to add generator tag to page HEAD.
		 */
		public static function generator() {
			add_action( 'wp_head', array( 'Redux_Functions_Ex', 'meta_tag' ) );
		}


		/**
		 * Callback for wp_head hook to add meta tag.
		 */
		public static function meta_tag() {
			echo '<meta name="framework" content="Redux ' . esc_html( Redux_Core::$version ) . '" />';
		}

		/**
		 * Run URL through a ssl check.
		 *
		 * @param     string $url URL to check.
		 *
		 * @return string
		 */
		public static function verify_url_protocol( $url ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			$protocol = ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] || ( ! empty( $_SERVER['SERVER_PORT'] ) && 443 === $_SERVER['SERVER_PORT'] ) ? 'https://' : 'http://';
			if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
				$new_protocol = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) ) . '://'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
				if ( 'http://' === $protocol && $new_protocol !== $protocol && false === strpos( $url, $new_protocol ) ) {
					$url = str_replace( $protocol, $new_protocol, $url );
				}
			}
			return $url;
		}

		/**
		 * Check s.
		 *
		 * @access public
		 * @since 4.0.0
		 * @return bool
		 */
		public static function s() {
			if ( ! empty( get_option( 'redux_p' . 'ro_lic' . 'ense_key', false ) ) ) { // phpcs:ignore Generic.Strings.UnnecessaryStringConcat.Found
				$s = get_option( 'redux_p' . 'ro_l' . 'icense_status', false ); // phpcs:ignore Generic.Strings.UnnecessaryStringConcat.Found
				if ( ! empty( $s ) && in_array( $s, array( 'valid', 'site_inactive' ), true ) ) {
					return true;
				}
			}
			return false;
		}

		/**
		 * Is file in theme.
		 *
		 * @param     string $file File to check.
		 *
		 * @return bool
		 */
		public static function file_in_theme( $file ) {
			if ( strpos( dirname( $file ), get_template_directory() ) !== false ) {
				return true;
			} elseif ( strpos( dirname( $file ), get_stylesheet_directory() ) !== false ) {
				return true;
			}
			return false;
		}

		/**
		 * Is Redux embedded inside a plugin.
		 *
		 * @param     string $file File to check.
		 *
		 * @return array|bool
		 */
		public static function is_inside_plugin( $file ) {
			$file            = self::wp_normalize_path( $file );
			$plugin_basename = self::wp_normalize_path( plugin_basename( $file ) );

			if ( self::file_in_theme( $file ) ) {
				return false;
			}

			if ( $plugin_basename !== $file ) {
				$slug = explode( '/', $plugin_basename );
				$slug = $slug[0];

				$data = array(
					'slug'      => $slug,
					'basename'  => $plugin_basename,
					'path'      => $file,
					'url'       => self::verify_url_protocol( plugins_url( $plugin_basename ) ),
					'real_path' => self::wp_normalize_path( dirname( realpath( $file ) ) ),
				);

				return $data;
			}

			return false;
		}

		/**
		 * Is Redux embedded in a theme.
		 *
		 * @param     string $file File to check.
		 *
		 * @return array|bool
		 */
		public static function is_inside_theme( $file = '' ) {

			if ( ! self::file_in_theme( $file ) ) {
				return false;
			}

			$theme_paths = array(
				self::wp_normalize_path( get_template_directory() )   => get_template_directory_uri(),
				// parent.
				self::wp_normalize_path( get_stylesheet_directory() ) => get_stylesheet_directory_uri(),
				// child.
			);

			$theme_paths = array_unique( $theme_paths );
			$file_path   = self::wp_normalize_path( $file );

			$filename = explode( '\\', $file );

			end( $filename );

			$filename = prev( $filename );

			foreach ( $theme_paths as $theme_path => $url ) {
				$real_path = self::wp_normalize_path( realpath( $theme_path ) );

				if ( empty( $real_path ) ) {
					continue;
				}

				if ( ! empty( $real_path ) && strpos( $file_path, $real_path ) !== false ) {
					$slug             = explode( '/', $theme_path );
					$slug             = end( $slug );
					$relative_path    = explode( $slug . '/', dirname( $file_path ) );
					$relative_path    = $relative_path[1];
					$data             = array(
						'slug'      => $slug,
						'path'      => trailingslashit( trailingslashit( $theme_path ) . $relative_path ) . $filename,
						'real_path' => trailingslashit( trailingslashit( $real_path ) . $relative_path ) . $filename,
						'url'       => self::verify_url_protocol( trailingslashit( trailingslashit( $url ) . $relative_path ) . $filename ),
						'basename'  => trailingslashit( $slug ) . trailingslashit( $relative_path ) . $filename,
					);
					$data['realpath'] = $data['real_path'];  // Shim for old extensions.

					if ( count( $theme_paths ) > 1 ) {
						$key = array_search( $theme_path, $theme_paths, true );

						if ( false !== $key ) {
							unset( $theme_paths[ $key ] );
						}

						$data['parent_slug'] = end( explode( '/', end( $theme_paths ) ) );
						$data['parent_slug'] = end( explode( '/', end( $theme_paths ) ) );
					}

					return $data;
				}
			}

			return false;
		}

		/**
		 * Used to fix 3.x and 4 compatibility for extensions
		 *
		 * @param     object $parent The extension parent object.
		 * @param     string $path - Path of the file.
		 * @param     string $ext_class - Extension class name.
		 * @param     string $new_class_name - New dynamic class name.
		 * @param     string $name extension name.
		 *
		 * @return object - Extended field class.
		 */
		public static function extension_compatibility( $parent, $path, $ext_class, $new_class_name, $name ) {
			if ( empty( $new_class_name ) ) {
				return;
			}
			$upload_dir = ReduxFramework::$_upload_dir . '/extension_compatibility/';
			if ( ! file_exists( $upload_dir . $ext_class . '.php' ) ) {
				if ( ! is_dir( $upload_dir ) ) {
					$parent->filesystem->mkdir( $upload_dir );
					$parent->filesystem->put_contents( $upload_dir . 'index.php', '<?php // Silence is golden.' );
				}
				if ( ! class_exists( $ext_class ) ) {
					require_once $path;
				}
				if ( ! file_exists( $upload_dir . $new_class_name . '.php' ) ) {
					$class_file = '<?php' . PHP_EOL . PHP_EOL .
						'class {{ext_class}} extends Redux_Extension_Abstract {' . PHP_EOL .
						'    private $c;' . PHP_EOL .
						'    public function __construct( $parent, $path, $ext_class ) {' . PHP_EOL .
						'        $this->c = $parent->extensions[\'' . $name . '\'];' . PHP_EOL .
						'        // Add all the params of the Abstract to this instance.' . PHP_EOL .
						'        foreach( get_object_vars( $this->c ) as $key => $value ) {' . PHP_EOL .
						'            $this->$key = $value;' . PHP_EOL .
						'        }' . PHP_EOL .
						'        parent::__construct( $parent, $path );' . PHP_EOL .
						'    }' . PHP_EOL .
						'    // fake "extends Redux_Extension_Abstract\" using magic function' . PHP_EOL .
						'    public function __call( $method, $args ) {' . PHP_EOL .
						'        return call_user_func_array( array( $this->c, $method ), $args );' . PHP_EOL .
						'    }' . PHP_EOL .
						'}' . PHP_EOL;
					$template   = str_replace( '{{ext_class}}', $new_class_name, $class_file );
					$parent->filesystem->put_contents( $upload_dir . $new_class_name . '.php', $template );
				}
				if ( file_exists( $upload_dir . $new_class_name . '.php' ) ) {
					if ( ! class_exists( $new_class_name ) ) {
						require_once $upload_dir . $new_class_name . '.php';
					}
					if ( class_exists( $new_class_name ) ) {
						return new $new_class_name( $parent, $path, $ext_class );
					}
				} else {
					// Why doesn't the file exist? Gah permissions.
					return;
				}
			}
		}

		/**
		 * Used to deep merge two arrays.
		 *
		 * @param     array $a First array to deep merge.
		 * @param     array $b Second array to deep merge.
		 *
		 * @return    array - Deep merge of the two arrays.
		 */
		public static function nested_wp_parse_args( &$a, $b ) {
			$a      = (array) $a;
			$b      = (array) $b;
			$result = $b;
			foreach ( $a as $k => &$v ) {
				if ( is_array( $v ) && isset( $result[ $k ] ) ) {
					$result[ $k ] = self::nested_wp_parse_args( $v, $result[ $k ] );
				} else {
					$result[ $k ] = $v;
				}
			}

			return $result;
		}

		/**
		 * AJAX callback key
		 */
		public static function hash_key() {
			$key  = '';
			$key .= defined( 'AUTH_KEY' ) ? AUTH_KEY : get_site_url();
			$key .= defined( 'SECURE_AUTH_KEY' ) ? SECURE_AUTH_KEY : '';

			return $key;
		}

		/**
		 * Check if Redux is activated.
		 *
		 * @access public
		 * @since 4.0.0
		 */
		public static function activated() {
			if ( Redux_Core::$insights->tracking_allowed() ) {
				return true;
			}
			return false;
		}

		/**
		 * Set Redux to activated.
		 *
		 * @access public
		 * @since 4.0.0
		 */
		public static function set_activated() {
			Redux_Core::$insights->optin();
		}

		/**
		 * Set Redux to deactivated.
		 *
		 * @access public
		 * @since 4.0.0
		 */
		public static function set_deactivated() {
			Redux_Core::$insights->optout();
		}

		/**
		 * Register a class path to be autoloaded.
		 *
		 * Registers a namespace to be autoloaded from a given path, using the
		 * WordPress/HM-style filenames (`class-{name}.php`).
		 *
		 * @link https://engineering.hmn.md/standards/style/php/#file-naming
		 *
		 * @param string $prefix Prefix to autoload from.
		 * @param string $path Path to validate.
		 */
		public static function register_class_path( $prefix = '', $path = '' ) {
			if ( ! class_exists( 'Redux_Autoloader' ) ) {
				require_once Redux_Path::get_path( '/inc/classes/class-redux-autoloader.php' );
			}
			$loader = new Redux_Autoloader( $prefix, $path );
			spl_autoload_register( array( $loader, 'load' ) );
		}

		/**
		 * Check if a string starts with a string.
		 *
		 * @param string $haystack Full string.
		 * @param string $needle String to check if it starts with.
		 * @return string
		 */
		public static function string_starts_with( $haystack, $needle ) {
			$length = strlen( $needle );
			return substr( $haystack, 0, $length ) === $needle;
		}

		/**
		 * Check if a string ends with a string.
		 *
		 * @param string $haystack Full string.
		 * @param string $needle String to check if it starts with.
		 * @return string
		 */
		public static function string_ends_with( $haystack, $needle ) {
			$length = strlen( $needle );
			if ( ! $length ) {
				return true;
			}
			return substr( $haystack, -$length ) === $needle;
		}

		/**
		 * Get the url where the Admin Columns website is hosted
		 *
		 * @param string $path Path to add to url.
		 *
		 * @return string
		 */
		private static function get_site_url( $path = '' ) {
			$url = 'https://redux.io';

			if ( ! empty( $path ) ) {
				$url .= '/' . trim( $path, '/' ) . '/';
			}

			return $url;
		}

		/**
		 * Url with utm tags
		 *
		 * @param string $path Path on site.
		 * @param string $utm_medium Medium var.
		 * @param string $utm_content Content var.
		 * @param bool   $utm_campaign Campaign var.
		 *
		 * @return string
		 */
		public static function get_site_utm_url( $path, $utm_medium, $utm_content = null, $utm_campaign = false ) {
			$url = self::get_site_url( $path );

			if ( ! $utm_campaign ) {
				$utm_campaign = 'plugin-installation';
			}

			$args = array(
				// Referrer: plugin.
				'utm_source'   => 'plugin-installation',

				// Specific promotions or sales.
				'utm_campaign' => $utm_campaign,

				// Marketing medium: banner, documentation or email.
				'utm_medium'   => $utm_medium,

				// Used for differentiation of medium.
				'utm_content'  => $utm_content,
			);

			$args = array_map( 'sanitize_key', array_filter( $args ) );

			return add_query_arg( $args, $url );
		}

	}
}
