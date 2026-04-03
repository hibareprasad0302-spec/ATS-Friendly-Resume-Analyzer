<?php

class FileUploader
{
    private string $uploadDir;
    private array $errors = [];

    public function __construct(string $uploadDir = UPLOAD_DIR)
    {
        $this->uploadDir = rtrim($uploadDir, '/') . '/';
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function upload(array $file): array|false
    {
        $this->errors = [];

        if (empty($file) || !isset($file['tmp_name'])) {
            $this->errors[] = 'No file uploaded.';
            return false;
        }

        if (!$this->validateUploadError($file['error'] ?? UPLOAD_ERR_NO_FILE)) {
            return false;
        }

        if (!$this->validateFileSize($file['size'] ?? 0)) {
            return false;
        }

        $ext = $this->validateExtension($file['name'] ?? '');
        if ($ext === false) {
            return false;
        }

        if (!$this->validateMimeType($file['tmp_name'])) {
            return false;
        }

        if (!$this->validateFileContent($file['tmp_name'], $ext)) {
            return false;
        }

        $storedFilename = $this->generateStoredFilename($ext);
        $destination = $this->uploadDir . $storedFilename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $this->errors[] = 'Failed to save uploaded file.';
            return false;
        }

        return [
            'stored_filename'   => $storedFilename,
            'original_filename' => $file['name'],
            'file_type'         => $ext,
        ];
    }

    private function validateUploadError(int $error): bool
    {
        if ($error === UPLOAD_ERR_OK) {
            return true;
        }

        $messages = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds server upload limit.',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds form upload limit.',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server misconfiguration: missing temp directory.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'Upload stopped by a PHP extension.',
        ];

        $this->errors[] = $messages[$error] ?? 'Unknown upload error.';
        return false;
    }

    private function validateFileSize(int $size): bool
    {
        if ($size > MAX_FILE_SIZE) {
            $maxMB = MAX_FILE_SIZE / (1024 * 1024);
            $this->errors[] = "File size exceeds the maximum limit of {$maxMB} MB.";
            return false;
        }
        return true;
    }

    private function validateExtension(string $filename): string|false
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_EXTENSIONS, true)) {
            $this->errors[] = 'Invalid file type. Only PDF and DOCX files are accepted.';
            return false;
        }
        return $ext;
    }

    private function validateMimeType(string $tmpPath): bool
    {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmpPath);

        if (!in_array($mime, ALLOWED_MIME_TYPES, true)) {
            $this->errors[] = 'File content does not match an allowed type.';
            return false;
        }
        return true;
    }

    private function validateFileContent(string $tmpPath, string $ext): bool
    {
        if ($ext === 'pdf') {
            $header = file_get_contents($tmpPath, false, null, 0, 5);
            if ($header !== '%PDF-') {
                $this->errors[] = 'Invalid PDF file.';
                return false;
            }
        } elseif ($ext === 'docx') {
            $header = file_get_contents($tmpPath, false, null, 0, 4);
            if ($header !== "PK\x03\x04") {
                $this->errors[] = 'Invalid DOCX file.';
                return false;
            }
        }
        return true;
    }

    private function generateStoredFilename(string $ext): string
    {
        return uniqid('resume_', true) . '.' . $ext;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
