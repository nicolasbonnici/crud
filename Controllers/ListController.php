<?php
namespace bundles\crud\Controllers;

/**
 * Crud ListController
 *
 * @todo Tout est réunis dans le Core pour faire un scaffold des forms
 *
 *       Check user's permissions with the \Libray\Core\ACL component layer first then the CRUD model methods and finaly
 *       the \Libray\Core\Entity component to ensure data integrity on write access and perform the database request
 *
 * @author Nicolas Bonnici
 *
 */
class ListController extends CrudController
{

    public function __preDispatch()
    {
        parent::__preDispatch();
    }

    /**
     * List \app\Entities entity then pass them to a given view
     *
     * @param string $sViewTpl
     */
    public function indexAction($sViewTpl = 'list/list.tpl', $iOffset = 0, $iLoadStep = 10, $sOrderBy = 'lastupdate', $sOrder = 'DESC')
    {
        assert('$this->oCrudModel instanceof \bundles\crud\Models\Crud');
        try {
            if (isset($this->aParams['view']) && strlen(isset($this->aParams['view'])) > 0) {
                $sViewTpl = $this->aParams['view'];
            }

            if (isset($this->aParams['ioffset']) && $this->aParams['ioffset'] > 0) {
                $iOffset = (int) $this->aParams['ioffset'];
            }

            if (isset($this->aParams['iLoadStep']) && $this->aParams['iLoadStep'] > 0) {
                $iLoadStep = (int) $this->aParams['iLoadStep'];
            }

            if (isset($this->aParams['orderby']) && $this->aParams['orderby'] > 0) {
                $sOrderBy = $this->aParams['orderby'];
            }

            if (isset($this->aParams['order']) && $this->aParams['order'] > 0) {
                $sOrder = $this->aParams['order'];
            }

            $aLimit = array($iOffset, $iLoadStep);
            if ($this->oCrudModel->load($sOrderBy, $sOrder, $aLimit)) {
                $this->aView['iStatus'] = self::XHR_STATUS_OK;
                $this->aView['oEntities'] = $this->oCrudModel->getEntities();
            }
        } catch (\bundles\crud\Models\CrudModelException $oException) {
            $this->aView['error_message'] = $oException->getMessage();
            $this->aView['error_code'] = $oException->getCode();
        }
        $this->oView->render($this->aView, $sViewTpl, $this->aView['iStatus'], false, true);
    }
}