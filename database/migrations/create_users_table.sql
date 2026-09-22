-- ============================================================
-- TABLE: users
-- ============================================================
-- Tabel untuk menyimpan data login pengguna aplikasi Android
-- Role: 'user' untuk peserta biasa, 'admin' untuk admin
-- Password menggunakan NIK (sama dengan username)
-- ============================================================

CREATE TABLE IF NOT EXISTS ramah_tamah.users (
    id BIGSERIAL PRIMARY KEY,
    nik VARCHAR(30) NOT NULL UNIQUE,
    nama VARCHAR(150) NOT NULL,
    departemen VARCHAR(100) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    CONSTRAINT chk_users_role CHECK (role IN ('user', 'admin'))
);

-- Index untuk pencarian cepat
CREATE INDEX IF NOT EXISTS idx_users_nik ON ramah_tamah.users(nik);
CREATE INDEX IF NOT EXISTS idx_users_role ON ramah_tamah.users(role);

-- ============================================================
-- TRIGGER: Auto update updated_at
-- ============================================================
CREATE OR REPLACE FUNCTION ramah_tamah.update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ language 'plpgsql';

DROP TRIGGER IF EXISTS update_users_updated_at ON ramah_tamah.users;
CREATE TRIGGER update_users_updated_at
    BEFORE UPDATE ON ramah_tamah.users
    FOR EACH ROW
    EXECUTE FUNCTION ramah_tamah.update_updated_at_column();

-- ============================================================
-- SEED: Insert admin user
-- ============================================================
-- Admin: Fikky Firdaus dengan NIK 2022-001931
INSERT INTO ramah_tamah.users (nik, nama, departemen, role)
VALUES ('2022-001931', 'FIKKY FIRDAUS', '(81800) MIS', 'admin')
ON CONFLICT (nik) DO UPDATE SET
    nama = EXCLUDED.nama,
    departemen = EXCLUDED.departemen,
    role = EXCLUDED.role,
    updated_at = NOW();

-- ============================================================
-- SEED: Insert semua peserta sebagai user
-- ============================================================
-- Sinkronisasi data peserta ke tabel users
INSERT INTO ramah_tamah.users (nik, nama, departemen, role)
SELECT 
    p.nik,
    p.nama,
    p.departemen,
    'user' as role
FROM ramah_tamah.peserta p
WHERE p.nik != '2022-001931'
ON CONFLICT (nik) DO NOTHING;

-- ============================================================
-- VIEW: User dengan info kehadiran dan status pemenang
-- ============================================================
CREATE OR REPLACE VIEW ramah_tamah.v_user_dashboard AS
SELECT 
    u.id as user_id,
    u.nik,
    u.nama,
    u.departemen,
    u.role,
    CASE WHEN k.id IS NOT NULL THEN true ELSE false END as sudah_hadir,
    k.waktu_datang,
    CASE WHEN w.id IS NOT NULL THEN true ELSE false END as is_pemenang,
    w.id as pemenang_id,
    h.nama_hadiah as hadiah,
    w.waktu_menang,
    w.status_pengambilan,
    w.waktu_pengambilan
FROM ramah_tamah.users u
LEFT JOIN ramah_tamah.kehadiran k ON k.peserta_id = (
    SELECT id FROM ramah_tamah.peserta WHERE nik = u.nik
)
LEFT JOIN ramah_tamah.pemenang w ON w.peserta_id = (
    SELECT id FROM ramah_tamah.peserta WHERE nik = u.nik
)
LEFT JOIN ramah_tamah.hadiah h ON h.id = w.hadiah_id;

-- ============================================================
-- VERIFICATION
-- ============================================================
SELECT 'Tabel users berhasil dibuat' as status;
SELECT COUNT(*) as total_users FROM ramah_tamah.users;
SELECT COUNT(*) as total_admin FROM ramah_tamah.users WHERE role = 'admin';
SELECT COUNT(*) as total_regular_users FROM ramah_tamah.users WHERE role = 'user';
