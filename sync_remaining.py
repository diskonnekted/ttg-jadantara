import sqlite3
import os
import shutil
from pathlib import Path

BASE = Path('.')
DB = BASE / 'database' / 'ttg_pondokrejo.db'
FOTO = BASE / 'ttg' / 'foto'
UPLOADS = BASE / 'public' / 'uploads'

conn = sqlite3.connect(DB)
c = conn.cursor()

updates = [
    # (article_id, photo_filename_in_foto, new_filename_in_uploads)
    (77, 'air gambut.jpeg', 'kelola_air_gambut.jpeg'),
    (64, 'french fries ubi jalar.webp', 'french_fries_ubi_jalar.webp'),
    (65, 'gaplek ubi kayu.webp', 'gaplek_ubi_kayu.webp'),
    (69, 'jahe kristal.jpg', 'jahe_kristal.jpg'),
    (72, 'jempeng - saringan air batu padas.jpg', 'jempeng.jpg'),
    (74, 'kakus cemplung.jpg', 'kakus_cemplung.jpg'),
    (76, 'kakus vietnam.jpg', 'kakus_vietnam.jpg'),
    (80, 'kelola liombah kakus 1.jpg', 'kelola_limbah_kakus1.jpg'),
    (81, 'kelola limbah kakus 2.jpg', 'kelola_limbah_kakus2.jpg'),
    (21, 'tiram mutiara.jpg', 'tiram_mutiara.jpg'),
]

for aid, foto_name, upload_name in updates:
    src = FOTO / foto_name
    dst = UPLOADS / upload_name
    
    if src.exists():
        if not dst.exists():
            shutil.copy2(src, dst)
        
        c.execute('UPDATE tutorials SET image_path = ? WHERE id = ?', (f'/uploads/{upload_name}', aid))
        print(f'✅ ID {aid} -> {upload_name}')
    else:
        print(f'❌ Source not found: {foto_name}')

conn.commit()
conn.close()
print('✅ Done!')
