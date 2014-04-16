<?php
namespace bundles\crud\Controllers;

/**
 * Crud ReadController
 *
 * @todo Tout est réunis dans le Core pour faire un scaffold des forms
 *
 *       Check user's permissions with the \Libray\Core\ACL component layer first then the CRUD model methods and finaly
 *       the \Libray\Core\Entity component to ensure data integrity on write access and perform the database request
 *
 * @author Nicolas Bonnici
 *
 */
class ReadController extends CrudController
{

    public function __preDispatch()
    {
        parent::__preDispatch();
    }

    /**
     * Read an entity then pass it to a given view
     *
     * @param string $sViewTpl
     */
    public function indexAction($sViewTpl = 'read/read.tpl')
    {
        assert('($oEntity = $this->oCrudModel->getEntity()) && $oEntity->isLoaded()');
        try {
            if (isset($this->aParams['view']) && strlen(isset($this->aParams['view'])) > 0) {
                $sViewTpl = $this->aParams['view'];
            }

            $oEntity = $this->oCrudModel->read();
            $oEntity->pk = $oEntity->getId();
            $this->aView['oEntity'] = $this->oCrudModel->read();
            $this->aView['iStatus'] = self::XHR_STATUS_OK;
        } catch (\bundles\crud\Models\CrudModelException $oException) {
            $this->aView['iStatus'] = self::XHR_STATUS_ERROR;
            $this->aView['error_message'] = $oException->getMessage();
            $this->aView['error_code'] = $oException->getCode();
        }
        $this->oView->render($this->aView, $sViewTpl, $this->aView['iStatus'], false, true);
    }
}