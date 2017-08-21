<?php
	/**
	 * Created by IntelliJ IDEA.
	 * User: Fry
	 * Date: 21.08.2017
	 * Time: 15:00
	 */

	namespace Cms\Model\Tables;

	use Application\Model\AbstractModels\DatabaseTable;
	use Cms\Model\DataModels\Content;

	class ContentTable extends DatabaseTable
	{
		public $table = 'pages';


		public function __construct(Adapter $adapter)
		{
			parent::__construct($adapter, Content::class);
		}

		public function findAll()
		{
			return $this->getAll();
		}

		public function findById($id)
		{
			return $this->getById($id);
		}

		public function findByUrl($url)
		{
			return $this->getByKey('url', $url);
		}

		public function save($post)
		{
			return $this->save($post);
		}

		public function deleteContent (Content $post)
		{
			return $this->remove($post->getId());
		}
	}