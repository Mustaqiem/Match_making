<?php 

namespace App\Controllers\Api\Users;

use App\Models\Users\UserModel;
use App\Models\Users\UserToken;
use App\Models\Users\LatarBelakangModel;
use App\Controllers\Api\BaseController;

class LatarBelakangController extends BaseController
{
    public function getAll($request, $response)
    {
        $latar = new LatarBelakangModel($this->db);
        $userToken = new UserToken($this->db);
        $token = $request->getHeader('Authorization')[0];
        $userId = $userToken->getUserId($token);
        $get = $latar->getAllData();
        $countLatar = count($get);
        $query = $request->getQueryParams();
        if ($get) {
            $page = !$request->getQueryParam('page') ? 1 : $request->getQueryParam('page');
            $perPage = $request->getQueryParam('perpage');
            $getLatar = $latar->getAllData()->setPaginate($page, $perPage);

            if ($getLatar) {
                $data = $this->responseDetail(200, false,  'Data tersedia', [
                        'data'          =>  $getLatar['data'],
                        'pagination'    =>  $getLatar['pagination'],
                    ]);
            } else {
                $data = $this->responseDetail(404, true, 'Data tidak ditemukan');
            }
        } else {
            $data = $this->responseDetail(204, false, 'Tidak ada konten');
        }

        return $data;
    }

    public function createLatarBelakang($request, $response)
    {
        $latar = new LatarBelakangModel($this->db);
        $userToken = new UserToken($this->db);
        $token = $request->getHeader('Authorization')[0];
        $userId = $userToken->getUserId($token);
        // var_dump($userId);die();

        $this->validator->rule('required', ['pendidikan', 'penjelasan_pendidikan', 'agama', 'penjelasan_agama', 'muallaf', 'baca_quran', 'hafalan', 'keluarga', 'penjelasan_keluarga', 'shalat']);

        if ($this->validator->validate()) {
            $createData = $latar->createLatar($request->getParsedBody(), $userId);
            $finds = $latar->find('id', $createData);
            $data = $this->responseDetail(200, false, 'Berhasil menambahkan data latar belakang', [
                    'data' => $finds
                ]); 
        } else {
            $data = $this->responseDetail(400, true, $this->validator->errors());
        }
            return $data;

    }

    public function updateLatarBelakang($request, $response, $args)
    {
        $user  = new UserModel($this->db);
        $userToken = new UserToken($this->db);
        $token = $request->getHeader('Authorization')[0];
        $userId = $userToken->getUserId($token);
        $latar = new LatarBelakangModel($this->db);

        $find = $latar->find('user_id', $userId);
        $query = $request->getQueryParams();
        $findUser = $user->getUser('id', $userId);
// var_dump($findUser);die;
        if ($find) {
                $latar->updateLatar($request->getParsedBody(), $userId);
                $afterUpdate = $latar->find('user_id', $userId);

                $data = $this->responseDetail(200, false, 'Data berhasil di perbaharui', [
                        'data'  =>  $afterUpdate
                    ]);
        } else {
            $data = $this->responseDetail(404, true, 'Data tidak ditemukan');
        }

        return $data;
    }

    public function findData($request, $response, $args)
    {
        $latar = new LatarBelakangModel($this->db);
        $users = new UserModel($this->db);
        $userToken = new UserToken($this->db);
        $token = $request->getHeader('Authorization')[0];
        $userId = $userToken->getUserId($token);

        $find = $latar->find('user_id', $args['id']);

        if ($find) {
            $data = $this->responseDetail(200, false, 'Data Latar belakang user tersedia', [
                    'data' => $find
                ]);
        } else {
            $data = $this->responseDetail(200, false, 'Data Latar belakang user tidak ditemukan');
        }

        return $data;
    }
}