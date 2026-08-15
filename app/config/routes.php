<?php

return [
    /* FRONTEND ROUTES */
    // Public routes
    ''                                           => ['HomeController', 'index'],
    'home'                                       => ['HomeController', 'index'],
    'contact'                                    => ['HomeController', 'contact'],
    'about'                                      => ['HomeController', 'about'],
    'services'                                   => ['HomeController', 'services'],
    'popia'                                      => ['HomeController', 'popia'],
    'privacy'                                    => ['HomeController', 'privacy'],
    'policies'                                   => ['HomeController', 'policies'],

    // Authentication routes
    'auth/register'                              => ['AuthController', 'register'],
    'auth/login'                                 => ['AuthController', 'login'],
    'auth/logout'                                => ['AuthController', 'logout'],
    'user-registration'                          => ['AuthController', 'self_user_registration'],
    'forgot-password'                            => ['AuthController', 'forgot_password'],
    'reset-password/{id}'                        => ['AuthController', 'reset_password'],

    /* ADMIN ROUTES */ 
    'admin'                                      => ['AdminController', 'index'],
    'admin/no-access'                            => ['AdminController', 'no_access'],

    // Users 
    'admin/users'                                => ['UserController', 'index', 'middleware' => ['AuthMiddleware']],
    'admin/users/new'                            => ['UserController', 'create'],
    'admin/users/edit/{id}'                      => ['UserController', 'edit'],
    'admin/users/delete/{id}'                    => ['UserController', 'delete'],
    'admin/users/profile/{id}'                   => ['UserController', 'profile'],
    'admin/users/pdf/{id}'                       => ['PDF', 'userProfilePDF'],
    'admin/users/pdf'                            => ['PDF', 'user_list_pdf'],

    // Employees 
    'admin/employees'                            => ['EmployeeController', 'index'],
    'admin/employee/create'                      => ['EmployeeController', 'create'],
    'admin/employee/edit/{id}'                   => ['EmployeeController', 'edit'],
    'admin/employee/delete/{id}'                 => ['EmployeeController', 'delete'],
    'admin/employee/profile/{id}'                => ['EmployeeController', 'profile'],

    // Payments 
    'admin/payments'                            => ['PaymentController', 'index'],
    'admin/payment/create'                      => ['PaymentController', 'create_payment'],
    'admin/payment/edit/{id}'                   => ['PaymentController', 'edit_payment'],
    'admin/payment/delete/{id}'                 => ['PaymentController', 'delete_payment'],

    // Expenditure 
    'admin/expenditure'                         => ['ExpenditureController', 'index'],
    'admin/expenditure/create'                  => ['ExpenditureController', 'create_expenditure'],
    'admin/expenditure/edit/{id}'               => ['ExpenditureController', 'edit_expenditure'],
    'admin/expenditure/delete/{id}'             => ['ExpenditureController', 'delete_expenditure'],
    
    /* LOGGER */
    'admin/client-onboarding/logview'            => ['LogViewerController', 'user_onboarding'],
    'admin/client-onboarding/csv'                => ['LogViewerController', 'exportCsv'],
     
    // Company 
    'admin/company'                              => ['AdminController', 'companyDetails'],
    'admin/company/edit/{id}'                    => ['AdminController', 'companyDetailsEdit'],
    'admin/social'                               => ['AdminController', 'socialLinks'],
    'admin/social/edit/{id}'                     => ['AdminController', 'socialLinksEdit'],
    'admin/hours'                                => ['AdminController', 'operatingHours'],
    'admin/hours/edit/{id}'                      => ['AdminController', 'operatingHoursEdit'],

    // Chat 
    'admin/chat'                                 => ['ChatController', 'index'],
    'admin/chat/room/{id}'                       => ['ChatController', 'room'],
    'admin/chat/conversation/{id}'               => ['ChatController', 'conversation'],
    'admin/chat/start/{id}'                      => ['ChatController', 'startConversation'],
    'admin/chat/join/{id}'                       => ['ChatController', 'joinRoom'],
    'admin/chat/leave/{id}'                      => ['ChatController', 'leaveRoom'],
    'admin/chat/create-room'                     => ['ChatController', 'createRoom'],
    'admin/chat/add-participant'                 => ['ChatController', 'addParticipant'],
    'admin/chat/remove-participant'              => ['ChatController', 'removeParticipant'],
    'admin/chat/send'                            => ['ChatController', 'apiSendMessage'],
    'admin/chat/messages'                        => ['ChatController', 'getMessages'],
    'admin/chat/upload-voice'                    => ['ChatController', 'uploadVoice'],
    'admin/chat/upload-image'                    => ['ChatController', 'uploadImage'],
    'admin/chat/mark-read'                       => ['ChatController', 'markRead'],
    'admin/chat/typing'                          => ['ChatController', 'typing'],
    'admin/chat/get-typing'                      => ['ChatController', 'getTyping'],
    'admin/chat/contacts'                        => ['ChatController', 'getContacts'],
    'admin/chat/online'                          => ['ChatController', 'getOnlineUsers'],
    'admin/chat/search'                          => ['ChatController', 'search'],
    'admin/chat/delete-message'                  => ['ChatController', 'deleteMessage'],

    // Boardroom
    'admin/meetings'                             => ['MeetingsController', 'meetings'],
    'admin/create-meeting'                       => ['MeetingsController', 'create_meeting'],
    'admin/boardroom'                            => ['MeetingsController', 'boardroom'],

    // Track Visitors
    'admin/visitors'                             => ['VisitorController', 'visitors'],
    'admin/visitors/view/{id}'                   => ['VisitorController', 'single_view'],

    /* SETTINGS */
    'admin/settings'                             => ['SettingsController', 'index'],
    'admin/settings/update'                      => ['SettingsController', 'update'],

    /* OFFLINE-FIRST / PWA SYNC API */
    'offline/config'                             => ['SyncController', 'config'],
    'offline/pull/{table}'                       => [
        'controller' => ['SyncController', 'pull'],
        'middleware' => ['AuthMiddleware']
    ],
    'offline/push'                               => [
        'controller' => ['SyncController', 'push'],
        'middleware' => ['AuthMiddleware']
    ],
    'offline/status'                             => [
        'controller' => ['SyncController', 'status'],
        'middleware' => ['AuthMiddleware']
    ],

    /* INSTALLATION ROUTES */
    'install'                                    => ['InstallController', 'index'],
    'install/requirements'                       => ['InstallController', 'requirements'],
    'install/database'                           => ['InstallController', 'database'],
    'install/run_migrations'                     => ['InstallController', 'run_migrations'],
    'install/admin'                              => ['InstallController', 'admin'],
    'install/settings'                           => ['InstallController', 'settings'],
    'install/finish'                             => ['InstallController', 'finish'],
    'install/restart'                            => ['InstallController', 'restart'],
];
