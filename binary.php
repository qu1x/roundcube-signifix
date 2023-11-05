<?php

// Copyright (c) 2016-2023 Rouven Spreckels <rs@qu1x.dev>
//
// Usage of the works is permitted provided that
// this instrument is retained with the works, so that
// any entity that uses the works is notified of this instrument.
//
// DISCLAIMER: THE WORKS ARE WITHOUT WARRANTY.

namespace signifix_binary;

const OUT_OF_LOWER_BOUND = 0;
const OUT_OF_UPPER_BOUND = 1;
const NAN = 2;

abstract class Error extends \Exception {
	public abstract function getNumber();
}

class OutOfLowerBound extends Error {
	private $number;
	public function getNumber() {
		return $this->number;
	}
	public function __construct($number) {
		$this->number = $number;
		parent::__construct(
			"out of lower bound ±1.000 (= ±1 024 ^ 0) for $number",
			OUT_OF_LOWER_BOUND,
		);
	}
}
class OutOfUpperBound extends Error {
	private $number;
	public function getNumber() {
		return $this->number;
	}
	public function __construct($number) {
		$this->number = $number;
		parent::__construct(
			"out of upper bound ±1 023 Yi (≈ ±1 024 ^ 9) for $number",
			OUT_OF_UPPER_BOUND,
		);
	}
}
class Nan extends Error {
	public function getNumber() {
		return \NAN;
	}
	public function __construct() {
		parent::__construct('not a number (NaN)', NAN);
	}
}

const DEF_MIN_LEN = 8;
const DEF_MAX_LEN = 9;

const SYMBOLS = array(
	'  ',
	'Ki',
	'Mi',
	'Gi',
	'Ti',
	'Pi',
	'Ei',
	'Zi',
	'Yi',
);
const FACTORS = array(
	2**00,
	2**10,
	2**20,
	2**30,
	2**40,
	2**50,
	2**60,
	2**70,
	2**80,
);

class Signifix {
	private $numerator;
	private $exponent;
	private $prefix;

	public function significand() {
		return $this->numerator / $this->denominator();
	}
	public function numerator() {
		return $this->numerator;
	}
	public function denominator() {
		return array(1, 10, 100, 1000)[$this->exponent];
	}
	public function exponent() {
		return $this->exponent;
	}
	public function integer() {
		return intdiv($this->numerator, $this->denominator());
	}
	public function fractional() {
		return abs($this->numerator) % $this->denominator();
	}
	public function parts() {
		$trunc = $this->integer();
		$fract = $this->numerator - $this->denominator() * $trunc;
		return array($trunc, abs($fract));
	}
	public function prefix() {
		return $this->prefix;
	}
	public function symbol() {
		return SYMBOLS[$this->prefix];
	}
	public function factor() {
		return FACTORS[$this->prefix];
	}

	public function __construct($number) {
		settype($number, 'float');
		$numerator = abs($number);
		if ($numerator < FACTORS[8]) {
			if ($numerator < FACTORS[4]) {
				if ($numerator < FACTORS[2])
					$this->prefix = $numerator < FACTORS[1] ? 0 : 1;
				else
					$this->prefix = $numerator < FACTORS[3] ? 2 : 3;
			} else {
				if ($numerator < FACTORS[6])
					$this->prefix = $numerator < FACTORS[5] ? 4 : 5;
				else
					$this->prefix = $numerator < FACTORS[7] ? 6 : 7;
			}
		} else {
			$this->prefix = 8;
		}
		$numerator /= FACTORS[$this->prefix];
		$middle = round($numerator * 1e+02);
		if ($middle < 1e+04) {
			$lower = round($numerator * 1e+03);
			if ($lower < 1e+04) {
				if ($lower < 1e+03)
					throw new OutOfLowerBound($number);
				$this->numerator = (integer)$lower;
				$this->exponent = 3;
			} else {
				$this->numerator = (integer)$middle;
				$this->exponent = 2;
			}
		} else {
			$upper = round($numerator * 1e+01);
			if ($upper < 1e+04) {
				$this->numerator = (integer)$upper;
				$this->exponent = 1;
			} else {
				$above = round($numerator);
				if ($above < 1.024e+03) {
					$this->numerator = (integer)$above;
					$this->exponent = 0;
				} else {
					if (++$this->prefix == count(FACTORS))
						throw is_nan($number)
							? new Nan() : new OutOfUpperBound($number);
					$this->numerator = 1000;
					$this->exponent = 3;
				}
			}
		}
		if ($number < 0.0)
			$this->numerator = -$this->numerator;
	}

	public function __toString() {
		return $this->def();
	}

	public function def($plus = '', $decimal_mark = '.', $grouping_sep = ' ') {
		$sign = $this->numerator < 0 ? '-' : $plus;
		if ($this->exponent == 0) {
			$fractional = sprintf('%03d', abs($this->numerator) - 1000);
			return $sign.'1'.$grouping_sep.$fractional.' '.$this->symbol();
		}
		$parts = $this->parts();
		return $sign.abs($parts[0]).$decimal_mark
			.sprintf('%0'.$this->exponent.'d ', $parts[1]).$this->symbol();
	}
}

function def($number, $plus = '', $decimal_mark = '.', $grouping_sep = ' ') {
	return (new Binary($number))->def($plus, $decimal_mark, $grouping_sep);
}
