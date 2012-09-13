<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Praktika_EpitropesController extends Zend_Controller_Action {
    public function indexAction() {
        $this->_request->setParam('format', null);
        $filters = $this->_helper->filterHelper($this, 'Praktika_Form_Committee_Filters');
        $this->view->entries = Zend_Registry::get('entityManager')->getRepository('Praktika_Model_CommitteeBase')->findBy(array('_active' => true));
    }

    public function newAction() {
        $form = new Praktika_Form_Committee($this->view);
        $this->formhandling(null, $form);
    }

    public function reviewAction() {
        // Έλεγχος ότι η επιτροπή υπάρχει
        $committee = Zend_Registry::get('entityManager')->getRepository('Praktika_Model_CommitteeBase')->find($this->getRequest()->getParam('id', null));
        $this->view->committee = $committee;
        if($committee == null) {
            throw new Exception("Η συγκεκριμένη επιτροπή δεν υπάρχει.");
        }
        if(!isset($this->view->type)) {
            $this->view->type = get_class($committee);
        }

        $form = new Praktika_Form_Committee($this->view);
        $form->populate($committee);
        $form->getSubForm('competitiontype')->getElement('type')->setValue(Praktika_Model_CommitteeBase::getReverseMapping(get_class($committee)));

        $this->formhandling($committee, $form);
    }

    public function deleteAction() {
        $committee = Zend_Registry::get('entityManager')->getRepository('Praktika_Model_CommitteeBase')->find($this->getRequest()->getParam('id', null));
        if($committee == null) {
            throw new Exception('Η επιτροπή δεν υπάρχει');
        } else {            return $this->_helper->deleteHelper($this, 'id', 'Praktika_Model_CommitteeBase', 'committee');
        }
    }

    protected function formhandling($committee = null, Zend_Form $form = null) {
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            $newtype = Praktika_Model_CommitteeBase::getEpitropiMapping($values['competitiontype']['type']);
            if($committee == null || $newtype !== get_class($committee)) {
                $newcommittee = Praktika_Model_CommitteeBase::factory($values['competitiontype']['type']);
            }
            if(isset($newcommittee) && defined(get_class($newcommittee).'::formclass')) {
                $formclass = $newcommittee::formclass;
                $form->addSubForm(new $formclass($this->view), 'default');
            } else if(defined(get_class($committee).'::formclass')) {
                $formclass = $committee::formclass;
                $form->addSubForm(new $formclass($this->view), 'default');
            }
            // Η φόρμα έχει σταλθεί. Ελέγχουμε αν ειναι έγκυρη.
            if ($form->isValid($this->getRequest()->getPost())) {
                // Η φόρμα ΕΙΝΑΙ έγκυρη. Αποθηκεύουμε στη βάση και στέλνουμε το χρήστη στη σελίδα επιβεβαίωσης.
                $values = $form->getValues();
                if($committee != null) {
                    $committee->remove();
                }
                if(isset($newcommittee)) {
                    $newcommittee->setOptions($values);
                    $newcommittee->save();
                } else {
                    $committee->setOptions($values);
                    $committee->save();
                }
                $this->_helper->flashMessenger->addMessage('Οι αλλαγές καταχωρήθηκαν με επιτυχία');
                $this->_helper->redirector('index', null, null, array('type' => $this->_request->getParam('type')));
                return;
            }
        }
        // Η φόρμα δεν έχει σταλθεί ή δεν είναι έγκυρη. Τη στέλνουμε στο view και σταματάμε.
        $this->view->form = $form;
        $this->view->headScript()->appendFile($this->view->baseUrl('media/js/formchangedwarning.js', 'text/javascript'));
        $this->_helper->viewRenderer('review');
        return;
    }
    
    public function ajaxformAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $params = $this->_request->getParams();
        if(isset($params['type'])) {
            $committeeclassname = Praktika_Model_CommitteeBase::getEpitropiMapping($params['type']);
            if(!isset($committeeclassname)) {
                throw new Exception('Ο συγκεκριμένος τύπος επιτροπής δεν υπάρχει.');
            }
        } else {
            throw new Exception('Δεν έχει οριστεί η παράμετρος type.');
        }
        if(isset($params['committeeid']) && $params['committeeid'] != null) {
            $curcommittee = Zend_Registry::get('entityManager')->getRepository('Praktika_Model_CommitteeBase')->find($params['committeeid']);
            $params = $params + $curcommittee->getOptions();
        }
        $committee = new $committeeclassname();
        $this->view->subform = new Dnna_Form_FormBase($this->view);
        $this->view->subform->setDecorators(array('FormElements'));
        if(defined(get_class($committee).'::formclass')) {
            $formclass = $committee::formclass;
            $this->view->subform->addSubForm(new $formclass($this->view), 'default');
        } else {
            $this->view->subform->addSubForm(new Dnna_Form_SubFormBase($this->view), 'default');
        }
        if(isset($curcommittee)) {
            $this->view->subform->populate($curcommittee);
        } else {
            $this->view->subform->populate($committee);
        }
        $legend = new Application_Form_Element_Note('legend', array(
            'value' => '<legend>Πρόσθετα Στοιχεία</legend>',
            'order' => 0
        ));
        $legend->setDecorators(array('ViewHelper'));
        $this->view->subform->addElement($legend);
        $this->view->subform->getSubForm('default')->setDecorators(array('FormElements'));
        echo $this->view->subform;
    }
}
?>