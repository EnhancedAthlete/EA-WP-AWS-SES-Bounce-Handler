<?php

namespace EA_WP_AWS_SES_Bounce_Handler;


class Logger {

	static function log( $message, $level = 'debug' ) {
		error_log( __NAMESPACE__ . " [$level] " . $message );
	}
}