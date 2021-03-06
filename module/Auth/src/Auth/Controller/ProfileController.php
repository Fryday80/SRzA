<?php
namespace Auth\Controller;

use Application\Controller\Plugin\ImagePlugin;
use Application\Service\StatisticService;
use Application\Utility\DataTable;
use Application\Utility\URLModifier;
use Cast\Form\CharacterForm;
use Auth\Service\AccessService;
use Auth\Service\UserService;
use Auth\Utility\UserPassword;
use Cast\Model\Tables\FamiliesTable;
use Cast\Model\Tables\JobTable;
use Cast\Service\CastService;
use vakata\database\Exception;
use Zend\Mvc\Controller\AbstractActionController;
use Auth\Form\UserForm;
use Auth\Model\User;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ProfileController extends AbstractActionController
{
    /** @var FamiliesTable  */
    protected $familyTable;
    /** @var JobTable  */
    protected $jobTable;
    /** @var StatisticService  */
    protected $statsService;
    /** @var CastService  */
    protected $castService;
    /** @var UserService  */
    protected $userService;
    /** @var AccessService  */
    private $accessService;

    function __construct(
        FamiliesTable $familyTable,
        JobTable $jobTable,
        StatisticService $statService,
        CastService $castService,
        AccessService $accessService,
        UserService $userService
    )
    {
        $this->familyTable = $familyTable;
        $this->jobTable = $jobTable;
        $this->statsService = $statService;
        $this->castService = $castService;
        $this->accessService = $accessService;
        $this->userService = $userService;
    }

    function jsonAction() {
        $request = json_decode($this->getRequest()->getContent());
        $result = ['error' => false];
        $userID =  $this->accessService->getUserID();
        try {
            switch ($request->method) {
                case 'getChars':
                    try {
                        if (!isset($request->userID)) {
                            $result['data'] = $this->castService->getCharsByUserId($userID);
                        } else {
                            $result['data'] = $this->castService->getCharsByUserId($request->userID);
                        }
                    } catch (Exception $e) {
                        $result['error'] = true;
                        $result['message'] = $e->getMessage();
                    }
                    break;
                default:
                    $result['error'] = true;
                    $result['message'] = 'Method not found';
                    break;
            };
        } catch (Exception $e) {
            $result['error'] = true;
            $result['message'] = $e->getMessage();
        }

        //output
        return new JsonModel($result);
    }

    //show profile (public) route: /profile/:username
    public function publicProfileAction()
    {
        $url = new URLModifier();
        $viewModel = new ViewModel();
        $username = $this->params()->fromRoute('username');
        $username = $url->fromURL($username);
        if ($family = $this->castService->getFamilyByName($username)) {
            $viewModel->setVariables( $this->familyprofileAction($family));
            $viewModel->setTemplate('auth/profile/family.phtml');

            return $viewModel;
        }

        $user = $this->userService->getUserDataByName($username);

        $characters = $this->castService->getCharsByUserId($user->id);
        $isActive = $this->statsService->isActive($user->name);
        $askingUser = $this->accessService->getUserName();
        $user->setActiveUser($askingUser);

        $viewModel->setVariable('askingUser', $askingUser);
        $viewModel->setVariable('isActive', $isActive);
        $viewModel->setVariable('user', $user);
        $viewModel->setVariable('characters', $characters);
        $viewModel->setVariable('askingRole', $this->accessService->getRole());

        $viewModel->setTemplate('auth/profile/public.phtml');

        return $viewModel;

    }

    //edit own profile route: /profile
    public function privateProfileAction() {

        $username = $this->accessService->getUserName();
        if(!$username) return $this->redirect()->toRoute('home');
        /** @var User $user */
        $user = $this->userService->getUserDataByName($username);

        $characters = $this->castService->getCharsByUserId($user->id);
        $isActive = $this->statsService->isActive($user->name);
        
        $viewModel = new ViewModel();
        $viewModel->setVariable('askingUser', $username);
        $viewModel->setVariable('isActive', $isActive);
        $viewModel->setVariable('user', $user);
        $viewModel->setVariable('characters', $characters);

        $request = $this->getRequest();

        //create userForm
        $form = new UserForm();
        $viewModel->setVariable('userForm', $form);

        $form->setData($user->getArrayCopyPlainDate());
        $form->get('submit')->setAttribute('value', 'Edit');

        if ($request->isPost()) {
            //check witch form was sent
            if ($request->getPost('email') !== null) {
                //merge post data and files
                $post = array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
                );
                //user form
                $form->setData($post);
                
                if ($form->isValid()) {
                    $data = $form->getData();
                    $data['birthday'] = strtotime($data['birthday']);
                    $id =  $this->accessService->getUserID();
                    if ($id === 0) return $this->redirect()->toRoute('profile');
                    if ($id !== $data['id']) return $this->redirect()->toRoute('profile');

					//handle user image
					$data = $this->uploadImage($data);

                    $user->exchangeArray($data);
                    if (strlen($data['password']) > MIN_PW_LENGTH) {
                        $userPassword = new UserPassword();
                        $user->password = $userPassword->create($user->password);
                    }
                    $this->userService->saveUser($user);
                    //redirect

                }
            }
        }
        //create charForm
        $charForm = new CharacterForm($this->castService);
//        $charForm->setAttribute('action', '#');
        $viewModel->setVariable('charForm', $charForm);
        $viewModel->setTemplate('auth/profile/private.phtml');
        return $viewModel;
    }

    //show char profile route: /profile/:username/:charname
    public function charprofileAction(){
        $url = new URLModifier();
        $hasFamily = false;
        $username = $this->params()->fromRoute('username');
        $username = $url->fromURL($username);
        $charnameURL = $this->params()->fromRoute('charname');

        $char = $this->castService->getCharacterDataByName($charnameURL, $username);
        $char['userData'] = $this->userService->getUserDataByName($username);
        $charFamily = $this->castService->getAllCharsFromFamily($char['family_id']);

        foreach ($charFamily as $key => $member){
            $wholeName = $url->toURL($member['name']) . "-" . $url->toURL($member['surename']);
            if ($wholeName == $charnameURL){
                unset($charFamily[$key]);
                break;
            }
        }
        
        if ($charFamily) $hasFamily = true;

        return  new ViewModel(array(
            'isOwner'    => ($username == $this->accessService->getUserName()) ? true : false,
            'char'       => $char,
            'hasFamily'  => $hasFamily,
            'charFamily' => $charFamily,
            'username'   => $username,
            'charnameURL'   => $charnameURL,
        ));
    }

    //@todo familyprofileAction
    public function familyprofileAction($family)
    {
        $members = $this->castService->getAllCharsFromFamily((int)$family['id']);

        return array(
            'members' => $members,
            'family' => $family,
        );

    }

    public function listAction()
    {
        $userTable = new DataTable( array(
            'data' => $this->userService->getAllUsers(),
            'columns' => array(
                array (
                    'name'  => 'name',
                    'label' => 'Name'
                ),
                array(
                    'name'  => 'aktive',
                    'label' => ' ',
                    'type'  => 'custom',
                    'render' => function($row){
                        $isActive = $this->statsService->isActive($row['name']);
                        $stateUrl = ($isActive) ? '/img/uikit/led-green.png' : '/img/uikit/led-red.png';
                        $state    = ($isActive) ? 'online' : 'offline';
                        $activityState = " <div class='onlineStatus' data-balloon=$state data-balloon-pos='down' ><img src=$stateUrl class='onlineStatus' alt=$state style='height: 25px;'></div>";
                        return $activityState;
                    }
                ),
                array(
                    'name'  => 'chars',
                    'label' => 'Chars',
                    'type'  => 'custom',
                    'render' => function($row){
                        return  count($this->castService->getCharsByUserId($row['id']));
                    }
                ),
                array(
                    'name'  => 'href',
                    'label' => 'Profil',
                    'type'  => 'custom',
                    'render' => function($row){
                        return  "<a href='/profile/" . $row['name'] . "'>zum Profil</a>";
                    }
                ),
            ),
        ));
        return array(
            'userTable' => $userTable,
        );
    }

	private function uploadImage ($data, $newId = null)
	{
		/** @var ImagePlugin $imageUpload */
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
				}
			};

			// === write paths to item
			$data = $dataTarget + $data;
		}
		return $data;
	}
}
