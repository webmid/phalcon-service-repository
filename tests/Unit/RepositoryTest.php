<?php


namespace Unit;

use MyApp\Models\User;
use PhalconRepositories;
use Tests\Unit\AbstractUnitTest;


class RepositoryTest extends AbstractUnitTest
{
    public function testCreate()
    {
        parent::setUp();
        $userNumber = 3;

        $usersRepo = new UsersRepo();
        $usersRepo->truncate();

        $this->createUsers($usersRepo, $userNumber);

        $users = \MyApp\Models\User::find();
        $this->assertEquals(3, count($users));
    }

    public function testAll()
    {
        $usersRepo = new UsersRepo();
        $users = $usersRepo->all();
        $this->assertEquals(3, count($users));
    }

    public function testFind()
    {
        $userId = 1;

        $usersRepo = new UsersRepo();
        $user = $usersRepo->find($userId);
        $this->assertEquals($userId, $user->getId());
    }

    public function testFindOrFail()
    {
        $userId = 1;
        $usersRepo = new UsersRepo();
        $user = $usersRepo->FindOrFail($userId);
        $this->assertEquals($userId, $user->getId());
    }

    /**
     * @expectedException \PhalconRepositories\ModelNotFoundException
     */
    public function testFailingFindOrFail()
    {
        $usersRepo = new UsersRepo();
        $this->expectException('Exception');
        $usersRepo->FindOrFail(1000);

    }

    public function testFirst()
    {
        $usersRepo = new UsersRepo();
        $user = $usersRepo->first();
        $this->assertEquals(1, $user->getId());
    }

    public function testFirstOrFail()
    {
        $userId = 1;
        $usersRepo = new UsersRepo();
        $user = $usersRepo->FirstOrFail();
        $this->assertEquals($userId, $user->getId());
    }

    public function testFirstBy()
    {
        $usersRepo = new UsersRepo();
        $user = $usersRepo->firstBy(['id' => 1]);
        $this->assertEquals(1, $user->getId());
    }

    /**
     * @expectedException \PhalconRepositories\ModelNotFoundException
     */
    public function testFailingFirstOrFailBy()
    {
        $usersRepo = new UsersRepo();
        $this->expectException('Exception');
        $usersRepo->firstOrFailBy(['name' => 'Error']);
    }

    public function testGetBy()
    {
        $usersRepo = new UsersRepo();
        $users = $usersRepo->getBy(['name' => 'name 2']);
        $this->assertEquals(1, count($users));
        $this->assertEquals('name 2', $users->getFirst()->getName());
    }

    public function testGetByWithLikeUsername()
    {
        $usersRepo = new UsersRepo();
        $data = [
            'name%OR%lastname' => ['%unique%', 'LIKE']
        ];

        $users = $usersRepo->getBy($data);
        $this->assertEquals(1, count($users));
    }


    public function testGetByLimit()
    {
        $usersRepo = new UsersRepo();
        $users = $usersRepo->getByOrder('id', [], 'desc', 2);
        $this->assertEquals(2, count($users));
        $this->assertEquals(3, $users->getFirst()->getId());
    }

    public function testGetIn()
    {
        $usersRepo = new UsersRepo();
        $users = $usersRepo->getIn('id', [2, 3]);
        $this->assertEquals(2, count($users));
        $this->assertEquals(2, $users->getFirst()->getId());
    }

    public function testGetNotIn()
    {
        $usersRepo = new UsersRepo();
        $users = $usersRepo->getNotIn('id', [2, 3]);
        $this->assertEquals(1, count($users));
        $this->assertEquals(1, $users->getFirst()->getId());
    }

    public function testGetInAndWhereByPage()
    {
        $idArray = [2, 3, 4];
        $usersRepo = new UsersRepo();
        $users = $usersRepo->getInAndWhereByPage(1, 2,'id', $idArray);
        $this->assertEquals(2, count($users));
        foreach ($users as $user) {
            $this->assertTrue(in_array($user->getId(), $idArray));
        }
    }

    public function testGetByPage()
    {
        $usersRepo = new UsersRepo();
        $users = $usersRepo->getByPage(2, 1, [], 'id', 'asc');
        $this->assertEquals(1, count($users));
        $this->assertEquals(2, $users->getFirst()->getId());
    }

    public function testGetByGroupBy()
    {
        $usersRepo = new UsersRepo();
        $users = $usersRepo->all();
        $users[2]->setLastname($users[1]->getLastname());
        $users[2]->save();

        $usersList = $usersRepo->getByGroupBy('lastname', [], true);

        $this->assertEquals(3, count($usersList));
        $this->assertTrue($usersList[0]['lastname'] === $users[0]->getLastname());
        $this->assertTrue((int)$usersList[0]['number'] === 1);
        $this->assertTrue($usersList[1]['lastname'] === $users[1]->getLastname());
        $this->assertTrue((int)$usersList[1]['number'] === 1);
    }

    public function testUpdateById()
    {
        $userId = 2;
        $usersRepo = new UsersRepo();
        $usersRepo->updateById($userId, ['lastname' => 'Family 3']);
        $user = $usersRepo->findOrFail($userId);
        $this->assertEquals('Family 3', $user->getLastname());
    }

    public function testUpdateBy()
    {
        $usersRepo = new UsersRepo();
        $usersRepo->updateBy(['lastname' => 'Family 3'], ['lastname' => 'Family 2']);
        $user = $usersRepo->getBy(['lastname' => 'Family 2'])->toArray()[0];
        $this->assertEquals('Family 2', $user['lastname']);
    }

    /**
     * @expectedException \PhalconRepositories\ModelNotFoundException
     */
    public function testDestroy()
    {
        $userId = 1;
        $usersRepo = new UsersRepo();
        $usersRepo->destroy($userId);
        $this->expectException('Exception');
        $usersRepo->findOrFail($userId);
    }

    /**
     * @expectedException \PhalconRepositories\ModelNotFoundException
     */
    public function testDestroyFirstBy()
    {
        $userId = 3;
        $usersRepo = new UsersRepo();
        $usersRepo->destroyFirstBy(['id' => $userId]);
        $this->expectException('Exception');
        $usersRepo->findOrFail($userId);
    }


    public function testCount()
    {
        $userNumber = 3;

        $usersRepo = new UsersRepo();
        $usersRepo->truncate();
        $this->createUsers($usersRepo, $userNumber);
        $countedUserNumber = $usersRepo->count();
        $this->assertEquals($userNumber, $countedUserNumber);
    }

    public function testCountBy()
    {
        $usersRepo = new UsersRepo();
        $countedUserNumber = $usersRepo->countBy(['name' => 'name 1']);
        $this->assertEquals(1, $countedUserNumber);
    }

    public function testCountByLikeUsername()
    {
        $usersRepo = new UsersRepo();
        $countedUserNumber = $usersRepo->countBy(['name%OR%lastname' => ['%Unique%', 'LIKE']]);
        $this->assertEquals(1, $countedUserNumber);
    }


    protected function createUsers(UsersRepo $userRepo, $number = 3): ?array
    {
        if ($number <= 0) {
            return null;
        }

        $users = [];

        for ($i = 1; $i <= $number - 1 ; $i++) {

            $users[] = $userRepo->create([
                'name' => 'name ' . $i,
                'lastname' => 'Family ' . $i,
                'mobile' => '+995562435'.$i
            ]);
        }
        $users[] = $userRepo->create([
            'name' => 'unique name',
            'lastname' => 'unique family',
            'mobile' => '+99556243500'
        ]);


        return $users;
    }
}
class UsersRepo extends PhalconRepositories\ServiceRepository
{

    protected $model;

    function __construct()
    {
        $this->model = new User();
    }
}



