<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Dashboard::index');
$routes->get('info', 'Info::index');
$routes->get('info/detail/(:num)', 'Info::detail/$1');


$routes->get('auth', 'Auth::index');
$routes->post('auth/login', 'Auth::login');
$routes->get('auth/logout', 'Auth::logout');

$routes->get('parent/login', 'ParentAuth::index');
$routes->get('parent/register', 'ParentAuth::register');
$routes->post('parent/save', 'ParentAuth::save');
$routes->post('parent/login', 'ParentAuth::login');
$routes->get('parent/logout', 'ParentAuth::logout');

// Coach public routes (no auth required)
$routes->group('coach', static function ($routes) {
    $routes->get('login', 'CoachAuth::index');
    $routes->get('register', 'CoachAuth::register');
    $routes->post('save', 'CoachAuth::save');
    $routes->post('login', 'CoachAuth::login');
    $routes->get('dashboard', 'CoachAuth::dashboard');
    $routes->get('logout', 'CoachAuth::logout');
});

// Coach protected routes (requires coachAuth filter)
$routes->group('coach', ['filter' => 'coachAuth'], static function ($routes) {
    $routes->get('evaluasi', 'Coach\Evaluasi::index');
    $routes->get('evaluasi/input/(:num)', 'Coach\Evaluasi::input/$1');
    $routes->post('evaluasi/store', 'Coach\Evaluasi::store');
    
    // Level Promotion Ujian Kenaikan
    $routes->get('ujian', 'Coach\Evaluasi::ujianList');
    $routes->post('ujian/rekomendasi', 'Coach\Evaluasi::rekomendasiUjian');
    $routes->get('ujian/evaluasi/(:num)', 'Coach\Evaluasi::evaluasiUjian/$1');
    $routes->post('ujian/evaluasi/store', 'Coach\Evaluasi::evaluasiUjianStore');

    // Coach Management (Head Coach Only)
    $routes->get('pelatih', 'Coach\Evaluasi::coachList');
    $routes->post('pelatih/update/(:num)', 'Coach\Evaluasi::coachUpdate/$1');

    // Jadwal Les - Coach dapat melihat dan join jadwal
    $routes->get('jadwal', 'Coach\Evaluasi::jadwalList');
    $routes->get('jadwal/join/(:num)', 'Coach\Evaluasi::joinJadwal/$1');
    $routes->get('jadwal/cancel/(:num)', 'Coach\Evaluasi::cancelJadwal/$1');
});

$routes->get('admin', 'Auth::index');
$routes->get('admin/login', 'Auth::index');

// API Routes for Mobile App
$routes->get('api/test-root', 'ApiTest::index');
$routes->get('api/test-sub', 'ApiTest::sub');
$routes->get('api/test', function() {
    return "API Route Berhasil Terbaca!";
});

$routes->group('api', static function ($routes) {
    // Admin API
    $routes->group('admin', static function ($routes) {
        $routes->get('test', '\App\Controllers\Api\Admin\AdminApiController::test');
        $routes->post('login', '\App\Controllers\Api\Admin\AdminApiController::login');
        $routes->get('jadwal-aktif', '\App\Controllers\Api\Admin\AdminApiController::getJadwalAktif');
        $routes->post('daftar-anak', '\App\Controllers\Api\Admin\AdminApiController::daftarAnakKeJadwal');
        $routes->post('checkin', '\App\Controllers\Api\Admin\AdminApiController::checkin');
    });

    // Parent API
    $routes->group('parent', static function ($routes) {
        $routes->post('login', '\App\Controllers\Api\Parent\ParentApiController::login');
        $routes->get('dashboard', '\App\Controllers\Api\Parent\ParentApiController::dashboard');
        $routes->get('jenis-les', '\App\Controllers\Api\Parent\ParentApiController::getJenisLes');
        
        $routes->group('anak', static function ($routes) {
            $routes->post('store', '\App\Controllers\Api\Parent\ParentApiController::storeAnak');
        });

        $routes->group('jadwal', static function ($routes) {
            $routes->get('tersedia', '\App\Controllers\Api\Parent\ParentApiController::getJadwalTersedia');
            $routes->post('daftar', '\App\Controllers\Api\Parent\ParentApiController::daftarJadwal');
        });

        $routes->group('pembayaran', static function ($routes) {
            $routes->get('/', '\App\Controllers\Api\Parent\ParentApiController::getPembayaran');
            $routes->post('store', '\App\Controllers\Api\Parent\ParentApiController::storePembayaran');
        });
    });
});

$routes->group('parent', ['filter' => 'parentAuth'], static function ($routes) {
    $routes->get('dashboard', 'ParentAuth::dashboard');
    $routes->post('tambah-anak', 'ParentAuth::tambahAnak');
    $routes->post('update-anak', 'ParentAuth::updateAnak');
    $routes->get('hapus-anak/(:num)', 'ParentAuth::hapusAnak/$1');
    $routes->post('konfirmasi-pembayaran', 'ParentAuth::konfirmasiPembayaran');
    $routes->post('upload-bukti', 'ParentAuth::uploadBukti');
    $routes->post('daftar-jadwal', 'ParentAuth::daftar_jadwal');
    $routes->post('jadwal/daftar', 'ParentAuth::daftar_jadwal');

    $routes->get('tambah-anak-form', 'Parents::tambahAnakForm');
    $routes->get('edit-anak/(:num)', 'Parents::editAnak/$1');
    $routes->put('update-anak/(:num)', 'Parents::updateAnak/$1');

    $routes->get('jadwal/detail/(:num)', 'Parent\JadwalController::detail/$1');
    $routes->get('jadwal/daftar/(:num)', 'Parent\JadwalController::daftar/$1');
    $routes->post('jadwal/proses-daftar', 'Parent\JadwalController::prosesDaftar');
    
    // Curriculum & Progress
    $routes->get('curriculum', 'Parent\Curriculum::index');
    $routes->get('certificate/download/(:num)/(:num)', 'Parent\Curriculum::downloadCertificate/$1/$2');
    $routes->get('raport/download/(:num)/(:num)', 'Parent\Curriculum::downloadRaport/$1/$2');
});

$routes->group('admin', ['filter' => 'auth'], static function ($routes) {
    $routes->get('dashboard', 'Admin::dashboard');

    // Swimming Curriculum & Level Management
    $routes->get('curriculum', 'Admin\Curriculum::index');
    $routes->post('curriculum/store-level', 'Admin\Curriculum::storeLevel');
    $routes->post('curriculum/update-level/(:num)', 'Admin\Curriculum::updateLevel/$1');
    $routes->get('curriculum/delete-level/(:num)', 'Admin\Curriculum::deleteLevel/$1');
    $routes->post('curriculum/assign', 'Admin\Curriculum::assign');
    $routes->get('curriculum/ujian', 'Admin\Curriculum::ujianList');
    $routes->post('curriculum/ujian/rekomendasi', 'Admin\Curriculum::rekomendasiUjian');
    $routes->get('curriculum/ujian/evaluasi/(:num)', 'Admin\Curriculum::evaluasiUjian/$1');
    $routes->post('curriculum/ujian/evaluasi/store', 'Admin\Curriculum::evaluasiUjianStore');
    $routes->get('curriculum/sertifikat-raport', 'Admin\Curriculum::sertifikatRaportList');
    $routes->get('curriculum/certificate/print/(:num)/(:num)', 'Admin\Curriculum::printCertificate/$1/$2');
    $routes->get('curriculum/raport/print/(:num)/(:num)', 'Admin\Curriculum::printRaport/$1/$2');

    $routes->get('anak', 'Admin\Anak::index');
    $routes->get('anak/detail/(:num)', 'Admin\Anak::detail/$1');
    $routes->get('anak/edit/(:num)', 'Admin\Anak::edit/$1');
    $routes->post('anak/update/(:num)', 'Admin\Anak::update/$1');
    $routes->get('anak/delete-pembayaran/(:num)', 'Admin\Anak::deletePembayaran/$1');
    $routes->get('anak/delete-kehadiran/(:num)', 'Admin\Anak::deleteKehadiran/$1');
    $routes->get('anak/delete/(:num)', 'Admin\Anak::delete/$1');
    $routes->post('anak/extend-paket', 'Admin\Anak::extendPaket');

    $routes->group('report', static function ($routes) {
        $routes->get('keuangan', 'Admin\Report::keuangan');
        $routes->get('kehadiran', 'Admin\Report::kehadiran');
        $routes->get('siswa', 'Admin\Report::siswa');
        $routes->get('pembayaran', 'Admin\Report::pembayaran');
        $routes->get('pembayaran/detail/(:any)', 'Admin\Report::pembayaranDetail/$1');
        $routes->get('paket-expired', 'Admin\Report::paketExpired');
    });

    $routes->get('settings', 'Admin\Settings::index');
    $routes->post('settings/update', 'Admin\Settings::update');
    $routes->get('settings/sync-all', 'Admin\Settings::syncAll');

    $routes->get('parents', 'Admin\Parents::index');
    $routes->get('parents/delete/(:num)', 'Admin\Parents::delete/$1');

    $routes->get('cetak-kartu', 'Admin\Anak::cetakKartuView');
    $routes->post('cetak-kartu/print', 'Admin\Anak::cetakKartu');

    $routes->get('jadwal', 'Admin\JadwalController::index');
    $routes->get('jadwal/riwayat', 'Admin\JadwalController::riwayat');
    $routes->post('jadwal/save', 'Admin\JadwalController::save');
    $routes->get('jadwal/detail/(:num)', 'Admin\JadwalController::detail/$1');
    $routes->post('jadwal/update/(:num)', 'Admin\JadwalController::update/$1');
    $routes->get('jadwal/delete/(:num)', 'Admin\JadwalController::delete/$1');
    $routes->post('jadwal/absensi/(:num)', 'Admin\JadwalController::updateAbsensi/$1');
    $routes->post('jadwal/coach-absensi/(:num)', 'Admin\JadwalController::updateCoachAbsensi/$1');
    $routes->post('jadwal/tambah-peserta/(:num)', 'Admin\JadwalController::tambahPeserta/$1');
    $routes->get('jadwal/hapus-peserta/(:num)/(:num)', 'Admin\JadwalController::hapusPeserta/$1/$2');
    $routes->post('jadwal/tambah-coach/(:num)', 'Admin\JadwalController::tambahCoach/$1');
    $routes->get('jadwal/hapus-coach/(:num)/(:num)', 'Admin\JadwalController::hapusCoach/$1/$2');

    $routes->get('jenis-les', 'Admin\JenisLes::index');
    $routes->get('jenis-les/create', 'Admin\JenisLes::create');
    $routes->post('jenis-les/store', 'Admin\JenisLes::store');
    $routes->get('jenis-les/edit/(:num)', 'Admin\JenisLes::edit/$1');
    $routes->post('jenis-les/update/(:num)', 'Admin\JenisLes::update/$1');
    $routes->get('jenis-les/delete/(:num)', 'Admin\JenisLes::delete/$1');

    $routes->get('pembayaran', 'Admin\Pembayaran::index');
    $routes->get('pembayaran/detail/(:num)', 'Admin\Pembayaran::detail/$1');
    $routes->get('pembayaran/approve/(:num)', 'Admin\Pembayaran::approve/$1');
    $routes->get('pembayaran/reject/(:num)', 'Admin\Pembayaran::reject/$1');
    $routes->get('pembayaran/manual', 'Admin\Pembayaran::manual');
    $routes->post('pembayaran/manual', 'Admin\Pembayaran::manualStore');
    $routes->get('pembayaran/manual-search', 'Admin\Pembayaran::manualSearch');
    $routes->get('pembayaran/riwayat', 'Admin\Pembayaran::riwayat');
    $routes->get('pembayaran/confirm-boss/(:num)', 'Admin\Pembayaran::confirmByBoss/$1');
    $routes->post('pembayaran/bulk-confirm-boss', 'Admin\Pembayaran::bulkConfirmByBoss');
    $routes->post('pembayaran/complete-month-boss', 'Admin\Pembayaran::completeMonthBoss');
    $routes->post('pembayaran/reject-boss/(:num)', 'Admin\Pembayaran::rejectByBoss/$1');
    $routes->post('pembayaran/reject/(:num)', 'Admin\Pembayaran::reject/$1');
    $routes->get('pembayaran/edit/(:num)', 'Admin\Pembayaran::edit/$1');
    $routes->post('pembayaran/update/(:num)', 'Admin\Pembayaran::update/$1');
    $routes->get('pembayaran/delete/(:num)', 'Admin\Pembayaran::delete/$1');
    $routes->get('pembayaran/cetak/(:num)', 'Admin\Pembayaran::cetak/$1');
    $routes->get('pembayaran/export-excel', 'Admin\Pembayaran::exportExcel');

    $routes->get('kedatangan', 'Admin\Kedatangan::index');
    $routes->get('kedatangan/get-schedules-by-date', 'Admin\Kedatangan::getSchedulesByDate');
    $routes->post('kedatangan/save-bulk-manual', 'Admin\Kedatangan::saveBulkManual');
    $routes->get('kedatangan/edit', 'Admin\Kedatangan::edit');
    $routes->get('kedatangan/edit-absensi/(:num)', 'Admin\Kedatangan::editAbsensi/$1');
    $routes->get('kedatangan/kirim-wa-riwayat/(:num)/(:any)', 'Admin\Kedatangan::kirimWaRiwayat/$1/$2');
    $routes->get('kedatangan/kirim-wa-group-jadwal/(:num)', 'Admin\Kedatangan::kirimWaGroupJadwal/$1');
    $routes->post('kedatangan/save-edit-absensi', 'Admin\Kedatangan::saveEditAbsensi');
    $routes->get('kedatangan/delete-edit-absensi/(:num)', 'Admin\Kedatangan::deleteEditAbsensi/$1');
    $routes->get('kedatangan/cetak-laporan/(:num)', 'Admin\Kedatangan::cetakLaporan/$1');
    $routes->get('kedatangan/riwayat', 'Admin\Kedatangan::riwayat');
    $routes->get('kedatangan/edit-kehadiran/(:num)', 'Admin\Kedatangan::editKehadiran/$1');
    $routes->post('kedatangan/update-kehadiran/(:num)', 'Admin\Kedatangan::updateKehadiran/$1');
    $routes->get('kedatangan/absensi/(:num)', 'Admin\Kedatangan::absensi/$1');
    $routes->get('kedatangan/get-absensi-data/(:num)', 'Admin\Kedatangan::getAbsensiData/$1');
    $routes->post('kedatangan/tambah-peserta-manual', 'Admin\Kedatangan::tambahPesertaManual');
    $routes->post('kedatangan/checkin', 'Admin\Kedatangan::checkin');
    $routes->get('kedatangan/search-anak', 'Admin\Kedatangan::searchAnak');
    $routes->post('kedatangan/save-all-absensi', 'Admin\Kedatangan::saveAllAbsensi');
    $routes->post('kedatangan/update-absensi/(:num)', 'Admin\Kedatangan::updateAbsensi/$1');
    $routes->get('kedatangan/delete-absensi/(:num)', 'Admin\Kedatangan::deleteAbsensi/$1');
    $routes->get('kedatangan/delete-peserta/(:num)/(:num)', 'Admin\Kedatangan::deletePeserta/$1/$2');
    $routes->get('kedatangan/buka/(:num)', 'Admin\Kedatangan::buka/$1');
    $routes->get('kedatangan/export-absensi/(:num)', 'Admin\Kedatangan::exportAbsensi/$1');
    $routes->get('kedatangan/tambah-peserta-manual-form/(:num)', 'Admin\Kedatangan1::tambahPesertaManualForm/$1');
    $routes->post('kedatangan/save', 'Admin\Kedatangan::save');
    $routes->post('kedatangan/update/(:num)', 'Admin\Kedatangan::update/$1');
    $routes->get('kedatangan/delete/(:num)', 'Admin\Kedatangan::delete/$1');
    $routes->get('kedatangan/detail/(:num)', 'Admin\Kedatangan::detail/$1');
    $routes->post('kedatangan/approve/(:num)', 'Admin\Kedatangan::approve/$1');
    $routes->post('kedatangan/reject/(:num)', 'Admin\Kedatangan::reject/$1');
    $routes->get('kedatangan/cetak/(:num)', 'Admin\Kedatangan::cetak/$1');
    $routes->get('kedatangan/export', 'Admin\Kedatangan::export');
    $routes->get('kedatangan/export-excel/(:num)', 'Admin\Kedatangan::exportExcel/$1');
    $routes->get('kedatangan/export-pdf', 'Admin\Kedatangan::exportPDF');
    $routes->get('kedatangan/export-csv', 'Admin\Kedatangan::exportCSV');

    $routes->get('coach', 'Admin::coach');
    $routes->post('coach/save', 'Admin::coachSave');
    $routes->post('coach/update/(:num)', 'Admin::coachUpdate/$1');
    $routes->get('coach/delete/(:num)', 'Admin::coachDelete/$1');
    $routes->get('coach/toggle-registration', 'Admin::toggleCoachRegistration');

    // Logo Management Routes
    $routes->group('logo', static function ($routes) {
        $routes->get('/', 'Admin\LogoController::index');
        $routes->post('store', 'Admin\LogoController::store');
        $routes->post('update/(:num)', 'Admin\LogoController::update/$1');
        $routes->get('delete/(:num)', 'Admin\LogoController::delete/$1');
        $routes->get('preview-cert', 'Admin\LogoController::previewCert');
    });
});

// (coach routes are defined above, before the admin group)
