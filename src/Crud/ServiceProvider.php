<?php

namespace Fjord\Crud;

use Fjord\Crud\Api\ApiRepositories;
use Fjord\Crud\Fields\Block\Block;
use Fjord\Crud\Fields\Boolean;
use Fjord\Crud\Fields\Checkboxes;
use Fjord\Crud\Fields\Component;
use Fjord\Crud\Fields\Datetime;
use Fjord\Crud\Fields\Icon;
use Fjord\Crud\Fields\Input;
use Fjord\Crud\Fields\ListField\ListField;
use Fjord\Crud\Fields\Media\File;
use Fjord\Crud\Fields\Media\Image;
use Fjord\Crud\Fields\Modal;
use Fjord\Crud\Fields\Password;
use Fjord\Crud\Fields\Radio;
use Fjord\Crud\Fields\Range;
use Fjord\Crud\Fields\Relations\ManyRelation;
use Fjord\Crud\Fields\Relations\OneRelation;
use Fjord\Crud\Fields\Route;
use Fjord\Crud\Fields\Route\RouteCollectionResolver;
use Fjord\Crud\Fields\Select;
use Fjord\Crud\Fields\Textarea;
use Fjord\Crud\Fields\Wysiwyg;
use Fjord\Crud\Models\Relations\CrudRelations;
use Fjord\Crud\Repositories\BlockRepository;
use Fjord\Crud\Repositories\DefaultRepository;
use Fjord\Crud\Repositories\ListRepository;
use Fjord\Crud\Repositories\MediaRepository;
use Fjord\Crud\Repositories\ModalRepository;
use Fjord\Crud\Repositories\RelationRepository;
use Fjord\Crud\Repositories\Relations\BelongsToManyRepository;
use Fjord\Crud\Repositories\Relations\BelongsToRepository;
use Fjord\Crud\Repositories\Relations\HasManyRepository;
use Fjord\Crud\Repositories\Relations\HasOneRepository;
use Fjord\Crud\Repositories\Relations\ManyRelationRepository;
use Fjord\Crud\Repositories\Relations\MorphManyRepository;
use Fjord\Crud\Repositories\Relations\MorphOneRepository;
use Fjord\Crud\Repositories\Relations\MorphToManyRepository;
use Fjord\Crud\Repositories\Relations\MorphToRepository;
use Fjord\Crud\Repositories\Relations\OneRelationRepository;
use Fjord\Support\Facades\Form as FormFacade;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Available fields.
     *
     * @var array
     */
    protected $fields = [
        'input'        => Input::class,
        'password'     => Password::class,
        'select'       => Select::class,
        'boolean'      => Boolean::class,
        'icon'         => Icon::class,
        'datetime'     => Datetime::class,
        'dt'           => Datetime::class,
        'checkboxes'   => Checkboxes::class,
        'range'        => Range::class,
        'textarea'     => Textarea::class,
        'text'         => Textarea::class,
        'wysiwyg'      => Wysiwyg::class,
        'block'        => Block::class,
        'image'        => Image::class,
        'file'         => File::class,
        'modal'        => Modal::class,
        'component'    => Component::class,
        'oneRelation'  => OneRelation::class,
        'manyRelation' => ManyRelation::class,
        'list'         => ListField::class,
        'radio'        => Radio::class,
        'route'        => Route::class,
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->register(CrudRelations::class);
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerForm();

        $this->registerCrud();

        $this->registerApiRepositories();
    }

    /**
     * Register the singleton.
     *
     * @return void
     */
    protected function registerCrud()
    {
        $this->callAfterResolving('fjord.app', function ($app) {
            $app->singleton('crud', function () {
                return new Crud;
            });

            $app->singleton('crud.route.resolver', function () {
                return new RouteCollectionResolver;
            });
        });
    }

    /**
     * Register the singleton.
     *
     * @return void
     */
    protected function registerForm()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('Form', FormFacade::class);

        $this->app->singleton('fjord.form', function () {
            $form = new Form();

            $this->registerFields($form);

            return $form;
        });
    }

    /**
     * Register crud api repositories.
     *
     * @return string
     */
    protected function registerApiRepositories()
    {
        $this->app->singleton(ApiRepositories::class, function () {
            $rep = new ApiRepositories();

            $rep->register('default', DefaultRepository::class);
            $rep->register('list', ListRepository::class);
            $rep->register('block', BlockRepository::class);
            $rep->register('media', MediaRepository::class);
            $rep->register('modal', ModalRepository::class);
            $rep->register('relation', RelationRepository::class);
            $rep->register('one-relation', OneRelationRepository::class);
            $rep->register('many-relation', ManyRelationRepository::class);
            $rep->register('belongs-to', BelongsToRepository::class);
            $rep->register('belongs-to-many', BelongsToManyRepository::class);
            $rep->register('has-many', HasManyRepository::class);
            $rep->register('has-one', HasOneRepository::class);
            $rep->register('morph-one', MorphOneRepository::class);
            $rep->register('morph-to-many', MorphToManyRepository::class);
            $rep->register('morph-to', MorphToRepository::class);
            $rep->register('morph-many', MorphManyRepository::class);

            return $rep;
        });
    }

    /**
     * Register fields.
     *
     * @param  Form $field
     * @return void
     */
    protected function registerFields(Form $form)
    {
        foreach ($this->fields as $alias => $field) {
            $form->field($alias, $field);
        }
    }
}
