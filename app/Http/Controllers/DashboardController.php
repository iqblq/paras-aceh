<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    /**
     * Tampilan Utama Executive Dashboard Kanwil DJPb Provinsi Aceh
     */
    public function index(): View
    {
        $kpi = $this->supabase->getExecutiveKpi();
        $kppnRanking = $this->supabase->getKppnRegionalRanking();
        $satkerKl = $this->supabase->getSatkerKlSummary();
        $topKl = $this->supabase->getTop10Kl();
        $bunData = $this->supabase->getBunRegionalData();
        $jenisBelanjaChart = $this->supabase->getJenisBelanjaChart();

        return view('dashboard.index', compact('kpi', 'kppnRanking', 'satkerKl', 'topKl', 'bunData', 'jenisBelanjaChart'));
    }
}
