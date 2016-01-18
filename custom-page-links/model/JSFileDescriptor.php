<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 11-01-16
 * Time: 19:46
 */

namespace dk\mholt\CustomPageLinks\model;


use dk\mholt\CustomPageLinks\CustomPageLinks;

class JSFileDescriptor {
	/**
	 * @var string
	 */
	private $filename;

	/**
    * @var string
    */
	private $pluginRelativePath;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $extension;

	public function __construct( $filename ) {
		$this->filename  = $filename;
		$fullLength      = strlen( $filename );
		$lastSlash       = strrpos( $filename, DIRECTORY_SEPARATOR );
		$lastDot         = strrpos( $filename, '.' );
		$this->extension = substr( $filename, $lastDot );
		$extensionLength = strlen( $this->extension );
		$startPosition   = $lastSlash + 1;
		$this->name      = substr( $filename, $startPosition, $fullLength - ( $startPosition + $extensionLength ) );

		$pluginPath               = CustomPageLinks::$PLUGIN_PATH;
		$dirPath                  = plugin_dir_path( $this->filename );
		$filePath                 = $dirPath . DIRECTORY_SEPARATOR . $this->name . $this->extension;
		$this->pluginRelativePath = wp_normalize_path( str_replace( $pluginPath, "", $filePath ) );
	}

	public function getFilename() {
		return $this->filename;
	}

	public function getName() {
		return $this->name;
	}

	public function getExtension() {
		return $this->extension;
	}

	public function getDependencies() {
		$meta = $this->getMeta();

		if ( ! array_key_exists( 'depends', $meta ) ) {
			return [ ];
		}

		return json_decode( $meta['depends'] );
	}

	/**
	 * @var string
	 */
	private $handle;

	public function getHandle() {
		if ( empty( $this->handle ) ) {
			$name         = $this->name;
			$this->handle = "cpl-${name}";
		}

		return $this->handle;
	}

	public function getPluginRelativePath() {
		return $this->pluginRelativePath;
	}

	public function getSourcePath() {
		$path   = $this->getPluginRelativePath();
		$plugin = CustomPageLinks::$PLUGIN_PATH . DIRECTORY_SEPARATOR . "custom-page-links.php";

		return plugins_url( $path, $plugin );
	}

	public function enqueue() {
		wp_enqueue_script( $this->getHandle(),
			$this->getSourcePath(),
			$this->getDependencies(),
			CustomPageLinks::CURRENT_VERSION,
			true );
	}

	/**
	 * @var string[]
	 */
	private $meta = [ ];

	public function getMeta() {
		if ( empty( $meta ) ) {
			$filename = $this->getFilename();
			$fp = @fopen( $filename, "r" );
			if ( ! $fp ) {
				throw new \Exception( "Error reading file: ${filename}" );
			}

			$foundHeader = false;
			while ( ( $line = fgets( $fp ) ) !== false ) {
				if ( ! $foundHeader ) {
					$header = "/** CustomPageLinks Meta";
					if ( ! CustomPageLinks::startsWith( $line, $header, true ) ) {
						throw new \Exception( "Unexpected line: ${line}, expected opening comment" );
					}

					$foundHeader = true;
				} elseif ( trim( $line ) == "*/" ) {
					break;
				} else {
					$key   = substr( $line, 0, strpos( $line, ':' ) );
					$value = trim( substr( $line, strlen( $key ) + 1 ) );

					$key = strtolower( trim( $key, " \t\n\r\0\x0B*" ) );

					$this->meta[ $key ] = $value;
				}
			}

			fclose( $fp );
		}

		return $this->meta;
	}
}