<?php

namespace App\Services\InteractiveMap;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class InteractiveMapOwnerResolver
{
    /** @return array{class: class-string<Model>, foreign_key: string}> */
    public function resolve(string $ownerType): array
    {
        $owners = config('interactive_map.owners', []);
        $foreignKeys = config('interactive_map.foreign_keys', []);

        if (! isset($owners[$ownerType], $foreignKeys[$ownerType])) {
            throw new InvalidArgumentException('Unsupported interactive map owner type: ' . $ownerType);
        }

        return [
            'class' => $owners[$ownerType],
            'foreign_key' => $foreignKeys[$ownerType],
        ];
    }

    public function findModel(string $ownerType, int $ownerId): Model
    {
        $meta = $this->resolve($ownerType);

        /** @var Model|null $model */
        $model = $meta['class']::query()->find($ownerId);

        if (! $model) {
            throw new InvalidArgumentException('Owner record not found.');
        }

        return $model;
    }

    public function foreignKey(string $ownerType): string
    {
        return $this->resolve($ownerType)['foreign_key'];
    }
}
