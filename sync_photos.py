#!/usr/bin/env python3
"""
Synchronize photos from ttg/foto with articles in the database.
Replaces placeholders with appropriate real photos.
"""
import sqlite3
import os
from pathlib import Path
import shutil

BASE_DIR = Path(__file__).parent
DB_PATH = BASE_DIR / 'database' / 'ttg_pondokrejo.db'
FOTO_DIR = BASE_DIR / 'ttg' / 'foto'
UPLOAD_DIR = BASE_DIR / 'public' / 'uploads'

# Define photo mapping based on keywords
# Format: (article_keyword, [photo_keywords], new_filename)
MAPPING = [
    # Perikanan
    ('mujair', ['mujair', 'tilapia'], 'mujair.jpg'),
    ('lele', ['lele', 'catfish', 'catfish.jpg'], 'lele.jpg'),
    ('mas', ['ikan mas', 'mas'], 'mas.jpg'),
    ('nila', ['nila'], 'nila.jpg'),
    ('gurame', ['gurame'], 'gurame.jpg'),
    ('bandeng', ['bandeng', 'Abon-Ikan-Bandeng'], 'bandeng.jpg'),
    ('kerapu', ['kerapu', 'Ikan-Kerapu', 'kerapu ikan'], 'kerapu.jpg'),
    ('kakap', ['kakap', 'kakap putih'], 'kakap.jpg'),
    ('tawes', ['tawes', 'TAWES'], 'tawes.jpg'),
    ('patin', ['patin'], 'patin.jpg'),
    ('belut', ['belut', 'belut banyak', 'belut1', 'belut.jpeg'], 'belut.jpg'),
    ('bawal', ['bawal'], 'bawal.jpg'),
    ('teripang', ['teripang', 'teripang 3'], 'teripang.jpg'),
    ('udang', ['udang', 'udang windu'], 'udang.jpg'),
    ('ikan hias', ['ikan hias', 'manfish', 'Ikan-Manfish', 'manfish'], 'manfish.jpg'),
    ('tiram', ['tiram', 'tiram mutiara', 'manfaat-konsumsi-tiram'], 'tiram.jpg'),
    ('pengenalan ikan', ['ikan-hias', 'ikan discus', 'Ikan-Rainbow'], 'ikan-hias.jpg'),
    
    # Peternakan
    ('ayam', ['ayam', 'Ayam-broiler', 'Fakta Ayam'], 'ayam.jpg'),
    ('puyuh', ['puyuh', 'quail', 'Brown_Quail', 'puuyuh'], 'puyuh.jpg'),
    ('walet', ['walet', 'sarang walet', 'peluang-bisnis-sarang'], 'walet.jpg'),
    ('bekicot', ['bekicot', 'Escargot'], 'bekicot.jpg'),
    ('cacing', ['cacing', 'earthworm'], 'cacing.jpg'),
    ('domba', ['domba', 'kambing', 'ternak-kambing'], 'domba.jpg'),
    
    # Pengairan
    ('instalasi', ['bambu', 'bambu penyalur', 'bambu saluran', 'Watering-bamboo'], 'instalasi_bambu.jpg'),
    ('pompa', ['pompa', 'water pump', 'pompa air manual'], 'pompa.jpg'),
    ('mata air', ['air jernih', 'spring'], 'mata_air.jpg'),
    
    # Pengolahan Limbah / Penjernihan
    ('penjernihan', ['penjernihan', 'pengolahan air', 'Menjernihkan-Air', 'kolam penjernihan', 'Diy-Water-Filters'], 'penjernihan.jpg'),
    ('arang sekam', ['arang', 'pemjernih air arang', 'Active-charcoal'], 'arang_sekam.jpg'),
    ('kelor', ['kelor', 'biji kelor'], 'kelor.jpg'),
    ('gambut', ['gambut', 'pengolahan air'], 'gambut.jpg'),
    
    # Sanitasi
    ('jamban', ['jamban', 'cuci tangan', 'kakus'], 'jamban.jpg'),
    ('saluran', ['saluran mandi', 'cuci piring', 'hipwee-laundry'], 'saluran.jpg'),
    
    # Pengolahan Pangan
    ('ubi', ['ubi', 'ubi ungu', 'ubi jalar', 'ilustrasi-ubi'], 'ubi.jpg'),
    ('pala', ['pala', 'buah pala', 'minyak atsiri'], 'pala.jpg'),
    ('kakao', ['kakao', 'fermentasi', 'biji kakao'], 'kakao.jpg'),
    ('sawit', ['sawit', 'kelapa'], 'sawit.jpg'),
    ('pengawetan', ['pengawetan'], 'pengawetan.jpg'),
    
    # Pertanian
    ('hidroponik', ['hidroponik', 'sayuran', 'sayur'], 'hidroponik.jpg'),
    ('jerami', ['jerami', 'amoniasi'], 'jerami.jpg'),
    ('sawah', ['sawah', 'rice fields'], 'sawah.jpg'),
    
    # Energi
    ('surya', ['solar', 'surya', 'panel'], 'surya.jpg'),
    
    # Berita / Info
    ('berita', ['edukasi', 'edukas', 'ide', 'logo', 'ttglogo'], 'edukasi.png'),
]

# Photos to ignore (logos, icons, generic stuff)
IGNORE_PATTERNS = [
    'logo', 'icon', 'logottg', 'LOGO', 'mokup', 'usahrin', 'usahrantisan',
    'ttglogo', 'logosmall', 'whitelogottg', 'ttgwikiblue', 'logottghijau'
]

def get_available_photos():
    """Get list of available photos in ttg/foto directory."""
    photos = []
    for f in FOTO_DIR.iterdir():
        if f.is_file() and f.suffix.lower() in ['.jpg', '.jpeg', '.png', '.webp']:
            # Skip ignored patterns
            skip = False
            for pat in IGNORE_PATTERNS:
                if pat.lower() in f.name.lower():
                    skip = True
                    break
            if not skip:
                photos.append(f)
    return photos

def find_best_photo(title, photo_filename):
    """Check if a photo matches an article title."""
    title_lower = title.lower()
    photo_lower = photo_filename.lower()
    
    # Check if any keyword from mapping matches
    for mapping in MAPPING:
        article_keywords = [mapping[0]]
        photo_keywords = mapping[1]
        
        # Check if article title matches
        if any(kw in title_lower for kw in article_keywords):
            # Check if photo matches any of the photo keywords
            if any(kw in photo_lower for kw in photo_keywords):
                return True
    return False

def main():
    conn = sqlite3.connect(DB_PATH)
    c = conn.cursor()
    
    # Get all articles with placeholders or wrong images
    c.execute("SELECT id, title, image_path FROM tutorials ORDER BY id")
    articles = c.fetchall()
    
    photos = get_available_photos()
    updated_count = 0
    skipped_count = 0
    
    print(f"Found {len(photos)} usable photos in ttg/foto")
    print(f"Checking {len(articles)} articles...\n")
    
    for article_id, title, current_image in articles:
        title_lower = title.lower()
        
        # Skip if already has a good specific image (not placeholder)
        if current_image and not any(placeholder in current_image.lower() for placeholder in [
            'placeholder', 'default', 'no-photo', 'dummy'
        ]):
            # Check if current image exists
            img_path = UPLOAD_DIR / current_image.lstrip('/uploads/')
            if img_path.exists():
                # Check if it's a generic placeholder we made earlier
                img_name = img_path.name.lower()
                is_placeholder = any(p in img_name for p in ['placeholder', 'dummy', 'default'])
                if not is_placeholder:
                    skipped_count += 1
                    continue
        
        # Try to find a matching photo
        matched_photo = None
        for photo in photos:
            if find_best_photo(title, photo.name):
                matched_photo = photo
                break
        
        if matched_photo:
            # Determine new filename
            # Use a simplified name based on article title
            new_filename = f"article_{article_id}{matched_photo.suffix}"
            dest_path = UPLOAD_DIR / new_filename
            
            # Copy photo if not already copied
            if not dest_path.exists():
                shutil.copy2(matched_photo, dest_path)
            
            # Update database
            new_image_path = f'/uploads/{new_filename}'
            c.execute("UPDATE tutorials SET image_path = ? WHERE id = ?", (new_image_path, article_id))
            
            print(f"✅ Updated ID {article_id}: {title[:40]}... -> {matched_photo.name}")
            updated_count += 1
        else:
            print(f" No match for ID {article_id}: {title[:40]}...")
    
    conn.commit()
    print(f"\n{'='*50}")
    print(f"Summary:")
    print(f"  Updated: {updated_count}")
    print(f"  Skipped (already good): {skipped_count}")
    print(f"{'='*50}")
    
    conn.close()

if __name__ == '__main__':
    main()
