<?php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\dh_dashboard\Services\DashboardManager;

/**
 * Provides a block displaying certificate progress.
 *
 * @category DH_Dashboard
 * @package  DH_Dashboard
 * @author   Yuji Shinozaki <yuji@virginia.edu>
 * @license  https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link     https://www.drupal.org/project/dh_dashboard
 *
 * @Block(
 *   id = "certificate_progress_block",
 *   admin_label = @Translation("Certificate Progress"),
 *   category = @Translation("DH Dashboard")
 * )
 */
class CertificateProgressBlock extends BlockBase implements ContainerFactoryPluginInterface
{

    /**
     * Dashboard manager service.
     *
     * @var \Drupal\dh_dashboard\Services\DashboardManager
     */
    protected $dashboardManager;

    /**
     * Constructs a new CertificateProgressBlock instance.
     *
     * @param array                                           $configuration
     *   A configuration array containing information about the plugin instance.
     *   A configuration array containing information about the plugin instance.
     * @param string $plugin_id
     *   The plugin ID for the plugin instance.
     * @param mixed $plugin_definition
     *   The plugin implementation definition.
     * @param \Drupal\dh_dashboard\Services\DashboardManager $dashboard_manager
     *   The dashboard manager service.
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition, DashboardManager $dashboard_manager)
    {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->dashboardManager = $dashboard_manager;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
    {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('dh_dashboard.manager')
        );
    }

    public function build()
    {
        $route_match = \Drupal::routeMatch();
        $user = $route_match->getParameter('user');
        
        if (!$user) {
            $user = \Drupal::currentUser();
        }

        $progress = $this->dashboardManager->getDHCertificateProgress($user);

        return [
            '#theme' => 'certificate_progress',
            '#progress' => $progress,
            '#attached' => [
                'library' => ['dh_dashboard/certificate-progress'],
            ],
            '#cache' => [
                'max-age' => 0, // Temporarily disable caching for debugging
                'contexts' => ['user'],
                'tags' => ['user:' . $user->id()],
            ],
        ];
    }
}
