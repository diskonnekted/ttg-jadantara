#!/usr/bin/env python3
"""Map PDF files to tutorials and update database."""
import sqlite3
import os
import shutil
from pathlib import Path

BASE = Path('.')
DB = BASE / 'database' / 'ttg_pondokrejo.db'
PDF_SRC = BASE / 'ttg' / 'pdf'
PDF_DST = BASE / 'public' / 'uploads' / 'pdfs'

PDF_DST.mkdir(exist_ok=True)

conn = sqlite3.connect(DB)
c = conn.cursor()

# Get all tutorials
c.execute("SELECT id, title FROM tutorials ORDER BY id")
tutorials = c.fetchall()

# PDF mapping (article keyword -> pdf filename)
pdf_map = [
    ('selada hidroponik', 'hidroponik.pdf'),  # Not available, skip
    ('lele', 'lele.pdf'),
    ('pot bunga', None),  # No PDF
    ('panel surya', None),
    ('ayam pedaging', 'ayam_pedaging.pdf'),
    ('bekicot', 'bekicot.pdf'),
    ('pompa tali', 'pompa_tali.pdf'),
    ('pompa hisap', 'pompa_hisap_las.pdf'),
    ('saluran air', 'saluran_mandi_cuci.pdf'),
    ('kualitas air', None),
    ('arang sekam', 'penjernihan_air_arang_sekam.pdf'),
    ('mas koki', 'budidaya_ikan_koki_mutiara.pdf'),
    ('tetra', 'budidaya_ikan_hias_tetra.pdf'),
    ('livebearer', 'budidaya_ikan_hias_livebearer.pdf'),
    ('beronang', 'budidaya_ikan_beronang.pdf'),
    ('gurame', 'budidaya_ikan_gurame.pdf'),
    ('manfish', 'budidaya_ikan_manfish.pdf'),
    ('oscar', 'budidaya_ikan_oscar.pdf'),
    ('tiram', 'budidaya_tiram.pdf'),
    ('tiram mutiara', 'budidaya_tiram_mutiara.pdf'),
    ('puyuh', 'burung_puyuh.pdf'),
    ('bak tampung bambu semen', 'bak_tampung_bambu_semen01.pdf'),
    ('niu bang', None),
    ('bing lang', None),
    ('walet', 'burung_walet.pdf'),
    ('cacing', 'cacing_tanah.pdf'),
    ('flavonoid', 'EKSTRAKSI_FLAVONOID_DARI_DAUN_PARE_MOMORDICA_CHARA.pdf'),
    ('biji kelor', 'penjernihan_air_biji_kelor.pdf'),
    ('teknologi tepat guna', None),
    ('bak penampungan air', 'bak_tampung_bambu_semen01.pdf'),
    ('bak penampungan bambu semen 2', 'bak_tampung_bambu_semen02.pdf'),
    ('mata air', 'bak_tampung_mata_air.pdf'),
    ('instalasi air bersih pipa bambu', 'instalasi_air_bambu_tertutup.pdf'),
    ('bangunan tanah', None),
    ('mina padi', 'pelihara_ikan_mina_padi.pdf'),
    ('udang windu', 'udang_windu.pdf'),
    ('nila', 'nila.pdf'),
    ('teripang putih', 'pembenihan_teripang_putih0000.pdf'),
    ('tawes', 'pembenihan_ikan_tawes.pdf'),
    ('penggelondongan bandeng', 'penggelondongan_bandeng.pdf'),
    ('patin', 'patinooooooooooo.pdf'),
    ('penjernihan air berbiaya', 'penjernihan_air_saring1.pdf'),
    ('pompa tenaga surya', None),
    ('solar home', None),
    ('pengusir burung', None),
    ('abon daging', 'abon_daging_keluwih.pdf'),
    ('amonisasi', 'amonisasi_jerami_padi_pakan.pdf'),
    ('ayam pedaging', 'ayam_pedaging.pdf'),
    ('ayam petelur', 'ayam_petelur.pdf'),
    ('bak tampung air bambu semen 2500', 'bak_tampung_bambu_semen01.pdf'),
    ('bak tampung air bambu semen 10000', 'bak_tampung_bambu_semen02.pdf'),
    ('mata air', 'bak_tampung_mata_air.pdf'),
    ('belut', 'belut.pdf'),
    ('kakao', 'Pektin Kakao.pdf'),
    ('buah sayuran', None),
    ('tetra', 'budidaya_ikan_hias_tetra.pdf'),
    ('koki mutiara', 'budidaya_ikan_koki_mutiara.pdf'),
    ('manfish', 'budidaya_ikan_manfish.pdf'),
    ('cookies', 'cookies_ubi_jalar.pdf'),
    ('dodol', 'dodol_ubi_jalar.pdf'),
    ('domba', 'domba.pdf'),
    ('french fries', 'french_fries_ubi_jalar.pdf'),
    ('gaplek', 'gaplek.pdf'),
    ('instalasi air bambu', 'instalasi_air_bambu_tertutup.pdf'),
    ('instalasi air bersih tradisional', 'instalasi_air_bersih_tradisional.pdf'),
    ('jagung', 'jagung_goreng.pdf'),
    ('jahe', 'jahe_kristal.pdf'),
    ('jamban leher', 'jamban_leher_angsa.pdf'),
    ('jamban septik', 'jamban_septik_tank_ganda.pdf'),
    ('jempeng', 'jempeng.pdf'),
    ('juknis', 'juknis_ikan_laut.pdf'),
    ('kakus cemplung', 'kakus_cemplung.pdf'),
    ('kakus sopasandas', 'kakus_sopasandas.pdf'),
    ('kakus vietnam', 'kakus_vietnam.pdf'),
    ('gambut', 'kelola_air_gambut.pdf'),
    ('limbah rt1', 'kelola_air_limbah_rt1.pdf'),
    ('limbah rt2', 'kelola_air_limbah_rt2.pdf'),
    ('limbah kakus1', 'kelola_limbah_kakus1.pdf'),
    ('limbah kakus2', 'kelola_limbah_kakus2.pdf'),
    ('sampah', 'kelola_sampah.pdf'),
    ('minyak atsiri', 'minyak_atsiri_fuli_buah_pala.pdf'),
    ('mujair', 'mujair.pdf'),
    ('patin', 'patinooooooooooo.pdf'),
    ('penyakit ikan', 'pedoman_penyakit_ikan_laut.pdf'),
    ('pektin', 'Pektin Kakao.pdf'),
    ('mina padi', 'pelihara_ikan_mina_padi.pdf'),
    ('bandeng', 'pembenihan_bandeng00000000.pdf'),
    ('tawes', 'pembenihan_ikan_tawes.pdf'),
    ('kakap putih', 'pembenihan_kakap_putih.pdf'),
    ('kakap putih hsrt', 'pembenihan_kakap_putih_hsrt.pdf'),
    ('kerapu macan', 'pembenihan_kerapu_macan01000000.pdf'),
    ('kerapu macan02', 'pembenihan_kerapu_macan02.pdf'),
    ('teripang putih', 'pembenihan_teripang_putih0000.pdf'),
    ('bawal', 'pembesaran_ikan_bawal_air_tawar000000.pdf'),
    ('kakap putih', 'pembesaran_ikan_kakap_putih.pdf'),
    ('pengawetan', 'pengawetan.pdf'),
    ('pengenalan ikan', 'pengenalan_ikan_hias.pdf'),
    ('penjernihan air arang', 'penjernihan_air_arang_sekam.pdf'),
    ('penjernihan air kelor', 'penjernihan_air_biji_kelor.pdf'),
    ('saring1', 'penjernihan_air_saring1.pdf'),
    ('saring2', 'penjernihan_air_saring2.pdf'),
    ('saring kimia', 'penjernihan_air_saring_kimia1.pdf'),
    ('cangkringan', 'pijah_ikan_cangkringan.pdf'),
    ('pompa hisap balok', 'pompa_hisap_balok.pdf'),
    ('pompa hisap las', 'pompa_hisap_las.pdf'),
    ('saluran mandi', 'saluran_mandi_cuci.pdf'),
    ('saponin', 'saponin_basmi_hama_udang.pdf'),
    ('selai buah', 'Selai Buah Pala.pdf'),
]

# Better approach: match by keyword in title
updated = 0
for tid, title in tutorials:
    title_lower = title.lower()
    matched_pdf = None
    
    # Try to find matching PDF
    for pdf_file in PDF_SRC.iterdir():
        if pdf_file.suffix.lower() != '.pdf':
            continue
        
        pdf_name = pdf_file.stem.lower().replace('_', ' ').replace('-', ' ')
        
        # Check if PDF name matches any word in title
        # Or if title contains PDF name
        if any(word in title_lower for word in pdf_name.split() if len(word) > 3):
            matched_pdf = pdf_file.name
            break
        if any(word in pdf_name for word in title_lower.split() if len(word) > 3):
            matched_pdf = pdf_file.name
            break
    
    # Manual overrides for tricky ones
    if 'gamb' in title_lower: matched_pdf = 'kelola_air_gambut.pdf'
    elif 'limbah kakus 1' in title_lower: matched_pdf = 'kelola_limbah_kakus1.pdf'
    elif 'limbah kakus 2' in title_lower: matched_pdf = 'kelola_limbah_kakus2.pdf'
    elif 'limbah rt1' in title_lower: matched_pdf = 'kelola_air_limbah_rt1.pdf'
    elif 'limbah rt2' in title_lower: matched_pdf = 'kelola_air_limbah_rt2.pdf'
    elif 'saring kimia' in title_lower: matched_pdf = 'penjernihan_air_saring_kimia1.pdf'
    elif 'saring1' in title_lower: matched_pdf = 'penjernihan_air_saring1.pdf'
    elif 'saring2' in title_lower: matched_pdf = 'penjernihan_air_saring2.pdf'
    elif 'arang sekam' in title_lower: matched_pdf = 'penjernihan_air_arang_sekam.pdf'
    elif 'biji kelor' in title_lower: matched_pdf = 'penjernihan_air_biji_kelor.pdf'
    elif 'penjernihan air berbiaya' in title_lower: matched_pdf = 'penjernihan_air_saring1.pdf'
    elif 'teripang putih' in title_lower: matched_pdf = 'pembenihan_teripang_putih0000.pdf'
    elif 'kerapu macan' in title_lower: matched_pdf = 'pembenihan_kerapu_macan01000000.pdf'
    elif 'kakap putih hsrt' in title_lower: matched_pdf = 'pembenihan_kakap_putih_hsrt.pdf'
    elif 'kakap putih' in title_lower: matched_pdf = 'pembenihan_kakap_putih.pdf'
    elif 'mina padi' in title_lower: matched_pdf = 'pelihara_ikan_mina_padi.pdf'
    elif 'pompa hisap balok' in title_lower: matched_pdf = 'pompa_hisap_balok.pdf'
    elif 'pompa hisap las' in title_lower: matched_pdf = 'pompa_hisap_las.pdf'
    elif 'pompa hisap sistem' in title_lower: matched_pdf = 'pompa_hisap_las.pdf'
    elif 'instalasi air bambu tertutup' in title_lower: matched_pdf = 'instalasi_air_bambu_tertutup.pdf'
    elif 'instalasi air bersih tradisional' in title_lower: matched_pdf = 'instalasi_air_bersih_tradisional.pdf'
    elif 'bak tampung bambu semen 2500' in title_lower or 'bak tampung bambu semen01' in title_lower: matched_pdf = 'bak_tampung_bambu_semen01.pdf'
    elif 'bak tampung bambu semen 10000' in title_lower or 'bak tampung bambu semen02' in title_lower: matched_pdf = 'bak_tampung_bambu_semen02.pdf'
    elif 'bak tampung bambu semen' in title_lower: matched_pdf = 'bak_tampung_bambu_semen01.pdf'
    elif 'bak tampung mata air' in title_lower: matched_pdf = 'bak_tampung_mata_air.pdf'
    elif 'bak penampungan air' in title_lower: matched_pdf = 'bak_tampung_mata_air.pdf'
    elif 'bak penampungan sumber' in title_lower: matched_pdf = 'bak_tampung_mata_air.pdf'
    elif 'amonisasi' in title_lower: matched_pdf = 'amonisasi_jerami_padi_pakan.pdf'
    elif 'minyak atsiri' in title_lower: matched_pdf = 'minyak_atsiri_fuli_buah_pala.pdf'
    elif 'selai buah pala' in title_lower: matched_pdf = 'Selai Buah Pala.pdf'
    elif 'patin' in title_lower and 'budidaya patin' in title_lower: matched_pdf = 'patinooooooooooo.pdf'
    elif 'patin' in title_lower: matched_pdf = 'patinooooooooooo.pdf'
    elif 'french fries' in title_lower: matched_pdf = 'french_fries_ubi_jalar.pdf'
    elif 'gaplek' in title_lower: matched_pdf = 'gaplek.pdf'
    elif 'cookies' in title_lower: matched_pdf = 'cookies_ubi_jalar.pdf'
    elif 'dodol' in title_lower: matched_pdf = 'dodol_ubi_jalar.pdf'
    elif 'jahe kristal' in title_lower: matched_pdf = 'jahe_kristal.pdf'
    elif 'jagung goreng' in title_lower: matched_pdf = 'jagung_goreng.pdf'
    elif 'jempeng' in title_lower: matched_pdf = 'jempeng.pdf'
    elif 'kakus cemplung' in title_lower: matched_pdf = 'kakus_cemplung.pdf'
    elif 'kakus sopasandas' in title_lower: matched_pdf = 'kakus_sopasandas.pdf'
    elif 'kakus vietnam' in title_lower: matched_pdf = 'kakus_vietnam.pdf'
    elif 'jamban leher' in title_lower: matched_pdf = 'jamban_leher_angsa.pdf'
    elif 'jamban septik' in title_lower: matched_pdf = 'jamban_septik_tank_ganda.pdf'
    elif 'pengawetan' in title_lower: matched_pdf = 'pengawetan.pdf'
    elif 'jujutsu' in title_lower or 'juknis' in title_lower: matched_pdf = 'juknis_ikan_laut.pdf'
    elif 'pengenalan ikan hias' in title_lower: matched_pdf = 'pengenalan_ikan_hias.pdf'
    elif 'pijah ikan cangkringan' in title_lower: matched_pdf = 'pijah_ikan_cangkringan.pdf'
    elif 'saponin' in title_lower: matched_pdf = 'saponin_basmi_hama_udang.pdf'
    elif 'flavonoid' in title_lower or 'daun pare' in title_lower: matched_pdf = 'EKSTRAKSI_FLAVONOID_DARI_DAUN_PARE_MOMORDICA_CHARA.pdf'
    elif 'pektin kakao' in title_lower: matched_pdf = 'Pektin Kakao.pdf'
    elif 'biji kakao' in title_lower: matched_pdf = 'biji_kakao_fermentasi.pdf'
    elif 'saluran mandi cuci' in title_lower: matched_pdf = 'saluran_mandi_cuci.pdf'
    elif 'sampah' in title_lower: matched_pdf = 'kelola_sampah.pdf'
    elif 'pengusir burung' in title_lower: matched_pdf = None  # No PDF
    elif 'solar home' in title_lower: matched_pdf = None
    elif 'pompa tenaga surya' in title_lower: matched_pdf = None
    elif 'hidroponik' in title_lower: matched_pdf = None  # Using existing
    elif 'lele' in title_lower: matched_pdf = 'lele.pdf'
    elif 'mujair' in title_lower: matched_pdf = 'mujair.pdf'
    elif 'gurame' in title_lower: matched_pdf = 'budidaya_ikan_gurame.pdf'
    elif 'nila' in title_lower: matched_pdf = 'nila.pdf'
    elif 'tawes' in title_lower: matched_pdf = 'pembenihan_ikan_tawes.pdf'
    elif 'bandeng' in title_lower: matched_pdf = 'pembenihan_bandeng00000000.pdf'
    elif 'penggelondongan bandeng' in title_lower: matched_pdf = 'penggelondongan_bandeng.pdf'
    elif 'bawal' in title_lower: matched_pdf = 'pembesaran_ikan_bawal_air_tawar000000.pdf'
    elif 'kakap putih' in title_lower: matched_pdf = 'pembesaran_ikan_kakap_putih.pdf'
    elif 'udang windu' in title_lower: matched_pdf = 'udang_windu.pdf'
    elif 'belut' in title_lower: matched_pdf = 'belut.pdf'
    elif 'bekicot' in title_lower: matched_pdf = 'bekicot.pdf'
    elif 'walet' in title_lower: matched_pdf = 'burung_walet.pdf'
    elif 'puyuh' in title_lower: matched_pdf = 'burung_puyuh.pdf'
    elif 'cacing' in title_lower: matched_pdf = 'cacing_tanah.pdf'
    elif 'domba' in title_lower: matched_pdf = 'domba.pdf'
    elif 'ayam pedaging' in title_lower: matched_pdf = 'ayam_pedaging.pdf'
    elif 'ayam petelur' in title_lower: matched_pdf = 'ayam_petelur.pdf'
    elif 'beronang' in title_lower: matched_pdf = 'budidaya_ikan_beronang.pdf'
    elif 'mas koki' in title_lower: matched_pdf = 'budidaya_ikan_koki_mutiara.pdf'
    elif 'tetra' in title_lower: matched_pdf = 'budidaya_ikan_hias_tetra.pdf'
    elif 'manfish' in title_lower: matched_pdf = 'budidaya_ikan_manfish.pdf'
    elif 'livebearer' in title_lower: matched_pdf = 'budidaya_ikan_hias_livebearer.pdf'
    elif 'oscar' in title_lower: matched_pdf = 'budidaya_ikan_oscar.pdf'
    elif 'tiram mutiara' in title_lower: matched_pdf = 'budidaya_tiram_mutiara.pdf'
    elif 'tiram' in title_lower: matched_pdf = 'budidaya_tiram.pdf'
    elif 'pompa tali' in title_lower: matched_pdf = 'pompa_tali.pdf'
    elif 'pot bunga' in title_lower: matched_pdf = None
    elif 'panel surya' in title_lower: matched_pdf = None
    elif 'abon daging' in title_lower: matched_pdf = 'abon_daging_keluwih.pdf'
    
    if matched_pdf:
        src = PDF_SRC / matched_pdf
        dst = PDF_DST / matched_pdf
        
        if src.exists():
            if not dst.exists():
                shutil.copy2(src, dst)
            
            pdf_path = f'/uploads/pdfs/{matched_pdf}'
            c.execute('UPDATE tutorials SET pdf_path = ? WHERE id = ?', (pdf_path, tid))
            print(f'✅ ID {tid}: {title[:40]}... -> {matched_pdf}')
            updated += 1
        else:
            print(f'⚠️  ID {tid}: PDF not found: {matched_pdf}')
    else:
        print(f'⏭️  ID {tid}: {title[:40]}... (no PDF)')

conn.commit()
print(f'\n✅ Updated {updated} tutorials with PDF links')
conn.close()
