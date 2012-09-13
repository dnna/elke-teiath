<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_Erga_Ypoerga_Paradotea_AuthorsController extends Api_IndexController
{
    const noindex = true;

    public function init() {
        parent::init();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $this->view->addHelperPath(APPLICATION_PATH.'/modules/erga/views/helpers', 'Erga_View_Helper');
    }

    public function indexAction() {
        throw new Exception('Δεν υποστηρίζεται.');
    }

    public function getAction() {
        $object = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubItems_Deliverable')->find($this->_request->getParam('id'));
        if(!isset($object)) {
            throw new Exception('Το αντικείμενο δεν βρέθηκε.', 404);
        }
        $this->view->deliverable = $object;
        $subproject = $this->view->deliverable->get_workpackage()->get_subproject();
        if($subproject->get_subprojectdirectlabor() == 1) {
            $authors['authors'] = array();
            foreach($object->get_authors() as $curAuthor) {
                $authors['authors'][$curAuthor->get_recordid()] = $curAuthor->__toString();
            }
        } else {
            $authors['contractors'] = array();
            if($object->get_contractor() != null) {
                $authors['contractors'][$object->get_contractor()->get_recordid()] = $object->get_contractor()->__toString();
            }
        }
        echo json_encode($authors);
    }

    public function postAction() {
        throw new Exception('Δεν υποστηρίζεται.');
    }

    public function putAction() {
        throw new Exception('Δεν υποστηρίζεται.');
    }

    public function deleteAction() {
        throw new Exception('Δεν υποστηρίζεται.');
    }

    public function schemaAction() {
        throw new Exception('Δεν υποστηρίζεται.');
    }
}
?>