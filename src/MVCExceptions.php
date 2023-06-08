<?php
namespace Lubed\MVCKernel;

use Lubed\Exceptions\RuntimeException;

final class MVCExceptions
{
	const START_FAILED=10401;
	const INVALID_ACTION_HANDLER=101411;
	const MISS_REQUIRED_ARGUMENT=101412;

	public static function startFailed(string $msg):RuntimeException
	{
		throw new RuntimeException(self::START_FAILED, $msg);
	}

	public static function invalidActionHandler(string $msg):RuntimeException
	{
		throw new RuntimeException(self::INVALID_ACTION_HANDLER, $msg);
	}

	public static function missRequiredArgument(string $msg):RuntimeException
	{
		throw new RuntimeException(self::MISS_REQUIRED_ARGUMENT, $msg);
	}
}