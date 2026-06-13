<?php

namespace App\Services;

use App\Models\MedicalHistory;
use App\Models\Patient;

class VoiceReportService
{
    public function transcribeAndStructure(string $transcript, int $patientId): array
    {
        $structured = $this->parseTranscript($transcript);

        $structured['patient_id'] = $patientId;
        $structured['visit_type'] = $structured['visit_type'] ?? 'follow_up';
        $structured['visit_date'] = now()->toDateString();

        return $structured;
    }

    private function parseTranscript(string $text): array
    {
        $result = [
            'chief_complaint' => null,
            'diagnosis' => null,
            'treatment' => null,
            'prescriptions' => null,
            'doctor_notes' => $text,
            'follow_up' => null,
        ];

        // Extract chief complaint
        if (preg_match('/(?:شکایت|شکایت اصلی|مراجعه|علت مراجعه)[:\s]*(.+?)(?:\.|،|تشخیص|$)/ui', $text, $m)) {
            $result['chief_complaint'] = trim($m[1]);
        }

        // Extract diagnosis
        if (preg_match('/(?:تشخیص|تشخیص پزشک)[:\s]*(.+?)(?:\.|،|درمان|$)/ui', $text, $m)) {
            $result['diagnosis'] = trim($m[1]);
        }

        // Extract treatment
        if (preg_match('/(?:درمان|اقدام|اقدام انجام شده)[:\s]*(.+?)(?:\.|،|تجویز|$)/ui', $text, $m)) {
            $result['treatment'] = trim($m[1]);
        }

        // Extract prescriptions/medications
        if (preg_match('/(?:تجویز|دارو|نسخه|داروهای)[:\s]*(.+?)(?:\.|،|پیگیری|هفته|$)/ui', $text, $m)) {
            $result['prescriptions'] = trim($m[1]);
        }

        // Extract follow-up
        if (preg_match('/(?:مراجعه|پیگیری|کنترل)[:\s]*(.+?)(?:\.|،|$)/ui', $text, $m)) {
            $result['follow_up'] = trim($m[1]);
        }

        // If no structured data found, put entire text as chief complaint
        if (!$result['chief_complaint'] && !$result['diagnosis'] && !$result['treatment']) {
            $result['chief_complaint'] = mb_substr($text, 0, 255);
        }

        return $result;
    }

    public function createMedicalReport(array $structuredData, int $createdBy): MedicalHistory
    {
        $data = [
            'patient_id' => $structuredData['patient_id'],
            'visit_date' => $structuredData['visit_date'],
            'visit_type' => $structuredData['visit_type'],
            'chief_complaint' => $structuredData['chief_complaint'],
            'diagnosis' => $structuredData['diagnosis'],
            'treatment' => $structuredData['treatment'],
            'prescriptions' => $structuredData['prescriptions'],
            'doctor_notes' => $structuredData['doctor_notes'],
            'created_by' => $createdBy,
        ];

        // Check for follow-up date
        if ($structuredData['follow_up']) {
            $followUpService = app(FollowUpSuggestionService::class);
            $suggestion = $followUpService->detectFromText($structuredData['follow_up']);
            if ($suggestion) {
                $data['follow_up_date'] = $suggestion['date'];
            }
        }

        return MedicalHistory::create($data);
    }
}
