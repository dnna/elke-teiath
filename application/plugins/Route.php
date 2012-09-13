<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Plugin_Route extends Zend_Controller_Router_Route_Module {
    public function match($path, $partial = false) {
        $params = parent::match($path, $partial);
        $ext = substr(strrchr($params['controller'], '.'), 1);
        if($ext != false) {
            $params['controller'] = preg_replace("/\\.[^.\\s]{3,4}$/", "", $params['controller']);
            // Χειρισμός των feeds
            if($ext === 'rss') {
                $params['action'] = 'feed';
                $params['feedType'] = 'rss';
            } else if($ext === 'atom') {
                $params['action'] = 'feed';
                $params['feedType'] = 'atom';
            } else if($ext === 'ics') {
                // Χειρισμός εξαγωγής ημερολογίου σε μορφή iCalendar
                $params['action'] = 'ical';
            } else {
                $params['action'] = $ext;
            }
        }
        return $params;
    }
    
    public function assemble($data = array(), $reset = false, $encode = true, $partial = false) {
        if(isset($data['absoluteUrl']) && $data['absoluteUrl'] == true) {
            $absoluteUrl = true;
            unset($data['absoluteUrl']);
        }
        if(isset($data['action'])) {
            if($data['action'] === 'rss' || $data['action'] === 'feed') {
                $data['feedType'] = 'rss';
                return $this->assembleFeed($data, $reset, $encode, $partial);
            } else if($data['action'] === 'atom') {
                $data['feedType'] = 'atom';
                return $this->assembleFeed($data, $reset, $encode, $partial);
            } else if($data['action'] === 'ical') {
                $data['feedType'] = 'ics';
                return $this->assembleFeed($data, $reset, $encode, $partial);
            }
        }
        if(isset($absoluteUrl) && $absoluteUrl == true) {
            $url = parent::assemble($data, $reset, $encode, $partial);
            return $request->getScheme().'://'.$request->getHttpHost().$baseUrl.'/'.$url;
        } else {
            return parent::assemble($data, $reset, $encode, $partial);
        }
    }
    
    protected function assembleFeed($data = array(), $reset=false, $encode = true, $partial = false)
    {
        $frontController = Zend_Controller_Front::getInstance();
        $url = '';
        if(!isset($data['feedType'])) {
            throw new Exception('Δεν έχει οριστεί είδος feed.');
        }
        $feedType = $data['feedType'];
        unset($data['feedType']);

        if(isset($data['module'])) {
            $url .= $data['module'];
            unset($data['module']);
        } else {
            $url .= 'default';
        }
        if(isset($data['controller'])) {
            $url .= '/'.$data['controller'].'.'.$feedType;
            unset($data['controller']);
        } else {
            $url .= '/index.'.$feedType;
        }
        unset($data['action']);

        if(!empty($data)) {
            $urlParts = array();
            foreach($data as $key=>$value) {
                $urlParts[] = $key . '=' . $value;
            }
            $url .= '?' . implode('&', $urlParts);
        }
        
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        return $request->getScheme().'://'.$request->getHttpHost().$baseUrl.'/'.$url;
    }
}
?>