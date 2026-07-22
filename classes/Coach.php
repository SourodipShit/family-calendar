<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/File.php';

class Coach
{
    /**
     * Add a new coach profile along with certifications and plans.
     */
    public static function add($data)
    {
        try {
            Database::getInstance()->beginTransaction();

            // Insert into coach_profiles
            $profileData = $data['profile'];
            $fields = array_keys($profileData);
            $placeholders = array_fill(0, count($fields), '?');
            $params = array_values($profileData);

            $sql = "INSERT INTO coach_profiles (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
            Database::runPrepared($sql, $params);

            $profileId = Database::getInstance()->lastInsertId();

            // user_id is the foreign key reference for coach_id in child tables
            $userId = $profileData['user_id'];

            if (!empty($data['certifications'])) {
                self::addCertifications($userId, $data['certifications']);
            }

            if (!empty($data['plans'])) {
                self::addPlans($userId, $data['plans']);
            }

            Database::getInstance()->commit();
            return ["status" => "success", "id" => $profileId, "user_id" => $userId];
        } catch (Exception $e) {
            Database::getInstance()->rollBack();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Update an existing coach profile and their child records.
     */
    public static function update($id, $data)
    {
        try {
            Database::getInstance()->beginTransaction();

            $profileData = $data['profile'];
            $fields = [];
            $params = [];
            foreach ($profileData as $key => $value) {
                $fields[] = "$key = ?";
                $params[] = $value;
            }
            $params[] = $id;

            $sql = "UPDATE coach_profiles SET " . implode(", ", $fields) . " WHERE id = ?";
            Database::runPrepared($sql, $params);

            // Fetch user_id to update child tables
            $stmt = Database::runPrepared("SELECT user_id FROM coach_profiles WHERE id = ?", [$id]);
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$profile) throw new Exception("Coach profile not found");

            $userId = $profile['user_id'];

            // Replace certifications if provided in the update payload
            if (isset($data['certifications'])) {
                Database::runPrepared("DELETE FROM coach_certifications WHERE coach_id = ?", [$userId]);
                self::addCertifications($userId, $data['certifications']);
            }

            // Replace plans if provided in the update payload
            if (isset($data['plans'])) {
                Database::runPrepared("DELETE FROM coach_plans WHERE coach_id = ?", [$userId]);
                self::addPlans($userId, $data['plans']);
            }

            Database::getInstance()->commit();
            return ["status" => "success", "message" => "Updated successfully"];
        } catch (Exception $e) {
            Database::getInstance()->rollBack();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Helper to add certifications. Handles file uploads using File.php.
     */
    private static function addCertifications($coachId, $certifications)
    {
        foreach ($certifications as $cert) {
            $imagePath = $cert['image'] ?? '';

            // Automatically handle file upload if 'file' array (like $_FILES format) is passed
            if (isset($cert['file']) && is_array($cert['file']) && $cert['file']['error'] === UPLOAD_ERR_OK) {
                $upload = File::upload($cert['file'], 'certifications');
                if ($upload['status'] === 'success') {
                    $imagePath = $upload['filePath'];
                }
            }

            $params = [
                $coachId,
                $cert['name'] ?? '',
                $cert['description'] ?? '',
                $imagePath
            ];
            $sql = "INSERT INTO coach_certifications (coach_id, name, description, image) VALUES (?, ?, ?, ?)";
            Database::runPrepared($sql, $params);
        }
    }

    /**
     * Add a single certification.
     * 
     * @return array
     */
    public static function addSingleCertification($coachId, $cert)
    {
        try {
            $imagePath = '';
            if (isset($cert['file']) && is_array($cert['file']) && $cert['file']['error'] === UPLOAD_ERR_OK) {
                $upload = File::upload($cert['file'], 'certifications');
                if ($upload['status'] === 'success') {
                    $imagePath = $upload['filePath'];
                } else {
                    return ["status" => "error", "message" => $upload['message'] ?? 'Image upload failed.'];
                }
            }

            $params = [
                $coachId,
                $cert['name'] ?? '',
                $cert['description'] ?? '',
                $imagePath
            ];
            $sql = "INSERT INTO coach_certifications (coach_id, name, description, image) VALUES (?, ?, ?, ?)";
            Database::runPrepared($sql, $params);
            
            return ["status" => "success", "message" => "Certification added successfully"];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Update a single certification.
     * 
     * @return array
     */
    public static function updateSingleCertification($certId, $coachId, $cert)
    {
        try {
            $fields = ["name = ?", "description = ?"];
            $params = [$cert['name'] ?? '', $cert['description'] ?? ''];

            if (isset($cert['file']) && is_array($cert['file']) && $cert['file']['error'] === UPLOAD_ERR_OK) {
                $upload = File::upload($cert['file'], 'certifications');
                if ($upload['status'] === 'success') {
                    $fields[] = "image = ?";
                    $params[] = $upload['filePath'];
                } else {
                    return ["status" => "error", "message" => $upload['message'] ?? 'Image upload failed.'];
                }
            }

            // Ensure the cert belongs to the coach
            $params[] = $certId;
            $params[] = $coachId;

            $sql = "UPDATE coach_certifications SET " . implode(", ", $fields) . " WHERE id = ? AND coach_id = ?";
            Database::runPrepared($sql, $params);
            
            return ["status" => "success", "message" => "Certification updated successfully"];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Delete a single certification.
     * 
     * @return array
     */
    public static function deleteSingleCertification($certId, $coachId)
    {
        try {
            $sql = "DELETE FROM coach_certifications WHERE id = ? AND coach_id = ?";
            Database::runPrepared($sql, [$certId, $coachId]);
            return ["status" => "success", "message" => "Certification deleted successfully"];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Helper to add coach plans.
     */
    public static function addPlans($coachId, $plans)
    {
        foreach ($plans as $plan) {
            $plan['coach_id'] = $coachId;
            $fields = array_keys($plan);
            $placeholders = array_fill(0, count($fields), '?');
            $params = array_values($plan);

            $sql = "INSERT INTO coach_plans (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
            Database::runPrepared($sql, $params);
        }
    }

    /**
     * Fetch a coach profile by ID, including user details, certifications, and plans.
     */
    public static function getById($id)
    {
        try {
            $sql = "SELECT cp.*, u.name as user_name, u.email, u.phone, u.image as profile_image, cc.name as category_name
                    FROM coach_profiles cp
                    INNER JOIN users u ON cp.user_id = u.id
                    LEFT JOIN coach_categories cc ON cp.category_id = cc.id
                    WHERE cp.id = ?";

            $stmt = Database::runPrepared($sql, [$id]);
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$profile) return ["status" => "error", "message" => "Coach profile not found."];

            $userId = $profile['user_id'];

            // Certifications
            $certStmt = Database::runPrepared("SELECT * FROM coach_certifications WHERE coach_id = ?", [$userId]);
            $certifications = $certStmt->fetchAll(PDO::FETCH_ASSOC);

            // Plans
            $planStmt = Database::runPrepared("SELECT * FROM coach_plans WHERE coach_id = ?", [$userId]);
            $plans = $planStmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                "status" => "success",
                "data" => [
                    "profile" => $profile,
                    "certifications" => $certifications,
                    "plans" => $plans
                ]
            ];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Fetch a coach profile by user_id, including user details, certifications, and plans.
     */
    public static function getByUserId($userId)
    {
        try {
            $sql = "SELECT cp.*, u.name as user_name, u.email, u.image as profile_image, cc.name as category_name, u.phone
                    FROM coach_profiles cp
                    INNER JOIN users u ON cp.user_id = u.id
                    LEFT JOIN coach_categories cc ON cp.category_id = cc.id
                    WHERE cp.user_id = ?";

            $stmt = Database::runPrepared($sql, [$userId]);
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$profile) return ["status" => "error", "message" => "Coach profile not found."];

            // Certifications
            $certStmt = Database::runPrepared("SELECT * FROM coach_certifications WHERE coach_id = ?", [$userId]);
            $certifications = $certStmt->fetchAll(PDO::FETCH_ASSOC);

            // Plans
            $planStmt = Database::runPrepared("SELECT * FROM coach_plans WHERE coach_id = ?", [$userId]);
            $plans = $planStmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                "status" => "success",
                "data" => [
                    "profile" => $profile,
                    "certifications" => $certifications,
                    "plans" => $plans
                ]
            ];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Fetch all coaches based on optional filters.
     */
    public static function getAll($filter = [])
    {
        try {
            $sql = "SELECT cp.*, u.name as user_name, u.email, u.phone, u.image as profile_image, cc.name as category_name
                    FROM coach_profiles cp
                    INNER JOIN users u ON cp.user_id = u.id
                    LEFT JOIN coach_categories cc ON cp.category_id = cc.id
                    WHERE 1=1";
            $params = [];

            if (!empty($filter['category_id'])) {
                $sql .= " AND cp.category_id = ?";
                $params[] = $filter['category_id'];
            }

            if (!empty($filter['approval_status'])) {
                $sql .= " AND cp.approval_status = ?";
                $params[] = $filter['approval_status'];
            }

            $sql .= " ORDER BY cp.created_at DESC";

            $stmt = Database::runPrepared($sql, $params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return ["status" => "success", "data" => $results];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Fetch all coach categories.
     */
    public static function getAllCategories()
    {
        try {
            $stmt = Database::run("SELECT * FROM coach_categories ORDER BY name ASC");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ["status" => "success", "data" => $results];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Delete a coach profile and associated sub-records.
     */
    public static function delete($id)
    {
        try {
            Database::getInstance()->beginTransaction();

            $stmt = Database::runPrepared("SELECT user_id FROM coach_profiles WHERE id = ?", [$id]);
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($profile) {
                $userId = $profile['user_id'];
                Database::runPrepared("DELETE FROM coach_certifications WHERE coach_id = ?", [$userId]);
                Database::runPrepared("DELETE FROM coach_plans WHERE coach_id = ?", [$userId]);
            }

            Database::runPrepared("DELETE FROM coach_profiles WHERE id = ?", [$id]);

            Database::getInstance()->commit();
            return ["status" => "success", "message" => "Coach profile deleted successfully"];
        } catch (Exception $e) {
            Database::getInstance()->rollBack();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Update the approval status of a coach profile.
     */
    public static function updateStatus($id, $status)
    {
        try {
            $allowedStatuses = ['pending', 'approved', 'rejected', 'pending_edits'];
            if (!in_array($status, $allowedStatuses)) {
                return ["status" => "error", "message" => "Invalid status provided."];
            }

            Database::runPrepared("UPDATE coach_profiles SET approval_status = ? WHERE id = ?", [$status, $id]);
            return ["status" => "success", "message" => "Coach status updated successfully."];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Fetch the count of pending coach profiles.
     */
    public static function getPendingCoachesCount()
    {
        try {
            $stmt = Database::runPrepared("SELECT COUNT(id) as count FROM coach_profiles WHERE approval_status = 'pending'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return ["status" => "success", "data" => $result['count']];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Family hires a coach.
     */
    public static function hireCoach($familyId, $coachId, $planId, $priceAtHire)
    {
        try {
            $sql = "INSERT INTO family_coaches (family_id, coach_id, plan_id, price_at_hire, status) VALUES (?, ?, ?, ?, 'pending_admin_approval')";
            Database::runPrepared($sql, [$familyId, $coachId, $planId, $priceAtHire]);
            $familyCoachId = Database::getLastInsertId();

            // Fetch Family Account
            require_once __DIR__ . '/Account.php';
            $accountRes = Account::getByFamilyId($familyId);
            if ($accountRes['status'] !== 'success' || empty($accountRes['data'])) {
                // Dummy account creation if not exists
                $accountNum = 'ACCT-' . strtoupper(substr(md5(time() . rand()), 0, 8));
                Database::runPrepared("INSERT INTO accounts (family_id, account_number, next_billing_date) VALUES (?, ?, DATE_ADD(CURDATE(), INTERVAL 1 MONTH))", [$familyId, $accountNum]);
                $accountId = Database::getLastInsertId();
                $accountRes = ['data' => ['id' => $accountId, 'account_number' => $accountNum]];
            } else {
                $accountId = $accountRes['data'][0]['id'] ?? $accountRes['data']['id'];
                $accountNum = $accountRes['data'][0]['account_number'] ?? $accountRes['data']['account_number'];
            }

            // Fetch Coach Name & Family Info for Invoice
            require_once __DIR__ . '/User.php';
            require_once __DIR__ . '/Family.php';
            $coachData = User::getUserById($coachId);
            $coachName = $coachData ? $coachData['name'] : 'Professional Coach';
            
            $familyData = Family::getFamily($familyId);
            $familyName = $familyData ? $familyData['name'] : 'Valued Customer';
            $familyEmail = $familyData ? $familyData['email'] : null;

            // Stripe Integration
            require_once __DIR__ . '/GlobalSettings.php';
            if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
                require_once __DIR__ . '/../vendor/autoload.php';
            }

            $stripeKeyRes = GlobalSettings::getSetting('stripe_secret_key');
            $stripeKey = ($stripeKeyRes['status'] === 'success' && !empty($stripeKeyRes['data'])) ? $stripeKeyRes['data']['setting_value'] : '';
            
            $baseUrlRes = GlobalSettings::getSetting('base_url');
            $baseUrl = ($baseUrlRes['status'] === 'success' && !empty($baseUrlRes['data'])) ? rtrim($baseUrlRes['data']['setting_value'], '/') : 'http://localhost/project/family-calendar';

            $stripeSessionId = null;
            $paymentUrl = '#';

            if (!empty($stripeKey)) {
                try {
                    \Stripe\Stripe::setApiKey($stripeKey);
                    $checkout_session = \Stripe\Checkout\Session::create([
                        'payment_method_types' => ['card'],
                        'line_items' => [[
                            'price_data' => [
                                'currency' => 'usd',
                                'product_data' => [
                                    'name' => 'Coach Hiring Invoice - ' . $coachName,
                                ],
                                'unit_amount' => (int)($priceAtHire * 100),
                            ],
                            'quantity' => 1,
                        ]],
                        'mode' => 'payment',
                        'success_url' => $baseUrl . '/payment_status.php?status=success',
                        'cancel_url' => $baseUrl . '/payment_status.php?status=failed',
                    ]);
                    $stripeSessionId = $checkout_session->id;
                    $paymentUrl = $checkout_session->url;
                } catch (Exception $e) {
                    // Ignore stripe errors, it will fall back to '#'
                }
            }

            // Generate Invoice PDF
            require_once __DIR__ . '/PDF.php';
            $invoiceData = [
                'family_name' => $familyName,
                'account_number' => $accountNum,
                'invoice_date' => date('Y-m-d'),
                'amount' => $priceAtHire,
                'stripe_link' => $paymentUrl,
                'invoice_title' => 'Coach Hiring Invoice',
                'item_description' => 'Hiring Coach: ' . $coachName
            ];
            
            $pdfResult = PDF::generateBill($invoiceData);
            $pdfPath = $pdfResult['status'] === 'success' ? $pdfResult['public_path'] : null;

            // Insert Payment Record
            $sqlPay = "INSERT INTO payments (account_id, invoice_date, amount, stripe_session_id, status, pdf_path, payment_type, reference_id) VALUES (?, ?, ?, ?, 'unpaid', ?, 'coach_hire', ?)";
            Database::runPrepared($sqlPay, [$accountId, date('Y-m-d'), $priceAtHire, $stripeSessionId, $pdfPath, $familyCoachId]);

            // Email the Invoice to all family heads
            $headsQuery = Database::runPrepared("
                SELECT users.email, users.name 
                FROM users 
                INNER JOIN user_family ON users.id = user_family.user_id 
                WHERE user_family.family_id = ? AND users.role = 'family-head'
            ", [$familyId]);
            $familyHeads = $headsQuery->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($familyHeads)) {
                require_once __DIR__ . '/../services/mail/Mailer.php';
                
                foreach ($familyHeads as $head) {
                    $emailHtml = Mailer::render('invoice', [
                        'name' => $head['name'],
                        'invoiceTitle' => 'Coach Hiring Invoice',
                        'invoiceDate' => date('Y-m-d'),
                        'amount' => $priceAtHire,
                        'paymentUrl' => $paymentUrl
                    ]);
                    
                    $mailData = [
                        'to' => $head['email'],
                        'subject' => 'Coach Hiring Invoice',
                        'html' => $emailHtml,
                        'attachments' => []
                    ];
                    
                    if ($pdfPath) {
                        $realPath = __DIR__ . '/../' . ltrim($pdfPath, './');
                        if (file_exists($realPath)) {
                            $pdfContent = file_get_contents($realPath);
                            $mailData['attachments'][] = [
                                'name' => 'Coach_Hiring_Invoice.pdf',
                                'type' => 'application/pdf',
                                'content' => $pdfContent
                            ];
                        }
                    }
                    
                    Mailer::send($mailData);
                }
            }

            return ["status" => "success", "message" => "Coach hired successfully and invoice sent! Pending admin approval."];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Get coaches hired by a specific family.
     */
    public static function getFamilyCoaches($familyId)
    {
        try {
            $sql = "SELECT fc.*, u.name as coach_name, u.email as coach_email, u.phone as coach_phone, u.image as coach_image, cp.price as plan_price, cp.duration_days 
                    FROM family_coaches fc
                    INNER JOIN users u ON fc.coach_id = u.id
                    INNER JOIN coach_plans cp ON fc.plan_id = cp.id
                    WHERE fc.family_id = ?
                    ORDER BY fc.created_at DESC";
            $stmt = Database::runPrepared($sql, [$familyId]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ["status" => "success", "data" => $results];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Get pending coach hire approvals for siteadmin.
     */
    public static function getPendingCoachApprovals()
    {
        try {
            $sql = "SELECT fc.*, f.name as family_name, u.name as coach_name, cp.duration_days as plan_duration, cp.price
                    FROM family_coaches fc
                    INNER JOIN families f ON fc.family_id = f.id
                    INNER JOIN users u ON fc.coach_id = u.id
                    INNER JOIN coach_plans cp ON fc.plan_id = cp.id
                    WHERE fc.status = 'pending_admin_approval'
                    ORDER BY fc.created_at DESC";
            $stmt = Database::runPrepared($sql);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ["status" => "success", "data" => $results];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Update the status of a family_coach record.
     */
    public static function updateFamilyCoachStatus($id, $status)
    {
        try {
            $allowedStatuses = ['pending_admin_approval', 'approved', 'rejected', 'active'];
            if (!in_array($status, $allowedStatuses)) {
                return ["status" => "error", "message" => "Invalid status provided."];
            }
            Database::runPrepared("UPDATE family_coaches SET status = ? WHERE id = ?", [$status, $id]);
            return ["status" => "success", "message" => "Hire status updated successfully."];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Update the CSV link for a family_coach record.
     */
    public static function updateFamilyCoachCsvLink($id, $csvLink)
    {
        try {
            Database::runPrepared("UPDATE family_coaches SET csv_link = ? WHERE id = ?", [$csvLink, $id]);
            return ["status" => "success", "message" => "CSV link updated successfully."];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Get specific family_coach record details.
     */
    public static function getFamilyCoachDetails($id)
    {
        try {
            $sql = "SELECT fc.*, f.name as family_name, u.name as coach_name, cp.duration_days as plan_duration, cp.price as plan_price
                    FROM family_coaches fc
                    INNER JOIN families f ON fc.family_id = f.id
                    INNER JOIN users u ON fc.coach_id = u.id
                    INNER JOIN coach_plans cp ON fc.plan_id = cp.id
                    WHERE fc.id = ?";
            $stmt = Database::runPrepared($sql, [$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                return ["status" => "error", "message" => "Record not found."];
            }
            return ["status" => "success", "data" => $result];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Get families that have hired a specific coach.
     */
    public static function getCoachFamilies($coachId)
    {
        try {
            $sql = "SELECT fc.*, f.name as family_name, f.email as family_email, cp.price as plan_price, cp.duration_days 
                    FROM family_coaches fc
                    INNER JOIN families f ON fc.family_id = f.id
                    INNER JOIN coach_plans cp ON fc.plan_id = cp.id
                    WHERE fc.coach_id = ? AND fc.status = 'approved'
                    ORDER BY fc.created_at DESC";
            $stmt = Database::runPrepared($sql, [$coachId]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ["status" => "success", "data" => $results];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
