<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 28-04-15
 * Time: 20:38
 */

namespace dk\mholt\CustomPageLinks;


class HttpStatus {
	const HttpContinue = 100;
	const HttpSwitchingProtocols = 101;
	const HttpProcessing = 102;
	const HttpOK = 200;
	const HttpCreated = 201;
	const HttpAccepted = 202;
	const HttpNonAuthoritativeInformation = 203;
	const HttpNoContent = 204;
	const HttpResetContent = 205;
	const HttpPartialContent = 206;
	const HttpMultiStatus = 207;
	const HttpAlreadyReported = 208;
	const HttpIMUsed = 226;
	const HttpMultipleChoices = 300;
	const HttpMovedPermanently = 301;
	const HttpFound = 302;
	const HttpSeeOther = 303;
	const HttpNotModified = 304;
	const HttpUseProxy = 305;
	const HttpTemporaryRedirect = 307;
	const HttpPermanentRedirect = 308;
	const HttpBadRequest = 400;
	const HttpUnauthorized = 401;
	const HttpPaymentRequired = 402;
	const HttpForbidden = 403;
	const HttpNotFound = 404;
	const HttpMethodNotAllowed = 405;
	const HttpNotAcceptable = 406;
	const HttpProxyAuthenticationRequired = 407;
	const HttpRequestTimeout = 408;
	const HttpConflict = 409;
	const HttpGone = 410;
	const HttpLengthRequired = 411;
	const HttpPreconditionFailed = 412;
	const HttpPayloadTooLarge = 413;
	const HttpURITooLong = 414;
	const HttpUnsupportedMediaType = 415;
	const HttpRangeNotSatisfiable = 416;
	const HttpExpectationFailed = 417;
	const HttpMisdirectedRequest = 421;
	const HttpUnprocessableEntity = 422;
	const HttpLocked = 423;
	const HttpFailedDependency = 424;
	const HttpUpgradeRequired = 426;
	const HttpPreconditionRequired = 428;
	const HttpTooManyRequests = 429;
	const HttpRequestHeaderFieldsTooLarge = 431;
	const HttpInternalServerError = 500;
	const HttpNotImplemented = 501;
	const HttpBadGateway = 502;
	const HttpServiceUnavailable = 503;
	const HttpGatewayTimeout = 504;
	const HttpHTTPVersionNotSupported = 505;
	const HttpVariantAlsoNegotiates = 506;
	const HttpInsufficientStorage = 507;
	const HttpLoopDetected = 508;
	const HttpNotExtended = 510;
	const HttpNetworkAuthenticationRequire = 511;

	/**
	 * @var string[]
	 */
	private static $status = [
		self::HttpContinue                     => "Continue",
		self::HttpSwitchingProtocols           => "Switching Protocols",
		self::HttpProcessing                   => "Processing",
		self::HttpOK                           => "OK",
		self::HttpCreated                      => "Created",
		self::HttpAccepted                     => "Accepted",
		self::HttpNonAuthoritativeInformation  => "Non-Authoritative Information",
		self::HttpNoContent                    => "No Content",
		self::HttpResetContent                 => "Reset Content",
		self::HttpPartialContent               => "Partial Content",
		self::HttpMultiStatus                  => "Multi-Status",
		self::HttpAlreadyReported              => "Already Reported",
		self::HttpIMUsed                       => "IM Used",
		self::HttpMultipleChoices              => "Multiple Choices",
		self::HttpMovedPermanently             => "Moved Permanently",
		self::HttpFound                        => "Found",
		self::HttpSeeOther                     => "See Other",
		self::HttpNotModified                  => "Not Modified",
		self::HttpUseProxy                     => "Use Proxy",
		self::HttpTemporaryRedirect            => "Temporary Redirect",
		self::HttpPermanentRedirect            => "Permanent Redirect",
		self::HttpBadRequest                   => "Bad Request",
		self::HttpUnauthorized                 => "Unauthorized",
		self::HttpPaymentRequired              => "Payment Required",
		self::HttpForbidden                    => "Forbidden",
		self::HttpNotFound                     => "Not Found",
		self::HttpMethodNotAllowed             => "Method Not Allowed",
		self::HttpNotAcceptable                => "Not Acceptable",
		self::HttpProxyAuthenticationRequired  => "Proxy Authentication Required",
		self::HttpRequestTimeout               => "Request Timeout",
		self::HttpConflict                     => "Conflict",
		self::HttpGone                         => "Gone",
		self::HttpLengthRequired               => "Length Required",
		self::HttpPreconditionFailed           => "Precondition Failed",
		self::HttpPayloadTooLarge              => "Payload Too Large",
		self::HttpURITooLong                   => "URI Too Long",
		self::HttpUnsupportedMediaType         => "Unsupported Media Type",
		self::HttpRangeNotSatisfiable          => "Range Not Satisfiable",
		self::HttpExpectationFailed            => "Expectation Failed",
		self::HttpMisdirectedRequest           => "Misdirected Request",
		self::HttpUnprocessableEntity          => "Unprocessable Entity",
		self::HttpLocked                       => "Locked",
		self::HttpFailedDependency             => "Failed Dependency",
		self::HttpUpgradeRequired              => "Upgrade Required",
		self::HttpPreconditionRequired         => "Precondition Required",
		self::HttpTooManyRequests              => "Too Many Requests",
		self::HttpRequestHeaderFieldsTooLarge  => "Request Header Fields Too Large",
		self::HttpInternalServerError          => "Internal Server Error",
		self::HttpNotImplemented               => "Not Implemented",
		self::HttpBadGateway                   => "Bad Gateway",
		self::HttpServiceUnavailable           => "Service Unavailable",
		self::HttpGatewayTimeout               => "Gateway Timeout",
		self::HttpHTTPVersionNotSupported      => "HTTP Version Not Supported",
		self::HttpVariantAlsoNegotiates        => "Variant Also Negotiates",
		self::HttpInsufficientStorage          => "Insufficient Storage",
		self::HttpLoopDetected                 => "Loop Detected",
		self::HttpNotExtended                  => "Not Extended",
		self::HttpNetworkAuthenticationRequire => "Network Authentication Required"
	];

	/**
	 * Get the message of the specified status
	 *
	 * @param int $status A valid HTTP status header
	 *
	 * @return string
	 */
	public static function getStatus( $status ) {
		if ( ! array_key_exists( $status, self::$status ) ) {
			ViewController::error( "Unexpected status: {$status}" );
		}

		return self::$status[ $status ];
	}

	/**
	 * Send the specified status header.
	 *
	 * @param int $status A valid HTTP status header
	 */
	public static function sendHeader( $status ) {
		if ( ! array_key_exists( $status, self::$status ) ) {
			ViewController::error( "Unexpected status: {$status}" );
		}

		header( 'HTTP/1.1 ' . $status );
	}
}