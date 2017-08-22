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
		 * @return Content[]|null
		 */
		public function getAll()
		{
			return parent::getAll();
		}

		public function findByUrl($url)
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