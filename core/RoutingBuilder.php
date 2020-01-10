<?php

use function Helpers\Core\regex_dir_search;


/**
 * Class RoutingBuilder
 * This class responsible for building all routes
 *
 *
 */
class RoutingBuilder
{
    /**
     * @var array<string>
     * []
     */
    private $_routers;
    private static $HttpVerbs = [
        'HttpGet',
        'HttpPost',
        'HttpPut',
        'HttpDelete',
        'HttpPatch',
        'HttpVerb'
    ];
    public function __construct()
    {
        $this->_routers['ControllerRouters'] = [
            'BaseUrl' => '', // no prefix
            'BaseDir' => __SPECIFICATION_APP_LOCATION__ . '/'  . __DEFAULT_CONTROLLERS_PATH__ ,
            'Controllers' => [

            ]
        ];
        $this->_routers['AreaRouters'] = [
            'BaseUrl' => '', // no prefix
            'BaseDir' => __SPECIFICATION_APP_LOCATION__  . '/' .  __DEFAULT_AREAS_PATH__ ,
            'Areas' => [

            ]

        ];
        $this->_routers['PageRouters'] = [
            'BaseUrl' => '', // no prefix
            'BaseDir' => __SPECIFICATION_APP_LOCATION__ . '/'  . __DEFAULT_PAGES_PATH__ ,
            'Pages' => [

            ]
        ];

    }

    public function build_areas(){

    }

    public function build_controllers(): void
    {
        // load controllers
        $controllers_location = __SPECIFICATION_APP_LOCATION__ . '/' . __DEFAULT_CONTROLLERS_PATH__ ;
        $controllers = regex_dir_search($controllers_location, '/.*\.php/');

        // [Controller location not does not have any meaning for controller link]

        foreach ($controllers as $key => $controller){
            require_once $controller;
            $classes = get_declared_classes();
            $class = end($classes);
            try{
                $r = new ReflectionClass($class);
                $className = $r->getName();
                $doc = $r->getDocComment();
                $metas = $this->get_annotation_metas($doc);
                $urlName = $metas['Controller']['urlName'] ?? $className;
                $fullPath = $controller;
                $actions = $this->get_class_actions($r->getMethods());

                // store
                $this->_routers['ControllerRouters']['Controllers'];
                /**
                 * we stop here , we need to get controller name , but with provided @Controller(url="")
                 * Like what  we done with actions
                 * so its very big work to deal with
                 */
                echo 'ok';
            }catch (Exception $ex) {

            }
        }
    }
    public function build_pages(){
        $pages_location = $this->_routers['PageRouters']['BaseDir'];
        $m = regex_dir_search($pages_location , '/.*\.php/');
        $this->_routers['PageRouters']['Pages'] =
            $this->get_only_relative_files_locations($m , $pages_location);
    }

    private function deserialize_class_metas(array $metas) : array {
        $ds_metas = [];
    }

    /**
     * @param array<ReflectionMethod> $methods
     * @return array
     */
    private function get_class_actions(array $methods) : array {
        $actions = [];
        foreach ($methods as $key => $method) {
            $docs = $method->getDocComment();
            if ($docs){
                [$verb , $annotations] = $this->get_action_annotations($docs);
                if ($verb){ // http method
                    // check on parameters
                    $action_name = $annotations[$verb]['slug'] ?? $method->getName();
                    $actions[$action_name] = [];
                    $actions[$action_name]['HttpVerb'] = $verb;
                    $actions[$action_name]['MetaDataCollection'] = $annotations;

                    $actions[$action_name]['Method'] = $method->getName();
                    $parameters = $method->getParameters();
                    $actions[$action_name]['Parameters'] = [];
                    $hasHttpBody = isset($annotations['HttpBodyParam']);
                    if ($parameters){
                        foreach ($parameters as $p_key => $param){
                            if ($hasHttpBody && $param->getName() === $annotations['HttpBodyParam']['name']){
                                $actions[$action_name]['HttpBodyParam'] = $param->getName();
                            }

                        }
                    }
                }
            }
        }
        return $actions;
    }



    private function get_action_annotations(string $method_doc) : array {
        $annotations = $this->get_annotation_metas($method_doc);
        foreach ($annotations as $verb => $values){
            if (in_array($verb, static::$HttpVerbs, true)){
                return [$verb , $annotations];
            }
        }
        return [];

    }
    private function get_annotation_metas($doc) : array {
        preg_match_all('#@(.*?)\n#s', $doc, $annotations);
        if ($annotations){
            $annotation_list = [];
            foreach ($annotations[0] as $key => $value){
                if (trim($value) === ''){
                    continue;
                }
                // we will remove start and end to reduce regex iterations from 1263 -> 170
                $pos = strpos( $value , '(');
                $class_name = substr(trim(substr($value, 0,$pos)), 1);
                $brc = substr($value, $pos + 1);
                $brc = substr($brc,0, -1);
                $annotation = $this->get_annotations_from_string($brc);
                if ($annotation){
                    $annotation_list [$class_name] = $annotation;
                }
            }
            return $annotation_list;
        }
        return [];
    }
    private function get_annotations_from_string(string $brc): array
    {

        preg_match_all('/(\s*(.+?)\s*=\s*"(.+?)"\s*),?/', $brc, $result);

        return $this->key_value_map_from_arrays($result[2], $result[3]);
    }
    private function key_value_map_from_arrays(array $arr1, array $arr2) : array {
        // if we will use this function in other purpose , we need to do multiple checks
        // but here we will just use it to map $arr1,$arr2 => key,value pairs
        // so we know exactly how much items in two arrays
        $arr = [];
        for ($c = 0, $cMax = count($arr1) ; $c < $cMax; $c++){
            $arr[$arr1[$c]] = $arr2[$c];
        }
        return $arr;
    }

    private function get_only_relative_files_locations(array $pages_array, string $base) : array {
        $pages = [];
        $l = strlen($base);
        foreach ($pages_array as $key => $page){
            $pages[] = str_replace(array('.php', '\\'), array('', '/'), substr($page, $l + 1));
        }
        return $pages;
    }
}
