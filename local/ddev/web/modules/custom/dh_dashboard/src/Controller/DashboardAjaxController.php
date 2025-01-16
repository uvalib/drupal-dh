<?php

namespace Drupal\dh_dashboard\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Access\CsrfTokenGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Core\Render\RendererInterface;

class DashboardAjaxController extends ControllerBase {

  protected $csrfToken;
  protected $logger;
  protected $blockManager;
  protected $renderer;

  public function __construct(
    CsrfTokenGenerator $csrf_token,
    LoggerChannelFactoryInterface $logger_factory,
    BlockManagerInterface $block_manager,
    RendererInterface $renderer
  ) {
    $this->csrfToken = $csrf_token;
    $this->logger = $logger_factory->get('dh_dashboard');
    $this->blockManager = $block_manager;
    $this->renderer = $renderer;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('csrf_token'),
      $container->get('logger.factory'),
      $container->get('plugin.manager.block'),
      $container->get('renderer')
    );
  }

  public function eventsContent(Request $request) {
    $block_id = $request->query->get('block_id');
    
    if (!$block_id) {
      return new JsonResponse(
        ['error' => 'Missing block_id parameter'],
        Response::HTTP_BAD_REQUEST
      );
    }

    $page = (int) $request->query->get('page', 0);
    return $this->handleContent($request, $block_id, $page);
  }

  public function newsContent(Request $request) {
    $block_id = $request->query->get('block_id');
    
    if (!$block_id) {
      return new JsonResponse(
        ['error' => 'Missing block_id parameter'],
        Response::HTTP_BAD_REQUEST
      );
    }

    $page = (int) $request->query->get('page', 0);
    return $this->handleContent($request, $block_id, $page);
  }

  protected function handleContent(Request $request, string $block_id, int $page = 0) {
    try {
      $block_id = $request->query->get('block_id');
      $page = (int) $request->query->get('page', 0);
      
      $this->logger->debug('Received AJAX request: @params', [
        '@params' => print_r($request->query->all(), TRUE)
      ]);
      
      // Clean up block ID
      $original_block_id = $block_id;
      $block_id = preg_replace('/^block[_-]/', '', $block_id);
      $block_id = str_replace('-', '_', $block_id);

      $this->logger->debug('Processing block ID: @original -> @processed', [
        '@original' => $original_block_id,
        '@processed' => $block_id
      ]);

      if (!$block_id) {
        throw new \Exception('Missing block_id parameter');
      }

      // Debug info
      $debug = [
        'original_block_id' => $original_block_id,
        'processed_block_id' => $block_id,
        'page' => $page,
        'request_params' => $request->query->all(),
      ];

      // If no specific instance ID, use the default block plugin ID
      if (strpos($block_id, 'dh_dashboard_') === 0) {
        $this->logger->debug('Creating block plugin instance: @id', ['@id' => $block_id]);
        try {
          // Use BlockManager to create plugin instance
          $plugin = $this->blockManager->createInstance($block_id, []);
        }
        catch (\Exception $e) {
          $this->logger->error('Failed to create block plugin: @error', [
            '@error' => $e->getMessage()
          ]);
          throw $e;
        }
      } else {
        // Load the block entity
        $this->logger->debug('Loading block instance: @id', ['@id' => $block_id]);
        $block = $this->entityTypeManager()
          ->getStorage('block')
          ->load($block_id);

        if (!$block) {
          $this->logger->error('Block not found: @id', ['@id' => $block_id]);
          throw new \Exception("Block not found: $block_id");
        }

        $plugin = $block->getPlugin();
      }

      $debug['block_found'] = true;
      $this->logger->debug('Block plugin loaded successfully');

      // Get block plugin and build content
      $build = $plugin->build();
      $debug['build_array'] = array_keys($build);
      
      $this->logger->debug('Build array keys: @keys', [
        '@keys' => implode(', ', array_keys($build))
      ]);

      // Render the content
      $content = $this->renderer->renderRoot($build);
      $content_length = strlen($content->__toString());
      $debug['content_length'] = $content_length;
      
      $this->logger->info('Content rendered successfully: @length bytes', [
        '@length' => $content_length
      ]);

      return new JsonResponse([
        'content' => $content->__toString(),
        'debug' => $debug
      ]);
    }
    catch (\Exception $e) {
      $this->logger->error('AJAX error: @message', [
        '@message' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
        'request' => $request->query->all()
      ]);
      
      return new JsonResponse([
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
        'debug' => $debug ?? []
      ], 500);
    }
  }
}
