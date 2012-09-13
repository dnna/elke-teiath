<?php
require_once APPLICATION_PATH . '/../library/SmartDOMDocument/SmartDOMDocument.php';

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_View_Helper_TableOfContents extends Zend_View_Helper_Abstract {

    public $view;

    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function tableOfContents($viewpath) {
        $realapppath = realpath(APPLICATION_PATH);
        $return = '';
        $navigation = new Zend_Navigation();
        $this->createPages(new DirectoryIterator($viewpath), $realapppath, $navigation);
        // Iterate recursively using RecursiveIteratorIterator
        $it = new RecursiveIteratorIterator(
                        $navigation, RecursiveIteratorIterator::SELF_FIRST);

        // Output: Page 1, Page 2, Page 2.1, Page 2.2, Page 3
        $return .= '<ol class="nonumbering">';
        foreach ($it as $page) {
            $return .= '<li value="'.$page->sectionid.'" class="nonumbering"><h3 data-sectionid="'.$page->sectionid.'">'.$this->view->navigation()->htmlify($page) . '</h3></li>';
        }
        $return .= '</ol>';
        return $return;
    }

    protected function createPages(DirectoryIterator $dir, $realapppath, Zend_Navigation_Container &$navigation) {
        foreach ($dir as $node) {
            $basename = $node->getBasename();
            if ($node->isDir() && !$node->isDot() && $basename[0] !== '.') {
                $this->createPages(new DirectoryIterator($node->getPathname()), $realapppath, $navigation);
            } else if ($node->isFile()) {
                $relpath = $this->getRelpath($node->getRealPath(), $realapppath);
                $nameandid = $this->getName($node->getRealPath());
                if ($relpath != null && $relpath !== '.') {
                    $navigation->addPage(new Zend_Navigation_Page_Uri(array('label' => $nameandid['name'], 'uri' => $relpath, 'sectionid' => $nameandid['sectionid'], 'order' => $nameandid['sectionid'])));
                }
            }
        }
    }

    public function getRelpath($absolutePath, $realapppath) {
        $relpath = str_replace($realapppath . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'help' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR, '', $absolutePath);
        $relpath = str_replace(DIRECTORY_SEPARATOR, '/', $relpath);
        $basename = basename($relpath, '.phtml');
        $dirname = dirname($relpath);
        if ($basename === 'index') {
            $relpath = $dirname;
        } else if ($dirname === '.') {
            return;
        } else {
            $relpath = $dirname . '/' . $basename;
        }
        return $this->view->baseUrl() . $relpath;
    }

    public function getName($absolutePath) {
        $fh = @fopen($absolutePath, 'r');
        if ($fh) {
            for ($i = 0; $i < 5; $i++) {
                $lines[] = fgets($fh, 4096);
            }
        } else {
            throw new Exception('Σφάλμα κατά την ανάγνωση του template');
        }
        $doc = new SmartDOMDocument();
        $doc->loadHTML(implode('', $lines));
        foreach ($doc->getElementsByTagName('h2') as $curElement) {
            $return = array();
            $return['name'] = html_entity_decode($this->DOMinnerHTML($curElement), ENT_QUOTES, 'UTF-8');
            $return['sectionid'] = $curElement->getAttribute('data-sectionid');
            return $return;
        }
        return array('name' => 'UNKNOWN NAME', 'sectionid' => '0');
    }
    
    private function DOMinnerHTML($element) 
    { 
        $innerHTML = ""; 
        $children = $element->childNodes; 
        foreach ($children as $child) 
        {
            $tmp_dom = new SmartDOMDocument(); 
            $tmp_dom->appendChild($tmp_dom->importNode($child, true)); 
            $innerHTML.=trim($tmp_dom->saveHTMLExact()); 
        }
        return $innerHTML;
    }
}
?>