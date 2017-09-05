<?php
namespace Auth\Controller;

use Application\Service\MessageService;
use Application\Service\TemplateTypes;
use Application\Utility\DataTable;
use Auth\Model\RoleTable;
use Auth\Service\AccessService;
use Auth\Service\UserService;
use Auth\Utility\UserPassword;
use Auth\Form\UserForm;
use Auth\Model\User;
use Exception;
use Media\Service\MediaService;
use Zend\Mvc\Controller\AbstractActionController;

class UserController extends AbstractActionController
{
    /** @var  AccessService */
    protected $accessService;
    /** @var  RoleTable */
    protected $roleTable;
    /** @var MediaService */
    private $mediaService;
    /** @var  UserService */
    private $userService;
    /** @var  MessageService */
    private $messageService;

    public function __construct(AccessService $accessService, RoleTable $roleTable, MediaService $mediaService, UserService $userService, MessageService $messageService)
    {
        $this->accessService = $accessService;
        $this->roleTable = $roleTable;
        $this->mediaService = $mediaService;
        $this->userService = $userService;
        $this->messageService = $messageService;
    }
    public function indexAction()
    {
        $data = $this->userService->getAllUsers()->toArray();

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
                $data = $form->getData();

				//handle user image
				$data = $this->uploadImage($data, $this->userService->getNextId());

                $user->exchangeArray($data);

                if (strlen($form->getData()['password']) > 4) {
                    $userPassword = new UserPassword();
                    $user->password = $userPassword->create($user->password);
                }
                $this->userService->saveUser($user);
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
            $user = $this->userService->getUserById($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('user');
        }

        $userRole = $this->accessService->getRole();

        $allRoles = $this->roleTable->getUserRoles();

        $form = new UserForm($allRoles, $userRole);
        $form->setData($user->getArrayCopy());
        $form->get('submit')->setAttribute('value', 'Edit');
        $status = $form->get('status')->getValue();
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            //merge post data and files
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            if ($post['status'] != $status && $post['status'] != null) {
                $type = ($post['status'] == "1") ? TemplateTypes::ACTIVATION : TemplateTypes::DEACTIVATION;
                $this->messageService->SendMailFromTemplate($post['email'], $type, $user->getArrayCopy());
            }
            $form->setData($post);
            if ($form->isValid()) {
                $data = $form->getData();

				//handle user image
				$data = $this->uploadImage($data);

                $user->exchangeArray($data);
                if (strlen($form->getData()['password']) > 3) {
                    $userPassword = new UserPassword();
                    $user->password = $userPassword->create($user->password);
                }
                $this->userService->saveUser($user);
                // Redirect to list of Users
                return $this->redirect()->toRoute('user');
            }
        }

        return array(
            'id' => $id,
            'user' => $user,
            'form' => $form
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (! $id) {
            return $this->redirect()->toRoute('user');
        }
        $user = $this->userService->getUserById($id);
        if (!$user) {
            //user dosen't exists
            throw new Exception("User with id '$id' does not exists");
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->userService->deleteUserById($id);
                //@todo der löscht nicht arrrg
                $this->deleteRecursive('./Data/users/'.$user->id);
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

	private function uploadImage ($data, $newId = null)
	{
		/** @var Image $imageUpload */
		$imageUpload = $this->image();

		if($newId !== null) $data['id'] = $newId;
		$dataTarget = array();

		// upload and save images
		// =======================
		// === check if there is a upload array
		if ($imageUpload->containsUploadArray($data))
		{
			$uploadedImages = $imageUpload->getUploadArrays();
			// if sth was uploaded
			if ( !empty($uploadedImages) )
			{
				// === create path
				$dataTargetPath = '/users/' . $data['id'] .'/pub/';
				foreach ($uploadedImages as $key => &$uploadedImage)
				{
					list ($fileName, $extension) = $imageUpload->getFileDataFromUpload($data[$key]);
					$uploadFileName = 'profileImage.' . $extension;
					$dataTarget[$key] = $dataTargetPath . $uploadFileName;

					// === upload image
					$imageUpload
						->setData($uploadedImage)
						->setDestination($dataTargetPath)
						->setFileName($uploadFileName);

					$mediaItem = $imageUpload->upload();

					// === process image
					$imageUpload->imageProcessor->load($mediaItem);
					$side = 1000; // @todo implement config
					$imageUpload->imageProcessor->resizeToMaxSide($side);
					$imageUpload->imageProcessor->saveImage();

					$imageUpload->imageProcessor->load($mediaItem);
					$imageUpload->mediaService->createDefaultThumbs($mediaItem);
				}
			};

			// === write paths to item
			$data = $dataTarget + $data;
		}
		return $data;
	}
}