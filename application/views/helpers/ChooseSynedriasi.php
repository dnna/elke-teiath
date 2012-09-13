<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_View_Helper_ChooseSynedriasi extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function chooseSynedriasi(Aitiseis_Model_AitisiBase $aitisi) {
        $this->view->headScript()->appendFile($this->view->baseUrl('media/js/ajaxformdialog.js', 'text/javascript'));
        $this->view->flexboxDependencies();
        $this->view->headScript()->appendFile($this->view->baseUrl('media/js/synedriaseisee/choosesynedriasi.js', 'text/javascript'));
        $return = '';
        $return .= '<a href="javascript:void(0);" id="csaitisiid-'.$aitisi->get_aitisiid().'" class="chooseSynedriasi">
                        <img src="'.$this->view->baseUrl('images/conference.png').'" alt="Προσθήκη ως θέμα συνεδρίασης"  title="Προσθήκη ως θέμα συνεδρίασης" />
                    </a>';

        return $return;
    }
}
?>