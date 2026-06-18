@include('admin.properties._form', [
    'property' => $property,
    'listingType' => $listingType,
    'projectTypes' => $projectTypes,
    'dealers' => $dealers,
    'states' => $states,
    'onlySection' => $section,
])
