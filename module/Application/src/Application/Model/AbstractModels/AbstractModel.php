<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 18.07.2017
 * Time: 14:04
 */

namespace Application\Model\AbstractModels;

use Application\Model\Interfaces\IHydratorModelAccess;
use Application\Model\Interfaces\IObjectToArray;
use ArrayAccess;

class AbstractModel implements ArrayAccess, IObjectToArray, IHydratorModelAccess
{
    private $propertiesCache;

    /**
     * called after hydrating data into this model
     * @param $data
     */
    public function preHydrate($data) { }
    public function postHydrate($data) { }
    public function preExtract(&$arrayData) { }
    public function postExtract(&$arrayData) { }

    /**
     * must return anything than false to disable default hydrating
     * @param $data
     * @return bool
     */
    public function hydrate($data) {
        return false;
    }

    /**
     * must return array to disable default extracting
     * @return false|array
     */
    public function extract() {
        return false;
    }
    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset) {
        if ($this->propertyExists($offset))
            return true;
        return false;
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset) {
//        $getterName = 'get' + ucfirst($offset);
//        if (method_exists($this, $getterName)) {
//            return $this->$getterName();
//        }
        if ($this->propertyExists($offset)) {
            return $this->$offset;
        }
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value) {
//        $setterName = 'get' + ucfirst($offset);
//        if (method_exists($this, $setterName)) {
//            return $this->$setterName($value);
//        }
        if ($this->propertyExists($offset)) {
            $this->$offset = $value;
        }
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset) {
        if ($this->propertyExists($offset)) {
            $this->$offset = null;
        }
    }
    private function propertyExists($name) {
        if (!$this->propertiesCache) {
            $this->propertiesCache = Extras::get_vars($this);
        }
        if (isset($this->propertiesCache[$name]) ) {
            return true;
        }
        return false;
    }

	public function toArray()
	{
		return get_object_vars($this);
	}
}