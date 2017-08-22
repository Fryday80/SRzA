<?php
	/**
	 * Created by IntelliJ IDEA.
	 * User: Fry
	 * Date: 22.08.2017
	 * Time: 11:40
	 */

	namespace Equipment\Model\Enums;


	class EEquipLending
	{
		const LEND_LIST = array (
			0 => 'no',
			1 => 'only SRzA',
			2 => 'SRzA & S&M',
			3 => 'Extern',
		);
		const NO  = 0;
		const SRA = 1;
		const SM  = 2;
		const EXTERN = 3;
	}