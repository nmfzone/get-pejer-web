<?php

return [

    'model' => [
        'created' => ':model berhasil ditambahkan.',
        'created_to' => ':model berhasil ditambahkan ke :parent.',
        'updated' => ':model berhasil diperbarui.',
        'deleted' => ':model berhasil dihapus.',
        'deleted_from' => ':model berhasil dihapus dari :parent.',
        'not_found' => ':model dengan :key yang diberikan tidak ditemukan.',
        'cant_deleted' => ':model ini tidak dapat dihapus.',
    ],

    'codes' => [
        401 => 'Tidak terautentikasi.',
        403 => 'Anda tidak mempunyai izin untuk mengakses Endpoint ini!',
        404 => 'Endpoint tidak ditemukan atau HTTP method salah.',
        422 => 'Terdapat kesalahan pada data yang diberikan.',
        500 => 'Terdapat kesalahan pada server!',
    ],

];
