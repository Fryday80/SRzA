<?php
	/**
	 * Created by IntelliJ IDEA.
	 * User: Fry
	 * Date: 04.09.2017
	 * Time: 12:02
	 */

	namespace Application\Controller\Plugin;


	use Zend\Mvc\Controller\Plugin\AbstractPlugin;
	use Zend\View\Model\ViewModel;

	class DefaultView extends AbstractPlugin
	{
		public $vars = array(
			'title' => null,
			'url' => null, // target url for delete post request
			'links' => null,
			'subjectId' => null,
			'subjectName' => null,
			'image' => null,
			'form' => null
		);


		public function setVars($vars)
		{
			$this->vars = $vars;
		}

		public function addVars($vars)
		{
			$this->vars = $vars + $this->vars;
		}

		public function setVar($key, $value)
		{
			$this->vars[$key] = $value;
		}


		public function delete($vars = null)
		{
			if ($vars !== null)
				$this->vars = $vars + $this->vars;

			if($this->vars['title'] == null) $this->vars['title'] = 'delete';

			// === create delete view from default
			$viewModel = new ViewModel($this->vars);
			$viewModel->setTemplate('application/defaults/delete.phtml');
			return $viewModel;
		}

		public function edit($vars = null)
		{
			if ($vars !== null)
				$this->vars = $vars + $this->vars;

			if($this->vars['title'] == null) $this->vars['title'] = 'edit';

			// === create delete view from default
			$viewModel = new ViewModel($this->vars);
			$viewModel->setTemplate('application/defaults/edit.phtml');
			return $viewModel;
		}
	}