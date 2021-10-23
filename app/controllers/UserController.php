<?php
declare(strict_types=1);
namespace Linkfire\Assignment\Controllers;

use Phalcon\Mvc\Controller;

class UserController extends Controller
{
    public $userRepo;
    public function initialize()
    {
        $this->userRepo = $this->di->getServiceRepo();
    }
    /**
     * @Route(
     *     '/user/show/{id}',
     *     methods={'GET'},
     *     name='list-user'
     * )
     */
    public function indexAction()
    {
        try {
            $users = $this->userRepo->all()->toArray();
        }catch (\Exception $e) {
            return jsonMessage(false, $e->getMessage());
        }
        return jsonResponse(true, $users);
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
        $selectUser = [];
        try {
            $user = $this->userRepo->find($id);
        }catch (\Exception $e) {
            return jsonMessage(false, $e->getMessage());
        }
        if($user) {
            $selectUser[] = $user;
        }
        return jsonResponse(true, $selectUser);

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

        try {
            $this->userRepo->create([
                'name' => $data->name,
                'lastname' => $data->lastname,
                'mobile' => $data->mobile
            ]);
            return jsonMessage(true, 'user added successfully');
        }catch (\Exception $e) {
            return jsonMessage(false, $e->getMessage());
        }

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
        if(!isset($data['id']) || $data['id'] =='' || !is_int($data['id'])) {
            return jsonMessage(false, 'the field id is required');
        }
        $id = $data['id'];
        $user = $this->userRepo->find($id);
        if(!$user) {
            return jsonMessage(false, 'user is not exist');
        }
        foreach ($data as $key=>$value) {
            if($key == 'id') {
                unset($data['id']);
            }
        }
        try {
            $this->userRepo->updateById($id,$data);
            return jsonMessage(true, 'user updated successfully');

        }catch (\Exception $e) {
            return jsonMessage(false, $e->getMessage());
        }

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
        if(!isset($data->id) || $data->id =='' || !is_int($data->id)) {
            return jsonMessage(false, 'the field id is required');
        }
        $id = $data->id;
        $user = $this->userRepo->find($id);
        if(!$user) {
            return jsonMessage(false, 'user is not exist');
        }
        try {
            $this->userRepo->destroy($id);
            return jsonMessage(true, 'user deleted successfully');
        }catch (\Exception $e) {
            return jsonMessage(false, $e->getMessage());
        }

    }

}

