<?php

namespace App\Helpers;


class LvCount{

    public static function lv_count($n) {

		$n = (0 + str_replace(",", "", $n));

		if (!is_numeric($n)) {
			return false;
		}

		if ($n > 1000000000000) {
			return round(($n / 1000000000000), 1) . ' T';
		} elseif ($n > 1000000000) {
			return round(($n / 1000000000), 1) . ' B';
		} elseif ($n > 1000000) {
			return round(($n / 1000000), 1) . ' M';
		} elseif ($n >= 1000) {
			return round(($n / 1000), 1) . ' K';
		}

		return number_format($n);

	}

}