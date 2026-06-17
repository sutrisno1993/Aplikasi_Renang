<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LevelModel;
use App\Models\AnakModel;
use App\Models\CoachModel;
use App\Models\UjianKenaikanModel;
use App\Models\SertifikatModel;

class Curriculum extends BaseController
{
    protected $levelModel;
    protected $anakModel;
    protected $coachModel;
    protected $ujianModel;
    protected $sertifikatModel;

    public function __construct()
    {
        $this->levelModel = new LevelModel();
        $this->anakModel = new AnakModel();
        $this->coachModel = new CoachModel();
        $this->ujianModel = new UjianKenaikanModel();
        $this->sertifikatModel = new SertifikatModel();
    }

    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $levels = $this->levelModel->getAllLevelsOrdered();
        
        // Fetch all children with their level and coach names
        $students = $this->anakModel->select('anak.*, swimming_levels.nama_level, coach.nama as nama_coach')
                                    ->join('swimming_levels', 'swimming_levels.id = anak.current_level_id', 'left')
                                    ->join('coach', 'coach.id = anak.assigned_coach_id', 'left')
                                    ->orderBy('anak.nama', 'ASC')
                                    ->findAll();

        $coaches = $this->coachModel->orderBy('nama', 'ASC')->findAll();

        $data = [
            'title' => 'Manajemen Kurikulum & Evaluasi',
            'active' => 'curriculum',
            'levels' => $levels,
            'students' => $students,
            'coaches' => $coaches
        ];

        return view('admin/curriculum/index', $data);
    }

    public function storeLevel()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $rules = [
            'nama_level' => 'required|min_length[3]',
            'deskripsi' => 'permit_empty',
            'urutan' => 'required|numeric'
        ];

        if ($this->validate($rules)) {
            $data = [
                'nama_level' => $this->request->getPost('nama_level'),
                'deskripsi' => $this->request->getPost('deskripsi'),
                'urutan' => (int) $this->request->getPost('urutan')
            ];

            $this->levelModel->insert($data);
            session()->setFlashdata('success', 'Level baru berhasil ditambahkan.');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan level: Validasi salah.');
        }

        return redirect()->to('admin/curriculum');
    }

    public function updateLevel($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $rules = [
            'nama_level' => 'required|min_length[3]',
            'deskripsi' => 'permit_empty',
            'urutan' => 'required|numeric'
        ];

        if ($this->validate($rules)) {
            $data = [
                'nama_level' => $this->request->getPost('nama_level'),
                'deskripsi' => $this->request->getPost('deskripsi'),
                'urutan' => (int) $this->request->getPost('urutan')
            ];

            $this->levelModel->update($id, $data);
            session()->setFlashdata('success', 'Level berhasil diperbarui.');
        } else {
            session()->setFlashdata('error', 'Gagal memperbarui level: Validasi salah.');
        }

        return redirect()->to('admin/curriculum');
    }

    public function deleteLevel($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $level = $this->levelModel->find($id);
        if ($level) {
            $this->levelModel->delete($id);
            session()->setFlashdata('success', 'Level berhasil dihapus.');
        } else {
            session()->setFlashdata('error', 'Level tidak ditemukan.');
        }

        return redirect()->to('admin/curriculum');
    }

    public function assign()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $anakId = (int) $this->request->getPost('anak_id');
        $levelId = $this->request->getPost('level_id');
        $coachId = $this->request->getPost('coach_id');

        if ($anakId <= 0) {
            session()->setFlashdata('error', 'Anak tidak ditemukan.');
            return redirect()->to('admin/curriculum');
        }

        $data = [];
        // Allow NULLing out level or coach if requested (e.g. empty string)
        $data['current_level_id'] = $levelId !== '' ? (int) $levelId : null;
        $data['assigned_coach_id'] = $coachId !== '' ? (int) $coachId : null;

        $this->anakModel->update($anakId, $data);
        session()->setFlashdata('success', 'Penugasan level & coach berhasil diperbarui.');

        return redirect()->to('admin/curriculum');
    }

    public function ujianList()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        // Fetch all active/completed level promotion exams
        $exams = $this->ujianModel->select('ujian_kenaikan.*, anak.nama as nama_anak, lvl_asal.nama_level as level_asal_nama, lvl_tujuan.nama_level as level_tujuan_nama, coach.nama as nama_examiner')
                                  ->join('anak', 'anak.id = ujian_kenaikan.anak_id')
                                  ->join('swimming_levels lvl_asal', 'lvl_asal.id = ujian_kenaikan.level_asal_id')
                                  ->join('swimming_levels lvl_tujuan', 'lvl_tujuan.id = ujian_kenaikan.level_tujuan_id')
                                  ->join('coach', 'coach.id = ujian_kenaikan.examiner_id', 'left')
                                  ->orderBy('ujian_kenaikan.tanggal', 'DESC')
                                  ->findAll();

        // Get planned exam date from query params, default to next week
        $examDate = $this->request->getGet('tanggal');
        if (empty($examDate)) {
            $examDate = date('Y-m-d', strtotime('+7 days'));
        }

        // Fetch required sessions from settings
        $settingModel = new \App\Models\SettingModel();
        $minSessionsSetting = $settingModel->getSetting('min_sessions_for_exam');
        $minSessions = $minSessionsSetting ? (int) $minSessionsSetting['value'] : 12;

        // Fetch students to recommend
        $rawStudents = $this->anakModel->select('anak.*, swimming_levels.nama_level')
                                    ->join('swimming_levels', 'swimming_levels.id = anak.current_level_id')
                                    ->orderBy('anak.nama', 'ASC')
                                    ->findAll();

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

        $coaches = $this->coachModel->orderBy('nama', 'ASC')->findAll();

        $data = [
            'title' => 'Ujian Kenaikan Tingkat (Admin)',
            'active' => 'curriculum',
            'exams' => $exams,
            'students' => $students,
            'coaches' => $coaches,
            'min_sessions' => $minSessions,
            'tanggal_ujian' => $examDate
        ];

        return view('admin/curriculum/ujian', $data);
    }

    public function rekomendasiUjian()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $anakId = (int) $this->request->getPost('anak_id');
        $examinerId = (int) $this->request->getPost('examiner_id');
        $examDate = $this->request->getPost('tanggal');

        if (empty($examDate)) {
            $examDate = date('Y-m-d', strtotime('+7 days'));
        }

        $student = $this->anakModel->find($anakId);
        if (!$student || empty($student['current_level_id'])) {
            session()->setFlashdata('error', 'Siswa tidak ditemukan atau belum memiliki level.');
            return redirect()->to('admin/curriculum/ujian');
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
            return redirect()->to('admin/curriculum/ujian?tanggal=' . $examDate);
        }

        $currentLevel = $this->levelModel->find($student['current_level_id']);
        $nextLevel = $this->levelModel->where('urutan >', $currentLevel['urutan'])
                                      ->orderBy('urutan', 'ASC')
                                      ->first();

        if (!$nextLevel) {
            session()->setFlashdata('error', 'Siswa sudah berada di level tertinggi.');
            return redirect()->to('admin/curriculum/ujian');
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
        session()->setFlashdata('success', 'Ujian Kenaikan Tingkat berhasil dijadwalkan.');
        return redirect()->to('admin/curriculum/ujian?tanggal=' . $examDate);
    }

    public function evaluasiUjian($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $exam = $this->ujianModel->select('ujian_kenaikan.*, anak.nama as nama_anak, lvl_asal.nama_level as level_asal_nama, lvl_tujuan.nama_level as level_tujuan_nama')
                                  ->join('anak', 'anak.id = ujian_kenaikan.anak_id')
                                  ->join('swimming_levels lvl_asal', 'lvl_asal.id = ujian_kenaikan.level_asal_id')
                                  ->join('swimming_levels lvl_tujuan', 'lvl_tujuan.id = ujian_kenaikan.level_tujuan_id')
                                  ->where('ujian_kenaikan.id', $id)
                                  ->first();

        if (!$exam) {
            session()->setFlashdata('error', 'Data ujian tidak ditemukan.');
            return redirect()->to('admin/curriculum/ujian');
        }

        $coaches = $this->coachModel->orderBy('nama', 'ASC')->findAll();

        $data = [
            'title' => 'Penilaian Ujian Kenaikan Tingkat',
            'active' => 'curriculum',
            'exam' => $exam,
            'coaches' => $coaches
        ];

        return view('admin/curriculum/penilaian_ujian', $data);
    }

    public function evaluasiUjianStore()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $examId = (int) $this->request->getPost('exam_id');
        $exam = $this->ujianModel->find($examId);

        if (!$exam) {
            session()->setFlashdata('error', 'Data ujian tidak ditemukan.');
            return redirect()->to('admin/curriculum/ujian');
        }

        $rules = [
            'status_kelulusan' => 'required',
            'examiner_id' => 'required',
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
                'examiner_id' => (int) $this->request->getPost('examiner_id'),
                'teknik_kaki' => $this->request->getPost('teknik_kaki'),
                'teknik_tangan' => $this->request->getPost('teknik_tangan'),
                'teknik_pernapasan' => $this->request->getPost('teknik_pernapasan'),
                'keberanian' => $this->request->getPost('keberanian'),
                'disiplin' => $this->request->getPost('disiplin'),
                'sikap_fokus' => $this->request->getPost('sikap_fokus'),
                'tournament_name' => $this->request->getPost('tournament_name'),
                'prestasi' => $this->request->getPost('prestasi'),
                'catatan_evaluasi' => $this->request->getPost('catatan_evaluasi'),
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
                session()->setFlashdata('success', 'Penilaian ujian kenaikan tingkat berhasil disimpan.');
            } catch (\Throwable $e) {
                $db->transRollback();
                session()->setFlashdata('error', 'Gagal memproses penilaian: ' . $e->getMessage());
            }
        } else {
            session()->setFlashdata('error', 'Gagal menyimpan penilaian. Lengkapi data.');
        }

        return redirect()->to('admin/curriculum/ujian');
    }
}
