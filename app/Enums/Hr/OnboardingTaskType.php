<?php

namespace App\Enums\Hr;

enum OnboardingTaskType: string
{
    case DocumentUpload  = 'document_upload';
    case FormFill        = 'form_fill';
    case TrainingCourse  = 'training_course';
    case ReadAcknowledge = 'read_acknowledge';
    case ExternalLink    = 'external_link';

    public function label(): string
    {
        return match($this) {
            self::DocumentUpload  => 'Document Upload',
            self::FormFill        => 'Form Fill',
            self::TrainingCourse  => 'Training Course',
            self::ReadAcknowledge => 'Read & Acknowledge',
            self::ExternalLink    => 'External Link',
        };
    }
}
