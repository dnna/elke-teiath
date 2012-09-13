<?php
require_once APPLICATION_PATH . '/../library/iCalcreator/iCalcreator.class.php';

/**
 * Παίρνει ένα αντικείμενο και εμφανίζει τα πεδία του σαν στήλες ενός αρχείου
 * excel.
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Action_Helper_CreateIcal extends Zend_Controller_Action_Helper_Abstract {

    protected $_timezone;
    protected $_name;
    protected $_email;
    protected $_location;

    public function __construct() {
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $config = $bootstrap->getOptions();
        $this->_timezone = $config['phpSettings']['date']['timezone'];
        $this->_name = $config['admin']['email']['toname'];
        $this->_email = $config['admin']['email']['toaddress'];
        $this->_location = $config['admin']['location'];
    }
    
    protected function addEvent(Synedriaseisee_Model_Event $event, &$v, $controller, $unique_id) {
        $vevent = & $v->newComponent('vevent');
        // create an event calendar component
        $vevent->setProperty("uid", md5(get_class($event))."-".$event->get_id()."@".$unique_id);
        // set uid based on the event class and event id
        $edstart = $event->get_start();
        $edstart->setTimezone(new DateTimeZone('Etc/UTC'));
        $start = array('year' => $edstart->format('Y'), 'month' => $edstart->format('n'), 'day' => $edstart->format('j'), 'hour' => $edstart->format('G'), 'min' => $edstart->format('i'), 'sec' => $edstart->format('s'), 'tz' => 'Z');
        $vevent->setProperty('dtstart', $start);
        $edend = $event->get_end();
        $edend->setTimezone(new DateTimeZone('Etc/UTC'));
        $end = array('year' => $edend->format('Y'), 'month' => $edend->format('n'), 'day' => $edend->format('j'), 'hour' => $edend->format('G'), 'min' => $edend->format('i'), 'sec' => $edend->format('s'), 'tz' => 'Z');
        $vevent->setProperty('dtend', $end);
        $vevent->setProperty('LOCATION', $this->_location);
        // property name - case independent
        $vevent->setProperty('summary', $event->__toString());
        $vevent->setProperty('description', $event->__toString());
        $vevent->setProperty('comment', $event->__toString());
        $vevent->setProperty('attendee', $this->_email);
        // Google Calendar attributes
        /*$vevent->setProperty('X-GOOGLE-CALENDAR-CONTENT-TITLE', $event->__toString());
        $vevent->setProperty('X-GOOGLE-CALENDAR-CONTENT-ICON', 'http://www.teiath.gr/favicon.ico');
        if($event instanceof Synedriaseisee_Model_Synedriasi) {
            $opts = array('module' => 'synedriaseisee', 'controller' => 'index', 'action' => 'eventview', 'id' => $event->get_id());
            $vevent->setProperty('X-GOOGLE-CALENDAR-CONTENT-URL', $controller->view->serverUrl().$controller->view->url($opts+array('print' => 'true')));
            $vevent->setProperty('X-GOOGLE-CALENDAR-CONTENT-TYPE', 'text/html');
            $vevent->setProperty('X-GOOGLE-CALENDAR-CONTENT-WIDTH', '332');
            $vevent->setProperty('X-GOOGLE-CALENDAR-CONTENT-HEIGHT', '255');
        }*/
        return $v;
    }

    public function direct(Zend_Controller_Action $controller, $events) {
        $controller->getHelper('layout')->disableLayout();
        $controller->getHelper('viewRenderer')->setNoRender(TRUE);
        if(isset($_SERVER['HTTP_HOST'])) {
            $uniqueid = $_SERVER['HTTP_HOST'];
        } else {
            $uniqueid = $_SERVER['SERVER_NAME']; // Fallback check http://stackoverflow.com/questions/5404811/php-get-domain-name
        }
        $uniqueid = str_replace("www.","", $uniqueid);

        $config = array('unique_id' => $uniqueid);
        // set Your unique id
        $v = new vcalendar($config);
        // create a new calendar instance
        $v->setProperty('method', 'PUBLISH');
        // required of some calendar software
        $v->setProperty("x-wr-calname", $this->_name);
        // required of some calendar software
        $v->setProperty("X-WR-CALDESC", $this->_name);
        // required of some calendar software
        $v->setProperty("X-WR-TIMEZONE", $this->_timezone);
        // required of some calendar software
        // Βρίσκουμε τα events
        foreach($events as $curEvent) {
            $this->addEvent($curEvent, $v, $controller, $uniqueid);
        }
        // supporting parse of strict rfc2445 formatted text
        // all calendar components are described in rfc2445
        // a complete iCalcreator function list (ex. setProperty) in iCalcreator manual
        $v->returnCalendar();
        //$str = $v->createCalendar(); // generate and get output in string, for testing?
        // redirect calendar file to browser
    }

}

?>