<?php
namespace bundles\crud\Controllers;

/**
 * Crud UpdateController
 *
 * @todo Tout est réunis dans le Core pour faire un scaffold des forms
 *      
 *       Check user's permissions with the \Libray\Core\ACL component layer first then the CRUD model methods and finaly
 *       the \Libray\Core\Entity component to ensure data integrity on write access and perform the database request
 *      
 * @author Nicolas Bonnici
 *        
 */
class UpdateController extends CrudController
{

    /**
     * Run the parent __preDispatch hook at call to setup the CrudController
     *
     * @see \bundles\crud\Controllers\CrudController::__preDispatch()
     */
    public function __preDispatch()
    {
        parent::__preDispatch();
    }

    /**
     * Update an \app\Entities entity object then pass them to a given view
     *
     * @param unknown $sViewTpl            
     */
    public function indexAction($sViewTpl = 'update/update.tpl')
    {
        assert('($oEntity = $this->oCrudModel->getEntity()) && $oEntity->isLoaded()');
        try {
            
            // Form datas in JSON
            if (isset($this->_params['parameters'])) {
                $aParameters = json_decode($this->_params['parameters'], true);
            }
            
            // Xeditable element data we accept empty value since a variable can be nullable it'll be handle by the Entity component
            if (isset($this->_params['name'], $this->_params['value']) && ! empty($this->_params['name'])) {
                $aParameters = array(
                    'name' => $this->_params['name'],
                    'value' => $this->_params['value']
                );
            }
            
            // The view
            if (isset($this->_params['view']) && strlen(isset($this->_params['view'])) > 0) {
                $sViewTpl = $this->_params['view'];
            }
            
            if (empty($aParameters)) {
                throw new CrudControllerException('No parameters sent for update action!');
            }
            
            if (($this->_view['bUpdateEntity'] = $this->oCrudModel->update($aParameters)) === true) {
                $this->_view['iStatus'] = self::XHR_STATUS_OK;
                $this->_view['oEntity'] = $this->oCrudModel->getEntity();
            } else {
                $this->_view['bUpdateEntity'] = false; // clean exception
            }
        } catch (\bundles\crud\Models\CrudModelException $oException) {
            $this->_view['bUpdateEntity'] = false;
            $this->_view['error_message'] = $oException->getMessage();
            $this->_view['error_code'] = $oException->getCode();
        }
        
        $this->render($sViewTpl, $this->_view['iStatus'], false, true);
    }
}