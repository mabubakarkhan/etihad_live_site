@include('admin.projects._form_sections', [
    'project' => $project,
    'projectTypes' => $projectTypes,
    'states' => $states,
    'onlySection' => $section,
])
