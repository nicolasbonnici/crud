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
            if (isset($this->aParams['parameters'])) {
                $aParameters = json_decode($this->aParams['parameters'], true);
            }
            
            // Xeditable element data we accept empty value since a variable can be nullable it'll be handle by the Entity component
            if (isset($this->aParams['name'], $this->aParams['value']) && ! empty($this->aParams['name'])) {
                $aParameters = array(
                    'name' => $this->aParams['name'],
                    'value' => $this->aParams['value']
                );
            }
            
            // The view
            if (isset($this->aParams['view']) && strlen(isset($this->aParams['view'])) > 0) {
                $sViewTpl = $this->aParams['view'];
            }
            
            if (empty($aParameters)) {
                throw new CrudControllerException('No parameters sent for update action!');
            }
            
            if (($this->aView['bUpdateEntity'] = $this->oCrudModel->update($aParameters)) === true) {
                $this->aView['iStatus'] = self::XHR_STATUS_OK;
                $this->aView['oEntity'] = $this->oCrudModel->getEntity();
            } else {
                $this->aView['bUpdateEntity'] = false; // clean exception
            }
        } catch (\bundles\crud\Models\CrudModelException $oException) {
            $this->aView['bUpdateEntity'] = false;
            $this->aView['error_message'] = $oException->getMessage();
            $this->aView['error_code'] = $oException->getCode();
        }
        
        $this->oView->render($this->aView, $sViewTpl, $this->aView['iStatus'], false, true);
    }
}