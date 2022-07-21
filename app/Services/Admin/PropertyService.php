<?php

namespace App\Services\Admin;

use App\Models\Property;
use App\Models\PropertyOption;
use App\Models\Section;
use Illuminate\Support\Str;

class PropertyService
{
    public function getListBySection($sectionId, $search = null)
    {
        $section = Section::find($sectionId);
        $sections = Section::whereNull('sectionId')->get();
        $properties = Property::where('sectionId', '=', $sectionId);

        if ($search) {
            $properties = $properties->where('title', 'like', "%{$search}%");
        }

        $properties = $properties->paginate(10);

        return [
            'currentPage' => $properties->currentPage(),
            'lastPage' => $properties->lastPage(),
            'items' => $properties->map(function ($item) {
                $type = 'Строка';

                switch ($item->valueType) {
                    case 'text':
                        $type = 'Текст/HTML';
                        break;
                    case 'number':
                        $type = 'Целове число';
                        break;
                    case 'float':
                        $type = 'Число с плавающей запятой';
                        break;
                    case 'model':
                        $type = 'Связаные модели';
                        break;
                }

                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'code' => $item->code,
                    'type' => $type,
                    'realType' => $item->valueType,
                ];
            }),
            'types' => [
                [
                    'val' => 'string', 'title' => 'Строка',
                ],
                [
                    'val' => 'text', 'title' => 'Текст/HTML',
                ],
                [
                    'val' => 'number', 'title' => 'Целое число',
                ],
                [
                    'val' => 'float', 'title' => 'Число с плавающей запятой',
                ],
                [
                    'val' => 'model', 'title' => 'Связанные объекты',
                ],
            ],
            'section' => [
                'id' => $section->id,
                'title' => $section->title,
            ],
            'sections' => $sections->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                ];
            }),
        ];
    }

    public function store($sectionId, $data)
    {
        foreach ($data['sectionIds'] as $section) {
            Property::updateOrCreate([
                'title' => $data['title'],
                'code' => Str::slug($data['title'] . '-' . $section),
                'sectionId' => $section,
                'valueType' => $data['type'],
            ], []);
        }
    }

    public function getOptionsBySection($sectionId, $propertyId)
    {
        $property = Property::where('id', '=', $propertyId)
            ->where('sectionId', '=', $sectionId)
            ->first();

        $section = Section::find($sectionId);

        $options = PropertyOption::where('propertyId', '=', $propertyId)
            ->paginate(10);

        $type = 'Строка';

        switch ($property->valueType) {
            case 'text':
                $type = 'Текст/HTML';
                break;
            case 'number':
                $type = 'Целове число';
                break;
            case 'float':
                $type = 'Число с плавающей запятой';
                break;
            case 'model':
                $type = 'Связаные модели';
                break;
        }

        return [
            'lastPage' => $options->lastPage(),
            'currentPage' => $options->currentPage(),
            'items' => $options->map(function ($item) {
               return [
                   'id' => $item->id,
                   'value' => $item->value,
               ];
            }),
            'property' => [
                'title' => $property->title,
                'sectionId' => $property->sectionId,
                'section' => [
                    'id' => $section->id,
                    'title' => $section->title,
                ],
                'type' => $type,
                'realType' => $property->valueType,
            ],
        ];
    }

    public function storeOption($sectionId, $propertyId, $data)
    {
        $option = new PropertyOption();

        if (!empty($data['id'])) {
            $option = PropertyOption::find($data['id']);
        }

        $option->fill($data);
        $option->setValue($propertyId, $data['value']);
        $option->propertyId = $propertyId;
        $option->sectionId = $sectionId;
        $option->save();
    }

    public function deleteProperty($propertyId)
    {
        Property::find($propertyId)->delete();
    }

    public function deleteOption($optionId)
    {
        PropertyOption::find($optionId)->delete();
    }
}
