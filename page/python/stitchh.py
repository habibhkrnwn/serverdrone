import cv2
import os
import gc
import numpy as np

# Fungsi untuk memuat dan mengurangi resolusi gambar dari folder
def load_and_resize_images_from_folder(folder, scale_percent=10):
    if not os.path.exists(folder):
        print("Folder does not exist!")
        return []
    
    images = []
    print("Processing files in folder:")
    for filename in os.listdir(folder):
        img_path = os.path.join(folder, filename)
        if filename.endswith(('.png', '.JPG', '.jpeg')) and os.path.isfile(img_path):
            print(f"Processing file: {filename}")
            img = cv2.imread(img_path)
            if img is None:
                print(f"Skipped file (not a valid image): {filename}")
                continue
            # Mengubah resolusi gambar
            width = int(img.shape[1] * scale_percent / 100)
            height = int(img.shape[0] * scale_percent / 100)
            resized_img = cv2.resize(img, (width, height), interpolation=cv2.INTER_AREA)
            images.append(resized_img)  # Align orientation removed for simplicity
        else:
            print(f"Skipped file (not an image): {filename}")
    return images

# Fungsi untuk memeriksa format gambar
def check_image_format(images):
    for img in images:
        if img is None or len(img.shape) != 3:
            print("Invalid image format or corrupted image detected.")
            return False
    return True

# Fungsi untuk mengurangi resolusi gambar jika melebihi batas tertentu
def resize_if_large(image, max_width=16000, max_height=16000):
    height, width = image.shape[:2]
    if width > max_width or height > max_height:
        scaling_factor = min(max_width / width, max_height / height)
        new_width = int(width * scaling_factor)
        new_height = int(height * scaling_factor)
        print(f"Resizing image from {width}x{height} to {new_width}x{new_height}")
        image = cv2.resize(image, (new_width, new_height), interpolation=cv2.INTER_AREA)
    return image

# Fungsi untuk menjahit gambar menggunakan OpenCV Stitcher dengan overlap antar batch
def stitch_multiple_images_with_overlap(image_folder, output_file, mode=cv2.Stitcher_PANORAMA, batch_size=10, overlap_size=5):
    # Memuat dan mengurangi resolusi gambar
    images = load_and_resize_images_from_folder(image_folder, scale_percent=10)
    
    if len(images) < 2:
        print("Tidak cukup gambar untuk melakukan stitching.")
        return
    
    # Memeriksa format gambar
    if not check_image_format(images):
        print("Invalid image format detected. Exiting process.")
        return
    
    # Menggunakan OpenCV's Stitcher untuk menjahit gambar
    stitcher = cv2.Stitcher_create(mode)
    
    batch_output_images = []  # Menyimpan hasil stitching setiap batch
    
    # Memproses gambar dalam batch dengan overlap
    i = 0
    while i < len(images):
        batch_images = images[i:i + batch_size]
        if len(batch_images) < 2:
            print(f"Batch {i // batch_size + 1} tidak memiliki cukup gambar untuk stitching.")
            break

        print(f"Processing batch {i // batch_size + 1}: {len(batch_images)} images")

        status, stitched_image = stitcher.stitch(batch_images)

        if status == cv2.Stitcher_OK:
            # Mengurangi ukuran gambar hasil stitching jika terlalu besar
            stitched_image = resize_if_large(stitched_image)
            
            # Menyimpan hasil stitching batch
            output_batch_file = f"{output_file[:-4]}batch{i // batch_size + 1}.jpg"
            cv2.imwrite(output_batch_file, stitched_image)
            print(f"Gambar berhasil dijahit untuk batch {i // batch_size + 1} dan disimpan di {output_batch_file}")
            batch_output_images.append(stitched_image)  # Menyimpan hasil batch
        else:
            print(f"Gagal menjahit gambar di batch {i // batch_size + 1}. Status error: {status}")

        # Menggunakan gc.collect untuk membersihkan memori setelah setiap batch
        gc.collect()

        # Geser batch dengan overlap
        i += batch_size - overlap_size  # Menggunakan overlap untuk batch berikutnya
    
    # Menjahit hasil batch menjadi satu gambar akhir
    if len(batch_output_images) > 1:
        print("Menjahit semua hasil batch menjadi satu gambar akhir...")
        status, final_image = stitcher.stitch(batch_output_images)
        
        if status == cv2.Stitcher_OK:
            # Mengurangi ukuran gambar hasil stitching akhir jika terlalu besar
            final_image = resize_if_large(final_image)
            
            final_output_file = output_file  # Nama file akhir
            cv2.imwrite(final_output_file, final_image)
            print(f"Gambar akhir berhasil disimpan di {final_output_file}")
        else:
            print(f"Gagal menjahit gambar akhir. Status error: {status}")
    elif len(batch_output_images) == 1:
        # Jika hanya ada satu hasil batch, tidak perlu menjahit lagi
        final_output_file = output_file
        cv2.imwrite(final_output_file, batch_output_images[0])
        print(f"Hanya satu batch yang dijahit. Gambar akhir disimpan di {final_output_file}")
    else:
        print("Tidak ada gambar batch yang dapat dijahit menjadi hasil akhir.")

# Menjalankan stitching
if __name__ == "__main__":
    image_folder = '../serverdrone/Output/Drone/sawahdata2'  # Ganti sesuai dengan folder di Kaggle
    output_file = 'stitched_output_data2baru.jpg'
    
    print("Starting stitching process...")
    
    # Coba mode PANORAMA
    stitch_multiple_images_with_overlap(image_folder, output_file, mode=cv2.Stitcher_PANORAMA, batch_size=10, overlap_size=5)
    
    # Jika stitching gagal dengan PANORAMA, coba mode SCANS
    if not os.path.exists(output_file):  # Jika output tidak terbentuk
        print("Switching to SCANS mode...")
        stitch_multiple_images_with_overlap(image_folder, output_file, mode=cv2.Stitcher_SCANS, batch_size=10, overlap_size=5)