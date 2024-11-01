import cv2
import os
import sys  # Import sys to read command line arguments

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
            images.append(resized_img)
        else:
            print(f"Skipped file (not an image): {filename}")
    return images

# Fungsi untuk menjahit gambar menggunakan OpenCV Stitcher
def stitch_multiple_images_with_overlap(image_folder, output_file, mode=cv2.Stitcher_PANORAMA, batch_size=10, overlap_size=5):
    images = load_and_resize_images_from_folder(image_folder, scale_percent=10)
    if len(images) < 2:
        print("Tidak cukup gambar untuk melakukan stitching.")
        return
    
    stitcher = cv2.Stitcher_create(mode)
    batch_output_images = []

    i = 0
    while i < len(images):
        batch_images = images[i:i + batch_size]
        if len(batch_images) < 2:
            print(f"Batch {i // batch_size + 1} tidak memiliki cukup gambar untuk stitching.")
            break

        print(f"Processing batch {i // batch_size + 1}: {len(batch_images)} images")
        status, stitched_image = stitcher.stitch(batch_images)

        if status == cv2.Stitcher_OK:
            output_batch_file = f"{output_file[:-4]}_batch{i // batch_size + 1}.jpg"
            cv2.imwrite(output_batch_file, stitched_image)
            print(f"Batch image stitched and saved as {output_batch_file}")
            batch_output_images.append(stitched_image)
        else:
            print(f"Failed to stitch batch {i // batch_size + 1}. Error status: {status}")

        i += batch_size - overlap_size

    # Stitch batch output images into a final output
    if len(batch_output_images) > 1:
        print("Stitching all batch results into a final image...")
        status, final_image = stitcher.stitch(batch_output_images)
        if status == cv2.Stitcher_OK:
            cv2.imwrite(output_file, final_image)
            print(f"Final image successfully saved as {output_file}")
        else:
            print(f"Failed to stitch final image. Error status: {status}")
    elif len(batch_output_images) == 1:
        cv2.imwrite(output_file, batch_output_images[0])
        print(f"Only one batch stitched. Final image saved as {output_file}")

# Menjalankan stitching
if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Usage: python Stitch.py <input_folder> <output_file>")
    else:
        image_folder = sys.argv[1]
        output_file = sys.argv[2]
        if not os.path.isdir(image_folder):
            print("Specified folder is not valid.")
        else:
            print("Starting stitching process...")
            stitch_multiple_images_with_overlap(image_folder, output_file)
