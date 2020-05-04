<?php
/**
 * Company : Bsetec
 * Product: Instasocial
 * Library : VideoHelpers
 * Email : support@bsetec.com
 * Copyright © 2018 BSEtec. All rights reserved.
 */
namespace App\Library;
class VideoHelpers {
	private $ffmpeg_path;
	private $file_path;
	private $file_name;
	private $data;

	public function __construct($path, $file, $name) {
		$this->ffmpeg_path = $path;
		$this->file_path = $file;
		$this->file_name = $name;
		$this->data['split_duration'] = 0;
		$this->data['size'] = '320x480';
	}
	/**
	 * This is the function for getting duration of uploaded video.
	 */
	public function getDuration() {
		$cmd = shell_exec("$this->ffmpeg_path -i \"{$this->file_path}\" 2>&1");
		preg_match('/Duration: (.*?),/', $cmd, $matches);
		if (count($matches) > 0) {
			return $matches[1];
		} else {
			return '';
		}
	}
	/**
	 * This is the function for converting image from video.
	 * It will be called while user uploading video from mobile application.
	 * This converted image will be used as feed thumbnail for video post.
	 */
	public function convertImages() {
		$cmd = "$this->ffmpeg_path -i {$this->file_path} -an -ss 00:00:00 -vframes 1 " . base_path() . "/uploads/images/post/{$this->file_name}.jpg";
		if (!shell_exec($cmd)) {
			return true;
		} else {
			return false;
		}
	}
}
