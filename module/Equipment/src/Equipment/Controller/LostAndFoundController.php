<?php
namespace Equipment\Controller;

use Auth\Service\AccessService;
use Auth\Service\UserService;
use Equipment\Model\Enums\EEquipTypes;
use Equipment\Service\LostAndFoundService;
use Equipment\Utility\LostAndFoundDataTable;
use Media\Service\ImageProcessor;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LostAndFoundController extends AbstractActionController
{
    /** @var LostAndFoundService  */
    private $lostAndFoundService;
    /** @var UserService  */
    private $userService;
    /** @var AccessService  */
    private $accessService;
    private $config;
    /** @var ImageProcessor  */
	private $imageProcessor;

	private $activeUserId;

	const READ_OUT = "/media/file/";
	private $dataRootPath;

    private $dataTable;


	public function __construct(
		LostAndFoundService $lostAndFoundService,
		UserService $userService,
		AccessService $accessService,
		ImageProcessor $imageProcessor
	) {
        $this->userService   = $userService;
        $this->accessService = $accessService;
        $this->lostAndFoundService  = $lostAndFoundService;

		$this->imageProcessor = $imageProcessor;
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
		//@todo add user id as claiming user to LAF-Item
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
        $viewModel->setTemplate('application/default/delete.phtml');
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
        $form = new $vars['formType'][$type]($this->equipService, $this->userService);
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
                // push into model for selection in service
                $data = new $vars['model'][$type]($form->getData());
                $newId = $this->equipService->getNextId();

				// upload and save images
				if ($data['image1'] !== null || $data['image2'] !== null){
					$targetPaths = $this->imageProcessor->uploadEquipImages($data, $newId);
					foreach ($targetPaths as $key => $targetPath) {
						$data->$key = $targetPath;
					}
				}
				$this->equipService->save($data);
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

        $form = new $vars['formType'][$vars['type']]($this->equipService, $this->userService);
        $request = $this->getRequest();

        if ($request->isPost()) {
            //merge post data and files
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $form->setData($post);
            if ($form->isValid()){
                $item = new $vars['model'][$vars['type']]($form->getData());
				// upload and save images
				if ($item['image1'] !== null || $item['image2'] !== null){
					$targetPaths = $this->imageProcessor->uploadEquipImages($item);
					// set data in DataModel
					foreach ($targetPaths as $key => $targetPath) {
						$item->$key = $targetPath;
					}
				}

                $this->equipService->save($item);
                return $this->redirect()->toUrl($this->flashMessenger()->getMessages('ref')[0]);
            }
        }

        $ref = ($request->getHeader('Referer')) ? $request->getHeader('Referer')->uri()->getPath() : '/equip';
        $this->flashMessenger()->addMessage($ref, 'ref');

        $equip = $this->equipService->getById($equipId);
        $form->setData($equip->toArray());
        return array_merge($vars, array(
            'form' => $form,
        ));
    }
}
