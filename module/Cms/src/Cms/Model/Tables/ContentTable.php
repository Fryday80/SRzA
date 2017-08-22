<?php
	/**
	 * Created by IntelliJ IDEA.
	 * User: Fry
	 * Date: 21.08.2017
	 * Time: 15:00
	 */

	namespace Cms\Model;

	use Application\Model\AbstractModels\AbstractModel;
	use Application\Model\AbstractModels\DatabaseTable;
	use Cms\Model\DataModels\Content;
	use Zend\Db\Adapter\Adapter;

	class ContentTable extends DatabaseTable
	{
		public $table = 'pages';


		public function __construct(Adapter $adapter)
		{
			parent::__construct($adapter, Content::class);
		}

		/**
		 * @return AbstractModel[]|null
		 */
		public function findAll()
		{
			return $this->getAll();
		}

		public function getById($id)
		{
			return $this->getById($id);
		}

		public function getByUrl($url)
		{
			return $this->getByKey('url', $url);
		}

		public function save($post)
		{
			return parent::save($post);
		}

		public function deleteContent (Content $post)
		{
			return $this->remove($post->getId());
		}
	}