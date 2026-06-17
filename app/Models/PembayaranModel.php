<?php

namespace App\Models;

use CodeIgniter\Model;

class PembayaranModel extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'anak_id', 
        'parent_id', 
        'tanggal', 
        'jumlah_pertemuan', 
        'total', 
        'metode_pembayaran', 
        'bukti_pembayaran', 
        'catatan', 
        'status', 
        'status_approval_admin',
        'catatan_tolak_admin',
        'waktu_approval_admin',
        'status_approval_bos',
        'catatan_tolak_bos',
        'waktu_approval_bos',
        'is_confirmed_boss',
        'signature_boss',
        'berlaku_sampai',
        'earn_coach',
        'earn_owner',
        'jenis_les_id'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $validationRules = [
        'anak_id' => 'required|integer|greater_than[0]',
        'parent_id' => 'required|integer|greater_than[0]',
        'jumlah_pertemuan' => 'required|integer|in_list[4]',
        'total' => 'required|decimal|greater_than[0]',
        'metode_pembayaran' => 'required|string|max_length[50]',
        'status' => 'required|in_list[pending,success,rejected]',
    ];
    
    // Mendapatkan semua pembayaran dengan status pending
    public function getPendingPayments()
    {
        return $this->select('pembayaran.*, anak.nama as nama_anak, parents.nama as nama_parent, jenis_les.nama_les as nama_les')
                    ->join('anak', 'anak.id = pembayaran.anak_id', 'left')
                    ->join('parents', 'parents.id = pembayaran.parent_id', 'left')
                    ->join('jenis_les', 'jenis_les.id = pembayaran.jenis_les_id', 'left')
                    ->where('pembayaran.status', 'pending')
                    ->orderBy('pembayaran.tanggal', 'DESC')
                    ->findAll();
    }
    
    // Mendapatkan detail pembayaran berdasarkan ID
    public function getPaymentDetail($id)
    {
        return $this->select('pembayaran.*, anak.nama as nama_anak, parents.nama as nama_parent, jenis_les.nama_les as nama_les')
                    ->join('anak', 'anak.id = pembayaran.anak_id', 'left')
                    ->join('parents', 'parents.id = pembayaran.parent_id', 'left')
                    ->join('jenis_les', 'jenis_les.id = pembayaran.jenis_les_id', 'left')
                    ->where('pembayaran.id', $id)
                    ->first();
    }
    
    // Approve pembayaran
    public function approvePayment($id)
    {
        return $this->update($id, ['status' => 'success']);
    }
    
    // Reject pembayaran
    public function rejectPayment($id)
    {
        return $this->update($id, ['status' => 'rejected']);
    }
    
    public function getPembayaranWithDetails()
    {
        return $this->select('pembayaran.*, anak.nama as nama_anak, parents.nama as nama_parent, jenis_les.nama_les as nama_les_snapshot')
                        ->join('anak', 'anak.id = pembayaran.anak_id', 'left')
                        ->join('parents', 'parents.id = pembayaran.parent_id', 'left')
                        ->join('jenis_les', 'jenis_les.id = pembayaran.jenis_les_id', 'left')
                        ->orderBy('pembayaran.tanggal', 'DESC')
                        ->findAll();
    }
    
    public function getTotalPendapatanCoachBulanIni()
    {
        $startDate = date('Y-m-01'); // tanggal 1 bulan ini
        $currentDate = date('Y-m-d'); // tanggal hari ini
        
        return $this->selectSum('earn_coach')
                    ->where('status', 'success')
                    ->where('tanggal >=', $startDate)
                    ->where('tanggal <=', $currentDate)
                    ->first()['earn_coach'] ?? 0;
    }
    
    public function getTotalPendapatanKolamBulanIni()
    {
        $startDate = date('Y-m-01'); // tanggal 1 bulan ini
        $currentDate = date('Y-m-d'); // tanggal hari ini
        
        return $this->selectSum('earn_owner')
                    ->where('status', 'success')
                    ->where('tanggal >=', $startDate)
                    ->where('tanggal <=', $currentDate)
                    ->first()['earn_owner'] ?? 0;
    }
    
    public function countPendingPayments()
    {
        return $this->where('status', 'pending')
                    ->countAllResults();
    }
    
    public function getTotalPendapatanKotor()
    {
        return $this->selectSum('total')
                    ->where('status', 'success')
                    ->first()['total'] ?? 0;
    }

    /**
     * Berlaku sampai = tanggal pembayaran + 90 hari (format Y-m-d).
     */
    public function computeBerlakuSampaiFromPaymentDate(?string $paymentDate): ?string
    {
        if (empty($paymentDate)) {
            return null;
        }

        $timestamp = strtotime($paymentDate);
        if ($timestamp === false) {
            return null;
        }

        return date('Y-m-d', strtotime('+90 days', $timestamp));
    }

    /**
     * Tanggal pembayaran sukses terakhir per anak.
     */
    public function getLastSuccessPaymentDate(int $anakId): ?string
    {
        if ($anakId <= 0) {
            return null;
        }

        $row = $this->select('tanggal')
            ->where('anak_id', $anakId)
            ->where('status', 'success')
            ->orderBy('tanggal', 'DESC')
            ->first();

        if (!$row || empty($row['tanggal'])) {
            return null;
        }

        return $row['tanggal'];
    }

    /**
     * Berlaku sampai aktif: tanggal pembayaran sukses terakhir + 90 hari.
     */
    public function getBerlakuSampaiForAnak(int $anakId): ?string
    {
        return $this->computeBerlakuSampaiFromPaymentDate(
            $this->getLastSuccessPaymentDate($anakId)
        );
    }

    /**
     * Expired: masa berlaku sudah lewat hari ini tetapi masih ada sisa pertemuan (> 0).
     */
    public function isMasaBerlakuExpired(int $sisaPertemuan, ?string $berlakuSampai): bool
    {
        if ($sisaPertemuan <= 0 || empty($berlakuSampai)) {
            return false;
        }

        $batas = strtotime(date('Y-m-d', strtotime($berlakuSampai)));
        $hariIni = strtotime(date('Y-m-d'));

        return $batas !== false && $batas < $hariIni;
    }

    public function hasPendingForAnak(int $anakId): bool
    {
        if ($anakId <= 0) {
            return false;
        }

        return $this->where('anak_id', $anakId)
            ->where('status', 'pending')
            ->countAllResults() > 0;
    }

    /**
     * Validasi orang tua boleh membuat pembayaran baru (tanpa ubah skema DB).
     *
     * @return array{allowed: bool, message: string, sisa_pertemuan: int|null}
     */
    public function validateParentCanPay(int $anakId, int $parentId): array
    {
        $anakModel = new AnakModel();
        $anak = $anakModel->where('id', $anakId)->where('parent_id', $parentId)->first();

        if (!$anak) {
            return [
                'allowed' => false,
                'message' => 'Data anak tidak ditemukan atau bukan milik akun Anda.',
                'sisa_pertemuan' => null,
            ];
        }

        $anakModel->recalculateSisaPertemuan($anakId);
        $anak = $anakModel->find($anakId);
        $sisa = (int) ($anak['sisa_pertemuan'] ?? 0);

        if ($sisa > 0) {
            return [
                'allowed' => false,
                'message' => 'Pembayaran hanya bisa dilakukan jika sisa pertemuan 0 atau kurang.',
                'sisa_pertemuan' => $sisa,
            ];
        }

        if ($this->hasPendingForAnak($anakId)) {
            return [
                'allowed' => false,
                'message' => 'Masih ada pembayaran yang menunggu verifikasi admin untuk anak ini.',
                'sisa_pertemuan' => $sisa,
            ];
        }

        return [
            'allowed' => true,
            'message' => '',
            'sisa_pertemuan' => $sisa,
        ];
    }
}