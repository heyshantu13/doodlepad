<?php
/**
 * Company : Bsetec
 * Product: Instasocial
 * Library : bsetecHelpers
 * Email : support@bsetec.com
 * Copyright © 2018 BSEtec. All rights reserved.
 */
class bsetecHelpers {
	/**
	 * This is the function for formatting count values like (following,follower,post count).
	 * If those count is large number of digits, we will reduce digit and return as 1K/1M/1B/1T
	 */
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
	/**
	 * This is the function for getting profile picture url
	 * if profice picture is not available, we have return default avatar image based on gender
	 */
	public static function getProfilePicture($userId, $userInfo) {
		$profilePic = url('/uploads/images/default.png');
		if (strlen($userInfo->profile_pic) > 0) {
			$profilePic = url('/uploads/images/user/' . $userInfo->profile_pic);
		} else {
			switch ($userInfo->gender) {
			case 'male':
				$profilePic = url('/uploads/images/male.png');
				break;
			case 'female':
				$profilePic = url('/uploads/images/female.png');
				break;
			default:
				$profilePic = url('/uploads/images/default.png');
				break;
			}
		}
		return $profilePic;
	}
}
?>
