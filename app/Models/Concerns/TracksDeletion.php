<?php

namespace App\Models\Concerns;

use App\Models\RiwayatData;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * Soft delete + pencatatan siapa yang menghapus.
 *
 * Pasang di model neraca cukup dengan `use TracksDeletion;` dan
 * `protected string $riwayatDomain = 'batubara';` (slug = nilai domain_akses).
 *
 * Yang otomatis terjadi:
 *  - delete()        -> deleted_at + deleted_by terisi, masuk "Sampah", tercatat di riwayat_data
 *  - restore()       -> deleted_at & deleted_by dikosongkan, tercatat di riwayat_data
 *  - forceDelete()   -> hilang permanen, tercatat di riwayat_data (snapshot terakhir disimpan)
 *
 * Jadi controller lama TIDAK perlu diubah: $model->delete() sudah otomatis jadi soft delete.
 */
trait TracksDeletion
{
    use SoftDeletes;

    public static function bootTracksDeletion(): void
    {
        // Fire setelah delete(). Untuk forceDelete() event ini juga ikut fire, jadi disaring.
        static::deleted(function ($model) {
            if ($model->isForceDeleting()) {
                return;
            }

            $userId = auth()->id();

            // runSoftDelete() cuma meng-update deleted_at, jadi deleted_by kita isi terpisah.
            DB::table($model->getTable())
                ->where($model->getKeyName(), $model->getKey())
                ->update(['deleted_by' => $userId]);

            $model->setAttribute('deleted_by', $userId);
            $model->syncOriginalAttribute('deleted_by');

            $model->catatRiwayat('dihapus');
        });

        // restore() menyimpan atribut yang dirubah di sini, jadi deleted_by ikut ter-null-kan.
        static::restoring(function ($model) {
            $model->deleted_by = null;
        });

        static::restored(function ($model) {
            $model->catatRiwayat('dipulihkan');
        });

        static::forceDeleted(function ($model) {
            $model->catatRiwayat('dihapus_permanen');
        });
    }

    /** Siapa yang menghapus (null kalau data tidak sedang di sampah). */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function catatRiwayat(string $aksi): void
    {
        $user = auth()->user();

        RiwayatData::create([
            'domain'    => property_exists($this, 'riwayatDomain') ? $this->riwayatDomain : 'lainnya',
            'aksi'      => $aksi,
            'data_id'   => $this->getKey(),
            'nama_data' => (string) ($this->nama_objek ?? ('#' . $this->getKey())),
            'user_id'   => $user?->id,
            'user_nama' => $user?->name,
            'user_role' => $user?->role,
            'snapshot'  => json_encode($this->getAttributes(), JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR),
        ]);
    }
}
