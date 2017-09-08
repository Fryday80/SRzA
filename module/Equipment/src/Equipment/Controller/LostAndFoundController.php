<?php
namespace Equipment\Controller;

use Application\Controller\Plugin\ImagePlugin;
use Auth\Service\AccessService;
use Auth\Service\UserService;
use Equipment\Model\DataModels\LostAndFoundItem;
use Equipment\Model\Enums\EEquipTypes;
use Equipment\Service\LostAndFoundService;
use Equipment\Utility\LostAndFoundDataTable;
use Zend\Form\Form;
use Zend\View\Model\ViewModel;

class LostAndFoundController extends EquipmentController
{
    /** @var LostAndFoundService  */
	protected $service;

	public function __construct(
		LostAndFoundService $lostAndFoundService,
		UserService $userService,
		AccessService $accessService
	) {
        $this->userService   = $userService;
        $this->accessService = $accessService;
        $this->service  	 = $lostAndFoundService;

		$this->activeUserId   = $this->accessService->getUserID();
		$this->activeUserRole = $this->accessService->getRole();

		$this->dataTable     = new LostAndFoundDataTable();
        $this->dataTable->setServices($this->service, $this->accessService);
	}

    public function indexAction() {
		$this->dataTable->configure($this->service->getAll());

		return array(
			'dataTable' => $this->dataTable,
		);
    }

	public function claimAction()
	{
		$itemId = $this->params()->fromRoute('id');
		/** @var LostAndFoundItem $item */
		$item = $this->service->getById($itemId);
		array_push($item->claimed, $this->activeUserId);
		$this->service->save($item);
		return $this->redirect()->toRoute('lostAndFound');
    }

    public function deleteAction(){
		$itemId = $this->params()->fromRoute('id');

        $item = $this->service->getById($itemId);
        
        if ($item->createdBy !== $this->activeUserId && $this->activeUserRole !== 'Administrator')
        	return $this->redirect()->toRoute('home');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            if ($post['del'] == 'Yes' && $itemId == $post['id']){
                if ($item->createdBy !== $this->activeUserId)
                    if ($this->activeUserRole !== 'Administrator')
                        return $this->redirect()->toRoute('home');

                $this->handleDeleteAllImages((int) $item->id);
                $this->service->deleteById($itemId);

                return $this->redirect()->toUrl($this->flashMessenger()->getMessages('ref')[0]);
            }
        }

        $ref = ($request->getHeader('Referer')) ? $request->getHeader('Referer')->uri()->getPath() : '/laf';
        $this->flashMessenger()->addMessage($ref, 'ref');
        $vars =  array(
        	'titel' => 'Lost or Found löschen',
            'subjectName' => $item->name . ', ' . $item->event,
			'subjectId' => $itemId,
//			'links' => ,
        );
        $viewModel = new ViewModel($vars);
        $viewModel->setTemplate('application/defaults/delete.phtml');
        return $viewModel;
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
				$newId = (int) $this->service->getNextId();
				// upload and save images
				$newPaths = $this->handleUploads($newId, array(
					'image' => $data['image']));
				$data = $newPaths + $data;

				// push into model for selection in service
				$item = new $vars['model'][$vars['type']]($data);
				$this->service->save($data);
                return $this->redirect()->toUrl($this->flashMessenger()->getMessages('ref')[0]);
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
					'image' => $data['image']));
				$data = $newPaths + $data;

				// push into model for selection in service
				$item = new $vars['model'][$vars['type']]($data);

                $this->service->save($item);
                return $this->redirect()->toUrl($this->flashMessenger()->getMessages('ref')[0]);
            }
        }

        $ref = ($request->getHeader('Referer')) ? $request->getHeader('Referer')->uri()->getPath() : '/equip';
        $this->flashMessenger()->addMessage($ref, 'ref');

        $equip = $this->service->getById($equipId);
        $form->setData($equip->toArray());
        return array_merge($vars, array(
            'form' => $form,
        ));
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

		// upload and save images
		// =======================
		// === create path
		foreach ($data as $key => &$uploadedImage) {
			if ($imageUpload->isValidUploadArray($uploadedImage)) {
				list ($fileName, $extension) = $imageUpload->getFileDataFromUpload($data[ $key ]);
				$uploadFileNames[ $key ] = $key . '.' . $extension;
				$dataTargetPaths[ $key ] = 'LostAndFound/' . $id . '/';
				$dataTarget[ $key ] = $dataTargetPaths[ $key ] . $uploadFileNames[ $key ];
			}
		}
		if ($dataTargetPaths !== null){
			$mediaItems = $imageUpload->upload($data, $dataTargetPaths, $uploadFileNames);
			if (!is_array($mediaItems)){
				$mediaItems[0] = $mediaItems;
			}
		}

		foreach ($mediaItems as $mediaItem) {
			// === process image
			$imageUpload->imageProcessor->load($mediaItem);
			$side = 500; // @todo implement config
			$imageUpload->imageProcessor->resize_square($side);
			$imageUpload->imageProcessor->saveImage();
		}

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
