<?php

class MyControllerResources extends modRestController {
    public $classKey = 'modResource';
    public $defaultSortField = 'pagetitle';
    public $defaultSortDirection = 'ASC';
    
	public function setProperties(array $properties = array(),$merge = false) {
        parent::setProperties($properties,$merge);
        $this->sanitizeRequest();
	} 
    
    /**
     * Harden GPC variables by removing any MODX tags, Javascript, or entities.
     */
    public function sanitizeRequest() {
        $sanitizePatterns = array(
        'scripts'       => '@<script[^>]*?>.*?</script>@si',
        'entities'      => '@&#(\d+);@',
        'tags1'          => '@\[\[(?:(?!(\[\[|\]\])).)*\]\]@si',
        //'tags1'          => '@\[\[(.*?)\]\]@si',
        'tags2'          => '@(\[\[|\]\])@si',
        );
        
        // \[\[(?:(?!(\[\[|\]\])).)*\]\]
        
        // @\[\[(.*?)\]\]@si

        $modxtags = array_values($sanitizePatterns);
        
        $depth = (int)ini_get('max_input_nesting_level');
        $depth = ($depth <= 0) ? 99 : $depth + 1;
        
        if ($this->modx->getOption('allow_tags_in_post',null,true)) {
            modX :: sanitize($this->properties);
        } else {
            modX :: sanitize($this->properties, $modxtags, $depth);
        }
    }       

    public function beforeDelete() {
        if ($this->modx->hasPermission('delete_document')) {

        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function beforePut() {

        if ($this->modx->hasPermission('new_document')) {

        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }
    
    public function afterPut(array &$objectArray) {
        $tvlist = explode(',',$this->modx->getChunk('migxquasar_tvlist',array()));
        if (is_array($tvlist)){
            foreach ($tvlist as $tvname){
                $value = $this->object->get('tv_' . $tvname);
                $value = is_array($value) ? json_encode($value) : $value;
                
                $this->object->setTVValue($tvname,$value);
            }
        }            
    }

    public function beforePost() {

        if ($this->modx->hasPermission('edit_document')) {

        } else {
            throw new Exception('Unauthorized', 401);
        }


        return !$this->hasErrors();
    }

    public function verifyAuthentication() {
        if (!$this->modx->hasPermission('edit_document')) {
            return false;
        }
        return true;
    }
    
    public function afterRead(array &$objectArray) {
        
        $tvlist = explode(',',$this->modx->getChunk('migxquasar_tvlist',array()));
        if (is_array($tvlist)){
            foreach ($tvlist as $tvname){
                $value = $this->object->getTVValue($tvname);
                $array_value = json_decode($value,1);
                
                $objectArray['tv_' . $tvname] = is_array($array_value) ? $array_value : $value;
            }
        }
        
        return !$this->hasErrors();
    }    

    protected function prepareListQueryBeforeCount(xPDOQuery $c) {
        /*
        $joins = '[{"alias":"Bootsgattung"}]';

        $this->modx->migx->prepareJoins($this->classKey, json_decode($joins,1) , $c);

        $c->where(array('deleted' => 0, 'Bootsgattung.name' => 'Fuhrpark'));
        */
        $resourcelist = explode(',',$this->modx->getChunk('migxquasar_resourcelist',array()));
        $c->where(array('id:IN'=>$resourcelist));
        return $c;
    }
 
}
