<?php 

namespace App\Controllers\Api\Users\Poligami;

use App\Models\Users\UserModel;
use App\Models\Users\UserToken;
use App\Models\Users\Poligami\DipoligamiModel;
use App\Controllers\Api\BaseController;


class DipoligamiController extends BaseController
{
    public function getAll($request, $response)
    {
        $poligami = new DipoligamiModel($this->db);
        $get = $poligami->getAllData();
        $count = count($get);
        $query = $request->getQueryParams();
        if ($get) {
            $page = !$request->getQueryParam('page') ? 1 : $request->getQueryParam('page');
            $getData = $poligami->getAllData()->setPaginate($page, 5);

            if ($getData) {
                $data = $this->responseDetail(200, false,  'Data tersedia', [
                        'data'          =>  $getData['data'],
                        'pagination'    =>  $getData['pagination'],
                    ]);
            } else {
                $data = $this->responseDetail(404, true, 'Data tidak ditemukan');
            }
        } else {
            $data = $this->responseDetail(204, false, 'Tidak ada konten');
        }

        return $data;
    }

    public function createDiPoligami($request, $response)
    {
        $user = new UserModel($this->db);
        $poligami = new DipoligamiModel($this->db);
        $UserToken = new UserToken($this->db);
        $token = $request->getHeader('Authorization')[0];
        $userId = $UserToken->getUserId($token);;
        // var_dump($userId);die();
        $find = $user->getUser('id', $userId);
        // var_dump($find['gender']);die();
        $this->validator->rule('required', ['kesiapan', 'penjelasan_kesiapan']);

        if ($this->validator->validate()) {
            if ($find['gender'] == 'laki-laki') {
                $data = $this->responseDetail(400, true, 'Anda tidak mempunyai akses kesini');
            } else {
                $create = $poligami->createDiPoligami($request->getParsedBody(), $userId);
                // var_dump($create);die();
                $find = $poligami->find('id', $create);
                $data = $this->responseDetail(201, false, 'Profile berhasil dibuat', [
                        'data' => $find,
                    ]);
            }
        } else {
            $data = $this->responseDetail(400, true, $this->validator->errors());
        }   
            return $data;
    }

    public function updateDiPoligami($request, $response, $args)
    {
        $user = new UserModel($this->db);
        $userToken = new UserToken($this->db);
        $token = $request->getHeader('Authorization')[0];
        $userId = $userToken->getUserId($token);
        $poligami = new DipoligamiModel($this->db);

        $find = $poligami->findWithoutDelete('user_id', $userId['id']);
        if ($find) {
            $datainput  = $request->getParsedBody();
            $datainput['user_id'] = $userId['id'];

            try {
                $poligami->updateDiPoligami($datainput);
                $find       = $poligami->findWithoutDelete('user_id', $userId['id']);

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
        $poligami = new DipoligamiModel($this->db);
        $userToken = new UserToken($this->db);
        $token = $request->getHeader('Authorization')[0];
        $userId = $userToken->getUserId($token);

        $find = $poligami->find('user_id', $args['id']);

        if ($find) {
            $data = $this->responseDetail(200, false, 'Data poligami user tersedia', [
                    'data' => $find
                ]);
        } else {
            $data = $this->responseDetail(200, false, 'Data poligami user tidak ditemukan');
        }

        return $data;
    }
}