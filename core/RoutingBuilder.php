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
            $metas = $this->get_annotation_metas($class);

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
    private function get_annotation_metas($class) : array {
        try {
            $r = new ReflectionClass($class);
            $doc = $r->getDocComment();
            echo $doc . ' <br>';
            preg_match_all('#@(.*?)\n#s', $doc, $annotations);
            if ($annotations){
                $annotation_list = [];
                foreach ($annotations[0] as $key => $value){
                    // we will remove start and end to reduce regex iterations from 1263 -> 170
                    $pos = strpos( $value , '(');
                    $controller_name = substr(trim(substr($value, 0,$pos -1)), 1);
                    $brc = substr($value, $pos + 1);
                    $brc = substr($brc,0, -1);
                    $annotation_list [$controller_name] = $this->get_annotations_from_string($brc);
                }
                return $annotation_list;
            }
        } catch (ReflectionException $e) {
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
