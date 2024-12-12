<?php
// dh_dashboard/src/Services/DashboardManager.php

namespace Drupal\dh_dashboard\Services;

use Drupal\Core\Entity\EntityTypeManagerInterface;

class DashboardManager
{
    protected $entityTypeManager;

    public function __construct(EntityTypeManagerInterface $entity_type_manager)
    {
        $this->entityTypeManager = $entity_type_manager;
    }

    public function getUserDashboard($account)
    {
        return 'dh_certificate';
    }

    public function getDHCertificateProgress($account)
    {
        $user = $this->entityTypeManager->getStorage('user')->load($account->id());
    
      // Check if the field exists before trying to access it
        if (!$user->hasField('field_dh_requirements')) {
          // Return empty progress structure if field doesn't exist
            return [
            'courses' => [],
            'general' => [],
            'total_completed' => 0,
            'total_requirements' => 0,
            'field_missing' => true,
            ];
        }

        $requirements = $user->get('field_dh_requirements')->referencedEntities();
    
        $progress = [
        'courses' => [],
        'general' => [],
        'total_completed' => 0,
        'total_requirements' => count($requirements),
        'field_missing' => false,
        ];

        foreach ($requirements as $requirement) {
            $type = $requirement->bundle();
            $completed = $requirement->get('field_completed')->value;
      
            if ($completed) {
                $progress['total_completed']++;
            }

            if ($type === 'course_requirement') {
                $progress['courses'][] = [
                'name' => $requirement->get('field_course_name')->value,
                'number' => $requirement->get('field_course_number')->value,
                'semester' => $requirement->get('field_semester_taken')->value,
                'completed' => $completed,
                ];
            } else {
                $progress['general'][] = [
                'name' => $requirement->get('field_requirement_name')->value,
                'completed' => $completed,
                ];
            }
        }

        return $progress;
    }
}
