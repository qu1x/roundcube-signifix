<?php

// Copyright (c) 2023 Rouven Spreckels <rs@qu1x.dev>
//
// Usage of the works is permitted provided that
// this instrument is retained with the works, so that
// any entity that uses the works is notified of this instrument.
//
// DISCLAIMER: THE WORKS ARE WITHOUT WARRANTY.

include_once 'binary.php';
include_once 'metric.php';

/**
 * Signifix
 *
 * Formats bytes with metric or binary prefixes using four significant figures.
 *
 * For more information, see `README.md`.
 *
 * @version 1.0.0
 * @author Rouven Spreckels <rs@qu1x.dev>
 * @url https://roundcube.net/plugins/signifix
 */
class signifix extends rcube_plugin {
	public $task = 'mail';
	private $rc;

	function init() {
		$this->rc = rcmail::get_instance();
		$this->load_config();

		$this->add_texts('localization');
		$this->add_hook('show_bytes', array($this, 'format'));
	}
	function format($args) {
		if ($args['bytes'] == 0) {
			return array(
				'result' => '    0   B',
				'unit' => 'B',
			);
		} else {
			$decimal_mark = $this->gettext('decimal_mark');
			$grouping_sep = $this->rc->config->get('signifix_use_ws', false)
				? ' ' : $this->gettext('grouping_sep');
			if ($this->rc->config->get('signifix_metric', false))
				$bytes = new \signifix_metric\Signifix($args['bytes']);
			else
				$bytes = new \signifix_binary\Signifix($args['bytes']);
			return array(
				'result' => $bytes->def('', $decimal_mark, $grouping_sep).'B',
				'unit' => trim($bytes->symbol(), ' i').'B',
			);
		}
	}
}
