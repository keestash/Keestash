<?php
declare(strict_types=1);

use doganoo\DI\Encryption\User\IUserService;
use doganoo\DIP\Encryption\User\UserService;
use KSA\GeneralApi\Factory\Repository\DemoUsersRepositoryFactory;
use KSA\Settings\Api\Organization\Activate;
use KSA\Settings\Api\Organization\Add;
use KSA\Settings\Api\Organization\Get;
use KSA\Settings\Api\Organization\ListAll;
use KSA\Settings\Api\Organization\Update;
use KSA\Settings\Api\User\GetAll;
use KSA\Settings\Api\User\ProfilePicture;
use KSA\Settings\Api\User\UserAdd;
use KSA\Settings\Api\User\UserEdit;
use KSA\Settings\Api\User\UserLock;
use KSA\Settings\Api\User\UserRemove;
use KSA\Settings\BackgroundJob\UserDeleteTask;
use KSA\Settings\Command\UpdatePassword;
use KSA\Settings\Controller\Organization\Detail;
use KSA\Settings\Controller\Controller;
use KSA\Settings\Event\Listener\OrganizationAddedEventListener;
use KSA\Settings\Event\Listener\PostStateChange;
use KSA\Settings\Factory\Api\File\ProfilePictureFactory;
use KSA\Settings\Factory\Api\Organization\ActivateFactory;
use KSA\Settings\Factory\Api\Organization\AddFactory;
use KSA\Settings\Factory\Api\Organization\GetFactory;
use KSA\Settings\Factory\Api\Organization\ListAllFactory;
use KSA\Settings\Factory\Api\Organization\UpdateFactory;
use KSA\Settings\Factory\Api\Organization\UserFactory;
use KSA\Settings\Factory\Api\User\GetAllFactory;
use KSA\Settings\Factory\Api\User\UserAddFactory;
use KSA\Settings\Factory\Api\User\UserLockFactory;
use KSA\Settings\Factory\Api\User\UserRemoveFactory;
use KSA\Settings\Factory\BackgroundJob\UserDeleteTaskFactory;
use KSA\Settings\Factory\Command\UpdatePasswordFactory;
use KSA\Settings\Factory\Controller\Organization\DetailFactory;
use KSA\Settings\Factory\Controller\SettingsControllerFactory;
use KSA\Settings\Factory\Event\Listener\OrganizationAddedEventListenerFactory;
use KSA\Settings\Factory\Event\Listener\PostStateChangeFactory;
use KSA\Settings\Factory\Repository\OrganizationRepositoryFactory;
use KSA\Settings\Factory\Repository\OrganizationUserRepositoryFactory;
use KSA\Settings\Factory\Service\SegmentServiceFactory;
use KSA\Settings\Repository\DemoUsersRepository;
use KSA\Settings\Repository\IOrganizationRepository;
use KSA\Settings\Repository\IOrganizationUserRepository;
use KSA\Settings\Repository\OrganizationRepository;
use KSA\Settings\Repository\OrganizationUserRepository;
use KSA\Settings\Service\SegmentService;
use KSA\Settings\Factory\Api\User\UserEditFactory;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'factories' => [
        // service
        UserService::class                         => InvokableFactory::class,
        SegmentService::class                      => SegmentServiceFactory::class,

        // controller
        Controller::class                          => SettingsControllerFactory::class,
        Detail::class                              => DetailFactory::class,

        // background job
        UserDeleteTask::class                      => UserDeleteTaskFactory::class,

        // api
        Activate::class                            => ActivateFactory::class,
        Add::class                                 => AddFactory::class,
        Get::class                                 => GetFactory::class,
        ListAll::class                             => ListAllFactory::class,
        Update::class                              => UpdateFactory::class,
        \KSA\Settings\Api\Organization\User::class => UserFactory::class,
        UserEdit::class                            => UserEditFactory::class,
        ProfilePicture::class                      => ProfilePictureFactory::class,
        GetAll::class                              => GetAllFactory::class,
        UserAdd::class                             => UserAddFactory::class,
        UserLock::class                            => UserLockFactory::class,
        UserRemove::class                          => UserRemoveFactory::class,

        // repository
        OrganizationRepository::class              => OrganizationRepositoryFactory::class,
        OrganizationUserRepository::class          => OrganizationUserRepositoryFactory::class,
        DemoUsersRepository::class                 => DemoUsersRepositoryFactory::class,

        // event
        // ----- listener
        OrganizationAddedEventListener::class      => OrganizationAddedEventListenerFactory::class,
        PostStateChange::class                     => PostStateChangeFactory::class,

        // command
        UpdatePassword::class                      => UpdatePasswordFactory::class,
    ]
    , 'aliases' => [
        IOrganizationRepository::class     => OrganizationRepository::class,
        IOrganizationUserRepository::class => OrganizationUserRepository::class,
        IUserService::class                => UserService::class,
    ]
];