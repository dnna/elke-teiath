<?php
/**
 * Παίρνει ένα doc αρχείο και αντικαθιστά κάποια strings μέσα σε αυτό. Στη
 * συγκεκριμένη εφαρμογή χρησιμοποιείται για την παραγωγή των αιτήσεων μέσα από
 * τις φόρμες.
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Action_Helper_CreateDoc extends Zend_Controller_Action_Helper_Abstract {

    protected $_templatesPath;
    protected $_livedocxuser;
    protected $_livedocxpass;
    protected $_livedocxPreferedInput;
    protected $_livedocxPreferedOutput;
    protected $_livedocxMimeType;
    /**
     * @var string Αυτό είναι null πριν από τη δημιουργία του doc
     */
    protected $_outputDocName;
    public $view;

    public function __construct() {
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $config = $bootstrap->getOptions();
        $this->_templatesPath = $config['docs']['templatePath'];
        $this->_livedocxuser = $config['livedocx']['user'];
        $this->_livedocxpass = $config['livedocx']['pass'];
        $this->_livedocxPreferedInput = $config['livedocx']['preferedInput'];
        $this->_livedocxPreferedOutput = $config['livedocx']['preferedOutput'];
        $this->_livedocxMimeType = $config['livedocx']['mimeType'];
    }

    protected function getCachedDocumentPath($input, $templatePath, $prefix = 'elkedoc') {
        $inputhash = '';
        if($input instanceof Dnna_Model_Object || $input instanceof Traversable || is_array($input)) {
            $inputhash = md5(serialize($input).$templatePath);
        } else {
            throw new Exception('Η δημιουργία του εγγράφου είναι αδύνατη γιατί η είσοδος δεν είναι πίνακας.');
        }
        return $this->_cachePath.$prefix.'_'.$inputhash.'.'.$this->_livedocxPreferedInput;
    }
    
    protected function checkForExtension($templatePath) {
        $ext = substr(strrchr($templatePath, '.'), 1);
        if($ext != false) {
            return $templatePath;
        } else {
            return $templatePath.'.'.$this->_livedocxPreferedInput;
        }
    }

    public function direct(Zend_Controller_Action $controller = null, $input = null, $templatePath = null) {
        $templatePath = $this->checkForExtension($templatePath);
        // Τα παρακάτω γίνονται μόνο αν το έγγραφο δεν υπάρχει στο cache
        /* @var $cache Zend_Cache_Core */
        $cache = Zend_Registry::get('cache');
        $inputhash = md5(serialize($input).$templatePath);
        if($cache->load($inputhash) == false) {
            $mailMerge = new Zend_Service_LiveDocx_MailMerge();
            $mailMerge->setUsername($this->_livedocxuser)
                      ->setPassword($this->_livedocxpass);
            // Ελέγχουμε αν το template υπάρχει στο server και αν έχει ίδιο μέγεθος με το τοπικό
            $uploadedTemplates = $mailMerge->listTemplates();
            $existsAndIsSame = false;
            foreach($uploadedTemplates as $curTemplate) {
                if($curTemplate['filename'] === $templatePath && $curTemplate['fileSize'] == filesize($this->_templatesPath . $templatePath)) {
                    $existsAndIsSame = true;
                    break;
                }
            }
            // Αν δεν υπάρχει ή αν δεν είναι ίδιο το ανεβάζουμε
            //if(!$existsAndIsSame) {
                $mailMerge->deleteTemplate($templatePath);
                $mailMerge->uploadTemplate($this->_templatesPath . $templatePath);
            //}

            // Επιλογή του template και δημιουργία του εγγράφου
            $mailMerge->setRemoteTemplate($templatePath);
            if($input instanceof Dnna_Model_Object) {
                $variables = $input->getOptionsAsStrings('doc');
            }
            foreach ($variables as $curVariable => $curValue) {
                $mailMerge->assign($curVariable, $curValue);
            }
            $mailMerge->createDocument();
            $document = $mailMerge->retrieveDocument($this->_livedocxPreferedOutput);

            // Αποθήκευση στο cache
            $cache->save($document, $inputhash);
        } else {
            $document = $cache->load($inputhash);
        }

        return $document;
    }

    public function set_livedocxPreferedOutput($_livedocxPreferedOutput) {
        $this->_livedocxPreferedOutput = $_livedocxPreferedOutput;
    }
}
?>