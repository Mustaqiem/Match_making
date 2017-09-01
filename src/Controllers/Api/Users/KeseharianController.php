<?php 

namespace App\Controllers\Api\Users;

use App\Models\Users\UserModel;
use App\Models\Users\UserToken;
use App\Models\Users\KeseharianModel;
use App\Controllers\Api\BaseController;


class KeseharianController extends BaseController
{
    public function createKeseharian($request, $response)
    {
        $keseharian = new KeseharianModel($this->db);
        $users = new UserModel($this->db);
        $userToken = new UserToken($this->db);
        $token = $request->getHeader('Authorization')[0];
        $userId = $userToken->getUserId($token);

        $this->validator->rule('required', ['pekerjaan', 'status_pekerjaan', 'penghasilan_per_bulan', 'status', 'jumlah_anak', 'merokok', 'status_tinggal', 'memiliki_cicilan', 'bersedia_pindah_tinggal']);

        if ($this->validator->validate()) {
            $createData = $keseharian->create($request->getParsedBody(), $userId['user_id']);
            $finds = $keseharian->find('id', $createData);
            $data = $this->responseDetail(200, false, 'Berhasil menambahkan data profil', [
                    'data' => $finds
                ]); 
        } else {
            $data = $this->responseDetail(400, true, $this->validator->errors());
        }
            return $data;
    }

    public function updateKeseharian($request, $response, $args)
    {
        $user      = new UserModel($this->db);
        $userToken = new UserToken($this->db);
        $token = $request->getHeader('Authorization')[0];
        $user = $userToken->getUserId($token);
        $keseharian = new KeseharianModel($this->db);

        $find       = $keseharian->findWithoutDelete('user_id', $user['id']);

        if ($find) {
            $datainput  = $request->getParsedBody();
            $datainput['user_id'] = $user['id'];

            try {
                $keseharian->updateKeseharian($datainput);
                $find       = $keseharian->findWithoutDelete('user_id', $user['id']);

                $data = $this->responseDetail(200, false, 'Data telah terupdate', [
                        'data'  => $find
                    ]);

            } catch (Exception $e) {
                $data = $this->responseDetail(500, true, $e->getMessage);
            }

        } else {
            $data = $this->responseDetail(400, true, 'update data gagal');
        }
        return $data;
    }

    public function findData($request, $response, $args)
    {
        $keseharian = new KeseharianModel($this->db);
        $users = new UserModel($this->db);
        $userToken = new UserToken($this->db);
        $token = $request->getHeader('Authorization')[0];
        $userId = $userToken->getUserId($token);

        $find = $keseharian->find('user_id', $args['id']);

        if ($find) {
            $data = $this->responseDetail(200, false, 'Data keseharian user tersedia', [
                    'data' => $find
                ]);
        } else {
            $data = $this->responseDetail(200, false, 'Data keseharian user tidak ditemukan');
        }

        return $data;
    }
}