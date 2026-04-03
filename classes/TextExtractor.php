<?php

use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory as WordFactory;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\ListItem;
use PhpOffice\PhpWord\Element\ListItemRun;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextBreak;

class TextExtractor
{
    public function extract(string $filePath, string $fileType): string
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException('File not found: ' . $filePath);
        }

        $text = match ($fileType) {
            'pdf'  => $this->extractFromPdf($filePath),
            'docx' => $this->extractFromDocx($filePath),
            default => throw new RuntimeException("Unsupported file type: $fileType"),
        };

        return $this->sanitizeUtf8($text);
    }

    private function sanitizeUtf8(string $text): string
    {
        // Remove invalid UTF-8 bytes that cause MySQL encoding errors
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        // Strip any remaining non-UTF8 characters
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text);
        // Fallback: force valid UTF-8
        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = iconv('UTF-8', 'UTF-8//IGNORE', $text);
        }
        return $text;
    }

    private function extractFromPdf(string $path): string
    {
        $parser = new PdfParser();
        $pdf = $parser->parseFile($path);
        $text = $pdf->getText();

        if (empty(trim($text))) {
            throw new RuntimeException(
                'Could not extract text from PDF. The file may be image-based or encrypted.'
            );
        }
        return $text;
    }

    private function extractFromDocx(string $path): string
    {
        $phpWord = WordFactory::load($path, 'Word2007');
        $text = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                $text .= $this->extractElementText($element) . "\n";
            }
        }

        if (empty(trim($text))) {
            throw new RuntimeException('Could not extract text from DOCX.');
        }
        return $text;
    }

    private function extractElementText($element): string
    {
        if ($element instanceof Text) {
            return $element->getText();
        }

        if ($element instanceof TextRun || $element instanceof ListItemRun) {
            $parts = [];
            foreach ($element->getElements() as $child) {
                $parts[] = $this->extractElementText($child);
            }
            return implode('', $parts);
        }

        if ($element instanceof ListItem) {
            return '- ' . $element->getText();
        }

        if ($element instanceof Table) {
            $rows = [];
            foreach ($element->getRows() as $row) {
                $cells = [];
                foreach ($row->getCells() as $cell) {
                    $cellParts = [];
                    foreach ($cell->getElements() as $cellElement) {
                        $cellParts[] = $this->extractElementText($cellElement);
                    }
                    $cells[] = implode(' ', $cellParts);
                }
                $rows[] = implode(' | ', $cells);
            }
            return implode("\n", $rows);
        }

        if ($element instanceof TextBreak) {
            return "\n";
        }

        if (method_exists($element, 'getText')) {
            return $element->getText();
        }

        if (method_exists($element, 'getElements')) {
            $parts = [];
            foreach ($element->getElements() as $child) {
                $parts[] = $this->extractElementText($child);
            }
            return implode('', $parts);
        }

        return '';
    }
}
