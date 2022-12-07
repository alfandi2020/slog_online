# Aplikasi BAM Cargo

PROJECT SETUP

- Nafies memberi akses read di repositori Nafies
- Buat Fork di Repo Herdian
- Buka project yang akan kolaborasi
- Fork this repositori
- Advanced Setting
- Name jangan diubah
- Description (Contoh Project Bam Cargo)
- Forking (No Forks)
- Hilangkan centang issue tracking
- Klik tombol Fork Repository
- Clone (Contoh: bam-cargo Akhmad Herdian bukan Nafies Luthfi)
- Copy perintah (git clone git@bitbucket.org:orcome-funk/bam-cargo.git) pada terminal
- Bila telah selesai jalankan $ ll dan lanjutkan $ cd bam-cargo
- Jalankan perintah $ composer install 
- Duplicate .env.example dan rename menjadi .env
- Jalankan perintah $ php artisan key:generate
- Buat database name lv_2017_bam dengan collation utf8-general_ci
- Buat (lagi untuk testing) database name lv_2017_bam_test dengan collation utf8-general_ci
- Setting .env sesuaikan nama databasenya
- Pastikan setting phpunit.xml
  <env name="DB_DATABASE" value="lv_2017_bam_test"/>
- Jalankan perintah $ php artisan migrate --seed
- Ubah nama databasenya di .env untuk migrate database testingnya. (lv_2017_bam_test)
- Jalankan perintah $ php artisan migrate --seed
- Kembalikan seting .env untuk database ke database utama. (lv_2017_bam)
- Jalankan $ vendor/bin/phpunit dan pastikan hijau
- Jalankan $ sudo chmod 777 -R storage
- Jalankan aplikasi di browser dan login sesuai user yang ada dalam database