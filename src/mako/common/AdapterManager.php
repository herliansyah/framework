<?php

/**
 * @copyright  Frederic G. Østby
 * @license    http://www.makoframework.com/license
 */

namespace mako\common;

use \RuntimeException;

use \mako\syringe\Container;

/**
 * Adapter manager.
 *
 * @author  Frederic G. Østby
 */

abstract class AdapterManager
{
	use \mako\common\ConfigurableTrait;
	
	//---------------------------------------------
	// Class properties
	//---------------------------------------------

	/**
	 * Reuse instances?
	 * 
	 * @var boolean
	 */

	const REUSE_INSTANCES = true;

	/**
	 * IoC container instance.
	 * 
	 * @var \mako\syringe\Container
	 */

	protected $container;

	/**
	 * Connections.
	 * 
	 * @var array
	 */

	protected $instances = [];

	//---------------------------------------------
	// Class constructor, destructor etc ...
	//---------------------------------------------

	/**
	 * Constructor.
	 * 
	 * @access  public
	 * @param   string                   $default         Default connection name
	 * @param   array                    $configurations  Configurations
	 * @param   \mako\syringe\Container  $container       IoC container instance
	 */

	public function __construct($default, array $configurations, Container $container)
	{
		$this->default = $default;

		$this->configurations = $configurations;

		$this->container = $container;
	}

	//---------------------------------------------
	// Class methods
	//---------------------------------------------

	/**
	 * Returns the factory method name.
	 * 
	 * @access  protected
	 * @param   string     $type  Cache type
	 * @return  string
	 */

	protected function getFactoryMethodName($type)
	{
		$method = $type . 'Factory';

		if(!method_exists($this, $method))
		{
			throw new RuntimeException(vsprintf("%s(): A factory method for the [ %s ] adapter has not been defined.", [__METHOD__, $type]));
		}

		return $method;
	}

	/**
	 * Returns a new adapter instance.
	 * 
	 * @access  public
	 * @param   string  $configuration  Configuration name
	 * @return  mixed
	 */

	abstract protected function instantiate($configuration);

	/**
	 * Returns an instance of the chosen adapter configuration.
	 * 
	 * @access  public
	 * @param   string  $configuration  (optional) Configuration name
	 * @return  mixed
	 */

	public function instance($configuration = null)
	{
		$configuration = $configuration ?: $this->default;

		if(static::REUSE_INSTANCES)
		{
			if(!isset($this->instances[$configuration]))
			{
				$this->instances[$configuration] = $this->instantiate($configuration);
			}

			return $this->instances[$configuration];
		}
		else
		{
			return $this->instantiate($configuration);
		}
	}

	/**
	 * Magic shortcut to the default configuration.
	 *
	 * @access  public
	 * @param   string  $name       Method name
	 * @param   array   $arguments  Method arguments
	 * @return  mixed
	 */

	public function __call($name, $arguments)
	{
		return call_user_func_array([$this->instance(), $name], $arguments);
	}
}