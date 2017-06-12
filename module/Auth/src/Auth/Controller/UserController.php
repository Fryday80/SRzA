<?php
namespace Auth\Controller;

use Application\Utility\DataTable;
use Auth\Model\RoleTable;
use Auth\Model\UserTable;
use Auth\Service\AccessService;
use Auth\Utility\UserPassword;
use Auth\Form\UserForm;
use Auth\Model\User;
use Exception;
use Media\Service\MediaService;
use Zend\Form\View\Helper\FormDate;
use Zend\Mvc\Controller\AbstractActionController;

class UserController extends AbstractActionController
{
    /** @var  AccessService */
    protected $accessService;
    /** @var UserTable */
    protected $userTable;
    /** @var  RoleTable */
    protected $roleTable;
    /** @var MediaService */
    private $mediaService;

    public function __construct(UserTable $userTable, AccessService $accessService, RoleTable $roleTable, MediaService $mediaService)
    {
        $this->accessService = $accessService;
        $this->userTable = $userTable;
        $this->roleTable = $roleTable;
        $this->mediaService = $mediaService;
    }
    public function indexAction()
    {
        $data = $this->userTable->getUsers()->toArray();

        $userDataTable = new DataTable( array( 'data' => $data ));
        $userDataTable->insertLinkButton('/user/add', "Neuer Benutzer");
        $userDataTable->setColumns( array (
            array (
                'name'  => 'name',
                'label' => 'Name'
            ),
            array (
                'name'  => 'email',
                'label' => 'eMail'
            ),
            array (
                'name'  => 'role_name',
                'label' => 'Rolle'
            ),
            array (
                'name'  => 'href',
                'label' => 'Aktion',
                'type'  => 'custom',
                'render' => function ($row){
                    $edit = '<a href="user/edit/' . $row['id'] . '">Edit</a>';
                    $delete = '<a href="user/delete/' . $row['id'] . '">Delete</a>';
                    return $edit.' '.$delete;
                }
            ),
        ) );
        return array(
            'users' => $userDataTable
        );
    }

    public function addAction()
    {
        $userRole = $this->accessService->getRole();

        $allRoles = $this->roleTable->getUserRoles();


        $form = new UserForm($allRoles, $userRole);
        $form->get('submit')->setValue('Add');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            //merge post data and files
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $user = new User();
            $form->setData($post);
            if ($form->isValid()) {
                $formData = $form->getData();
                $user->exchangeArray($formData);

                if (strlen($form->getData()['password']) > 4) {
                    $userPassword = new UserPassword();
                    $user->password = $userPassword->create($user->password);
                }

                //handle user image
                if ($formData['user_image'] === null || $formData['user_image']['error'] > 0) {
                    //@todo no image or image upload error
                } else {
                    $userPic = $formData['user_image']['tmp_name'];
                    $dataPath = realpath('./data');
                    @mkdir($dataPath . '/_users', 0755);
                    @mkdir($dataPath . '/_users/' . $user->id, 0755);
                    @mkdir($dataPath . '/_users/' . $user->id . '/pub', 0755);

                    $imageName = '/profileImage.' . pathinfo($formData['user_image']['name'], PATHINFO_EXTENSION);
                    $url = '/media/file/_users/' . $user->id . '/pub' . $imageName;

                    $newPath = realpath('./data/_users/' . $user->id . '/pub');
                    $newPath = $newPath . $imageName;
                    rename($userPic, $newPath);
                    $user->user_image = $url;
                }
                $this->userTable->saveUser($user);
                return $this->redirect()->toRoute('user');
            }
        }
        return array(
            'form' => $form
        );
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (! $id) {
            return $this->redirect()->toRoute('user');
        }
        
        try {
            $user = $this->userTable->getUser($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('user');
        }

        $userRole = $this->accessService->getRole();

        $allRoles = $this->roleTable->getUserRoles();

        $form = new UserForm($allRoles, $userRole);
        $form->setData($user->getArrayCopy());
        $form->get('submit')->setAttribute('value', 'Edit');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            //merge post data and files
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $form->setData($post);
            if ($form->isValid()) {
                $formData = $form->getData();
                $user->exchangeArray($formData);
                if (strlen($form->getData()['password']) > 3) {
                    $userPassword = new UserPassword();
                    $user->password = $userPassword->create($user->password);
                }

                //handle user image
                if ($formData['user_image'] === null || $formData['user_image']['error'] > 0) {
                    //@todo no image or image upload error
                } else {
                    $userPic = $formData['user_image']['tmp_name'];
                    $dataPath = realpath('./data');
                    @mkdir($dataPath . '/_users', 0755);
                    @mkdir($dataPath . '/_users/' . $user->id, 0755);
                    @mkdir($dataPath . '/_users/' . $user->id . '/pub', 0755);

                    $imageName = '/profileImage.' . pathinfo($formData['user_image']['name'], PATHINFO_EXTENSION);
                    $url = '/media/file/_users/' . $user->id . '/pub' . $imageName;

                    $newPath = realpath('./data/_users/' . $user->id . '/pub');
                    $newPath = $newPath . $imageName;
                    //@todo serach old image and unlink (files can have different extensions)
                    @unlink($newPath);
                    rename($userPic, $newPath);
                    $user->user_image = $url;
                }
                $this->userTable->saveUser($user);
                // Redirect to list of Users
                return $this->redirect()->toRoute('user');
            }
        }

        return array(
            'id' => $id,
            'form' => $form
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (! $id) {
            return $this->redirect()->toRoute('user');
        }
        $user = $this->userTable->getUser($id);
        if (!$user) {
            //user dosen't exists
            throw new Exception("User with id '$id' does not exists");
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->userTable->deleteUser($id);
                //@todo der löscht nicht arrrg
                $this->deleteRecursive('./Data/_users/'.$user->id);
            }
            // Redirect to list of Users
            return $this->redirect()->toRoute('user');
        }
        
        return array(
            'id' => $id,
            'user' => $user,
        );
    }
    private function deleteRecursive($path) {
        $realPath = realpath($path);
        if (is_dir($realPath)){
            $files = glob($realPath.'/*', GLOB_MARK); //GLOB_MARK adds a slash to directories returned
            foreach ($files as $file)
            {
                $this->deleteRecursive( $file );
            }

            rmdir($realPath);
        } elseif (is_file($realPath)) {
            unlink($realPath);
        }
    }
}
