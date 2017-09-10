<?php

	namespace Application\Model\Interfaces;


	interface IHydratorModelAccess
	{
		public function preHydrate($data);
		public function postHydrate($data);
		public function preExtract(&$arrayData);
		public function postExtract(&$arrayData);
		public function hydrate($data);
	}