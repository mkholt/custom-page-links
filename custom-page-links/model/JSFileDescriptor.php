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

	private $baseDir;

	public function __construct( $filename, $baseDir = null ) {
		$this->filename = $filename;
		if ( empty( $baseDir ) ) {
			$baseDir = CustomPageLinks::$PLUGIN_PATH;
		}

		$this->baseDir = $baseDir;
	}

	public function getFilename() {
		return $this->filename;
	}

	public function getBaseDir() {
		return $this->baseDir;
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
			$filename     = $this->getFilename();
			$name         = substr( $filename, 0, strpos( $filename, '.' ) );
			$this->handle = "cpl-${name}";
		}

		return $this->handle;
	}

	public function getSourceLocation() {
		return $this->getBaseDir() . DIRECTORY_SEPARATOR . $this->getFilename();
	}

	public function enqueue() {
		wp_enqueue_script( $this->getHandle(),
			$this->getSourceLocation(),
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
			$fp = fopen( $this->getSourceLocation(), "r" );
			if ( ! $fp ) {
				$sourceLocation = $this->getSourceLocation();
				throw new \Exception( "Error reading file: ${sourceLocation}" );
			}

			$foundHeader = false;
			while ( ( $line = fgets( $fp ) ) !== false ) {
				if ( ! $foundHeader ) {
					$header = "/** CustomPageLinks Meta";
					if ( ! $this->startsWith( $line, $header ) ) {
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

	// http://stackoverflow.com/a/834355
	private function startsWith( $haystack, $needle ) {
		$length = strlen( $needle );

		return ( substr( $haystack, 0, $length ) === $needle );
	}

	// http://stackoverflow.com/a/834355
	private function endsWith( $haystack, $needle ) {
		$length = strlen( $needle );
		if ( $length == 0 ) {
			return true;
		}

		return ( substr( $haystack, - $length ) === $needle );
	}
}