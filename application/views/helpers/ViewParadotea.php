<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_View_Helper_ViewParadotea extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    /**
     * @param array $entries Τα παραδοτέα
     */
    public function viewParadotea($entries) {
        $now = new EDateTime('now');
        $return = '';
        $return .= '
            <table class="fatTable">
                <thead>
                <tr>
                <th>Τίτλος Παραδοτέου</th>
                <th>Πακέτο Εργασίας</th>
                <th>Υποέργο</th>
                <th>Ημ/νία παράδοσης</th>
                <th>Εκκρεμεί</th>
                </tr>
                </thead>
                <tbody>';
        if(is_array($entries)) {
            foreach($entries as $entry) {
                $return .= '
                        <tr>
                            <td>
                            <a href="'.$this->view->url(array('module' => 'erga', 'controller' => 'Paketaergasias', 'action' => 'reviewdeliverable', 'deliverableid' => $entry->get_recordid()), null, true).'">
                                '.$entry->get_title().'
                            </a>
                            </td>
                            <td>
                            <a href="'.$this->view->url(array('module' => 'erga', 'controller' => 'Paketaergasias', 'action' => 'review', 'workpackageid' => $entry->get_workpackage()->get_recordid()), null, true).'">
                                '.$entry->get_workpackage()->get_name().'
                            </a>
                            </td>
                            <td>
                            <a href="'.$this->view->url(array('module' => 'erga', 'controller' => 'Ypoerga', 'action' => 'review', 'subprojectid' => $entry->get_workpackage()->get_subproject()->get_subprojectid()), null, true).'">
                                '.$entry->get_workpackage()->get_subproject()->get_subprojecttitle().'
                            </a>
                            </td>
                            <td>
                                '.$entry->get_enddate().'
                            </td>
                            <td>
                                '.$now->diff($entry->get_enddate())->format('%a ημέρες').'
                            </td>
                        </tr>';
            }
        }
        $return .= '
                </tbody>
            </table>
            ';
        
        // Links σελίδων
        if($entries instanceof Zend_Paginator) {
            $return .= $entries->__toString();
        }
        
        return $return;
    }
}
?>