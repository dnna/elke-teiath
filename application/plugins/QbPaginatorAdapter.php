<?php
/**
 * Κάνει paginate με βάση τα results ενός Doctrine Querybuilder
 */
class Application_Plugin_QbPaginatorAdapter implements Zend_Paginator_Adapter_Interface
{
    /**
     * @var \Doctrine\ORM\QueryBuilder 
     */
    protected $_qb;

    protected $_rowcount;

    public function __construct($qb) {
        $this->_qb = $qb;
    }

    public function count() {
        if(!isset($this->_rowcount)) {
            if($this->_qb instanceof \Doctrine\ORM\QueryBuilder) {
                /* @var $countqb \Doctrine\ORM\QueryBuilder */
                $countqb = clone $this->_qb;
                /* @var $select \Doctrine\ORM\Query\Expr\Select */
                $dqlparts = $countqb->getDQLParts();
                $select = $dqlparts['select'][0]->__toString();
                if(is_array($dqlparts['groupBy']) && count($dqlparts['groupBy']) > 0) {
                    // Αν έχει groupBy τότε δεν έχουμε άλλη επιλογή από το να πάρουμε όλα τα results και να μετρήσουμε (slow)
                    $this->_rowcount = count($this->_qb->getQuery()->getResult());
                } else if(strpos($select, ',') !== false) {
                    $select = explode(',', $select);
                    $newselect = $select;
                    unset($newselect[0]);
                    $newselect = array_merge(array($select[0], ' COUNT('.$select[0].') as pagresult'), $newselect);
                    $countqb->select(implode(',', $newselect));
                    $result = $countqb->getQuery()->getScalarResult();
                    $this->_rowcount = (int)$result['pagresult'];
                } else {
                    $countqb->select('COUNT('.$select.')');
                    $this->_rowcount = $countqb->getQuery()->getSingleScalarResult();
                }
            } else {
                $this->_rowcount = $this->_qb->count();
            }
        }
        return $this->_rowcount;
    }

    public function getItems($offset, $itemCountPerPage) {
        if($this->_qb instanceof \Doctrine\ORM\QueryBuilder) {
            $this->_qb->setFirstResult($offset);
            $this->_qb->setMaxResults($itemCountPerPage);
            
            return $this->_qb->getQuery()->getResult();
        } else {
            return $this->_qb->slice($offset, $itemCountPerPage);
        }
    }
}
?>