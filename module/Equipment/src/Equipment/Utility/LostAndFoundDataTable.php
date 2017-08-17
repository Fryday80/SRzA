<?php
namespace Equipment\Utility;

use Application\Model\AbstractModels\DataTableAbstract;
use Auth\Service\AccessService;
use Equipment\Service\LostAndFoundService;

class LostAndFoundDataTable extends DataTableAbstract
{
    // check vars
    private $aService = false;
    private $lafService = false;
    private $tablePrepared = false;

    // Services
    /** @var  AccessService */
    private $accessService;
    /** @var  LostAndFoundService */
    private $lostAndFoundService;

    public function setServices (LostAndFoundService $lostAndFoundService, AccessService $accessService)
    {
		$this->setLostAndFoundService($lostAndFoundService);
		$this->setAccessService($accessService);
    }

    public function setLostAndFoundService(LostAndFoundService $lostAndFoundService)
    {
        $this->lostAndFoundService = $lostAndFoundService;
        $this->lafService = true;
    }

	public function setAccessService($accessService)
	{
		$this->accessService = $accessService;
		$this->aService = true;
	}

    public function configure($items)
    {
        parent::setData($items);
        $this->insertLinkButton("/laf/add", 'Neuer Eintrag');
        $this->setColumns($this->columnConfiguration());
    }

    public function isPrepared()
    {
        if($this->tablePrepared == true)
            return true;
        if ($this->aService == true && $this->lafService == true)
            return $this->tablePrepared = true;
        else
            bdump ('You need to inject UserService and LostAndFoundService!! Use: setServices || setUserService && setLostAndFoundService');
        return false;
    }

    private function columnConfiguration(){
        return array(
        	array (
        		'name'  => 'L&F',
				'label' => 'L&F',
				'type'  => 'custom',
				'render' => function($row) {
					$style = 'background: green;';
					$text = 'habe';
        			if ($row['lost'] == 1){
        				$style = 'background: red;';
        				$text = 'suche';
					}
					if ($row['claimed'] !== null)
						$style = 'background: yellow;';
					return "<span style='$style'>$text</span>";
				}
			),
            array (
                'name'  => 'name',
                'label' => 'Name'
            ),
            array (
                'name'  => 'userName',
                'label' => 'Bei',
            ),
            array (
                'name'  => 'event',
                'label' => 'gefunden bei'
            ),
            array (
                'name'  => 'image',
                'label' => 'Bild',
                'type'  => 'custom',
                'render' => function($row) {
					return '<img src="' . $row['image'] . '" alt="Item" style="height:35px;">';
                }
            ),
            array (
                'name'  => 'href',
                'label' => 'Details',
                'type'  => 'custom',
                'render' => function($row) {
                    $edit = '';
                    $delete = '';
                    $askingId = $this->accessService->getUserID();
                    $askingRole = $this->accessService->getRole();
                    $link1 = '<a href="/laf/claim/' . $row['id'] . '">Meins</a>';
                    if ($row['possessed'] == $askingId || $askingRole == 'Administrator') {
                        $edit = '<a href="/laf/edit/' . $row['id'] . '">Edit</a>';
                        $delete = '<a href="/laf/delete/' . $row['id'] . '">Delete</a>';
                    }
                    return $link1 . '<br/>' . $edit . '<br/>' . $delete;
                }
            ),
        );
    }
}