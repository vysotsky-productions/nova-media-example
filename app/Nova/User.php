<?php

namespace App\Nova;

use App\SavePhoto;
use App\SavePhotoCollection;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\Password;
use VysotskyProductions\NovaGalleryField\NovaGalleryField;
use VysotskyProductions\NovaPhotoFiled\NovaPhotoField;

class User extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\\User';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'email',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

//            Gravatar::make(),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:8')
                ->updateRules('nullable', 'string', 'min:8'),

//            BelongsTo::make('Альбом', 'album', Album::class)->nullable(),


            NovaPhotoField::make('Превью', 'preview')
//                ->aspectRatio(3/4)
                ->getPhoto('original_url')
                ->getPhotoForm('preview_url')
                ->getPhotoDetail('preview_url')
                ->getPhotoIndex('preview_url')
                ->setHandler(
                    new SavePhoto('persons/avatar', config('thumbs.user.persons/avatar'))
                ),

            NovaGalleryField::make('Альбом', $this->album )
//                ->aspectRatio(3/4)
                ->getPhoto('original_url')
                ->getPhotoForm('preview_url')
                ->getPhotoDetail('preview_url')
                ->getPhotoIndex('preview_url')
                ->setCustomGalleryFields([
                    Text::make('name'),
                    Text::make('description')
                ])
                ->setHandler(
                    new SavePhotoCollection(
                        new SavePhoto('persons/album', config('thumbs.user.persons/avatar'))
                    )
                )
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
