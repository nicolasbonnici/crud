<?php
namespace bundles\crud\Controllers;

/**
 * Crud CreateController
 *
 * @todo Tout est réunis dans le Core pour faire un scaffold des forms
 *      
 *       Check user's permissions with the \Libray\Core\ACL component layer first then the CRUD model methods and finaly
 *       the \Libray\Core\Entity component to ensure data integrity on write access and perform the database request
 *      
 * @author Nicolas Bonnici
 *        
 */
class CreateController extends CrudController
{

    public function __preDispatch()
    {
        parent::__preDispatch();
    }

    /**
     * Create an \app\Entities entity object then pass it to a given view
     *
     * @param string $sViewTpl            
     */
    public function indexAction($sViewTpl = 'create/create.tpl')
    {
        assert('$this->oCrudModel instanceof \bundles\crud\Models\Crud');
        try {
            // Toutes les données du formulaire en JSON
            if (isset($this->_params['parameters'])) {
                $aParameters = json_decode($this->_params['parameters'], true);
            }
            
            if (isset($this->_params['view']) && strlen(isset($this->_params['view'])) > 0) {
                $sViewTpl = $this->_params['view'];
            }
            
            if ($this->oCrudModel->create($aParameters)) {
                $this->_view['bCreateEntity'] = true;
                $this->_view['iStatus'] = self::XHR_STATUS_OK;
                $this->_view['oEntity'] = $this->oCrudModel->getEntity();
            } else {
                $this->_view['bCreateEntity'] = false;
            }
        } catch (\bundles\crud\Models\CrudModelException $oException) {
            $this->_view['bCreateNewEntity'] = false;
            $this->_view['error_message'] = $oException->getMessage();
            $this->_view['error_code'] = $oException->getCode();
        }
        
        $this->render($sViewTpl, $this->_view['iStatus'], false, true);
    }
}