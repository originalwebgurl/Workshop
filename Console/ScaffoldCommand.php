<?php namespace Modules\Workshop\Console;

use Illuminate\Console\Command;
use Modules\Workshop\Scaffold\ModuleScaffold;

class ScaffoldCommand extends Command
{
    protected $name = 'asgard:scaffold';
    protected $description = 'Scaffold a new module';
    /**
     * @var array
     */
    protected $entities = [];
    /**
     * @var array
     */
    protected $valueObjects = [];
    /**
     * @var string The type of entities to generate [Eloquent or Doctrine]
     */
    protected $entityType;
    /**
     * @var ModuleScaffold
     */
    private $moduleScaffold;

    public function __construct(ModuleScaffold $moduleScaffold)
    {
        parent::__construct();
        $this->moduleScaffold = $moduleScaffold;
    }

    /**
     *
     */
    public function fire()
    {
        $moduleName = $this->ask('Please enter the module name. Example: vendor/name.');
        list($vendor, $name) = $this->separateVendorAndName($moduleName);

        $this->askForEntities();
        $this->askForValueObjects();

        $this->moduleScaffold
            ->vendor($vendor)
            ->name($name)
            ->setEntityType($this->entityType)
            ->withEntities($this->entities)
            ->withValueObjects($this->valueObjects)
            ->scaffold();

        $this->info('Module generated and is ready to be used.');
    }

    /**
     *
     */
    private function askForEntities()
    {
        $this->entityType = $this->choice('Do you want to use Eloquent or Doctrine ? [eloquent]', ['Eloquent', 'Doctrine'], 0);
        do {
            $entity = $this->ask('Enter entity name. Leaving option empty will continue script.');
            if (!empty($entity)) {
                $this->entities[] = ucfirst($entity);
            }
        } while (!empty($entity));
    }

    /**
     *
     */
    private function askForValueObjects()
    {
        do {
            $valueObject = $this->ask('Enter value object name. Leaving option empty will continue script.');
            if (!empty($valueObject)) {
                $this->valueObjects[] = ucfirst($valueObject);
            }
        } while (!empty($valueObject));
    }

    /**
     * Extract the vendor and module name as two separate values
     * @param  string $fullName
     * @return array
     */
    private function separateVendorAndName($fullName)
    {
        $explodedFullName = explode('/', $fullName);

        return [
            $explodedFullName[0],
            ucfirst($explodedFullName[1]),
        ];
    }
}