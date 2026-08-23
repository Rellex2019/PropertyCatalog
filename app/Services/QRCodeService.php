<?php

namespace App\Services;

use App\Contracts\QRCodeGeneratorInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Illuminate\Support\Facades\Log;

class QRCodeService implements QRCodeGeneratorInterface
{
    protected array $config;

    public function __construct()
    {
        $this->config = [
            'size' => 300,
            'margin' => 10,
            'foreground' => [0, 0, 0],
            'background' => [255, 255, 255],
            'error_correction' => 'High',
        ];
    }

    /**
     * Генерирует QR-код и возвращает base64 строку
     */
    public function generate(string $data, int $size = null): string
    {
        try {
            $size = $size ?? $this->config['size'];
            
            $result = $this->buildQrCode($data, $size);
            
            return $result->getDataUri();
        } catch (\Exception $e) {
            Log::error('QR Code generation failed: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw new \RuntimeException('Не удалось сгенерировать QR-код: ' . $e->getMessage());
        }
    }

    /**
     * Генерирует QR-код и сохраняет в файл
     */
    public function saveToFile(string $data, string $path, int $size = null): bool
    {
        try {
            $size = $size ?? $this->config['size'];
            
            $result = $this->buildQrCode($data, $size);
            
            // Сохраняем в публичную директорию
            $fullPath = public_path($path);
            
            // Создаем директорию, если её нет
            $directory = dirname($fullPath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            $result->saveToFile($fullPath);
            
            return true;
        } catch (\Exception $e) {
            Log::error('QR Code save failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Генерирует QR-код с логотипом
     */
    public function generateWithLogo(string $data, string $logoPath, int $size = 400): string
    {
        try {
            $builder = new Builder(
                writer: new PngWriter(),
                writerOptions: [],
                validateResult: false,
                data: $data,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: $this->getErrorCorrectionLevel(),
                size: $size,
                margin: $this->config['margin'],
                roundBlockSizeMode: RoundBlockSizeMode::Margin,
                foregroundColor: $this->getForegroundColor(),
                backgroundColor: $this->getBackgroundColor(),
                logoPath: $logoPath,
                logoResizeToWidth: 80,
                logoResizeToHeight: 80,
                logoPunchoutBackground: true
            );

            $result = $builder->build();

            return $result->getDataUri();
        } catch (\Exception $e) {
            Log::error('QR Code with logo generation failed: ' . $e->getMessage());
            throw new \RuntimeException('Не удалось сгенерировать QR-код с логотипом');
        }
    }

    /**
     * Генерирует QR-код для скачивания (возвращает бинарные данные)
     */
    public function generateForDownload(string $data, int $size = 500): string
    {
        try {
            $result = $this->buildQrCode($data, $size);
            
            return $result->getString();
        } catch (\Exception $e) {
            Log::error('QR Code download generation failed: ' . $e->getMessage());
            throw new \RuntimeException('Не удалось сгенерировать QR-код для скачивания');
        }
    }

    /**
     * Генерирует QR-код для конкретного объекта (например, для товара)
     */
    public function generateForModel(string $modelType, int $modelId, array $additionalData = []): string
    {
        // Создаем URL для модели
        $url = route("{$modelType}.show", $modelId);
        
        // Добавляем дополнительные данные, если нужно
        if (!empty($additionalData)) {
            $url .= '?' . http_build_query($additionalData);
        }
        
        return $this->generate($url);
    }

    /**
     * Строит QR-код с общими настройками
     */
    protected function buildQrCode(string $data, int $size)
    {
        // ✅ Используем упрощенный Builder без цветов
        $builder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            validateResult: false,
            data: $data,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: $this->getErrorCorrectionLevel(),
            size: $size,
            margin: $this->config['margin'],
            roundBlockSizeMode: RoundBlockSizeMode::Margin
        );

        return $builder->build();
    }

    /**
     * Получает уровень коррекции ошибок из конфига
     */
    protected function getErrorCorrectionLevel()
    {
        $levels = [
            'Low' => ErrorCorrectionLevel::Low,
            'Medium' => ErrorCorrectionLevel::Medium,
            'Quartile' => ErrorCorrectionLevel::Quartile,
            'High' => ErrorCorrectionLevel::High,
        ];

        return $levels[$this->config['error_correction']] ?? ErrorCorrectionLevel::High;
    }

    /**
     * Получает цвет переднего плана из конфига
     */
    protected function getForegroundColor(): Color
    {
        // ✅ Проверяем, что массив существует и имеет нужные индексы
        $color = $this->config['foreground'] ?? [0, 0, 0];
        
        // Проверяем, что это массив и имеет 3 элемента
        if (!is_array($color) || count($color) < 3) {
            return new Color(0, 0, 0);
        }
        
        return new Color($color[0] ?? 0, $color[1] ?? 0, $color[2] ?? 0);
    }

    /**
     * Получает цвет фона из конфига
     */
    protected function getBackgroundColor(): Color
    {
        // ✅ Проверяем, что массив существует и имеет нужные индексы
        $color = $this->config['background'] ?? [255, 255, 255];
        
        // Проверяем, что это массив и имеет 3 элемента
        if (!is_array($color) || count($color) < 3) {
            return new Color(255, 255, 255);
        }
        
        return new Color($color[0] ?? 255, $color[1] ?? 255, $color[2] ?? 255);
    }
}