const admin = require('firebase-admin');
const fs = require('fs');
const path = require('path');

// Inisialisasi aplikasi Firebase
const serviceAccount = require('./firebasekey/droneapi-c606a-firebase-adminsdk-5cnz0-29a51dc44d.json');
admin.initializeApp({
  credential: admin.credential.cert(serviceAccount),
  storageBucket: 'gs://droneapi-c606a.appspot.com'
});

const bucket = admin.storage().bucket();

// Fungsi untuk memastikan bahwa direktori eksis
function ensureDirectoryExistence(dirPath) {
  if (!fs.existsSync(dirPath)) {
    fs.mkdirSync(dirPath, { recursive: true });
    console.log(`Directory created: ${dirPath}`);
  }
}

// Fungsi untuk memeriksa apakah objek adalah file atau folder
function isFile(objectName) {
  return path.extname(objectName) !== ''; // Jika memiliki ekstensi, ini adalah file
}

// Fungsi untuk mendownload file dan folder
async function downloadFiles(prefix = '', localDir = 'serverdrone/Output/Drone/') {
  const options = { prefix };
  const [files] = await bucket.getFiles(options);

  for (let i = 0; i < files.length; i++) {
    const file = files[i];
    const relativePath = file.name.replace(prefix, ''); // Mendapatkan path relatif
    const localPath = path.join(localDir, relativePath);

    if (isFile(file.name)) {
      ensureDirectoryExistence(path.dirname(localPath)); // Pastikan direktori target ada
      try {
        await file.download({ destination: localPath });
        const progress = ((i + 1) / files.length * 100).toFixed(2);
        console.log(`Progress: ${progress}% - Downloaded file: ${file.name} to ${localPath}`);
      } catch (err) {
        console.error(`Failed to download file: ${file.name}`, err);
      }
    } else {
      ensureDirectoryExistence(localPath);
      console.log(`Created folder: ${localPath}`);
    }
  }
}

// Panggil fungsi utama
downloadFiles().catch(console.error);
