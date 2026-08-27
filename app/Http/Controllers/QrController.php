<?php

namespace App\Http\Controllers;

use App\Services\QRCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QRController extends Controller
{
    protected QRCodeService $qrService;

    public function __construct(QRCodeService $qrService)
    {
        $this->qrService = $qrService;
    }

    /**
     * Генерация QR-кода (возвращает base64)
     */
    public function generate(Request $request)
    {
        try {
            $request->validate([
                'data' => 'required|string|max:2048',
                'size' => 'nullable|integer|min:100|max:1000',
            ]);

            $data = $request->input('data');
            $size = $request->input('size', 300);
            
            Log::info('Generating QR for URL: ' . $data);
            
            $qrCode = $this->qrService->generate($data, $size);

            return response()->json([
                'success' => true,
                'qrCode' => $qrCode
            ]);

        } catch (\Exception $e) {
            Log::error('QR generation error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Не удалось сгенерировать QR-код: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Скачивание QR-кода в формате PNG
     */
    public function download(Request $request)
    {
        try {
            $request->validate([
                'data' => 'required|string|max:2048',
                'size' => 'nullable|integer|min:100|max:1000',
            ]);

            $data = $request->input('data');
            $size = $request->input('size', 500);
            
            Log::info('Downloading QR for URL: ' . $data);
            
            // Получаем бинарные данные QR-кода
            $qrData = $this->qrService->generateForDownload($data, $size);

            // Генерируем имя файла
            $filename = 'qrcode_' . date('Y-m-d_H-i-s') . '.png';

            return response($qrData)
                ->header('Content-Type', 'image/png')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Content-Length', strlen($qrData));

        } catch (\Exception $e) {
            Log::error('QR download error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Не удалось скачать QR-код: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Скачивание QR-кода с логотипом
     */
    public function downloadWithLogo(Request $request)
    {
        try {
            $request->validate([
                'data' => 'required|string|max:2048',
                'logo' => 'required|image|max:2048', // Логотип загружается файлом
                'size' => 'nullable|integer|min:100|max:1000',
            ]);

            $data = $request->input('data');
            $size = $request->input('size', 500);
            
            // Сохраняем логотип временно
            $logoPath = $request->file('logo')->store('temp', 'public');
            $fullLogoPath = storage_path('app/public/' . $logoPath);
            
            // Генерируем QR с логотипом
            $qrData = $this->qrService->generateWithLogo($data, $fullLogoPath, $size);
            
            // Удаляем временный файл
            \Illuminate\Support\Facades\Storage::disk('public')->delete($logoPath);

            $filename = 'qrcode_with_logo_' . date('Y-m-d_H-i-s') . '.png';

            return response($qrData)
                ->header('Content-Type', 'image/png')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            Log::error('QR download with logo error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Не удалось скачать QR-код с логотипом: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получение QR-кода для отображения в HTML (inline)
     */
    public function inline(Request $request)
    {
        try {
            $request->validate([
                'data' => 'required|string|max:2048',
                'size' => 'nullable|integer|min:100|max:1000',
            ]);

            $data = $request->input('data');
            $size = $request->input('size', 300);
            
            $qrCode = $this->qrService->generate($data, $size);

            return response()->json([
                'success' => true,
                'qrCode' => $qrCode
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}