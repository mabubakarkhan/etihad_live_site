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

        if (! $project || trim(strip_tags((string) ($project->tabs_follow_content ?? ''))) !== '') {
            return;
        }

        $project->tabs_follow_content = <<<'HTML'
<h3>About This Development</h3>
<p>Etihad Marketing presents a carefully curated overview of this project for buyers, investors, and families exploring premium real estate opportunities in Lahore. This development combines thoughtful master planning, verified documentation, and practical amenities designed for modern living. Whether you are comparing payment plans, evaluating location advantages, or reviewing long-term investment potential, the information below is intended to give you a clear and confident starting point.</p>
<p>Our team works directly with registered dealers and approved inventory so you receive accurate plot details, transparent pricing guidance, and timely updates throughout the buying journey. From initial inquiry to final handover support, we focus on clarity, responsiveness, and professional service at every step.</p>
<h3>Location &amp; Connectivity</h3>
<p>The project enjoys strategic access to major road networks, commercial corridors, and essential civic infrastructure. Nearby schools, healthcare facilities, shopping areas, and mosques make daily life convenient while preserving a calm residential atmosphere. Future infrastructure upgrades in the surrounding zone continue to strengthen accessibility and long-term value for property owners.</p>
<p>Buyers often choose this location for its balance between peaceful neighborhood living and practical connectivity to central Lahore. Commute times remain manageable, and the surrounding master plan supports sustainable growth rather than overcrowded expansion.</p>
<h3>Investment Highlights</h3>
<p>Key reasons investors consider this project include documented ownership structures, phased development planning, and demand from end-users seeking secure communities with reliable utilities and community facilities. Flexible installment options, transparent booking procedures, and professional sales support help reduce uncertainty for first-time and repeat buyers alike.</p>
<p>Market trends in comparable developments suggest steady appreciation when projects maintain clear records, deliver infrastructure on schedule, and offer amenities that match buyer expectations. We recommend scheduling a site visit to review plot maps, discuss current availability, and confirm the plan that best fits your goals.</p>
HTML;
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

        $project->tabs_follow_content = null;
        $project->save();
    }
};
