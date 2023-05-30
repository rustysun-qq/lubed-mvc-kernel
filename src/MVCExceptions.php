<?php
namespace Lubed\MVCKernel;

use Lubed\Exceptions\RuntimeException;

final class MVCExceptions
{
	const INVALID_ACTION_HANDLER=101401;
	const MISS_REQUIRED_ARGUMENT=101402;

	public static function invalidActionHandler(string $msg):RuntimeException
	{
		throw new RuntimeException(self::INVALID_ACTION_HANDLER, $msg);
	}

	public static function missRequiredArgument(string $msg):RuntimeException
	{
		throw new RuntimeException(self::MISS_REQUIRED_ARGUMENT, $msg);
	}
}