<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_View_Helper_ViewYpoerga extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function viewYpoerga($entries, $type, $edit = true) {
        $return = '';
        $return .= '
            <table class="fatTable">
                <thead>
                <tr>
                    <th>Κατάσταση</th>
                    <th></th>
                    <th></th>
                    <th>Έργο</th>
                    <th>Υποέργο</th>';
            $return .=  '<th>Προϋπολογισμός</th>
                    <th>Ημ/νία Έναρξης</th>
                    <th>Κατηγορία</th>
                    <th>Ε.Π.</th>
                </tr>
                </thead>
                <tbody>';
        if(is_array($entries) || $entries instanceof Zend_Paginator) {
            foreach($entries as $entry) {
                $overviewlink = $this->view->url(array('module' => 'erga', 'controller' => $this->view->getControllerName(), 'action' => 'overview', 'subprojectid' => $entry->get_subprojectid()), null, true);
                $mfpoverviewlink = $this->view->url(array('module' => 'erga', 'controller' => $this->view->getControllerName(), 'action' => 'mfpoverview', 'subprojectid' => $entry->get_subprojectid()), null, true);
                if($edit == false) {
                    $return .= '<tr style="cursor:pointer" onclick="window.location = \''.$overviewlink.'\'" title="Επισκόπηση έργου">';
                } else {
                    $return .= '<tr>';
                }
                $return .= '<td>'.$this->view->getCompletionIcon($entry).'</td>
                            <td class="nopadding"><a href="'.$overviewlink.'" id="episkopish">
                                    <img src="'.$this->view->baseUrl('images/overview.png').'" alt="review" title="Επισκόπηση έργου" />
                                </a>
                            </td>
                            <td class="nopadding"><a href="'.$mfpoverviewlink.'" id="episkopish">
                                    <img src="'.$this->view->baseUrl('images/icons/excelIcon.jpg').'" alt="mfpreview" title="Επισκόπηση ΜΦΠ" />
                                </a>
                            </td>';
                $return .= '<td>'.$this->view->escape($entry->get_parentproject()->__toString()).'</td>';
                $return .= '<td>'.$this->view->escape($entry->__toString()).'</td>';
                $return .= '</td>';
                $return .=  '<td class="formatFloat">'.$this->view->escape($entry->get_subprojectbudgetfpa()).'</td>
                            <td>'.$this->view->escape($entry->get_subprojectstartdate()).'</td>
                            <td>'.$this->view->escape($entry->get_parentproject()->get_basicdetails()->get_category()->get_name()).'</td>
                            <td>'.$this->view->escape($entry->get_parentproject()->get_financialdetails()->get_opprogramme()).'</td>
                        </tr>';
            }
        }
        $return .= '
                </tbody>
            </table>';

        // Links σελίδων (pagination)
        $return .= $entries->__toString();

        return $return;
    }
}
?>