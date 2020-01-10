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
        $controllers_location = __SPECIFICATION_APP_LOCATION__ . '/' . __DEFAULT_CONTROLLERS_PATH__ . '/*.php';
        $controllers = glob($controllers_location);
        foreach ($controllers as $key => $controller){
            require_once $controller;
            $classes = get_declared_classes();
            $class = end($classes);
            $this->get_class_meta($class);
//            echo $class;
        }
    }
    public function build_pages(){
        $pages_location = $this->_routers['PageRouters']['BaseDir'];
        $m = regex_dir_search($pages_location , '/.*\.php/');
        $this->_routers['PageRouters']['Pages'] =
            $this->get_only_relative_pages_locations($m , $pages_location);
    }

    private function get_class_meta($class){
        try {
            $r = new ReflectionClass($class);
            $doc = $r->getDocComment();
            echo $doc . ' <br>';
            preg_match_all('#@(.*?)\n#s', $doc, $annotations);
//            print_r($annotations[1][1]);
            return $annotations[1];
        } catch (ReflectionException $e) {
        }
        return '';
    }

    private function get_only_relative_pages_locations(array $pages_array, string $base) : array {
        $pages = [];
        $l = strlen($base);
        foreach ($pages_array as $key => $page){
            $pages[] = str_replace(array('.php', '\\'), array('', '/'), substr($page, $l + 1));
        }
        return $pages;
    }
}
