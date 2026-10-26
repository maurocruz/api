<?php
namespace Plinct\Api\Infrastructure\FileProcessor;

use finfo;
use getID3;

class AudioVideoMetadataExtractor
{
	public function extract(string $file): array
	{
		if ($this->ffprobeAvailable()) {
			return $this->extractWithFFprobe($file);
		}

		if (class_exists(getID3::class)) {
			return $this->extractWithGetID3($file);
		}

		return $this->extractBasic($file);
	}

	private function extractWithGetID3(string $file): array
	{
		$getID3 = new getID3;

		$info = $getID3->analyze($file);

		return [
			'duration' => $info['playtime_seconds'] ?? null,
			'bitrate' => $info['bitrate'] ?? null,
			'format' => $info['fileformat'] ?? null,

			'video' => [
				'codec' => $info['video']['codec'] ?? null,
				'width' => $info['video']['resolution_x'] ?? null,
				'height' => $info['video']['resolution_y'] ?? null,
				'fps' => $info['video']['frame_rate'] ?? null,
			],

			'audio' => [
				'codec' => $info['audio']['codec'] ?? null,
				'channels' => $info['audio']['channels'] ?? null,
				'sample_rate' => $info['audio']['sample_rate'] ?? null,
			]
		];
	}

	private function extractWithFFprobe(string $file): array
	{
		$cmd = sprintf(
			'ffprobe -v quiet -print_format json -show_format -show_streams %s',
			escapeshellarg($file)
		);

		$json = shell_exec($cmd);

		if (!$json) {
			return [];
		}

		$data = json_decode($json, true);

		return [
			'duration' => $data['format']['duration'] ?? null,
			'bitrate' => $data['format']['bit_rate'] ?? null,
			'format' => $data['format']['format_name'] ?? null
		];
	}

	private function extractBasic(string $file): array
	{
		$finfo = new finfo(FILEINFO_MIME_TYPE);

		return [
			'mime' => $finfo->file($file),
			'size' => filesize($file)
		];
	}

	private function ffprobeAvailable(): bool
	{
		if (!function_exists('shell_exec')) {
			return false;
		}

		$where = PHP_OS_FAMILY === 'Windows' ? 'where' : 'which';

		return !empty(shell_exec("$where ffprobe"));
	}
}
