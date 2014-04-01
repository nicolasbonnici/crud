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
            if (isset($this->_params['view']) && strlen(isset($this->_params['view'])) > 0) {
                $sViewTpl = $this->_params['view'];
            }
            
            if ($this->oCrudModel->loadEntities()) {
                $this->_view['iStatus'] = self::XHR_STATUS_OK;
                $this->_view['oEntities'] = $this->oCrudModel->getEntities();
            }
        } catch (\bundles\crud\Models\CrudModelException $oException) {
            $this->_view['error_message'] = $oException->getMessage();
            $this->_view['error_code'] = $oException->getCode();
        }
        $this->render($sViewTpl, $this->_view['iStatus'], false, true);
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
            if (isset($this->_params['view']) && strlen(isset($this->_params['view'])) > 0) {
                $sViewTpl = $this->_params['view'];
            }
            
            if ($this->oCrudModel->loadUserEntities()) {
                $this->_view['iStatus'] = self::XHR_STATUS_OK;
                $this->_view['oEntities'] = $this->oCrudModel->getEntities();
                $this->_view['aEntityAttributes'] = $this->oCrudModel->getEntityAttributes();
            }
        } catch (\bundles\crud\Models\CrudModelException $oException) {
            $this->_view['error_message'] = $oException->getMessage();
            $this->_view['error_code'] = $oException->getCode();
        }
        
        $this->render($sViewTpl, $this->_view['iStatus'], false, true);
    }
}