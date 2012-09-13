<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Form_Validate_Aitisi extends Zend_Validate_Abstract {
    const APPROVED_MISMATCH = 'approvedMismatch';
    const INVALID_TYPE = 'invalidType';
    const NOT_FOUND = 'aitisiNotFound';
    const ACCESS_DENIED = 'accessDenied';

    protected $_messageTemplates = array(
        self::APPROVED_MISMATCH => 'Η κατάσταση έγρκισης της αίτησης δεν είναι ίδια με τα κριτήρια που ορίστηκαν',
        self::INVALID_TYPE => 'Ο συγκεκριμένος τύπος αίτησης δεν υπάρχει',
        self::NOT_FOUND => 'Η αίτηση που επιλέξατε δεν βρέθηκε ή δεν είναι του σωστού τύπου',
        self::ACCESS_DENIED => 'Δεν έχετε δικαιώμα να επιλέξετε τη συγκεκριμένη αίτηση',
    );

    protected $_type = null;
    protected $_approved;
    protected $_required;

    /**
     * Constructor of this validator
     *
     * The argument to this constructor is the third argument to the elements' addValidator
     * method.
     *
     * @param array|string $fieldsToCheck
     */
    public function __construct($type = 'ypovoliergou', $approved = null, $required = true) {
        $this->_type = $type;
        $this->_approved = $approved;
        $this->_required = $required;
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
        if($this->_required == false && (!isset($context['aitisiid']) || $context['aitisiid'] === 'null')) {
            return true; // Επιτρέπουμε η αίτηση να είναι 'null'
        }
        $front = Zend_Controller_Front::getInstance();
        $aitiseismoduledir = $front->getModuleDirectory('aitiseis');
        require_once($aitiseismoduledir.'/controllers/helpers/GetMapping.php');
        $mappinghelper = new Aitiseis_Action_Helper_GetMapping();
        $aitisiclass = $mappinghelper->direct($this->_type);
        if(!isset($aitisiclass)) {
            $this->_error(self::INVALID_TYPE);
            return false;
        }
        if(isset($context['aitisiid']) && $context['aitisiid'] != "") {
            $aitisi = Zend_Registry::get('entityManager')->getRepository($aitisiclass)->findOneBy(array('_aitisiid' => $context['aitisiid']));
        }
        if(!isset($aitisi)) {
            $this->_error(self::NOT_FOUND);
            return false;
        }
        if(isset($this->_approved) && $aitisi->get_approved() != $this->_approved) {
            $this->_error(self::APPROVED_MISMATCH);
            return false;
        }
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || (!$auth->getStorage()->read()->hasRole('elke') && $auth->getStorage()->read()->get_userid() != $aitisi->get_creator()->get_userid())) {
            $this->_error(self::ACCESS_DENIED);
            return false;
        }

        return true;
    }
}
?>