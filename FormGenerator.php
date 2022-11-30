<?php

if (php_sapi_name() != 'cli') {
    die('Must run from command line');
}

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('log_errors', 0);
ini_set('html_errors', 0);


require_once('vendor/autoload.php');

use Symfony\Component\Console\Output\OutputInterface;

$app = new Silly\Application();
$app->command('forms [name]', function ($name, OutputInterface $output) {
    $schemefile = __DIR__ . '/schema.xml';
    $templateDir = __DIR__ . '/templates/';

    $xmldata = simplexml_load_file($schemefile) or die("Failed to load");
    foreach ($xmldata->table as $table) {
        $tablename = isset($table['phpName']) ? ((string)$table['phpName']) : ((string)$table['name']);
        $export = !empty($name) ? $name == $tablename : true;
        if ($export) {
            $filename = $templateDir . $tablename . '.twig';
            $output->write($filename . '...');
            $rows = [];

            foreach ($table->column as $column) {
                                
                $named = isset($column['name']) ? str_replace(' ', '_', ((string)$column['name'])) : "Unknown" . count($rows);
                $title = isset($column['name']) ? ((string)$column['name']) : $named;

                $title = preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $title);
                $title = preg_replace('/(_)/', ' ', $title);
                
                $type = strtolower((isset($column['type']) ? ((string)$column['type']) : 'varchar'));
                $required = (((string)$column['required']) == "true") ? 'required="required"' : '';
                $autogenerated = (((string)$column['autoIncrement']) == "true") ? 'hidden' : '';
                $disabled = (((string)$column['autoIncrement']) == "true") ? 'disabled' : '';

                switch ($type) {
                    case 'longvarchar':
                        $rows[] = <<<ROW
                        <div class="form-group d-flex" $autogenerated>
                            <label class="col-form-label col-md-3 text-right" for="$named">$title</label>
                            <textarea class="form-control"  name="$named" $disabled data-type="$type" $required rows="3"></textarea>
                        </div>
                        ROW;
                    break;
                    case 'bigint':
                    case 'integer':
                    case 'smallint':
                        $rows[] = <<<ROW
                        <div class="form-group d-flex" $autogenerated>
                            <label class="col-form-label col-md-3 text-right" for="$named">$title</label>
                            <input class="form-control" type="number" $disabled name="$named" data-type="$type" $required />
                        </div>
                        ROW;
                    break;
                    case 'real':
                    case 'decimal':
                    case 'float':
                        $rows[] = <<<ROW
                        <div class="form-group d-flex" $autogenerated>
                            <label class="col-form-label col-md-3 text-right" for="$named">$title</label>
                            <textarea class="form-control" type="number" $disabled step="0.001"  name="$named" data-type="$type" $required $disabled />
                        </div>
                        ROW;
                    break;
                    case 'timestamp':
                        $rows[] = <<<ROW
                        <div class="form-group d-flex" $autogenerated>
                            <label class="col-form-label col-md-3 text-right" for="$named">$title</label>
                            <textarea class="form-control" type="date"  name="$named" data-type="$type" $required $disabled />
                        </div>
                        ROW;
                    break;
                    case 'boolean':
                        $rows[] = <<<ROW
                        <div class="form-group d-flex" $autogenerated>
                            <label class="col-form-label col-md-3 text-right" for="$named">
                                $title
                            </label>
                            <select class="form-control" name="$named" data-type="$type" $required>
                                <option value="true">Yes</option>
                                <option value="false">No</option>
                            </select>
                        </div>
                        ROW;
                    break;
                    default:
                        $rows[] = <<<ROW
                        <div class="form-group d-flex" $autogenerated>
                            <label class="col-form-label col-md-3 text-right" for="$named">$title</label>
                            <input type="input" class="form-control" name="$named" data-type="$type" $required $disabled>
                        </div> 
                        ROW;
                    break;
                }
            }

            $rows = join("\n", $rows);
            file_put_contents($filename, "<form data-name=\"$tablename\">$rows</form>");
            $output->writeln('done');
        }
    }
});

$app->run();
