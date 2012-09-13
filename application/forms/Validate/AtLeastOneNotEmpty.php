<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Form_Validate_AtLeastOneNotEmpty extends Zend_Validate_Abstract {
    const ALL_EMPTY = 'allFieldsAreEmpty';

    protected $_messageTemplates = array(
        self::ALL_EMPTY => 'At least one of these fields must not be empty'
    );

    /**
     * The fields that the current element needs to match
     *
     * @var array
     */
    protected $_fieldsToCheck = array();

    /**
     * Constructor of this validator
     *
     * The argument to this constructor is the third argument to the elements' addValidator
     * method.
     *
     * @param array|string $fieldsToCheck
     */
    public function __construct($fieldsToCheck = array()) {
        if (is_array($fieldsToCheck)) {
            foreach ($fieldsToCheck as $field) {
                $this->_fieldsToCheck[] = (string) $field;
            }
        } else {
            $this->_fieldsToCheck[] = (string) $fieldsToCheck;
        }
    }

    /**
     * Check if the element using this validator is valid
     *
     * This method will compare the $value of the element to the other elements
     * it needs to match. If they all match, the method returns true.
     *
     * @param $value string
     * @param $context array All other elements from the form
     * @return boolean Returns true if the element is valid
     */
    public function isValid($value, $context = null) {
        $error = true;

        if(isset($value) && $value != "") {
            $error = false;
        } else {
            foreach ($this->_fieldsToCheck as $fieldName) {
                if (isset($context[$fieldName]) && $context[$fieldName] != "") {
                    $error = false;
                    break;
                }
            }
        }

        if($error == true) {
            $this->_error(self::ALL_EMPTY);
        }

        return !$error;
    }
}
?>