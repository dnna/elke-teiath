<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Form_Validate_Project extends Zend_Validate_Abstract {
    const NOT_FOUND = 'projectNotFound';
    const ACCESS_DENIED = 'accessDenied';

    protected $_messageTemplates = array(
        self::NOT_FOUND => 'Το έργο που επιλέξατε δεν βρέθηκε',
        self::ACCESS_DENIED => 'Δεν έχετε δικαιώμα να επιλέξετε το συγκεκριμένο έργο',
    );

    protected $_required;

    /**
     * Constructor of this validator
     *
     * The argument to this constructor is the third argument to the elements' addValidator
     * method.
     *
     * @param array|string $fieldsToCheck
     */
    public function __construct($required = true) {
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
        if($this->_required == false && (!isset($context['projectid']) || $context['projectid'] === 'null')) {
            return true; // Επιτρέπουμε το έργο να είναι 'null'
        }
        if(isset($context['projectid']) && $context['projectid'] != "") {
            $project = Zend_Registry::get('entityManager')->getRepository('Erga_Model_Project')->findOneBy(array('_projectid' => $context['projectid']));
        }
        if(!isset($project)) {
            $this->_error(self::NOT_FOUND);
            return false;
        }
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || (!$auth->getStorage()->read()->hasRole('elke') && $auth->getStorage()->read()->get_userid() != $project->get_basicdetails()->get_supervisor()->get_userid())) {
            $this->_error(self::ACCESS_DENIED);
            return false;
        }

        return true;
    }
}
?>