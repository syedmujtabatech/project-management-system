<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\Milestone;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TimeLog;
use App\Models\ActivityLog;
use App\Models\CompanySetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Company Settings
        CompanySetting::create([
            'company_name' => 'ApexPMS Enterprise Technologies',
            'tagline' => 'Enterprise Project Orchestration & Agile Delivery Platform',
            'company_email' => 'operations@apexcompany.com',
            'company_phone' => '+1 (800) 420-APEX',
            'website' => 'https://apexcompany.com',
            'address' => 'One World Trade Center, Floor 64, New York, NY 10007',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'timezone' => 'America/New_York',
        ]);

        // 2. Users
        $admin = User::create([
            'name' => 'Sarah Jenkins',
            'email' => 'admin@apexcompany.com',
            'password' => Hash::make('admin123'),
            'role' => 'super_admin',
            'phone' => '+1 (212) 555-0199',
            'job_title' => 'Director of Engineering & Product',
            'department' => 'Executive Management',
            'is_active' => true,
        ]);

        $pm = User::create([
            'name' => 'Alex Rivera',
            'email' => 'alex.pm@apexcompany.com',
            'password' => Hash::make('password123'),
            'role' => 'project_manager',
            'phone' => '+1 (212) 555-0144',
            'job_title' => 'Lead Agile Project Manager',
            'department' => 'Project Management Office',
            'is_active' => true,
        ]);

        $devSenior = User::create([
            'name' => 'David Kim',
            'email' => 'david.dev@apexcompany.com',
            'password' => Hash::make('password123'),
            'role' => 'team_member',
            'phone' => '+1 (212) 555-0182',
            'job_title' => 'Principal Backend Architect',
            'department' => 'Core Engineering',
            'is_active' => true,
        ]);

        $devFrontend = User::create([
            'name' => 'Elena Rostova',
            'email' => 'elena.ui@apexcompany.com',
            'password' => Hash::make('password123'),
            'role' => 'team_member',
            'phone' => '+1 (212) 555-0173',
            'job_title' => 'Senior Frontend & UX Engineer',
            'department' => 'Product Design & UI',
            'is_active' => true,
        ]);

        $qa = User::create([
            'name' => 'Omar Farooq',
            'email' => 'omar.qa@apexcompany.com',
            'password' => Hash::make('password123'),
            'role' => 'team_member',
            'phone' => '+1 (212) 555-0129',
            'job_title' => 'Lead QA Automation Engineer',
            'department' => 'Quality Assurance',
            'is_active' => true,
        ]);

        $devOps = User::create([
            'name' => 'Chloe Bennett',
            'email' => 'chloe.ops@apexcompany.com',
            'password' => Hash::make('password123'),
            'role' => 'team_member',
            'phone' => '+1 (212) 555-0165',
            'job_title' => 'Cloud & DevOps Architect',
            'department' => 'Cloud Infrastructure',
            'is_active' => true,
        ]);

        $clientUser = User::create([
            'name' => 'Marcus Vance',
            'email' => 'client@finovatech.com',
            'password' => Hash::make('password123'),
            'role' => 'client',
            'phone' => '+44 20 7946 0912',
            'job_title' => 'Chief Technology Officer',
            'department' => 'Client Stakeholder',
            'is_active' => true,
        ]);

        // 3. Clients
        $client1 = Client::create([
            'company_name' => 'Finova Global Ltd',
            'contact_person' => 'Marcus Vance',
            'email' => 'marcus.vance@finovatech.com',
            'phone' => '+44 20 7946 0912',
            'website' => 'https://finovatech.com',
            'address' => '25 Bank Street, Canary Wharf',
            'city' => 'London',
            'country' => 'United Kingdom',
            'status' => 'active',
            'notes' => 'Tier 1 Fintech Enterprise Client. High compliance and PCI-DSS requirements.',
        ]);

        $client2 = Client::create([
            'company_name' => 'Aura Health Systems',
            'contact_person' => 'Dr. Rebecca Hastings',
            'email' => 'rhastings@aurahealth.org',
            'phone' => '+1 (617) 555-8831',
            'website' => 'https://aurahealth.org',
            'address' => '500 Boylston St, Suite 1200',
            'city' => 'Boston',
            'country' => 'United States',
            'status' => 'active',
            'notes' => 'HIPAA-compliant telemedicine platforms and patient record sync.',
        ]);

        $client3 = Client::create([
            'company_name' => 'Vanguard Logistics Network',
            'contact_person' => 'Tariq Mansour',
            'email' => 'tmansour@vanguardlog.ae',
            'phone' => '+971 4 391 1111',
            'website' => 'https://vanguardlog.ae',
            'address' => 'Sheikh Zayed Road, DIFC Tower 3',
            'city' => 'Dubai',
            'country' => 'United Arab Emirates',
            'status' => 'active',
            'notes' => 'Cross-border smart freight forwarding and route optimization.',
        ]);

        $client4 = Client::create([
            'company_name' => 'Nexus Retail Omnichannel',
            'contact_person' => 'Wei Chen',
            'email' => 'w.chen@nexusretail.sg',
            'phone' => '+65 6789 0123',
            'website' => 'https://nexusretail.sg',
            'address' => '10 Marina Boulevard, Tower 2',
            'city' => 'Singapore',
            'country' => 'Singapore',
            'status' => 'active',
            'notes' => 'Enterprise POS, inventory sync across 140 flagship retail stores.',
        ]);

        // 4. Projects
        $prj1 = Project::create([
            'code' => 'PRJ-2026-001',
            'title' => 'Fintech Core Payment Gateway 2.0',
            'description' => 'Multi-currency settlement engine with instant SEPA, ACH, and SWIFT rail integration. ISO 20022 messaging compliance and fraud detection AI pipeline.',
            'client_id' => $client1->id,
            'manager_id' => $pm->id,
            'category' => 'Financial Technology',
            'priority' => 'urgent',
            'status' => 'in_progress',
            'start_date' => Carbon::now()->subDays(45),
            'due_date' => Carbon::now()->addDays(30),
            'budget' => 95000.00,
            'currency' => 'USD',
            'progress' => 65,
        ]);
        $prj1->members()->attach([
            $devSenior->id => ['project_role' => 'Lead Architect'],
            $devFrontend->id => ['project_role' => 'Frontend UI Engineer'],
            $qa->id => ['project_role' => 'QA Lead'],
            $devOps->id => ['project_role' => 'DevOps Specialist'],
        ]);

        $prj2 = Project::create([
            'code' => 'PRJ-2026-002',
            'title' => 'Aura Telehealth Suite & EHR Integration',
            'description' => 'HIPAA-compliant web & iOS/Android video consultation portal with HL7 FHIR electronic medical records synchronization and automated e-prescriptions.',
            'client_id' => $client2->id,
            'manager_id' => $pm->id,
            'category' => 'Healthcare & MedTech',
            'priority' => 'high',
            'status' => 'in_progress',
            'start_date' => Carbon::now()->subDays(20),
            'due_date' => Carbon::now()->addDays(55),
            'budget' => 125000.00,
            'currency' => 'USD',
            'progress' => 40,
        ]);
        $prj2->members()->attach([
            $devSenior->id => ['project_role' => 'Backend Engineer'],
            $devFrontend->id => ['project_role' => 'Lead UI Engineer'],
            $qa->id => ['project_role' => 'Security & Compliance QA'],
        ]);

        $prj3 = Project::create([
            'code' => 'PRJ-2026-003',
            'title' => 'Autonomous Supply Chain Freight AI',
            'description' => 'Predictive ETA algorithm, vessel tracking, container load balancing and carbon offset reporting dashboard for maritime transport.',
            'client_id' => $client3->id,
            'manager_id' => $admin->id,
            'category' => 'Supply Chain & AI',
            'priority' => 'medium',
            'status' => 'planning',
            'start_date' => Carbon::now()->subDays(5),
            'due_date' => Carbon::now()->addDays(90),
            'budget' => 70000.00,
            'currency' => 'USD',
            'progress' => 15,
        ]);
        $prj3->members()->attach([
            $devSenior->id => ['project_role' => 'Data Engineer'],
            $devOps->id => ['project_role' => 'Cloud Architect'],
        ]);

        $prj4 = Project::create([
            'code' => 'PRJ-2026-004',
            'title' => 'Nexus Omnichannel POS & Warehouse Cloud',
            'description' => 'Centralized cloud ERP connector for real-time inventory updates across physical brick-and-mortar stores and Shopify Plus storefronts.',
            'client_id' => $client4->id,
            'manager_id' => $pm->id,
            'category' => 'Retail & E-Commerce',
            'priority' => 'high',
            'status' => 'completed',
            'start_date' => Carbon::now()->subDays(90),
            'due_date' => Carbon::now()->subDays(5),
            'completed_at' => Carbon::now()->subDays(5),
            'budget' => 110000.00,
            'currency' => 'USD',
            'progress' => 100,
        ]);
        $prj4->members()->attach([
            $devSenior->id => ['project_role' => 'ERP Specialist'],
            $devFrontend->id => ['project_role' => 'UI Engineer'],
            $qa->id => ['project_role' => 'Performance Tester'],
        ]);

        $prj5 = Project::create([
            'code' => 'PRJ-2026-005',
            'title' => 'Corporate Zero-Trust DevSecOps Migration',
            'description' => 'Kubernetes cluster hardening, HashiCorp Vault secrets rotation, automated SAST/DAST CI/CD security gating and SOC 2 Type II audit readiness.',
            'client_id' => $client1->id,
            'manager_id' => $admin->id,
            'category' => 'Cloud Infrastructure & Security',
            'priority' => 'high',
            'status' => 'in_progress',
            'start_date' => Carbon::now()->subDays(30),
            'due_date' => Carbon::now()->addDays(15),
            'budget' => 50000.00,
            'currency' => 'USD',
            'progress' => 80,
        ]);
        $prj5->members()->attach([
            $devOps->id => ['project_role' => 'Lead Security Architect'],
            $qa->id => ['project_role' => 'Audit Tester'],
        ]);

        // 5. Milestones for Project 1 (Fintech)
        $m1 = Milestone::create([
            'project_id' => $prj1->id,
            'title' => 'Milestone 1: Architectural Design & API Specs',
            'description' => 'OpenAPI 3.1 specification, idempotency key mechanisms and database schema review.',
            'due_date' => Carbon::now()->subDays(25),
            'status' => 'achieved',
        ]);

        $m2 = Milestone::create([
            'project_id' => $prj1->id,
            'title' => 'Milestone 2: Multi-Currency Settlement Engine',
            'description' => 'Real-time ledger entries, transaction rollback mechanisms and webhook callbacks.',
            'due_date' => Carbon::now()->subDays(5),
            'status' => 'achieved',
        ]);

        $m3 = Milestone::create([
            'project_id' => $prj1->id,
            'title' => 'Milestone 3: Sandbox SDK & Developer Portal',
            'description' => 'Interactive Swagger UI, TypeScript/PHP SDKs, and mock transaction generator.',
            'due_date' => Carbon::now()->addDays(15),
            'status' => 'in_progress',
        ]);

        $m4 = Milestone::create([
            'project_id' => $prj1->id,
            'title' => 'Milestone 4: Production Penetration Test & Launch',
            'description' => 'External CREST-certified penetration testing and blue/green production deployment.',
            'due_date' => Carbon::now()->addDays(30),
            'status' => 'pending',
        ]);

        // Milestones for Project 2 (Telehealth)
        $m2_1 = Milestone::create([
            'project_id' => $prj2->id,
            'title' => 'Milestone 1: HIPAA Compliance & E2E Video Encryption',
            'description' => 'WebRTC media server architecture with AES-256 peer-to-peer encryption.',
            'due_date' => Carbon::now()->subDays(5),
            'status' => 'achieved',
        ]);

        $m2_2 = Milestone::create([
            'project_id' => $prj2->id,
            'title' => 'Milestone 2: FHIR Patient Record Synchronization',
            'description' => 'Bi-directional medical history integration with Epic and Cerner health systems.',
            'due_date' => Carbon::now()->addDays(20),
            'status' => 'in_progress',
        ]);

        // 6. Tasks (Covering all 5 Kanban stages)
        $tasksData = [
            // Done tasks
            [
                'task_number' => 'TSK-101',
                'project_id' => $prj1->id,
                'milestone_id' => $m1->id,
                'assigned_to' => $devSenior->id,
                'created_by' => $pm->id,
                'title' => 'Design Double-Entry Accounting Ledger Schema',
                'description' => 'Ensure database transactions are atomized, immutable and cryptographically audited.',
                'priority' => 'high',
                'status' => 'done',
                'estimated_hours' => 24.0,
                'logged_hours' => 26.5,
                'start_date' => Carbon::now()->subDays(40),
                'due_date' => Carbon::now()->subDays(28),
                'completed_at' => Carbon::now()->subDays(29),
                'order_column' => 1,
            ],
            [
                'task_number' => 'TSK-102',
                'project_id' => $prj1->id,
                'milestone_id' => $m1->id,
                'assigned_to' => $devFrontend->id,
                'created_by' => $pm->id,
                'title' => 'Wireframe & High-Fidelity Merchant Dashboard',
                'description' => 'Create pixel-perfect Figma designs for merchant payout settings and statement download.',
                'priority' => 'medium',
                'status' => 'done',
                'estimated_hours' => 16.0,
                'logged_hours' => 15.0,
                'start_date' => Carbon::now()->subDays(35),
                'due_date' => Carbon::now()->subDays(25),
                'completed_at' => Carbon::now()->subDays(26),
                'order_column' => 2,
            ],
            [
                'task_number' => 'TSK-103',
                'project_id' => $prj1->id,
                'milestone_id' => $m2->id,
                'assigned_to' => $devSenior->id,
                'created_by' => $pm->id,
                'title' => 'Implement SEPA Instant & Swift MT103 Parser',
                'description' => 'Parse ISO 20022 XML messages and handle asynchronous clearing house responses.',
                'priority' => 'urgent',
                'status' => 'done',
                'estimated_hours' => 32.0,
                'logged_hours' => 30.0,
                'start_date' => Carbon::now()->subDays(25),
                'due_date' => Carbon::now()->subDays(8),
                'completed_at' => Carbon::now()->subDays(7),
                'order_column' => 3,
            ],

            // In Review tasks
            [
                'task_number' => 'TSK-104',
                'project_id' => $prj1->id,
                'milestone_id' => $m2->id,
                'assigned_to' => $qa->id,
                'created_by' => $pm->id,
                'title' => 'Automated Load Testing for Settlement Engine (10k TPS)',
                'description' => 'Execute JMeter and k6 distributed stress testing on staging clusters to verify throughput under load.',
                'priority' => 'high',
                'status' => 'in_review',
                'estimated_hours' => 20.0,
                'logged_hours' => 18.5,
                'start_date' => Carbon::now()->subDays(10),
                'due_date' => Carbon::now()->addDays(2),
                'completed_at' => null,
                'order_column' => 1,
            ],
            [
                'task_number' => 'TSK-105',
                'project_id' => $prj2->id,
                'milestone_id' => $m2_1->id,
                'assigned_to' => $devFrontend->id,
                'created_by' => $pm->id,
                'title' => 'WebRTC Video Grid with Waiting Room Queue',
                'description' => 'Doctor-patient private consultation room with audio/video toggle, screen sharing and encrypted chat.',
                'priority' => 'urgent',
                'status' => 'in_review',
                'estimated_hours' => 28.0,
                'logged_hours' => 27.0,
                'start_date' => Carbon::now()->subDays(12),
                'due_date' => Carbon::now()->addDays(1),
                'completed_at' => null,
                'order_column' => 2,
            ],

            // In Progress tasks
            [
                'task_number' => 'TSK-106',
                'project_id' => $prj1->id,
                'milestone_id' => $m3->id,
                'assigned_to' => $devSenior->id,
                'created_by' => $pm->id,
                'title' => 'Implement Real-Time Webhook Notification Dispatcher',
                'description' => 'Build high-reliability Redis queue with exponential backoff retry for merchant transaction alerts.',
                'priority' => 'high',
                'status' => 'in_progress',
                'estimated_hours' => 18.0,
                'logged_hours' => 11.5,
                'start_date' => Carbon::now()->subDays(4),
                'due_date' => Carbon::now()->addDays(5),
                'completed_at' => null,
                'order_column' => 1,
            ],
            [
                'task_number' => 'TSK-107',
                'project_id' => $prj1->id,
                'milestone_id' => $m3->id,
                'assigned_to' => $devFrontend->id,
                'created_by' => $pm->id,
                'title' => 'Developer Sandbox Portal & API Key Management',
                'description' => 'Customer self-service UI for generating test API tokens, simulating refunds, and viewing live payload logs.',
                'priority' => 'medium',
                'status' => 'in_progress',
                'estimated_hours' => 22.0,
                'logged_hours' => 9.0,
                'start_date' => Carbon::now()->subDays(3),
                'due_date' => Carbon::now()->addDays(8),
                'completed_at' => null,
                'order_column' => 2,
            ],
            [
                'task_number' => 'TSK-108',
                'project_id' => $prj2->id,
                'milestone_id' => $m2_2->id,
                'assigned_to' => $devSenior->id,
                'created_by' => $pm->id,
                'title' => 'HL7 FHIR Medical Records Data Transformation',
                'description' => 'Map incoming JSON clinical data models to HL7 standards with field-level encryption for patient confidentiality.',
                'priority' => 'urgent',
                'status' => 'in_progress',
                'estimated_hours' => 30.0,
                'logged_hours' => 14.0,
                'start_date' => Carbon::now()->subDays(5),
                'due_date' => Carbon::now()->addDays(7),
                'completed_at' => null,
                'order_column' => 3,
            ],
            [
                'task_number' => 'TSK-109',
                'project_id' => $prj5->id,
                'milestone_id' => null,
                'assigned_to' => $devOps->id,
                'created_by' => $admin->id,
                'title' => 'Kubernetes Cilium Service Mesh & Mutual TLS (mTLS)',
                'description' => 'Configure microservices zero-trust network policies, certificate auto-rotation and ingress filtering.',
                'priority' => 'high',
                'status' => 'in_progress',
                'estimated_hours' => 24.0,
                'logged_hours' => 16.0,
                'start_date' => Carbon::now()->subDays(6),
                'due_date' => Carbon::now()->addDays(4),
                'completed_at' => null,
                'order_column' => 4,
            ],

            // To Do tasks
            [
                'task_number' => 'TSK-110',
                'project_id' => $prj1->id,
                'milestone_id' => $m4->id,
                'assigned_to' => $devOps->id,
                'created_by' => $pm->id,
                'title' => 'Configure Blue/Green Production Deployment Pipeline',
                'description' => 'Set up automated health checks and traffic routing with zero downtime cutover via AWS Route53 & ALB.',
                'priority' => 'high',
                'status' => 'todo',
                'estimated_hours' => 16.0,
                'logged_hours' => 0.0,
                'start_date' => Carbon::now()->addDays(2),
                'due_date' => Carbon::now()->addDays(14),
                'completed_at' => null,
                'order_column' => 1,
            ],
            [
                'task_number' => 'TSK-111',
                'project_id' => $prj2->id,
                'milestone_id' => $m2_2->id,
                'assigned_to' => $qa->id,
                'created_by' => $pm->id,
                'title' => 'HIPAA Audit Trail Verification & Logging Test',
                'description' => 'Verify audit trail captures all unauthorized access attempts and patient record views with tamper-evident logs.',
                'priority' => 'urgent',
                'status' => 'todo',
                'estimated_hours' => 12.0,
                'logged_hours' => 0.0,
                'start_date' => Carbon::now()->addDays(4),
                'due_date' => Carbon::now()->addDays(16),
                'completed_at' => null,
                'order_column' => 2,
            ],
            [
                'task_number' => 'TSK-112',
                'project_id' => $prj3->id,
                'milestone_id' => null,
                'assigned_to' => $devSenior->id,
                'created_by' => $admin->id,
                'title' => 'Satellite AIS Vessel Tracking Feed Integration',
                'description' => 'Ingest global vessel positional data stream via WebSocket and store timeseries coordinates in TimescaleDB.',
                'priority' => 'medium',
                'status' => 'todo',
                'estimated_hours' => 35.0,
                'logged_hours' => 0.0,
                'start_date' => Carbon::now()->addDays(3),
                'due_date' => Carbon::now()->addDays(21),
                'completed_at' => null,
                'order_column' => 3,
            ],

            // Backlog tasks
            [
                'task_number' => 'TSK-113',
                'project_id' => $prj1->id,
                'milestone_id' => $m4->id,
                'assigned_to' => null,
                'created_by' => $pm->id,
                'title' => 'AI Anomaly Detection for Card Fraud Patterns',
                'description' => 'Implement machine learning scoring model to flag suspicious velocity spikes and geographical anomalies.',
                'priority' => 'low',
                'status' => 'backlog',
                'estimated_hours' => 40.0,
                'logged_hours' => 0.0,
                'start_date' => null,
                'due_date' => Carbon::now()->addDays(45),
                'completed_at' => null,
                'order_column' => 1,
            ],
            [
                'task_number' => 'TSK-114',
                'project_id' => $prj2->id,
                'milestone_id' => null,
                'assigned_to' => null,
                'created_by' => $pm->id,
                'title' => 'Apple HealthKit & Google Fit Wearable Sync',
                'description' => 'Allow patients to securely share continuous heart rate, blood pressure and glucose readings with physicians.',
                'priority' => 'low',
                'status' => 'backlog',
                'estimated_hours' => 28.0,
                'logged_hours' => 0.0,
                'start_date' => null,
                'due_date' => Carbon::now()->addDays(60),
                'completed_at' => null,
                'order_column' => 2,
            ],
            [
                'task_number' => 'TSK-115',
                'project_id' => $prj3->id,
                'milestone_id' => null,
                'assigned_to' => null,
                'created_by' => $admin->id,
                'title' => 'Automated Carbon Offset Certificate Issuance',
                'description' => 'Smart contract verification for green ocean freight eco-credits.',
                'priority' => 'low',
                'status' => 'backlog',
                'estimated_hours' => 20.0,
                'logged_hours' => 0.0,
                'start_date' => null,
                'due_date' => Carbon::now()->addDays(75),
                'completed_at' => null,
                'order_column' => 3,
            ],
        ];

        $createdTasks = [];
        foreach ($tasksData as $tData) {
            $createdTasks[] = Task::create($tData);
        }

        // Recalculate progress for projects
        $prj1->recalculateProgress();
        $prj2->recalculateProgress();
        $prj3->recalculateProgress();
        $prj5->recalculateProgress();

        // 7. Time Logs
        TimeLog::create([
            'project_id' => $prj1->id,
            'task_id' => $createdTasks[0]->id,
            'user_id' => $devSenior->id,
            'hours' => 8.0,
            'log_date' => Carbon::now()->subDays(30),
            'notes' => 'Implemented transactional locking for ledger entries and audited isolation levels.',
        ]);

        TimeLog::create([
            'project_id' => $prj1->id,
            'task_id' => $createdTasks[0]->id,
            'user_id' => $devSenior->id,
            'hours' => 6.5,
            'log_date' => Carbon::now()->subDays(29),
            'notes' => 'Completed unit tests and code review with lead auditor.',
        ]);

        TimeLog::create([
            'project_id' => $prj1->id,
            'task_id' => $createdTasks[5]->id, // TSK-106 Webhooks
            'user_id' => $devSenior->id,
            'hours' => 5.5,
            'log_date' => Carbon::now()->subDays(2),
            'notes' => 'Wrote Redis queue worker and signature verification helper with HMAC-SHA256.',
        ]);

        TimeLog::create([
            'project_id' => $prj1->id,
            'task_id' => $createdTasks[6]->id, // TSK-107 Sandbox
            'user_id' => $devFrontend->id,
            'hours' => 4.5,
            'log_date' => Carbon::now()->subDays(1),
            'notes' => 'Implemented token generation modal, clipboard copy and live payload inspector.',
        ]);

        TimeLog::create([
            'project_id' => $prj2->id,
            'task_id' => $createdTasks[7]->id, // TSK-108 FHIR
            'user_id' => $devSenior->id,
            'hours' => 7.0,
            'log_date' => Carbon::now()->subDays(2),
            'notes' => 'Configured FHIR R4 schema parser and mapped diagnosis codes to ICD-10.',
        ]);

        TimeLog::create([
            'project_id' => $prj5->id,
            'task_id' => $createdTasks[8]->id, // TSK-109 Cilium
            'user_id' => $devOps->id,
            'hours' => 8.0,
            'log_date' => Carbon::now()->subDays(3),
            'notes' => 'Deployed Cilium CNI on EKS cluster with Hubble UI observability.',
        ]);

        // 8. Task Comments
        TaskComment::create([
            'task_id' => $createdTasks[5]->id,
            'user_id' => $devSenior->id,
            'comment' => 'Initial webhook dispatcher draft is live on staging. HMAC signatures tested against Python and Node client samples.',
        ]);

        TaskComment::create([
            'task_id' => $createdTasks[5]->id,
            'user_id' => $pm->id,
            'comment' => 'Looks great David! Please make sure timeout thresholds are configured to maximum 3 seconds with exponential jitter.',
        ]);

        TaskComment::create([
            'task_id' => $createdTasks[3]->id,
            'user_id' => $qa->id,
            'comment' => 'Stress test reached 8,400 TPS with 99.2% latency under 45ms. Bottleneck discovered on foreign key check in audit log table; optimizing index now.',
        ]);

        // 9. Activity Logs
        ActivityLog::create([
            'user_id' => $admin->id,
            'project_id' => $prj1->id,
            'action' => 'Project Milestone Achieved',
            'description' => 'Sarah approved Milestone 2: Multi-Currency Settlement Engine after passing QA sign-off.',
            'ip_address' => '127.0.0.1',
        ]);

        ActivityLog::create([
            'user_id' => $pm->id,
            'project_id' => $prj1->id,
            'action' => 'Task Moved to In Progress',
            'description' => 'Alex assigned TSK-106 (Webhook Dispatcher) to David Kim.',
            'ip_address' => '127.0.0.1',
        ]);

        ActivityLog::create([
            'user_id' => $devSenior->id,
            'project_id' => $prj1->id,
            'action' => 'Time Logged',
            'description' => 'David logged 5.5 hours on TSK-106 (Webhook Dispatcher).',
            'ip_address' => '127.0.0.1',
        ]);

        ActivityLog::create([
            'user_id' => $devOps->id,
            'project_id' => $prj5->id,
            'action' => 'Security Audit Milestone',
            'description' => 'Chloe Bennett deployed Cilium service mesh mTLS enforcement across all production pods.',
            'ip_address' => '127.0.0.1',
        ]);

        ActivityLog::create([
            'user_id' => $pm->id,
            'project_id' => $prj4->id,
            'action' => 'Project Delivered & Closed',
            'description' => 'Nexus Omnichannel POS & Warehouse Cloud successfully deployed to 140 store locations.',
            'ip_address' => '127.0.0.1',
        ]);
    }
}
