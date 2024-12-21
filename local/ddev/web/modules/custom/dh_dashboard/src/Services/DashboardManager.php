<?php

/**
 * @file
 * Contains \Drupal\dh_dashboard\Services\DashboardManager.
 *
 * PHP version 7.4
 *
 * @category Services
 * @package  UVA_DH_Dashboard
 * @author   Your Name <your.email@example.com>
 * @license  http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 * @link     http://example.com
 */
namespace Drupal\dh_dashboard\Services;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\profile\Entity\Profile;
use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * Class DashboardManager.
 *
 * Provides functionality to manage user dashboards and certificate progress.
 *
 * @category Services
 * @package  UVA_DH_Dashboard
 * @author   Your Name <your.email@example.com>
 * @license  http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 * @link     http://example.com
 */
class DashboardManager
{
    protected $entityTypeManager;
    
    public function __construct(
        protected AccountProxyInterface $currentUser,
        protected PluginManagerInterface $requirementManager,
        EntityTypeManagerInterface $entityTypeManager
    ) {
        $this->entityTypeManager = $entityTypeManager;
    }

    public function getUserDashboard($account)
    {
        return 'dh_certificate';
    }

    public function getDHCertificateProgress($account)
    {
        // Just return mock data directly for now
        return $this->getMockProgress();
    }

    protected function populateProfileWithMockData(Profile $profile) {
        $mockCourses = [
            [
                'number' => 'DH 5000',
                'name' => 'Introduction to Digital Humanities',
                'completed' => true,
                'semester' => 'Fall 2023'
            ],
            [
                'number' => 'DH 5100',
                'name' => 'Digital Research Methods',
                'completed' => false,
                'semester' => 'Spring 2024'
            ],
            [
                'number' => 'DH 5200',
                'name' => 'Data Analysis for Humanities',
                'completed' => false,
                'semester' => ''
            ]
        ];

        $mockGeneralRequirements = [
            [
                'name' => 'Digital Project',
                'completed' => true
            ],
            [
                'name' => 'Workshop Attendance',
                'completed' => true
            ],
            [
                'name' => 'Portfolio Submission',
                'completed' => false
            ]
        ];

        if ($profile->hasField('field_dh_courses')) {
            $profile->set('field_dh_courses', $mockCourses);
        }
        
        if ($profile->hasField('field_general_requirements')) {
            $profile->set('field_general_requirements', $mockGeneralRequirements);
        }
        
        $profile->save();
        
        return $profile;
    }

    protected function getDefaultProgress() {
        return [
            'total_completed' => 0,
            'total_requirements' => 6,
            'courses' => [],
            'general' => []
        ];
    }

    protected function getProfileCourses($profile) {
        $courses = [];
        if ($profile->hasField('field_dh_courses')) {
            // ...process course data from profile...
        }
        return $courses;
    }

    protected function getProfileGeneralRequirements($profile) {
        $requirements = [];
        if ($profile->hasField('field_general_requirements')) {
            // ...process general requirements data from profile...
        }
        return $requirements;
    }

    public function checkCourseCompletion($course_number) {
        $progress = $this->getMockProgress();
        foreach ($progress['courses'] as $course) {
            if ($course['number'] === $course_number) {
                return $course['completed'];
            }
        }
        return false;
    }

    public function checkGeneralRequirements($requirement_name) {
        $progress = $this->getMockProgress();
        foreach ($progress['general'] as $requirement) {
            if ($requirement['name'] === $requirement_name) {
                return $requirement['completed'];
            }
        }
        return false;
    }

    public function getMockProgress() {
        return [
            'total_completed' => 3,
            'total_requirements' => 6,
            'courses' => [
                [
                    'number' => 'DH 5000',
                    'name' => 'Introduction to Digital Humanities',
                    'completed' => true,
                    'semester' => 'Fall 2023'
                ],
                [
                    'number' => 'DH 5100',
                    'name' => 'Digital Research Methods',
                    'completed' => false,
                    'semester' => 'Spring 2024'
                ],
                [
                    'number' => 'DH 5200',
                    'name' => 'Data Analysis for Humanities',
                    'completed' => false,
                    'semester' => ''
                ]
            ],
            'general' => [
                [
                    'name' => 'Digital Project',
                    'completed' => true
                ],
                [
                    'name' => 'Workshop Attendance',
                    'completed' => true
                ],
                [
                    'name' => 'Portfolio Submission',
                    'completed' => false
                ]
            ]
        ];
    }
}
