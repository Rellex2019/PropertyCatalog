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

    public function generate(Request $request)
    {
        try {
            $request->validate([
                'data' => 'required|string|max:2048',
            ]);

            $data = $request->input('data');
            
            Log::info('Generating QR for URL: ' . $data);
            
            $qrCode = $this->qrService->generate($data, 300);

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
}