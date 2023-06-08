<?php
namespace Lubed\MVCKernel;

final class MVCKernelComponent
{
	const NAME='lubed_mvc_kernel';
	const KERNEL_INSTANCE_NAME='lubed_mvc_kernel';

	private $instances;

	public function __construct(array $instances)
	{
		$this->instances=$instances;
	}

	public function getInstanceBy(string $name)
	{
		if(false === isset($this->instances[$name])){

		}

		return $this->instances[$name];
	}

	public function getInstances():array
	{
		return $this->instances;
	}
}