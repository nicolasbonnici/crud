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
    public function indexAction($sViewTpl = 'list/list.tpl')
    {
        assert('$this->oCrudModel instanceof \bundles\crud\Models\Crud');
        try {
            if (isset($this->aParams['view']) && strlen(isset($this->aParams['view'])) > 0) {
                $sViewTpl = $this->aParams['view'];
            }
            
            if ($this->oCrudModel->loadEntities()) {
                $this->aView['iStatus'] = self::XHR_STATUS_OK;
                $this->aView['oEntities'] = $this->oCrudModel->getEntities();
            }
        } catch (\bundles\crud\Models\CrudModelException $oException) {
            $this->aView['error_message'] = $oException->getMessage();
            $this->aView['error_code'] = $oException->getCode();
        }
        $this->render($sViewTpl, $this->aView['iStatus'], false, true);
    }

    /**
     * Load latest entities restricted to the curently instantiate \app\Entities\User session scope
     *
     * @param string $sViewTpl            
     */
    public function listByUserAction($sViewTpl = 'list/list.tpl')
    {
        assert('$this->oCrudModel instanceof \bundles\crud\Models\Crud');
        try {
            if (isset($this->aParams['view']) && strlen(isset($this->aParams['view'])) > 0) {
                $sViewTpl = $this->aParams['view'];
            }
            
            if ($this->oCrudModel->loadUserEntities()) {
                $this->aView['iStatus'] = self::XHR_STATUS_OK;
                $this->aView['oEntities'] = $this->oCrudModel->getEntities();
                $this->aView['aEntityAttributes'] = $this->oCrudModel->getEntityAttributes();
            }
        } catch (\bundles\crud\Models\CrudModelException $oException) {
            $this->aView['error_message'] = $oException->getMessage();
            $this->aView['error_code'] = $oException->getCode();
        }
        
        $this->render($sViewTpl, $this->aView['iStatus'], false, true);
    }
}