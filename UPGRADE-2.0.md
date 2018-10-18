UPGRADE FROM 1.3 TO 2.0
=======================

There is no "gentle" migration procedure.

But the migration from version 1.3 to the version 2.0 should be anyway easy if you follow those instructions.

**=========> ATTENTION <=========**

**Keep in mind that the upgrade procedure has to be done manually. Also if it is described in details in this document, please**

**DO A BACKUP OF YOU DATABASE BEFORE MIGARTING!!!**

**=========> ATTENTION <=========**

The main changes involve the configuration settings, the renaming of a table and some modifications to the name of some methods and the removal of some others, so it is sufficient to

1. Renamed bundle class;
2. Rename tables;
3. Remove properties not still existent;
4. Change the changed ones;
5. Eventually add the new available ones;
6. Update your code to use the new methods.

Then it is required to edit the databse to reflect the new namings: `EmailStatus` entity, in fact, was renamed to `EmailStatus` and so its table has to be renamed, too.

Also some fields of various entities was renamed or deleted.

To upgrade your database without losing the data collected until now, please 
follow those steps.

Also the database fields change from version 1.3 to version 2.0: so, please, run the following command:

```console
bin/console doctrine:schema:update --force
```

Those are the changes that will be applied:

- `EmailStatus`:
  - CHANGE `EmailStatus::$emailAddress` to `EmailStatus::$address`
  - REMOVE `EmailStatus::$complaintsCount`: use `EmailStatus::getComplaints()->count()` instead
  - REMOVE `EmailStatus::$deliveriesCount`: use `EmailStatus::getDeliveries()->count()` instead

There should be no loss of data about emails and their statuses if you follow those steps.

STEP 0: Rename the Bundle class
-------------------------------

To make [Flex](https://symfony.com/doc/current/setup/flex.html) able to auto-configure the bundle, we renamed the bundle class from `SHQAwsSesMonitorBundle` to `SerendipityHQAwsSesMonitorBundle`:
rename it in your configuration files.

If you are using SF^3:

```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        ...
//        From this
//        new SerendipityHQ\Bundle\SHQAwsSesMonitorBundle(),

//        To this
          new SerendipityHQ\Bundle\SerendipityHQAwsSesMonitorBundle(),
        ...
    );

    ...

    return $bundles;
}
```

STEP 1: Update the Config
-------------------------

### Removed config parameters

The following parameters was removed or renamed: please remove or rename them from your configuration:

```yaml
shq_aws_ses_monitor:
    db_driver: orm # Removed
    model_manager_name: null # Removed
    endpoint: #Added
        scheme: '%env(APP_SCHEME)%' # Added
        host: '%env(APP_HOST)%' # Added
    bounces:
        track: true # Added. Moved from "bounces.filter.enabled"
        topic: ses-your_app-bounces-topic # Changed. Now accepts the name of the topic
            endpoint: # Removed
                route_name: # Removed
                protocol: # Removed
                host: # Removed
        filter:
            enabled: # Removed. Moved to "bounces.enabled"
    complaints:
        track: true # Added. Moved from "complaints.filter.enabled"
        topic: ses-your_app-complaints-topic # Changed. Now accepts the name of the topic
            endpoint: # Removed
                route_name: # Removed
                protocol: # Removed
                host: # Removed
        filter:
            enabled: # Removed. Moved to "complaints.enabled"
    deliveries:
        track: true # Renamed from "deliveries.enabled"
        enabled: # Removed. Moved to "deliveries.track"
        topic: ses-your_app-deliveries-topic # Changed. Now accepts the name of the topic
            endpoint: # Removed
                route_name: # Removed
                protocol: # Removed
                host: # Removed
```

### Changed default values

```yaml
shq_aws_ses_monitor:
    aws_config:
        region: eu-west-1 # Previously was "us-east-1"
```

STEP 2: Fully update the database using `doctrine:schema:update`
-----------------------------------------------------------------

This will reflect the other changes to the properties of the entities and will automatically rename the fields, indexes and foreign keys:

```console
bin/console doctrine:schema:update --force
```

If you want to see which queries Doctrine will execute WITHOUT really executing them, run:

```console
bin/console doctrine:schema:update --dump-sql
```

STEP 3: Update your code using the new classes and methods
----------------------------------------------------------

This is a more long procedure and you have to do it manually.

Below there is a complete list of the changes made to the code base of `SHQAwsSesMonitorBundle`: please, follow it.

In this work can be of help the use of a tool like PHPStan or Phan to statically analyze your code (this requires your code base is still healthy).

Classes and their Methods
-------------------------

### Changed methods

`EmailStatus` entity:

- `EmailStatus::getEmailAddress()` => `EmailStatus::getAddress()`;
- `Bounce::getEmailAddress():string` => `Bounce::getEmail():EmailStatus`
- `Delivery::getEmailAddress():string` => `Delivery::getEmail():EmailStatus`
- `Complaint::getEmailAddress():string` => `Complaint::getEmail():EmailStatus`

### Removed methods

`EmailStatus` entity:

- `EmailStatus::getComplaintsCount()` (use `EmailStatus::getComplaints()->count()` instead)
- `EmailStatus::getDeliveriesCount()` (use `EmailStatus::getComplaints()->count()` instead)

Commands
--------

The command `aws:ses:monitor:subscribe` was substituted by the broader and more powerful `aws:ses:configure`.
