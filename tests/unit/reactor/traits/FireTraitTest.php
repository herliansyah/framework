<?php

/**
 * @copyright  Frederic G. Østby
 * @license    http://www.makoframework.com/license
 */

namespace mako\tests\unit\reactor\traits;

use PHPUnit_Framework_TestCase;

use mako\reactor\traits\FireTrait;

/**
 * @group unit
 */
class FireTraitTest extends PHPUnit_Framework_TestCase
{
	/**
	 *
	 */
	public function testBuildReactorPath()
	{
		$class = new class
		{
			use FireTrait;

			protected $app;

			public function __construct()
			{
				$this->app = new class
				{
					public function getPath()
					{
						return DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'bar';
					}
				};
			}

			public function test()
			{
				return $this->buildReactorPath();
			}
		};

		$this->assertEquals(DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'bar' . DIRECTORY_SEPARATOR . 'reactor', $class->test());
	}

	/**
	 *
	 */
	public function testBuildCommandWithoutEnv()
	{
		$class = new class
		{
			use FireTrait;

			protected $app;

			public function __construct()
			{
				$this->app = new class
				{
					public function getPath()
					{
						return '/foo/bar';
					}

					public function getEnvironment()
					{
						return null;
					}
				};
			}

			public function test()
			{
				return $this->buildCommand('foobar --test=1');
			}
		};

		if(DIRECTORY_SEPARATOR === '/')
		{
			$command = PHP_BINARY . ' '. DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'bar' . DIRECTORY_SEPARATOR . 'reactor foobar --test=1 2>&1';

			$this->assertEquals($command, $class->test());
		}
		else
		{
			$command = 'start ' . PHP_BINARY . ' '. DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'bar' . DIRECTORY_SEPARATOR . 'reactor foobar --test=1 2>&1';
		}
	}

	/**
	 *
	 */
	public function testBuildBackgroundCommandWithoutEnv()
	{
		$class = new class
		{
			use FireTrait;

			protected $app;

			public function __construct()
			{
				$this->app = new class
				{
					public function getPath()
					{
						return '/foo/bar';
					}

					public function getEnvironment()
					{
						return null;
					}
				};
			}

			public function test()
			{
				return $this->buildCommand('foobar --test=1', true);
			}
		};

		if(DIRECTORY_SEPARATOR === '/')
		{
			$command = PHP_BINARY . ' '. DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'bar' . DIRECTORY_SEPARATOR . 'reactor foobar --test=1 2>&1 &';

			$this->assertEquals($command, $class->test());
		}
		else
		{
			$command = 'start /b ' . PHP_BINARY . ' '. DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'bar' . DIRECTORY_SEPARATOR . 'reactor foobar --test=1 2>&1';
		}
	}

	/**
	 *
	 */
	public function testBuildCommandWithEnv()
	{
		$class = new class
		{
			use FireTrait;

			protected $app;

			public function __construct()
			{
				$this->app = new class
				{
					public function getPath()
					{
						return '/foo/bar';
					}

					public function getEnvironment()
					{
						return 'dev';
					}
				};
			}

			public function test()
			{
				return $this->buildCommand('foobar --test=1');
			}
		};

		if(DIRECTORY_SEPARATOR === '/')
		{
			$command = PHP_BINARY . ' '. DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'bar' . DIRECTORY_SEPARATOR . 'reactor foobar --test=1 --env=dev 2>&1';

			$this->assertEquals($command, $class->test());
		}
		else
		{
			$command = 'start ' . PHP_BINARY . ' '. DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'bar' . DIRECTORY_SEPARATOR . 'reactor foobar --test=1 --env=dev 2>&1';
		}
	}

	/**
	 *
	 */
	public function testBuildCommandWithEnvWithManualOverride()
	{
		$class = new class
		{
			use FireTrait;

			protected $app;

			public function __construct()
			{
				$this->app = new class
				{
					public function getPath()
					{
						return '/foo/bar';
					}

					public function getEnvironment()
					{
						return 'dev';
					}
				};
			}

			public function test()
			{
				return $this->buildCommand('foobar --test=1 --env=prod');
			}
		};

		if(DIRECTORY_SEPARATOR === '/')
		{
			$command = PHP_BINARY . ' '. DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'bar' . DIRECTORY_SEPARATOR . 'reactor foobar --test=1 --env=prod 2>&1';

			$this->assertEquals($command, $class->test());
		}
		else
		{
			$command = 'start ' . PHP_BINARY . ' '. DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'bar' . DIRECTORY_SEPARATOR . 'reactor foobar --test=1 --env=prod 2>&1';
		}
	}

	/**
	 *
	 */
	public function testBuildCommandWithEnvWithoutUsingSame()
	{
		$class = new class
		{
			use FireTrait;

			protected $app;

			public function __construct()
			{
				$this->app = new class
				{
					public function getPath()
					{
						return '/foo/bar';
					}

					public function getEnvironment()
					{
						return 'dev';
					}
				};
			}

			public function test()
			{
				return $this->buildCommand('foobar --test=1', false, false);
			}
		};

		if(DIRECTORY_SEPARATOR === '/')
		{
			$command = PHP_BINARY . ' '. DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'bar' . DIRECTORY_SEPARATOR . 'reactor foobar --test=1 2>&1';

			$this->assertEquals($command, $class->test());
		}
		else
		{
			$command = 'start ' . PHP_BINARY . ' '. DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'bar' . DIRECTORY_SEPARATOR . 'reactor foobar --test=1 2>&1';
		}
	}
}