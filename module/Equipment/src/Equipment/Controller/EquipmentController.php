<?php
namespace Equipment\Controller;

use Application\Controller\Plugin\ImagePlugin;
use Auth\Service\AccessService;
use Auth\Service\UserService;
use Equipment\Model\AbstractModels\AbstractEquipmentDataItemModel;
use Equipment\Model\Enums\EEquipTypes;
use Equipment\Service\EquipmentService;
use Equipment\Utility\EquipmentDataTable;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;

class EquipmentController extends AbstractActionController
{
    /** @var EquipmentService  */
    protected $service;
    /** @var UserService  */
    protected $userService;
    /** @var AccessService  */
    protected $accessService;
    protected $config;

	protected $activeUserId;
	protected $activeUserRole;

    protected $dataTable;


	public function __construct(
		$config,
		EquipmentService $equipmentService,
		UserService $userService,
		AccessService $accessService
	) {
        $controllerName = str_replace("Controller", "", explode("\\", get_class($this))[2]);
        $this->config = $config[$controllerName];

        $this->userService   = $userService;
        $this->accessService = $accessService;
        $this->service  = $equipmentService;

		$this->activeUserId   = $this->accessService->getUserID();
		$this->activeUserRole = $this->accessService->getRole();

        $this->dataTable     = new EquipmentDataTable();
        $this->dataTable->setServices($this->accessService, $this->service);
    }

    public function indexAction() {
        $vars = $this->getVars('index');
        $this->dataTable->configure('index', null, $this->service->getAll());
        return $vars + array ('dataTable' => $this->dataTable);
    }

    public function typeAction()
    {
        $action = 'type';
        $type = $vars['typeString'] = $this->params()->fromRoute('type');
        $type = $vars['type'] = EEquipTypes::TRANSLATE_TO_ID[strtolower($type)];
        $vars = array_merge_recursive($vars, $this->getVars($action, $vars['typeString']));

        // create data table
        $items = $this->service->getAllByType($type)->toArray();
        $this->dataTable->configure($action, $type, $items);
        
        foreach ($items as $item)
            $vars['userList'][$item['userId']] = $item['userName'];

        return array_merge($vars, array(
            'dataTable' => $this->dataTable,
        ));
    }

    public function userallAction(){
        $action = 'userall';
        $type = $vars['typeString'] = $this->params()->fromRoute('type');
        $type = $vars['type'] = EEquipTypes::TRANSLATE_TO_ID[strtolower($type)];
        $userId = (int) $this->params()->fromRoute('userId');
        $vars = array_merge_recursive($vars, $this->getVars($action, $vars['typeString'], $userId));

        // create data table
        $items = $this->service->getByUserIdAndType($userId, $type)->toArray();
        $this->dataTable->configure($action, $type, $items);
        foreach ($items as $item)
            $vars['userList'][$item['userId']] = $item['userName'];

        return array_merge($vars, array(
            'dataTable' => $this->dataTable,
        ));
    }

    public function showAction(){
        $action = 'show';
        $vars['typeString'] = $this->params()->fromRoute('type');
        $vars['type'] = EEquipTypes::TRANSLATE_TO_ID[strtolower($vars['typeString'])];
        $equipId = (int) $this->params()->fromRoute('equipId');
        $userId = (int) $this->params()->fromRoute('userId');
        $vars = array_merge_recursive($vars, $this->getVars($action, $vars['typeString'], $userId));

        return array_merge($vars, array(
            'equip' => $this->service->getById($equipId),
        ));
    }

    public function deleteAction(){
        $action = 'delete';
        $vars['typeString'] = $this->params()->fromRoute('type');
        $vars['type'] = EEquipTypes::TRANSLATE_TO_ID[strtolower($vars['typeString'])];
        $equipId = (int) $this->params()->fromRoute('equipId');
        $userId = (int) $this->params()->fromRoute('userId');
        $vars = array_merge_recursive($vars, $this->getVars($action, $vars['typeString'], $userId));

        
        if ($userId !== $this->activeUserId && $this->activeUserRole !== 'Administrator') return $this->redirect()->toRoute('home');
        
        $url = "/equip/" .$vars['typeString']. "/$userId/delete/$equipId";
        $equip = $this->service->getById($equipId);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            if ($post['del'] == 'Yes' && $equipId == $post['id']){
                $checkItem = $this->service->getById($equipId);
                if ($this->activeUserId !== $checkItem->userId)
                    if ($this->activeUserRole !== 'Administrator')
                        return $this->redirect()->toRoute('home');
                $this->handleDeleteAllImages($checkItem);
                $this->service->deleteById($equipId);
                return $this->redirect()->toUrl($this->flashMessenger()->getMessages('ref')[0]);
            }
        }

        $ref = ($request->getHeader('Referer')) ? $request->getHeader('Referer')->uri()->getPath() : '/equip';
        $this->flashMessenger()->addMessage($ref, 'ref');

        // === create delete view from default
        return $this->defaultView()->delete(
			array(
				'title' => 'Equipment Löschen?',
				'url' => $url, // target url for delete post request
				'subjectName' => $equip->name,
				'subjectId' => $equip->id,
				'subjectImage' => isset($equip->image1) ? $equip->image1 : $equip->image2,
			)
		);
    }

    public function addAction()
    {
        $action = 'add';
        $type = $vars['typeString'] = $this->params()->fromRoute('type');
        $type = $vars['type'] = EEquipTypes::TRANSLATE_TO_ID[strtolower($type)];
        $userId = (int) $this->params()->fromRoute('userId');
        $vars = array_merge_recursive($vars, $this->getVars($action, $vars['typeString'], $userId));

        /** @var Form $form */
        $form = new $vars['formType'][$type]($this->service, $this->userService);
        $form->get('userId')->setValue($userId);
        $request = $this->getRequest();
        if ($request->isPost()) {
            //merge post data and files
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $form->setData($post);
            if ($form->isValid()){
                $data = $form->getData();
                $newId = $this->service->getNextId();

				$newPaths = $this->handleUploads($newId, array(
					'image1' => $data['image1'],
					'image2' => $data['image2'],
					'bill'   => $data['bill']));
				$data = $newPaths + $data;

                $this->getFiles();
                $this->getFile("fileName");

                //		push into model for selection in service
				$item = new $vars['model'][$type]($data);

				$this->service->save($item);
				$url = $this->flashMessenger()->getMessages('ref')[0];
				if ($url = "/equip/equipment/add") $url = "/equip/equipment";
                return $this->redirect()->toUrl($url);
            }
        }

        $ref = ($request->getHeader('Referer')) ? $request->getHeader('Referer')->uri()->getPath() : '/equip';
        $this->flashMessenger()->addMessage($ref, 'ref');
        return array_merge($vars, array(
            'form' => $form,
        ));

    }

    public function editAction(){
        $action = 'edit';
        $vars['typeString'] = $this->params()->fromRoute('type');
		$vars['type'] = EEquipTypes::TRANSLATE_TO_ID[strtolower($vars['typeString'])];
        $userId = (int) $this->params()->fromRoute('userId');
        $equipId = (int) $this->params()->fromRoute('equipId');
        $vars = array_merge_recursive($vars, $this->getVars($action, $vars['typeString'], $userId));

        $form = new $vars['formType'][$vars['type']]($this->service, $this->userService);
        $request = $this->getRequest();

        if ($request->isPost()) {
            //merge post data and files
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $form->setData($post);
            if ($form->isValid()){
				$data = $form->getData();

            	// upload and save images
				$newPaths = $this->handleUploads((int) $data['id'], array(
					'image1' => $data['image1'],
					'image2' => $data['image2'],
					'bill'   => $data['bill']));
				$data = $newPaths + $data;

				// push into model for selection in service
				$item = new $vars['model'][$vars['type']]($data);

                $this->service->save($item);
                return $this->redirect()->toUrl("/equip/equipment");
            }
        }

        $ref = ($request->getHeader('Referer')) ? $request->getHeader('Referer')->uri()->getPath() : '/equip';
        $this->flashMessenger()->addMessage($ref, 'ref');

        $equip = $this->service->getById($equipId);
        $form->setData($equip->toArray());
		$image = ($equip->image1 !== null) ? $equip->image1 : $equip->image2;
//        return $this->defaultView()->edit(
//        	array(
//        		'image' => $image,
//        		'links' => $vars['links'],
//        		'title' => 'edit user',
//				'form' => $form,
//			)
//		);
        return array_merge($vars, array(
            'form' => $form,
        ));
    }

    private function getVars($action, $typeString = null, $userId = false)
    {
        if (isset($this->config['config'][$action]))
            $page = array_merge_recursive($this->config['config']['default_actionName'], $this->config['config'][$action]);
        if (isset($this->config['config'][$action]['name']))  $page['name']  = $this->config['config'][$action]['name'];
        if (isset($this->config['config'][$action]['label'])) $page['label'] = $this->config['config'][$action]['label'];
        else
            $page = $this->config['config']['default_actionName'];

        if(is_array($page['label']))
            $page['label'] = $page['label'][EEquipTypes::TRANSLATE_TO_ID[$typeString]];

        $vars = $page['vars'];
        $vars['page'] = $page;

        if ($action !=='index') {
            if ($action !== 'type')
                $vars['links']['zurück zur Übersicht'] = "/equip/$typeString";
            if ($action !== 'type' && $action !== 'userall')
                $vars['links']['zurück zur User-Übersicht'] = "/equip/$typeString/$userId";
        }
        return $vars;
    }

	/* ===============================
	 *
	 *  Image Handling
	 *
	 * =============================== */
	/**
	 * @param  int   $id	special dynamic selector
	 * @param  array $data  array of upload arrays like [$key1 => $uploadArray1]
	 *
	 * @return array 		array prepared for save
	 */
    protected function handleUploads (int $id, array $data)
	{
		/** @var ImagePlugin $imageUpload */
		$imageUpload = $this->image();
		$dataTargetPaths = $uploadFileNames = null;
		$dataTarget = array();
		$mediaItems = null;
		$uploadCount = 0;

		// upload and save images
		// =======================
		// === create path
		foreach ($data as $key => &$uploadedImage) {
			if ($imageUpload->isValidUploadArray($uploadedImage)) {
				$uploadCount++;
				list ($fileName, $extension) = $imageUpload->getFileDataFromUpload($data[ $key ]);
				$uploadFileNames[ $key ] = $key . '.' . $extension;
				$dataTargetPaths[ $key ] = 'equipment/' . $id . '/';
				$dataTarget[ $key ] = $dataTargetPaths[ $key ] . $uploadFileNames[ $key ];
			}
		}
		if ($dataTargetPaths !== null){
			if ($uploadCount == 1)
			$mediaItems = $imageUpload->upload($data, $dataTargetPaths, $uploadFileNames);
		}

		if ($mediaItems){
			foreach ($mediaItems as $mediaItem) {
				// === process image
				$imageUpload->imageProcessor->load($mediaItem);
				$side = 500; // @todo implement config
				$imageUpload->imageProcessor->resize_square($side);
				$imageUpload->imageProcessor->saveImage();
			}
		}

		if (empty($dataTarget)) return null;
		return $dataTarget;
	}

	/**
	 * @param int $itemId
	 */
	protected function handleDeleteAllImages(int $itemId)
	{
		$this->image()->deleteAllImagesByPath('equipment/' . $itemId .'/');
	}
}
