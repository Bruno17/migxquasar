<?php

class MyControllerFields extends modRestController {
    public $classKey = 'modResource';
    public $defaultSortField = 'pagetitle';
    public $defaultSortDirection = 'ASC';
    
	public function XXXsetProperties(array $properties = array(),$merge = false) {
        parent::setProperties($properties,$merge);
        $this->sanitizeRequest();
	} 
    
    /**
     * Harden GPC variables by removing any MODX tags, Javascript, or entities.
     */
    public function XXXsanitizeRequest() {
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
    
    public function XXXafterRead(array &$objectArray) {
        
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

    protected function XXXprepareListQueryBeforeCount(xPDOQuery $c) {
        /*
        $joins = '[{"alias":"Bootsgattung"}]';

        $this->modx->migx->prepareJoins($this->classKey, json_decode($joins,1) , $c);

        $c->where(array('deleted' => 0, 'Bootsgattung.name' => 'Fuhrpark'));
        */
        $resourcelist = explode(',',$this->modx->getChunk('migxquasar_resourcelist',array()));
        $c->where(array('id:IN'=>$resourcelist));
        return $c;
    }
    
    public function getList() {
        $list = array();
        
        $ui = '
{
   "resourcefields":{
      "label":"Resource Fields",
      "ui":[
         {
            "component":"q-input",
            "model":"pagetitle",
            "fieldOptions":{
               "on":[
                  "input"
               ],
               "attrs":{
                  "placeholder":"Please enter pagetitle",
                  "label":"Pagetitle",
                  "outlined":true,
                  "bg-color":"white"
               }
            }
         },
         {
            "component":"q-field",
            "fieldOptions":{
               "attrs":{
                  "label":"Content",
                  "outlined":true,
                  "stack-label":true,
                  "bg-color":"white"
               }
            },
            "children":[
               {
                  "component":"q-editor",
                  "model":"content",
                  "fieldOptions":{
                     "on":[
                        "input"
                     ],
                     "slot":"control",
                     "class":"full-width"
                  }
               }
            ]
         },
         {
            "component":"q-input",
            "model":"content",
            "fieldOptions":{
               "on":[
                  "input"
               ],
               "attrs":{
                  "type":"textarea",
                  "placeholder":"Please enter content",
                  "label":"Content",
                  "outlined":true,
                  "bg-color":"white"
               }
            }
         },
         {
            "component":"q-checkbox",
            "model":"published",
            "fieldOptions":{
               "on":[
                  "input"
               ],
               "attrs":{
                  "label":"published"
               },
               "props":{
                  "true-value":true
               }
            }
         }
      ]
   },
   "tvs":{
      "label":"TVs",
      "ui":[
         {
            "component":"q-input",
            "model":"tv_scripts",
            "fieldOptions":{
               "on":[
                  "input"
               ],
               "attrs":{
                  "placeholder":"Please enter pagetitle",
                  "label":"Scripts",
                  "outlined":true,
                  "bg-color":"white",
                  "type":"textarea"
               }
            }
         }
      ]
   },
   "migx_1":{
      "label":"MIGX 1",
      "type":"formrepeater",
      "field":"tv_migx_contentblocks",
      "ui":[
         {
            "component":"q-input",
            "model":"title",
            "fieldOptions":{
               "on":[
                  "input"
               ],
               "attrs":{
                  "placeholder":"Please enter pagetitle",
                  "label":"Title",
                  "outlined":true,
                  "bg-color":"white",
                  "type":"text"
               }
            }
         },
         {
            "component":"q-input",
            "model":"text",
            "fieldOptions":{
               "on":[
                  "input"
               ],
               "attrs":{
                  "placeholder":"Please enter pagetitle",
                  "label":"Text",
                  "outlined":true,
                  "bg-color":"white",
                  "type":"textarea"
               }
            }
         }         
      ]
   }   
}       
        ';
        
        $list = json_decode($ui);
        
        return $this->collection($list);        
    }
    
    
 
}
