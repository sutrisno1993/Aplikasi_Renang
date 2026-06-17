<?php

namespace App\Controllers\Coach;

use App\Controllers\BaseController;
use App\Models\AnakModel;
use App\Models\LevelModel;
use App\Models\EvaluasiMingguanModel;
use App\Models\UjianKenaikanModel;
use App\Models\SertifikatModel;
use App\Models\CoachModel;

class Evaluasi extends BaseController
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
        if (!session()->get('coach_isLoggedIn')) {
            return false;
        }
        return true;
    }

    public function index()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/coach/login');
        }

        $coachId = session()->get('coach_id');
        $coach = $this->coachModel->find($coachId);

        // Get assigned students (directly assigned OR in levels they are responsible for)
        $builder = $this->anakModel->select('anak.*, swimming_levels.nama_level')
                                   ->join('swimming_levels', 'swimming_levels.id = anak.current_level_id', 'left');
        
        $builder->groupStart()
                ->where('anak.assigned_coach_id', $coachId);
        if (!empty($coach['assigned_levels'])) {
            $levelIds = explode(',', $coach['assigned_levels']);
            $builder->orWhereIn('anak.current_level_id', $levelIds);
        }
        $builder->groupEnd();

        $students = $builder->orderBy('anak.nama', 'ASC')->findAll();

        // Get recent evaluations by this coach
        $evaluations = $this->evalModel->select('evaluasi_mingguan.*, anak.nama as nama_anak, swimming_levels.nama_level')
                                       ->join('anak', 'anak.id = evaluasi_mingguan.anak_id')
                                       ->join('swimming_levels', 'swimming_levels.id = evaluasi_mingguan.level_id')
                                       ->where('evaluasi_mingguan.coach_id', $coachId)
                                       ->orderBy('evaluasi_mingguan.tanggal', 'DESC')
                                       ->findAll(50);

        $data = [
            'title' => 'Evaluasi Mingguan',
            'coach' => $coach,
            'students' => $students,
            'evaluations' => $evaluations
        ];

        return view('coach/evaluasi/index', $data);
    }

    public function input($anakId)
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/coach/login');
        }

        $coachId = session()->get('coach_id');
        $coach = $this->coachModel->find($coachId);

        $builder = $this->anakModel->where('anak.id', $anakId);
        $builder->groupStart()
                ->where('anak.assigned_coach_id', $coachId);
        if (!empty($coach['assigned_levels'])) {
            $levelIds = explode(',', $coach['assigned_levels']);
            $builder->orWhereIn('anak.current_level_id', $levelIds);
        }
        $builder->groupEnd();

        $student = $builder->first();

        if (!$student) {
            session()->setFlashdata('error', 'Siswa tidak ditemukan atau bukan anak didik Anda.');
            return redirect()->to('coach/evaluasi');
        }

        if (empty($student['current_level_id'])) {
            session()->setFlashdata('error', 'Siswa belum memiliki tingkatan level. Hubungi Admin.');
            return redirect()->to('coach/evaluasi');
        }

        $level = $this->levelModel->find($student['current_level_id']);

        $data = [
            'title' => 'Input Evaluasi Perkembangan',
            'student' => $student,
            'level' => $level
        ];

        return view('coach/evaluasi/input', $data);
    }

    public function store()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/coach/login');
        }

        $coachId = session()->get('coach_id');
        $coach = $this->coachModel->find($coachId);
        $anakId = (int) $this->request->getPost('anak_id');

        $builder = $this->anakModel->where('anak.id', $anakId);
        $builder->groupStart()
                ->where('anak.assigned_coach_id', $coachId);
        if (!empty($coach['assigned_levels'])) {
            $levelIds = explode(',', $coach['assigned_levels']);
            $builder->orWhereIn('anak.current_level_id', $levelIds);
        }
        $builder->groupEnd();

        $student = $builder->first();

        if (!$student) {
            session()->setFlashdata('error', 'Siswa tidak ditemukan.');
            return redirect()->to('coach/evaluasi');
        }

        $rules = [
            'teknik_kaki' => 'required',
            'teknik_tangan' => 'required',
            'teknik_pernapasan' => 'required',
            'keberanian' => 'required',
            'disiplin' => 'required',
            'sikap_fokus' => 'required',
            'catatan_coach' => 'permit_empty'
        ];

        if ($this->validate($rules)) {
            $data = [
                'anak_id' => $anakId,
                'coach_id' => $coachId,
                'level_id' => $student['current_level_id'],
                'tanggal' => date('Y-m-d'),
                'teknik_kaki' => $this->request->getPost('teknik_kaki'),
                'teknik_tangan' => $this->request->getPost('teknik_tangan'),
                'teknik_pernapasan' => $this->request->getPost('teknik_pernapasan'),
                'keberanian' => $this->request->getPost('keberanian'),
                'disiplin' => $this->request->getPost('disiplin'),
                'sikap_fokus' => $this->request->getPost('sikap_fokus'),
                'catatan_coach' => $this->request->getPost('catatan_coach')
            ];

            $this->evalModel->insert($data);
            session()->setFlashdata('success', 'Evaluasi perkembangan mingguan berhasil disimpan.');
        } else {
            session()->setFlashdata('error', 'Gagal menyimpan evaluasi. Harap lengkapi form.');
        }

        return redirect()->to('coach/evaluasi');
    }

    public function ujianList()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/coach/login');
        }

        $coachId = session()->get('coach_id');
        $coach = $this->coachModel->find($coachId);

        // Get planned exam date from query params, default to next week
        $examDate = $this->request->getGet('tanggal');
        if (empty($examDate)) {
            $examDate = date('Y-m-d', strtotime('+7 days'));
        }

        // Fetch required sessions from settings
        $settingModel = new \App\Models\SettingModel();
        $minSessionsSetting = $settingModel->getSetting('min_sessions_for_exam');
        $minSessions = $minSessionsSetting ? (int) $minSessionsSetting['value'] : 12;

        // Fetch assigned students for the recommendation drop down (directly assigned OR in levels they are responsible for)
        $builder = $this->anakModel->select('anak.*, swimming_levels.nama_level')
                                    ->join('swimming_levels', 'swimming_levels.id = anak.current_level_id');
        
        $builder->groupStart()
                ->where('anak.assigned_coach_id', $coachId);
        if (!empty($coach['assigned_levels'])) {
            $levelIds = explode(',', $coach['assigned_levels']);
            $builder->orWhereIn('anak.current_level_id', $levelIds);
        }
        $builder->groupEnd();

        $rawStudents = $builder->orderBy('anak.nama', 'ASC')->findAll();

        $students = [];
        foreach ($rawStudents as $s) {
            $eligibility = $this->anakModel->checkExamEligibility($s['id'], $examDate, $minSessions);

            $s['attended_sessions'] = $eligibility['attended_sessions'];
            $s['min_sessions'] = $eligibility['min_sessions'];
            $s['is_eligible'] = $eligibility['is_eligible'];
            $s['start_date'] = $eligibility['start_date'];
            $s['end_date'] = $eligibility['end_date'];

            $students[] = $s;
        }

        // Fetch exam history submitted by this coach
        $myRecommendations = $this->ujianModel->select('ujian_kenaikan.*, anak.nama as nama_anak, lvl_asal.nama_level as level_asal_nama, lvl_tujuan.nama_level as level_tujuan_nama')
                                               ->join('anak', 'anak.id = ujian_kenaikan.anak_id')
                                               ->join('swimming_levels lvl_asal', 'lvl_asal.id = ujian_kenaikan.level_asal_id')
                                               ->join('swimming_levels lvl_tujuan', 'lvl_tujuan.id = ujian_kenaikan.level_tujuan_id')
                                               ->where('anak.assigned_coach_id', $coachId)
                                               ->orderBy('ujian_kenaikan.tanggal', 'DESC')
                                               ->findAll();

        // If head_coach, fetch ALL pending exams
        $pendingExams = [];
        if ($coach['role'] === 'head_coach') {
            $pendingExams = $this->ujianModel->select('ujian_kenaikan.*, anak.nama as nama_anak, lvl_asal.nama_level as level_asal_nama, lvl_tujuan.nama_level as level_tujuan_nama, coach.nama as nama_coach')
                                             ->join('anak', 'anak.id = ujian_kenaikan.anak_id')
                                             ->join('swimming_levels lvl_asal', 'lvl_asal.id = ujian_kenaikan.level_asal_id')
                                             ->join('swimming_levels lvl_tujuan', 'lvl_tujuan.id = ujian_kenaikan.level_tujuan_id')
                                             ->join('coach', 'coach.id = anak.assigned_coach_id', 'left')
                                             ->where('ujian_kenaikan.status_kelulusan', 'pending')
                                             ->orderBy('ujian_kenaikan.tanggal', 'ASC')
                                             ->findAll();
        }

        $data = [
            'title' => 'Ujian Kenaikan Tingkat',
            'coach' => $coach,
            'students' => $students,
            'myRecommendations' => $myRecommendations,
            'pendingExams' => $pendingExams,
            'min_sessions' => $minSessions,
            'tanggal_ujian' => $examDate
        ];

        return view('coach/evaluasi/ujian', $data);
    }

    public function rekomendasiUjian()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/coach/login');
        }

        $coachId = session()->get('coach_id');
        $coach = $this->coachModel->find($coachId);
        $anakId = (int) $this->request->getPost('anak_id');
        $examDate = $this->request->getPost('tanggal');

        if (empty($examDate)) {
            $examDate = date('Y-m-d', strtotime('+7 days'));
        }

        $builder = $this->anakModel->where('anak.id', $anakId);
        $builder->groupStart()
                ->where('anak.assigned_coach_id', $coachId);
        if (!empty($coach['assigned_levels'])) {
            $levelIds = explode(',', $coach['assigned_levels']);
            $builder->orWhereIn('anak.current_level_id', $levelIds);
        }
        $builder->groupEnd();

        $student = $builder->first();

        if (!$student) {
            session()->setFlashdata('error', 'Siswa tidak ditemukan.');
            return redirect()->to('coach/ujian');
        }

        if (empty($student['current_level_id'])) {
            session()->setFlashdata('error', 'Siswa belum memiliki tingkatan level awal.');
            return redirect()->to('coach/ujian');
        }

        // Fetch settings
        $settingModel = new \App\Models\SettingModel();
        $minSessionsSetting = $settingModel->getSetting('min_sessions_for_exam');
        $minSessions = $minSessionsSetting ? (int) $minSessionsSetting['value'] : 12;

        // Perform backend lookback check
        $eligibility = $this->anakModel->checkExamEligibility($anakId, $examDate, $minSessions);
        if (!$eligibility['is_eligible']) {
            session()->setFlashdata('error', sprintf(
                'Siswa %s tidak memenuhi syarat ujian. Jumlah kehadiran: %d/%d pada periode 3 bulan terakhir (%s s.d. %s).',
                $student['nama'],
                $eligibility['attended_sessions'],
                $minSessions,
                date('d/m/Y', strtotime($eligibility['start_date'])),
                date('d/m/Y', strtotime($eligibility['end_date']))
            ));
            return redirect()->to('coach/ujian?tanggal=' . $examDate);
        }

        $currentLevel = $this->levelModel->find($student['current_level_id']);
        
        // Find next level in terms of sequence
        $nextLevel = $this->levelModel->where('urutan >', $currentLevel['urutan'])
                                      ->orderBy('urutan', 'ASC')
                                      ->first();

        if (!$nextLevel) {
            session()->setFlashdata('error', 'Siswa sudah berada di level tertinggi.');
            return redirect()->to('coach/ujian');
        }

        // Find Head Coach to set as default examiner
        $headCoach = $this->coachModel->where('role', 'head_coach')->first();
        $examinerId = $headCoach ? $headCoach['id'] : $coachId;

        // Check if there is already a pending recommendation for this student
        $existing = $this->ujianModel->where('anak_id', $anakId)
                                     ->where('status_kelulusan', 'pending')
                                     ->first();

        if ($existing) {
            session()->setFlashdata('error', 'Siswa ini sudah direkomendasikan dan sedang menunggu ujian.');
            return redirect()->to('coach/ujian');
        }

        $data = [
            'anak_id' => $anakId,
            'level_asal_id' => $student['current_level_id'],
            'level_tujuan_id' => $nextLevel['id'],
            'examiner_id' => $examinerId,
            'tanggal' => $examDate,
            'status_kelulusan' => 'pending',
            'teknik_kaki' => 'B',
            'teknik_tangan' => 'B',
            'teknik_pernapasan' => 'B',
            'keberanian' => 'B',
            'disiplin' => 'B',
            'sikap_fokus' => 'B'
        ];

        $this->ujianModel->insert($data);
        session()->setFlashdata('success', 'Rekomendasi ujian kenaikan tingkat berhasil dikirim ke Head Coach.');
        return redirect()->to('coach/ujian?tanggal=' . $examDate);
    }

    public function evaluasiUjian($id)
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/coach/login');
        }

        $coachId = session()->get('coach_id');
        $coach = $this->coachModel->find($coachId);

        if ($coach['role'] !== 'head_coach') {
            session()->setFlashdata('error', 'Hanya Head Coach yang dapat menilai ujian kenaikan tingkat.');
            return redirect()->to('coach/ujian');
        }

        $exam = $this->ujianModel->select('ujian_kenaikan.*, anak.nama as nama_anak, lvl_asal.nama_level as level_asal_nama, lvl_tujuan.nama_level as level_tujuan_nama')
                                  ->join('anak', 'anak.id = ujian_kenaikan.anak_id')
                                  ->join('swimming_levels lvl_asal', 'lvl_asal.id = ujian_kenaikan.level_asal_id')
                                  ->join('swimming_levels lvl_tujuan', 'lvl_tujuan.id = ujian_kenaikan.level_tujuan_id')
                                  ->where('ujian_kenaikan.id', $id)
                                  ->first();

        if (!$exam) {
            session()->setFlashdata('error', 'Rekomendasi ujian tidak ditemukan.');
            return redirect()->to('coach/ujian');
        }

        $data = [
            'title' => 'Penilaian Ujian Kenaikan Tingkat',
            'exam' => $exam
        ];

        return view('coach/evaluasi/penilaian_ujian', $data);
    }

    public function evaluasiUjianStore()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/coach/login');
        }

        $coachId = session()->get('coach_id');
        $coach = $this->coachModel->find($coachId);

        if ($coach['role'] !== 'head_coach') {
            session()->setFlashdata('error', 'Hanya Head Coach yang dapat menilai.');
            return redirect()->to('coach/ujian');
        }

        $examId = (int) $this->request->getPost('exam_id');
        $exam = $this->ujianModel->find($examId);

        if (!$exam) {
            session()->setFlashdata('error', 'Data ujian tidak ditemukan.');
            return redirect()->to('coach/ujian');
        }

        $rules = [
            'status_kelulusan' => 'required',
            'teknik_kaki' => 'required',
            'teknik_tangan' => 'required',
            'teknik_pernapasan' => 'required',
            'keberanian' => 'required',
            'disiplin' => 'required',
            'sikap_fokus' => 'required',
            'tournament_name' => 'permit_empty',
            'prestasi' => 'permit_empty',
            'catatan_evaluasi' => 'permit_empty'
        ];

        if ($this->validate($rules)) {
            $statusKelulusan = $this->request->getPost('status_kelulusan');
            
            $data = [
                'status_kelulusan' => $statusKelulusan,
                'teknik_kaki' => $this->request->getPost('teknik_kaki'),
                'teknik_tangan' => $this->request->getPost('teknik_tangan'),
                'teknik_pernapasan' => $this->request->getPost('teknik_pernapasan'),
                'keberanian' => $this->request->getPost('keberanian'),
                'disiplin' => $this->request->getPost('disiplin'),
                'sikap_fokus' => $this->request->getPost('sikap_fokus'),
                'tournament_name' => $this->request->getPost('tournament_name'),
                'prestasi' => $this->request->getPost('prestasi'),
                'catatan_evaluasi' => $this->request->getPost('catatan_evaluasi'),
                'examiner_id' => $coachId,
                'tanggal' => date('Y-m-d')
            ];

            $db = \Config\Database::connect();
            $db->transStart();

            try {
                $this->ujianModel->update($examId, $data);

                if ($statusKelulusan === 'lulus') {
                    // Update student level
                    $this->anakModel->update($exam['anak_id'], [
                        'current_level_id' => $exam['level_tujuan_id']
                    ]);

                    // Generate digital certificate
                    $certNumber = $this->sertifikatModel->generateCertificateNumber($exam['anak_id'], $exam['level_tujuan_id']);
                    $this->sertifikatModel->insert([
                        'ujian_id' => $examId,
                        'anak_id' => $exam['anak_id'],
                        'level_id' => $exam['level_tujuan_id'],
                        'nomor_sertifikat' => $certNumber
                    ]);
                }

                $db->transComplete();
                session()->setFlashdata('success', 'Penilaian ujian berhasil disimpan.');
            } catch (\Throwable $e) {
                $db->transRollback();
                session()->setFlashdata('error', 'Gagal memproses penilaian: ' . $e->getMessage());
            }
        } else {
            session()->setFlashdata('error', 'Gagal menyimpan penilaian. Lengkapi data.');
        }

        return redirect()->to('coach/ujian');
    }

    public function coachList()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/coach/login');
        }

        $coachId = session()->get('coach_id');
        $currentCoach = $this->coachModel->find($coachId);

        if ($currentCoach['role'] !== 'head_coach') {
            session()->setFlashdata('error', 'Akses ditolak. Hanya Head Coach yang dapat mengelola pelatih.');
            return redirect()->to('coach/evaluasi');
        }

        $levelModel = new \App\Models\LevelModel();
        $levels = $levelModel->orderBy('urutan', 'ASC')->findAll();

        $data = [
            'title' => 'Kelola Pelatih (Head Coach)',
            'coach' => $currentCoach,
            'coaches' => $this->coachModel->findAll(),
            'levels' => $levels
        ];

        return view('coach/evaluasi/pelatih_list', $data);
    }

    public function coachUpdate($id)
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/coach/login');
        }

        $coachId = session()->get('coach_id');
        $currentCoach = $this->coachModel->find($coachId);

        if ($currentCoach['role'] !== 'head_coach') {
            session()->setFlashdata('error', 'Akses ditolak. Hanya Head Coach yang dapat mengelola pelatih.');
            return redirect()->to('coach/evaluasi');
        }

        $targetCoach = $this->coachModel->find($id);
        if (!$targetCoach) {
            session()->setFlashdata('error', 'Data pelatih tidak ditemukan.');
            return redirect()->to('coach/pelatih');
        }

        $assignedLevels = $this->request->getPost('assigned_levels');
        $assignedLevelsStr = !empty($assignedLevels) ? implode(',', $assignedLevels) : null;

        $data = [
            'id' => $id,
            'assigned_levels' => $assignedLevelsStr
        ];

        if ($this->coachModel->save($data)) {
            session()->setFlashdata('success', 'Tanggung jawab level pelatih ' . esc($targetCoach['nama']) . ' berhasil diperbarui.');
        } else {
            session()->setFlashdata('error', 'Gagal memperbarui tanggung jawab pelatih.');
        }

        return redirect()->to('coach/pelatih');
    }
}
