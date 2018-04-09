<?php

namespace Duffleman\MacAddr;

use Symfony\Component\Process\Process;
use Illuminate\Support\Collection;

class Darwin
{
	public function all(): array
	{
		$interfaces = $this->getInterfaces();

		$justHex = array_map(function ($item) {
			return $item['ethernetAddress'];
		}, $interfaces);

		$unique = array_unique($justHex);

		return $unique;
	}

	protected function getInterfaces(): array
	{
		$interfaces = [];

		$process = new Process('networksetup -listallhardwareports');
		$process->run();

		if (!$process->isSuccessful()) {
			return [];
		}

		$out = $process->getOutput();

		$rawInterfaceStrings = explode("\n\n", $out);

		foreach ($rawInterfaceStrings as $is) {
			$interface = [];

			$lines = explode("\n", $is);

			foreach ($lines as $line) {
				$keyVal = explode(': ', $line);
				$key = toCamelCase($keyVal[0]);
				$val = isset($keyVal[1]) ? $keyVal[1] : null;

				if ($val) {
					$interface[$key] = $val;
				}

			}

			if (count($interface) >= 1) {
				$interfaces[] = $interface;
			}
		}

		return $interfaces;
	}
}

function toCamelCase($string){
	$string = str_replace('-', ' ', $string);
	$string = str_replace('_', ' ', $string);
	$string = lcfirst(ucwords(strtolower($string)));
	$string = str_replace(' ', '', $string);

	return $string;
}
