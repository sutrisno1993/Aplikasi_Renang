<?php

namespace App\Controllers\Parent;

use App\Controllers\BaseController;
use App\Models\AnakModel;
use App\Models\LevelModel;
use App\Models\EvaluasiMingguanModel;
use App\Models\UjianKenaikanModel;
use App\Models\SertifikatModel;
use App\Models\CoachModel;

class Curriculum extends BaseController
{
    protected $anakModel;
    protected $levelModel;
    protected $evalModel;
    protected $ujianModel;
    protected $sertifikatModel;
    protected $coachModel;

    public function __construct()
    {
        $this->anakModel = new AnakModel();
        $this->levelModel = new LevelModel();
        $this->evalModel = new EvaluasiMingguanModel();
        $this->ujianModel = new UjianKenaikanModel();
        $this->sertifikatModel = new SertifikatModel();
        $this->coachModel = new CoachModel();
    }

    private function checkAuth()
    {
        if (!session()->get('parent_isLoggedIn')) {
            return false;
        }
        return true;
    }

    public function index()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/parent/login');
        }

        $parentId = session()->get('parent_id');

        // Fetch children with their level and coach names
        $children = $this->anakModel->select('anak.*, swimming_levels.nama_level, coach.nama as nama_coach')
                                    ->join('swimming_levels', 'swimming_levels.id = anak.current_level_id', 'left')
                                    ->join('coach', 'coach.id = anak.assigned_coach_id', 'left')
                                    ->where('anak.parent_id', $parentId)
                                    ->findAll();

        $dataChildren = [];
        foreach ($children as $c) {
            $evaluations = $this->evalModel->getEvaluationsByAnakId($c['id']);
            $exams = $this->ujianModel->getExamsByAnakId($c['id']);
            
            // Get all certificates for this child
            $certificates = $this->sertifikatModel->select('sertifikat_digital.*, swimming_levels.nama_level')
                                                   ->join('swimming_levels', 'swimming_levels.id = sertifikat_digital.level_id')
                                                   ->where('sertifikat_digital.anak_id', $c['id'])
                                                   ->findAll();

            $dataChildren[] = [
                'child' => $c,
                'evaluations' => $evaluations,
                'exams' => $exams,
                'certificates' => $certificates
            ];
        }

        $data = [
            'title' => 'Kurikulum & Evaluasi Anak',
            'children' => $dataChildren
        ];

        return view('parent/curriculum/index', $data);
    }

    public function downloadCertificate($anakId, $levelId)
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/parent/login');
        }

        $parentId = session()->get('parent_id');
        
        // Verify child belongs to parent
        $child = $this->anakModel->where('id', $anakId)
                                 ->where('parent_id', $parentId)
                                 ->first();

        if (!$child) {
            session()->setFlashdata('error', 'Siswa tidak ditemukan.');
            return redirect()->to('parent/curriculum');
        }

        $certificate = $this->sertifikatModel->select('sertifikat_digital.*, swimming_levels.nama_level, ujian_kenaikan.tanggal as tanggal_lulus, ujian_kenaikan.tournament_name, ujian_kenaikan.prestasi')
                                             ->join('swimming_levels', 'swimming_levels.id = sertifikat_digital.level_id')
                                             ->join('ujian_kenaikan', 'ujian_kenaikan.id = sertifikat_digital.ujian_id')
                                             ->where('sertifikat_digital.anak_id', $anakId)
                                             ->where('sertifikat_digital.level_id', $levelId)
                                             ->first();

        if (!$certificate) {
            session()->setFlashdata('error', 'Sertifikat tidak ditemukan.');
            return redirect()->to('parent/curriculum');
        }

        // Find Head Coach
        $headCoach = $this->coachModel->where('role', 'head_coach')->first();
        $headCoachName = $headCoach ? $headCoach['nama'] : 'Heri Setiawan';

        $data = [
            'title' => 'Sertifikat Digital ' . $child['nama'],
            'child' => $child,
            'cert' => $certificate,
            'headCoachName' => $headCoachName
        ];

        return view('certificate/print', $data);
    }

    public function downloadRaport($anakId, $levelId)
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/parent/login');
        }

        $parentId = session()->get('parent_id');
        
        // Verify child belongs to parent
        $child = $this->anakModel->where('id', $anakId)
                                 ->where('parent_id', $parentId)
                                 ->first();

        if (!$child) {
            session()->setFlashdata('error', 'Siswa tidak ditemukan.');
            return redirect()->to('parent/curriculum');
        }

        $certificate = $this->sertifikatModel->select('sertifikat_digital.*, swimming_levels.nama_level, swimming_levels.deskripsi as level_deskripsi, ujian_kenaikan.id as ujian_id, ujian_kenaikan.tanggal as tanggal_lulus, ujian_kenaikan.tournament_name, ujian_kenaikan.prestasi, ujian_kenaikan.catatan_evaluasi, ujian_kenaikan.teknik_kaki, ujian_kenaikan.teknik_tangan, ujian_kenaikan.teknik_pernapasan, ujian_kenaikan.keberanian, ujian_kenaikan.disiplin, ujian_kenaikan.sikap_fokus, coach.nama as nama_penguji')
                                             ->join('swimming_levels', 'swimming_levels.id = sertifikat_digital.level_id')
                                             ->join('ujian_kenaikan', 'ujian_kenaikan.id = sertifikat_digital.ujian_id')
                                             ->join('coach', 'coach.id = ujian_kenaikan.examiner_id', 'left')
                                             ->where('sertifikat_digital.anak_id', $anakId)
                                             ->where('sertifikat_digital.level_id', $levelId)
                                             ->first();

        if (!$certificate) {
            session()->setFlashdata('error', 'Raport tidak ditemukan.');
            return redirect()->to('parent/curriculum');
        }

        $data = [
            'title' => 'Raport Evaluasi ' . $child['nama'],
            'child' => $child,
            'cert' => $certificate
        ];

        return view('certificate/raport', $data);
    }
}
