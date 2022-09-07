<?php

declare(strict_types=1);

namespace Lazer\Migrate\Classes;

use Lazer\Classes\Database as Lazer;
use Lazer\Classes\LazerException;

class Database
{
    /**
     * Converts structure and data to Lazer database
     * It will add data to tables and fallback to create table if not found.
     *
     * Example for table data:
     * [
     *  'columnName' => 'value'
     *  'id' => 1
     *  'name' => 'Hello world!'
     * ]
     *
     * @param string $tableName
     * @param string $database
     * @return Lazer
     * @throws LazerException
     */
    public function addDataToIndex($tableName, $tableData, $objCb = null): Lazer
    {
        if (!trim($tableName)) {
            throw new LazerException('Table name missing');
        }

        // Check if greg0/lazer-database installed
        if (!class_exists('Lazer\Classes\Database')) {
            throw new \Exception('Lazer database package not found.' . PHP_EOL . 'install it: composer require greg0/lazer-database');
        }

        $tableFound = false;
        $newFields = [];
        $newFields['id'] = 'integer';
        $newFields['entity_id'] = 'integer';
        $numReg = '/[1-9.][0-9.]*$/';
        foreach ($tableData as $i => $doc) {
            foreach ($doc as $n => $k) {
                if (isset($newFields[$n])) {
                    if ($newFields[$n] == 'integer') {
                        $tableData[$i][$n] = (int) $k;
                    } else if (is_array($k)) {
                        $newFields[$n] = 'string';
                        $tableData[$i][$n] = json_encode($k);
                        continue;
                    }
                    continue;
                }
                if (is_array($k)) {
                    $newFields[$n] = 'string';
                    $tableData[$i][$n] = json_encode($k);
                    continue;
                }
                if (is_object($k)) {
                    // Callback method must return string only
                    if (!$objCb || !is_callable($objCb)) {
                        throw new LazerException('Callback method for Object type is not given.');
                    }
                    $newFields[$n] = 'string';
                    $tableData[$i][$n] = $objCb($k);
                    continue;
                }
                if (isset($fields[$n])) {
                    $newFields[$n] = $fields[$n];
                    continue;
                }
                if (preg_match($numReg, $k)) {
                    $newFields[$n] = 'integer';
                    $tableData[$i][$n] = (int) $k;
                    continue;
                }
                if (is_string($k)) {
                    $newFields[$n] = 'string';
                    continue;
                }
            }
        }

        $table = null;
        try {
            // create table if not exists
            if (!$tableFound) {
                Lazer::create($tableName, $newFields);
            }
        } catch (LazerException $e) {
            $tableFound = true;
            $table = Lazer::table($tableName);
            $table->addFields($newFields);
        }

        $table = $table ?? Lazer::table($tableName);
        $doc = current($tableData);

        foreach ($tableData as $it) {
            foreach ($it as $t => $v) {
                if ($newFields[$t] == 'integer') {
                    $it[$t] = (int) $v;
                }
            }

            $table->set($it);
            $table->save();
        }

        $initialFields[] = 'indexScope';

        return $table;
    }
}
