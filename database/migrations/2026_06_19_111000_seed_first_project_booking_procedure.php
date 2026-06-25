<?php

use App\Models\Project;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $project = Project::query()
            ->where('title', 'First')
            ->orWhere('slug', 'first')
            ->first();

        if (! $project || ! empty($project->booking_procedure)) {
            return;
        }

        $title = trim((string) $project->title);

        $project->booking_procedure = [
            'heading' => $title . ' Booking Procedure',
            'content' => '<p>Booking your plot in ' . e($title) . ' is a simple and transparent process. Our team guides you through each step so you can secure your investment with confidence.</p>'
                . '<ul>'
                . '<li>Visit our sales office or contact our team to start your application.</li>'
                . '<li>Complete the booking form with accurate applicant details.</li>'
                . '<li>Submit required documents and initial payment as per policy.</li>'
                . '<li>Receive your provisional allotment letter after verification.</li>'
                . '<li>Track status updates until final documentation is complete.</li>'
                . '</ul>',
            'documents_heading' => 'Required Documents',
            'steps' => [
                [
                    'title' => 'Application Form',
                    'description' => 'Fill out your booking application form with full attention and correct personal details.',
                ],
                [
                    'title' => 'Documents',
                    'description' => 'Attach the CNIC copies of the applicant and the next of kin for verification.',
                ],
                [
                    'title' => 'Payment',
                    'description' => 'Pay the down payment via cheque or pay-order in favour of the project account. Confirm the latest process with management before payment.',
                ],
                [
                    'title' => 'Submit',
                    'description' => 'Submit all required documents and the payment receipt to complete your booking request.',
                ],
            ],
            'documents' => [
                ['icon' => 'fa-user', 'label' => "Applicant's passport-size photographs"],
                ['icon' => 'fa-id-card', 'label' => "Applicant's and next of kin's copy of CNIC or passport"],
                ['icon' => 'fa-passport', 'label' => 'Copy of NICOP for overseas clients'],
                ['icon' => 'fa-receipt', 'label' => 'Copy of payment receipt'],
            ],
        ];
        $project->save();
    }

    public function down(): void
    {
        $project = Project::query()
            ->where('title', 'First')
            ->orWhere('slug', 'first')
            ->first();

        if (! $project) {
            return;
        }

        $project->booking_procedure = null;
        $project->save();
    }
};
