CREATE SCHEMA IF NOT EXISTS ramah_tamah;

CREATE TABLE ramah_tamah.peserta (
    id BIGSERIAL PRIMARY KEY,
    nik VARCHAR(30) NOT NULL UNIQUE,
    nama VARCHAR(150) NOT NULL,
    departemen VARCHAR(100) NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE ramah_tamah.kehadiran (
    id BIGSERIAL PRIMARY KEY,
    peserta_id BIGINT NOT NULL,

    waktu_datang TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    status VARCHAR(20) NOT NULL DEFAULT 'HADIR',

    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    CONSTRAINT fk_kehadiran_peserta
        FOREIGN KEY (peserta_id)
        REFERENCES ramah_tamah.peserta(id)
        ON DELETE CASCADE,

    CONSTRAINT uq_kehadiran_peserta
        UNIQUE (peserta_id),

    CONSTRAINT chk_kehadiran_status
        CHECK (status = 'HADIR')
);

CREATE TABLE ramah_tamah.hadiah (
    id BIGSERIAL PRIMARY KEY,

    nama_hadiah VARCHAR(150) NOT NULL,

    jumlah INTEGER NOT NULL,

    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    CONSTRAINT chk_hadiah_jumlah
        CHECK (jumlah > 0)
);

CREATE TABLE ramah_tamah.pemenang (
    id BIGSERIAL PRIMARY KEY,

    peserta_id BIGINT NOT NULL,

    hadiah_id BIGINT NOT NULL,

    waktu_menang TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    status_pengambilan VARCHAR(20) NOT NULL DEFAULT 'MENANG',

    waktu_pengambilan TIMESTAMPTZ NULL,

    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    CONSTRAINT fk_pemenang_peserta
        FOREIGN KEY (peserta_id)
        REFERENCES ramah_tamah.peserta(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_pemenang_hadiah
        FOREIGN KEY (hadiah_id)
        REFERENCES ramah_tamah.hadiah(id)
        ON DELETE RESTRICT,

    CONSTRAINT uq_pemenang_peserta
        UNIQUE (peserta_id),

    CONSTRAINT chk_status_pengambilan
        CHECK (
            status_pengambilan IN ('MENANG', 'DIAMBIL')
        )
);

-- ============================================================
-- VIEW: v_kehadiran_tetap
-- Filter kehadiran hanya untuk karyawan tetap
-- NIK karyawan tetap diawali dengan 4 digit tahun + tanda strip
-- Digunakan oleh Lucky Draw fase khusus karyawan tetap
-- ============================================================
CREATE OR REPLACE VIEW ramah_tamah.v_kehadiran_tetap AS
SELECT
    k.id,
    k.peserta_id,
    k.waktu_datang,
    k.status,
    k.created_at
FROM ramah_tamah.kehadiran k
INNER JOIN ramah_tamah.peserta p
    ON p.id = k.peserta_id
WHERE p.nik ~ '^[0-9]{4}-';