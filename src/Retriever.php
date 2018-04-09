<?php

namespace Duffleman\MacAddr;

use Duffleman\MacAddr\Exceptions\UnknownOperatingSystem;

class Retriever
{
	private $os;

	public function __construct()
	{
		$this->os = PHP_OS;
	}

	public function all(): array
	{
		return $this->getByOS();
	}

	private function getByOS(): array
	{
		switch ($this->os) {
			case 'Darwin':
				return (new Darwin)->all();
			default:
				throw new UnknownOperatingSystem("{$this->os} is not supported");
		}
	}

}
