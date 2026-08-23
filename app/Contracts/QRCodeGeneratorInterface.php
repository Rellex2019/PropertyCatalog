<?php

namespace App\Contracts;

interface QRCodeGeneratorInterface
{
    public function generate(string $data, int $size = null): string;
    public function saveToFile(string $data, string $path, int $size = null): bool;
    public function generateWithLogo(string $data, string $logoPath, int $size = 400): string;
    public function generateForDownload(string $data, int $size = 500): string;
    public function generateForModel(string $modelType, int $modelId, array $additionalData = []): string;
}