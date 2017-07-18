<?php
namespace AlbumTest\Controller;

use Album\Controller\AlbumController;
use Album\Model\AlbumTable;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Album\Model\Album;
use Prophecy\Argument;

/**
 * Class AlbumControllerTest
 *
 * @package AlbumTest\Controller
 */
class AlbumControllerTest extends AbstractHttpControllerTestCase
{
    /**
     * @var bool
     */
    protected $traceError = false;

    /**
     * @var
     */
    protected $albumTable;

    /**
     *
     */
    public function setUp()
    {
        $configOverrides = [];

        $this->setApplicationConfig(ArrayUtils::merge(
        // Grabbing the full application configuration:
            include __DIR__ . '/../../../../config/application.config.php',
            $configOverrides
         ));

        parent::setUp();

        $this->configureServiceManager($this->getApplicationServiceLocator());
    }

    /**
     * To Test Index Action Can Be Accessed
     */
    public function testIndexActionCanBeAccessed()
    {
        $this->albumTable->fetchAll()->willReturn([]);

        $this->dispatch('/album');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName(AlbumController::class);
        $this->assertControllerClass('AlbumController');
        $this->assertMatchedRouteName('album');
    }

    /**
     * @param ServiceManager $services
     */
    protected function configureServiceManager(ServiceManager $services)
    {
        $services->setAllowOverride(true);

        $services->setService('config', $this->updateConfig($services->get('config')));
        $services->setService(AlbumTable::class, $this->mockAlbumTable()->reveal());

        $services->setAllowOverride(false);
    }

    /**
     * To Test Add Action Redirects After Valid Post
     */
    public function testAddActionRedirectsAfterValidPost()
    {
        $this->albumTable
            ->saveAlbum(Argument::type(Album::class))
            ->shouldBeCalled();

        $postData = [
            'title'  => 'Led Zeppelin III',
            'artist' => 'Led Zeppelin',
            'id'     => '',
        ];
        $this->dispatch('/album/add', 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/album');
    }

    /**
     * @param $config
     *
     * @return mixed
     */
    protected function updateConfig($config)
    {
        $config['db'] = [];
        return $config;
    }

    /**
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    protected function mockAlbumTable()
    {
        $this->albumTable = $this->prophesize(AlbumTable::class);
        return $this->albumTable;
    }
}