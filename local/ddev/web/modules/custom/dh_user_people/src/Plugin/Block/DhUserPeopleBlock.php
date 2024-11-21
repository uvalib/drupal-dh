<?php

declare(strict_types=1);

namespace Drupal\dh_user_people\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\node\NodeInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\user\UserInterface;

/**
 * Provides a User People block.
 *
 * @Block(
 *   id = "dh_user_people_block",
 *   admin_label = @Translation("DH User People Block"),
 *   category = @Translation("DH Custom"),
 * )
 */
final class DhUserPeopleBlock extends BlockBase implements ContainerFactoryPluginInterface
{

    protected AccountProxyInterface $currentUser;
    protected EntityTypeManagerInterface $entityTypeManager;
    protected LoggerChannelFactoryInterface $loggerFactory;
    protected RouteMatchInterface $routeMatch;

    public function getCacheMaxAge()
    {
        return 0;
    }

    public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        AccountProxyInterface $current_user,
        EntityTypeManagerInterface $entity_type_manager,
        LoggerChannelFactoryInterface $logger_factory,
        RouteMatchInterface $route_match
    ) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->currentUser = $current_user;
        $this->entityTypeManager = $entity_type_manager;
        $this->loggerFactory = $logger_factory;
        $this->routeMatch = $route_match;
    }

    public static function create(
        ContainerInterface $container,
        array $configuration,
        $plugin_id,
        $plugin_definition
    ): self {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('current_user'),
            $container->get('entity_type.manager'),
            $container->get('logger.factory'),
            $container->get('current_route_match')
        );
    }

    public function defaultConfiguration(): array
    {
        return [
            'example' => $this->t('Hello world!'),
        ];
    }

    public function blockForm($form, FormStateInterface $form_state): array
    {
        $form['example'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Example'),
            '#default_value' => $this->configuration['example'],
        ];
        return $form;
    }

    public function blockSubmit($form, FormStateInterface $form_state): void
    {
        $this->configuration['example'] = $form_state->getValue('example');
    }

    /**
     * Gets the user from the current route context.
     */
    protected function getContextUser(): ?UserInterface
    {
        // First try to get user directly from route
        if ($user = $this->routeMatch->getParameter('user')) {
            return $user instanceof UserInterface ? $user : null;
        }

        // Then try user ID
        if ($uid = $this->routeMatch->getParameter('user_id')) {
            try {
                $user = $this->entityTypeManager->getStorage('user')->load($uid);
                return $user instanceof UserInterface ? $user : null;
            } catch (\Exception $e) {
                $this->loggerFactory->get('dh_user_people')
                    ->error('Error loading user @uid: @message', [
                        '@uid' => $uid,
                        '@message' => $e->getMessage()
                    ]);
            }
        }

        return null;
    }

    public function build(): array
    {
        $context_user = $this->getContextUser();
        if (!$context_user) {
            return [
                '#markup' => $this->t('No user found in context.'),
            ];
        }

        $people_node = $this->getPeopleNode($context_user);
    
        if (!$people_node) {
            return [
                '#markup' => $this->t(
                    'No associated People node found for user @uid.',
                    ['@uid' => $context_user->id()]
                ),
            ];
        }
    
        $build = $this->entityTypeManager->getViewBuilder('node')->view($people_node, 'full');

            // Check if user has permission to edit this node
        if ($people_node->access('update')) {
            $build['edit_link'] = [
            '#type' => 'link',
            '#title' => $this->t('Edit'),
            '#url' => $people_node->toUrl('edit-form'),
            '#attributes' => [
                'class' => ['button', 'button--primary'],
            ],
            // Optionally add some styling wrapper
            '#prefix' => '<div class="people-edit-link">',
            '#suffix' => '</div>',
            // Make sure link appears after content
            '#weight' => 100,
            ];
        }

        return $build;
    }

    protected function getPeopleNode(UserInterface $user): ?NodeInterface
    {
        try {
            $node_storage = $this->entityTypeManager->getStorage('node');
      
            $query = $node_storage->getQuery()
                ->accessCheck(false)
                ->condition('type', 'people')
                ->condition('field_user', $user->id())
                ->range(0, 1);
        
            $nids = $query->execute();
      
            if (empty($nids)) {
                return null;
            }
      
            $nid = reset($nids);
            $node = $node_storage->load($nid);

            if ($node) {
                \Drupal::messenger()->addMessage('Node ID: ' . $node->id() . ' Bundle: ' . $node->bundle());
            } else {
                \Drupal::messenger()->addMessage('Node not found for ' . $user->id());
            }

            return $node;
        } catch (\Exception $e) {
            $this->loggerFactory->get('dh_user_people')
                ->error('Error loading people node: @message', ['@message' => $e->getMessage()]);
            return null;
        }
    }
}
