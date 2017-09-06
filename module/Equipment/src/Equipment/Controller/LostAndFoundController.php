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
    private $lostAndFoundService;
    /** @var UserService  */
    private $userService;
    /** @var AccessService  */
    private $accessService;

	private $activeUserId;

	private $dataRootPath;

    private $dataTable;


	public function __construct(
		LostAndFoundService $lostAndFoundService,
		UserService $userService,
		AccessService $accessService
	) {
        $this->userService   = $userService;
        $this->accessService = $accessService;
        $this->lostAndFoundService  = $lostAndFoundService;

		$this->dataRootPath = getcwd() . '/Data';

		$this->dataTable     = new LostAndFoundDataTable();
        $this->dataTable->setServices($this->lostAndFoundService, $this->accessService);

        $this->activeUserId = $this->accessService->getUserID();
    }

    public function indexAction() {
		$this->dataTable->configure($this->lostAndFoundService->getAll());

		return array(
			'dataTable' => $this->dataTable,
		);
    }

	public function claimAction()
	{
		$itemId = $this->params()->fromRoute('id');
		/** @var LostAndFoundItem $item */
		$item = $this->lostAndFoundService->getById($itemId);
		array_push($item->claimed, $this->accessService->getUserID());
		$this->lostAndFoundService->save($item);
		return $this->redirect()->toRoute('lostAndFound');
    }

    public function deleteAction(){
		$itemId = $this->params()->fromRoute('id');
        $askingRole = $this->accessService->getRole();

        $item = $this->lostAndFoundService->getById($itemId);
        
        if ($item->createdBy !== $this->activeUserId && $askingRole !== 'Administrator')
        	return $this->redirect()->toRoute('home');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            if ($post['del'] == 'Yes' && $itemId == $post['id']){
                if ($item->createdBy !== $this->activeUserId)
                    if ($askingRole !== 'Administrator')
                        return $this->redirect()->toRoute('home');

                $this->lostAndFoundService->deleteById($itemId);

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
        $form = new $vars['formType'][$type]($this->lostAndFoundService, $this->userService);
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
				$newId = (int) $this->lostAndFoundService->getNextId();
				// upload and save images
				$data = $this->uploadImage($data, $newId);

				// push into model for selection in service
				$item = new $vars['model'][$vars['type']]($data);
				$this->lostAndFoundService->save($data);
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

        $form = new $vars['formType'][$vars['type']]($this->lostAndFoundService, $this->userService);
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
				$data = $this->uploadImage($data);

				// push into model for selection in service
				$item = new $vars['model'][$vars['type']]($data);

                $this->lostAndFoundService->save($item);
                return $this->redirect()->toUrl($this->flashMessenger()->getMessages('ref')[0]);
            }
        }

        $ref = ($request->getHeader('Referer')) ? $request->getHeader('Referer')->uri()->getPath() : '/equip';
        $this->flashMessenger()->addMessage($ref, 'ref');

        $equip = $this->lostAndFoundService->getById($equipId);
        $form->setData($equip->toArray());
        return array_merge($vars, array(
            'form' => $form,
        ));
    }

	private function uploadImage ($data, $newId = null)
	{
		/** @var ImagePlugin $imageUpload */
		$imageUpload = $this->image();

		$id = ($newId !== null) ? $newId : $data['id'];
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
				$dataTargetPath = '/LostAndFound/' . $id .'/';
				foreach ($uploadedImages as $key => &$uploadedImage)
				{
					list ($fileName, $extension) = $imageUpload->getFileDataFromUpload($data[$key]);
					$uploadFileName = $key .'.' . $extension;
					$dataTarget[$key] = $dataTargetPath . $uploadFileName;

					// === upload image
					$imageUpload
						->setData($uploadedImage)
						->setDestination($dataTargetPath)
						->setFileName($uploadFileName);

					$mediaItem = $imageUpload->upload();

					// === process image
					$imageUpload->imageProcessor->load($mediaItem);
					$side = 500; // @todo implement config
					$imageUpload->imageProcessor->resize_square($side);
					$imageUpload->imageProcessor->saveImage();
				}
			};

			// === write paths to item
			$data = $dataTarget + $data;
		}
		return $data;
	}
}
