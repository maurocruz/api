<?php
namespace Plinct\Api\Infrastructure\FileProcessor;

use Exception;
use finfo;
use Psr\Log\InvalidArgumentException;
use Smalot\PdfParser\Parser;

class PdfMetadataExtractor
{
	protected string $filePath;

	public function __construct(string $filePath)
	{
		if (!file_exists($filePath)) {
			throw new InvalidArgumentException("Arquivo não encontrado.");
		}

		$this->filePath = $filePath;

		if (!$this->isPdf()) {
			throw new InvalidArgumentException("Arquivo não é um PDF válido.");
		}
	}

	public function extract(): array
	{
		// Prioridade 1: pdfinfo (melhor opção)
		if ($this->commandExists('pdfinfo')) {
			return $this->extractUsingPdfInfo();
		}

		// Prioridade 2: smalot/pdfparser
		if (class_exists(Parser::class)) {
			return $this->extractUsingPdfParser();
		}

		// Prioridade 3: PHP puro
		return $this->extractUsingPurePhp();
	}

	protected function isPdf(): bool
	{
		// Se finfo existir, usar validação real
		if (class_exists('finfo')) {
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			return $finfo->file($this->filePath) === 'application/pdf';
		}

		// Fallback: checar header do arquivo
		$handle = fopen($this->filePath, 'rb');
		$header = fread($handle, 4);
		fclose($handle);

		return $header === '%PDF';
	}

	protected function extractUsingPdfInfo(): array
	{
		$cmd = 'pdfinfo ' . escapeshellarg($this->filePath);
		exec($cmd, $output, $returnCode);

		if ($returnCode !== 0) {
			return [];
		}

		$metadata = [];

		foreach ($output as $line) {
			if (str_contains($line, ':')) {
				[$key, $value] = explode(':', $line, 2);
				$metadata[trim($key)] = trim($value);
			}
		}

		return $metadata;
	}

	protected function extractUsingPdfParser(): array
	{
		try {
			$parser = new Parser();
			$pdf = $parser->parseFile($this->filePath);

			$details = $pdf->getDetails();

			$details['Pages'] = count($pdf->getPages());

			return $details;
		} catch (Exception) {
			return [];
		}
	}

	protected function extractUsingPurePhp(): array
	{
		$content = file_get_contents($this->filePath);

		$metadata = [];

		// Contagem simples de páginas
		preg_match_all("/\/Type\s*\/Page[^s]/", $content, $matches);
		$metadata['Pages'] = count($matches[0]);

		// Tentar extrair Title
		if (preg_match('/\/Title\s*\((.*?)\)/', $content, $match)) {
			$metadata['Title'] = $match[1];
		}

		if (preg_match('/\/Author\s*\((.*?)\)/', $content, $match)) {
			$metadata['Author'] = $match[1];
		}

		return $metadata;
	}

	protected function commandExists(string $command): bool
	{
		$whereIsCommand = (PHP_OS_FAMILY === 'Windows') ? 'where' : 'which';

		$process = shell_exec("$whereIsCommand $command");

		return !empty($process);
	}
}