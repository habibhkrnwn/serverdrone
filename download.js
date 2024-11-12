const admin = require('firebase-admin');
const fs = require('fs');
const path = require('path');
const cliProgress = require('cli-progress');

// Inisialisasi aplikasi Firebase
const serviceAccount = require('/home/admin/keyjson/key.json');
admin.initializeApp({
  credential: admin.credential.cert(serviceAccount),
  storageBucket: 'gs://droneapi-c606a.appspot.com'
});

const bucket = admin.storage().bucket();

function ensureDirectoryExistence(dirPath) {
  if (!fs.existsSync(dirPath)) {
    fs.mkdirSync(dirPath, { recursive: true });
    console.log(`Directory created: ${dirPath}`);
  }
}

function isFile(objectName) {
  return path.extname(objectName) !== '';
}

async function downloadFiles(prefix = '', localDir = 'serverdrone/Output/Drone/', retryCount = 3) {
  const options = { prefix };
  const [files] = await bucket.getFiles(options);

  const progressBar = new cliProgress.SingleBar({}, cliProgress.Presets.shades_classic);
  progressBar.start(files.length, 0);

  for (let i = 0; i < files.length; i++) {
    const file = files[i];
    const relativePath = file.name.replace(prefix, '');
    const localPath = path.join(localDir, relativePath);

    if (isFile(file.name)) {
      ensureDirectoryExistence(path.dirname(localPath));

      for (let attempt = 0; attempt < retryCount; attempt++) {
        try {
          await file.download({ destination: localPath });
          break;
        } catch (err) {
          console.error(`Attempt ${attempt + 1} failed to download file: ${file.name}`);
          if (attempt === retryCount - 1) {
            console.error(`Failed to download file after ${retryCount} attempts: ${file.name}`);
          }
        }
      }
    } else {
      ensureDirectoryExistence(localPath);
    }

    progressBar.update(i + 1);
  }

  progressBar.stop();
  console.log('All files have been downloaded successfully.');
}

// Panggil fungsi utama
downloadFiles().catch(error => {
  console.error("Failed to download files:", error);
});
