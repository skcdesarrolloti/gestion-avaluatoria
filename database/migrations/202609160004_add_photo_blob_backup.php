<?php
declare(strict_types=1);
use App\Database\Schema;
use App\Services\AppraisalPhotoStorage;

return static function (Schema $schema): void {
    $schema->addColumn('appraisal_photos', 'file_blob', 'LONGBLOB NULL');
    $rows = $schema->db->query('SELECT id, storage_filename FROM appraisal_photos WHERE file_blob IS NULL')->fetchAll();
    $update = $schema->db->prepare('UPDATE appraisal_photos SET file_blob = ? WHERE id = ?');
    foreach ($rows as $row) {
        $path = AppraisalPhotoStorage::path((string) $row['storage_filename']);
        if (is_file($path)) {
            $blob = file_get_contents($path);
            if (is_string($blob)) $update->execute([$blob, $row['id']]);
        }
    }
};
