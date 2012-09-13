<?php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Aitiseis_Model_Repositories_Aitiseis") @Table(name="elke_aitiseis.ypovoliaitimatos")
 */
class Aitiseis_Model_YpovoliAitimatos extends Aitiseis_Model_AitisiBase {
    const type = "Υποβολή Γενικού Αιτήματος";
    const formclass = "Aitiseis_Form_YpovoliAitimatos";
    const template = "YpovoliAitimatos";
    protected $_availableActions = array(self::ACTION_DOWNLOAD);

    /**
     * @Column (name="urgent", type="integer")
     */
    protected $_urgent;
    /**
     * @Column (name="description", type="string")
     */
    protected $_description;
    /**
     * @Column (name="attachmentname", type="string")
     */
    protected $_attachmentname;
    /**
     * @Column (name="attachment", type="blob")
     */
    protected $_attachment;

    public function get_urgent() {
        return $this->_urgent;
    }

    public function set_urgent($_urgent) {
        $this->_urgent = $_urgent;
    }

    public function get_description() {
        return $this->_description;
    }

    public function set_description($_description) {
        $this->_description = $_description;
    }

    public function get_attachmentname() {
        return $this->_attachmentname;
    }

    public function set_attachmentname($_attachmentname) {
        $this->_attachmentname = $_attachmentname;
    }

    public function get_attachment() {
        return $this->_attachment;
    }

    public function set_attachment($_attachment) {
        $this->_attachment = $_attachment;
    }

    protected function updateProject() {
        $vars = $this->toArray(null, true);
        $vars["supervisor"] = array("userid" => $vars["supervisor"]["userid"]); // Για να μην χάνονται τα roles
        if($this->_project->get_iscomplex() != 0) {
            throw new Exception('Το έργο δεν μπορεί να ενημερωθεί γιατί είναι σύνθετο.');
        }
        $this->_project->get_basicdetails()->setOptions($vars);
        $this->_project->get_financialdetails()->setOptions($vars);
        $this->_project->get_position()->setOptions($vars);
        $this->_project->save();
        return $this->_project;
    }

    public function onApproval() {}

    public function onRejection() {
        if(isset($this->_childrenaitiseis) && $this->_childrenaitiseis->count() > 0) {
            throw new Exception('Η αίτηση δεν μπορεί να απορριφθεί γιατί έχουν κατατεθεί άλλες που βασίζονται σε αυτή.');
        }
        if(isset($this->_project)) {
            throw new Exception('Η αίτηση δεν μπορεί να απορριφθεί γιατί έχει συνδεθεί με έργο.');
        }
        $projects = Zend_Registry::get('entityManager')->getRepository('Erga_Model_Project')->findProjects(array('aitisiypovolisergou' => $this->get_aitisiid()));
        if(count($projects) > 0) {
            foreach($projects as $project) {
                $project->set_aitisiypovolisergou(null);
            }
        }
    }
    
    public function hasOwnTitle() {
        return true;
    }
}
?>