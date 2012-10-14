<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_View_Helper_ViewList extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    /**
     * @param array $entries Τα παραδοτέα
     */
    public function viewList($entries, $entriesCount, $type) {
        if(strpos($type, 'List') === false) {
            throw new Exception('Ο συγκεκριμένος τύπος δεν είναι τύπος λίστας.');
        }
        $return = "";
        $return .= '
            <div>
                <a href="'.$this->view->url(array('controller' => $this->view->getControllerName(), 'action' => 'add', 'type' => $type), null, true).'">
                    Προσθήκη Νέου
                </a>
            </div>
            <table class="thinTable">
                <thead>
                <tr>';
        if($entries != null && count($entries) > 0) {
            $form = new Dnna_Form_AutoForm(get_class($entries[0]), $this->view);
            $fields = $form->getFormFields();
            foreach($fields as $curField) {
                $return .= '<th>'.$curField->get_label().'</th>';
            }
        }
        $return .= '
                <th>Διαγραφή</th>
                </tr>
                </thead>
                <tbody>';
        if(is_array($entries)) {
            foreach($entries as $entry) {
                $return .= '
                    <tr>';
                    $first = true;
                    foreach($fields as $curField) {
                        if($curField->get_type() == Dnna_Form_Abstract_FormField::TYPE_SIMPLESELECT) {
                            $methodname = 'get_'.$curField->get_name().'AsString';
                        } else {
                            $methodname = 'get_'.$curField->get_name();
                        }
                        if($first) {
                            $return .= '
                                <td>
                                    <a href="'.$this->view->url(array('controller' => $this->view->getControllerName(), 'action' => 'edit', 'type' => $type, 'id' => $entry->get_id())).'">
                                '.$entry->$methodname().'
                                    </a>
                                </td>';
                            $first = false;
                        } else {
                            $return .= '<td>'.$entry->$methodname().'</td>';
                        }
                    }
                $return .= '
                            <td>
                                <a href="javascript:void(0);" onclick="if( prompt(\'Θέλετε σίγουρα να διαγράψετε την καταχώρηση;\',\'Παρακαλώ πληκτρολογήστε ΝΑΙ για να συνεχίσετε...\') == \'ΝΑΙ\'  ) { window.location = \''.$this->view->url(array('controller' => $this->view->getControllerName(), 'action' => 'delete', 'type' => $type, 'id' => $entry->get_id(), 'return' => urlencode($this->view->getCurrentUrl())), null, true).'\' }">
                                    <img src="'.$this->view->baseUrl('images/delete_x.gif').'" alt="Διαγραφή"/>
                                </a>
                            </td>
                        </tr>';
            }
        }
        $return .= '
                </tbody>
            </table>
            ';
        
        return $return;
    }
}
?>