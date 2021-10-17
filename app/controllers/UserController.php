<?php
declare(strict_types=1);
namespace Linkfire\Assignment\Controllers;

use Phalcon\Mvc\Controller;

class UserController extends Controller
{
    /**
     * @Route(
     *     '/user/show/{id}',
     *     methods={'GET'},
     *     name='list-user'
     * )
     */
    public function indexAction()
    {
        $userRepo = $this->di->getServiceRepo();
        $users = $userRepo->all()->toArray();
        return $this->response
            ->setJsonContent($users)
            ->send();
    }
    /**
     * @Route(
     *     '/user/show/{id}',
     *     methods={'GET'},
     *     name='show-user'
     * )
     */
    public function showAction($id)
    {
        $userRepo= $this->di->getServiceRepo();
        $selectUser = [];
        $user = $userRepo->find($id);
        if($user) {
            $selectUser[] = $user;
        }
        return $this->response
            ->setJsonContent($selectUser)
            ->send();

    }
    /**
     * @Route(
     *     '/user/add',
     *     methods={'POST'},
     *     name='add-user'
     * )
     */
    public function addAction()
    {
        $data = json_decode($this->request->getRawBody());
        $userRepo= $this->di->getServiceRepo();

        try {
            $userRepo->create([
                'name' => $data->name,
                'lastname' => $data->lastname,
                'mobile' => $data->mobile
            ]);
            $array = array(
                'success' => true,
                'message' => 'user added successfully'
            );
        }catch (\Exception $e) {
            $array = array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
        return $this->response
            ->setJsonContent($array)
            ->send();

    }
    /**
     * @Route(
     *     '/user/update',
     *     methods={'POST'},
     *     name='update-user'
     * )
     */
    public function updateAction()
    {
        $data = json_decode($this->request->getRawBody(), true);
        $userRepo= $this->di->getServiceRepo();
        if(!isset($data['id']) || $data['id'] =='' || !is_int($data['id'])) {
            return $this->response
                ->setJsonContent(array(
                    'success' => false,
                    'message' => 'the field id is required'
                ))
                ->send();
        }
        $id = $data['id'];
        $user = $userRepo->find($id);
        if(!$user) {
            return $this->response
                ->setJsonContent(array(
                    'success' => false,
                    'message' => 'user is not exist'
                ))
                ->send();
        }
        foreach ($data as $key=>$value) {
            if($key == 'id') {
                unset($data['id']);
            }
        }
        try {
            $userRepo->updateById($id,$data);
            $array = array(
                'success' => true,
                'message' => 'user updated successfully'
            );
        }catch (\Exception $e) {
            $array = array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
        return $this->response
            ->setJsonContent($array)
            ->send();

    }
    /**
     * @Route(
     *     '/user/delete',
     *     methods={'POST'},
     *     name='delete-user'
     * )
     */
    public function deleteAction()
    {
        $data = json_decode($this->request->getRawBody());
        $userRepo= $this->di->getServiceRepo();
        if(!isset($data->id) || $data->id =='' || !is_int($data->id)) {
            return $this->response
                ->setJsonContent(array(
                    'success' => false,
                    'message' => 'the field id is required'
                ))
                ->send();
        }
        $id = $data->id;
        $user = $userRepo->find($id);
        if(!$user) {
            return $this->response
                ->setJsonContent(array(
                    'success' => false,
                    'message' => 'user is not exist'
                ))
                ->send();
        }

        try {
            $userRepo->destroy($id);
            $array = array(
                'success' => true,
                'message' => 'user deleted successfully'
            );
        }catch (\Exception $e) {
            $array = array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
        return $this->response
            ->setJsonContent($array)
            ->send();

    }

}

