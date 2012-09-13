<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Form_Validate_Supervisor extends Zend_Validate_Abstract {
    const NOT_FOUND = 'aitisiNotFound';
    const NO_ACCESS ='noAccess';

    protected $_messageTemplates = array(
        self::NOT_FOUND => 'Ο επιστημονικά υπεύθυνος που ορίσατε δεν βρέθηκε',
        self::NO_ACCESS => 'Δεν έχετε πρόσβαση να επιλέξετε τον συγκεκριμένο επιστημονικά υπεύθυνο',
    );

    protected $_allowAny = false;

    /**
     * Constructor of this validator
     *
     * The argument to this constructor is the third argument to the elements' addValidator
     * method.
     *
     * @param array|string $fieldsToCheck
     */
    public function __construct($allowAny = false) {
        $this->_allowAny = $allowAny;
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
        if(isset($context['userid']) && $context['userid'] != "") {
            $user = Zend_Registry::get('entityManager')->getRepository('Application_Model_User')->findOneBy(array('_userid' => $context['userid']));
        }
        if(!isset($user)) {
            $this->_error(self::NOT_FOUND);
            return false;
        }
        $auth = Zend_Auth::getInstance();
        if($this->_allowAny != true && $user->get_userid() != $auth->getStorage()->read()->get_userid()) {
            $this->_error(self::NO_ACCESS);
            return false;
        }

        return true;
    }
}
?>