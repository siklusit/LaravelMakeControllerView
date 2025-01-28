<?php

namespace Siklusit\LaravelMakeControllerView\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeControllerAndView extends Command
{
    protected $signature = 'make:controller-view {name} {--resource} {--api} {--model} {--migration} {--request} {--route} {--folder=}';
    protected $description = 'Create a new controller, view, model, migration, and request with a template';

    public function handle()
    {
        $name = $this->argument('name');
        $folder = $this->option('folder') ? $this->option('folder') . '/' : '';

        // Buat Controller
        $this->createController($name, $folder);

        // Buat View
        $this->createView($name, $folder);

        // Buat Model
        if ($this->option('model')) {
            $this->createModel($name, $folder);
        }

        // Buat Migration
        if ($this->option('migration')) {
            $this->createMigration($name);
        }

        // Buat Request
        if ($this->option('request')) {
            $this->createRequest($name, $folder);
        }

        // Tambahkan Route
        if ($this->option('route')) {
            $this->addRoute($name, $folder);
        }
    }

    protected function createController($name, $folder)
    {
        $controllerPath = app_path("Http/Controllers/{$folder}{$name}Controller.php");
        $controllerTemplate = "<?php\n\nnamespace App\Http\Controllers;\n\nuse Illuminate\Http\Request;\n\nclass {$name}Controller extends Controller\n{\n";

        if ($this->option('resource')) {
            $controllerTemplate .= "    public function index()\n    {\n        return view('{$folder}{$name}.index');\n    }\n\n";
            $controllerTemplate .= "    public function create()\n    {\n        return view('{$folder}{$name}.create');\n    }\n\n";
            $controllerTemplate .= "    public function store(Request \$request)\n    {\n        // Logic to store\n    }\n\n";
            $controllerTemplate .= "    public function show(\$id)\n    {\n        // Logic to show\n    }\n\n";
            $controllerTemplate .= "    public function edit(\$id)\n    {\n        return view('{$folder}{$name}.edit');\n    }\n\n";
            $controllerTemplate .= "    public function update(Request \$request, \$id)\n    {\n        // Logic to update\n    }\n\n";
            $controllerTemplate .= "    public function destroy(\$id)\n    {\n        // Logic to delete\n    }\n";
        } elseif ($this->option('api')) {
            $controllerTemplate .= "    public function index()\n    {\n        return response()->json([]);\n    }\n\n";
            $controllerTemplate .= "    public function show(\$id)\n    {\n        return response()->json([]);\n    }\n";
        } else {
            $controllerTemplate .= "    public function index()\n    {\n        return view('{$folder}{$name}.index');\n    }\n";
        }

        $controllerTemplate .= "}\n";

        if (!File::exists($controllerPath)) {
            File::put($controllerPath, $controllerTemplate);
            $this->info("Controller {$folder}{$name}Controller created successfully.");
        } else {
            $this->error("Controller {$folder}{$name}Controller already exists.");
        }
    }

    protected function createView($name, $folder)
    {
        $views = ['index', 'create', 'edit'];
        foreach ($views as $view) {
            $viewPath = resource_path("views/{$folder}{$name}/{$view}.blade.php");
            $viewTemplate = "<!-- Template for {$name} {$view} -->\n<h1>{$view} for {$name}</h1>\n";

            if (!File::exists($viewPath)) {
                File::makeDirectory(dirname($viewPath), 0755, true, true);
                File::put($viewPath, $viewTemplate);
                $this->info("View {$folder}{$name}/{$view}.blade.php created successfully.");
            } else {
                $this->error("View {$folder}{$name}/{$view}.blade.php already exists.");
            }
        }
    }

    protected function createModel($name, $folder)
    {
        $modelPath = app_path("Models/{$folder}{$name}.php");
        $modelTemplate = "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Database\Eloquent\Model;\n\nclass {$name} extends Model\n{\n    use HasFactory;\n\n    protected \$fillable = [];\n}\n";

        if (!File::exists($modelPath)) {
            File::put($modelPath, $modelTemplate);
            $this->info("Model {$folder}{$name} created successfully.");
        } else {
            $this->error("Model {$folder}{$name} already exists.");
        }
    }

    protected function createMigration($name)
    {
        $migrationName = "create_{$name}s_table";
        $migrationPath = database_path("migrations/" . date('Y_m_d_His') . "_{$migrationName}.php");
        $migrationTemplate = "<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nclass Create" . ucfirst($name) . "sTable extends Migration\n{\n    public function up()\n    {\n        Schema::create('{$name}s', function (Blueprint \$table) {\n            \$table->id();\n            \$table->timestamps();\n        });\n    }\n\n    public function down()\n    {\n        Schema::dropIfExists('{$name}s');\n    }\n}\n";

        File::put($migrationPath, $migrationTemplate);
        $this->info("Migration for {$name} created successfully.");
    }

    protected function createRequest($name, $folder)
    {
        $requestPath = app_path("Http/Requests/{$folder}{$name}Request.php");
        $requestTemplate = "<?php\n\nnamespace App\Http\Requests;\n\nuse Illuminate\Foundation\Http\FormRequest;\n\nclass {$name}Request extends FormRequest\n{\n    public function authorize()\n    {\n        return true;\n    }\n\n    public function rules()\n    {\n        return [\n            // Validation rules\n        ];\n    }\n}\n";

        if (!File::exists($requestPath)) {
            File::put($requestPath, $requestTemplate);
            $this->info("Request {$folder}{$name}Request created successfully.");
        } else {
            $this->error("Request {$folder}{$name}Request already exists.");
        }
    }

    protected function addRoute($name, $folder)
    {
        $routePath = base_path('routes/web.php');
        $routeTemplate = "Route::resource('{$folder}{$name}', {$folder}{$name}Controller::class);\n";

        if (!File::exists($routePath)) {
            $this->error("Route file not found.");
            return;
        }

        File::append($routePath, $routeTemplate);
        $this->info("Route for {$folder}{$name} added successfully.");
    }
}