<?php
$controller_name = $controller->_get->toString('controller');
$action_name = $controller->_get->toString('action');

// Dashboard
$isScriptDashboard = in_array($controller_name, array('pjAdmin')) && in_array($action_name, array('pjActionIndex'));

// Bookings
$isScriptBookings = in_array($controller_name, array('pjAdminBookings'));

// Clients
$isScriptClientsController = in_array($controller_name, array('pjAdminClients'));
$isScriptClientsIndex = $isScriptClientsController && in_array($action_name, array('pjActionIndex', 'pjActionCreate', 'pjActionUpdate'));

// Driver
$isScriptDriversController = in_array($controller_name, array('pjAdminDrivers'));
$isScriptDriversIndex = $isScriptDriversController && in_array($action_name, array('pjActionIndex', 'pjActionCreate', 'pjActionUpdate'));

$isScriptDriversResIndex = $isScriptDriversController && in_array($action_name, array('pjActionGetDriverReservationIndex'));

// Vehicles
$isScriptFleetsController = in_array($controller_name, array('pjAdminFleets'));
$isScriptFleetsIndex = $isScriptFleetsController && in_array($action_name, array('pjActionIndex', 'pjActionCreate', 'pjActionUpdate'));

// Cities
$isScriptCitiesController = in_array($controller_name, array('pjAdminCities'));
$isScriptCitiesIndex = $isScriptCitiesController && in_array($action_name, array('pjActionIndex', 'pjActionCreate', 'pjActionUpdate'));

// Extras
$isScriptExtrasController         = in_array($controller_name, array('pjAdminExtras'));

// Payments
$isScriptPaymentsController = in_array($controller_name, array('pjPayments'));

// Settings
$isScriptOptionsController = in_array($controller_name, array('pjAdminOptions')) && !in_array($action_name, array('pjActionPreview', 'pjActionInstall'));
$isScriptOptionsBooking         = $isScriptOptionsController && in_array($action_name, array('pjActionBooking'));
$isScriptOptionsBookingForm     = $isScriptOptionsController && in_array($action_name, array('pjActionBookingForm'));
$isScriptOptionsTerm            = $isScriptOptionsController && in_array($action_name, array('pjActionTerm'));
$isScriptOptionsNotifications   = $isScriptOptionsController && in_array($action_name, array('pjActionNotifications'));

// Permissions - Dashboard
$hasAccessScriptDashboard = pjAuth::factory('pjAdmin', 'pjActionIndex')->hasAccess();


// Permissions - Bookings
$hasAccessScriptBookings = pjAuth::factory('pjAdminBookings', 'pjActionIndex')->hasAccess();

// Permissions - Clients
$hasAccessScriptClients            = pjAuth::factory('pjAdminClients')->hasAccess();
$hasAccessScriptClientsIndex       = pjAuth::factory('pjAdminClients', 'pjActionIndex')->hasAccess();

// Permissions - Vehicles
$hasAccessScriptFleets            = pjAuth::factory('pjAdminFleets')->hasAccess();
$hasAccessScriptFleetsIndex       = pjAuth::factory('pjAdminFleets', 'pjActionIndex')->hasAccess();

// Permissions - Cities
$hasAccessScriptCities           = pjAuth::factory('pjAdminCities')->hasAccess();
$hasAccessScriptCitiesndex       = pjAuth::factory('pjAdminCities', 'pjActionIndex')->hasAccess();

// Permissions - Drivers
$hasAccessScriptDrivers            = pjAuth::factory('pjAdminDrivers')->hasAccess();
$hasAccessScriptDriversIndex       = pjAuth::factory('pjAdminDrivers', 'pjActionIndex')->hasAccess();

// Permissions - Reservation
$hasAccessScriptReservationDrivers            = pjAuth::factory('pjAdminDrivers')->hasAccess();
$hasAccessScriptReservationDriversIndex       = pjAuth::factory('pjAdminDrivers', 'pjActionGetDriverReservationIndex')->hasAccess();

// Permissions - Extras
$hasAccessScriptExtras       = pjAuth::factory('pjAdminExtras')->hasAccess();

// Permissions - Payments
$hasAccessScriptPayments = pjAuth::factory('pjPayments', 'pjActionIndex')->hasAccess();

// Permissions - Settings
$hasAccessScriptOptions                 = pjAuth::factory('pjAdminOptions')->hasAccess();
$hasAccessScriptOptionsBooking          = pjAuth::factory('pjAdminOptions', 'pjActionBooking')->hasAccess();
$hasAccessScriptOptionsBookingForm      = pjAuth::factory('pjAdminOptions', 'pjActionBookingForm')->hasAccess();
$hasAccessScriptOptionsTerm             = pjAuth::factory('pjAdminOptions', 'pjActionTerm')->hasAccess();
$hasAccessScriptOptionsNotifications    = pjAuth::factory('pjAdminOptions', 'pjActionNotifications')->hasAccess();
?>

<?php if ($hasAccessScriptDashboard): ?>
    <li<?php echo $isScriptDashboard ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionIndex"><i class="fa fa-th-large"></i> <span class="nav-label"><?php __('plugin_base_menu_dashboard');?></span></a>
    </li>
<?php endif; ?>

<?php if ($hasAccessScriptBookings): ?>
    <li<?php echo $isScriptBookings ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex"><i class="fa fa-list"></i> <span class="nav-label"><?php __('menuEnquiries');?></span></a>
    </li>
<?php endif; ?>

<?php if ($hasAccessScriptFleets): ?>
    <li<?php echo $isScriptFleetsIndex ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminFleets&amp;action=pjActionIndex"><i class="fa fa-car"></i> <span class="nav-label"><?php __('menuFleets');?></span></a>
    </li>
<?php endif; ?>

<?php if ($hasAccessScriptClients): ?>
    <li<?php echo $isScriptClientsIndex ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&amp;action=pjActionIndex"><i class="fa fa-user"></i> <span class="nav-label"><?php __('menuClients');?></span></a>
    </li>
<?php endif; ?>

<?php 
    $auth = pjAuth::factory();
    $roleId = $auth->getRoleId();

    // Show Drivers menu only if NOT a Driver (roleId != 4) AND user has access
    if ((int)$roleId !== 4 && $hasAccessScriptDrivers): ?>
        <li<?php echo $isScriptDriversIndex ? ' class="active"' : ''; ?>>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminDrivers&amp;action=pjActionIndex">
                <i class="fa fa-dribbble"></i> 
                <span class="nav-label"><?php __('menuDrivers'); ?></span>
            </a>
        </li>
    <?php endif; ?>

<?php if ($hasAccessScriptCities): ?>
    <li<?php echo $isScriptCitiesIndex ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCities&amp;action=pjActionIndex"><i class="fa fa-map-marker"></i> <span class="nav-label"><?php __('infoCitiesTitle');?></span></a>
    </li>
<?php endif; ?>

<?php if ($hasAccessScriptExtras): ?>
    <li<?php echo $isScriptExtrasController ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminExtras&amp;action=pjActionIndex"><i class="fa fa-plus-circle"></i> <span class="nav-label"><?php __('menuExtras');?></span></a>
    </li>
<?php endif; ?>

<?php if ($hasAccessScriptOptions || $hasAccessScriptPayments): ?>
    <li<?php echo $isScriptOptionsController || $isScriptPaymentsController ? ' class="active"' : NULL; ?>>
        <a href="#"><i class="fa fa-cogs"></i> <span class="nav-label"><?php __('script_menu_settings');?></span><span class="fa arrow"></span></a>
        <ul class="nav nav-second-level collapse">
            <?php if ($hasAccessScriptOptionsBooking): ?>
                <li<?php echo $isScriptOptionsBooking ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionBooking"><?php __('menuReservation');?></a></li>
            <?php endif; ?>

            <?php if ($hasAccessScriptPayments): ?>
                <li<?php echo $isScriptPaymentsController ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjPayments&amp;action=pjActionIndex"><?php __('script_menu_payments');?></a></li>
            <?php endif; ?>

            <?php if ($hasAccessScriptOptionsBookingForm): ?>
                <li<?php echo $isScriptOptionsBookingForm ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionBookingForm"><?php __('menuReservationForm');?></a></li>
            <?php endif; ?>
            
            <?php if ($hasAccessScriptOptionsNotifications): ?>
                <li<?php echo $isScriptOptionsNotifications ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionNotifications"><?php __('menuNotifications');?></a></li>
            <?php endif; ?>
            
            <?php if ($hasAccessScriptOptionsTerm): ?>
                <li<?php echo $isScriptOptionsTerm ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionTerm"><?php __('menuTerms');?></a></li>
            <?php endif; ?>
        </ul>
    </li>
<?php endif; ?>

<?php 
$auth = pjAuth::factory();
$roleId = $auth->getRoleId();
if ((int)$roleId === 4): ?>
    <li<?php echo $isScriptDriversResIndex ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminDrivers&amp;action=pjActionGetDriverReservationIndex"><i class="fa fa-dribbble"></i> <span class="nav-label"><?php __('front_your_reservations');?></span></a>
    </li>
 <?php endif; ?>