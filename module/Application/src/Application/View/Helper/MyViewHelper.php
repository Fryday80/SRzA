<?php
	/**
	 * Created by IntelliJ IDEA.
	 * User: Fry
	 * Date: 24.08.2017
	 * Time: 11:15
	 */

	namespace Application\View\Helper;


	use Zend\View\Helper\AbstractHelper;

	class MyViewHelper extends AbstractHelper
	{
		protected $jsFiles  = array ();
		protected $cssFiles = array ();

		public function __construct($view)
		{
			$this->view = $view;
			$this->attachCSS();
			$this->attachJS();
		}

		protected function attachCSS()
		{
			if (!empty ($this->jsFiles)) {
				foreach ($this->jsFiles as $file) {
					$this->view->headLink()->appendStylesheet($this->view->basePath($file));
				}
			}
		}

		protected function attachJS()
		{
			if (!empty ($this->cssFiles)) {
				foreach ($this->cssFiles as $file) {
					$this->view->headScript()->appendFile($this->view->basePath($file));
				}
			}
		}
	}