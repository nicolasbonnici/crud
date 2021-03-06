<?php
namespace bundles\crud\Controllers;

/**
 * Crud HomeController
 *
 * @todo Tout est réunis dans le Core pour faire un scaffold des forms
 *
 *       Check user's permissions with the \Libray\Core\ACL component layer first then the CRUD model methods and finaly
 *       the \Libray\Core\Entity component to ensure data integrity on write access and perform the database request
 *
 * @author Nicolas Bonnici
 *
 */
class CrudController extends \Library\Core\Auth
{

    /**
     * Crud model couch instance
     *
     * @var \bundles\crud\Models\Crud
     */
    protected $oCrudModel;

    /**
     * Two dimensional array to restrict the CrudController entities scope (Before even check the ACL)
     *
     * @var array
     */
    protected $aEntitiesScope = array(
    	'Todo' => array(
    	   'entity'     => '\bundles\todo\Entities\Todo',
    	   'collection' => '\bundles\todo\Entities\Collection\TodoCollection'
        )
    );

    protected $aActionsScope = array(
        'create',
        'read',
        'update',
        'delete',
        'list',
        'listByUser'
    );

    /**
     * Pre dispatch hook for CrudController's actions
     * Ask ACL component for the current request
     * Instantiate Crud model couch
     *
     * @throws CrudControllerException
     */
    public function __preDispatch()
    {
        $this->aView['iStatus'] = self::XHR_STATUS_ERROR;

        if (count($this->aEntitiesScope) > 0 && ! array_key_exists($this->aParams['entity'], $this->aEntitiesScope)) {
            throw new CrudControllerException('Entity ' . $this->aParams['entity'] . ' restricted in CrudController scope', \Library\Core\Crud::ERROR_FORBIDDEN_BY_ACL);
        }

        if ($this->oUser->getId() !== intval($this->_session['iduser'])) {
            throw new CrudControllerException('User session is invalid', \Library\Core\Crud::ERROR_USER_INVALID);
        }

        // Check user permissions on entity then entity itself
        if (
            isset($this->aParams['entity']) &&
            ($sEntityName = $this->aParams['entity']) && strlen($sEntityName) > 0 &&
            ($sAction = ucfirst(substr($this->sController, 0, (strlen($this->sController) - strlen('controller'))))) &&
            in_array(strtolower($sAction), $this->aActionsScope) && ($sCheckMethodName = 'has' . $sAction . 'Access') &&
            method_exists($this, $sCheckMethodName) && $this->{$sCheckMethodName}($sEntityName)
        ) {

            try {
                $iPrimaryKey = ((isset($this->aParams['pk']) && intval($this->aParams['pk']) > 0) ? intval($this->aParams['pk']) : 0);

                // Check Entity instance with Crud model constructor
                $this->oCrudModel = new \bundles\crud\Models\Crud($this->aEntitiesScope[$sEntityName]['entity'], $this->aEntitiesScope[$sEntityName]['collection'], $iPrimaryKey, $this->oUser);
            } catch (\bundles\crud\Models\CrudModelException $oException) {
                throw new CrudControllerException('Invalid Entity requested: ' . $sEntityName, \Library\Core\Crud::ERROR_ENTITY_NOT_LOADABLE);
            }
        } else {
            throw new CrudControllerException('Error forbidden by ACL or unauthorized action: ' . $sAction . ' on entity: ' .$sEntityName . '.', \Library\Core\Crud::ERROR_FORBIDDEN_BY_ACL);
        }
    }

    /**
     * Post dispatch hook
     */
    public function __postDispatch()
    {}
}

class CrudControllerException extends \Exception
{
}