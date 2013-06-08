<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_View_Helper_ViewErga extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function viewErga($entries, $type, $edit = true, $showOrderLinks = true) {
        /*$return = '
            <form>
            <input type="checkbox" name="showcompletes" value="1" checked>Ολοκληρωμένα
            <input type="checkbox" name="showcompletes" value="1" checked>Έχουν εκπρόθεσμα παραδοτέα
            </form>';*/
        $headerLink = function($property, $text) use($showOrderLinks) {
            if($showOrderLinks) {
                return $this->view->getOrderLink($property, $text);
            } else {
                return $text;
            }
        };
        $return = '';
        $return .= '
            <table class="fatTable">
                <thead>
                <tr>
                    <th>'.$headerLink('status', 'Κατάσταση').'</th>
                    <th></th>
                    <th></th>
                    <th>'.$headerLink('basicdetails_title', 'Τίτλος').'</th>';
        if($edit == true) {
            $return .= '<th>'.$headerLink('basicdetails_supervisor_realname', 'Επιστημονικά Υπεύθυνος').'</th>';
        }
            $return .=  '<th>'.$headerLink('financialdetails_budget', 'Προϋπολογισμός').'</th>
                    <th>'.$headerLink('basicdetails_startdate', 'Ημ/νία Έναρξης').'</th>
                    <th>'.$headerLink('basicdetails_category_name', 'Κατηγορία').'</th>
                    <th>'.$headerLink('basicdetails_opprogramme_opprogrammeid', 'Ε.Π.').'</th>
                </tr>
                </thead>
                <tbody>';
        if(is_array($entries) || $entries instanceof Zend_Paginator) {
            foreach($entries as $entry) {
                $overviewlink = $this->view->url(array('module' => 'erga', 'controller' => $this->view->getControllerName(), 'action' => 'overview', 'projectid' => $entry->get_projectid()), null, true);
                $mfpoverviewlink = $this->view->url(array('module' => 'erga', 'controller' => $this->view->getControllerName(), 'action' => 'mfpoverview', 'projectid' => $entry->get_projectid()), null, true);
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
                            </td>
                            <td>';
                if($edit == false) {
                $return .= $this->view->escape($entry->__toString());
                } else {
                $return .= '<a href="'.$this->view->url(array('module' => 'erga', 'controller' => $this->view->getControllerName(), 'action' => 'review', 'projectid' => $entry->get_projectid()), null, true).'">
                                '.$this->view->escape($entry->__toString()).'
                            </a>';
                }
                $return .= '</td>';
                if($edit == true) {
                $return .= '<td>'.$this->view->escape($entry->get_basicdetails()->get_supervisor()->get_realnameLowercase()).'</td>';
                }
                $return .=  '<td class="formatFloat">'.$this->view->escape($entry->get_financialdetails()->get_budgetwithfpa()).'</td>
                            <td>'.$this->view->escape($entry->get_basicdetails()->get_startdate()).'</td>
                            <td>'.$this->view->escape($entry->get_basicdetails()->get_category()->get_name()).'</td>
                            <td>'.$this->view->escape($entry->get_financialdetails()->get_opprogramme()).'</td>
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