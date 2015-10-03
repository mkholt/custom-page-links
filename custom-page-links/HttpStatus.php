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

	private static $status = [
		100 => "Continue",
		101 => "Switching Protocols",
		102 => "Processing",
		200 => "OK",
		201 => "Created",
		202 => "Accepted",
		203 => "Non-Authoritative Information",
		204 => "No Content",
		205 => "Reset Content",
		206 => "Partial Content",
		207 => "Multi-Status",
		208 => "Already Reported",
		226 => "IM Used",
		300 => "Multiple Choices",
		301 => "Moved Permanently",
		302 => "Found",
		303 => "See Other",
		304 => "Not Modified",
		305 => "Use Proxy",
		307 => "Temporary Redirect",
		308 => "Permanent Redirect",
		400 => "Bad Request",
		401 => "Unauthorized",
		402 => "Payment Required",
		403 => "Forbidden",
		404 => "Not Found",
		405 => "Method Not Allowed",
		406 => "Not Acceptable",
		407 => "Proxy Authentication Required",
		408 => "Request Timeout",
		409 => "Conflict",
		410 => "Gone",
		411 => "Length Required",
		412 => "Precondition Failed",
		413 => "Payload Too Large",
		414 => "URI Too Long",
		415 => "Unsupported Media Type",
		416 => "Range Not Satisfiable",
		417 => "Expectation Failed",
		421 => "Misdirected Request",
		422 => "Unprocessable Entity",
		423 => "Locked",
		424 => "Failed Dependency",
		426 => "Upgrade Required",
		428 => "Precondition Required",
		429 => "Too Many Requests",
		431 => "Request Header Fields Too Large",
		500 => "Internal Server Error",
		501 => "Not Implemented",
		502 => "Bad Gateway",
		503 => "Service Unavailable",
		504 => "Gateway Timeout",
		505 => "HTTP Version Not Supported",
		506 => "Variant Also Negotiates",
		507 => "Insufficient Storage",
		508 => "Loop Detected",
		510 => "Not Extended",
		511 => "Network Authentication Required"
	];

	public static function getStatus($status)
	{
		if (!array_key_exists($status, self::$status))
		{
			CustomPageLinks::error("Unexpected status: {$status}");
		}

		return self::$status[$status];
	}
}