<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Claus Due <claus@wildside.dk>, Wildside A/S
 *
 *   All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * JSON Service
 *
 * Encodes and decodes JSON using optimal settings for mixed data types.
 *
 * @package Tool
 * @subpackage Service
 */
class Tx_Tool_Service_JsonService implements t3lib_Singleton {

	/**
	 * Detect the PHP version being used
	 *
	 * @return float
	 */
	private function getPhpVersion() {
		$segments = explode('.', phpversion());
		$major = array_shift($segments);
		$minor = array_shift($segments);
		$num = $major . '.' . $minor;
		$num = (float) $num;
		return $num;
	}

	/**
	 * Get encoding options depending on PHP version
	 *
	 * @return integer
	 */
	private function getEncodeOptions() {
		if ($this->getPhpVersion() >= 5.3) {
			return JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP;
		}
		return 0;
	}

	/**
	 * Get decoding options depending on PHP version
	 *
	 * @return integer
	 */
	private function getDecodeOptions() {
		if ($this->getPhpVersion() >= 5.3) {
			return JSON_OBJECT_AS_ARRAY;
		}
		return 0;
	}

	/**
	 * Encode to working JSON depending on PHP version
	 *
	 * @param mixed $source
	 * @return string
	 * @api
	 */
	public function encode($source) {
		if ($this->getPhpVersion() >= 5.3) {
			$options = $this->getEncodeOptions();
			$str = json_encode($source, $options);
		} else {
			$str = json_encode($source);
		}
		return $str;
	}

	/**
	 * Decode to working JSON depending on PHP version
	 *
	 * @param string $str
	 * @return mixed
	 * @api
	 */
	public function decode($str) {
		if ($this->getPhpVersion() >= 5.3) {
			$options = $this->getEncodeOptions();
			$decoded = json_decode($str, $options);
		} else {
			$decoded = json_decode($str);
		}
		return $decoded;
	}

	/**
	 * @param Exception $e
	 * @return string
	 * @api
	 */
	public function getRpcError(Exception $e) {
		$data = array(
			'jsonrpc' => '2.0',
			'error' => array(
				'code' => $e->getCode(),
				'message' => $e->getMessage(),
				'id' => 'id'
			)
		);
		return $this->encode($data);
	}

	/**
	 * @param mixed $payload Data for the response
	 * @return string
	 * @api
	 */
	public function getRpcResponse($payload=NULL) {
		$data = array(
			'jsonrpc' => '2.0',
			'result' => array(
				$payload
			),
			'id' => 'id'
		);
		return $this->encode($data);
	}

}
