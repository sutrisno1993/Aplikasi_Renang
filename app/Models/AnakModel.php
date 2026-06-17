<?php

namespace App\Models;

use CodeIgniter\Model;

class AnakModel extends Model
{
    protected $table = 'anak';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'parent_id',
        'nama',
        'nama_panggilan',
        'asal_sekolah',
        'riwayat_penyakit',
        'tanggal_lahir',
        'jenis_kelamin',
        'jenis_les_id',
        'sisa_pertemuan',
        'foto',
        'status',
        'current_level_id',
        'assigned_coach_id',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    private const KUOTA_PER_PAKET = 4;

    public function getAnakWithJenisLesByParentId($parentId): array
    {
        $parentId = (int) $parentId;
        if ($parentId <= 0) {
            return [];
        }

        return $this->select('anak.*, jenis_les.nama_les')
            ->join('jenis_les', 'jenis_les.id = anak.jenis_les_id', 'left')
            ->where('anak.parent_id', $parentId)
            ->findAll();
    }

    public function getPesertaJadwal($jadwal_id): array
    {
        $jadwal_id = (int) $jadwal_id;
        if ($jadwal_id <= 0) {
            return [];
        }

        // Ambil data anak yang terdaftar di jadwal ini
        // Gunakan DISTINCT dan JOIN yang benar
        return $this->db->table('schedule_students ss')
            ->select('a.*, jl.nama_les as jenis_les_nama')
            ->join('anak a', 'a.id = ss.anak_id')
            ->join('jenis_les jl', 'jl.id = a.jenis_les_id', 'left')
            ->where('ss.schedule_id', $jadwal_id)
            ->groupBy('a.id')
            ->orderBy('MAX(ss.id)', 'ASC') // Urutkan berdasarkan ID pendaftaran
            ->get()
            ->getResultArray();
    }

    /**
     * Akuntansi per paket (FIFO) + hangus otomatis setelah berlaku_sampai.
     */
    public function getPaketBreakdown(int $anakId): array
    {
        $anakId = (int) $anakId;
        if ($anakId <= 0) {
            return $this->emptyBreakdown();
        }

        $db = \Config\Database::connect();
        $pembayaranModel = new PembayaranModel();
        $today = date('Y-m-d');

        $payments = $db->table('pembayaran')
            ->where('anak_id', $anakId)
            ->where('status', 'success')
            ->orderBy('tanggal', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        // Ambil data anak untuk mendapatkan jenis_les_id saat ini sebagai penentu tipe latihan
        $anak = $this->select('anak.*, jenis_les.nama_les as current_jenis_les')
            ->join('jenis_les', 'jenis_les.id = anak.jenis_les_id', 'left')
            ->find($anakId);
        
        $isAnakPrivate = str_contains(strtolower($anak['current_jenis_les'] ?? ''), 'private');

        // Ambil semua kehadiran (hadir)
        // Menurut instruksi: status reguler/private bersifat imutabel (snapshot saat absen)
        $hadir = $db->table('latihan_attendance la')
            ->select('la.*, s.tanggal, s.jam_mulai, s.jam_selesai, s.materi, jl.nama_les as snapshot_jenis_les')
            ->join('schedules s', 's.id = la.schedule_id', 'left')
            ->join('jenis_les jl', 'jl.id = la.jenis_les_id', 'left')
            ->where('la.anak_id', $anakId)
            ->where('la.status_kehadiran', 'hadir')
            ->orderBy('s.tanggal', 'ASC')
            ->orderBy('la.id', 'ASC')
            ->get()
            ->getResultArray();

        $packages = [];
        foreach ($payments as $index => $pay) {
            $standardExpired = $pembayaranModel->computeBerlakuSampaiFromPaymentDate($pay['tanggal'] ?? null);
            $berlaku = $pay['berlaku_sampai'] ?? $standardExpired;

            // Identifikasi tipe paket dari snapshot jenis_les_id di tabel pembayaran
            $tipePaket = 'reguler'; 
            if (!empty($pay['jenis_les_id'])) {
                $jLes = $db->table('jenis_les')->where('id', $pay['jenis_les_id'])->get()->getRowArray();
                if ($jLes && str_contains(strtolower($jLes['nama_les']), 'private')) {
                    $tipePaket = 'private';
                }
            }

            $packages[] = [
                'paket_ke' => $index + 1,
                'pembayaran_id' => (int) $pay['id'],
                'payment' => $pay,
                'tanggal_bayar' => $pay['tanggal'],
                'berlaku_sampai' => $berlaku,
                'kuota' => self::KUOTA_PER_PAKET,
                'terpakai' => 0,
                'sisa_aktif' => 0,
                'hangus' => 0,
                'is_expired' => false,
                'status_label' => 'aktif',
                'tipe_paket' => $tipePaket,
                'allocated' => [],
            ];
        }

        // --- ALGORITMA FIFO BERBASIS STATUS SISWA ---
        // Sesuai instruksi: "Status reguler dan private pada sesi latihan ditentukan sepenuhnya berdasarkan status siswa"
        
        $hadirCount = count($hadir);
        $hadirStatus = array_fill(0, $hadirCount, false);

        foreach ($packages as &$pkg) {
            $pkgBerlaku = $pkg['berlaku_sampai'] ? date('Y-m-d', strtotime($pkg['berlaku_sampai'])) : '9999-12-31';
            $isPkgExpired = ($pkgBerlaku < $today);
            
            foreach ($hadir as $hIdx => $h) {
                if ($hadirStatus[$hIdx]) continue; 
                if ($pkg['terpakai'] >= self::KUOTA_PER_PAKET) break;

                // Tipe kehadiran ditentukan oleh snapshot saat absen dicatat
                $tipeHadir = str_contains(strtolower($h['snapshot_jenis_les'] ?? ''), 'private') ? 'private' : 'reguler';
                
                // Hanya alokasikan jika tipe paket cocok dengan tipe kehadiran siswa
                if ($tipeHadir === $pkg['tipe_paket']) {
                    $canAllocate = false;
                    if (!$isPkgExpired) {
                        $canAllocate = true;
                    } else {
                        if (($h['tanggal'] ?? null) !== null && $h['tanggal'] <= $pkgBerlaku) {
                            $canAllocate = true;
                        }
                    }

                    if ($canAllocate) {
                        $session = $h;
                        $session['pertemuan_ke'] = $pkg['terpakai'] + 1;
                        $session['paket_ke'] = $pkg['paket_ke'];
                        $pkg['allocated'][] = $session;
                        $pkg['terpakai']++;
                        $hadirStatus[$hIdx] = true;
                    }
                }
            }
        }
        unset($pkg);

        $debtSessions = [];
        foreach ($hadir as $hIdx => $h) {
            if (!$hadirStatus[$hIdx]) {
                $h['tipe_nunggak'] = str_contains(strtolower($h['snapshot_jenis_les'] ?? ''), 'private') ? 'private' : 'reguler';
                $debtSessions[] = $h;
            }
        }
        $debt = count($debtSessions);

        $sisaTotal = 0;
        $hangusTotal = 0;
        $hasExpiredHangus = false;
        $berlakuAktifTerakhir = null;

        foreach ($packages as &$pkg) {
            $berlakuDate = $pkg['berlaku_sampai'] ? date('Y-m-d', strtotime($pkg['berlaku_sampai'])) : null;
            
            // Hitung tanggal 90 hari standar untuk perbandingan
            $standardExpired = $pembayaranModel->computeBerlakuSampaiFromPaymentDate($pkg['tanggal_bayar'] ?? null);
            $standardExpiredDate = $standardExpired ? date('Y-m-d', strtotime($standardExpired)) : null;

            if ($berlakuDate === '2026-08-17') {
                $pkg['is_expired'] = $standardExpiredDate && $standardExpiredDate < $today;
                $pkg['berlaku_sampai'] = $standardExpired; 
            } else {
                $pkg['is_expired'] = $berlakuDate && $berlakuDate < $today;
            }

            $belumTerpakai = self::KUOTA_PER_PAKET - $pkg['terpakai'];

            if ($pkg['is_expired']) {
                $pkg['hangus'] = max(0, $belumTerpakai);
                $pkg['sisa_aktif'] = 0;
                $pkg['status_label'] = $pkg['hangus'] > 0 ? 'expired' : ($pkg['terpakai'] >= self::KUOTA_PER_PAKET ? 'habis' : 'expired');
                if ($pkg['hangus'] > 0) {
                    $hasExpiredHangus = true;
                }
            } else {
                $pkg['hangus'] = 0;
                $pkg['sisa_aktif'] = max(0, $belumTerpakai);
                $pkg['status_label'] = $pkg['sisa_aktif'] === 0 ? 'habis' : 'aktif';
                $berlakuAktifTerakhir = $berlakuDate;
            }

            $hangusTotal += $pkg['hangus'];
            $sisaTotal += $pkg['sisa_aktif'];
        }
        unset($pkg);

        // Jika siswa memiliki minimal satu paket yang masih aktif (belum expired),
        // maka status kedaluwarsa siswa secara umum (has_expired_hangus) harus dinonaktifkan.
        // Hal ini dilakukan agar notifikasi/badge "Expired" dan "Pertemuan Hangus" tidak muncul 
        // ketika siswa sudah melakukan pembayaran baru untuk aktif latihan kembali.
        $hasActivePackage = false;
        foreach ($packages as $pkg) {
            if (!$pkg['is_expired']) {
                $hasActivePackage = true;
                break;
            }
        }
        if ($hasActivePackage) {
            $hasExpiredHangus = false;
        }

        $sisaTotal -= $debt;

        $historyGroups = $this->buildHistoryGroups($packages, $debtSessions);
        $detailSisa = $this->buildDetailSisaFromBreakdown($packages, $sisaTotal, $debt, $hadirCount, count($payments));

        $berlakuDisplay = $berlakuAktifTerakhir;
        if (!$berlakuDisplay && !empty($packages)) {
            $last = $packages[count($packages) - 1];
            $berlakuDisplay = $last['berlaku_sampai'] ? date('Y-m-d', strtotime($last['berlaku_sampai'])) : null;
        }

        return [
            'paket' => $packages,
            'history_groups' => $historyGroups,
            'sisa_total' => $sisaTotal,
            'debt' => $debt,
            'hangus_total' => $hangusTotal,
            'has_expired_hangus' => $hasExpiredHangus,
            'berlaku_sampai' => $berlakuDisplay,
            'is_expired_display' => $hasExpiredHangus,
            'total_hadir' => $hadirCount,
            'total_pembayaran_sukses' => count($payments),
            'detail_sisa' => $detailSisa,
        ];
    }

    private function emptyBreakdown(): array
    {
        return [
            'paket' => [],
            'history_groups' => [],
            'sisa_total' => 0,
            'debt' => 0,
            'hangus_total' => 0,
            'has_expired_hangus' => false,
            'berlaku_sampai' => null,
            'is_expired_display' => false,
            'total_hadir' => 0,
            'total_pembayaran_sukses' => 0,
            'detail_sisa' => [
                'sisa' => 0,
                'sisa_display' => 0,
                'paket_ke' => 1,
                'pertemuan_ke' => 0,
                'total_hadir' => 0,
                'total_bayar' => 0,
            ],
        ];
    }

    private function buildHistoryGroups(array $packages, array $debtSessions): array
    {
        $groups = [];

        foreach ($packages as $pkg) {
            $sessions = [];
            for ($slot = 1; $slot <= self::KUOTA_PER_PAKET; $slot++) {
                if (isset($pkg['allocated'][$slot - 1])) {
                    $sessions[$slot] = $pkg['allocated'][$slot - 1];
                } elseif ($pkg['is_expired'] && $slot > $pkg['terpakai']) {
                    $sessions[$slot] = ['slot_status' => 'hangus'];
                } else {
                    $sessions[$slot] = null;
                }
            }

            $label = 'Pembayaran ' . $pkg['paket_ke'];
            if (isset($pkg['tipe_paket'])) {
                $label .= ' (' . ucfirst($pkg['tipe_paket']) . ')';
            }

            $groups[] = [
                'label' => $label,
                'payment' => $pkg['payment'],
                'sessions' => $sessions,
                'berlaku_sampai' => $pkg['berlaku_sampai'],
                'is_expired' => $pkg['is_expired'],
                'hangus' => $pkg['hangus'],
                'sisa_aktif' => $pkg['sisa_aktif'],
                'terpakai' => $pkg['terpakai'],
                'status_label' => $pkg['status_label'],
                'tipe_paket' => $pkg['tipe_paket'] ?? 'reguler',
            ];
        }

        if (!empty($debtSessions)) {
            $sessions = [];
            foreach ($debtSessions as $idx => $session) {
                $sessions[$idx + 1] = $session;
            }
            $groups[] = [
                'label' => 'Nunggak (melebihi kuota paket)',
                'payment' => null,
                'sessions' => $sessions,
                'berlaku_sampai' => null,
                'is_expired' => false,
                'hangus' => 0,
                'sisa_aktif' => 0,
                'terpakai' => count($debtSessions),
                'status_label' => 'nunggak',
            ];
        }

        return $groups;
    }

    private function buildDetailSisaFromBreakdown(array $packages, int $sisaTotal, int $debt, int $totalHadir, int $totalBayar): array
    {
        $paketKe = 1;
        $pertemuanKe = 0;
        $activePaket = null;

        foreach ($packages as $pkg) {
            if (!$pkg['is_expired'] && $pkg['sisa_aktif'] > 0) {
                $activePaket = $pkg;
                break;
            }
        }

        if ($activePaket === null && !empty($packages)) {
            $activePaket = $packages[count($packages) - 1];
        }

        if ($activePaket) {
            $paketKe = $activePaket['paket_ke'];
            // Pertemuan Ke-X adalah jumlah yang sudah terpakai di paket tersebut
            $pertemuanKe = $activePaket['terpakai'];
        }

        if ($sisaTotal < 0) {
            $sisaDisplay = $sisaTotal;
            // Jika nunggak, tampilkan sebagai pertemuan ke-5 (sesuai permintaan user)
            $pertemuanKe = 5;
            $paketKe = $totalBayar > 0 ? $totalBayar : 1;
        } else {
            $sisaDisplay = $sisaTotal;
        }

        return [
            'sisa' => $sisaTotal,
            'sisa_display' => $sisaDisplay,
            'paket_ke' => $paketKe,
            'pertemuan_ke' => $pertemuanKe,
            'total_hadir' => $totalHadir,
            'total_bayar' => $totalBayar,
            'debt' => $debt,
        ];
    }

    /**
     * Apakah paket aktif sudah selesai (boleh pindah jenis les).
     *
     * @return array{paket_selesai: bool, message: string, sisa_pertemuan: int, debt: int}
     */
    public function getPaketSelesaiStatus(int $anakId): array
    {
        $anakId = (int) $anakId;
        if ($anakId <= 0) {
            return [
                'paket_selesai' => false,
                'message' => 'Data anak tidak valid.',
                'sisa_pertemuan' => 0,
                'debt' => 0,
            ];
        }

        $this->recalculateSisaPertemuan($anakId);
        $breakdown = $this->getPaketBreakdown($anakId);
        $sisa = (int) $breakdown['sisa_total'];
        $debt = (int) ($breakdown['debt'] ?? 0);

        if ($sisa > 0) {
            return [
                'paket_selesai' => false,
                'message' => 'Sisa pertemuan paket saat ini: ' . $sisa . '.',
                'sisa_pertemuan' => $sisa,
                'debt' => $debt,
            ];
        }

        if ($debt > 0) {
            return [
                'paket_selesai' => false,
                'message' => 'Masih ada ' . $debt . ' pertemuan nunggak (belum tertutup pembayaran).',
                'sisa_pertemuan' => $sisa,
                'debt' => $debt,
            ];
        }

        $pembayaranModel = new PembayaranModel();
        if ($pembayaranModel->hasPendingForAnak($anakId)) {
            return [
                'paket_selesai' => false,
                'message' => 'Masih ada pembayaran yang menunggu verifikasi admin.',
                'sisa_pertemuan' => $sisa,
                'debt' => $debt,
            ];
        }

        return [
            'paket_selesai' => true,
            'message' => '',
            'sisa_pertemuan' => $sisa,
            'debt' => $debt,
        ];
    }

    /**
     * Validasi pindah paket / ganti jenis les.
     *
     * @return array{allowed: bool, message: string}
     */
    public function validateTransferJenisLes(int $anakId, int $newJenisLesId, int $currentJenisLesId): array
    {
        if ($newJenisLesId === $currentJenisLesId) {
            return ['allowed' => true, 'message' => ''];
        }

        $status = $this->getPaketSelesaiStatus($anakId);
        if (!$status['paket_selesai']) {
            return [
                'allowed' => false,
                'message' => 'Pindah paket/jenis les hanya bisa dilakukan setelah paket lama selesai. ' . $status['message'],
            ];
        }

        return ['allowed' => true, 'message' => ''];
    }

    public function recalculateSisaPertemuan($anak_id)
    {
        $breakdown = $this->getPaketBreakdown((int) $anak_id);

        return $this->update($anak_id, ['sisa_pertemuan' => $breakdown['sisa_total']]);
    }

    /**
     * Satu pintu sinkronisasi kuota setelah ubah pembayaran / absensi.
     * - Hapus pasangan schedule_students jika absensi dihapus (opsional lewat $scheduleId)
     * - Hitung ulang sisa dari FIFO (sumber kebenaran)
     *
     * @return array{sisa_pertemuan: int, hangus_total: int, debt: int, message: string}
     */
    public function syncKuotaFromRiwayat(int $anakId, ?int $scheduleId = null): array
    {
        $anakId = (int) $anakId;
        if ($anakId <= 0) {
            return [
                'sisa_pertemuan' => 0,
                'hangus_total' => 0,
                'debt' => 0,
                'message' => 'ID anak tidak valid.',
            ];
        }

        if ($scheduleId !== null && $scheduleId > 0) {
            $db = \Config\Database::connect();
            $db->table('schedule_students')
                ->where('schedule_id', $scheduleId)
                ->where('anak_id', $anakId)
                ->delete();
        }

        $this->recalculateSisaPertemuan($anakId);
        $breakdown = $this->getPaketBreakdown($anakId);

        $sisa = (int) $breakdown['sisa_total'];
        $hangus = (int) ($breakdown['hangus_total'] ?? 0);
        $debt = (int) ($breakdown['debt'] ?? 0);

        $parts = ['Sisa kuota sistem: ' . $sisa];
        if ($hangus > 0) {
            $parts[] = $hangus . ' pertemuan hangus (tidak dihitung sebagai sisa)';
        }
        if ($debt > 0) {
            $parts[] = $debt . ' pertemuan nunggak';
        }

        return [
            'sisa_pertemuan' => $sisa,
            'hangus_total' => $hangus,
            'debt' => $debt,
            'message' => implode('. ', $parts) . '.',
        ];
    }

    public function getDetailedSisa($anak_id)
    {
        $breakdown = $this->getPaketBreakdown((int) $anak_id);

        return $breakdown['detail_sisa'];
    }

    /**
     * Annotasi riwayat kehadiran dengan paket/pertemuan (FIFO).
     */
    public function annotateKehadiranWithPaket(int $anakId, array $kehadiran): array
    {
        $breakdown = $this->getPaketBreakdown($anakId);
        $map = [];

        foreach ($breakdown['history_groups'] as $group) {
            foreach ($group['sessions'] as $num => $session) {
                if (is_array($session) && !empty($session['id']) && empty($session['slot_status'])) {
                    $map[(int) $session['id']] = [
                        'paket_ke' => $session['paket_ke'] ?? '-',
                        'pertemuan_ke' => $session['pertemuan_ke'] ?? $num,
                    ];
                }
            }
        }

        foreach ($kehadiran as &$row) {
            if (($row['status_kehadiran'] ?? '') === 'hadir' && isset($map[(int) $row['id']])) {
                $row['paket_ke'] = $map[(int) $row['id']]['paket_ke'];
                $row['pertemuan_ke'] = $map[(int) $row['id']]['pertemuan_ke'];
            } else {
                $row['paket_ke'] = '-';
                $row['pertemuan_ke'] = '-';
            }
        }
        unset($row);

        return $kehadiran;
    }

    /**
     * Monitoring paket yang sudah melewati masa berlaku (expired).
     *
     * @param array{tgl_mulai?: string, tgl_selesai?: string, nama_anak?: string, hanya_hangus?: string} $filters
     * @return array{rows: list<array<string, mixed>>, summary: array<string, int|float>}
     */
    public function collectExpiredPackagesReport(array $filters = []): array
    {
        $tglMulai = !empty($filters['tgl_mulai']) ? date('Y-m-d', strtotime($filters['tgl_mulai'])) : null;
        $tglSelesai = !empty($filters['tgl_selesai']) ? date('Y-m-d', strtotime($filters['tgl_selesai'])) : null;
        $namaAnak = trim((string) ($filters['nama_anak'] ?? ''));
        $hanyaHangus = ($filters['hanya_hangus'] ?? '1') === '1'; // Default ke 1 (Hanya yang masih ada sisa) sesuai instruksi baru

        $db = \Config\Database::connect();
        $builder = $db->table('anak a')
            ->select('a.id')
            ->join('pembayaran p', 'p.anak_id = a.id')
            ->where('p.status', 'success');

        if ($namaAnak !== '') {
            $builder->like('a.nama', $namaAnak);
        }

        $anakIds = array_unique(array_map('intval', array_column($builder->groupBy('a.id')->get()->getResultArray(), 'id')));
        $today = date('Y-m-d');
        $rows = [];

        foreach ($anakIds as $anakId) {
            if ($anakId <= 0) {
                continue;
            }

            $anak = $db->table('anak a')
                ->select('a.id, a.nama, a.status, jl.nama_les, parents.nama as nama_parent, parents.whatsapp')
                ->join('jenis_les jl', 'jl.id = a.jenis_les_id', 'left')
                ->join('parents', 'parents.id = a.parent_id', 'left')
                ->where('a.id', $anakId)
                ->get()
                ->getRowArray();

            if (!$anak) {
                continue;
            }

            $breakdown = $this->getPaketBreakdown($anakId);

            foreach ($breakdown['paket'] as $pkg) {
                if (empty($pkg['is_expired'])) {
                    continue;
                }

                $hangus = (int) ($pkg['hangus'] ?? 0);
                if ($hanyaHangus && $hangus <= 0) {
                    continue;
                }

                $berlakuDate = $pkg['berlaku_sampai'] ? date('Y-m-d', strtotime($pkg['berlaku_sampai'])) : null;
                if (!$berlakuDate) {
                    continue;
                }

                if ($tglMulai && $berlakuDate < $tglMulai) {
                    continue;
                }
                if ($tglSelesai && $berlakuDate > $tglSelesai) {
                    continue;
                }

                $tanggalBayar = $pkg['tanggal_bayar'] ?? ($pkg['payment']['tanggal'] ?? null);
                $tanggalBayarDate = $tanggalBayar ? date('Y-m-d', strtotime((string) $tanggalBayar)) : null;
                $hariSejakExpired = max(0, (int) floor((strtotime($today) - strtotime($berlakuDate)) / 86400));
                $payment = $pkg['payment'] ?? [];
                $terpakai = (int) ($pkg['terpakai'] ?? 0);
                $kuota = (int) ($pkg['kuota'] ?? self::KUOTA_PER_PAKET);

                $rows[] = [
                    'anak_id' => $anakId,
                    'nama_anak' => $anak['nama'],
                    'nama_parent' => $anak['nama_parent'] ?? '-',
                    'whatsapp' => $anak['whatsapp'] ?? '-',
                    'jenis_les' => $anak['nama_les'] ?? '-',
                    'status_anak' => $anak['status'] ?? '-',
                    'paket_ke' => (int) $pkg['paket_ke'],
                    'pembayaran_id' => (int) ($pkg['pembayaran_id'] ?? 0),
                    'tanggal_bayar' => $tanggalBayarDate,
                    'tanggal_bayar_display' => $tanggalBayarDate ? date('d/m/Y', strtotime($tanggalBayarDate)) : '-',
                    'berlaku_sampai' => $berlakuDate,
                    'berlaku_sampai_display' => date('d/m/Y', strtotime($berlakuDate)),
                    'hari_sejak_expired' => $hariSejakExpired,
                    'terpakai' => $terpakai,
                    'kuota' => $kuota,
                    'hangus' => $hangus,
                    'total_bayar' => (float) ($payment['total'] ?? 0),
                    'invoice_number' => $payment['invoice_number'] ?? null,
                    'status_paket' => $pkg['status_label'] ?? 'expired',
                    'keterangan' => $this->buildExpiredPackageKeterangan($pkg, $tanggalBayarDate, $berlakuDate),
                    'detail_pertemuan' => $this->buildExpiredPackageMeetingDetail($pkg),
                ];
            }
        }

        usort($rows, static function (array $a, array $b): int {
            $cmp = strcmp($b['berlaku_sampai'], $a['berlaku_sampai']);
            if ($cmp !== 0) {
                return $cmp;
            }

            return strcmp($a['nama_anak'], $b['nama_anak']);
        });

        $summary = [
            'total_paket' => count($rows),
            'total_hangus' => array_sum(array_column($rows, 'hangus')),
            'total_siswa' => count(array_unique(array_column($rows, 'anak_id'))),
            'total_nominal' => array_sum(array_column($rows, 'total_bayar')),
        ];

        return ['rows' => $rows, 'summary' => $summary];
    }

    /**
     * @param array<string, mixed> $pkg
     */
    private function buildExpiredPackageKeterangan(array $pkg, ?string $tanggalBayar, string $berlakuDate): string
    {
        $paketKe = (int) ($pkg['paket_ke'] ?? 0);
        $terpakai = (int) ($pkg['terpakai'] ?? 0);
        $kuota = (int) ($pkg['kuota'] ?? self::KUOTA_PER_PAKET);
        $hangus = (int) ($pkg['hangus'] ?? 0);
        $bayarTeks = $tanggalBayar ? date('d/m/Y', strtotime($tanggalBayar)) : 'tidak diketahui';
        $berlakuTeks = date('d/m/Y', strtotime($berlakuDate));

        if ($hangus > 0) {
            return sprintf(
                'Paket ke-%d kedaluwarsa per %s (dibayar %s). Digunakan %d dari %d pertemuan; %d pertemuan hangus karena tidak diikuti sebelum batas masa berlaku.',
                $paketKe,
                $berlakuTeks,
                $bayarTeks,
                $terpakai,
                $kuota,
                $hangus
            );
        }

        return sprintf(
            'Paket ke-%d masa berlaku berakhir %s (dibayar %s). Semua %d pertemuan telah digunakan sebelum kedaluwarsa.',
            $paketKe,
            $berlakuTeks,
            $bayarTeks,
            $terpakai
        );
    }

    /**
     * @param array<string, mixed> $pkg
     */
    private function buildExpiredPackageMeetingDetail(array $pkg): string
    {
        $kuota = (int) ($pkg['kuota'] ?? self::KUOTA_PER_PAKET);
        $parts = [];

        for ($slot = 1; $slot <= $kuota; $slot++) {
            $session = $pkg['allocated'][$slot - 1] ?? null;
            if (is_array($session) && !empty($session['tanggal'])) {
                $tgl = date('d/m/Y', strtotime($session['tanggal']));
                $jam = !empty($session['jam_mulai']) ? date('H:i', strtotime($session['jam_mulai'])) : '';
                $parts[] = 'Pert. ' . $slot . ': hadir ' . $tgl . ($jam ? ' ' . $jam : '');
            } elseif (($pkg['hangus'] ?? 0) > 0 && $slot > (int) ($pkg['terpakai'] ?? 0)) {
                $parts[] = 'Pert. ' . $slot . ': hangus (tidak diikuti)';
            } else {
                $parts[] = 'Pert. ' . $slot . ': tidak ada data';
            }
        }

        return implode(' · ', $parts);
    }

    /**
     * Mendapatkan data paket anak yang akan expired dalam X hari kedepan
     */
    public function getNearExpiredPackages(int $days = 20, ?int $parentId = null): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('pembayaran p')
            ->select('p.*, a.nama as nama_anak, a.parent_id, parents.nama as nama_parent, parents.whatsapp')
            ->join('anak a', 'a.id = p.anak_id')
            ->join('parents', 'parents.id = a.parent_id')
            ->where('p.status', 'success')
            ->where('p.berlaku_sampai IS NOT NULL');

        if ($parentId !== null) {
            $builder->where('a.parent_id', $parentId);
        }

        $today = date('Y-m-d');
        $targetDate = date('Y-m-d', strtotime("+$days days"));

        $builder->where('p.berlaku_sampai >=', $today)
                ->where('p.berlaku_sampai <=', $targetDate)
                ->orderBy('p.berlaku_sampai', 'ASC');

        $results = $builder->get()->getResultArray();
        $nearExpired = [];

        foreach ($results as $row) {
            $breakdown = $this->getPaketBreakdown($row['anak_id']);
            $currentPkg = null;
            
            // Cari paket yang sesuai dengan ID pembayaran ini di breakdown
            foreach ($breakdown['paket'] as $pkg) {
                if ($pkg['pembayaran_id'] == $row['id'] && !$pkg['is_expired'] && $pkg['sisa_aktif'] > 0) {
                    $currentPkg = $pkg;
                    break;
                }
            }

            if ($currentPkg) {
                $diff = strtotime($row['berlaku_sampai']) - strtotime($today);
                $daysLeft = (int) floor($diff / 86400);
                
                $row['days_left'] = $daysLeft;
                $row['sisa_sesi'] = $currentPkg['sisa_aktif'];
                $nearExpired[] = $row;
            }
        }

        return $nearExpired;
    }

    /**
     * Check student exam eligibility based on a simultaneous exam date and 3-month lookback.
     */
    public function checkExamEligibility(int $anakId, string $examDate, int $minSessions): array
    {
        $endDate = date('Y-m-d', strtotime($examDate));
        $startDate = date('Y-m-d', strtotime($examDate . ' -3-months'));

        $attended = $this->db->table('latihan_attendance la')
                             ->join('schedules s', 's.id = la.schedule_id')
                             ->where('la.anak_id', $anakId)
                             ->where('la.status_kehadiran', 'hadir')
                             ->where('s.tanggal >=', $startDate)
                             ->where('s.tanggal <=', $endDate)
                             ->countAllResults();

        return [
            'attended_sessions' => $attended,
            'min_sessions' => $minSessions,
            'is_eligible' => ($attended >= $minSessions),
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
    }
}
