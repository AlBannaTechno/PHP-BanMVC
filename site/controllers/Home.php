<?php
include_once __SPECIFICATION_APP_LOCATION__ . '/' . 'models/AboutModel.php';

/**
 * Class Home
 * @Controller(urlName="main/Home")
 * @ZontrolXr(urlName= "main/Home" , area = "osama")
 * @Rx()
 */
class Home extends ControllerBase
{
    private $_container;
    public function __construct(Container $container)
    {
        parent::__construct();
        $this->title = 'Home';
        $this->_container = $container;
    }

    public function index(){
        $this->setActionTitle('Index');
        $this->view(new class(){
            public $message  = "Index Here";
        });
    }

    // we may need to set default values for action parameters otherwise , php will show error
    // if passed arguments [url parameters] count is < action[function] parameters count
    // we must prevent invoke Dependencies from actions/method ,
    // in MVC we should only allow this behaviour from constructor
    /**
     * @param $id
     * @param $name
     * @param User $user
     * @HttpGet(slug="collect/{id}")
     * @HttpBodyParam(name="user")
     */
    public function about($id,User $user){
        $aboutModel = $this->_container->resolve(AboutModel::class, [
            'id' => $id, 'name' => ''
        ]);
        $this->view($aboutModel);
    }

}
