<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    protected SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    /**
     * API Filter Wilayah BA BUN (TKD Aceh) untuk AJAX Request
     */
    public function filterBunWilayah(Request $request): JsonResponse
    {
        $code = $request->query('code', 'ALL');
        $bunMasterData = $this->supabase->getBunRegionalData();

        $selected = $bunMasterData[$code] ?? $bunMasterData['ALL'];

        return response()->json([
            'status' => 'success',
            'code' => $code,
            'data' => $selected
        ]);
    }

    /**
     * API Autentikasi PIN Administrator (123456)
     */
    public function verifyAdmin(Request $request): JsonResponse
    {
        $pin = $request->input('pin', '');

        if ($pin === '123456') {
            return response()->json([
                'status' => 'success',
                'message' => 'Autentikasi Administrator Berhasil.',
                'officer' => 'SYAHRIAL (KANWIL - KD1)',
                'sync_status' => 'Data Terpadu Kanwil DJPb Provinsi Aceh - Realtime Live Sync'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'PIN Administrator tidak valid.'
        ], 401);
    }
}
