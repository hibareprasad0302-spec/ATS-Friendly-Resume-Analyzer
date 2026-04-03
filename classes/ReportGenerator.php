<?php

class ReportGenerator
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function save(array $data): int
    {
        $stmt = $this->db->prepare('
            INSERT INTO resume_reports (
                user_id, original_filename, stored_filename, file_type,
                job_description, job_role, extracted_text,
                score_keyword, score_skills, score_sections,
                score_projects, score_experience, score_education,
                score_formatting, total_score,
                matched_keywords, missing_keywords,
                matched_skills, missing_skills,
                detected_sections, missing_sections,
                suggestions, full_report
            ) VALUES (
                :user_id, :original_filename, :stored_filename, :file_type,
                :job_description, :job_role, :extracted_text,
                :score_keyword, :score_skills, :score_sections,
                :score_projects, :score_experience, :score_education,
                :score_formatting, :total_score,
                :matched_keywords, :missing_keywords,
                :matched_skills, :missing_skills,
                :detected_sections, :missing_sections,
                :suggestions, :full_report
            )
        ');

        $stmt->execute([
            ':user_id'           => $data['user_id'],
            ':original_filename' => $data['original_filename'],
            ':stored_filename'   => $data['stored_filename'],
            ':file_type'         => $data['file_type'],
            ':job_description'   => $data['job_description'],
            ':job_role'          => $data['job_role'] ?: null,
            ':extracted_text'    => $data['extracted_text'],
            ':score_keyword'     => $data['scores']['keyword'],
            ':score_skills'      => $data['scores']['skills'],
            ':score_sections'    => $data['scores']['sections'],
            ':score_projects'    => $data['scores']['projects'],
            ':score_experience'  => $data['scores']['experience'],
            ':score_education'   => $data['scores']['education'],
            ':score_formatting'  => $data['scores']['formatting'],
            ':total_score'       => $data['scores']['total'],
            ':matched_keywords'  => json_encode($data['keyword_result']['matched']),
            ':missing_keywords'  => json_encode($data['keyword_result']['missing']),
            ':matched_skills'    => json_encode($data['skill_result']['matched']),
            ':missing_skills'    => json_encode($data['skill_result']['missing']),
            ':detected_sections' => json_encode($data['section_result']['detected']),
            ':missing_sections'  => json_encode($data['section_result']['missing']),
            ':suggestions'       => json_encode($data['suggestions']),
            ':full_report'       => json_encode($data),
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function getById(int $id, ?int $userId = null): ?array
    {
        $sql = 'SELECT * FROM resume_reports WHERE id = ?';
        $params = [$id];

        if ($userId !== null) {
            $sql .= ' AND user_id = ?';
            $params[] = $userId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $report = $stmt->fetch();

        if (!$report) {
            return null;
        }

        // Decode JSON fields
        foreach (['matched_keywords', 'missing_keywords', 'matched_skills', 'missing_skills', 'detected_sections', 'missing_sections', 'suggestions', 'full_report'] as $field) {
            if (isset($report[$field]) && is_string($report[$field])) {
                $report[$field] = json_decode($report[$field], true);
            }
        }

        return $report;
    }

    public function getByUser(int $userId, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;

        $countStmt = $this->db->prepare('SELECT COUNT(*) FROM resume_reports WHERE user_id = ?');
        $countStmt->execute([$userId]);
        $total = (int) $countStmt->fetchColumn();

        $stmt = $this->db->prepare(
            'SELECT id, original_filename, job_role, total_score, created_at
             FROM resume_reports WHERE user_id = ?
             ORDER BY created_at DESC LIMIT ? OFFSET ?'
        );
        $stmt->execute([$userId, $perPage, $offset]);
        $reports = $stmt->fetchAll();

        return [
            'reports'    => $reports,
            'total'      => $total,
            'page'       => $page,
            'per_page'   => $perPage,
            'total_pages' => (int) ceil($total / $perPage),
        ];
    }

    public function getAll(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;

        $countStmt = $this->db->query('SELECT COUNT(*) FROM resume_reports');
        $total = (int) $countStmt->fetchColumn();

        $stmt = $this->db->prepare(
            'SELECT r.id, r.original_filename, r.job_role, r.total_score, r.created_at,
                    u.full_name as user_name
             FROM resume_reports r
             LEFT JOIN users u ON r.user_id = u.id
             ORDER BY r.created_at DESC LIMIT ? OFFSET ?'
        );
        $stmt->execute([$perPage, $offset]);
        $reports = $stmt->fetchAll();

        return [
            'reports'     => $reports,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => (int) ceil($total / $perPage),
        ];
    }
}
